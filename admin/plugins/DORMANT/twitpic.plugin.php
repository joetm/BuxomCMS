<?php

/* **************************************************************
 *  File: flickr.plugin.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*** include the init.php file ***/
require_once dirname(__FILE__).'/../../_init.php';

require_once 'Zend/Oauth.php';
require_once 'Zend/Oauth/Consumer.php';


//class name of plugin must be in the form of "<name>_plugin"
class flickr_plugin extends SocialPlugin {

	public $updateposting_possible = true;

	//add notes (description)
	const ADD_NOTES = true;
	//add tags
	const ADD_TAGS = true;
	//add location
	const ADD_LOCATION = true;
	//License ID from 1-7, although 7 is not used as of writing this
	//set to 0 (or false) to disable setting the license
	const LICENSE_ID = 1;


	public $infotext = 'Automatically posts the free images of a new update to flickr. You need to manually set the callback-url on flickr to http://yourdomain.com /admin/plugins/ flickr.plugin.php';

	public $auth_type = "rest";

	private $access_token = false;
	public $defaults = array();
	public $options = array();
	private static $configuration = array(
		'method' => 'POST',
		'rest_endpoint' => 'http://api.flickr.com/services/rest/',
		'upload_endpoint' => 'http://api.flickr.com/services/upload/',
	);


