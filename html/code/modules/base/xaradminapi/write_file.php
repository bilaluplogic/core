<?php
/**
 * @package modules
 * @subpackage base module
 * @category Xaraya Web Applications Framework
 * @version 2.2.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @link http://xaraya.com/index.php/release/68.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function base_adminapi_write_file($args)
{
    if (empty($args['file'])) return false;
    try {
        $fp = fopen($args['file'], "wb");
    
        if (get_magic_quotes_gpc()) {
            $data = stripslashes($args['data']);
        }
        fwrite($fp, $args['data']);
        fclose ($fp);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>