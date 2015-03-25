<?php

/* **************************************************************
 *  File: String.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* String Class
*
*/
class String
{
    const NEWLINE_WINDOWS = "\r\n";
    const NEWLINE_UNIX = "\n";
    const NEWLINE_MAC = "\r";

/**
* Function to Add or Remove Trailing and Preceding Slashes ("/")
*
* @access	public
* @param	string	$string
* @param	bool	$preceding: 0: Remove Slash, 1: Add Slash
* @param	bool	$trailing: 0: Remove Slash, 1: Add Slash
* @return	string
*/
	public static function Slash($string, $preceding = false, $trailing = false) {
		if($trailing)
		{
			//check if string has no trailing slash
			if(substr($string, -1) != '/')
			{
				$string = $string . '/';
			}
		}
		else
			$string = rtrim($string, '/');

		if($preceding)
		{
			//check if string has no preceding slash
			if(substr($string, 0, 1) != '/')
				$string = '/' . $string;
		}
		else
			$string = ltrim($string, '/');

		return $string;
	}
/**
* Remove Slashes
*
* @access	public
* @param	string	$string
* @return	mixed	array or string
*/
	public static function RemoveSlashes($string)
	{
		return is_array($string)?array_map(array('String', 'RemoveSlashes'), $string): stripslashes($string);
	}

/**
* Right Trim Line Breaks
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function rTrimBr($string) {
		return preg_replace(array("~[<br>]+$~i","~[<br\s/>]+$~i","~[<br/>]+$~i"), '', $string);
	}

/**
* Convert Dateline Integer to Date
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function convert_dateline($string, $format='') {
		if(!is_numeric($string))
			$ret = null;
		else
		{
			if(empty($format))
				$format = Config::Get('datetime_string');

			if($string == 0)
				$ret = null;
			elseif( !empty($format) )
				$ret = date($format, $string);
			else
				$ret = date($string);
		}

		return $ret;

	} //convert_dateline

/**
* Convert date to integer
*
* @access	public
* @param	string	$date
* @return	int
*/
    public static function date_to_dateline($date) {

	return strtotime($date);
    }

/**
* Strip Slashes
*
* @access	public
* @param	string	$value
* @return	string
*/
    public static function stripslashes_deep($value) {
        if (is_array($value)) {
            if (count($value)>0) {
                $return = array_combine(array_map(array('String','stripslashes_deep'), array_keys($value)),array_map(array('String','stripslashes_deep'), array_values($value)));
            } else {
                $return = array_map(array('String','stripslashes_deep'), $value);
            }
            return $return;
        } else {
            $return = stripslashes($value);
            return $return ;
        }
    }

/**
* Is Valid IP Address?
*
* @access	public
* @param	string	$ip
* @return	bool
*/
	public static function IsIPAddress($ip){
	    if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)){
	        return true;
	    }
	    return false;

	} //IsIPAdress

/**
* Convert IP Address
*
* @access	public
* @param	string	$ip
* @return	string
*/
	public static function ConvertIP($ip){
	    if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)){
	        return $ip;
	    }
	    else
	    {
			$arr = explode(".", $ip);
			$arr = array_map(create_function('$value', 'return (int)$value;'), $arr);
			$arr = implode(".", $arr);

		    return $arr;
	    }

	} //IsIPAdress

/**
* Replace New Line Characters
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function ReplaceNewLine($string)
	{
		//replace "\n" with line breaks
		//to treat string like a double-quotation string
		return str_replace('\n', chr(10), $string);

	} //ReplaceNewLine

/**
* Encode Line Breaks
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function LineBreakEncode($string)
	{
		//convert line breaks to PHP_EOL
        return preg_replace('~\r\n|\r|\n~', PHP_EOL, $string);

	} //LineBreakEncode

/**
* Decode Line Breaks
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function LineBreakDecode($string)
	{
        return preg_replace('~\n~', chr(20), $string);

	} //LineBreakDecode

/**
* Remove Line Breaks
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function RemoveLineBreaks($string)
	{
        return preg_replace('~\r\n|\r|\n~', "", $string);

	} //RemoveLineBreaks
/**
* Replace Line Breaks
*
* @access	public
* @param	string	$string
* @param	string	$what
* @return	string
*/
	public static function ReplaceLineBreaks($string, $what)
	{
        return preg_replace('~\r\n|\r|\n~', $what, $string);

	} //ReplaceLineBreaks

