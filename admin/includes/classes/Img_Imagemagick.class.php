<?php

/* **************************************************************
 *  File: Img_Imagemagick.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* ImageMagick Class
*
*/
class Img_Imagemagick {

	private $file;
	private $newname;
	private $orig_size;
	private $doublewidth;
	private $doubleheight;
	private $quality;

	const IMAGICK_APP = 'convert';
	const IMAGICK_IDENTIFY = 'identify'; //identify.exe

	private static $_imagemagickpath = '';

/**
* Constructor
*
* @access	public
*/
	public function __construct()
	{
		self::$_imagemagickpath = Config::Get('_imagemagickpath');
	}

/**
* Resize
*
* @access	public
* @param	string	$file
* @param	string	$newname
* @param	integer	$w
* @param	integer	$h
*/
	public function Resize($file, $newname, $w, $h){

		//validate
		$w = intval($w);
		$h = intval($h);

		shell_exec(self::$_imagemagickpath . self::IMAGICK_APP.
		" ".escapeshellarg($file).
		" -strip".
		" -interlace Plane".
		" -resize ".escapeshellarg($w."x".$h)."^".
		" ".escapeshellarg($newname));

	} //Resize

/**
* Crop
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

		//validate

		$x = intval($x);
		$y = intval($y);
		$w = intval($w);
		$h = intval($h);

		shell_exec(self::$_imagemagickpath . self::IMAGICK_APP.
		" ".escapeshellarg($file).
		" -crop ".escapeshellarg($w."x".$h."+".$x."+".$y).
		" +repage".
		" -strip".
		" -interlace Plane".
		" ".escapeshellarg($newname));

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

		$this->file = $file;
		$this->newname = $newname;

		//get config
		/***database connect***/
		$db = DB::getInstance();

		$options = Config::GetDBOptions(array('thumbnailquality','thumbnailsharpen','picturegrab_thumbnailsize'));








/*
	//muss NICHT thumbnailsizes vom richtigen TYP verwenden
	//weil es eine dedizierter PICTURE function ist

		//OLD.... type
		if(Step2Process::$type == Path::Get('videos'))
			$options['thumbnailsizes'] = unserialize($options['videograb_thumbnailsize']);
		else
			$options['thumbnailsizes'] = unserialize($options['picturegrab_thumbnailsize']);
*/

		$options['thumbnailsizes'] = $options['picturegrab_thumbnailsize'];









		if(is_null($quality))
			$this->quality = $options['thumbnailquality'];
		else
			$this->quality = intval($quality);


		// Get the image size
		//GD function
		$this->orig_size = getimagesize($this->file);

		//compare image size with config thumbnail size
		$config_aspect_ration = 0;
		$img_aspect_ratio = 1;
		if ($options['thumbnailsizes']['height'] != 0)
			$config_aspect_ration = $options['thumbnailsizes']['width'] / $options['thumbnailsizes']['height'];
		if ($this->orig_size[1] != 0)
			$img_aspect_ratio = $this->orig_size[0] / $this->orig_size[1];

		//makes cropping faster
		$this->doublewidth = $options['thumbnailsizes']['width']*2;
		$this->doubleheight = $options['thumbnailsizes']['height']*2;

