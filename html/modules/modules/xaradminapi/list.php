<?php
/**
 * File: $Id$
 *
 * Obtain list of modules (deprecated)
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage modules module
 * @author Xaraya Team 
 */
/**
 * Obtain list of modules (deprecated)
 *
 * @param none
 * @returns array
 * @return array of known modules
 * @raise NO_PERMISSION
 */
function modules_adminapi_list($args)
{
    // Get arguments from argument array
    extract($args);

    // Security Check
	if(!xarSecurityCheck('AdminModules')) return;

    // Obtain information
    if (!isset($state)) $state = '';
    $modList = xarModAPIFunc('modules', 
                          'admin', 
                          'GetList', 
                          array('filter'     => array('State' => $state)));
    //throw back
    if (!isset($modList)) return;

    return $modList;
}

?>
