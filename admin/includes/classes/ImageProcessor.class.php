<?php

/* **************************************************************
 *  File: ImageProcessor.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Image Processor Exceptions
*
*/
class IMGProcessorException extends Exception {}

/**
* Image Processor Class
*
*/
class ImageProcessor
{

	private $folder = '';
	private $path = '';

	private $file = '';
	private $new_name = '';

	private $doublewidth = '';
	private $doubleheight = '';

	private $size = array();

	public $imgAdapter = null;

	private $coord_x = 0;
	private $coord_y = 0;
	private $coord_w = 0;
	private $coord_h = 0;

	//defaults
	private $allowed_mime_types = array('jpeg','jpg','gif');


/**
* Process
*
* @access	public
*/
	public function process()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		if(!isset($this->folder))
			throw new IMGProcessorException($translate->_("No folder specified"));

		//we have a folder

		//check if folder exists
		if(!is_dir($this->folder))
			throw new IMGProcessorException($translate->_("Not a folder"));

		$picArray = array();
		//get image list
		$this->allowed_mime_types = Config::Get('image_extensions');
		$dir = opendir($this->folder);
			while( $entry = readdir( $dir ))
			{
				if (is_dir($entry)) continue;

				if( in_array(strtolower(substr($entry, strrpos($entry, '.') + 1)), array_map('strtolower', $this->allowed_mime_types)) )
					$picArray[] = $entry;
			}
		closedir($dir);

		// Count the number of images
		$indexCount = count($picArray);

		//abort if folder is empty
		if($indexCount == 0)
			throw new IMGProcessorException($translate->_("No images in folder"));

		require_once "Zend/ProgressBar.php";
		require_once "Zend/ProgressBar/Adapter/JsPush.php";
		$adapter = new Zend_ProgressBar_Adapter_JsPush(array(
			'updateMethodName' => 'ProgressBar_Update',
			'finishMethodName' => 'ProgressBar_Finished')
		);
		$progressBar = new Zend_ProgressBar($adapter, 0, $indexCount);

//FIX: Ugly double 'thumbs' in url
		if(!is_dir(Path::Get('path:thumbs/pics') .DIR_SEP. Step2Process::$slug."/thumbs"))
		{
			//create thumbnail folder
//FIX: Ugly double 'thumbs' in url
			$makedirstatus = @Filehandler::MkDir(Path::Get('path:thumbs/pics').DIR_SEP.Step2Process::$slug."/thumbs", true); //recursive
			if(!$makedirstatus) {
				throw new IMGProcessorException($translate->_("Could not create thumbnail directory"));
			}
		}
		else
		{
			echo "Thumbnail directory already exists. Continuing anyway.<br>";
		}



//time limit is critical
set_time_limit(7200);


		$this->imgAdapter = new Img_Adapter;

		$data = array();
		//process the folder loop
		for ($i=0; $i < $indexCount; $i++)
		{
			$this->file = $this->folder. DIR_SEP  .$picArray[$i];

//FIX: Ugly double 'thumbs' in url
			$this->new_name = Path::Get('path:thumbs/pics') . DIR_SEP . Step2Process::$slug. DIR_SEP . "thumbs". DIR_SEP . $picArray[$i];


			//special function for picture set thumbnailing
			$this->imgAdapter->PictureThumbnail($this->file, $this->new_name);


			//get image size
			$size = $this->imgAdapter->GetImgSize($this->file);

			//transmit the thumbnail data
			$data['id'] = $i;
			$data['filename'] = $picArray[$i];
//FIX: Ugly double 'thumbs' in url
			$data['url'] = Path::Get('url:thumbs/pics'). DIR_SEP  .Step2Process::$slug.DIR_SEP  . "thumbs". DIR_SEP . $picArray[$i]; // $new_name;
			//file size
			$data['orig_width'] = $size[0][0];
			$data['orig_height'] = $size[0][1];

			//update progressbar
//			if ($i % 3 == 0)
				$progressBar->update($i, $data);

		} //process folder for-loop

		//end
		$progressBar->finish();

	} //process


/**
* Set Folder
*
* @access	public
*/
	public function setFolder($folder)
	{
		$this->folder = rtrim($folder, "/");










	} //setFolder

/**
* Set Coordinates
*
* @access	public
*/
	public function setCoords($x=0,$y=0,$w=0,$h=0)
	{
		//set the coordinates for cropping an image
		//we need x,y,w,h

		if($x == 0 && $y == 0 && $w == 0 && $h == 0)
			die("No coordinates for cropping.");
		else
		{
			$this->coord_x = intval($x);
			$this->coord_y = intval($y);
			$this->coord_w = intval($w);
			$this->coord_h = intval($h);
		}

	} //setCoords

} //class Imageprocessor