<?php
/**
 * File: public.conf.php
 *
 * Configuration file for a public site
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage installer
 * @author Marc Lutolf
 */

$configuration_name = 'Public Site';

function installer_public_moduleoptions() {
    return array(
        array('name' => "autolinks",            'regid' => 11),
        array('name' => "bloggerapi",           'regid' => 745),
        array('name' => "categories",           'regid' => 147),
        array('name' => "comments",             'regid' => 14),
        array('name' => "example",              'regid' => 36),
        array('name' => "hitcount",             'regid' => 177),
        array('name' => "metaweblogapi",        'regid' => 747),
        array('name' => "ratings",              'regid' => 41),
        array('name' => "search",               'regid' => 32),
        array('name' => "soapserver",           'regid' => 748),
        array('name' => "wiki",                 'regid' => 28),
        array('name' => "xmlrpcserver",         'regid' => 743),
        array('name' => "xmlrpcsystemapi",      'regid' => 744),
        array('name' => "xmlrpcvalidatorapi",   'regid' => 746),
        array('name' => "articles",             'regid' => 151)
    );
}
function installer_public_privilegeoptions() {
    return array(
                  array(
                        'item' => 'p1',
                        'option' => 'true',
                        'comment' => xarML('Registered users have read access to all modules of the site.')
                        ),
                  array(
                        'item' => 'p2',
                        'option' => 'false',
                        'comment' => xarML('Unregistered users have read access to the non-core modules of the site. If this option is not chosen unregistered users see only the first page.')
                        )
                  );
}

/**
 * Load the configuration
 *
 * @access public
 * @return boolean
 */
function installer_public_configuration_load($args)
{
/*    $content['marker'] = '[x]';                                           // create the user menu
    $content['displaymodules'] = 1;
    $content['content'] = '';

    // Load up database
    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();

    $blockGroupsTable = $tables['block_groups'];

    $query = "SELECT    xar_id as id
              FROM      $blockGroupsTable
              WHERE     xar_name = 'left'";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Freak if we don't get one and only one result
    if ($result->PO_RecordCount() != 1) {
        $msg = xarML("Group 'left' not found.");
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list ($leftBlockGroup) = $result->fields;

    $adminBlockId= xarModAPIFunc('blocks',
                                 'admin',
                                 'block_type_exists',
                                 array('modName'  => 'base',
                                       'blockType'=> 'menu'));

    if (!isset($adminBlockId) && xarExceptionMajor() != XAR_NO_EXCEPTION) {
        return;
    }

    xarModAPIFunc('blocks','admin','create_instance',array('title' => 'Main Menu',
                                                           'type' => $adminBlockId,
                                                           'group' => $leftBlockGroup,
                                                           'template' => '',
                                                           'content' => serialize($content),
                                                           'state' => 2));
*/
// now do the necessary loading for each item

    if(in_array('p1',$args)) {
        installer_public_moderatenoncore();
        xarAssignPrivilege('ModerateNonCore','Users');
    }
    else {
        installer_public_readnoncore();
        xarAssignPrivilege('ReadNonCore','Users');
    }

    if(in_array('p2',$args)) {
        installer_public_commentnoncore();
        xarAssignPrivilege('CommentNonCore','Everybody');
   }
    else {
        if(in_array('p1',$args)) installer_public_readnoncore2();
        xarAssignPrivilege('ReadNonCore','Everybody');
    }

    return true;
}

function installer_public_commentnoncore()
{
    xarRegisterPrivilege('CommentNonCore','All','empty','All','All','ACCESS_NONE','Read access only to none-core modules');
    xarRegisterPrivilege('CommentAccess','All','All','All','All','ACCESS_COMMENT','Comment access to all modules');
    xarMakePrivilegeRoot('CommentNonCore');
    xarMakePrivilegeRoot('CommentAccess');
    xarMakePrivilegeMember('CommentAccess','CommentNonCore');
    xarMakePrivilegeMember('DenyPrivileges','CommentNonCore');
    xarMakePrivilegeMember('DenyAdminPanels','CommentNonCore');
    xarMakePrivilegeMember('DenyBlocks','CommentNonCore');
    xarMakePrivilegeMember('DenyMail','CommentNonCore');
    xarMakePrivilegeMember('DenyModules','CommentNonCore');
    xarMakePrivilegeMember('DenyThemes','CommentNonCore');
}

