<?php
/**
 * File: $Id$
 *
 * List modules and current settings
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage modules module
 * @author Xaraya Team 
 */
/**
 * List modules and current settings
 * @param several params from the associated form in template
 *
 */
function modules_admin_settings()
{
    // Security Check
    if(!xarSecurityCheck('AdminModules')) return;

    if (!xarVarFetch('hidecore', 'str:1:', $hidecore, '0', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('selstyle', 'str:1:', $selstyle, 'plain', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('selfilter', 'str:1:', $selfilter, 'XARMOD_STATE_ANY', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('selsort', 'str:1:', $selsort, 'namedesc', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('regen', 'str:1:', $regen, XARVAR_NOT_REQUIRED)) return; 
    
    xarModSetVar('modules', 'hidecore', $hidecore);
    xarModSetVar('modules', 'selstyle', $selstyle);
    xarModSetVar('modules', 'selfilter', $selfilter);
    xarModSetVar('modules', 'selsort', $selsort);
    
    xarResponseRedirect(xarModURL('modules', 'admin', 'list', array('regen' => $regen)));
}

?>