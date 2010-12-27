<?php

/**
 * Delete category links of module items
 */
function categories_admin_unlink()
{ 
    // Security Check
    if(!xarSecurityCheck('AdminCategories')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid',    'isset', $catid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return; 

    // Check for confirmation.
    if (empty($confirm)) {
        $data = array();
        $data['modid'] = $modid;
        $data['itemtype'] = $itemtype;
        $data['itemid'] = $itemid;

        $what = '';
        if (!empty($modid)) {
            $modinfo = xarMod::getInfo($modid);
            if (empty($itemtype)) {
                $data['modname'] = ucwords($modinfo['displayname']);
            } else {
                // Get the list of all item types for this module (if any)
                $mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                } else {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                }
            }
        }
        $data['confirmbutton'] = xarML('Confirm'); 
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey(); 
        // Return the template variables defined in this function
        return $data;
    } 

    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }        
    // unlink API does not support deleting all category links for all modules
    if (!empty($modid)) {
        $modinfo = xarMod::getInfo($modid);
        if (!xarMod::apiFunc('categories','admin','unlink',
                           array('modid' => $modid,
                                 'itemtype' => $itemtype,
                                 'iid' => $itemid,
                                 'confirm' => $confirm))) {
            return;
        }
        // TODO: support deleting all links for a category too (cfr. checklinks)
    }
    xarResponse::Redirect(xarModURL('categories', 'admin', 'stats'));
    return true;
}

?>