function installer_public_moderatenoncore()
{
    xarRegisterPrivilege('ModerateNonCore','All','empty','All','All','ACCESS_NONE','Read access only to none-core modules');
    xarRegisterPrivilege('ModerateAccess','All','All','All','All','ACCESS_MODERATE','Moderate access to all modules');
    xarRegisterPrivilege('DenyPrivileges','All','privileges','All','All','ACCESS_NONE','Deny access to the Privileges module');
    xarRegisterPrivilege('DenyAdminPanels','All','adminpanels','All','All','ACCESS_NONE','Deny access to the AdminPanels module');
    xarRegisterPrivilege('DenyBlocks','All','blocks','All','All','ACCESS_NONE','Deny access to the Blocks module');
    xarRegisterPrivilege('DenyMail','All','mail','All','All','ACCESS_NONE','Deny access to the Mail module');
    xarRegisterPrivilege('DenyModules','All','modules','All','All','ACCESS_NONE','Deny access to the Modules module');
    xarRegisterPrivilege('DenyThemes','All','themes','All','All','ACCESS_NONE','Deny access to the Themes module');
    xarMakePrivilegeRoot('ModerateNonCore');
    xarMakePrivilegeRoot('ModerateAccess');
    xarMakePrivilegeRoot('DenyPrivileges');
    xarMakePrivilegeRoot('DenyAdminPanels');
    xarMakePrivilegeRoot('DenyBlocks');
    xarMakePrivilegeRoot('DenyMail');
    xarMakePrivilegeRoot('DenyModules');
    xarMakePrivilegeRoot('DenyThemes');
    xarMakePrivilegeMember('ModerateAccess','ModerateNonCore');
    xarMakePrivilegeMember('DenyPrivileges','ModerateNonCore');
    xarMakePrivilegeMember('DenyAdminPanels','ModerateNonCore');
    xarMakePrivilegeMember('DenyBlocks','ModerateNonCore');
    xarMakePrivilegeMember('DenyMail','ModerateNonCore');
    xarMakePrivilegeMember('DenyModules','ModerateNonCore');
    xarMakePrivilegeMember('DenyThemes','ModerateNonCore');
}

function installer_public_readnoncore()
{
    xarRegisterPrivilege('ReadNonCore','All','empty','All','All','ACCESS_NONE','Read access only to none-core modules');
    xarRegisterPrivilege('ReadAccess','All','All','All','All','ACCESS_READ','Read access to all modules');
    xarRegisterPrivilege('DenyPrivileges','All','privileges','All','All','ACCESS_NONE','Deny access to the Privileges module');
    xarRegisterPrivilege('DenyAdminPanels','All','adminpanels','All','All','ACCESS_NONE','Deny access to the AdminPanels module');
    xarRegisterPrivilege('DenyBlocks','All','blocks','All','All','ACCESS_NONE','Deny access to the Blocks module');
    xarRegisterPrivilege('DenyMail','All','mail','All','All','ACCESS_NONE','Deny access to the Mail module');
    xarRegisterPrivilege('DenyModules','All','modules','All','All','ACCESS_NONE','Deny access to the Modules module');
    xarRegisterPrivilege('DenyThemes','All','themes','All','All','ACCESS_NONE','Deny access to the Themes module');
    xarMakePrivilegeRoot('ReadNonCore');
    xarMakePrivilegeRoot('ReadAccess');
    xarMakePrivilegeRoot('DenyPrivileges');
    xarMakePrivilegeRoot('DenyAdminPanels');
    xarMakePrivilegeRoot('DenyBlocks');
    xarMakePrivilegeRoot('DenyMail');
    xarMakePrivilegeRoot('DenyModules');
    xarMakePrivilegeRoot('DenyThemes');
    xarMakePrivilegeMember('ReadAccess','ReadNonCore');
    xarMakePrivilegeMember('DenyPrivileges','ReadNonCore');
    xarMakePrivilegeMember('DenyAdminPanels','ReadNonCore');
    xarMakePrivilegeMember('DenyBlocks','ReadNonCore');
    xarMakePrivilegeMember('DenyMail','ReadNonCore');
    xarMakePrivilegeMember('DenyModules','ReadNonCore');
    xarMakePrivilegeMember('DenyThemes','ReadNonCore');
}
function installer_public_readnoncore2()
{
    xarRegisterPrivilege('ReadNonCore','All','empty','All','All','ACCESS_NONE','Read access only to none-core modules');
    xarRegisterPrivilege('ReadAccess','All','All','All','All','ACCESS_READ','Read access to all modules');
    xarMakePrivilegeMember('ReadAccess','ReadNonCore');
    xarMakePrivilegeMember('DenyPrivileges','ReadNonCore');
    xarMakePrivilegeMember('DenyAdminPanels','ReadNonCore');
    xarMakePrivilegeMember('DenyBlocks','ReadNonCore');
    xarMakePrivilegeMember('DenyMail','ReadNonCore');
    xarMakePrivilegeMember('DenyModules','ReadNonCore');
    xarMakePrivilegeMember('DenyThemes','ReadNonCore');
}
?>
