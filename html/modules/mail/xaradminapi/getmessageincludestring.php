<?php
/**
 * File: $Id$
 *
 * Read a file in the messaging/<module>
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Mail Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * @param $args['template'] name of the template without .xd extension
 * @param $args['module'] module directory in var/messaging
 * @return string of file contents read
 */
function mail_adminapi_getmessageincludestring($args)
{
    extract($args);
    if (!isset($template)) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA', new SystemException('No template name was given.'));
    }

    if(!isset($module)){
        list($module) = xarRequestGetInfo();
    }

// Get the template that defines the substitution vars
    $messaginghome = "var/messaging/" . $module;
    if (!file_exists($messaginghome . "/includes/" . $template . ".xd")) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_FILE_NOT_EXIST', new SystemException('The variables template was not found.'));
    }
    $string = '';
    $fd = fopen($messaginghome . "/includes/" . $template . ".xd", 'r');
    while(!feof($fd)) {
        $line = fgets($fd, 1024);
        $string .= $line;
    }
    fclose($fd);
    return $string;
}

?>
