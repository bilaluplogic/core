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
 * Array Validation Function
 */
function variable_validations_array (&$subject, $parameters, $supress_soft_exc) {

    if (!is_array($subject)) {
        $msg = xarML('Not an array: "#(1)"', $subject);

        if (!$supress_soft_exc || $subject !== NULL) xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));

        // NULL is a special case. Perform a 'soft' fail should we encounter a NULL
        if ($subject !== NULL) {
            return;
        } else {
            return false;
        }
    }

    if (isset($parameters[0]) && trim($parameters[0]) != '') {
        if (!is_numeric($parameters[0])) {
            $msg = 'Parameter "'.$parameters[0].'" is not a Numeric Type';
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        } elseif (count($subject) < (int) $parameters[0]) {
            $msg = xarML('Array variable has less elements "#(1)" than the specified minimum "#(2)"', count($subject), $parameters[0]);
            if (!$supress_soft_exc) xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return false;
        }
    }

    if (isset($parameters[1]) && trim($parameters[1]) != '') {
        if (!is_numeric($parameters[1])) {
            $msg = 'Parameter "'.$parameters[1].'" is not a Numeric Type';
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return;
        } elseif (count($subject) > (int) $parameters[1]) {
            $msg = xarML('Array variable has more elements "#(1)" than the specified maximum "#(2)"', $value, $parameters[1]);
            if (!$supress_soft_exc) xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
            return false;
        }
    }

    return true;
}

?>