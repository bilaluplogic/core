<?php
/**
 * File: $Id$
 *
 * Get al data fields for an item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata module
 * @author mikespub <mikespub@xaraya.com>
*/
/**
 * get all data fields (dynamic or static) for an item
 * (identified by module + item type + item id or table + item id)
 *
 * @author the DynamicData module development team
 * @param $args['module'] module name of the item fields to get or
 * @param $args['modid'] module id of the item fields to get +
 * @param $args['itemtype'] item type of the item fields to get, or
 * @param $args['table'] database table to turn into an object
 * @param $args['itemid'] item id of the item fields to get
 * @param $args['fieldlist'] array of field labels to retrieve (default is all)
 * @param $args['status'] limit to property fields of a certain status (e.g. active)
 * @param $args['join'] join a module table to the dynamic object (if it extends the table)
 * @param $args['getobject'] flag indicating if you want to get the whole object back
 * @param $args['preview'] flag indicating if you're previewing an item
 * @returns array
 * @return array of (name => value), or false on failure
 * @raise BAD_PARAM, NO_PERMISSION
 */
function &dynamicdata_userapi_getitem($args)
{
    extract($args);

    if (empty($modid) && empty($moduleid)) {
        if (empty($module)) {
            $modname = xarModGetName();
        } else {
            $modname = $module;
        }
        if (is_numeric($modname)) {
            $modid = $modname;
        } else {
            $modid = xarModGetIDFromName($modname);
        }
    } elseif (empty($modid)) {
        $modid = $moduleid;
    }
    $modinfo = xarModGetInfo($modid);

    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $invalid = array();
    if (!isset($modid) || !is_numeric($modid) || empty($modinfo['name'])) {
        $invalid[] = 'module id';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'item type';
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $invalid[] = 'item id';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getall', 'DynamicData');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

	if(!xarSecurityCheck('ViewDynamicDataItems',1,'Item',"$modid:$itemtype:$itemid")) return;

    // check the optional field list
    if (empty($fieldlist)) {
        $fieldlist = null;
    } elseif (is_string($fieldlist)) {
        // support comma-separated field list
        $fieldlist = explode(',',$fieldlist);
    }

    // limit to property fields of a certain status (e.g. active)
    if (!isset($status)) {
        $status = null;
    }

    // join a module table to a dynamic object
    if (empty($join)) {
        $join = '';
    }
    // make some database table available via DD
    if (empty($table)) {
        $table = '';
    }

    $object = new Dynamic_Object(array('moduleid'  => $modid,
                                       'itemtype'  => $itemtype,
                                       'itemid'    => $itemid,
                                       'fieldlist' => $fieldlist,
                                       'join'      => $join,
                                       'table'     => $table,
                                       'status'    => $status));
    if (!isset($object) || empty($object->objectid)) return;
    if (!empty($itemid)) {
        $object->getItem();
    }
    if (!empty($preview)) {
        $object->checkInput();
    }

    if (!empty($getobject)) {
        return $object;
    }

    if (count($object->fieldlist) > 0) {
        $fieldlist = $object->fieldlist;
    } else {
        $fieldlist = array_keys($object->properties);
    }
    $fields = array();
    foreach ($fieldlist as $name) {
        $property = $object->properties[$name];
		if(xarSecurityCheck('ReadDynamicDataField',0,'Field',$property->name.':'.$property->type.':'.$property->id)) {
            $fields[$name] = $property->value;
        }
    }

    return $fields;
}

?>
