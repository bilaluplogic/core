<?php

/**
 * Changes the navigation language
 * This is the external entry point to tell MLS use another language
 */
function roles_user_changelanguage()
{
    if (!xarVarFetch('locale', 'str:1:', $locale, NULL, XARVAR_POST_ONLY)) return;
    if (!xarVarFetch('return_url', 'str:1:', $return_url, NULL, XARVAR_POST_ONLY)) return;

    $locales = xarMLSListSiteLocales();
    if (!isset($locales)) return; // throw back
    // Check if requested locale is supported
    if (!in_array($locale, $locales)) {
        $msg = xarML('Unsupported locale.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (xarUserSetNavigationLocale($locale) == false) {
        // Wrong MLS mode
        // FIXME: <marco> Show a custom error here or just throw an exception?
        // <paul> throw an exception. trap it later if we want it to look nice,
        // that's the whole point of exceptions.
    }
    xarResponseRedirect($return_url);
}

?>