<?php
/**
 * File: $Id$
 *
 * Resynchronise properties with object
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
 * resynchronise properties with object (for module & itemtype)
 *
 * @author the DynamicData module development team
 * @param $args['objectid'] object id for the properties you want to update
 * @param $args['moduleid'] new module id for the properties
 * @param $args['itemtype'] new item type for the properties
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION
 */
function dynamicdata_adminapi_syncprops($args)
{
    extract($args);

    // Required arguments
    $invalid = array();
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'object id';
    }
    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $invalid[] = 'module id';
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'item type';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'syncprops', 'DynamicData');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();

    $dynamicprop = $xartable['dynamic_properties'];

    $sql = "UPDATE $dynamicprop
            SET xar_prop_moduleid = " . xarVarPrepForStore($moduleid) . ",
                xar_prop_itemtype = " . xarVarPrepForStore($itemtype) . "
            WHERE xar_prop_objectid = " . xarVarPrepForStore($objectid);

    $result = $dbconn->Execute($sql);

    if (!$result) return;

    return true;
}

