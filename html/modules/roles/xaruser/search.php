<?php
/**
 * File: $Id$
 *
 * Search
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Roles Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
function roles_user_search()
{
    if(!xarVarFetch('startnum', 'isset', $startnum,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('email',    'isset', $email,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('uname',    'isset', $uname,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('name',     'isset', $name,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('q',        'isset', $q,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('bool',     'isset', $bool,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('author',   'isset', $author,    NULL, XARVAR_DONT_SET)) {return;}
    $data = array();
    $data['users'] = array();
    // show the search form
    if (!isset($q)) {
        if (xarModIsHooked('dynamicdata','roles')) {
            // get the Dynamic Object defined for this module
            $object =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'roles'));
            if (isset($object) && !empty($object->objectid)) {
                // get the Dynamic Properties of this object
                $data['properties'] =& $object->getProperties();
            }
        }
        return $data;
    }

    // execute the search

    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = 20;
    }

    // TODO: support wild cards / boolean / quotes / ... (cfr. articles) ?

    // remember what we selected before
    $data['checked'] = array();

    if (xarModIsHooked('dynamicdata','roles')) {
        // make sure the DD classes are loaded
        if (!xarModAPILoad('dynamicdata','user')) return $data;

        // get a new object list for roles
        $object = new Dynamic_Object_List(array('moduleid'  => xarModGetIDFromName('roles')));

        if (isset($object) && !empty($object->objectid)) {
            // save the properties for the search form
            $data['properties'] =& $object->getProperties();

            // run the search query
            // FIXME: could this be just addslashes?
            $q = xarVarPrepForStore($q);
            $where = array();
            // see which properties we're supposed to search in
            foreach (array_keys($object->properties) as $field) {
                $checkfield = xarVarCleanFromInput($field);
                if (!empty($checkfield)) {
                    $where[] = $field . " LIKE '%" . $q . "%'";
                    $where[] = $field . " LIKE '%" . strtoupper($q) . "%'";
                    $where[] = $field . " LIKE '%" . strtolower($q) . "%'";
                    $where[] = $field . " LIKE '%" . ucfirst($q) . "%'";
                    $where[] = $field . " LIKE '%" . ucwords($q) . "%'";
                    // remember what we selected before
                    $data['checked'][$field] = 1;
                }
            }
            if (count($where) > 0) {
            // TODO: refresh fieldlist of datastore(s) before getting items
                $items =& $object->getItems(array('where' => join(' or ', $where)));

                if (isset($items) && count($items) > 0) {
                // TODO: combine retrieval of roles info above
                    foreach (array_keys($items) as $uid) {
                        if (isset($data['users'][$uid])) {
                            continue;
                        }
                        // Get user information
                        $data['users'][$uid] = xarModAPIFunc('roles',
                                                             'user',
                                                             'get',
                                                             array('uid' => $uid));
                    }
                }
            }
        }
    }

    $selection = " AND (";
    $selection .= "(xar_name LIKE '%" . $q . "%')";
    $selection .= " OR (xar_uname LIKE '%" . $q . "%')";

    if (xarModGetVar('roles', 'searchbyemail')) {
        $selection .= " OR (xar_email LIKE '%" . $q . "%')";
    }
    
    $selection .= ")";

    $data['total'] = xarModAPIFunc('roles',
                                   'user',
                                   'countall',
                                   array('selection'   => $selection,
                                         'include_anonymous' => false,
                                         'include_myself' => false));

    if (!$data['total']){
        $data['status'] = xarML('No Users Found Matching Search Criteria');
        $data['total'] = 0;
        return $data;
    }

    $users = xarModAPIFunc('roles',
                           'user',
                           'getall',
                            array('startnum' => $startnum,
                                  'selection'   => $selection,
                                  'include_anonymous' => false,
                                  'include_myself' => false,
                                  'numitems' => xarModGetVar('roles',
                                                             'rolesperpage')));

    $data['users'] = $users;

    if (count($data['users']) == 0){
        $data['status'] = xarML('No Users Found Matching Search Criteria');
    }
    return $data;
}
?>