<?php
/**
 * File: $Id$
 *
 * Import an object definition or an object item from XML
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata module
 * @author mikespub <mikespub@xaraya.com>
*/
/**
 * Import an object definition or an object item from XML
 */
function dynamicdata_util_import($args)
{
// Security Check
	if(!xarSecurityCheck('AdminDynamicData')) return;

    if(!xarVarFetch('import', 'isset', $import,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('xml', 'isset', $xml,  NULL, XARVAR_DONT_SET)) {return;}

    extract($args);

    $data = array();
    $data['menutitle'] = xarML('Dynamic Data Utilities');

    $data['warning'] = '';
    $data['options'] = array();
    $data['authid'] = xarSecGenAuthKey();

    $basedir = 'modules/dynamicdata';
    $filetype = 'xml';
    $files = xarModAPIFunc('dynamicdata','admin','browse',
                           array('basedir' => $basedir,
                                 'filetype' => $filetype));
    if (!isset($files) || count($files) < 1) {
        $data['warning'] = xarML('There are currently no XML files available for import in "#(1)"',$basedir);
        return $data;
    }

    if (!empty($import) || !empty($xml)) {
        if (!xarSecConfirmAuthKey()) return;

        if (!empty($import)) {
            $found = '';
            foreach ($files as $file) {
                if ($file == $import) {
                    $found = $file;
                    break;
                }
            }
            if (empty($found) || !file_exists($basedir . '/' . $file)) {
                $msg = xarML('File not found');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                               new SystemException($msg));
                return;
            }
            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => $basedir . '/' . $file));
        } else {
            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('xml' => $xml));
        }
        if (empty($objectid)) return;

        $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                    array('objectid' => $objectid));
        if (empty($objectinfo)) return;

        $data['warning'] = xarML('Object #(1) was successfully imported',xarVarPrepForDisplay($objectinfo['label']));
        return $data;
    }

    natsort($files);
    array_unshift($files,'');
    foreach ($files as $file) {
         $data['options'][] = array('id' => $file,
                                    'name' => $file);
    }

    xarTplSetPageTemplateName('admin');

    return $data;
}

?>
