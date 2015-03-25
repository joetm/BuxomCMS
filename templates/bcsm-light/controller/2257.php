<?php

/*** connect ***/
$db = DB::getInstance();

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

	/***create 2257 image***/
	//create image only if it does not exist
	$filecheck = file_exists(__sitepath.'/img/2257.gif');
	if(!$filecheck){
		//get 2257 info
		$companyinfo = Config::GetDBOptions('2257info');
		if($companyinfo){
			$_im = new TextToImage();
			$_im->makeImageF($companyinfo, __sitepath."/img/fonts/times.ttf",$X=0,$Y=0,$fontsize=12,$color=array(0x0,0x0,0x0), false); //$bgcolor=array(0xE2,0xE2,0xE2)
			$_im->saveAsGif('2257', __sitepath.'/img/');
			$filecheck = true;
		}
	}
	/***create 2257 image***/

/*** disconnect ***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

//internationalization
$_t = $translate->translateArray(array(
	"full2257"  => "2257 Full",
));
$tpl->assign("_t", $_t);

//address image
if($filecheck) $tpl->assign("img2257", rtrim(__siteurl, '/').'/img/2257.gif');
