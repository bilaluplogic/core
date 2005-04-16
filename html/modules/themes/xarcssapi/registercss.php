<?php

/**
 * Handler for the xar:style tag
 *
 * Attributes:
 * file       - CDATA                   - basename of the style file to include
 * scope     -  [module|(theme)|system] - where to look for it
 * type      - (text/css)               - what content is to be expected
 * media     - (all)                    - for which media are we including style info (space separated list)
 * alternate - [yes|(no)]               - this style is an alternative to the main styling?
 * title     - ""                       - what title can we attach to the styling, if any
 * method    - [(import)|link]          - what method do we use to include the style info
 *
 * <xar:style file="basename" scope="theme" type="text/css" media="all" alternate="no" title="Great style" method="import" />
 */
function themes_cssapi_registercss($args)
{
    $argstring = 'array(';
    foreach ($args as $key => $value) {
        $argstring .= "'" . $key . "' => '" . $value . "',";
    }
    $argstring .= ")";
    return "xarModAPIFunc('themes', 'user', 'register',$argstring);\n";
}

?>
