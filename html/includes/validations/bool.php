<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package validation
 * @copyright (C) 2003 by the Xaraya Development Team.
*/

/**
 * Boolean Validation Function
 */
function variable_validations_bool (&$subject, $parameters=null, $supress_soft_exc, &$name)
{

    if ($subject == 'true') {
        $subject = true;
    } elseif ($subject == 'false') {
        $subject = false;
    } else {
        if ($name != '')
            $msg = xarML('Variable #(1) is not a boolean: "#(2)"', $name, $subject);
        else
            $msg = xarML('Not a boolean: "#(1)"', $subject);
        if (!$supress_soft_exc) xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return false;
    }

    return true;
}

?>