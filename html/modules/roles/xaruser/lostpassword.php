<?php
/**
 * File: $Id$
 *
 * Sends a new password to the user if they have fortotten theirs.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Roles Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * Sends a new password to the user if they have forgotten theirs.
 *
 */
function roles_user_lostpassword()
{
    // Security check
    if (!xarSecurityCheck('ViewRoles')) return;

    //If a user is already logged in, no reason to see this.
    //We are going to send them to their account.
    if (xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('roles',
                                      'user',
                                      'account',
                                       array('uid' => $item['uid'])));
       return true;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Lost Password')));

    if (!xarVarFetch('phase','str:1:100',$phase,'request',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    switch(strtolower($phase)) {

        case 'request':
        default:
            $authid = xarSecGenAuthKey();
            $data = xarTplModule('roles','user', 'requestpw', array('authid'    => $authid,
                                                                    'emaillabel' => xarML('E-Mail New Password')));

            break;

        case 'send':

            if (!xarVarFetch('uname','str:1:100',$uname,'',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('email','str:1:100',$email,'',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            if ((empty($uname)) && (empty($email))) {
                $msg = xarML('You must enter your username or your email to proceed');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }

            // check for user and grab uid if exists
            $user = xarModAPIFunc('roles',
                                  'user',
                                  'get',
                                   array('uname' => $uname,
                                         'email' => $email));

            if (empty($user)) {
                $msg = xarML('That email address or username is not registered');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }
            // Make new password
            $user['pass'] = xarModAPIFunc('roles',
                                  'user',
                                  'makepass');

            if (empty($user['pass'])) {
                $msg = xarML('Problem generating new password');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }
            //Update user password
            // check for user and grab uid if exists
            if (!xarModAPIFunc('roles','admin','update',$user)) {
            	$msg = xarML('Problem updating the user information');
                xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            }
  			// Send Reminder Email
            if (!xarModAPIFunc('roles', 'admin','senduseremail', array('uid' => array($user['uid'] => '1'), 'mailtype' => 'reminder', 'pass' => $user['pass']))) return;

            // Let user know that they have an email on the way.   
            $data = xarTplModule('roles','user','requestpwconfirm');
          break;
    }
    return $data;
}
?>