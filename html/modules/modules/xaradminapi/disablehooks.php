<?php
/**
 * File: $Id$
 *
 * Disable hooks between a caller module and a hook module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage modules module
 * @author Xaraya Team 
 */
/**
 * Disable hooks between a caller module and a hook module
 * Note : generic hooks will not be disabled if a specific item type is given
 *
 * @param $args['callerModName'] caller module
 * @param $args['callerItemType'] optional item type for the caller module
 * @param $args['hookModName'] hook module
 * @returns bool
 * @return true if successfull
 * @raise BAD_PARAM
 */
function modules_adminapi_disablehooks($args)
{
// Security Check (called by other modules, so we can't use one this here)
//    if(!xarSecurityCheck('AdminModules')) return;

    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($callerModName) || empty($hookModName)) {
        $msg = xarML('callerModName or hookModName');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', $msg);
        return;
    }
    if (empty($callerItemType)) {
        $callerItemType = '';
    }

    // Rename operation
    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Delete hooks regardless
    $sql = "DELETE FROM $xartable[hooks]
            WHERE xar_smodule = '" . xarVarPrepForStore($callerModName) . "'
              AND xar_stype = '" . xarVarPrepForStore($callerItemType) . "'
              AND xar_tmodule = '" . xarVarPrepForStore($hookModName) . "'";

    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    return true;
}

?>