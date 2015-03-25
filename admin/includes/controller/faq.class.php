<?php

/* **************************************************************
 *  File: faq.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ------------- THIS SCRIPT IS NOT FREE SOFTWARE --------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

if(!defined('BX_CONTROL_PANEL')) exit('BuxomCMS');

class faq extends BaseController
{
	private $faqitems;

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
		$tpl->title = $translate->_("Faq Items");
		/***page setup***/

		/***database connect***/
		$db = DB::getInstance();

			/***stats***/
			$stats = new Stats($tpl);
			/***stats***/

			//get the categories for category selectbox
			$categories = $db->FetchAll("SELECT `id`, `name` FROM `bx_faq_category`");

		/***disconnect***/
		unset($db);

		/***error messages***/
/*
		$tpl->parseErrorMessageArray();
*/

//is this needed here?
		if(isset($tpl->errormessage)) $tpl->assign("errormessage", $tpl->errormessage);

		/***security token***/
		if(self::$token) $tpl->assign("securitytoken", self::$token);

		//internationalization
		$_t = $translate->translateArray(array(
			"addfaqitem" => "Add Faq Item",
			"answer" => "Answer",
			"bulkapprove" => "Bulk Approve",
			"category" => "Category",
			"date" => "Date",
			"delete" => "Delete",
			"email" => "Email",
			"id" => "ID",
			"name" => "Name",
			"question" => "Question",
			"scheduled_date" => "Scheduled Date",
			"status" => "Status",
		));
		$tpl->assign("_t", $_t);

		/***prefill add tag***/
		if(isset($var)) $tpl->assign("var", $var);

		$tpl->display();

		$tpl->debug();

	} //function index

/**
* deletion
*
* @access private
*/
	private function deletion(){

		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			if(isset($_POST['checkbox']))
			{
				$tpl = Zend_Registry::get('tpl');

				/***get connection***/
				$db = DB::getInstance();

				//get the ratings that are marked for deletion
				$del_ids = Input::GetCheckBoxes();

				//delete the ratings that are marked for deletion
				$status = $db->Update("DELETE FROM `bx_faq` WHERE `id` IN (".$del_ids.")");

				//is plural?
				if(!explode(",",$del_ids,2))
					$delerror = $translate->_("Faq item deletion error");
				else
					$delerror = $translate->_("Faq item deletion errors");

				if(!$status) {
					$tpl->errormessage($delerror); //"Could not delete faq item(s)."
				}
				else{
					$tpl->successmessage($translate->_("Done"));
				}
			}

		unset($_POST);

	} //deletion function

/**
* approve
*
* @access private
*/
	private function approve(){

		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			$checkbox = $_POST['checkbox'];

			//bulk approve the updates that are marked for approval

			$appr_ids = Input::getCheckboxes();

			//approve!
			$sql = "UPDATE `bx_faq` SET `status` = 'approved' WHERE `id` IN (".$appr_ids.")";
			$status = $db->update($sql);

			//is plural?
			if(!explode(",",$appr_ids,2))
				$apprerror = "Faq Item approval error";
			else
				$apprerror = "Faq Item approval errors";

			if($status == -1) {
				$tpl->errormessage($translate->_($apprerror));	//"Could not approve faq item(s)."
			}
			else{
				$tpl->successmessage($translate->_("Done"));	//"Done."
			}

		unset($_POST);

	} //approve

/**
* addnew
*
* @access private
*/
	private function addnew()
	{
		//get template
		$tpl = Zend_Registry::get('tpl');

		//get translation
		$translate = Zend_Registry::get('translate');

			/***database connect***/
			$db = DB::getInstance();

			$tpl = Zend_Registry::get('tpl');

			/* question */
				$var['question'] = Input::postvalidate_or('question','str');
			/* answer */
				$var['answer'] = Input::postvalidate_or('answer','str');
			/* name */
				$var['name'] = Input::postvalidate_or('name','str');
			/* email */
				$var['email'] = Input::postvalidate_or('email','str');
			/* date */
				$var['date'] = Input::postvalidate_or('date','date');

			if(empty($tpl->arrErrors)){
				//add faq item to database

				$sql = "INSERT INTO `bx_faq` (`id`,`question`,`answer`,`name`,`email`,`date`,`status`) VALUES (NULL,'$var[question]','$var[answer]','$var[name]','$var[email]','$var[date]','approved')";
				$status = $db->insert($sql);

				if(!$status) {
					$tpl->errormessage($translate->_("New Faq Item error"));	//"Could not write the new faq item to database."
				}
				else{
					$tpl->successmessage($translate->_("Done")); 				//"Done."
					unset($var);
					unset($_POST['checkbox']);
				}
			}
			else
			{
				//prefill
				$tpl->assign("var", $var);
			}

	} //addnew

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
					/***bulk approve***/
					if (isset($_POST['approve']) && !empty($_POST['checkbox']))
						$this->approve();

					/***deletion handling***/
					if ((isset($_POST['delete']) || !isset($_POST['approve'])) && !empty($_POST['checkbox']))
					$this->deletion();

					/***add new faq item***/
					if (!empty($_POST['addfaqitem']))
						$this->addnew();
				}
			}
			catch (Exception $e) {
					echo $translate->_('Error').': ',  $e->getMessage(), "<br>".PHP_EOL;
			}
		}

		$this->index();

	} //ShowIndex function


} //class
