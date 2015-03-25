<?php

/* **************************************************************
 *  File: Img_Adapter.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Image Adapter Class
*
*/
class Img_Adapter {

	private $adapter = null;

	private static $has_imagick = false;
	private static $has_gd = false;

/**
* Constructor
*
* @access	public
*/
	public function __construct() {

		$this->SetAdapter();

	} //__construct

/**
* Set Image Adapter
*
* @access	private
*/
	private function SetAdapter()
	{
		//ImageMagick is the preferred tool
		//GD could produce memory problems on large image files!
		//ImageMagick is also much faster.
		//So if possible, use ImageMagick!

		self::detectImageLibrary();

		if(self::$has_imagick == true)
		{
			//use Imagick whenever possible!
			//it is way better than GD!
			$this->adapter = new Img_Imagemagick;
		}
		elseif(self::$has_gd == true)
		{
			//GD
			$this->adapter = new Img_GD;
		}
		else
		{
			//cannot crop any pics
			throw new IMGProcessorException("ImageMagick/GD not found!");
		}

	} //SetAdapter

/**
* Detect ImageMagick extension and GD library
*
* @access	public
*/
	public static function detectImageLibrary()
	{
		if( extension_loaded('imagick') )
		{
			self::$has_imagick = true;
			return;
		}
		elseif(@dl('imagick'))
		{
			self::$has_imagick = true;
			return;
		}
		else
		{
			///!!!

			$status = false;
			exec('convert -version', $results, $status);
			if(!$status || $status == 126)
			{
				self::$has_imagick = false;
			}
			else
			{
				self::$has_imagick = true;
				return;
	 		}

			//no imagemagick found
			//check for GD

			if (extension_loaded('gd') && function_exists('imagecreatetruecolor')){
				self::$has_gd = true;
				return;
			}

		}

		//no image library found
		self::$has_imagick = false;
		self::$has_gd = false;

	} //detectImageLibrary

	public static function HasImagick()
	{
		return self::$has_imagick;
	}

	public static function HasGD()
	{
		return self::$has_gd;
	}

/**
* Resize
*
* @access	public
* @param	string	$file
* @param	string	$newname
* @param	integer	$w
* @param	integer	$h
* @param	integer	$quality
*/
	public function Resize($file, $newname, $w, $h, $quality=NULL){

/*
		//get translation
		$translate = Zend_Registry::get('translate');

		try
		{
*/
			$this->adapter->Resize($file, $newname, $w, $h, $quality);
/*
		}
		catch (Exception $e) {
			echo $translate->_('Error').': '.$e->getMessage().PHP_EOL;
		}
*/

	} //Resize

/**
* Cropping
*
* @access	public
* @param	string	$file
* @param	string	$newname
* @param	integer	$x
* @param	integer	$y
* @param	integer	$w
* @param	integer	$h
*/
	public function Crop($file, $newname, $x, $y, $w, $h){

/*
		//get translation
		$translate = Zend_Registry::get('translate');

		try
		{
*/
			$this->adapter->Crop($file, $newname, $x, $y, $w, $h);
/*
		}
		catch (Exception $e) {
			echo $translate->_('Error').': '.$e->getMessage().PHP_EOL;
		}
*/

	} //Crop

/**
* Thumbnail
*
* @access	public
* @param	string	$file
* @param	string	$newname
*/
	public function PictureThumbnail($file, $newname){

/*
		//get translation
		$translate = Zend_Registry::get('translate');

		try
		{
*/
			$this->adapter->PictureThumbnail($file, $newname);
/*
		}
		catch (Exception $e) {
			echo $translate->_('Error').': '.$e->getMessage().PHP_EOL;
		}
*/

	} //Thumbnail

/**
* CreateGif
*
* @access	public
* @param	string	$dir
* @param	string	$newfile
* @return	bool
*/
	public function CreateGif($dir, $newfile){

		if(!is_dir($dir))
			return false;

		if(!$dir || !$newfile)
			return false;
/*
		//get translation
		$translate = Zend_Registry::get('translate');

		try
		{
*/
			return $this->adapter->CreateGif($dir, $newfile);
/*
		}
		catch (Exception $e) {
			echo $translate->_('Error').': '.$e->getMessage().PHP_EOL;
		}
*/

//		if(file_exists($newfile))
//			return true;

	} //CreateGif

/**
* Get Image Size
*
* @access	public
* @return	mixed	array | bool
*/
	public function GetImgSize($filepath){

		//get translation
		$translate = Zend_Registry::get('translate');

		try
		{
			$ret = $this->adapter->GetImgSize($filepath);
		}
		catch (Exception $e) {
			echo $translate->_('Error').': '.$e->getMessage().PHP_EOL;
			$ret = false;
		}

		return $ret;

	} //GetImgSize

} //class
