<?php

/**
 * File: $Id$
 *
 * Base JavaScript management functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage base
 * @author Jason Judge
 * @todo none
 */


/**
 * Include a module JavaScript link in a page.
 *
 * @author Jason Judge
 * @param $args['module'] module name; or
 * @param $args['moduleid'] module ID
 * @param $args['filename'] file name list (comma-separated or array)
 * @param $args['position'] position on the page; generally 'head' or 'body'
 * @returns true=success; null=fail
 * @return boolean
 */
function base_javascriptapi_modulefile($args)
{
    extract($args);

    $result = true;

    // Default the position to the head.
    if (empty($position)) {
        $position = 'head';
    }

    // Filename can be an array of files to include, or a
    // comma-separated list. This allows a bunch of files
    // to be included from a source module in one go.
    if (!is_array($args['filename'])) {
        $files = explode(',', $args['filename']);
    }

    foreach ($files as $file) {
        $args['filename'] = $file;
        $filePath = xarModAPIfunc('base', 'javascript', '_findfile', $args);

        // A failure to find a file is recorded, but does not stop subsequent files.
        if (!empty($filePath)) {
            $result = $result & xarTplAddJavaScript($position, 'src', $filePath, $filePath);
        } else {
            $result = false;
        }
    }

    return $result;
}

?>
