<?php

/* **************************************************************
 *  File: Img_GD.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* GD Class
*
*/
class Img_GD {

	const JPG = 'jpg';
	const GIF = 'gif';
	const PNG = 'png';
	const JPEG = 'jpeg';

	private $orig_size = array();
	private $quality;
	private $file;
	private $newname;

	public static $thumbnailcounter = 0;

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

		//get translation
		$translate = Zend_Registry::get('translate');

		if (!file_exists($file))
			throw new Exception ($translate->_("Error404"));

		//darf keine systemdateien ueberschreiben!!!





		$w = intval($w);
		$h = intval($h);

		if(is_null($quality))
			$this->quality = Config::GetDBOptions('thumbnailquality');
		else
			$this->quality = intval($quality);

		//get file extension
		$ext = strtolower(String::GetFileExtension($file));

		list($width_orig, $height_orig) = getimagesize($file);

		$image_p = imagecreatetruecolor($w, $h);

		switch($ext){
			case self::GIF:
				$image = @imagecreatefromgif($file);
				break;
			case self::JPG:
			case self::JPEG:
				$image = @imagecreatefromjpeg($file);
				break;
			case self::PNG:
				$image = @imagecreatefrompng($file);
				break;
		}

		@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w, $h, $width_orig, $height_orig);

		switch($ext){
			case self::GIF:
				if(!@imagegif($image_p, $newname)){
					throw new Exception($translate->_("Permission Denied")." [".self::GIF."]");
				}
				break;
			case self::JPG:
			case self::JPEG:
				if(!@imagejpeg($image_p, $newname, $this->quality)){
					throw new Exception($translate->_("Permission Denied")." [".self::JPG."]");
				}
				break;
			case self::PNG:
				if(!@imagepng($image_p, $newname, $this->quality)){
					throw new Exception($translate->_("Permission Denied")." [".self::PNG."]");
				}
				break;
		}

		@imagedestroy($image);
		unset($image_p);

	} //Resize

/**
* Crop
*
* @access	public
* @param	string	$file
* @param	string	$newname
* @param	integer	$src_x
* @param	integer	$src_y
* @param	integer	$src_w
* @param	integer	$src_h
* @param	integer	$dest_w
* @param	integer	$dest_h
* @param	integer	$quality
*/
	public function Crop($file, $newname, $src_x, $src_y, $src_w, $src_h, $dest_w=0, $dest_h=0, $quality=NULL){

		if (!file_exists($file))
			throw new Exception ($translate->_("Error404"));

		$src_x = intval($src_x);
		$src_y = intval($src_y);
		$src_w = intval($src_w);
		$src_h = intval($src_h);
		$dest_w = intval($dest_w);
		$dest_h = intval($dest_h);

		if($dest_w == 0 || $dest_h == 0)
		{
			$dest_w = $src_w;
			$dest_h = $src_h;
		}

		//validate file and newname
		$file = $file;
		$newname = $newname;


		//get translation
		$translate = Zend_Registry::get('translate');

		if(is_null($quality))
			$this->quality = Config::GetDBOptions('thumbnailquality');
		else
			$this->quality = intval($quality);

		$ext = strtolower(String::GetFileExtension($file));

		$image_p = imagecreatetruecolor($dest_w, $dest_h);

		switch($ext){
			case self::GIF:
				$image = @imagecreatefromgif($file);
				break;
			case self::JPG:
			case self::JPEG:
				$image = imagecreatefromjpeg($file);
				break;
			case self::PNG:
				$image = @imagecreatefrompng($file);
				break;
		}

		@imagecopyresampled($image_p, $image, 0, 0, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h);

		switch($ext){
			case self::GIF:
				if(!@imagegif($image_p, $newname)){
					throw new Exception($translate->_("Permission Denied")." [".self::GIF."]");
				}
				break;
			case self::JPG:
			case self::JPEG:
				if(!@imagejpeg($image_p, $newname, $this->quality)){
					throw new Exception($translate->_("Permission Denied")." [".self::JPG."]");
				}
				break;
			case self::PNG:
				if(!@imagepng($image_p, $newname, $this->quality)){
					throw new Exception($translate->_("Permission Denied")." [".self::PNG."]");
				}
				break;
		}

		@imagedestroy($image);
		unset($image_p);

	} //Crop

