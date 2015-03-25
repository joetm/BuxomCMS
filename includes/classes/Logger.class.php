<?php

/* **************************************************************
 *  File: Logger.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Logger Class
*
*/
class Logger
{

/**
* Admin Activity Logging
*
* @access	public
* @param	string	$activity
* @param	string	$info
*/
	public static function AdminActivity($activity, $info = NULL, $success = true)
	{
		/***database connect***/
		$db = DB::getInstance();

		$success = ($success == true ? 1 : 0);

		$db->Update("INSERT DELAYED INTO `bx_administrator_activitylog` (
		`id`, `username`, `action`, `info`, `IP`, `status`, `dateline`
		) VALUES (
		?,?,?,?,?,?,?
		)", array(
		NULL, Authentification::GetUsername(), $activity, $info, Session::FetchIP(), $success, time()
		));

	} //Add

/**
* Send Mail
*
* @access	public
* @param	string	$from
* @param	string	$subject
* @param	string	$fromname
* @param	string	$string
* @return	bool
*/
    public static function SendMail($from, $subject, $body, $fromname = '')
    {
		//send email notice about the new faq item
		include_once('./Zend/Mail.php');

		$mail = new Zend_Mail();
		$mail->setFrom($from, $fromname.' ('.$from.')');
		$to = Config::GetDBOptions('email');
		$mail->addTo($to, $to);
		$mail->setSubject($subject);
		$mail->setBodyText($body);

		return ($mail->send());
	} //SendMail

} //class