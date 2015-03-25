<?php

/* **************************************************************
 *  File: Stats.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Stats Class
*
*/
class Stats
{
	private $priordbconnection = true;

/**
* Constructor
*
* @access	public
* @param	object	$tpl
*/
	public function __construct (&$tpl){

		if(defined('__showheaderstats') && __showheaderstats === true){

			/***caching?***/
			if(__headerstatscache) {

				//with caching
				$headercache = Caching::Setup('element', true, __headerstatscache); //with serialization

				if(!$stats = $headercache->load('headerstats')) {

					$stats = $this->HeaderStats();
					$headercache->save($stats, 'headerstats', array('stats','header'));

//					Zend_Registry::set('headercache', $headercache);
				}
			}
			//without caching
			else
			{
				$stats = $this->HeaderStats();
			}

			if(isset($tpl))
				$tpl->assign('_stats', $stats);

//				$tpl->apply($stats);
//			else
//				throw new Exception('Error: Stats need to be called after template initialisation.');

			unset($stats);

		} //if __showheaderstats

	} //construct

/**
* Header Statistics
*
* @access	private
* @return	array
*/
	private function HeaderStats()
	{
				/***database connect***/
				$db = DB::getInstance();

				$stats = array();

				$types = Config::Get('types');

				if($types)
				foreach($types as $key => $val)
				{
					$stats[$key]['num'] = $db->Column("SELECT count(*) FROM `bx_content` WHERE `type`=?", array($key));
				}

				//special pic stats
				if(isset($stats['set']))
				{
					$stats['set']['size'] = $db->Column("SELECT SUM(`size`) FROM `bx_picture` WHERE `theme`=? GROUP BY `content_id`", array(Template::$theme));
				}

				//special video stats
				if(isset($stats['video']))
				{
					//we use the average size of the update videos for the stats
					$stats['video']['size'] = $db->Column("SELECT AVG(`size`) FROM `bx_video` GROUP BY `content_id`");
				}

				//total updates calculations
				if(!empty($stats))
				{
					$stats['total']['size'] = 0;
					$stats['total']['num'] = 0;
					foreach($stats as $st)
					{
						if(isset($st['num']))
						$stats['total']['num'] += $st['num'];

						if(isset($st['size']))
						$stats['total']['size'] += $st['size'];
					}
				}
/*
//OLD
				$stats['total_video_size'] = $arr[1];
				$stats['total_size'] = $this->readablefilesize($stats['total_pics'][1] + $stats['total_video_size']);
*/

				return $stats;
	} //HeaderStats

/**
* Video Statistics
*
* @access	private
* @return	array
*/
	private function video_stats(){
		//query the videos and
		//get size of each video

		$db = DB::getInstance();

		$file = '';
		$arr = array(0,0);

		$vids = $db->FetchAll("SELECT `path` FROM `bx_content` WHERE `type`=?", array('video'));

		$arr[0] = count($vids);

		if($arr[0])
			foreach ($vids as $v){

			//check the video files and get their size


//an neue Datenbankstruktur anpassen!!!

//-> Pfade!@






				$file = Path::Get('rel:member/videos') . Input::sanitize_str($v['sceneshortname']) . DIR_SEP . intval($v['id']) . DIR_SEP . Input::sanitize_str($v['path']);
				if (file_exists($file)){
					$arr[1] += filesize($file);
				}
			}

		//$arr[0] = video update count
		//$arr[1] = total size of video updates
		return $arr;
	}

/**
* Count Files in Folder
*
* @access	private
* @param	string	$dir_path
*/
	private function count_files_in_folder ($dir_path)
	{
		$this->count = count(glob($dir_path . "*"));
	}

/**
* Get Folder Count
*
* @access	public
* @param	string	$dir_path
* @return	integer
*/
	public function get_folder_count($dir_path)
	{
		if(!empty($dir_path)) $this->count_files_in_folder($dir_path);
		return $this->count;
	}

/**
* Get Readable File Size
*
* @access	public
* @param	string	$filename
* @return	mixed	string | bool
*/
	public static function fsize($filename) {
		if (is_file($filename)){
			return String::readablefilesize(filesize($filename));;
		}
		else{
			return false;
		}
	}

/**
* Return the count and filesize for files in a directory
*
* @access	private
* @param 	string $dir
* @param 	bool	$recursive
* @param 	string $searchext
* @return	mixed	array | false
*
*/
	private function dir_count($dir, $recursive = false, $searchext = false) {
	    $c = 0;
	    $s = 0;
	    if(substr($dir,-1)==".") $dir = substr($dir,0,-1);
	    if(!is_dir($dir)) {
	        trigger_error("Not a directory: {$dir}.");
	        return false;
	    }
	    if(!$d = opendir($dir)) {
	        trigger_error("Unable to open directory: {$dir}");
	        return false;
	    }
	    while(($i = readdir($d)) !== false) {
	        if(($i == '.') || ($i == '..')) continue;

	        $temp = explode(".",$i);
			if (!empty($temp))
	        	$e = array_pop($temp);

	        $p = $dir."/".$i;
	        if(is_file($p)) {
	            if(($searchext !== false) && ($e != $searchext)) continue;
	            $c++;
	            $s += filesize($p);
	        } elseif(is_dir($p) && $recursive) {
	            $r = $this->dir_count($p, $recursive, $searchext);
	            if ($r[0] > 0) $c += $r[0];
	            if ($r[1] > 1) $s += $r[1];
	        }
	    }
	    closedir($d);
	    return array($c,$s);
	}

/**
* Get Dir Count
*
* @access	public
* @param	string	$dir
* @param	bool	$recursive
* @param	bool	$searchext
* @return
*/
	public function getDirCount($dir, $recursive = false, $searchext = false)
	{
		return $this->dir_count($dir, $recursive = false, $searchext = false);
	}

/**
* Recursive Directory Size
*
* @access	private
* @param	string	$directory
* @param	bool	$format
* @return	string
*/
	private function recursive_directory_size($directory, $format = true)
	{
	// recursive_directory_size( directory, human readable format )
	// the function returns the filesize in bytes, KB and MB

	// to use this function to get the filesize in bytes, write:
	// recursive_directory_size('path/to/directory/to/count');

	// to use this function to get the size in a nice format, write:
	// recursive_directory_size('path/to/directory/to/count',TRUE);

		$size = 0;

		// if the path has a slash at the end we remove it here
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}

