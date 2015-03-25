<?php

/***************************************************************
 * File: Translation.php
 * Version: 1.0
 *
 * Database plugin for the template engine
 *
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

//class to define custom translations in the template

class TplTranslation extends OutlinePlugin {

	public static $X;


	public static function register (&$compiler)
	{
		self::$X = &$compiler;
		$compiler->registerTag('translations', 'user_translations');
	}

	public function build_array_args($args) {

		$args = String::stripChars($args);

		$a = array();
		foreach (self::$X->parse_attributes($args) as $name => $value)
			$a[$name] = trim(trim($value,"'\""));
		return $a;
	}

	public function collapse_array($args) {
		$a = array();
		foreach ($args as $name => $value)
			$a[] = "\"$name\" => \"$value\"";

		return "array(".implode(", ", $a).")";
	}


	public function user_translations ($_args)
	{
		$args = $this->build_array_args($_args);

		//internationalization
		$translate = Zend_Registry::get('translate');
		$_t = $translate->translateArray($args);

//		$translationstr = $this->collapse_array($_t);

		foreach ($_t as $key => $value)
		{
			self::$X->code("\$".$key."="."\"".$value."\";");
		}

//		self::$X->code("var_dump($translationstr);");

//		self::$X->code("\$outline->vars = \$outline->vars + $translationstr;");

	} //user_translations


	private function stripChars($str)
	{
		//replace new line characters and tabs with " "
		//also remove multiple " "
		return preg_replace('~\r\n|\r|\n|\t|[\s]+~', " ", $str);
	}

} //class