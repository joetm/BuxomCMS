<?php

/* **************************************************************
 *  File: helpers.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
 * Force Download
 *
 * Generates headers that force a download to happen
 *
 * @access	public
 * @param	string	filename
 * @param	mixed	the data to be downloaded
 * @return	void
 */
if (!function_exists('force_download'))
{
	function force_download($filename = '', $data = '')
	{
		if ($filename == '' OR $data == '')
		{
			return FALSE;
		}

		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.'))
		{
			return FALSE;
		}

		// Grab the file extension
		$extension = String::GetFileExtension($filename);

		// Load the mime types
		@include(APPPATH.'config/mimes'.EXT);

		// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension]))
		{
			$mime = 'application/octet-stream'; //'
		}
		else
		{
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

		// Generate the server headers
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}
		else
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache'); //'
			header("Content-Length: ".strlen($data)); //'
		}

		exit($data);
	}
} //force_download

/**
 * Send an email
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('send_email'))
{
	function send_email($recipient, $subject = 'Test email', $message = 'Hello World')
	{
		return mail($recipient, $subject, $message);
	}
} //send_email

/**
 * Create a Directory Map
 *
 * Reads the specified directory and builds an array
 * representation of it.  Sub-folders contained with the
 * directory will be mapped as well.
 *
 * @access	public
 * @param	string	path to source
 * @param	bool	whether to limit the result to the top level only
 * @return	array
 */
if (!function_exists('directory_map'))
{
	function directory_map($source_dir, $top_level_only = FALSE, $hidden = FALSE)
	{
		if ($fp = @opendir($source_dir))
		{
			$source_dir = rtrim($source_dir, '/') . DIRECTORY_SEPARATOR;
			$filedata = array();

			while (FALSE !== ($file = readdir($fp)))
			{
				if (($hidden == FALSE && strncmp($file, '.', 1) == 0) OR ($file == '.' OR $file == '..'))
				{
					continue;
				}

				if ($top_level_only == FALSE && @is_dir($source_dir.$file))
				{
					$temp_array = array();

					$temp_array = directory_map($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, $hidden);

					$filedata[$file] = $temp_array;
				}
				else
				{
					$filedata[] = $file;
				}
			}

			closedir($fp);
			return $filedata;
		}
		else
		{
			return FALSE;
		}
	}
} //directory_map

/**
 *	Get Filenames
 *
 *	Reads the specified directory and builds an array containing the filenames.
 *	Any sub-folders contained within the specified path are read as well.
 *
 *	@access	public
 *	@param	string	path to source
 *	@param	bool	whether to include the path as part of the filename
 *	@param	bool	internal variable to determine recursion status - do not use in calls
 *	@return	array
 */
if ( ! function_exists('get_filenames'))
{
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), '/') . DIRECTORY_SEPARATOR;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
				{
					 get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0)
				{
					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
				}
			}
			return $_filedata;
		}
		else
		{
			return FALSE;
		}
	}
}

/**
* Sanitizes title or use fallback title.
*
* Specifically, HTML and PHP tags are stripped. Further actions can be added
* via the plugin API. If $title is empty and $fallback_title is set, the latter
* will be used.
*
* @since 1.0.0
*
* @param string $title The string to be sanitized.
* @param string $fallback_title Optional. A title to use if $title is empty.
* @return string The sanitized string.
*/
function sanitize_title($title, $fallback_title = '') {
	$raw_title = $title;
	$title = strip_tags($title);
	$title = apply_filters('sanitize_title', $title, $raw_title);

	if ( '' === $title || false === $title )
		$title = $fallback_title;

	return $title;
}

/**
* Sanitizes title, replacing whitespace with dashes.
*
* Limits the output to alphanumeric characters, underscore (_) and dash (-).
* Whitespace becomes a dash.
*
* @since 1.2.0
*
* @param string $title The title to be sanitized.
* @return string The sanitized title.
*/
function sanitize_title_with_dashes($title) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	$title = remove_accents($title);
	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = str_replace('.', '-', $title);
	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');

	return $title;
}

/**
* Converts email addresses characters to HTML entities to block spam bots.
*
* @since 0.71
*
* @param string $emailaddy Email address.
* @param int $mailto Optional. Range from 0 to 1. Used for encoding.
* @return string Converted email address.
*/
function antispambot($emailaddy, $mailto=0) {
	$emailNOSPAMaddy = '';
	srand ((float) microtime() * 1000000);
	for ($i = 0; $i < strlen($emailaddy); $i = $i + 1) {
		$j = floor(rand(0, 1+$mailto));
		if ($j==0) {
			$emailNOSPAMaddy .= '&#'.ord(substr($emailaddy,$i,1)).';';
		} elseif ($j==1) {
			$emailNOSPAMaddy .= substr($emailaddy,$i,1);
		} elseif ($j==2) {
			$emailNOSPAMaddy .= '%'.zeroise(dechex(ord(substr($emailaddy, $i, 1))), 2);
		}
	}
	$emailNOSPAMaddy = str_replace('@','&#64;',$emailNOSPAMaddy);
	return $emailNOSPAMaddy;
}





/**
* Fetches the 'scriptpath' variable - ie: the URI of the current page
*
* @return	string
*/
	function fetch_scriptpath()
	{
		if ($this->registry->scriptpath != '')
		{
			return $this->registry->scriptpath;
		}
		else
		{
			if ($_SERVER['REQUEST_URI'] OR $_ENV['REQUEST_URI'])
			{
				$scriptpath = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_ENV['REQUEST_URI'];
			}
			else
			{
				if ($_SERVER['PATH_INFO'] OR $_ENV['PATH_INFO'])
				{
					$scriptpath = $_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO'] : $_ENV['PATH_INFO'];
				}
				else if ($_SERVER['REDIRECT_URL'] OR $_ENV['REDIRECT_URL'])
				{
					$scriptpath = $_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_ENV['REDIRECT_URL'];
				}
				else
				{
					$scriptpath = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_ENV['PHP_SELF'];
				}

				if ($_SERVER['QUERY_STRING'] OR $_ENV['QUERY_STRING'])
				{
					$scriptpath .= '?' . ($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_ENV['QUERY_STRING']);
				}
			}

			// in the future we should set $registry->script here too
			$quest_pos = strpos($scriptpath, '?');
			if ($quest_pos !== false)
			{
				$script = urldecode(substr($scriptpath, 0, $quest_pos));
				$scriptpath = $script . substr($scriptpath, $quest_pos);
			}
			else
			{
				$scriptpath = urldecode($scriptpath);
			}

			// store a version that includes the sessionhash
			$this->registry->reloadurl = $this->xss_clean($scriptpath);

			$scriptpath = $this->strip_sessionhash($scriptpath);
			$scriptpath = $this->xss_clean($scriptpath);
			$this->registry->scriptpath = $scriptpath;

			return $scriptpath;
		}
	}


/**
* Strips out the s=gobbledygook& rubbish from URLs
*
* @param	string	The URL string from which to remove the session stuff
*
* @return	string
*/
	function strip_sessionhash($string)
	{
		$string = preg_replace('/(s|sessionhash)=[a-z0-9]{32}?&?/', '', $string);
		return $string;
	}
