<?php
/** 
 * File: $Id$
 *
 * Count the number of block types [for a given type and/or module]
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Blocks administration
 * @author Jason Judge
*/
/**
 * Count the number of block types [of a given name or module]
 *
 * @access public
 * @param modName the module name
 * @param $args['type'] name of the block type (optional)
 * @param $args['module'] name of the module (optional)
 * @returns integer
 * @return count of block types that meet the required criteria
 * @raise DATABASE_ERROR, BAD_PARAM
 */
function blocks_userapi_countblocktypes($args)
{
    extract($args);

    $where = [];

    if (!empty($module)) {
        $where[] = 'xar_module = \'' . xarVarPrepForStore($module) . '\'';
    }

    if (!empty($type)) {
        $where[] = 'xar_type = \'' . xarVarPrepForStore($type) . '\'';
    }

    list ($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $block_types_table = $xartable['block_types'];

    $query = 'SELECT count(xar_id) FROM ' . $block_types_table;

    if (!empty($where)) {
        $query .= ' WHERE ' . implode(' AND ', $where);
    }

    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    list ($count) = $result->fields;

    return $count;
}

?>
