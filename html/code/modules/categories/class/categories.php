<?php
    sys::import('xaraya.structures.tree');

    class Categories extends Object
    {
        function get(int $id)
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $SQLquery = "SELECT id,
                                name,
                                description,
                                image,
                                parent_id,
                                left_id,
                                right_id
                           FROM  " . $xartable['categories'] . " WHERE id = ?";
            $bindvars = array($id);
            $result = $dbconn->Execute($SQLquery,$bindvars);
            if (!$result) return;

            $c = new CategoryTreeNode();
            list($c->id, $c->name, $c->description, $c->image, $c->parent, $c->left, $c->right) = $result->fields;
            return $c;
        }
    }

    class CategoryTreeNode extends TreeNode
    {
        public $name = '';
        public $description = '';
        public $image = '';
        public $child_object = "";
        public $parent = 0;
        public $left = 0;
        public $right = 0;

        public $cid = false;
        public $eid = false;
        public $mindepth = 1;
        public $maxdepth;
        public $getchildren = true;
        public $returnitself = true;
        public $start = 1;
        public $itemstoshow = 0;

        public $cidlist = null;

        function getChildren()
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $SQLquery = "SELECT id,
                                name,
                                description,
                                image,
                                child_object,
                                parent_id,
                                left_id,
                                right_id
                           FROM  " . $xartable['categories'] . " WHERE parent_id = ? ORDER BY left_id";
            $bindvars = array($this->id);
            $result = $dbconn->Execute($SQLquery,$bindvars);
            if (!$result) return;

            sys::import('xaraya.structures.sets.collection');
            $set = new BasecSet();
            while (!$result->EOF) {
                $c = new CategoryTreeNode();
                list($c->id, $c->name, $c->description, $c->image, $o->child_object, $c->parent, $c->left, $c->right) = $result->fields;
                $collection->add($c);
            }
            return $collection;
        }

        function getParent()
        {
            return Categories::get($this->parent);
        }

        function getChildAt()
        {

        }

        function getChildCount()
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $SQLquery = "SELECT COUNT(*) FROM " . $xartable['categories'] . " WHERE parent_id = ? ORDER BY left_id";
            $bindvars = array($this->id);
            $result = $dbconn->Execute($SQLquery,$bindvars);
            if (!$result) return;

            $fields = $result->fields;
            return array_pop($fields);
        }


        function isDescendant(CategoryTreeNode $n)
        {
            $dbconn = xarDB::getConn();
            $xartable = xarDB::getTables();

            $query = '
                SELECT  P1.id
                FROM    ' . $xartable['categories'] . ' AS P1,
                        ' . $xartable['categories'] . ' AS P2
                WHERE   P2.left_id >= P1.left_id
                AND     P2.left_id <= P1.right_id
                AND     P2.id = ' . $n->id . '
                AND     P1.id = ' . $this->id . '
                AND     P1.id !=' . $n->id;

            $result = $dbconn->SelectLimit($query, 1);
            if (!$result) {return;}

            if (!$result->EOF) {
                return true;
            } else {
                return false;
            }
        }

        function load(Array $args)
        {
            foreach($args as $key => $value) $this->$key = $value;
        }

        function setfilter($args=array())
        {
            foreach ($args as $key => $value) $this->$key = $value;
        }
        function toArray()
        {
            return array('id' => $this->id, 'name' => $this->name);
        }
    }

    class CategoryTree extends Tree
    {
        function createnodes(TreeNode $node)
        {
            if ($node->id != null) $node->cid = $node->id;
            $data = xarMod::apiFunc('categories',
                                    'user',
                                    'getcat',
                                  array('eid'           => $node->eid,
                                        'cid'           => $node->cid,
                                        'return_itself' => $node->returnitself,
                                        'getchildren'   => $node->getchildren,
                                        'maximum_depth' => $node->maxdepth,
                                        'minimum_depth' => $node->mindepth,
                                        'startnum'      => $node->start,
                                        'items_per_page'  => $node->itemstoshow,
                                        ));
             foreach ($data as $row) {
                $nodedata = array(
                    'id' => $row['cid'],
                    'parent' => $row['parent'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'indentation' => $row['indentation'],
                    'image' => $row['image'],
                    'child_object' => $row['child_object'],
                    'left' => $row['left'],
                    'right' => $row['right'],
                );
                if (!empty($node->cidlist) && isset($node->cidlist[$node->id])) {
                    $cidlist = $node->cidlist[$node->id];
                    if (in_array($row['cid'],$cidlist)) $this->treedata[] = $nodedata;
                } else {
                    $this->treedata[$row['name']] = $nodedata;
                }
            }
            parent::createnodes($node);
        }
    }
?>
