<?php

/* **************************************************************
 *  File: crop.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/*
 * crop and return image url
 */

/*** include the init.php file ***/
require '../../_init.php';

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

if('administrator' !== Authentification::GetRole() && 'editor' !== Authentification::GetRole())
	die('auth error');

$translate = new Translator("admin");
Zend_Registry::set('translate', $translate);

if(@$_POST['securitytoken'] !== Session::GetToken())
	die($translate->_('Security token mismatch'));

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	if('administrator' == Authentification::GetRole() || 'editor' == Authentification::GetRole())
	{
		$_config['thumbnailsizes'] = Config::GetDBOptions('picturegrab_thumbnailsize');

		//	$id = intval($_POST['id']);

		$input = Input::clean_array('p',array(
									'filename' => 'NOHTML',
									'internal_slug' => 'FILENAME',
									'x' => 'UINT',
									'y' => 'UINT',
									'w' => 'UINT',
									'h' => 'UINT',
									));
		$x = intval($input['x']);
		$y = intval($input['y']);
		$w = intval($input['w']);
		$h = intval($input['h']);


		$allowed_mime_types = Config::Get('image_extensions');
		if ( !in_array(strtolower(substr($filename, -3)), $allowed_mime_types )
			&& !in_array(strtolower(substr($filename, -4)), $allowed_mime_types ) )
			die($translate->_("Invalid File Extension"));

		$filepath = Path::Get('path:admin/temp').DIR_SEP.$input['filename'];

		if(file_exists($filepath))
		{
//FIX: Ugly double 'thumbs' in url
				$newfile =  Path::Get('path:thumbs/pics') . DIR_SEP . $input['internal_slug'] . DIR_SEP .'thumbs'. DIR_SEP . $input['filename'];

//				echo "new file: ".$newfile."<br>";

				$success = true;
				try
				{
					/* cropping */
					$imgAdapter = new Img_Adapter;
					//crop
						$imgAdapter->Crop($filepath, $filepath, $x, $y, $w, $h);
					//resize
						$imgAdapter->Resize($filepath, $newfile, $_config['thumbnailsizes']['width'], $_config['thumbnailsizes']['height']);
				}
				catch (Exception $e) {
					echo $translate->_('Error').': '.$e->getMessage().PHP_EOL;
					$success = false;
				}

				if($success == true)
				{
					//return success message
					echo "success";
				}
		}
		else
			echo $translate->_('Error404'); //or: 'Empty Path or Crop Values'

	} //auth

} //request_method == post

?>