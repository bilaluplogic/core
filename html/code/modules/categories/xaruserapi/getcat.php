<?php

/**
 * get categories
 *
 * @param $args['cid'] =Integer= restrict output only to this category ID and its sibbling (default none)
 * @param $args['eid'] =Integer= do not output this category and its sibblings (default none)
 * @param $args['maximum_depth'] =Integer= return categories with the given depth or less
 * @param $args['minimum_depth'] =Integer= return categories with the given depth or more
 * @param $args['indexby'] =string= specify the index type for the result array (default 'default')
 *  They only change the output IF 'cid' is set:
 *    @param $args['getchildren'] =Boolean= get children of category (default false)
 *    @param $args['getparents'] =Boolean= get parents of category (default false)
 *    @param $args['return_itself'] =Boolean= return the cid itself (default false)
 * @return =Array= of categories, or =Boolean= false on failure

 * Examples:
 *    getcat() => Return all the categories
 *    getcat(Array('cid' -> ID)) => Only cid and its children, grandchildren and
 *                                   every other sibbling will be returned
 *    getcat(Array('eid' -> ID)) => All categories will be returned EXCEPT
 *                                   eid and its children, grandchildren and
 *                                   every other sibbling will be returned
 */
function categories_userapi_getcat($args)
{
    extract($args);

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    if (!isset($return_itself)) {
        $return_itself = false;
    }

    if (empty($indexby)) {$indexby = 'default';}

    if (!isset($getchildren)) {
        $getchildren = false;
    }
    if (!isset($getparents)) {
        $getparents = false;
    }
    if (!isset($startnum)) {
        $startnum = 0;
    }
    elseif (!is_numeric($startnum)) {
        xarSession::setVar('errormsg', xarML('Bad numeric arguments for API function'));
        return false;
    } else {
        //The pager starts counting from 1
        //SelectLimit starts from 0
        $startnum--;
    }
    if (!isset($items_per_page)) {
        $items_per_page = 0;
    }
    elseif (!is_numeric($items_per_page)) {
        xarSession::setVar('errormsg', xarML('Bad numeric arguments for API function'));
        return false;
    }

    $categoriestable = $xartable['categories'];
    $bindvars = array();
    $SQLquery = "SELECT
                        COUNT(P2.id) AS indent,
                        P1.id,
                        P1.name,
                        P1.description,
                        P1.image,
                        P1.child_object,
                        P1.parent_id,
                        P1.left_id,
                        P1.right_id,
                        P1.state
                   FROM $categoriestable P1,
                        $categoriestable P2
                  WHERE P1.left_id
                     >= P2.left_id
                    AND P1.left_id
                     <= P2.right_id";
/* this is terribly slow, at least for MySQL 3.23.49-nt
                  WHERE P1.left_id
                BETWEEN P2.left_id AND
                        P2.right_id";
*/
    if (isset($cid) && !is_array($cid) && $cid != false)
    {
        if ($getchildren || $getparents)
        {
            // We have the category ID but we need
            // to know its left and right values
            $cat = xarMod::apiFunc('categories','user','getcatinfo',Array('cid' => $cid));
            if ($cat == false) {
                xarSession::setVar('errormsg', xarML('Category does not exist'));
                return Array();
            }

            // If not returning itself we need to take the appropriate
            // left values
            if ($return_itself)
            {
                $return_child_left = $cat['left'];
                $return_parent_left = $cat['left'];
            }
            else
            {
                $return_child_left = $cat['left'] + 1;
                $return_parent_left = $cat['left'] - 1;
            }

            // Introducing an AND operator in the WHERE clause
            $SQLquery .= ' AND (';
        }

        if ($getchildren)
        {
            $SQLquery .= "(P1.left_id BETWEEN ? AND ?)";
            $bindvars[] = $return_child_left; $bindvars[] = $cat['right'];
        }

        if ($getparents && $getchildren)
        {
               $SQLquery .= " OR ";
        }

        if ($getparents)
        {
             $SQLquery .= "( ? BETWEEN P1.left_id AND P1.right_id)";
            $bindvars[] = $return_parent_left;
        }

        if ($getchildren || $getparents)
        {
            // Closing the AND operator
            $SQLquery .= ' )';
        }
        else
        {// !(isset($getchildren)) && !(isset($getparents))
            // Return ONLY the info about the category with the given CID
            $SQLquery .= " AND (P1.id = ?) ";
            $bindvars[] = $cid;
        }

    }

    if (isset($eid) && !is_array($eid) && $eid != false) {
       $ecat = xarMod::apiFunc('categories', 'user', 'getcatinfo', Array('cid' => $eid));
       if ($ecat == false) {
           xarSession::setVar('errormsg', xarML('That category does not exist'));
           return Array();
       }
       //$SQLquery .= " AND P1.left_id
       //               NOT BETWEEN ? AND ? ";
       $SQLquery .= " AND (P1.left_id < ? OR P1.left_id > ?)";
       $bindvars[] = $ecat['left']; $bindvars[] = $ecat['right'];
    }

    // Have to specify all selected attributes in GROUP BY
    $SQLquery .= " GROUP BY P1.id, P1.name, P1.description, P1.image, P1.parent_id, P1.left_id, P1.right_id ";

    $having = array();
    // Postgre doesnt accept the output name ('indent' here) as a parameter in the where/having clauses
    // Bug #620
    if (isset($minimum_depth) && is_numeric($minimum_depth)) {
        $having[] = "COUNT(P2.id) >= ?";
        $bindvars[] = $minimum_depth;
    }
    if (isset($maximum_depth) && is_numeric($maximum_depth)) {
        $having[] = "COUNT(P2.id) < ?";
        $bindvars[] = $maximum_depth;
    }
    if (count($having) > 0) {
// TODO: make sure this is supported by all DBs we want
        $SQLquery .= " HAVING " . join(' AND ', $having);
    }

    $SQLquery .= " ORDER BY P1.left_id";

// cfr. xarcachemanager - this approach might change later
    $expire = xarModVars::get('categories','cache.userapi.getcat');
    if (is_numeric($items_per_page) && $items_per_page > 0 && is_numeric($startnum) && $startnum > -1) {
        if (!empty($expire)){
            $result = $dbconn->CacheSelectLimit($expire,$SQLquery, $items_per_page, $startnum, $bindvars);
        } else {
            $result = $dbconn->SelectLimit($SQLquery, $items_per_page, $startnum, $bindvars);
        }
    } else {
        if (!empty($expire)){
            $result = $dbconn->CacheExecute($expire,$SQLquery,$bindvars);
        } else {
            $result = $dbconn->Execute($SQLquery, $bindvars);
        }
    }

    if (!$result) return;

    if ($result->EOF) {
        //It�s ok.. no category found
        // The user doesn�t need to be informed, he will see it....
//        xarSession::setVar('statusmsg', xarML('No category found'));
        return Array();
    }

    $categories = array();

    $index = -1;
    while (!$result->EOF) {
        list($indentation,
                $cid,
                $name,
                $description,
                $image,
                $child_object,
                $parent,
                $left,
                $right,
                $state
               ) = $result->fields;
        $result->MoveNext();

        if (!xarSecurityCheck('ViewCategories',0,'Category',"$name:$cid")) {
             continue;
        }

        if ($indexby == 'cid') {
            $index = $cid;
        } else {
            $index++;
        }

        // are we looking to have the output in the "standard" form?
        if (!empty($dropdown)) {
            $categories[$index+1] = Array(
                'id'         => $cid,
                'name'        => $name,
            );
        } else {
            $categories[$index] = Array(
                'indentation' => $indentation,
                'cid'         => $cid,
                'id'         => $cid,
                'name'        => $name,
                'description' => $description,
                'image'       => $image,
                'child_object'       => $child_object,
                'parent'      => $parent,
                'left'        => $left,
                'right'       => $right,
                'state'       => $state
            );
        }
    }
    $result->Close();

    if (!empty($dropdown)) {
        $categories[0] = array('id' => 0, 'name' => '');
    }

    return $categories;
}

?>
