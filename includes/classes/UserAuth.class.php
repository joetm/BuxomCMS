<?php

/* **************************************************************
 *  File: UserAuth.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* User Authentification
*
*/
class UserAuth {

	const USERNAME = 'username';
	const PASSWORD = 'password';
	const REMEMBER = 'remember';
	const SESSION  = 'session';
	const COOKIE_NAME = 'bx';

	const SESSION_TIME = 2629743; //1 month

	private static $username;

	private static $authenticated = false;
	private static $error = null;

/**
* User Login
*
*/
	public static function Login()
	{
		/***database connect***/
		$db = DB::getInstance();

//		//get translation
//		$translate = Zend_Registry::get('translate');

		self::$username = null;
		self::$authenticated = false;

		if(isset($_POST[self::USERNAME]))
		{
			if(empty($_POST[self::USERNAME]))
			{
                self::$error = 'The username was left blank.';
                return;
			}

			if(empty($_POST[self::PASSWORD]))
			{
                self::$error = 'The password was left blank.';
				self::$username = $_POST[self::USERNAME];
                return;
			}

			$user = $db->Row('SELECT * FROM `bx_member` WHERE `username`=?',
						array( $_POST[self::USERNAME] ));

			if(!$user)
			{
				self::$error = "Invalid login.";
                return;
			}
			else
			{
				if($user['status'] != 'active' )
				{
					self::$error = "Account expired/inactive.";
	                return;
				}

				//check password
				if ($_POST[self::USERNAME] == $user[self::USERNAME] && sha1($user['salt'].$_POST[self::PASSWORD]) == $user[self::PASSWORD])
				{
					//we found a valid login
					self::$authenticated = true;
                    self::$username = $user[self::USERNAME];
				}
				else
				{
					self::$error = "Invalid user details.";
	                return;
				}


				if (self::$authenticated == true)
				{
					//generate new session id
					$session = sha1(uniqid(rand(), true));

					//generate security token
					Session::SetToken(Session::CreateToken());

					//write session info to database
					$db->Update("INSERT INTO `bx_member_session` (`username`,`SESSION`,`securitytoken`,`IP`,`browser`,`dateline`) VALUES (?,?,?,?,?,?)",
					array(self::$username, $session, Session::GetToken(), Session::FetchIP(), md5($_SERVER['HTTP_USER_AGENT']), time() ));

					/***remember me***/
					setcookie(self::COOKIE_NAME,
							self::USERNAME . '=' . urlencode(self::$username).'&'.
							self::SESSION  . '=' . urlencode($session),
							isset($_POST[self::REMEMBER]) ? time() + self::SESSION_TIME : null,
							Config::Get('cookie_path'), Config::Get('cookie_domain'));
					/***remember me***/

					self::$authenticated = true;
				}
			}
		}
		else if(isset($_COOKIE[self::COOKIE_NAME]))
		{
			$cookie = array();
			parse_str($_COOKIE[self::COOKIE_NAME], $cookie);

			//session cleanup
			$db->Update('DELETE FROM `bx_member_session` WHERE `username`=? AND `dateline` < ?', array($cookie[self::USERNAME], time() - self::SESSION_TIME));

			$session = $db->Row('SELECT `s`.`username`,`s`.`securitytoken` FROM `s`.`bx_member_session` AS `s`, `bx_member` AS `m` WHERE `s`.`username`=? AND `s`.`SESSION`=? AND `s`.`browser`=? AND `s`.`IP`=? AND `s`.`dateline` > ? AND `m`.`username`=`s`.`username`',
				array($cookie[self::USERNAME],
            	$cookie[self::SESSION],
            	md5($_SERVER['HTTP_USER_AGENT']),
            	Session::FetchIP(),
            	time() - self::SESSION_TIME));

			if(!$session)
			{
				//delete cookie
				setcookie(self::COOKIE_NAME, "", time() - 3600,
				Config::Get('cookie_path'), Config::Get('cookie_domain'));

	//session cleanup
				$db->Update('DELETE FROM `bx_member_session` WHERE `username`=? AND `dateline` < ?', array($cookie[self::USERNAME], time() - self::SESSION_TIME));

				self::$error = 'Session has expired.';
				self::$authenticated = false;

				Logger::Activity('failed login','invalid cookie username');

                return;
            }
            else
            {
				self::$username = $session[self::USERNAME];

				Session::SetToken($session['securitytoken']);

				self::$authenticated = true;
			}
		}
		else
		{
			self::$authenticated = false;
		}

		return self::$authenticated;

	} //Login

/**
* Authentification Check
*
*/
    public static function IsAuthenticated()
    {
        return self::$authenticated;
    }

/**
* Get Authentification Error
*
*/
	public static function GetError()
    {
        return self::$error;
    }

/**
* Get Username
*
*/
    public static function GetUsername()
    {
        return self::$username;
    }

/**
* Logout
*
*/
	public static function Logout()
    {
		/***database connect***/
		$db = DB::getInstance();

		self::$authenticated = false;

		if(isset($_COOKIE[self::COOKIE_NAME]))
		{
			$cookie = array();
			parse_str($_COOKIE[self::COOKIE_NAME], $cookie);

			//session cleanup
			$db->Update('DELETE FROM `bx_member_session` WHERE `username`=? AND `session`=?', array($cookie[self::USERNAME], $cookie[self::SESSION]));
		}

		setcookie(self::COOKIE_NAME, "", time() - 3600, Config::Get('cookie_path'), Config::Get('cookie_domain'));

		Logger::Activity('logout','success');
	}

/**
* Show Login Form
*
*/
	public static function ShowLogin(){

		$translate = new Translator();
		$translate = Zend_Registry::get('translate');

		/***setup***/
		$templatename = "login";
		$tpl = new Template('tpl:'.$templatename, __tpl_cache_time);
		/***setup***/

		/***page setup***/
		$tpl->title = $translate->_("Login");
		$tpl->barcolor = "999999";
		/***page setup***/

		if(isset($_GET['controller']))
		{
			$tpl->assign("redirect", Input::clean($_GET['controller'],'NOHTML'));
		}

		//error message
		$tpl->assign("_error", self::$error);

		//internationalization
		$_t = $translate->translateArray(array(
			"login" => "Log In",
			"password" => "Password",
			"rememberme" => "Remember Me",
			"username" => "Username",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

//		$tpl->debug();

		exit();

	} //ShowLogin

} //class
