<?php
/**
 * File: $Id$
 *
 * Dynamic Object Property
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage dynamicdata properties
 * @author mikespub <mikespub@xaraya.com>
*/

/**
 * Include the base class
 *
 */
include_once "includes/properties/Dynamic_Select_Property.php";

/**
 * handle the object property
 *
 * @package dynamicdata
 */
class Dynamic_Object_Property extends Dynamic_Select_Property
{
    function Dynamic_Object_Property($args)
    {
        $this->Dynamic_Select_Property($args);
        if (count($this->options) == 0) {
            $objects =& Dynamic_Object_Master::getObjects();
            if (!isset($objects)) {
                $objects = array();
            }
            foreach ($objects as $objectid => $object) {
                $this->options[] = array('id' => $objectid, 'name' => $object['name']);
            }
        }
    }

    // default methods from Dynamic_Select_Property


	/**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
	 **/
	 function getBasePropertyInfo()
	 {
	 	$baseInfo = array(
                              'id'         => 24,
                              'name'       => 'object',
                              'label'      => 'Object',
                              'format'     => '24',
                              'validation' => '',
							'source'     => '',
							'dependancies' => '',
							// ...
						   );
		return $baseInfo;
	 }

}

?>
