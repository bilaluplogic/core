<?php
/**
 * File: $Id$
 *
 * Get module information from xarversion.php for each module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage modules module
 * @author Xaraya Team 
 */
/**
 * Get module information from xarversion.php for each module
 *
 * Here we cycle through the modules directory and and
 * return an array of information from xarversion.php of each module.
 *
 * Excluded directories:
 * CVS - this is a special directory of the Concurrent Versioning System
 * SCCS - where Bitkeeper stores source files
 * PENDING - where Bitkeeper stores pending merges
 *
 * @param none
 * @returns array
 * @return an array of modules from the file system
 */
function modules_adminapi_getfilemodules()
{
    $fileModules = array();
    $dh = opendir('modules');

    while ($modOsDir = readdir($dh)) {
        switch ($modOsDir) {
            case '.':
            case '..':
            case 'CVS':
            case 'SCCS':
            case 'PENDING':
            case 'notinstalled':
                break;
            default:
                if (is_dir("modules/$modOsDir")) {

                    // no xarversion.php, no module
                    $modFileInfo = xarMod_getFileInfo($modOsDir);
                    if (!isset($modFileInfo)) {
                        continue;
                    }

                    // Found a directory
                    $name         = $modOsDir;
                    $nameinfile   = $modFileInfo['name'];
                    $regId        = $modFileInfo['id'];
                    $version      = $modFileInfo['version'];
                    $mode         = XARMOD_MODE_SHARED;
                    $class        = $modFileInfo['class'];
                    $category     = $modFileInfo['category'];
                    $adminCapable = $modFileInfo['admin_capable'];
                    $userCapable  = $modFileInfo['user_capable'];
                    $dependency   = $modFileInfo['dependency'];

                    // TODO: beautify :-)
                    if (!isset($regId)) {
                        xarSessionSetVar('errormsg', "Module '$name' doesn't seem to have a registered module ID defined in xarversion.php - skipping...\nPlease register your module at http://www.xaraya.com");
                        continue;
                    }

                    //Defaults
                    if (!isset($version)) {
                        $version = 0;
                    }

                    //FIXME: <johnny> add class and category checking
                    if (!isset($class)) {
                        $class = 'Miscellaneous';
                    }

                    if (!isset($category)) {
                        $category = 'Miscellaneous';
                    }

                    // Work out if admin-capable
                    if (!isset($adminCapable)) {
                        $adminCapable = 0;
                    }

                    //FIXME: <johnny> remove this when xarversion.php contains the user setting
                    if (file_exists('modules/' . $modOsDir .'/xaruser.php')) {
                        $userCapable = 1;
                    }

                    // No dependency information = ok
                    if (!isset($dependency)) {
                        $dependency = array();
                    }

                    //FIXME: <johnny> this detection isn't finished yet... we should be checking
                    //for xaruser.php and then overriding with if $modFileInfo['user_capable'] is 1
                    // Work out if user-capable
                    if (1 == $modFileInfo['user_capable']) {
                        $userCapable = 1;
                    } else {
                        $userCapable = 0;
                    }

                    //Check for duplicates
                    foreach ($fileModules as $module) {
                        if($regId == $module['regid']) {
                            $msg = xarML('The same registered ID (#(1)) was found in two different modules, #(2) and #(3). Please remove one of the modules and regenerate the list.', $regId, $name, $module['name']);
                            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                           new SystemException($msg));
                        }
                        if($nameinfile == $module['nameinfile']) {
                            $msg = xarML('The module #(1) was found under two different registered IDs, #(2) and #(3). Please remove one of the modules and regenerate the list', $nameinfile, $regId, $module['regid']);
                            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                                           new SystemException($msg));
                        }
                    }
                    $fileModules[$name] = array('directory'     => $modOsDir,
                                                'name'          => $name,
                                                'nameinfile'    => $nameinfile,
                                                'regid'         => $regId,
                                                'version'       => $version,
                                                'mode'          => $mode,
                                                'class'         => $class,
                                                'category'      => $category,
                                                'admin_capable' => $adminCapable,
                                                'user_capable'  => $userCapable,
                                                'dependency'    => $dependency);
                } // if
        } // switch
    } // while
    closedir($dh);

    return $fileModules;
}

?>
