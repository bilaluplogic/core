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
 * initialise block
 */
function dynamicdata_formblock_init()
{
    return true;
}

/**
 * get information on block
 */
function dynamicdata_formblock_info()
{
    // Values
    return array('text_type' => 'form',
                 'module' => 'dynamicdata',
                 'text_type_long' => 'Show dynamic data form',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function dynamicdata_formblock_display($blockinfo)
{
    // Security check
    if(!xarSecurityCheck('ReadDynamicDataBlock',0,'Block',"$blockinfo[title]:All:All")) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Populate block info and pass to theme
    if (!empty($vars['objectid'])) {
        $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                    $vars);
        if (!empty($objectinfo)) {
            if (!xarSecurityCheck('AddDynamicDataItem',0,'Item',"$objectinfo[moduleid]:$objectinfo[itemtype]:All")) return;
            $blockinfo['content'] = $objectinfo;
            return $blockinfo;
        }
    }
}

/**
 * built-in block help/information system.
 */
function dynamicdata_formblock_help()
{
    // No information yet.
    return '';
}
?>
