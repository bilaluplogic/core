<?php
/** 
 * File: $Id$
 *
 * Utility function to pass individual menu items to the main menu
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
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function blocks_adminapi_getmenulinks()
{
	if (xarSecurityCheck('EditBlock',0)) {

        $menulinks[] = Array('url'   => xarModURL('blocks',
                                                   'admin',
                                                   'view_instances'),
                              'title' => xarML('View or edit all block instances'),
                              'label' => xarML('View Instances'));
    }
	if (xarSecurityCheck('AddBlock',0)) {

        $menulinks[] = Array('url'   => xarModURL('blocks',
                                                   'admin',
                                                   'new_instance'),
                              'title' => xarML('Add a new block instance'),
                              'label' => xarML('Add Instance'));
    }
	if (xarSecurityCheck('AddBlock',0)) {

        $menulinks[] = Array('url'   => xarModURL('blocks',
                                                   'admin',
                                                   'new_group'),
                              'title' => xarML('Add a new group of blocks'),
                              'label' => xarML('Add Group'));
    }

	if (xarSecurityCheck('AdminBlock',0)) {

        $menulinks[] = Array('url'   => xarModURL('blocks',
                                                  'admin',
                                                  'registerblock'),
                              'title' => xarML('Add a new block type into the system'),
                              'label' => xarML('Register Block'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>
