<?php

/* **************************************************************
 *  File: Sorting.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Sorting
*
*/
class Sorting
{

	private $sortorder; //ASC or DESC or Array

	private $order = ''; //sql clause

/**
* Set Sort Order
*
* @access	public
* @param	string	$input
*/
	public function setSortOrder($input)
	{
		switch($input)
		{
			case 'ASC':
				$this->sortorder = 'ASC';
				break;
			case 'DESC':
			default:
				$this->sortorder = 'DESC';
				break;
		}
	}

/**
* Get Sort Order
*
* @access	public
* @return	string
*/
	public function getSortOrder()
	{
		return $this->sortorder;

	} //getSortOrder

/**
* Has Sort Order
*
* @access	public
* @return	bool
*/
	public function hasSortOrder()
	{
		if(!isset($this->sortorder))
		{
//			echo "has no sortorder: "; var_dump($this->sortorder);
			return false;
		}
		else
		{
//			echo "has sortorder: ";  var_dump($this->sortorder);
			return true;
		}

	} //hasSortOrder

/**
* Set Order
*
* @access	public
* @param	string	$input
*/
	public function setOrder($input)
	{
		$input = Input::clean($input,'NOHTML');
		$this->order = $input;
	}

/**
* Get Order
*
* @access	public
* @return	string
*/
	public function getOrder()
	{
		return $this->order;
	}

/**
* Get Order Clause
*
* @access	public
* @param	string	$input
* @return	string
*/
	public function getOrderClause($input)
	{
		/***database connect***/
		$db = DB::getInstance();

		if(isset($this->sortorder))
		{
			$this->order = $db->Prepare(' ORDER BY ? ?', array($input, $this->sortorder));
		}
		else
		{
			$this->order = $db->Prepare(' ORDER BY ? DESC', array($input));
		}

		return $this->order;

	} //getOrderClause

/**
* Sort Order Flip
*
* @access	public
*/
	public function SortorderFlip()
	{

		/***flip sortorder***/
		if($this->sortorder == 'ASC'){
			$this->sortorder = 'DESC';
		}
		else{
			$this->sortorder = 'ASC';
		}

	} //SortorderFlip

/**
* Construct Sort Order
*
* @access	public
* @param	string	$s
*/
	public function constructSortOrd($s){

		foreach($s as $key => $field){
			if(isset($_POST[$key])){
				if($_POST[$key] == 'ASC'){
					$this->order = " ORDER BY $key ASC";
					$s[$key] = 'DESC';
				}
				else{
					$this->order = " ORDER BY $key DESC";
					$s[$key] = 'ASC';
				}
			}
		}
		$this->sortorder = $s;

	} //SortOrd

} //class