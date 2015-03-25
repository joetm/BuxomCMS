<?php

/* **************************************************************
 *  File: Router.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* AdminRouter Class
*
*/
class AdminRouter {

	private $controllerdir;
	private $controllerfile;

	const STANDARDCONTROLLER = 'ShowIndex';
	const EMPTYROUTE = 'home';
	const LOGINROUTE = 'login';
	const ERROR404ROUTE = '_404';
	const ERROR403ROUTE = '_403';
	const ACTION = 'action';
	const CONTROLLER = 'controller';

	private $controller = false;
	private $action = false;

/**
* Constructor
*
* @access	public
*/
	public function __construct() {

		$path = realpath( dirname(__FILE__).'/../controller' );

		if (!is_dir($path))
			throw new Exception('Invalid controller path.');

		$this->controllerdir = $path;
	}

/**
* Loader
*
* @access	public
*/
	public function loader()
	{

		/*** get the route ***/
		$this->getController();

		/*** if the file is not there, show error 404 ***/
		if (is_readable($this->controllerfile) == false)
			$this->controller = self::ERROR404ROUTE;

		/*--------------------------------------------------------------*/
		/*						Authentification						*/
		/*--------------------------------------------------------------*/

		if (!Authentification::Login()) Authentification::ShowLogin();

		/*--------------------------------------------------------------*/
		/*						Template Setup							*/
		/*--------------------------------------------------------------*/

		$templatename = "admin_".$this->controller; //.($this->action!=''?"_".$this->action:'');
		$tpl = new Template('admin:'.$templatename, __tpl_cache_time);
//(set in constructor)		Zend_Registry::set('tpl', $tpl);

		if(Config::Get('showadmintooltips'))
			$tpl->assign('__showadmintooltips', true);
		else
			$tpl->assign('__showadmintooltips', false);

		/*--------------------------------------------------------------*/
		/*					   Translation Setup						*/
		/*--------------------------------------------------------------*/

		require_once "Zend/Translate/Adapter/Ini.php";

		//caching for translate object is in Translator::__construct()
		$translate = new Translator('admin');

		//set translation
		Zend_Registry::set('translate', $translate);

		/* caching for common admin translations (translations for admin navigation menu and footer) */
		if(!$translations = Caching::$elementcache->load('admin_commontranslations')) {

			$translations = Tools::GetCommonAdminTranslations();

			Caching::$elementcache->save($translations, 'admin_commontranslations', array('translation','translate'));
		}
		$tpl->apply($translations);

		/*--------------------------------------------------------------*/
		/*						Route to Controller						*/
		/*--------------------------------------------------------------*/

		/*** a new controller class instance ***/
		$class = $this->controller;
		//class/controller is all lower case
		$controller = new $class();

		/*** check if the action is callable ***/
		if (is_callable(array($controller, $this->action)) == false)
			$action = self::STANDARDCONTROLLER;
		else
			$action = $this->action;

		/*** run the action ***/
		$controller->$action();

	} //loader

/**
* Get Controller
*
* @access	public
*/
	private function getController()
	{
		/*** get the controller from the url ***/
		$route  = (empty($_GET[self::CONTROLLER])) ? '' : Input::clean_single('g', self::CONTROLLER, 'FILENAME');
		$action = (empty($_GET[self::ACTION])) ? '' : Input::clean_single('g', self::ACTION, 'FILENAME');

		/*** Get controller ***/
		if (empty($route))
			$this->controller = self::EMPTYROUTE;
		else
			$this->controller = strtolower($route);

		/*** Get action ***/
		if (empty($action))
			$this->action = '';
		else
			$this->action = strtolower($action);

		/*** set the file path ***/
		$this->controllerfile = $this->controllerdir . DIR_SEP . $this->controller . '.class.php';

	} //getController

} //class