/**
* Thumbnail
*
* @access	public
* @param	string	$file
* @param	string	$newname
* @param	integer	$quality
*/
	public function PictureThumbnail($file, $newname, $quality=NULL){

		if (!file_exists($file))
			throw new Exception ($translate->_("Error404"));

		//validate!
		$this->file = $file;
		$this->newname = $newname;

		//get config
		$thumbnailsizes = Config::GetDBOptions('picturegrab_thumbnailsize');
		if(!$thumbnailsizes) throw new Exception ("Thumbnail Configuration could not be loaded.");

		if(is_null($quality))	$this->quality = Config::GetDBOptions('thumbnailquality'); //Config::Get('thumbnailquality');
		else					$this->quality = intval($quality);

		// Get the image size
		$this->orig_size = getimagesize($this->file);

		//compare image size with config thumbnail size
		$config_aspect_ration = 0;
		$img_aspect_ratio = 1;

		if ($thumbnailsizes['height'] != 0)
			$config_aspect_ration = $thumbnailsizes['width'] / $thumbnailsizes['height'];
		if ($this->orig_size[1] != 0)
			$img_aspect_ratio = $this->orig_size[0] / $this->orig_size[1]; //width/height

		//if image aspect ratio does not match thumbnail size
		if($img_aspect_ratio != $config_aspect_ration)
		{
			//we need to crop!
			//we crop from the middle

			if($img_aspect_ratio < 1)
			{ /***portrait***/

				//width is defining
				$w = $this->orig_size[0];
				$h = $w / $config_aspect_ration;
				//check if we exceed the image height
				if($h > $this->orig_size[1])
				{
					//make height defining
					$h = $this->orig_size[1];
					$w = $config_aspect_ration * $h;
				}

				$x = 0;
				$y = ($this->orig_size[1] / 2) - ($h / 2);


//				echo self::$thumbnailcounter . ": portrait crop."; // w: ".$w.", h: ".$h.", x: ".$x.", y: ".$y;


				$this->Crop($this->file, $this->newname, $x, $y, $w, $h, $thumbnailsizes['width'], $thumbnailsizes['height'], $this->quality);
			}
			else
			{ /***landscape***/

				//height is defining
				$h = $this->orig_size[1];
				$w = $config_aspect_ration * $h;
				//check if we exceed the image width
				if($w > $this->orig_size[0])
				{
					//make width defining
					$w = $this->orig_size[0];
					$h = $w / $config_aspect_ration;
				}

				$x = ($this->orig_size[0] / 2) - ($w / 2);
				$y = 0;


//				echo self::$thumbnailcounter . ": landscape crop."; // w: ".$w.", h: ".$h.", x: ".$x.", y: ".$y;


				$this->Crop($this->file, $this->newname, $x, $y, $w, $h, $thumbnailsizes['width'], $thumbnailsizes['height'], $this->quality);

			}

		}
		//else: resize without cropping, because image aspect ratio matches the thumbsize
		else
		{

//			echo self::$thumbnailcounter . ": resizing without cropping.";

			$this->Resize($this->file, $this->newname, $thumbnailsizes['width'], $thumbnailsizes['height'], $this->quality);
		}

//		self::$thumbnailcounter++;

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

		$dir = String::Slash(Input::clean($dir, 'STR'), 1, 1);
		$newname = Input::clean($newname, 'STR');





		if(is_file($newname) && is_readable($newname) && filesize($newname) > 0)
			return true;
		else
			return false;

	} //CreateGif

/**
* Get Image Size
*
* @access	public
* @param	string	$filepath
* @param	bool	$quality
* @return	mixed	array | bool
*/
	public function GetImgSize($filepath)
	{
		$size = getimagesize($filepath);

		if($size)
			return array($size);
		else
			return false;

	} //GetImgSize

} //class