	//add images to flickr
	//executed when new update is successfully added
	public function NewUpdate($update)
	{
		if(!is_array($update) || empty($update['freeimages']))
			return false;

		//for flickr, we work with the freeimage array

		//get access token
		$this->options = parent::GetDetails('flickr_plugin');
		$this->access_token = $this->options['access_token'];

		//authorization check
		if($this->access_token && $this->access_token['stat'] == 'ok')
		{
			//reset, just in case
			$args = array();
			$photo_ids = array();

			if(empty($update['freeimages']))
			return false; //nothing to post

			//get the tags of the update
			$update['tags'] = false;
			if(!empty($update['id']))
			{
				/***database connect***/
				$db = DB::getInstance();

				$update['tags'] = $db->FetchAll("SELECT tag.tag
							FROM `bx_tag` AS `tag`
							JOIN `bx_tag_content` AS `tc` ON (tc.tag_id = tag.id)
							WHERE tc.content_id = ?", array($update['id']));

				if($update['tags']) $update['tags'] = implode(" ", $update['tags']);
				//flickr wants tag list separated by space
			}

			//add images to flickr
			//method:
			foreach($update['freeimages'] as $photo)
			{
				if(isset($update['type']) && $update['type'] == 'video')
					$content_type = 2; //screenshots
				else
					$content_type = 1; //image

				$is_public = 1; //image is public
				$hidden = 1; //image is showing in searches

				//add description to images
				$description = (self::ADD_NOTES && isset($update['description']) ? $update['description'] : null);

				//add tags to images
				$tags = (self::ADD_TAGS && isset($update['tags']) ? $update['tags'] : null);

				//safety level; deactivated by default
				$safety_level = null; //set this to 1..3 according to your site

				//upload
				$id = $this->AsyncUpload($photo, $update['title'], $description, $tags, $is_public, $safety_level, $content_type, $hidden);

				if($id)
				{
					$photo_ids[] = $id;

					//set license of image
					if(self::LICENSE_ID)
					{
						$this->Request('flickr.photos.licenses.setLicense', array(
							'api_key' => $this->options['key'],
							'photo_id' => $id,
							'license_id' => self::LICENSE_ID,
							));
					}
				}

			} //foreach freeimages


			//create photoset
			//(if more than one free image selected)
			if(count($photo_ids) > 1)
			{
				$args = array(
					'api_key' => $this->options['key'],
					'title' => $update['title'],
					'primary_photo_id' => $photo_ids[0], //use first photo as cover
					);

				if(self::ADD_NOTES)
					$args['description'] = $update['description'];

				$response = $this->Request('flickr.photosets.create', $args);

			} //create photoset


			//set location of photos
			//method: flickr.photos.geo.setLocation
			if(self::ADD_LOCATION)
			{
//todo



			}



//debug
var_dump($photo_ids);
die();

		} //stat == 'ok'

	} //NewUpdate



	public function Authorize()
	{
		self::GetConfig();

		//first time
		if(empty($_GET['frob']))
		{

			$signature = md5(self::$configuration['secret'].'api_key'.self::$configuration['key'].'permswrite');

			header("Location: http://flickr.com/services/auth/?api_key=".self::$configuration['key']."&perms=write&api_sig=".$signature);
			exit();
		}

		/*BREAK*/

		//this is the return visit
		//there is a frob

		//call flickr.auth.getToken

		$args = array(
			"api_key" => self::$configuration['key'],
			"frob" => $_GET['frob'],
			"method" => "flickr.auth.getToken",
			);


		$this->access_token = unserialize($this->Request('flickr.auth.getToken', $args));


		//save access_token
		if(!empty($this->access_token) && $this->access_token['stat'] == 'ok')
		{
			/***database connect***/
			$db = DB::getInstance();

			//we have a twitter access token
			//it only needs to be saved now
			$op = $db->Column("SELECT `value` FROM `bx_options` WHERE `key` = 'flickr_plugin'");
			//config found
			if($op)
			{
				$flickr_options = unserialize($op);

				$flickr_options['access_token'] = $this->access_token;

				$db->Update("UPDATE `bx_options` SET `value`=? WHERE `key`='flickr_plugin'", array(serialize($flickr_options)));
			}

		} //access_token


		header('Location: '.Path::Get('url:admin').'/options_social');
		exit();

	} //Authorize


	function AsyncUpload($photo, $title = null, $description = null, $tags = null, $is_public = null, $safety_level = null, $content_type = 1, $hidden = 1, $is_friend = null, $is_family = null) {

		if (!function_exists('curl_init'))
			die("Your server must support CURL in order to upload files to flickr.");

		//Process arguments, including method and login data.
		$args = array(
			"async" => 1,
			"api_key" => $this->options['key'],
			"title" => $title,
			"description" => $description,
			"tags" => $tags,
			"is_public" => $is_public,
			"is_friend" => $is_friend,
			"is_family" => $is_family,
			"safety_level" => $safety_level,
			"content_type" => $content_type,
			"hidden" => $hidden,
		);
		if (!empty($this->access_token))
		{
			$args = array_merge($args, array("auth_token" => $this->access_token));
		}

		ksort($args);
		$auth_sig = "";
		foreach ($args as $key => $data) {
			if ( is_null($data) ) {
				unset($args[$key]);
			} else {
				$auth_sig .= $key . $data;
			}
		}
		if (!empty($this->options['secret'])) {
			$args["api_sig"] = md5($this->options['secret'] . $auth_sig);
		}

		$photo = realpath($photo);
		$args['photo'] = '@' . $photo;

		$curl = curl_init(self::$configuration['upload_endpoint']);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
		curl_close($curl);

		$rsp = explode("\n", $response);
		foreach ($rsp as $line)
		{
			if (ereg('<err code="([0-9]+)" msg="(.*)"', $line, $match))
			{
/*
				if ($this->die_on_error)
					die("The Flickr API returned the following error: #{$match[1]} - {$match[2]}");
				else {
					$this->error_code = $match[1];
					$this->error_msg = $match[2];
					$this->parsed_response = false;
*/
					return false;
/*
				}
*/
			}
			elseif (ereg("<ticketid>(.*)</", $line, $match))
			{
				$this->error_code = false;
				$this->error_msg = false;
				return $match[1];
			}
		}
	}


	//send request to Flickr
	public function Request($method, $args = array())
	{
		if (substr($method,0,7) != "flickr.")
			$method = "flickr." . $method;

		//Process arguments, including method and login data.
		$args = array_merge(array("method" => $method, "format" => "php_serial", "api_key" => self::$configuration['key']), $args);

		if ($this->access_token)
			$args = array_merge($args, array("auth_token" => $this->access_token));
		else
		{
			/***database connect***/
			$db = DB::getInstance();

			//get access token from database
			if($options = $db->Column("SELECT `value` FROM `bx_options` WHERE `key`='flickr_plugin'"))
			{
				$options = unserialize($options);
				//see if we have an access token
				if($options['access_token']) $this->access_token = $options['access_token'];
			}
			//if no access token was found then this must be the first request to get the access token.

			unset($options);
		}

		ksort($args);

		$signature = "";
		foreach ($args as $key => $val) {
			if ( is_null($val) ) {
				unset($args[$key]);
				continue;
			}
			$signature .= $key . $val;
		}

		if (!empty(self::$configuration['secret'])) {
			$api_sig = md5(self::$configuration['secret'] . $signature);
			$args['api_sig'] = $api_sig;
		}


		$target = self::$configuration['rest_endpoint'];

		if ( function_exists('curl_init') ) {

			// use curl
			$curl = curl_init($target);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			curl_close($curl);
		}
		else
		{
			// Use sockets.
			$args = http_build_query($args);

			$fp = @pfsockopen($matches[1], 80);
			if (!$fp) {
				die('Could not connect to the web service');
			}
			fputs ($fp,'POST ' . $matches[2] . " HTTP/1.1\n");
			fputs ($fp,'Host: ' . $matches[1] . "\n");
			fputs ($fp,"Content-type: application/x-www-form-urlencoded\n");
			fputs ($fp,"Content-length: ".strlen($args)."\n");
			fputs ($fp,"Connection: close\r\n\r\n");
			fputs ($fp,$args . "\n\n");
			$response = "";
			while(!feof($fp)) {
				$response .= fgets($fp, 1024);
			}
			fclose ($fp);
			$chunked = false;
			$http_status = trim(substr($response, 0, strpos($response, "\n")));
			if ( $http_status != 'HTTP/1.1 200 OK' ) {
				die('The web service endpoint returned a "' . $http_status . '" response');
			}
			if ( strpos($response, 'Transfer-Encoding: chunked') !== false ) {
				$temp = trim(strstr($response, "\r\n\r\n"));
				$response = '';
				$length = trim(substr($temp, 0, strpos($temp, "\r")));
				while ( trim($temp) != "0" && ($length = trim(substr($temp, 0, strpos($temp, "\r")))) != "0" ) {
					$response .= trim(substr($temp, strlen($length)+2, hexdec($length)));
					$temp = trim(substr($temp, strlen($length) + 2 + hexdec($length)));
				}
			} elseif ( strpos($response, 'HTTP/1.1 200 OK') !== false ) {
				$response = trim(strstr($response, "\r\n\r\n"));
			}
		}


		/*
		 * Uncomment this line (and comment out the next one) if you're doing large queries
		 * and you're concerned about time.  This will, however, change the structure of
		 * the result, so be sure that you look at the results.
		 */
		$this->parsed_response = unserialize($response);
		//$parsed_response = $this->clean_text_nodes(unserialize($response));

		if ($parsed_response['stat'] == 'fail')
			die("The Flickr API returned the following error: #{$parsed_response['code']} - {$parsed_response['message']}");

		return $response;
	}


	public static function GetConfig()
	{
		/***database connect***/
		$db = DB::getInstance();

		$plugin_options = unserialize($db->Column("SELECT `value` FROM `bx_options` WHERE `key`='flickr_plugin'"));

		//runtime config
		self::$configuration = array_merge(self::$configuration,
			array(
			'callbackUrl' => Path::Get('url:admin').'/plugins/flickr.plugin.php',
			'key' => $plugin_options['key'],
			'secret' => $plugin_options['secret'],
			));

		return self::$configuration;
	}

	//get empty configuration defaults
	public function GetDefaults($name)
	{
		$this->defaults = array(
			'name' => $name, //get_class($this),
			'username' => NULL,
			'postonupdate' => false,
			'key' => NULL,
			'secret' => NULL,
			"updateposting_possible" => ($this->updateposting_possible ? $this->updateposting_possible : false),
			"auth_type" => ($this->auth_type ? $this->auth_type : NULL),
			'infotext' => ($this->infotext ? $this->infotext : NULL),
			);
		return $this->defaults;
	}

	//this function must be present in each social posting plugin
	//no changes in this function are necessary if you want to create a new plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class


//returning from flickr
if(!empty($_GET['frob']))
{
	$translate = new Translator("admin");
	Zend_Registry::set('translate', $translate);

			/*--------------------------------------------------------------*/
			/*						Authentification						*/
			/*--------------------------------------------------------------*/
	//				Authentification::check();
					if(!Authentification::Login())
					{
						echo Authentification::GetError();
						die();
					}
					Authentification::CheckPermission('administrator');
			/*--------------------------------------------------------------*/


	//returning from flickr auth
	//continuing where we left off
	$plugin = new flickr_plugin();
	$plugin->Authorize();
}
//request to remove auth
elseif(isset($_POST['plugin'])
	&& isset($_POST['remove_auth'])
	&& isset($_POST['securitytoken'])
	&& $_POST['remove_auth'] == '1')
{
	$translate = new Translator("admin");
	Zend_Registry::set('translate', $translate);

			/*--------------------------------------------------------------*/
			/*						Authentification						*/
			/*--------------------------------------------------------------*/
	//				Authentification::check();
					if(!Authentification::Login())
					{
						echo Authentification::GetError();
						die();
					}
			/*--------------------------------------------------------------*/

	if('administrator' != Authentification::GetRole())
		Template::PermissionDenied();

	if(!isset($_POST['securitytoken']) || $_POST['securitytoken'] !== Session::GetToken())
		die($translate->_('Security token mismatch'));

	//remove Auth
	SocialPlugin::DeAuthorize('flickr_plugin');
}







//debug
if(isset($_GET['test']) && $_GET['test']=='1')
{

	//test array
	$update = array(
			'id' => 3,
			'type' => 'pics',
			'title' => 'Test Title',
			'description' => 'test description',
			'freeimages' => array(
					0 => 'D:\AppServ\www\buxomcurves\webroot\free\pics\bianca-army\001.jpg',
					1 => 'D:\AppServ\www\buxomcurves\webroot\free\pics\bianca-army\004.jpg',
//					2 => 'D:\AppServ\www\buxomcurves\webroot\free\pics\bianca-army\006.jpg',
//					3 => 'D:\AppServ\www\buxomcurves\webroot\free\pics\bianca-army\014.jpg',
				),
		);

	//test
	$plugin = new flickr_plugin();
	$plugin->NewUpdate($update);
}