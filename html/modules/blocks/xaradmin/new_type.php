<?php
/** 
 * File: $Id$
 *
 * Register new block type
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Blocks administration
 * @author Jim McDonald, Paul Rosania
*/
/**
 * Register New Block Type
 */
function blocks_admin_new_type()
{
    // Security Check
    // FIXME: not sure what the security check should be?
	if (!xarSecurityCheck('AdminBlock', 0, 'Instance')) {return;}

    // Get parameters
    if (!xarVarFetch('modulename', 'str:1:', $modulename, 'base', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('blockname', 'str:1:', $blockname, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('submit', 'str:1:', $submit, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('scan', 'str:1:', $scan, '', XARVAR_NOT_REQUIRED)) {return;}

    // Initialise the list.
    $type_list = array();

    if (!empty($scan) && !empty($modulename)) {
        // 'Scan' button pressed.
    
        // Get a list of block types from the module files.
        $modid = xarModGetIDFromName($modulename);
        $modinfo = xarModGetInfo($modid);
        if (!empty($modinfo)) {
            // TODO: should 'modules' be hard-coded here?
            $blocks_path = 'modules/' . $modinfo['directory'] . '/xarblocks';

            // Open the directory and read all the files.
            $dir_handle = @opendir($blocks_path);
            if ($dir_handle !== FALSE) {
                while (false !== ($file = readdir($dir_handle))) {
                    // A block file contains no underscores, and is not 'index.php'
                    if (preg_match('/^[a-z0-9]+\.php$/', $file) && $file != 'index.php') {
                        // Add the name of the block type to the list.
                        $type_list[]['name'] = str_replace('.php', '', $file);
                    }
                }
                closedir($dir_handle);
            }
        }
    }

    
    if (!empty($submit)) {
        // Submit button was pressed

        // Confirm Auth Key
        if (!xarSecConfirmAuthKey()) {return;}

        // Create the block type.
        if (!xarModAPIFunc(
            'blocks', 'admin', 'create_type',
            array('module' => $modulename, 'type' => $blockname))
        ) {return;}

        xarResponseRedirect(xarModURL('blocks', 'admin', 'view_types'));
        return true;
    } else {
        // Nothing submited yet - return a blank form.
        return array(
            'authid' => xarSecGenAuthKey(),
            'module_list' => xarModAPIfunc('modules', 'admin', 'getlist'),
            'modulename' => $modulename,
            'type_list' => $type_list,
            'blockname' => $blockname
        );
    }
}

?>