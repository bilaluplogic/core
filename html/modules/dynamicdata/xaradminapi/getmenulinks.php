<?php
/**
 * File: $Id$
 *
 * Utility function to pass individual menu items to the main menu
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
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function dynamicdata_adminapi_getmenulinks()
{

    $menulinks = array();

// Security Check
	if (xarSecurityCheck('AdminDynamicData',0)) {

        $menulinks[] = Array('url'   => xarModURL('dynamicdata',
                                                   'admin',
                                                   'view'),
                              'title' => xarML('View module objects using dynamic data'),
                              'label' => xarML('View Objects'));
    }

// Security Check
	if (xarSecurityCheck('AdminDynamicData',0)) {
        $menulinks[] = Array('url'   => xarModURL('dynamicdata',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Configure the default property types'),
                              'label' => xarML('Property Types'));
    }

// Security Check
	if (xarSecurityCheck('AdminDynamicData',0)) {
        $menulinks[] = Array('url'   => xarModURL('dynamicdata',
                                                  'util',
                                                  'main'),
                              'title' => xarML('Import/export and other utilities'),
                              'label' => xarML('Utilities'));
    }

    return $menulinks;
}

?>
