<?php

/**
 * Manage definition of instances for privileges (unfinished)
 */
function dynamicdata_admin_privileges($args)
{ 
    // Security Check
    if (!xarSecurityCheck('AdminDynamicData')) return;

    // Preparation of  new block of getting the variables: (prolly use xarVarBatchFetch eeventually)
//    if(!xarVarFetch('objectid', 'id'   , $objectid                           )) return;    // id? , only passed on further in this func
//    if(!xarVarFetch('moduleid', 'str::', $moduleid,0, XARVAR_NOT_REQUIRED    )) return;    // empty, 'All', numeric or modulename
//    if(!xarVarFetch('itemtype', 'str::', $itemtype,0, XARVAR_NOT_REQUIRED    )) return;    // empty, 'All', numeric 
//    if(!xarVarFetch('itemid'  , 'str::', $itemid  ,0, XARVAR_NOT_REQUIRED    )) return;    // empty, 'All', numeric  
//    if(!xarVarFetch('propname', 'str::', $propname,'' XARVAR_NOT_REQUIRED    )) return;    // empty, 'All', string
//    if(!xarVarFetch('proptype', 'str::', $proptype,0, XARVAR_NOT_REQUIRED    )) return;    // empty, 'All', numeric
//    if(!xarVarFetch('propid'  , 'id'   , $propid  ,0, XARVAR_NOT_REQUIRED    )) return;    // empty, 'All', numeric
//    if(!xarVarFetch('apply'   , 'bool' , $apply , false, XARVAR_NOT_REQUIRED )) return;    // boolean?
//    if(!xarVarFetch('extpid'  , 'id'   , $extpid                             )) return;    // empty, 'All', numeric ?
//    if(!xarVarFetch('extname' , 'str:1', $extname                            )) return;    // ?
//    if(!xarVarFetch('extrealm', 'str:1', $extrealm                           )) return;    // ?
//    if(!xarVarFetch('extmodule','str:1', $extmodule                          )) return;    // ?
//    if(!xarVarFetch('extcomponent', 'enum:Item:Type', $extcomponent          )) return;    // 'Item', 'Type'
//    if(!xarVarFetch('extinstance', 'str:1', $extinstance,'', XARVAR_NOT_REQUIRED)) return; // somthing:somthing:somthing or empty
//    if(!xarVarFetch('extlevel', 'str:1', $extlevel                        )) return;
    
    // Deprecated block of getting the variables:
    // fixed params
    list($objectid     , $moduleid   , $itemtype, $itemid , $propname, $proptype,
         $propid       , $apply      , $extpid  , $extname, $extrealm, $extmodule,
         $extcomponent , $extinstance, $extlevel) = xarVarCleanFromInput(
         'objectid'    ,'moduleid'   ,'itemtype','itemid' ,'propname','proptype',
         'propid'      ,'apply'      ,  'extpid','extname','extrealm','extmodule',
         'extcomponent','extinstance','extlevel');
    extract($args);

// TODO: combine 'Item' and 'Type' instances someday ?

    if (!empty($extinstance)) {
        $parts = explode(':',$extinstance);
        if ($extcomponent == 'Item') {
            if (count($parts) > 0 && !empty($parts[0])) $moduleid = $parts[0];
            if (count($parts) > 1 && !empty($parts[1])) $itemtype = $parts[1];
            if (count($parts) > 2 && !empty($parts[2])) $itemid = $parts[2];
        } else {
            if (count($parts) > 0 && !empty($parts[0])) $propname = $parts[0];
            if (count($parts) > 1 && !empty($parts[1])) $proptype = $parts[1];
            if (count($parts) > 2 && !empty($parts[2])) $propid = $parts[2];
        }
    }

    if ($extcomponent == 'Item') {

        if (empty($moduleid) || $moduleid == 'All') {
            $moduleid = 0;
        } elseif (!is_numeric($moduleid)) { // for pre-wizard instances
            $modid = xarModGetIDFromName($moduleid);
            if (!empty($modid)) {
                $moduleid = $modid;
            } else {
                $moduleid = 0;
            }
        }
        if (empty($itemtype) || $itemtype == 'All' || !is_numeric($itemtype)) {
            $itemtype = 0;
        }
        if (empty($itemid) || $itemid == 'All' || !is_numeric($itemid)) {
            $itemid = 0;
        }

        // define the new instance
        $newinstance = array();
        $newinstance[] = empty($moduleid) ? 'All' : $moduleid;
        $newinstance[] = empty($itemtype) ? 'All' : $itemtype;
        $newinstance[] = empty($itemid) ? 'All' : $itemid;

    } else {

        if (empty($propname) || $propname == 'All' || !is_string($propname)) {
            $propname = '';
        }
        if (empty($proptype) || $proptype == 'All' || !is_numeric($proptype)) {
            $proptype = 0;
        }
        if (empty($propid) || $propid == 'All' || !is_numeric($propid)) {
            $propid = 0;
        }

        // define the new instance
        $newinstance = array();
        $newinstance[] = empty($propname) ? 'All' : $propname;
        $newinstance[] = empty($proptype) ? 'All' : $proptype;
        $newinstance[] = empty($propid) ? 'All' : $propid;

    }

    if (!empty($apply)) {
        // create/update the privilege
        $pid = xarReturnPrivilege($extpid,$extname,$extrealm,$extmodule,$extcomponent,$newinstance,$extlevel);
        if (empty($pid)) {
            return; // throw back
        }

        // redirect to the privilege
        xarResponseRedirect(xarModURL('privileges', 'admin', 'modifyprivilege',
                                      array('pid' => $pid)));
        return true;
    }

    // Get objects
    $objects = xarModAPIFunc('dynamicdata','user','getobjects');

    // TODO: use object list instead of (or in addition to) module + itemtype

    // Get module list
    $objectlist = array();
    $modlist = array();
    foreach ($objects as $id => $object) {
        $objectlist[$id] = $object['label'];
        $modid = $object['moduleid'];
        $modinfo = xarModGetInfo($modid);
        $modlist[$modid] = $modinfo['displayname'];
    }

    // Get property types
    $proptypes = xarModAPIFunc('dynamicdata','user','getproptypes');

    // Get properties
    $properties = xarModAPIFunc('dynamicdata','user','getitems',
                                array('module' => 'dynamicdata',
                                      'itemtype' => 1));
    $propnames = array();
    $propids = array();
    foreach ($properties as $property) {
        $propnames[$property['name']] = 1;
        if (!isset($objectlist[$property['objectid']])) continue;
        $objectname = $objectlist[$property['objectid']];
        if (!isset($propids[$objectname])) {
            $propids[$objectname] = array();
        }
        $propids[$objectname][$property['id']] = $property['label'];
    }
    ksort($propnames);

    if ($extcomponent == 'Item') {
        if (!empty($itemid)) {
            $numitems = xarML('probably');
        } elseif (!empty($objectid) || !empty($moduleid)) {
            $numitems = xarModAPIFunc('dynamicdata','user','countitems',
                                      array('objectid' => $objectid,
                                            'moduleid' => $moduleid,
                                            'itemtype' => $itemtype));
            if (empty($numitems)) {
                $numitems = 0;
            }
        } else {
            $numitems = xarML('probably');
        }

    } else { // 'Type'

        $numitems = xarML('probably');

    }

    $data = array(
                  'objectid'     => $objectid,
                  'moduleid'     => $moduleid,
                  'itemtype'     => $itemtype,
                  'itemid'       => $itemid,
                  'propname'     => $propname,
                  'proptype'     => $proptype,
                  'propid'       => $propid,
                  'objectlist'   => $objectlist,
                  'modlist'      => $modlist,
                  'propnames'    => $propnames,
                  'proptypes'    => $proptypes,
                  'propids'      => $propids,
                  'numitems'     => $numitems,
                  'extpid'       => $extpid,
                  'extname'      => $extname,
                  'extrealm'     => $extrealm,
                  'extmodule'    => $extmodule,
                  'extcomponent' => $extcomponent,
                  'extlevel'     => $extlevel,
                  'extinstance'  => xarVarPrepForDisplay(join(':',$newinstance)),
                 );

    $data['refreshlabel'] = xarML('Refresh');
    $data['applylabel'] = xarML('Finish and Apply to Privilege');

    return $data;
}

?>
