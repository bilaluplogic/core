<?php
/** 
 * File: $Id$
 *
 * Get one or more block types.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @param args['tid'] block type ID (optional)
 * @param args['module'] module name (optional)
 * @param args['type'] block type name (optional)
 * @returns array of block types, keyed on block type ID
 *
 * @subpackage Blocks administration
 * @author Jason Judge
*/

function blocks_userapi_getallblocktypes($args)
{
    extract($args);

    $where = array();

    if (!empty($module)) {
        $where[] = 'xar_module = \'' . xarVarPrepForStore($module) . '\'';
    }

    if (!empty($type)) {
        $where[] = 'xar_type = \'' . xarVarPrepForStore($type) . '\'';
    }

    if (!empty($tid) && is_numeric($tid)) {
        $where[] = 'xar_id = ' . $tid;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $block_types_table = $xartable['block_types'];

    // Fetch instance details.
    $query = 'SELECT xar_id, xar_type, xar_module FROM ' . $block_types_table;

    if (!empty($where)) {
        $query .= ' WHERE ' . implode(' AND ', $where);
    }

    // Return if no details retrieved.
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    // The main result array.
    $types = array();

    while (!$result->EOF) {
        // Fetch instance data
        list($tid, $module, $type) = $result->fields;

        $types[$tid] = array(
            'tid' => $tid,
            'module' => $module,
            'type' => $type
        );

        // Next block type.
        $result->MoveNext();
    }

    // Close the query.
    $result->Close();

    return $types;
}

?>
