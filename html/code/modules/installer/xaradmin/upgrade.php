<?php
function installer_admin_upgrade()
{
    // Security
    if (!xarSecurityCheck('AdminInstaller')) return; 
    
    if(!xarVarFetch('phase','int', $data['phase'], 1, XARVAR_DONT_SET)) {return;}

    // Version information
    $fileversion = XARCORE_VERSION_NUM;
    $dbversion = xarConfigVars::get(null, 'System.Core.VersionNum');
    sys::import('xaraya.version');
    
    // Versions prior to 2.1.0 had the revision number as version number, or something else
    if (strlen($dbversion) == 41 || empty($dbversion) || $dbversion == 'unknown') {
        $data['versioncompare'] = 1;
        $data['upgradable'] = 1;
        $data['oldversionnum'] = $dbversion;
    } else {
        $data['versioncompare'] = xarVersion::compare($fileversion, $dbversion);
        $data['upgradable'] = xarVersion::compare($fileversion, '2.0.0') > 0;
    }
    
    // Core modules
    $data['coremodules'] = array(
                                42    => 'authsystem',
                                68    => 'base',
                                13    => 'blocks',
                                182   => 'dynamicdata',
                                200   => 'installer',
                                771   => 'mail',
                                1     => 'modules',
                                1098  => 'privileges',
                                27    => 'roles',
                                17    => 'themes',
    );
    $data['versions'] = array(
                                '2.1.1',
    );
    
    if ($data['phase'] == 1) {
        $data['active_step'] = 1;

    } elseif ($data['phase'] == 2) {
        $data['active_step'] = 2;

        // Get the list of version upgrades
        Upgrader::loadFile('upgrades/upgrade_list.php');
        $upgrade_list = installer_adminapi_get_upgrade_list();

        // Run the upgrades
        foreach ($upgrade_list as $upgrade_version) {
            if (!Upgrader::loadFile('upgrades/' . $upgrade_version .'/main.php')) {
                $data['upgrade']['errormessage'] = Upgrader::$errormessage;
                return $data;
            }
            $upgrade_function = 'main_upgrade_' . $upgrade_version;
            $data = array_merge($data,$upgrade_function());
        }
        
    } elseif ($data['phase'] == 3) {
        $data['active_step'] = 3;
        // Align the db and filesystem version info
        xarConfigVars::set(null, 'System.Core.VersionId', xarCore::VERSION_ID);
        xarConfigVars::set(null, 'System.Core.VersionNum', xarCore::VERSION_NUM);
        xarConfigVars::set(null, 'System.Core.VersionRev', xarCore::VERSION_REV);
        xarConfigVars::set(null, 'System.Core.VersionSub', xarCore::VERSION_SUB);
        
        // Get the list of version checks
        Upgrader::loadFile('checks/check_list.php');
        $check_list = installer_adminapi_get_check_list();

        // Run the checks
        foreach ($check_list as $check_version) {
            if (!Upgrader::loadFile('checks/' . $check_version .'/main.php')) {
                $data['check']['errormessage'] = Upgrader::$errormessage;
                return $data;
            }
            $check_function = 'main_check_' . $check_version;
            $data = array_merge($data,$check_function());
        }

    } elseif ($data['phase'] == 4) {
        $data['active_step'] = 4;
//        xarResponse::redirect(xarServer::getCurrentURL(array('phase' => 4)));
    }
    return $data;
}

?>