<?php

/***page setup***/
$tpl->title = "BuxomCurves FAQ";
$tpl->headerimgheight = "238";
$tpl->barcolor = "969696";
$tpl->_keywords = "";
$tpl->_description = "BuxomCurves.com Frequently Asked Questions";
$perpage = 30;
/***page setup***/

/***database connect***/
$db = DB::getInstance();

/***catcha***/
require_once('./includes/classes/Zend/Captcha/Image.php');
$captcha = new Zend_Captcha_Image(Config::Get('_captcha'));

$var = array();

//CHECK FORM SUBMISSION:
if(!empty($_POST['comment'])){ //a question was posted

	//	name check
		if(empty($_POST['name'])){
			$tpl->errormessage($translate->_("No Name")."<br>"); //"You did not fill in a name."
		} else
		{
			$var['name'] = Input::sanitize_str($_POST['name']);
		}
	//	email check
		if(empty($_POST['email'])){
			$tpl->errormessage($translate->_("No Email")."<br>"); //"You did not fill in an email address."
		} else
		{
			require_once "./includes/classes/Zend/Validate/EmailAddress.php";

			$validator = new Zend_Validate_EmailAddress();
			if ($validator->isValid($_POST['email'])) {
			    // Email valid
				$var['email'] = $_POST['email'];
			} else {
			    // Email invalid
				$strError = '';
			    foreach ($validator->getMessages() as $message) {
			        $strError .= $message . "<br>".PHP_EOL;
			    }
				$tpl->errormessage($strError);
				unset($strError);
			}
		}
	//	comment check
		if(empty($_POST['comment'])){
			$tpl->errormessage($translate->_("No Question")."<br>"); //"You did not supply a question."
		} else
		{
			$var['question'] = Input::sanitize_str($_POST['comment']);
		}
	//	date
		$var['date'] = time();


	//	captcha check
        if (!$captcha->isValid($_POST['captcha'])) {
			$tpl->errormessage($translate->_("Captcha Incorrect")."<br>"); //"The Captcha was entered incorrectly. Please try again."
        }


	if ($tpl->errormessage == ''){ //all is okay

		//write data to database

		$db->Update('INSERT DELAYED INTO `bx_faq` (`id`, `question`, `name`, `email`, `date`, `status`) VALUES (?,?,?,?,?,?)', array(NULL, $var[question], $var[name], $var[email], $var[date], 'queued'));

		$tpl->successmessage($translate->_("Thank You")."<br>".$translate->_("Moderation Review"));

		//send email notice
		if (Config::GetDBOptions('sendemailonnewfaqitem')){

			$subject = __sitename.': '.$translate->_('New FAQ Item Moderation');
			$body = "Hi.\n".$translate->_('New Faq Item Review').":\n".$translate->_('Name').": ".$var['name']."\n".$translate->_('Email').": ".$var['email']."\n".$var['question']."\n\n(".$translate->_('Sent from')." ".__FILE__." on line ".__LINE__;

			Logger::SendMail($var['email'],$var['name'],$subject,$body);
		}

		//remove form prefill
		unset($var);
	}

} //END CHECK FORM SUBMISSION


	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/


/***pagination***/
$p = new Pagination;
$query = "SELECT count(*) FROM `bx_faq` WHERE `status` = 'approved'";
$p->paginate($query, $perpage, 'faq.php');
$p->assign($tpl);
/***pagination***/


/***content query***/
$categories = array();
$categories = $db->FetchAll("SELECT `id`, `name` FROM `bx_faq_category`");

$faqitems = array();
$faqitems = $db->FetchAll("SELECT `f`.`id`,`f`.`name`,`f`.`question`,`f`.`answer`,`c`.`name` FROM `bx_faq` AS `f`,`bx_faq_category` AS `c`  WHERE `f`.`status` = 'approved' AND `f`.`dateline` <= ? AND `c`.`id` = `f`.`category_id`" . $p->pagination['limit'], array( DB::UNIXNOW() ) );
/***content query***/


/***TEMPLATE ASSIGNMENTS***/

//dirty hack:
//->show header image of 2257 page.
$tpl->templatename = "2257page";

//content
$tpl->assign("categories", $categories);
$tpl->assign("faqitems", $faqitems);

//Captcha
$tpl->assign("captcha", array('id' => $captcha->generate(), 'img' => $captcha->render() ));

//errors
if(isset($tpl->errormessage)) $tpl->assign("errormessage", $tpl->errormessage);
if(isset($tpl->successmessage)) $tpl->assign("successmessage", $tpl->successmessage);

//prefill form
if(isset($var)) $tpl->assign("var", $var);

//internationalization
$_t = $translate->translateArray(array(
	"email"=> "Email",
	"enter_verification_code"=> "Enter Verification Code",
	"frequentlyaskedquestions"=> "Frequently Asked Questions",
	"nickname"=> "Nickname",
	"submitquestion"=> "Submit Question",
	"willbepublished"=> "will be published",
	"willnotbepublished"=> "will not be published",
	"categories"=> "Categories",
	"selectcategory"=> "Select Category",
));
$tpl->assign("_t", $_t);
