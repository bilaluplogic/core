<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 *
 * Mail System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage mail module
 * @author John Cox <admin@dinerminor.com>
 */

/**
 * utility function pass individual menu items to the admin panels
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function mail_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminMail', 0)) {
        $menulinks[] = Array('url' => xarModURL('mail',
                'admin',
                'compose'),
            'title' => xarML('Test your email configuration'),
            'label' => xarML('Test Configuration'));
        $menulinks[] = Array('url' => xarModURL('mail',
                'admin',
                'template'),
            'title' => xarML('Change the mail template for notifications'),
            'label' => xarML('Notification Template'));
        $menulinks[] = Array('url' => xarModURL('mail',
                'admin',
                'modifyconfig'),
            'title' => xarML('Modify the configuration for the utility mail module'),
            'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)) {
        $menulinks = '';
    }

    return $menulinks;
}
?>