<?php
/**
 * File: $Id$
 *
 * View complete themes information
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
 * View complete module information/details
 * function passes the data to the template
 *
 * @access public
 * @param none
 * @returns array
 * @todo some facelift
 */
function themes_admin_themesinfo()
{
    
    // Security check - not needed here, imo 
    // we just show some info here, not changing anything
    /* if (!xarSecConfirmAuthKey()) return; */

    $data = array();
    
    if (!xarVarFetch('id', 'int:1:', $id)) return; 
    // obtain maximum information about module
    $info = xarThemeGetInfo($id);
    // data vars for template
    $data['themeid']              = xarVarPrepForDisplay($id);
    $data['themename']            = xarVarPrepForDisplay($info['name']);
    $data['themedescr']           = xarVarPrepForDisplay($info['description']);
    //$data['themedispname']        = xarVarPrepForDisplay($themeinfo['displayname']);
    $data['themelisturl']         = xarModURL('themes', 'admin', 'list');

    $data['themedir']             = xarVarPrepForDisplay($info['directory']);
    $data['themeclass']           = xarVarPrepForDisplay($info['class']);
    $data['themever']             = xarVarPrepForDisplay($info['version']);
    $data['themeauthor']          = preg_replace('/,/', '<br />', xarVarPrepForDisplay($info['author']));
    if(!empty($info['dependency'])){
        $dependency             = xarML('Working on it...');
    } else {
        $dependency             = xarML('None');
    }
    $data['themedependency']      = xarVarPrepForDisplay($dependency);
    
    // Redirect
    return $data;
}
?>