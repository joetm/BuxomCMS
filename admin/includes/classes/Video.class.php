<?php

/* **************************************************************
 *  File: Video.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

class FrameGrabException extends Exception {}

/**
* Video Class
*
*/
class Video {

	const SKIP = '3'; //number of seconds to skip ahead in the video

	private $filename = false;
	private $info = null;

	private $cache = null;

	private $mplayer = false;
	private $ffmpeg  = false;
	private $yamdi  = false;

	private $dir = null; //folder in admin temp directory
	private $outputfolder = null; //full path to above folder

	//options
	private static $o = array();

	private static $debug;

/**
* Constructor
*
* @access	public
* @param	string	$video
*/
	public function __construct($video)
	{
		$this->filename = $video;

		$this->setLibrary();
	}

/**
* Set the video processing application
*
* @access	public
*/
	public function setLibrary()
	{
		$mplayer = @Config::Get('_mplayerpath');		//path to mplayer executable
		if (@$mplayer && is_executable($mplayer))
			$this->mplayer	= $mplayer;

		//ffmpeg is only used as a fall-back to get the video info.
		//it should not be used to extract screenshots (it will take forever!)
		$ffmpeg = @Config::Get('_ffmpegpath');			//path to ffmpeg executable
		if (@$ffmpeg && is_executable($ffmpeg))
			$this->ffmpeg	= $ffmpeg;

		$yamdi = @Config::Get('_yamdipath');			//path to yamdi executable
		if (@$yamdi && is_executable($yamdi))
			$this->yamdi	= $yamdi;
	}
/**
* Set the video processing application
*
* @access	public
*/
	public function getLibrary($library = null)
	{
		if(empty($library))
		{
			if($this->mplayer && is_executable($this->mplayer))
				return $this->mplayer;
			elseif($this->ffmpeg && is_executable($this->ffmpeg))
				return $this->ffmpeg;
		}
		else //return specific library
		{
			if(!is_array($library))
				if($this->{$library} && is_executable($this->{$library}))
					return $this->{$library};
			else
			{
				$stack = array();
				foreach($library as $lib)
				{
					if($this->{$lib} && is_executable($this->{$lib}))
					$stack[$lib] = $this->{$lib};
				}
				return $stack;
			}
		}
	}

/**
* Get Video Info
*
* @access	public
* @return	array
*/
	public function GetInfo()
	{
		if(!file_exists($this->filename))
			throw new FrameGrabException('No video found');

		$data = array();
		$matches = array();
		$raw = null;

		$ext = strtoupper(String::GetFileExtension($this->filename));


		/*********/
		/* yamdi */
		/*********/
		//inject metadata with yamdi
		//yamdi can only be used on FLV files
		if($this->yamdi && 'FLV' === $ext)
		{
			//input and output may not be the same file
			$tempfile = $this->filename.".temp";

			//inject metadata (quick and easy)
			$raw = shell_exec($this->yamdi.
			' -i '.$this->filename.							//input file
			' -x '.$this->filename.'.xml'.					//xml info file
			' -c '.Config::GetDBOptions('shortname').	//creator tag with your site info
			' -l '.											//onLastSecond event
			' -o '.$tempfile);								//output file

			//check if temp file was created
			if(file_exists($tempfile))
			{
				//create backup
				@Filehandler::MoveFile($this->filename, $this->filename."2");
				//try to move temp file
				if(@Filehandler::MoveFile($tempfile, $this->filename))
				{
					//move successful. delete temp file
					@Filehandler::DeleteFile($this->filename."2");
				}
				else
				{
					//move unsuccessful
					//delete temp file and move backup back
					@Filehandler::DeleteFile($tempfile);
					@Filehandler::MoveFile($this->filename."2", $this->filename);
				}
			}

		} //yamdi metadata injection
		if(file_exists($this->filename.'.xml') && 'FLV' == $ext && function_exists('simplexml_load_file'))
		{
			//yamdi successfully created an XML file with the video details
			//we get the video info from this file instead of mplayer/ffmpeg

			$xml = simplexml_load_file($this->filename.'.xml');

				//yamdi does not provide the mime type
				//we take it from the file's extension
				$data['video_format'] = $ext; //or "video/x-flv"

				$data['length'] = intval($xml->flv->duration);
				$data['video_fps'] = floatval($xml->flv->framerate);
				$data['video_frames'] = intval($data['video_fps'] * $xml->flv->lasttimestamp);

				//yamdi output is in kbps
				$data['audio_bitrate'] = intval($xml->flv->audiodatarate) * 1000;
				$data['video_bitrate'] = intval($xml->flv->videodatarate) * 1000;

				$data['bitrate'] = $data['video_bitrate'] + $data['audio_bitrate'];

				$data['video_width'] = intval($xml->flv->width);
				$data['video_height'] = intval($xml->flv->height);

			unset($xml);

			//delete the xml file
			@Filehandler::DeleteFile($this->filename.'.xml');

		}
		/***********/
		/* mplayer */
		/***********/
		elseif($this->mplayer) //no yamdi info file found. Use mplayer to extract the video info
		{

			//get video information
			$raw = shell_exec( $this->mplayer.
			' -identify'.							//output information
			' -really-quiet'.						//short output
			' '.escapeshellarg($this->filename).
			' -vo null'.							//no video output
			' -ao null'.							//no audio output
			' -noidx'.
			' -frames 0'.							//do not play/convert
			' 2>&1');

			if(preg_match_all('~^ID_([A-Z0-9_]+)=(.*)~m', $raw, $matches, PREG_SET_ORDER))
			{
				foreach($matches as $m)
					$data[strtolower($m[1])] = $m[2];

				if(!isset($data['video_format']))
					throw new FrameGrabException('Video invalid');

				$data['bitrate'] = $data['video_bitrate'] + $data['audio_bitrate'];

				$data['video_frames'] = floor($data['video_fps'] * $data['length']);

				$data['length'] = floor($data['length']);

			} //if preg_match

		}
		/**********/
		/* ffmpeg */
		/**********/
		elseif($this->ffmpeg) //mplayer not found. Last option is ffmpeg.
		{

			$raw = shell_exec( $this->ffmpeg.
			' -i '.
			' '.escapeshellarg($this->filename).
			' 2>&1');

			//Video: h264,
			if( preg_match('~Video: ([\da-zA-Z]+)~', $raw, $matches) )
				$data['video_format'] = $matches[1];
			else $data['video_format'] = null;

				if(!isset($data['video_format']))
					throw new FrameGrabException('Video invalid');

			//Duration: 00:29:11.41,
			if( preg_match('~Duration: (\d+:\d+:\d+)~', $raw, $matches) )
				$data['length'] = String::hms2sec($matches[1]);
			else $data['length'] = null;

			//bitrate: N/A
			if( preg_match('~bitrate: ([0-9.])~', $raw, $matches) )
				$data['bitrate'] = $matches[1];
			else $data['bitrate'] = null;

			//30 tbr,
			if( preg_match('~([0-9.]+) tbr~', $raw, $matches) )
				$data['video_fps'] = $matches[1];
			else $data['video_fps'] = null;

			$data['frames'] = floor($data['video_fps'] * $data['length']);

			//, 512x384
			if( preg_match('~, ([0-9]+)x([0-9]+) ~', $raw, $matches) )
			{
				$data['video_width'] = $matches[1];
				$data['video_height'] = $matches[2];
			}
			else
			{
				$data['video_width'] = null;
				$data['video_height'] = null;
			}

		}
		/*--------------------------------*/
		/* yamdi/mplayer/ffmpeg not found */
		/*--------------------------------*/
		else
		{

			$data = null;

//			throw new FrameGrabException('YAMDI or MPLAYER or FFMPEG not specified');








		}

		if(!is_null($data))
		{
			//create readable duration
			$data['duration'] = String::sec2hms($data['length']);

			//create readable bitrate
			$data['human_bitrate'] = String::HumanNumberFormat($data['bitrate']);
		}

		//cache
		if(!$this->cache)
			$this->cache = $data;

		return $data;

	} //GetInfo

/**
* Set Output Directory
*
* @access	public
* @param	string	$dir
*/
	public function SetOutputDir($dir)
	{
		$this->dir = String::slash($dir, 0, 0);

		if(!is_dir($this->dir))
			@Filehandler::MkDir(Path::Get('path:admin/temp') . DIR_SEP . $dir, true);

		$this->outputfolder = Path::Get('path:admin/temp') . DIR_SEP . $this->dir;

	} //SetOutputDir

/**
* Grab Single Frame
*
* @access	public
* @param	integer	$pos
* @return	mixed	array | null
*/
	public function GrabSingleFrame($pos)
	{
		if(!$this->filename)
			return false;

		if(!$this->outputfolder || !is_writeable($this->outputfolder))
			return;

		$pos = intval($pos);

		if(!isset(self::$o['thumbnailquality']) || !isset(self::$o['videograb_thumbnailsize']))
			self::$o = Config::GetDBOptions(array(
				'thumbnailquality',
				'videograb_thumbnailsize',
				));
		if(!self::$o['thumbnailquality']) self::$o['thumbnailquality'] = 90;
		if(!self::$o['videograb_thumbnailsize'])
		{
			if($this->cache)
				self::$o['videograb_thumbnailsize'] = array(
					'width' => $this->cache['video_width'],
					'height'=>$this->cache['video_height']
				);
			else
				self::$o['videograb_thumbnailsize'] = array(
					'width' => 200,
					'height' => 127
				);
		}


		//fork
		if($this->mplayer)
		{

			$cmd = $this->mplayer
				.' -nosound'
				.' -really-quiet'
				.' -ss '.$pos
				.' -vo jpeg:quality='.intval(self::$o['thumbnailquality'])
				.':outdir='.
				(DIRECTORY_SEPARATOR == "\\"
					? str_replace('"', '\\"', escapeshellarg($this->outputfolder))
					: escapeshellarg($this->outputfolder)
				)
				.' -sws 9' //lanczos
				.' -frames 1'
				.' -vf '. escapeshellarg('scale='.self::$o['videograb_thumbnailsize']['width'] . ':' . self::$o['videograb_thumbnailsize']['height'])
				.' '.escapeshellarg($this->filename)
				.' 2>&1';

			$raw = shell_exec($cmd);

			//rename output file
			Filehandler::MoveFile($this->outputfolder. DIR_SEP .'00000001.jpg',
			$this->outputfolder. DIR_SEP  .$pos.'.jpg');

		}
		else
		{
			throw new FrameGrabException('mplayer not found');
		}

		return array('filename' => $pos.'.jpg',
				'url' => $this->dir.'/'.$pos.'.jpg');

	} //GrabSingleFrame

/**
* Grab Multiple Frames
*
* @access	public
* @return	mixed	array | null
*/
	public function GrabFrames()
	{
		if(!$this->filename)
			return false;

		if(!$this->outputfolder || !is_writeable($this->outputfolder))
			return;

		$images = array();

		self::$o = Config::GetDBOptions(array(
			'num_video_screencaps',
			'videograb_thumbnailsize',
			'thumbnailquality',
			));

		if(!self::$o['num_video_screencaps']) return null;

		if(!self::$o['thumbnailquality']) self::$o['thumbnailquality'] = 90;

		if(!self::$o['videograb_thumbnailsize'])
		{
			if($this->cache)
				self::$o['videograb_thumbnailsize'] = array(
					'width' => $this->cache['video_width'],
					'height' => $this->cache['video_height']
				);
			else
				self::$o['videograb_thumbnailsize'] = array(
					'width' => 200,
					'height' => 127
				);
		}

		//we skip first and last (3) seconds of the video
		$length = floor($this->cache['length'] - (2 * self::SKIP));

		$step = $length / self::$o['num_video_screencaps']; //in seconds
//		echo "step $step = length $length / num ".self::$o['num_video_screencaps']."<br>";


		$pos = $step; //start position

//		echo "STEP $step<br>";

		self::$debug = Config::Get('debug');


		//fork
		if($this->mplayer)
		{
			$i = 0;
			while($pos <= $this->cache['length'])
			{
				$thgrab = $this->GrabSingleFrame($pos);

				$images[] = $thgrab;

				if(self::$debug) Tools::debug_msg( $thgrab['filename'], false ); //no br

				//transmit the thumbnail data
				$data['id'] = $i;
				$data['filename'] = $thgrab['filename'];
				$data['url'] = $thgrab['url'];
				//file size
				$data['width'] = self::$o['videograb_thumbnailsize']['width'];
				$data['height'] = self::$o['videograb_thumbnailsize']['height'];

				//update progressbar
				VideoProcessor::$progressBar->update( ($pos / $length), $data );

				$pos += $step; //next step
				$i++;

			} //while

			//finish progress bar
			VideoProcessor::$progressBar->finish();
		}
/*
		//ffmpeg framegrabbing disabled by default
		//ffmpeg will take 60 minutes for 20 pics,
		//while mplayer only takes 15 seconds!
		//it's your choice...
		elseif($this->ffmpeg)
		{
			while($pos <= $this->cache['length'])
			{
				$images[] = $this->GrabSingleFrame($pos);
				$pos += $step;
			} //while
		}
*/
		else
		{
			throw new FrameGrabException('mplayer not found');
		}


		return $images;

	} //GrabFrames

} //class