<?php
/**
 * File: $Id$
 *
 * Dynamic form block
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
 * modify block settings
 */
function dynamicdata_formblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
 * update block settings
 */
function dynamicdata_formblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:1:100', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}
?>