/**
* Convert Seconds to H:M:S String
*
* @access	public
* @param	integer	$sec
* @param	bool	$padHours
* @param	bool	$nohour
* @return	string
*/
	public static function sec2hms ($sec, $padHours = false, $nohour = false)
	{
	//$padhours adds leading zero
	//$nohour: no leading hour

		// holds formatted string
		$hms = "";
		$ms = "";

		// there are 3600 seconds in an hour, so if we
		// divide total seconds by 3600 and throw away
		// the remainder, we've got the number of hours
		$hours = intval(intval($sec) / 3600);

		// add to $hms, with a leading 0 if asked for
		$hms .= ($padHours)
			? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
			: $hours. ':';

		// dividing the total seconds by 60 will give us
		// the number of minutes, but we're interested in
		// minutes past the hour: to get that, we need to
		// divide by 60 again and keep the remainder
		$minutes = intval(($sec / 60) % 60);

		// then add to $hms (with a leading 0 if needed)
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
		$ms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

		// seconds are simple - just divide the total
		// seconds by 60 and keep the remainder
		$seconds = intval($sec % 60);

		// add to $hms, again with a leading 0 if needed
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
		$ms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		// done!
		if ($nohour)
			{return $ms;}
		else
			{return $hms;}

	}
/**
* Convert H:M:S String to Seconds
*
* @access	public
* @param	string	$hms
* @return	integer
*/
	public static function hms2sec ($hms) {
		if(!$hms){
			return 0;
		}
		else{
			//convert hh:mm:ss to seconds
			list($h, $m, $s) = explode (":", $hms);
			$seconds = 0;
			$seconds += (intval($h) * 3600);
			$seconds += (intval($m) * 60);
			$seconds += (intval($s));
			return $seconds;
		}
	}

/**
* Trim Text
*
* @access	public
* @param	string	$input
* @param	integer	$length
* @param	bool	$dots
* @param	bool	$strip_html
* @return	string
*/
	public static function TrimText($input, $length, $dots = true, $strip_html = true) {
		//strip tags, if desired
		if ($strip_html) {
			$input = strip_tags($input);
		}

		//no need to trim, already shorter than trim length
		if (strlen($input) <= $length) {
			return $input;
		}

		//find last space within length
		$last_space = strrpos(substr($input, 0, $length), ' ');
		$trimmed_text = substr($input, 0, $last_space);

		//add dots (...)
		if ($dots) {
			$trimmed_text .= '...';
		}

		return $trimmed_text;

	} //TrimText

