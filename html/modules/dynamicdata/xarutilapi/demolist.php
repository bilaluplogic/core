<?php

/**
 * sample function returning an array of options for a Dropdown or Radio Buttons property
 *
 * @author the DynamicData module development team
 * @returns array
 * @return array of values, or array of id => value combinations
 * @raise BAD_PARAM, NO_PERMISSION
 */
function dynamicdata_utilapi_demolist($args)
{
    extract($args);
    // do something with arguments

    // fill in the array with the values to be shown
    $options = array(
                     '',                          // use an empty value as default
                     xarML('Employed Full Time'),
                     xarML('Employed Part Time'),
                     xarML('Self-Employed'),
                     xarML('Unemployed'),
                     xarML('Student'),
                     xarML('Retired'),
                     xarML('Not Applicable'),
                    );

    // or fill in the array with the id => value combinations
/*
    $options = array(
                     'unknown'    => '',                          // use an empty value as default
                     'emp_full'   => xarML('Employed Full Time'),
                     'emp_part'   => xarML('Employed Part Time'),
                     'self_emp'   => xarML('Self-Employed'),
                     'unempl'     => xarML('Unemployed'),
                     'student'    => xarML('Student'),
                     'retired'    => xarML('Retired'),
                     'not_applic' => xarML('Not Applicable'),
                    );
*/

    return $options;
}

?>
