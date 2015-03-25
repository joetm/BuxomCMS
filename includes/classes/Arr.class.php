<?php

/* **************************************************************
 *  File: Arr.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Array Class
*
*/
class Arr
{

/**
* Parse Array to Object
*
* @deprecated
*/
	public static function parseArrayToObject($array) {
	   $object = new stdClass();
	   if (is_array($array) && count($array) > 0) {
	      foreach ($array as $name=>$value) {
	         $name = strtolower(trim($name));
	         if (!empty($name)) {
	            $object->$name = $value;
	         }
	      }
	   }
	   return $object;
	}

/**
* Parse Object to Array
*
* @deprecated
*/
	public static function parseObjectToArray($object) {
	   $array = array();
	   if (is_object($object)) {
	      $array = get_object_vars($object);
	   }
	   return $array;
	}

/**
* Parse Array Value Padding
*
*/
	public static function addArrayPadding(Array $array, $key = 'id', $num = 3)
	{
		$i = 0;
		foreach ($array as $a){
			$array[$i]['padded'][$key] = String::strPad($a[$key], $num);
			$i++;
		}

		return $array;

	} //addArrayPadding

/**
* Order Array
*
*/
	function order_array (Array $array, $key, $order = "ASC")
	{
		$tmp = array();
		if($array){
			foreach($array as $akey => $array2)
			{
				$tmp[$akey] = $array2[$key];
			}
		}

		if($order == "DESC")
		{arsort($tmp , SORT_NUMERIC );}
		else
		{asort($tmp , SORT_NUMERIC );}

		$tmp2 = array();
		foreach($tmp as $key => $value)
		{
			$tmp2[$key] = $array[$key];
		}

		return $tmp2;

	} //order_array

/**
* Trim Elements of Array
*
*/
	public static function Trim(Array $array)
	{
		return array_map('trim', $array);
	}

/**
* Make data in an array safe to use
*
* @param	array	data to be cleaned
* @param	array	Array of variable names and types
*
*/
	public function &clean_array(&$source, $variables)
	{
		$return = array();

		foreach ($variables AS $varname => $vartype)
		{
			$return["$varname"] =& $this->clean($source["$varname"], $vartype, isset($source["$varname"]));
		}

		return $return;
	}

/**
* Array Html Entities
*
*/
	public static function array_htmlentities(&$elem)
	{
		if (!is_array($elem))
		{
//			$elem = htmlentities($elem, $double_encode = false);
			$elem = htmlspecialchars($elem, $double_encode = false);
		}
		else
		{
			foreach ($elem as $key=>$value)
				$elem[$key] = self::array_htmlentities($value);
		}
		return $elem;
	} // array_htmlentities()

} //class