		// if the path is not valid or is not a directory ...
		if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
		{
			// ... we return -1 and exit the function
			return -1;
		}
		// we open the directory
		if($handle = opendir($directory))
		{
			// and scan through the items inside
			while(($file = readdir($handle)) !== false)
			{
				// we build the new path
				$path = $directory.DIR_SEP.$file;

				// if the filepointer is not the current directory
				// or the parent directory
				if($file != '.' && $file != '..')
				{
					// if the new path is a file
					if(is_file($path))
					{
						// we add the filesize to the total size
						$size += filesize($path);

					// if the new path is a directory
					}elseif(is_dir($path))
					{
						// we call this function with the new path
						$handlesize = recursive_directory_size($path);

						// if the function returns more than zero
						if($handlesize >= 0)
						{
							// we add the result to the total size
							$size += $handlesize;

						// else we return -1 and exit the function
						}else{
							return -1;
						}
					}
				}
			}
			// close the directory
			closedir($handle);
		}
		// if the format is set to human readable
		if($format == TRUE)
		{
			// if the total size is bigger than 1 MB
			if($size / 1048576 > 1)
			{
				return round($size / 1048576, 1).' MB';

			// if the total size is bigger than 1 KB
			}elseif($size / 1024 > 1)
			{
				return round($size / 1024, 1).' KB';

			// else return the filesize in bytes
			}else{
				return round($size, 1).' bytes';
			}
		}else{
			// return the total filesize in bytes
			return $size;
		}
	}

/**
* Directory Listing
*
* @access	public
* @param	string	$directory
* @return	mixed
*/
	public static function dirList ($directory)
	{
		// create an array to hold directory list
		$results = array();

		if(is_dir($directory)){

			// create a handler for the directory
			$handler = opendir($directory);

			// keep going until all files in directory have been read
			while ($file = readdir($handler))
			{

				// if $file isnt this directory or its parent,
				// add it to the results array
				if ($file != '.' && $file != '..')
					$results[] = $file;
		    }

			// tidy up: close the handler
			closedir($handler);

		// done!
		return $results;

		}
		else{
			//no directory found!
			return false;
		}

	}



/*
//funktioniert, aber zu rechenintensiv
$total_pics = dir_count( Path::Get('rel:member/pics') , true );
$total_videos = dir_count( Path::Get('rel:member/videos') , true );
$total_size = readablefilesize($total_pics[1] + $total_videos[1]);


$tpl->assign("total_size", $total_size);
$tpl->assign("total_pics", $total_pics[0]);
$tpl->assign("total_videos", $total_videos[0]);
*/

} //class
