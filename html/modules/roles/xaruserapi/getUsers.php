<?php

/**
 * getUsers - view users in group
 * @param $args['uid'] group id
 * @return $users array containing uname, uid
 */
function roles_userapi_getUsers($args)
{
    extract($args);

    if(!isset($uid)) {
        $msg = xarML('Wrong arguments to roles_userapi_getusers.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,
                    'BAD_PARAM',
                     new SystemException($msg));
        return false;
    }

// Security Check
    if(!xarSecurityCheck('ReadRole')) return;

    $roles = new xarRoles();
    $role = $roles->getRole($uid);

    $users = $role->getUsers();

    $flatusers = array();
    foreach($users as $user) {
        $flatusers[] = array('uid' => $user->getID(),
                        'uname' => $user->getUser()
                        );
    }

    return $flatusers;
}

?>