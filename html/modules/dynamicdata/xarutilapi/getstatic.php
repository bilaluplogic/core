<?php

/**
 * (try to) get the "static" properties, corresponding to fields in dedicated
 * tables for this module + item type
// TODO: allow modules to specify their own properties
 *
 * @author the DynamicData module development team
 * @param $args['module'] module name of table you're looking for, or
 * @param $args['modid'] module id of table you're looking for
 * @param $args['itemtype'] item type of table you're looking for
 * @param $args['table']  table name of table you're looking for (better)
 * @returns mixed
 * @return value of the field, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function dynamicdata_utilapi_getstatic($args)
{
    static $propertybag = array();

    extract($args);

    if (empty($modid) && !empty($module)) {
        $modid = xarModGetIDFromName($module);
    }
    if (empty($modid)) {
        $modid = xarModGetIDFromName(xarModGetName());
    }
    $modinfo = xarModGetInfo($modid);
    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $invalid = array();
    if (!isset($modid) || !is_numeric($modid) || empty($modinfo['name'])) {
        $invalid[] = 'module id ' . xarVarPrepForDisplay($modid);
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $invalid[] = 'item type';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'util', 'getstatic', 'DynamicData');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (empty($table)) {
        $table = '';
    }
    if (isset($propertybag["$modid:$itemtype:$table"])) {
        return $propertybag["$modid:$itemtype:$table"];
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

// TODO: support site tables as well
    $systemPrefix = xarDBGetSystemTablePrefix();
    $metaTable = $systemPrefix . '_tables';

    if ($modinfo['name'] == 'dynamicdata') {
        // let's cheat a little for DD, because otherwise it won't find any tables :)
        if ($itemtype == 0) {
            $modinfo['name'] = 'dynamic_objects';
        } elseif ($itemtype == 1) {
            $modinfo['name'] = 'dynamic_properties';
        } elseif ($itemtype == 2) {
            $modinfo['name'] = 'dynamic_data';
        }
    }

    $query = "SELECT xar_tableid,
                     xar_table,
                     xar_field,
                     xar_type,
                     xar_size,
                     xar_default,
                     xar_increment,
                     xar_primary_key
              FROM $metaTable ";

    // it's easy if the table name is known
    if (!empty($table)) {
        $query .= " WHERE xar_table = '" . xarVarPrepForStore($table) . "'";

    // otherwise try to get any table that starts with prefix_modulename
    } else {
        $query .= " WHERE xar_table LIKE '" . xarVarPrepForStore($systemPrefix)
                                   . '_' . xarVarPrepForStore($modinfo['name']) . '%' . "' ";
    }
    $query .= " ORDER BY xar_tableid ASC";

    $result =& $dbconn->Execute($query);

    if (!$result) return;

    $static = array();

    // add the list of table + field
    $order = 1;
    while (!$result->EOF) {
        list($id,$table, $field, $datatype, $size, $default,$increment,$primary_key) = $result->fields;
    // TODO: what kind of security checks do we want/need here ?
        //if (xarSecAuthAction(0, 'DynamicData::Field', "$name:$type:$id", ACCESS_READ)) {
        //}

        // assign some default label for now, by removing everything except the last part (xar_..._)
// TODO: let modules define this
        $name = preg_replace('/^.+_/','',$field);
        $label = ucfirst($name);
        if (isset($static[$name])) {
            $i = 1;
            while (isset($static[$name . '_' . $i])) {
                $i++;
            }
            $name = $name . '_' . $i;
            $label = $label . '_' . $i;
        }

        $status = 1;

        // assign some default validation for now
// TODO: improve this based on property type validations
        $validation = $datatype;
        $validation .= empty($size) ? '' : ' (' . $size . ')';

        // (try to) assign some default property type for now
        // = obviously limited to basic data types in this case
        switch ($datatype) {
            case 'char':
            case 'varchar':
                $proptype = 2; // Text Box
                if (!empty($size)) {
                    $validation = "0:$size";
                }
                break;
            case 'integer':
                $proptype = 15; // Number Box
                break;
            case 'float':
                $proptype = 17; // Number Box (float)
                break;
            case 'boolean':
                $proptype = 14; // Checkbox
                break;
            case 'date':
            case 'datetime':
            case 'timestamp':
                $proptype = 8; // Calendar
                break;
            case 'text':
                $proptype = 4; // Medium Text Area
                $status = 2;
                break;
            case 'blob':       // caution, could be binary too !
                $proptype = 4; // Medium Text Area
                $status = 2;
                break;
            default:
                $proptype = 1; // Static Text
                break;
        }

        // try to figure out if it's the item id
// TODO: let modules define this
        if (!empty($increment) || !empty($primary_key)) {
            // not allowed to modify primary key !
            $proptype = 21; // Item ID
        }

        $static[$name] = array('name' => $name,
                               'label' => $label,
                               'type' => $proptype,
                               'id' => $id,
                               'default' => $default,
                               'source' => $table . '.' . $field,
                               'status' => $status,
                               'order' => $order,
                               'validation' => $validation);
        $order++;
        $result->MoveNext();
    }

    $result->Close();

    $propertybag["$modid:$itemtype:$table"] = $static;
    return $static;
}

?>
