<?php

/* **************************************************************
 *  File: updates.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class updates extends BaseController
{
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
		$tpl->title = $translate->_("List Updates");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			if(isset($_POST['approve']))
			{
				//approve future updates
				$updid = intval($_POST['approve']);
				$status = $db->Update("UPDATE `bx_content` SET `dateline`=? WHERE `id`=?",
							array(
								time(),
								$updid
							));
				if ($status == false)
					$tpl->errormessage($translate->_("Error")."."
						.(Config::Get('debug') ? " ".mysql_error() : '')
					);
				else
					$tpl->successmessage($translate->_("Successfully made update live").".");
			}

		/***disconnect***/
		unset($db);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"addnewupdate" => "Add New Update",
			"date" => "Date",
			"delete" => "Delete",
			"edit" => "Edit",
			"ID" => "ID",
			"link" => "Link",
			"modelname" => "Modelname",
			"preview" => "Preview",
			"slug" => "Slug",
			"tags" => "Tags",
			"title" => "Title",
			"type" => "Type",
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
			//get translation
			$translate = Zend_Registry::get('translate');

			if(isset($_POST['checkbox']))
			{
				Tools::DeleteUpdate();

				Logger::AdminActivity('deleted update(s)', '');

			} //if

		unset($_POST);

	} //deletion

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

		/***actions***/
		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if( Authentification::CheckPermission('administrator', 'editor') )
				{
					# deletion
					if (!empty($_POST['delete']))
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
