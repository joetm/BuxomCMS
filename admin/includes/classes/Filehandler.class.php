<?php

/* **************************************************************
 *  File: Filehandler.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Filehandler Class
*
*/
class Filehandler
{

	private static $error = null;

/**
* Get Error
*
* @access	public
* @return	string
*/
	public static function GetError()
	{
		return self::$error;
	}

/**
* Empty a Directory
*
* @access	public
* @param	string	$dir
* @param	bool	$deletesubdir
*/
	public static function EmptyDir($dir, $deletesubdir = false) {

		$dir = rtrim($dir, '/');

		if(!is_dir($dir)) return false;

		if(!$dh = @opendir($dir)) return;
		while (false !== ($obj = readdir($dh))) {
			if($obj=='.' || $obj=='..') continue;
			if (!@unlink("$dir/$obj")){
				self::EmptyDir("$dir/$obj");
				self::RemoveDir("$dir/$obj");
			}
		}
		closedir($dh);

	} //EmptyDir

/**
* Remove Directory
*
* @access	public
* @param	string	$dir
* @param	bool	$recursive
* @return	bool
*/
	public static function RemoveDir($dir, $recursive = true) {

		$dir = rtrim($dir, '/');

		if(!is_dir($dir)) return false;

	        if($recursive)
	        {
	            foreach(scandir($dir) as $item)
	            {
	                if($item == '.' || $item == '..') continue;

	                is_dir("$dir/$item") ? self::RemoveDir("$dir/$item", true) : unlink("$dir/$item");
	            }
	        }

	        return(rmdir($dir));

	} //RemoveDir

/**
* Is Empty Folder?
*
* @access	public
* @param	string	$dir
* @return	bool
*/
	public static function is_empty_folder($dir){

			if (($files = @scandir($dir)) && count($files) <= 2)
				return true;
			else
				return false;

	} //is_empty_folder
/**
* Move File
*
* @access	public
* @param	string	$dir
* @param	string	$destination
* @return	bool
*/
	public static function MoveFile($name, $destination)
	{
		if(@is_file($name))
		{
			if(@file_exists($destination))
			{
				self::$error = 'Cannot rename file: File with a specified name already exist.';
				return false;
			}
			else
			{
				if(@rename($name,$destination))
				{
					self::$error = null;
					return true;
				}
				else{
					self::$error = 'Cannot delete directory: Permission denied.';
					return false;
				}
			}
		}
		else
		{
			self::$error = 'No file to rename.';
			return false;
		}

	} //rename

/**
* Delete File
*
* @access	public
* @param	string	$fname
* @return	bool
*/
	public static function DeleteFile($fname)
	{
		$ret = false;

		if(@is_file($fname))
		{
			if(@file_exists($fname))
			{
				if(@unlink("$fname"))
				{
					self::$error = null;
					$ret = true;
				}
				else if(@exec("del $fname"))
				{
					self::$error = null;
					$ret = true;
				}
				else if(@system("del $fname"))
				{
					self::$error = null;
					$ret = true;
				}
				else {
					self::$error = 'Cannot delete File: Permission denied.';
				}
			}
			else {
				self::$error = 'File does not exists.';
			}
		}
		else
		{
			self::$error = 'File does not exists.';
		}

		return $ret;

	} //delete file

/**
* Move Directory
*
* @access	public
* @param	string	$name
* @param	string	$destination
* @return	bool
*/
	public static function MoveDir($name, $destination)
	{
		$ret = false;

		if(@is_file($name))
		{
			//exit
			self::$error = "Not a directory.";
			$ret = false;
		}
		else
		{
			if(@is_dir($destination))
			{
				self::$error = "Directory already exists.";
				$ret = false;
			}
			else
			{
				$ret = @rename($name, $destination);
			}
		}

		return $ret;

	} //rename

/**
* Make Directory
*
* @access	public
* @param	string	$path
* @param	integer	$perms
* @param	bool	$recursive
* @return	bool
*/
	public static function MkDir($path, $recursive=false, $perms = 0666)
	{
		$fperms = Config::Get('__folderPermission');
		if(!empty($fperms)) $perms = $fperms;

		$path = rtrim($path, '/');

		if(is_dir($path))
		{
			self::$error = "Directory already exists.";
			return true; //directory already exists
		}

		return mkdir($path, $perms, $recursive);
	}

/**
* Empty the Temporary Directory
*
* @access	public
* @return	bool
*/
	public static function EmptyTempDir()
	{
		//empty temp directory!
		if(is_dir(Path::Get('path:admin/temp')))
			if (!self::is_empty_folder(Path::Get('path:admin/temp')))
				return self::EmptyDir(Path::Get('path:admin/temp'));

		return false;
	}

/**
* Copy Function
*
* some hosts disable copy() function and say its for security
* function that do same as copy function effect
*
* @access	private
* @param	string	$file
* @param	string	$newfile
* @return	bool
*/
	private static function copyemz ($file, $newfile){

		$status = false;

		if(function_exists('copy'))
		{
			if(@copy($file, $newfile))
			{
				self::$error = null;
				$status = true;
			}
			else
			{
				self::$error = 'Cannot copy the file.';
				$status = false;
			}
		}
		else
		{
			$contentx = @file_get_contents($file);
				$openedfile = fopen($newfile, "w");
				fwrite($openedfile, $contentx);
				fclose($openedfile);
				if ($contentx === FALSE)
				{
					self::$error = 'Cannot copy the file.';
					$status = false;
				}
				else
				{
					$status = true;
					self::$error = null;
				}
		}
		return $status;

    } //copyemz

/**
* Copy file or folder from source to destination, it can do
* recursive copy as well and is very smart
* It recursively creates the dest file or directory path if there weren't exists
* Situtaions :
* - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
* - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
* - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest
* - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
* - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
* - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
* @todo
*     - Should have rollback technique so it can undo the copy when it wasn't successful
*  - Auto destination technique should be possible to turn off
*  - Supporting callback function
*  - May prevent some issues on shared enviroments : http://us3.php.net/umask
*
* @param $source	file or folder
* @param $dest		file or folder
* @param $options	folderPermission,filePermission
* @return boolean
*/
	public static function smartCopy($source, $dest, $options=array('folderPermission'=>0777, 'filePermission'=>0777))
	{
		$result = false;

		//get defaults from options
		$folderPermission = Config::Get('__folderPermission');
		if(!empty($folderPermission)) $options['folderPermission'] = $folderPermission;
		$filePermission = Config::Get('__filePermission');
		if(!empty($filePermission)) $options['filePermission'] = $filePermission;

		if(file_exists($source))
		{
			if($dest[strlen($dest)-1]=='/')
			{
				if(!file_exists($dest))
				{
					self::MkDir($dest, true, $options['folderPermission']);
				}
				$__dest = $dest."/".basename($source);
			} else {

				//check if directory of this new file exists?
				$dir = dirname($dest);

				if(!is_dir($dir))
					self::MkDir($dir, true, $options['folderPermission']);

				$__dest = $dest;
			}

			$result = self::copyemz($source, $__dest);

			if($result)
				chmod($__dest, $options['filePermission']);


		//copying of directories
		}
		elseif(is_dir($source))
		{
			if ($dest[strlen($dest)-1]=='/') {
				if ($source[strlen($source)-1]=='/') {
					//Copy only contents
				} else {
					//Change parent itself and its contents
					$dest=$dest.basename($source);
					@mkdir($dest);
					chmod($dest,$options['filePermission']);
				}
			} else {
				if ($source[strlen($source)-1]=='/') {
					//Copy parent directory with new name and all its content
					@mkdir($dest,$options['folderPermission']);
					chmod($dest,$options['filePermission']);
				} else {
					//Copy parent directory with new name and all its content
					@mkdir($dest,$options['folderPermission']);
					chmod($dest,$options['filePermission']);
				}
			}

			$dirHandle = opendir($source);
			while($file = readdir($dirHandle))
			{
				if($file!="." && $file!="..")
				{
					if(!is_dir($source."/".$file)) {
						$__dest=$dest."/".$file;
					} else {
						$__dest=$dest."/".$file;
					}
					//echo "$source/$file ||| $__dest<br>";
					$result = smartCopy($source."/".$file, $__dest, $options);
				}
			}
			closedir($dirHandle);

		}
		else
		{
			$result = false;
		}

		return $result;

	} //smartCopy

/*
	public static function directdownload()
	{
		$file = "'ftp://backup.csv'";

		$handle = fopen($file);
		while(!feof($handle)) {
		    echo fgets($handle, 2048);
		}
		fclose($handle);
	}
*/

/**
* Readfile (in chunks)
*
* @access	public
* @param	string	$filename
* @param	integer	$retnumbytes
* @return	bool || integer
*/
	public static function readfile_chunked($filename, $retnumbytes = true) {
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		// $handle = fopen($filename, 'rb');
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}

		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();
			flush();
			if ($retnumbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retnumbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}

/**
* getFileType
*
* @access	public
* @param	string	$string
* @return	string 	$type
*/
	public static function getFileType($string) {
		$type = strtolower(eregi_replace("^(.*)\.","", $string));
		if ($type == "jpg") $type = "jpeg";
		return $type;
	}

} //class Filehandler