/**
* Sanitizes a filename
*
* Removes special characters in filenames
*
* @access	public
* @param	string	$filename
* @return	string
*/
	public static function sanitize_file_name($filename) {

		$filename = strval($filename);

		//german umlauts
		$filename = str_replace(array("Ä","Ö","Ü","ä","ö","ü"), array("AE","OE","UE","ae","oe","ue"), $filename);

		//replace slashes with "-"
		$filename = str_replace("/", "-", $filename);

		//remove trailing and leading dots and white space
		$filename = trim($filename, ". ");

		//replace space with "-"
		$filename = str_replace(" ", "-", $filename);

		/*whitelist method*/
		//all unwanted characters are discarded

		$matches = array();
		preg_match_all("~[a-zA-Z0-9_\+\.\-]*~", $filename, $matches);

		$filename = '';
		foreach($matches as $m) $filename .= implode("", $m);

		//trim
		$filename = trim($filename, '.-_');

		if(empty($filename)) return '';

		// Split the filename into base and extension
		$parts = explode('.', $filename);

		// if only one extension, return it
		if (count($parts) <= 2)
			return $filename;

/*
		// multiple extensions
		$filename = array_shift($parts);
		$extension = array_pop($parts);
		$mimes = Config::Get('image_extensions');

		for($i=0, $s = count($mimes); $i <$s; $i++)
		{
			//check for invalid input from config.php
			$mimes[$i] = str_replace(array("*.","*","."),"",trim($mimes[$i]));
		}

		// Loop over any intermediate extensions.  Munge them with a trailing underscore if they are a 2 - 5 character
		// long alpha string not in the extension whitelist.
		foreach ( (array) $parts as $part) {
			$filename .= '.' . $part;

			if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
				$allowed = false;
				foreach ( $mimes as $ext_preg => $mime_match ) {
					$ext_preg = '!(^' . $ext_preg . ')$!i';
					if ( preg_match( $ext_preg, $part ) ) {
						$allowed = true;
						break;
					}
				}
				if ( !$allowed )
					$filename .= '_';
			}
		}
		$filename .= '.' . $extension;
*/

		return $filename;
	}

/**
* Sanitizes a path
*
* Removes any illegal characters that should not be in a UNIX or WINDOWS path
*
* @access	public
* @param	string	$path
* @return	string
*/
	public static function sanitize_path($path) {

		$path = strval($path);

		//german umlauts
		$path = str_replace(array("Ä","Ö","Ü","ä","ö","ü"), array("AE","OE","UE","ae","oe","ue"), $path);

		/*whitelist method*/
		//all unwanted characters are discarded

		$matches = array();
		preg_match_all("~[a-zA-Z0-9:\\_+\./-]*~", $path, $matches);

		$path = '';
		foreach($matches as $m) $path .= implode("",$m);

		//trim
		$path = trim($path, '.-_');

		return $path;
	}

/**
* Check path
*
* Used to check if a path contains illegal characters or not.
*
* @access	public
* @param	string	$path
* @return	bool
*/
	public static function path_chars_valid($path) {

		$valid = false;

		$s = strlen(strval($path));

		//check each character
		for($i=0;$i<$s;$i++)
		{
			if(preg_match("~^[a-zA-Z\s0-9\":\\_+\./-]$~", $path[$i])) //"
			{
				//no illegal characters
				$valid = true;
			}
			else
			{
				//path contains illegal characters
				$valid = false;
				break;
			}
		}

		return $valid;
	}

/**
* Get File Extension from String
*
* @access	public
* @param	string	$string
* @return	string
*/
	public static function GetFileExtension($string) {
		return substr($string, strrpos($string, '.') + 1);
	}

/**
* Escape Quotes
*
* @access	public
* @param	string	$input
* @return	string
*/
	public static function escape_quotes($input) {
		return addcslashes($input, "'\""); //"
	}

/**
* Convert Dateline Integer to Date
*
* @access	public
* @param	string	$bytes
* @return	string
*/
	public static function ConvertDatelineToDate($string)
	{
		return date(Config::Get('date_string'), $string);
	}