		//if image aspect ratio does not match thumbnail size
		//image needs to be cropped
		if($img_aspect_ratio != $config_aspect_ration)
		{
			//we need to crop!

				//imagemagick switches

				// -thumbnail : remove metadata and resize
				// -define jpeg:size=999x999 : scale to smaller image first
				// -auto-orient : orient image according to camera data
				// -unsharp 0x.5 : sharpen

				if(Config::Get('debug') == true) //debug output
					echo "cropping ".basename(Input::clean($this->newname, 'NOHTML'))." [".($img_aspect_ratio <= 1 ? 'portrait' : 'landscape')."]: ";

				if($img_aspect_ratio <= 1)
				{
					//portrait
					$geometry = escapeshellarg($options['thumbnailsizes']['width']); //"^";

					if(Config::Get('debug') == true) //debug output
						echo "(portrait) ";
				}
				else
				{
					//landscape
					$geometry = "x". escapeshellarg($options['thumbnailsizes']['height'])."^";

					if(Config::Get('debug') == true) //debug output
						echo "(landscape) ";
				}

				if(Config::Get('debug') == true) //debug output
					echo $geometry."<br>";

/*
//OLD
				$geometry = escapeshellarg($options['thumbnailsizes']['width']) ."x". escapeshellarg($options['thumbnailsizes']['height'])."^";
*/

				$cmd = self::$_imagemagickpath . self::IMAGICK_APP.
				" -define jpeg:size=".escapeshellarg($this->doublewidth."x".$this->doubleheight).
				" -size ".escapeshellarg($this->orig_size[0]."x".$this->orig_size[1]).
				" ".escapeshellarg($this->file).
				" -thumbnail ".$geometry.
				" -auto-orient -gravity center".
				" -unsharp 0x".escapeshellarg($options['thumbnailsharpen']).
				" -crop ".escapeshellarg($options['thumbnailsizes']['width']."x".$options['thumbnailsizes']['height'])."+0+0".
				" +repage".
				" -strip".
				" -interlace Plane".
				" ".escapeshellarg($this->newname);
				shell_exec($cmd);
		}
		//else: just resize without cropping
		else
		{
			//image can safely be resized to thumbnail dimensions

			shell_exec(self::$_imagemagickpath . self::IMAGICK_APP.
			" -auto-orient".
			" -define jpeg:size=".escapeshellarg($this->doublewidth."x".$this->doubleheight).
			" -size ".escapeshellarg($this->orig_size[0]."x".$this->orig_size[1]).
			" ".escapeshellarg($this->file).
			" -thumbnail ".escapeshellarg($options['thumbnailsizes']['width']."x".$options['thumbnailsizes']['height']).
			" -strip".
			" -interlace Plane".
			" -unsharp 0x".escapeshellarg($options['thumbnailsharpen']).
			" ".escapeshellarg($this->newname));

		}

	} //Thumbnail

/**
* Create Gif
*
* @access	public
* @param	string	$dir
* @param	string	$newname
* @return	bool
*/
	public function CreateGif($dir, $newname){

		$dir = String::Slash(Input::clean($dir, 'STR'), 1, 1);
		$newname = Input::clean($newname, 'STR');

		shell_exec(self::$_imagemagickpath . self::IMAGICK_APP.
		" ".escapeshellarg($dir.'*.jpg').
		" -delay 20".
		" -loop 0".
		" -strip".
		" -interlace Plane".
		" ".escapeshellarg($newname));

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

/*
		$identify_path = String::Slash(self::$_imagemagickpath, 1, 1) . self::IMAGICK_IDENTIFY;

		if(is_executable($identify_path))
		{

			//	example raw output:
			//	<path>/members/pics/bianca/army/001.jpg JPEG 2336x3504 2336x3504+0+0 8-bit DirectClass 5.493MB 0.000u 0:00.000

			$raw = shell_exec($identify_path." ".escapeshellarg($filepath));


			//example output format
			//array(7) { [0]=>  int(2336) [1]=>  int(3504) [2]=>  int(2) [3]=>  string(26) "width="2336" height="3504"" ["bits"]=>  int(8) ["channels"]=>  int(3) ["mime"]=>  string(10) "image/jpeg" }


			$matches = array();
			$output = array();

			//type
			if(preg_match('~\s(\w+)\s~', $raw, $matches))
				$output['mime'] = "image/".strtolower($matches[0]);
			else
				$output['mime'] = null;

			//size
			if(preg_match('~\s(\d+)x(\d+)\s~', $raw, $matches))
			{
				$output[0] = $matches[1];
				$output[1] = $matches[2];
				$output[3] = "width=\"".$matches[1]."\" height=\"".$matches[2]."\"";
			}
			else
				$output[0] = $output[1] = $output[3] = null;

			return $output;
		}
		else
		{
			return false;
		}
*/

		$size = getimagesize($filepath);

		if($size)
			return array($size);
		else
			return false;

	} //GetImgSize

} //class
