<?php
/**
 * File: $Id$
 *
 * Dynamic Data Image Property
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
include_once "includes/properties/Dynamic_TextBox_Property.php";

/**
 * handle the image property
 *
 * @package dynamicdata
 */
class Dynamic_Image_Property extends Dynamic_TextBox_Property
{
    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (!empty($value) && $value != 'http://') {
        // TODO: add some image validation routine !
            if (preg_match('/[<>"]/',$value)) {
                $this->invalid = xarML('image URL');
                $this->value = null;
                return false;
            } else {
                $this->value = $value;
            }
        } else {
            $this->value = '';
        }
        return true;
    }

//    function showInput($name = '', $value = null,  $size = 0, $maxlength = 0, $id = '', $tabindex = '')
    function showInput($args = array())
    {
        extract($args);
        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($value)) {
            $value = 'http://';
        }
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        /*return '<input type="text"'.
               ' name="' . $name . '"' .
               ' value="'. (isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value)) . '"' .
               ' size="'. (!empty($size) ? $size : $this->size) . '"' .
               ' maxlength="'. (!empty($maxlength) ? $maxlength : $this->maxlength) . '"' .
               ' id="'. $id . '"' .
               (!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '') .
               ' />' .
               (!empty($value) && $value != 'http://' ? ' [ <a href="'.$value.'" target="preview">'.xarML('show').'</a> ]' : '') .
               (!empty($this->invalid) ? ' <span class="xar-error">'.xarML('Invalid #(1)', $this->invalid) .'</span>' : '');
        */
        $data['name']     = $name;
        $data['id']       = $id;
        $data['value']    = isset($value) ? xarVarPrepForDisplay($value) : xarVarPrepForDisplay($this->value);
        $data['tabindex'] = !empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '';
        $data['invalid']  = !empty($this->invalid) ? xarML('Invalid #(1)', $this->invalid) :'';
        $data['maxlength']= !empty($maxlength) ? $maxlength : $this->maxlength;
        $data['size']     = !empty($size) ? $size : $this->size;

        $template="image";
        return xarTplModule('dynamicdata', 'admin', 'showinput', $data , $template);
    }

    function showOutput($args = array())
    {
        extract($args);
        $data = array();

        if (!isset($value)) {
            $value = $this->value;
        }
        if (!empty($value)) {
            $value = xarVarPrepForDisplay($value);
        // TODO: add size/alt here ?
            //return '<img src="'.$value.'" />';
        }
        if (!isset($name)){
            $name=$this->name;
        }
        $data['value'] = $value;
        $data['name']  = $name;
        $data['id']    = $this->id;

        $template="image";
        return xarTplModule('dynamicdata', 'user', 'showoutput', $data ,$template);

    }

	/**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
	 **/
	 function getBasePropertyInfo()
	 {
	 	$args = array();
	 	$baseInfo = array(
                              'id'         => 12,
                              'name'       => 'image',
                              'label'      => 'Image',
                              'format'     => '12',
                              'validation' => '',
                              'source'         => '',
                              'dependancies'   => '',
                              'requiresmodule' => '',
                              'aliases'        => '',
							  'args'           => serialize($args),
							// ...
						   );
		return $baseInfo;
	 }
}

?>
