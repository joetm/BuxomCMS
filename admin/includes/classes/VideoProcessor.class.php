<?php

/* **************************************************************
 *  File: VideoProcessor.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Video Processor Exceptions
*
*/
class VIDProcessorException extends Exception {}

/**
* Video Processor Class
*
*/
class VideoProcessor
{

	private $folder = null;

	//progress bar (Video.class.php has its own progress bar)
	public static $progressBar = null;

	private static $debug;

	private static $thumbnails;

/**
* Process
*
* @access	public
*/
	public function process()
	{
		//get translation
		$translate = Zend_Registry::get('translate');

		if(is_null($this->folder))
			throw new VIDProcessorException($translate->_("No folder specified"));
		//check if folder exists
		if(!is_dir($this->folder))
			throw new VIDProcessorException($translate->_("Not a folder"));

		//we have a folder


		$videoArray = array();
		//get video list
		$video_mime_types = Config::Get('video_extensions');

		$dir = opendir($this->folder);
			while( $entry = readdir( $dir ))
			{
				if (is_dir($entry)) continue;

				if( in_array(strtolower(substr($entry, strrpos($entry, '.') + 1)), array_map('strtolower', $video_mime_types)) )
					$videoArray[] = $entry;
			}
		closedir($dir);

		if(empty($videoArray)) throw new Exception('no videos found');


		$numvideos = count($videoArray);


		//progress bar!
			//setup progress bar
			require_once "Zend/ProgressBar.php";
			require_once "Zend/ProgressBar/Adapter/JsPush.php";
			$adapter = new Zend_ProgressBar_Adapter_JsPush(array(
				'updateMethodName' => 'ProgressBar_Update',
				'finishMethodName' => 'ProgressBar_Finished')
			);
			$progress_max = $numvideos + Config::GetDBOptions('num_video_screencaps');
			self::$progressBar = new Zend_ProgressBar($adapter, 0, $progress_max); //0...numvideos + num_framegrabs

		self::$debug = Config::Get('debug');

		//timeout is death
		@set_time_limit(18000);

		try
		{

			$videodata = array();

			$outputfolder = 'framegrab' . time(); //no trailing slash!

			for($i=0; $i < $numvideos; $i++)
			{

				$v = new Video( $this->folder .'/'. $videoArray[$i] );

				//get video info
				if(self::$debug) Tools::debug_msg( "Fetching video data for ".htmlentities($videoArray[$i])."...");
					$videodata[$i] = $v->GetInfo();
				if(self::$debug) Tools::debug_msg( " ok [".$videodata[$i]['video_format']."]");

				if($videodata[$i])
				{
					$videodata[$i]['id'] = $i;

					//outputfolder
//					$outputfolder = 'framegrab' . time(); //no trailing slash!

					//set output folder
					$v->SetOutputDir($outputfolder);

					$start = time();
/*
//debug
						if(!self::$thumbnails) //only grab frames from the first video
						{
							//extract screencaps
							if(self::$debug) Tools::debug_msg( "Grabbing frames..." );
								self::$thumbnails = $v->GrabFrames();
							if(self::$debug) Tools::debug_msg( "Done." );
						}
*/
					$end = time();

					$cropping_time = ($end - $start);
//					$tpl->assign('cropping_time', $cropping_time);

					//set output folder
					$v->SetOutputDir($outputfolder .'/'. $i);

					//grab one single frame as preview (will not be used for anything else!)
					$thumb = $v->GrabSingleFrame(5); //5 seconds into the video

					$videodata[$i]['preview']  = Path::Get('rel:admin/temp').'/'.$thumb['url'];
					$videodata[$i]['filepath'] = $this->folder .'/'. $videoArray[$i];
					$videodata[$i]['filename'] = $videoArray[$i];


//var_dump( $videodata[$i] );


					self::$progressBar->update( $i, array('details' => $videodata[$i]) ); //data.text.details

				}
				else
				{
					//videodata empty
					continue;
				}

			} //for

			/***database connect***/
			$db = DB::getInstance();

			//to finish update later, save the thumbnail folder
			$db->Update("INSERT INTO `bx_temp`
					(`key`, `value`)
					VALUES
					('outputfolder',?)",
					array(
						$outputfolder,
					)
			);

			//end
			self::$progressBar->finish();

		}//try framegrabbing
		catch(FrameGrabException $e)
		{
			throw new VIDProcessorException( $e->getMessage() );
		}


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

} //class VideoProcessor