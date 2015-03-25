<?php

/* **************************************************************
 *  File: logout.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

//if ($userProfileNamespace->isLocked()) {
//    $userProfileNamespace->unLock();
//}

class logout {

/**
* index
*
* @access private
*/
	private function index()
	{
		Authentification::Logout();

		//redirect
		//$hostname = $_SERVER['HTTP_HOST'];
		/*
			//$path = dirname($_SERVER['PHP_SELF']);
			//($path == '/' ? '' : $path)
		*/
		header('Location: '.Path::Get('url:admin').'/login');
	}

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		$this->index();
	}

} //class