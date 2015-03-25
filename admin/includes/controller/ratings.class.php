<?php

/* **************************************************************
 *  File: ratings.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class ratings extends BaseController
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
		$tpl->title = $translate->_("List Ratings");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

		/***disconnect***/
		unset($db);

		//internationalization
		$_t = $translate->translateArray(array(
			"add" => "Add",
			"addrating" => "Add Rating",
			"contentid" => "Content ID",
			"date" => "Date",
			"delete" => "Delete",
			"edit" => "Edit",
			"editaddrating" => "Edit_Add Rating",
			"id" => "ID",
			"ip" => "IP",
			"preview" => "Preview",
			"rating" => "Rating",
			"slug" => "Slug",
			"type" => "Type",
			"update" => "Update",
			"username" => "Username",
		));
		$tpl->assign("_t", $_t);

		//security token
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		$tpl->display();

		$tpl->debug();

	} //index function

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

				//delete the ratings that are marked for deletion
					$status = $db->Update("DELETE FROM `bx_rating` WHERE `id` IN (".$del_ids.")");

				//error or success message
					if(!$status)
					{
						//is plural?
						if(!explode(",",$del_ids,2))
							$delerror = $translate->_("Rating deletion error");
						else
							$delerror = $translate->_("Rating deletion errors");

						$tpl->errormessage($delerror); //"Could not delete rating(s)."
					}
					else{
						$tpl->successmessage($translate->_("Done"));
						Logger::AdminActivity('deleted rating(s)', '');
					}
			}

		unset($_POST);

	} //deletion function

/**
* addnew
*
* @access private
*/
	private function addnew()
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
				$input = Input::clean_array('p',array(
					'content' => 'UINT',
					'rating' => 'NOHTML',
				));

				if(!$status) {
					$tpl->errormessage($translate->_("Could not add rating"));
				}
				else{
					$tpl->successmessage($translate->_("Done"));
					Logger::AdminActivity('added rating', '');
				}
			}

		unset($_POST);

	} //add new

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
					# deletion handling
						if (!empty($_POST['checkbox'])) $this->deletion();
					# addnew handling
						if (isset($_POST['addnew'])) $this->addnew();
				}
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();

	} //showIndex

} //class
