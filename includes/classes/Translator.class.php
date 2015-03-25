<?php

/* **************************************************************
 *  File: Translator.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

require_once Path::Get('path:site')."/includes/classes/Zend/Translate.php";

/**
* Translator Class
*
*/
class Translator extends Zend_Translate
{
	private static $area = '';


/**
* Constructor
*
*/
	public function __construct($target = '')
	{
    	$target = Input::clean($target, 'FILENAME');
		self::$area = $target;

		$translation_options = array('disableNotices' => true);

		/* caching for translate object (contains translations of language/xx_XX.ini) */
		Caching::$elementcache = Caching::Setup('element', true, __elementcache);
		$cachename = ($target=='admin'?'admin':'public').'_translation';
		if(!$translate = Caching::$elementcache->load($cachename)) {

			parent::__construct('ini', ($target=='admin' ? Path::Get('path:admin') : Path::Get('path:site')). '/language/en_US.ini', 'en', $translation_options);

			Caching::$elementcache->save($translate, $cachename, array('translation','translate'));
		}

		if(self::$area != 'admin') //set translation
			Zend_Registry::set('translate', $this);

	} //__construct

/**
* Commmon Translations
*
*/
	public function CommonTranslations()
	{
		/*--------------------------------------------------------------*/
		/*				Common variables for header and footer			*/
		/*--------------------------------------------------------------*/

		$translations = array();

		//some admin pages do not need common variables...
		//so only assign the common variables if tpl is registered
		if(Zend_Registry::isRegistered('tpl'))
		{
			//get template
			$tpl = Zend_Registry::get('tpl');

			if (self::$area != 'admin') //block attempts to load admin area
			{
				Caching::$elementcache = Caching::Setup('element', true, __elementcache);

				$cachename = 'public_commontranslations';

				if(!$translations = Caching::$elementcache->load($cachename)) {

					//get common translations array
					@require "CommonTranslations.php";

					if(is_array($translations))
					{
						//translate
						$translations = $this->translateArray($translations);

						Caching::$elementcache->save($translations, $cachename, array('translate','translations'));
					}
				}
			}

			//assign common translations to template
			$tpl->apply($translations);
		}
	} //CommonTranslations

/**
* Translation Setup
*
*/
	public static function TranslationSetup($rt = '')
	{
		if ('admin' == $rt)
		{
			$translate = new Translator('admin');
		}
		else
		{
			$translate = new Translator();
		}

		//set translation
//vgl im constructor!
		Zend_Registry::set('translate', $translate);

		return $translate;
	}

/**
* Translate an Array
*
*/
	public function translateArray(Array $t)
	{
		foreach ($t as $key => $x)
		{
			$t[$key] = $this->translate($x);
		}

		return $t;

	} //translateArray

} //class Translator
