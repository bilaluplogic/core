<?php
/**
 * File: $Id$
 *
 * Update a users status
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Roles Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * Update a users status
 * @param $args['uname'] is the users system name
 * @param $args['state'] is the new state for the user
 * returns bool
 */
function roles_userapi_updatestatus($args)
{
    extract($args);

    if ((!isset($uname)) ||
        (!isset($state))) {
        $msg = xarML('Invalid Parameter Count',
                      join (', ',$invalid), 'user', 'updatestatus', 'roles');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('ViewRoles')) return;

    // Get DB Set-up
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $rolesTable = $xartable['roles'];

    // Update the status
    $query = "UPDATE $rolesTable
             SET xar_valcode = '',
                 xar_state = '" . xarVarPrepForStore($state) . "'
             WHERE xar_uname = '" . xarVarPrepForStore($uname) . "'";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>