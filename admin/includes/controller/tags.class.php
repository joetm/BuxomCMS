<?php

/* **************************************************************
 *  File: tags.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class tags extends BaseController
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
		$tpl->title = $translate->_("List Tags");
		/***page setup***/

		/***get connection***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);

		//output

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"addnewtag" => "Add New Tag",
			"addtag" => "Add Tag",
			"delete" => "Delete",
			"description" => "Description",
			"id" => "ID",
			"preview" => "Preview",
			"tag" => "Tag",
			"update" => "Update",
			"updateid" => "Update-ID",
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
				$tpl = Zend_Registry::get('tpl');

				/***get connection***/
				$db = DB::getInstance();

				$status = false;

				//get the checkboxes
				$del_ids = Input::GetCheckBoxes();

				//delete the tags that are marked for deletion
					$status = $db->Update("DELETE FROM `bx_tag` WHERE `id` IN (".$del_ids.")");

				//remove the tags from updates
					$db->Update("DELETE FROM `bx_tag_content` WHERE `tag_id` IN (".$del_ids.")");

				//error or success message
					if(!$status)
					{
						//is plural?
						if(!explode(",",$del_ids,2))
							$delerror = $translate->_("Tag deletion error");
						else
							$delerror = $translate->_("Tag deletion errors");

						$tpl->errormessage($delerror); //"Could not delete tag(s)."
					}
					else{
						$tpl->successmessage($translate->_("Done"));
						Logger::AdminActivity('deleted tag(s)', '');
					}
			}

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

		if(!empty($_POST))
		{
			try {
				if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] != self::$token)
					throw new Exception($translate->_('Security Token mismatch'));

				if( Authentification::CheckPermission('administrator', 'editor') )
				{
					/***deletion handling***/
					if (!empty($_POST['checkbox'])) $this->deletion();
				}
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
//				die();
			}
		}

		/***show tags list***/
		$this->index();

	} //ShowIndex

} //class
