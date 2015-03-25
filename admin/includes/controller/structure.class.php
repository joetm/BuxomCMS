<?php

/* **************************************************************
 *  File: structure.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class structure extends BaseController
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

		$_config['db'] = Config::Get('db');

		//get translation
		$translate = Zend_Registry::get('translate');

		/***page setup***/
		$tpl->title = $translate->_("Admin Database Structure");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			$output = array();
			$output = $db->GetTables();

			foreach($output as $o)
				$output[$o['name']]['columns'] = $db->GetColumns($o['name']);

		/***disconnect***/
		unset($db);

		/***content***/
		if (isset($output)) $tpl->assign("output", $output);

		$tpl->display();

		$tpl->debug();

	} //function index

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//permission check
		//editors are not allowed to view database structure
		Authentification::CheckPermission('administrator');

		$this->index();
	}

} //class
