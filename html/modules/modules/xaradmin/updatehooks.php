<?php
/**
 * File: $Id$
 *
 * Update hooks by hook module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage modules module
 * @author Xaraya Team 
 */
/**
 * Update hooks by hook module
 *
 * @param none
 *
 */
function modules_admin_updatehooks()
{
// Security Check
    if(!xarSecurityCheck('AdminModules')) return;

    if (!xarSecConfirmAuthKey()) return;
    if (!xarVarFetch('curhook', 'str:1:', $curhook)) return; 

    $regId = xarModGetIDFromName($curhook);
    if (!isset($curhook) || !isset($regId)) {
        $msg = xarML('Invalid hook');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                        new SystemException($msg));
        return;
    }

    // Pass to API
    $updated = xarModAPIFunc('modules',
                             'admin',
                             'updatehooks',
                              array('regid' => $regId));

    if (!isset($updated)) return;

    xarResponseRedirect(xarModURL('modules', 'admin', 'hooks',
                                  array('hook' => $curhook)));
    return true;
}

//TODO: <johnny> update for exceptions
?>