<?php
/**
 *
 * CategoryTree Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Categories Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 */

sys::import('modules.categories.class.categories');

class CategoryTreeProperty extends DataProperty
{
    public $id         = 30046;
    public $name       = 'categorytree';
    public $desc       = 'CategoryTree';
    public $reqmodules = array('categories');

    function __construct(ObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);

        $this->tplmodule = 'categories';
        $this->filepath   = 'modules/categories/xarproperties';
    }

    public function showInput(Array $data = array())
    {
        if (empty($data['startnum'])) $data['startnum'] = 1;
        if (empty($data['items_per_page'])) $data['items_per_page'] = xarModVars::get('categories','items_per_page');

        if (isset($data['options'])) {
            $this->options = $data['options'];
        } else {
            $this->options = xarMod::apiFunc('categories','user','getchildren',array('cid' => 0));
        }
        $trees = array();
        $totalcount = 0;
        foreach ($this->options as $entry) {
            $node = new CategoryTreeNode($entry['cid']);
// Can't do the pager stuff here. needs to happen in the template
//            $node->start = $data['startnum'];
//            $node->itemstoshow = $data['items_per_page'];
            $tree = new CategoryTree($node);
            $nodes = $node->depthfirstenumeration();
            $trees[] = $nodes;

            // Perhaps this should be in the classes?
            $count = xarMod::apiFunc('categories','user','countcats', $entry);
            $totalcount += $count;
        }
        $data['trees'] = $trees;
        $data['pagertotal'] = $totalcount;

        return parent::showInput($data);
    }

}

?>
