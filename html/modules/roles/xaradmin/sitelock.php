<?php

function roles_admin_sitelock($args)
{
    // Security Check
    if(!xarSecurityCheck('AdminRole')) return;

    if (!xarVarFetch('cmd', 'isset', $cmd, NULL, XARVAR_DONT_SET)) return;

    if(!isset($cmd)) {
    // Get parameters from the db
        $lockvars = unserialize(xarModGetVar('roles','lockdata'));
        $toggle = $lockvars['locked'];
        $roles = $lockvars['roles'];
        $lockedoutmsg = (!isset($lockvars['message']) || $lockvars['message'] == '') ? xarML('The site is currently locked. Thank you for your patience.') : $lockvars['message'];
        $notifymsg = $lockvars['notifymsg'];
    }
    else {
    // Get parameters from input
        if (!xarVarFetch('serialroles', 'str', $serialroles, NULL, XARVAR_NOT_REQUIRED)) return;
        $roles = unserialize($serialroles);
        if (!xarVarFetch('lockedoutmsg', 'str', $lockedoutmsg, NULL, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('notifymsg', 'str', $notifymsg, NULL, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('toggle', 'str', $toggle, NULL, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('notify', 'isset', $notify, NULL, XARVAR_DONT_SET)) return;
        if(!isset($notify)) $notify = array();
        for($i=0;$i<count($roles);$i++) $roles[$i]['notify'] = in_array($roles[$i]['uid'],$notify);

        if ($cmd == 'delete') {
            if (!xarVarFetch('uid', 'int', $uid, NULL, XARVAR_DONT_SET)) return;
            if (isset($uid)) {
                for($i=0;$i<count($roles);$i++) {
                    if ($roles[$i]['uid'] == $uid) {
                        array_splice($roles,$i,1);
                        break;
                    }
                }
            // Write the configuration to disk
            $lockdata = array('roles' => $roles,
                              'message' => $lockedoutmsg,
                              'locked' => $toggle,
                              'notifymsg' => $notifymsg);
            xarModSetVar('roles', 'lockdata', serialize($lockdata));
            }
        }

        elseif ($cmd == 'add') {
            if (!xarVarFetch('newname', 'str', $newname, NULL, XARVAR_DONT_SET)) return;
            if (isset($newname)) {
                $r = xaruFindRole($newname);
                if (!$r) $r = xarFindRole($newname);
                if($r) {
                    $newuid = $r->getID();
                    $newname = $r->getUser();
                }
                else $newuid = 0;

                $newelement = array('uid' => $newuid, 'name' => $newname , 'notify' => TRUE);
                if ($newuid != 0 && !in_array($newelement,$roles))
                    $roles[] = $newelement;

            // Write the configuration to disk
            $lockdata = array('roles' => $roles,
                              'message' => $lockedoutmsg,
                              'locked' => $toggle,
                              'notifymsg' => $notifymsg);
            xarModSetVar('roles', 'lockdata', serialize($lockdata));
            }
        }

        elseif ($cmd == 'save') {
            $lockdata = array('roles' => $roles,
                              'message' => $lockedoutmsg,
                              'locked' => $toggle,
                              'notifymsg' => $notifymsg);
            xarModSetVar('roles', 'lockdata', serialize($lockdata));
            xarResponseRedirect(xarModURL('roles', 'admin', 'sitelock'));
        }

        elseif ($cmd == 'toggle') {

            // turn the site on or off
            $toggle = $toggle ? 0 : 1;

            // Find the users to be notified
            // First get the roles
            $rolesarray = array();
            $rolemaker = new xarRoles();
            for($i=0;$i<count($roles);$i++) {
                if($roles[$i]['notify'] == 1) {
                    $rolesarray[] = $rolemaker->getRole($roles[$i]['uid']);
                }
            }
            //Check each if it is a user or a group
            $notify = array();
            foreach($rolesarray as $roletotell) {
                if ($roletotell->isUser()) $notify[] = $roletotell;
                else $notify = array_merge($notify,$roletotell->getUsers());
            }
            $admin = xarUFindRole('Admin');
            $mailinfo = array('subject' => 'Site Lock',
                              'from' => $admin->getEmail()
            );

// We locked the site
            if ($toggle == 1) {

            // Clear the active sessions
                $spared = array();
                for($i=0;$i<count($roles);$i++) $spared[] = $roles[$i]['uid'];
                if(!xarModAPIFunc('roles','admin','clearsessions', $spared)) {
                    $msg = xarML('Could not clear sessions table');
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                    'DATABASE_ERROR',
                     new SystemException($msg));
                     return;
                }
                $mailinfo['message'] = 'The site ' . xarModGetVar('themes','SiteName') . ' has been locked.';
            }
// We unlocked the site
            else {
               $mailinfo['message'] = 'The site ' . xarModGetVar('themes','SiteName') . ' has been unlocked.';
            }

            $mailinfo['message'] .= "\n\n" . $notifymsg;

            // Send the mails
            foreach($notify as $recipient) {
                $mailinfo['info'] = $recipient->getEmail();
                xarModAPIFunc('mail','admin','sendmail', $mailinfo);
            }

            // Write the configuration to disk
            $lockdata = array('roles' => $roles,
                              'message' => $lockedoutmsg,
                              'locked' => $toggle,
                              'notifymsg' => $notifymsg);
            xarModSetVar('roles', 'lockdata', serialize($lockdata));
        }
    }


    $data['roles'] = $roles;
    $data['serialroles'] = xarVarPrepForDisplay(serialize($roles));
    $data['lockedoutmsg'] = $lockedoutmsg;
    $data['notifymsg'] = $notifymsg;
    $data['toggle'] = $toggle;
    if ($toggle == 1) {
        $data['togglelabel']    = xarML('Unlock the Site');
        $data['statusmessage']    = xarML('The site is locked');
    }
    else {
        $data['togglelabel']    = xarML('Lock the Site');
        $data['statusmessage']    = xarML('The site is unlocked');
    }
    $data['addlabel']    = xarML('Add a role');
    $data['deletelabel']    = xarML('Remove');
    $data['savelabel']    = xarML('Save the configuration');

    return $data;
}

?>