<?php

/* **************************************************************
 *  File: Authentification.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Admin Panel Authentification Class
*
*/
class Authentification {

	const USERNAME = 'username';
	const PASSWORD = 'password';
	const REMEMBER = 'remember';
	const SESSION  = 'session';
	const COOKIE_NAME = 'bxcmscp';

	private static $session_time = 86400; //1 day

	private static $username;
	private static $role;

	private static $authenticated = false;
	private static $error = null;

/**
* Login
*
* @access	public
* @return	bool
*/
	public static function Login()
	{
		/***database connect***/
		$db = DB::getInstance();

//		//get translation
//		$translate = Zend_Registry::get('translate');

		self::$username = null;
		self::$authenticated = false;

		$cookie_path = '/admin/';
		$cookie_domain = (isset($_SERVER['HTTP_HOST']) ? preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']) : null);

/*
		if(($options = $db->getDBOptions('admin_session_expiry')) > 0)
			self::$session_time = $options['admin_session_expiry'];
*/

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
				Logger::AdminActivity('failed login','password blank');

                return;
            }


			$users = $db->FetchAll("SELECT `username`,`password`,`salt`,`role`
									FROM `bx_administrator`");

			if(!$users)
			{
				self::$error = "No administrator account found.";
                return;
			}
			else
			{
				/***multiple admin account check***/
				$i = 0;
				foreach($users as $u){

					if ($_POST[self::USERNAME] == $u[self::USERNAME] && md5($u['salt'].$_POST[self::PASSWORD]) == $u[self::PASSWORD])
					{
						//we found a valid login
						self::$authenticated = true;
						self::$role = $u['role'];
/*
						$perms = unserialize($u['permissions']);
*/
						self::$username = $_POST[self::USERNAME];
						break;
					}
					$i++; //we use $i later
				}

				if (self::$authenticated == true)
				{
					//login success!

	                $session  = sha1(uniqid(rand(), true));

					Session::SetToken(Session::CreateToken());
/*
					Session::SetPermissions($perms);
*/
					//write session info to database
					$db->Update("INSERT INTO `bx_administrator_session` (`username`,`SESSION`,`securitytoken`,`IP`,`browser`,`dateline`) VALUES (?,?,?,?,?,?)",
					array(self::$username, $session, Session::GetToken(), Session::FetchIP(), md5($_SERVER['HTTP_USER_AGENT']), time() ));

					/***remember me***/
					setcookie(self::COOKIE_NAME,
							self::USERNAME . '=' . urlencode(self::$username).'&'.
							self::SESSION  . '=' . urlencode($session),
							isset($_POST[self::REMEMBER]) ? time() + self::$session_time : null,
							$cookie_path, $cookie_domain);
					/***remember me***/


					//renew Admin salt and encoded password
					$salt = Session::CreateToken();
					$db->Update("UPDATE `bx_administrator` SET `salt`=?, `password`=? WHERE `username`=?",
					array($salt, md5($salt.$_POST[self::PASSWORD]), self::$username));

					Logger::AdminActivity('login','success');
				}
				else
				{
					self::$error = 'Supplied username/password combination is not valid.';
					self::$authenticated = false;

					$db->Update('INSERT DELAYED INTO `bx_administrator_activitylog` (`id`,`username`,`IP`,`action`,`info`,`dateline`) VALUES (?,?,?,?,?,?)',
					array(NULL, Input::clean_single('p','username','NOHTML'), Session::FetchIP(), 'failed login','username/password mismatch', time()));

	                return;
				}
			}
		}
		elseif(isset($_COOKIE[self::COOKIE_NAME]))
		{
			$cookie = array();
			parse_str($_COOKIE[self::COOKIE_NAME], $cookie);

			//session cleanup
			$db->Update('DELETE FROM `bx_administrator_session` WHERE `username`=? AND `dateline` < ?', array($cookie[self::USERNAME], time() - self::$session_time));

			$session = $db->Row('SELECT s.*,a.role
						FROM `bx_administrator_session` AS `s`
						JOIN `bx_administrator` AS `a` USING (`username`)
						WHERE s.username=? AND s.SESSION=? AND s.browser=? AND s.IP=? AND s.dateline >= ?',
				array($cookie[self::USERNAME],
            	$cookie[self::SESSION],
            	md5($_SERVER['HTTP_USER_AGENT']),
            	Session::FetchIP(),
            	time() - self::$session_time));

			if(!$session)
			{
				//delete cookie
				setcookie(self::COOKIE_NAME, "", time() - 3600,
				$cookie_path, $cookie_domain);

				//session cleanup
				$db->Update('DELETE FROM `bx_administrator_session` WHERE `username`=? AND `dateline` < ?', array($cookie[self::USERNAME], time() - self::$session_time));

				self::$error = 'Control panel session has expired.';
				self::$authenticated = false;

				Logger::AdminActivity('failed login','invalid login');

                return;
            }
            else
            {
					self::$username = $session[self::USERNAME];

					Session::SetToken($session['securitytoken']);

					self::$role = $session['role'];

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
* Is Authenticated?
*
* @access	public
* @return	bool
*/
	public static function IsAuthenticated()
	{
		return self::$authenticated;
	}

/**
* Permission check
*
* @access	public
* @return	bool
*/
	public static function CheckPermission()
	{
		$input = func_get_args();

//		if(!is_array($input)) $input = array($input);

		$role = self::GetRole();

		if(in_array($role, $input))
			return true;
		else
			Template::PermissionDenied();
			//ends here
	}

/**
* Log Out
*
* @access	public
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
			$db->Update('DELETE FROM `bx_administrator_session` WHERE `username`=? AND `session`=?', array($cookie[self::USERNAME], $cookie[self::SESSION]));
		}

		$cookie_path = '/admin/';
		$cookie_domain = (isset($_SERVER['HTTP_HOST']) ? preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']) : null);

		setcookie(self::COOKIE_NAME, "", time() - 3600, $cookie_path, $cookie_domain);

		Logger::AdminActivity('logout','success');
	}


/*
	public static function Check()
	{
		$translate = new Translator('admin');

		if (isset($_REQUEST['_SESSION']))
		{
		//hacking attempt
			header("Location: ".Path::Get('url:admin')."/error403");
			die($translate->_("Authentification error"));
		}
	} //Check
*/

/**
* Get Error
*
* @access	public
* @return	string
*/
	public static function GetError()
	{
		return self::$error;
	}

/**
* Get Username
*
* @access	public
* @return	string
*/
	public static function GetUsername()
	{
		return self::$username;
	}

/**
* Get Role
*
* @access	public
* @return	string
*/
	public static function GetRole()
	{
		return self::$role;
	}
/**
* Show Login
*
* @access	public
*/
	public static function ShowLogin(){

		$translate = new Translator('admin');
	//		$translate = Zend_Registry::get('translate');

		/***setup***/
		$templatename = "admin_login";
		$tpl = new Template('admin:'.$templatename, __tpl_cache_time);
		/***setup***/

		/***page setup***/
		$tpl->title = $translate->_("Admin Login");
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
			"adminlogin" => "Admin Login",
			"login" => "Log In",
			"password" => "Password",
			"rememberme" => "Remember Me",
			"username" => "Username",
		));
		$tpl->assign("_t", $_t);

		$tpl->display();

	//		$tpl->debug();

		exit();

	} //ShowLogin function

} //class
