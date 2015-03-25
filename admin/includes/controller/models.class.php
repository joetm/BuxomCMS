<?php

/* **************************************************************
 *  File: models.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class models extends BaseController
{
	private $models;

	private static $token = false;

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
		$tpl->title = $translate->_("Edit Models");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***database disconnect***/
		unset($db);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"addnewmodel" => "Add New Model",
			"birthdate" => "Birthdate",
			"delete" => "Delete",
			"edit" => "Edit",
			"edittags" => "Edit Tags",
			"ID" => "ID",
			"comments" => "Comments",
			"link" => "Link",
			"modelname" => "Modelname",
			"preview" => "Preview",
			"rating" => "Rating",
			"slug" => "Slug",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

		$tpl->debug();

	} //function index

/**
* deletion
*
* @access private
*/
	private function deletion()
	{
		if(isset($_POST['checkbox']))
		{
			if( Authentification::CheckPermission('administrator', 'editor') )
			{
				Tools::DeleteUpdate();

				Logger::AdminActivity('deleted model(s)', '');
			}
		} //if

	} //deletion function

/**
* ShowIndex
*
* @access public
*/
	public function ShowIndex()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		self::$token = Session::GetToken();

		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if( Authentification::CheckPermission('administrator', 'editor') )
				{
					/***deletion handling***/
					if (isset($_POST['delete']))
						$this->deletion();
				}
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();
	}

} //class
