<?php
/**
 * File: $Id$
 *
 * Activate a theme
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Themes
 * @author Marty Vance
*/
/**
 * Activate a theme
 * 
 * Loads theme admin API and calls the activate
 * function to actually perform the activation,
 * then redirects to the list function with a
 * status message and returns true.
 * 
 * @param id $ the theme id to activate
 * @returns 
 * @return 
 */
function themes_admin_activate()
{ 
    // Security and sanity checks
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('id', 'int:1:', $id)) return; 
    // Activate
    $activated = xarModAPIFunc('themes',
        'admin',
        'activate',
        array('regid' => $id)); 
    // throw back
    if (!isset($activated)) return; 
    // Set State
    $set = xarModAPIFunc('themes',
        'admin',
        'setstate',
        array('regid' => $id,
            'state' => XARTHEME_STATE_ACTIVE)); 
    // throw back
    if (!isset($set)) return;

    xarResponseRedirect(xarModURL('themes', 'admin', 'list'));

    return true;
} 

?>
