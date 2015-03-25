<?php

/* **************************************************************
 *  File: error404.class.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

class error404 extends BaseController
{

/**
* index
*
* @access private
*/
	private function index()
	{
		//get template
		$tpl = Zend_Registry::get('tpl');
		//get translation
		$translate = Zend_Registry::get('translate');

		/***page setup***/
		$tpl->title = $translate->_("Error404");
		/***page setup***/

		//internationalization
		$tpl->assign("_error404",$translate->_("Error404"));

		$tpl->display();

//		$tpl->debug();

	} //index function

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//permission check
		//editors do not have access to options
		if('administrator' != Authentification::GetRole())
			Template::PermissionDenied();

		$this->index();
	}

} //class
