<?php

/* **************************************************************
 *  File: error404.class.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

//fertig und uebersetzt

class error403 extends BaseController
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
		$tpl->title = $translate->_("Error403");
		/***page setup***/

		//internationalization
		$tpl->assign("_error403", $translate->_("Error403"));

		$tpl->display();

	} //index function

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
