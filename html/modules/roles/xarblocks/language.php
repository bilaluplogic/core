<?php
/**
 * File: $Id$
 *
 * Language Selection via block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage Roles Module
 * @author Marco Canini
*/

/**
 * initialise block
 */
function roles_languageblock_init()
{
    return array();
}

/**
 * get information on block
 */
function roles_languageblock_info()
{
    return array(
        'text_type' => 'Language',
        'module' => 'roles',
        'text_type_long' => 'Language selection'
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function roles_languageblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadRole', 1, 'Block', "All:" . $blockinfo['title'] . ":" . $blockinfo['bid'])) {return;}

    // if (xarMLSGetMode() != XARMLS_BOXED_MULTI_LANGUAGE_MODE) {
    if (xarMLSGetMode() == XARMLS_SINGLE_LANGUAGE_MODE) {
        return;
    }

    $current_locale = xarMLSGetCurrentLocale();

    $site_locales = xarMLSListSiteLocales();

    asort($site_locales);

    if (count($site_locales) <= 1) {
        return;
    }

    foreach ($site_locales as $locale) {
        $locale_data =& xarMLSLoadLocaleData($locale);

        $selected = ($current_locale == $locale);

        $locales[] = array(
            'locale'   => $locale,
            'country'  => $locale_data['/country/display'],
            'name'     => $locale_data['/language/display'],
            'selected' => $selected
        );
    }


    $tplData['form_action'] = xarModURL('roles', 'user', 'changelanguage');
    $tplData['form_picker_name'] = 'locale';
    $tplData['locales'] = $locales;
    $tplData['blockid'] = $blockinfo['bid'];

    // URL of this page
    $tplData['return_url'] = xarServerGetCurrentURL();

    $blockinfo['content'] = $tplData;

    return $blockinfo;
}

?>