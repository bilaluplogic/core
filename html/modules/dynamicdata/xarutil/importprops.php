<?php

/**
 * Import the dynamic properties for a module + itemtype from a static table
 */
function dynamicdata_util_importprops()
{
// Security Check
	if(!xarSecurityCheck('AdminDynamicData')) return;

    if(!xarVarFetch('objectid', 'isset', $objectid,  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('modid',    'isset', $modid,     , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  , XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('table',    'isset', $table,     , XARVAR_NOT_REQUIRED)) {return;}

    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'util', 'importprop', 'dynamicdata');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('dynamicdata','util','importproperties',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'table' => $table,
                             'objectid' => $objectid))) {
        return;
    }

    xarResponseRedirect(xarModURL('dynamicdata', 'admin', 'modifyprop',
                                  array('modid' => $modid,
                                        'itemtype' => $itemtype)));
}

?>
