<?php

/* **************************************************************
 *  File: login.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class login extends BaseController
{

/**
* index
*
* @access private
*/
	private function index() //show login form
	{
		Authentification::ShowLogin();

		die();

	} //function index

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//show login form
		$this->index();

	} //function showIndex


} //class
