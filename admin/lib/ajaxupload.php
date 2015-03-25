<?php

/* **************************************************************
 *  File: ajaxupload.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/


//works, but needs hard security checked!!!

//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

require_once "../../_init.php";

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
//security check
if(Session::GetToken() !== @$_POST['securitytoken'] || isset($_GET['securitytoken']))
	die($translate->_('Security token mismatch'));

/***memory manager***/
/*
//we use memory manager to prevent running out of memory on expensive image resizing operations
include Path::Get('path:site')."/includes/classes/Zend/Memory.php";
$backendOptions = array(
    'cache_dir' => realpath(Path::Get('path:site').'/includes/classes/Outline/app/cache/')
); // Directory where to put the swapped memory blocks
$memoryManager = Zend_Memory::factory('File', $backendOptions);
// Set MinSize limit in bytes to prevent unnecessary swapping
//set to 10 MB
$memoryManager->setMinSize(10240000);
*/
/***memory manager***/



// check GD
if(!function_exists('imagecreatetruecolor')) {
    die('GD Library Error');
}



/***free memory on fatal error***
function shutdown_func() {
	global $filename;
	if($filename)
		@imagedestroy($filename);
}
register_shutdown_function("shutdown_func");
***free memory on fatal error***/

/*** upload ***/
function uploadImage($fileName, $maxSize, $maxW, $colorR, $colorG, $colorB, $maxH = null){

		//get translate
		$translate = new Translator("admin");

		$errorList = array();

		/***temp path config***/
		$fullRelPath = Path::Get('rel:admin/temp')."/";
		$relPath = "../".Path::Get('temp')."/";
		/***temp path config***/

/*
		$id = intval($_POST['lastid']);
*/

		//get thumb number (and later name form field with it (see bottom))
		$thnumber = intval($_POST['thnumber']);

		$folder = $relPath;
		$maxlimit = $maxSize;
		$match = "";


		$filesize = $_FILES['filename']['size'];
		if($filesize > 0){

			$filename = strtolower($_FILES['filename']['name']);

		   	if($filesize < 1){
				$errorList[] = $translate->_("File size empty");
			}
			if($filesize > $maxlimit){
				$errorList[] = $translate->_("File size too big");
			}

			if(count($errorList) === 0){

////




				$file_ext = preg_split("~\.~", $filename);

				$allowed_ext = Config::Get('image_extensions');

				for($i=0, $s=count($allowed_ext); $i < $s; $i++)
				{
					//check for invalid input from config.php
					$allowed_ext[$i] = str_replace(array("*","."),"",trim($allowed_ext[$i]));
				}

				foreach($allowed_ext as $ext){
					if($ext==end($file_ext)){
						$match = "1"; // File is allowed

						$front_name = $file_ext[0];

				        $front_name = str_replace(".", '', $front_name);

						if ($_POST['type'] == 'model'){
							//md5($front_name)
//							$newfilename = $front_name.".".end($file_ext);
//							$newfilename = md5($front_name . time()).".".$ext;
							$newfilename = uniqid('model_').".".$ext;

						} else { //type == 'update'
							//md5($front_name)
//							$newfilename = $front_name."_".$maxW."x".$maxH.".".end($file_ext);
//							$newfilename = md5($front_name . time()).".".$ext;
							$newfilename = uniqid('bx_').".".$ext;
						}


						$filetype = end($file_ext);
						$save = $folder.$newfilename;
						if(!file_exists($save)){
							list($width_orig, $height_orig) = getimagesize($_FILES['filename']['tmp_name']);



//echo "<br>";
//echo "Problem mit memory bei sehr grossen Bildern!!";
//echo "<br>";


if ($width_orig > 2000 || $height_orig > 2000){
	die("The image you tried to upload is too big ($width_orig x $height_orig pixels).<br>While the script will try to resize images for you, you should only upload thumbnails here.");
}





							if($maxH == null){
								if($width_orig < $maxW){
									$fwidth = $width_orig;
								}else{
									$fwidth = $maxW;
								}
								$ratio_orig = $width_orig/$height_orig;
								$fheight = $fwidth/$ratio_orig;

								$blank_height = $fheight;
								$top_offset = 0;

							}else{
								if($width_orig <= $maxW && $height_orig <= $maxH){
									$fheight = $height_orig;
									$fwidth = $width_orig;
								}else{
									if($width_orig > $maxW){
										$ratio = ($width_orig / $maxW);
										$fwidth = $maxW;
										$fheight = ($height_orig / $ratio);
										if($fheight > $maxH){
											$ratio = ($fheight / $maxH);
											$fheight = $maxH;
											$fwidth = ($fwidth / $ratio);
										}
									}
									if($height_orig > $maxH){
										$ratio = ($height_orig / $maxH);
										$fheight = $maxH;
										$fwidth = ($width_orig / $ratio);
										if($fwidth > $maxW){
											$ratio = ($fwidth / $maxW);
											$fwidth = $maxW;
											$fheight = ($fheight / $ratio);
										}
									}
								}

								if($fheight == 0 || $fwidth == 0 || $height_orig == 0 || $width_orig == 0){
									die("FATAL ERROR. REPORT ERROR CODE [add-pic-line-".__LINE__."]");
								}
								if($fheight < 45){
									$blank_height = 45;
									$top_offset = round(($blank_height - $fheight)/2);
								}else{
									$blank_height = $fheight;
								}
							}

							$image_p = imagecreatetruecolor($fwidth, $blank_height);
							//$white = imagecolorallocate($image_p, $colorR, $colorG, $colorB);
							//imagefill($image_p, 0, 0, $white);

							switch($filetype){
								case "gif":
									$image = @imagecreatefromgif($_FILES['filename']['tmp_name']);
								break;
								case "jpg":
								case "jpeg":
									$image = imagecreatefromjpeg($_FILES['filename']['tmp_name']);
								break;
								case "png":
									$image = @imagecreatefrompng($_FILES['filename']['tmp_name']);
								break;
							}

							@imagecopyresampled($image_p, $image, 0, $top_offset, 0, 0, $fwidth, $fheight, $width_orig, $height_orig);

							switch($filetype){
								case "gif":
									if(!@imagegif($image_p, $save)){
										$errorList[]= $translate->_("Permission Denied")." [GIF]";
									}
								break;
								case "jpg":
								case "jpeg":
									if(!@imagejpeg($image_p, $save, 100)){
										$errorList[]= $translate->_("Permission Denied")." [JPG]";
									}
								break;
								case "png":
									if(!@imagepng($image_p, $save, 0)){
										$errorList[]= $translate->_("Permission Denied")." [PNG]";
									}
								break;
							}

							@imagedestroy($filename);
							@imagedestroy($image);
							unset($image_p);

						}else{

							//$errorList[]= "CANNOT MAKE IMAGE IT ALREADY EXISTS";
							//use existing image!!!
							$ret = array($fullRelPath . $newfilename, $newfilename);
							return $ret;


						}
					}
				}
			}
		}else{
			$errorList[]= $translate->_("No File Selected");
		}
		if(!$match){
			$_ext = end($file_ext);
		   	$errorList[]= $translate->_("File type not allowed").': '.Input::clean($_ext,'NOHTML');
		}
		$s = sizeof($errorList);
		if($s == 0){

			$ret = array($fullRelPath . $newfilename , $newfilename);
			return $ret;
			//return $fullRelPath.$newfilename;

		}else{
			$eMessage = array();
			for ($x=0; $x<$s; $x++){
				$eMessage[] = $errorList[$x];
			}
			$ret = array ();
			$ret[] = $eMessage;
		   	return $ret;
		}
} //uploadImage