/**
* Readable Filesize
*
* @access	public
* @param	string	$bytes
* @param	integer	$precision
* @return	string
*/
	public static function readablefilesize($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

/**
* Human Number Formatting
*
* @access	public
* @param	string	$n
* @param	integer	$precision	Number of zeros to add
* @return	string
*/
	public static function HumanNumberFormat($n, $precision = 3) {
        // first strip any formatting;
        $n = (0+str_replace(array(",", "."), "", $n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it
        if($n>1000000000000) return round(($n/1000000000000), $precision).' trillion';
        else if($n>1000000000) return round(($n/1000000000), $precision).' billion';
        else if($n>1000000) return round(($n/1000000), $precision).'m';
        else if($n>1000) return round(($n/1000), $precision).'k';

        return number_format($n);
	}

/**
* Convert to Bool
*
* @access	public
* @param	string	$in
* @return	bool
*/
	public static function boolval($in)
	{

		if(in_array(strtolower($in), array('false', 'no', 'off', false, null), true))
			$out = false;
		elseif (in_array(strtolower($in), array('true', 'yes', 'on', true), true))
			$out = true;
		else //leave it untouched
			$out = $in;

		return $out;
	}

/**
* String Padding
*
* @access	public
* @param	string	$input
* @param	integer	$num	Number of zeros to add
* @return	string
*/
	public static function strPad($input, $num = 3)
	{
		$num = intval($num);
		return str_pad($input, $num, "0", STR_PAD_LEFT);
	}

/**
* Strip Characters
*
* @access	public
* @param	string	$str
* @return	string
*/
	public static function stripChars($str)
	{
		//replace new line characters and tabs with " "
		//also remove multiple " "
		return preg_replace('~\r\n|\r|\n|\t|[\s]+~', " ", $str);
	}

/**
* Is Valid Email?
*
* @access	public
* @param	string	$str
* @return	bool
*/
	public static function IsEmail($str)
	{
		if(1 == preg_match("~^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$~", $str))
			return true;
		else
			return false;

//		return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;

	} //CheckEmail
/**
*	Is String Serialized?
*
*	If $data is not an string, then returned value will always be false.
*	Serialized data is always a string.
*
*	@param	mixed	$data	Value to check to see if was serialized.
*	@return	bool 			FALSE if not serialized, TRUE if it was.
*/
	public static function is_serialized($data) {
		// if it isn't a string, it isn't serialized
		if ( !is_string($data) )
			return false;

		$data = trim($data);
			if ( 'N;' == $data )
				return true;
			if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
				return false;
			switch ($badions[1]) {
				case 'a' :
				case 'O' :
				case 's' :
					if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
						return true;
					break;
				case 'b' :
				case 'i' :
				case 'd' :
					if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
						return true;
					break;
		}
		return false;
	}


/**
* Underscore
*
* Takes multiple words separated by spaces and underscores them
*
* @access	public
* @param	string	$str
* @return	string
*/
	public static function underscore($str)
	{
		return preg_replace('/[\s]+/', '_', strtolower(trim($str)));
	} //underscore

/**
* Humanize Underscores
*
* Takes multiple words separated by underscores and changes them to spaces
*
* @access	public
* @param	string	$str
* @return	string
*/
	public static function humanize_underscores($str)
	{
		return preg_replace('/[_]+/', ' ', strtolower(trim($str)));
	} //humanize

/**
* Reduce Multiples
*
* Reduces multiple instances of a particular character.  Example:
* Fred, Bill,, Joe, Jimmy
* becomes:
* Fred, Bill, Joe, Jimmy
*
* @access	public
* @param	string
* @param	string	$str		the character you wish to reduce
* @param	bool	$character	TRUE/FALSE - whether to trim the character from the beginning/end
* @return	string
*/
	public static function reduce_multiples($str, $character = ',', $trim = FALSE)
	{
		$str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);

		if ($trim === TRUE)
		{
			$str = trim($str, $character);
		}

		return $str;

	} //reduce_multiples

/**
* Convert String to XML
* Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
*
* @param array $data
* @param string $rootNodeName - what you want the root node to be - defaultsto data.
* @param SimpleXMLElement $xml - should only be used recursively
* @return string XML
*/
	public static function toXml($data, $rootNodeName = 'data', $numericNodeName = 'unknownNode_', $xml=null)
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			@ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($xml == null)
		{
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />".PHP_EOL);
		}

		// loop through the data passed in.
		foreach($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = $numericNodeName . (string) $key;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value))
			{
				$node = $xml->addChild($key);
				// recursive call
				String::toXml($value, $rootNodeName, $numericNodeName, $node);
			}
			else
			{
				// add single node.
                                $value = htmlentities($value);
				$xml->addChild($key, $value);
			}

		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();

	} //toXML

} //class