<?php

/**
 * get a dynamic property
 *
 * @author the DynamicData module development team
 * @param $args['type'] type of property (required)
 * @param $args['name'] name for the property (optional)
 * @param $args['label'] label for the property (optional)
 * @param $args['default'] default for the property (optional)
 * @param $args['source'] data source for the property (optional)
 * @param $args['validation'] validation for the property (optional)
 * @returns object
 * @return a particular Dynamic Property
 */
function &dynamicdata_userapi_getproperty($args)
{
    if (empty($args['type'])) {
        return;
    }
    return Dynamic_Property_Master::getProperty($args);
}

?>