function file_upload_error_message($error_code) {
	//get translate
	$translate = new Translator("admin");

	switch ($error_code) {
		case UPLOAD_ERR_INI_SIZE:
			return $translate->_('Upload Size Exceeded');
		case UPLOAD_ERR_FORM_SIZE:
			return $translate->_('Maxfilesize exceeded');
		case UPLOAD_ERR_PARTIAL:
			return $translate->_('The uploaded file was only partially uploaded');
		case UPLOAD_ERR_NO_FILE:
			return $translate->_('No file was uploaded');
		case UPLOAD_ERR_NO_TMP_DIR:
			return $translate->_('Missing a temporary folder');
		case UPLOAD_ERR_CANT_WRITE:
			return $translate->_('Failed to write file to disk');
		case UPLOAD_ERR_EXTENSION:
			return $translate->_('File upload stopped by extension');
		default:
			return $translate->_('Unknown upload error');
	}
} //file_upload_error_message


if('administrator' == Authentification::GetRole() || 'editor' == Authentification::GetRole())
{

	if(is_uploaded_file($_FILES['filename']['tmp_name']) && $_FILES['filename']['error'] === UPLOAD_ERR_OK)
	{
		$maxSize = intval($_POST['maxSize']);
		$maxW = intval($_POST['maxW']);
		$maxH = intval($_POST['maxH']);
		$colorR = intval($_POST['colorR']);
		$colorG = intval($_POST['colorG']);
		$colorB = intval($_POST['colorB']);

		$upload_image = uploadImage('filename', $maxSize, $maxW, $colorR, $colorG, $colorB, $maxH);

		if(is_array($upload_image[0]))
		{
			foreach($upload_image[0] as $key => $value)
			{
				if($value == "-ERROR-") {
					unset($upload_image[0][$key]);
				}
			}
			$document = array_values($upload_image[0]);
			for ($x=0, $ds = sizeof($document); $x < $ds; $x++)
			{
				$errorList[] = Input::clean($document[$x], 'NOHTML');
			}
			$imgUploaded = false;
		}
		else
		{
			$imgUploaded = true;
		}
	}else
	{
			$imgUploaded = false;
			$errorList[] = file_upload_error_message($_FILES['filename']['error']);
	}

	//check the type (only used on update page)
	$type = Input::clean_single('g', 'type', 'NOHTML');

	if($imgUploaded)
	{
			echo '<img src="'.$upload_image[0].'" border="0" />
			<br><input type="hidden" name="'.$type.'_thumbs['.$thnumber.']" value="'.$upload_image[1].'" />
			<img src="'.Path::Get('url:admin').'/img/icons/success.gif" width="16" height="16" border="0" style="margin-bottom: -4px;" />';
	}
	else
	{
			echo '<img src="'.Path::Get('url:admin').'/img/icons/error.gif" width="16" height="16px" border="0" style="marin-bottom: -3px;" /> '.$translate->_("Error").': '.'<br>';
			foreach($errorList as $value){
		    		echo $value.'.<br>';
			}
	}

} //auth