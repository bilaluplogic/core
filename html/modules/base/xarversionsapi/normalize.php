<?php

/**
 * File: $Id: s.xaruser.php 1.16 03/04/07 04:30:01-04:00 johnny@falling.local.lan $
 *
 * Base User version management functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage base
 * @author Jason Judge
 * @todo none
 */

function base_versionsapi__normalize($ver, $sep, $strict)
{
    $sep2 = preg_quote($sep);

    // TODO: I don't now if caching the arrays is useful or not;
    // removed for now.

    return preg_replace(
       array(
           ($strict ? '/(?<=\d)[^\d'.$sep2.']+(?=\d)/' : '//'), // '1x2' => '1.2' if strict
           ($strict ? '/[^\d'.$sep2.']*/' : '//'),              // 'x' => '' if strict
           '/(\s)+/',                                           // ' ' => ''
           '/^'.$sep2.'/',                                      // '.1' => '0.1'
           '/'.$sep2.'$/',                                      // '1.' => '1.0'
           '/'.$sep2.$sep2.'/',                                 // '1..2' => '1.0.2'
           '/^$/'                                               // '' => '0'
        ),
        array(
            ($strict ? $sep : ''),
            '',
            '',
            '0'.$sep,
            $sep.'0',
            $sep.'0'.$sep,
            '0'
        ),
        $ver
    );
}


/**
 * Compare two legal-style versions supplied as strings or arrays, to an arbitrary number of levels
 * Usage : $which = xarModAPIFunc('base', 'versions', 'compare', array('ver1'=>$version1, 'ver2'=>$version2));
 * or shortcut $which = xarModAPIFunc('base', 'versions', 'compare', array($version1, $version2));
 *
 * @author Jason Judge
 * @param $args['ver'] 
 * @param $args['vers'] 
 * @param $args['strict'] indicates strict numeric-only comparisons (default: true)
 * @param $args['sep'] level separator character (default: '.')
 * @returns array or string of normalized version numbers
 * @return number indicating which parameter is the latest version
 */
function base_versionsapi_normalize($args)
{
    extract($args);

    // Set this flag if checking should be strictly numeric.
    // With strict set (true), non-numeric characters will be stripped prior to
    // the comparison.
    // With strict reset (false), then string comparisons will be
    // performed where one or both version levels are not numeric.
    if (!isset($strict)) {
        $strict = true;
    }

    // Default the level separator to '.' if none valid passed in.
    if (!isset($sep) || strlen($sep) <> 1) {
        $sep = '.';
    }

    // Options are:
    // a) $ver = '1.2.3'
    // b) $ver = array(1,2,3)
    // c) $vers = array('1.2.3', '4.5.6', ...)
    // d) $vers = array('1.2.3', array(4,5,6), ...)
    // Get all these options into format a) or c).

    if (isset($vers) && is_array($vers)) {
        foreach ($vers as $key => $verval) {
            // If array versions have been passed in, convert them
            // to legal-format strings.
            if (is_array($verval)) {
                $verval = implode($sep, $verval);
            }
            $result[$key] = base_versionsapi__normalize($verval, $sep, $strict);
        }
    } elseif (isset($ver)) {
        if (is_array($ver)) {
            $ver = implode($sep, $ver);
        }
        $result = base_versionsapi__normalize($ver, $sep, $strict);
    } else {
        $result = false;
    }

    return $result;
}

?>
