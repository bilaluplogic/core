<?php
/**
 * File: $Id$
 *
 * Purpose of file:  Table information for privileges module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage security
 * @author Marc Lutolf <marcinmilan@xaraya.com>
*/

/**
 * Return table name definitions to Xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded. It is called by xarMod__dbInfoLoad()
 *
 * @return array
 */
function privileges_xartables()
{
    // Initialise table array
    $tables = array();

    $privileges  = xarDBGetSiteTablePrefix() . '_privileges';
    $privMembers = xarDBGetSiteTablePrefix() . '_privmembers';
    $roles       = xarDBGetSiteTablePrefix() . '_roles';
    $roleMembers = xarDBGetSiteTablePrefix() . '_rolemembers';
    $acl         = xarDBGetSiteTablePrefix() . '_security_acl';
    $masks       = xarDBGetSiteTablePrefix() . '_security_masks';
    $instances   = xarDBGetSiteTablePrefix() . '_instances';
    $modules     = xarDBGetSiteTablePrefix() . '_modules';
    $module_states   = xarDBGetSiteTablePrefix() . '_module_states';
    $module_states   = xarDBGetSiteTablePrefix() . '_module_states';
    $privsets    = xarDBGetSiteTablePrefix() . '_security_privsets';

    // Set the table names
    $tables['privileges']     = $privileges;
    $tables['privmembers']    = $privMembers;
    $tables['roles']          = $roles;
    $tables['rolemembers']    = $roleMembers;
    $tables['security_acl']   = $acl;
    $tables['security_masks'] = $masks;
    $tables['instances']      = $instances;
    $tables['modules']      = $modules;
    $tables['module_states']      = $module_states;
    $tables['security_privsets']      = $privsets;

    // Return the table information
    return $tables;
}

?>
