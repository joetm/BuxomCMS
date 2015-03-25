<?php

/* **************************************************************
 *  File: error404.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class permissionerror extends BaseController
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

		$templatename = "admin_permissionerror";
		$tpl = new Template('admin:'.$templatename, __tpl_cache_time);

		$translations = Tools::GetCommonAdminTranslations();
		$tpl->apply($translations);

		/***page setup***/
		$tpl->title = $translate->_("Permission Error");
		/***page setup***/

		//internationalization
		$tpl->assign("_permissionerror", $translate->_("Permission Error"));
		$tpl->assign("_permissionerror_msg", $translate->_("Permission Error Message"));

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
