<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data module
 * @link http://xaraya.com/index.php/release/182.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Wrapper for DataObjectMaster::getObjectInfo
 *
 * @see  DataObjectMaster::getObjectInfo
 */
function dynamicdata_userapi_getobjectinfo($args)
{
    return DataObjectMaster::getObjectInfo($args);
}
?>
