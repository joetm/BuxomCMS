<?php

/* **************************************************************
 *  File: ZombaioGW_1_1.mysql.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/***config***/
require_once "../../_init.php";

require_once "./_config.php";


if (!isset($_REQUEST["ZombaioGWPass"]) || (!in_array($_REQUEST["ZombaioGWPass"], $gwpass)))
{
	/*
	header("HTTP/1.0 401 Unauthorized");
	exit("<h1>Zombaio Gateway 1.1</h1><h3>Authentication failed.</h3>");
	*/
	die('ERROR');
}


/*** connect ***/
$db = DB::getInstance();


/************************/
/*        SIGNUP        */
/************************/
if (!isset($_REQUEST["Action"]))
{
	//no action specified
	die('ERROR');
}

/************************/
/*     ADD NEW USER     */
/************************/
elseif ($_REQUEST["Action"] == "user.add")
{
	$input = Input::clean_array('r', array(
			'username'		=> 'NOHTML',
			'password'		=> 'STR',
			'SUBSCRIPTION_ID'	=> 'UINT',
			'TRANSACTION_ID'	=> 'UINT',
			'Amount'		=> 'FLOAT',
			'Amount_Currency'	=> 'NOHTML',
			'FIRSTNAME'		=> 'NOHTML',
			'LASTNAME'		=> 'NOHTML',
			'NAME_ON_CARD'		=> 'NOHTML',
			'ADDRESS'		=> 'NOHTML',
			'POSTAL'		=> 'NOHTML',
			'REGION'		=> 'NOHTML',
			'CITY'			=> 'NOHTML',
			'COUNTRY'		=> 'NOHTML',	//ISO CODE
			'EMAIL'			=> 'NOHTML',
			'SITE_ID'		=> 'UINT',
			'PRICING_ID'		=> 'UINT',
			'VISITOR_LANGUAGE'	=> 'NOHTML',
			'VISITOR_IP'		=> 'NOHTML',
			'CardHash'		=> 'STR',
			'AffiliateID'		=> 'UINT',
			'AffiliateCommission'	=> 'NOHTML',
		));

	if (empty($input['username']) || empty($input['password']))
		die("ERROR");


	$salt = Session::CreateToken();

	/* start insertion */
	$inserts = false;

	//user location
	//check if location does not exist
	$inserts['location_id'] = $db->Column("SELECT l.id
				FROM `bx_location` AS `l`
				WHERE l.location = ?
				AND l.state = ?
				AND l.zipcode = ?
				AND l.city = ?", array(
				$input['ADDRESS'],
				$input['POSTAL'],
				$input['REGION'],
				$input['CITY'],
			));
	if(!$inserts)
	{
		//insert location
		$db->Update("INSERT INTO `bx_location`
			SET `location` = ?,
			`zipcode` = ?,
			`state` = ?,
			`city` = ?,
			`country_iso` = ?",
			array(
				$input['ADDRESS'],
				$input['POSTAL'],
				$input['REGION'],
				$input['CITY'],
				$input['COUNTRY']
			));
		$inserts['location_id'] = $db->LastInsertId();
	}
	if(!$inserts['location_id']) $inserts['location_id'] = null;

	//user email
	$db->Update("INSERT INTO `bx_member_email`
			(`id`,`email`,`no_emails`,`email_status`,`last_mailing`,`num_mailings`)
			VALUES (
			NULL, ?, 0, 'unmailed', NULL, 0
			)",array(
			$input['email']
	));
	$inserts['email_id'] = $db->LastInsertId();
	if(!$inserts['email_id']) $inserts['email_id'] = null;


	//need to be added as extra variables to the join form!
//	$birthdate = (@$_GET['birthdate'] ? Input::clean_single('g','birthdate','NOHTML'):null);
//	$gender = (@$_GET['gender'] ? Input::clean_single('g','gender','NOHTML'):'unknown');


	//final write to db
	$status = $db->Update("INSERT INTO `bx_member` (
		`id`, `username`, `status`,
		`password`, `password_htaccess`, `salt`,
		`email_id`,
		`firstname`, `lastname`, `birthdate`, `gender`,
		`location_id`, `subscription_id`,
		`signup_IP`, `processor`,
		`name_on_cc`, `card_hash`,
		`join_date`, `expiration_date`, `expiration_reason`,
		`meta_locale`,
		`num_rebills`, `lifetime_revenue`
	) VALUES (
		NULL, ?, 'active',					//1 id,...
		?, ?, ?,						//2 password,...
		?,							//3 email_id
		?, ?, ?, ?,						//4 first_name,...
		?, ?,							//5 location_id,...
		?, 'zombaio',						//6 signup_IP
		?, ?,							//7 name_on_cc,...
		?, ?, ?,						//8 join_date,...
		?,							//9 meta_locale
		?, ?							//10 num_rebills,...
		)", array(
		$input['username'],							//1
		md5($salt.$input['password']), crypt($input['password']), $salt,	//2
		$inserts['email_id'],							//3
		$input['FIRSTNAME'], $input['LASTNAME'], NULL, NULL,			//4
		$inserts['location_id'], $input['SUBSCRIPTION_ID'],			//5
		$input['VISITOR_IP'],							//6
		$input['NAME_ON_CARD'], $input['CardHash'],				//7
		time(), $inserts['expiration_date'], NULL,				//8
		$input['VISITOR_LANGUAGE'],						//9
		0, $input['Amount']							//10
	));

	if($status)
		exit("OK");
	else
		die("ERROR");
}

/************************/
/*     CANCELLATION	*/
/************************/
elseif ($_REQUEST["Action"] == "user.delete")
{
	$input = Input::clean_array('r', array(
			'username'		=> 'NOHTML',
			'ReasonCode'		=> 'UINT',
			'SubscriptionID'	=> 'UINT',
			'SiteID'		=> 'UINT',
		));

	$id = $db->Column("SELECT `id`
					FROM `bx_member`
					WHERE `username` = ?
					AND `subscription_id` = ?", array(
						$input['username'], $input['SubscriptionID']
					));
	if (!$id)	die("USER_DOES_NOT_EXIST");

	//translate zombaio's reason code
	$reason = null;
	if(empty($input['ReasonCode'])) $input['ReasonCode'] = 0;
	else
		switch($input['ReasonCode'])
		{
			case 1:
				$reason = 'satisfied customer (just moving on)';
				break;
			case 2:
				$reason = 'income issues';
				break;
			case 3:
				$reason = 'spouse called in about charge';
				break;
			case 4:
				$reason = 'minor used card';
				break;
			case 5:
				$reason = 'only interested in trial subscription';
				break;
			case 6:
				$reason = 'did not read terms and conditions';
				break;
			case 7:
				$reason = 'not satisfied with content';
				break;
			case 8:
				$reason = 'not receiving replies from webmaster';
				break;
			case 9:
				$reason = 'password problems';
				break;
			case 10:
				$reason = 'unable to load content fast enough';
				break;
			default:
			case 11:
				$reason = 'other';
				break;
		}

	//deactivate member
	//we keep the member in the database
	//so that we can email them later
	$status = $db->Update("UPDATE `bx_member`
			SET `status` = 'inactive',
			`expiration_date` = ?,
			`expiration_reasoncode` = ?
			`expiration_reason` = ?
			WHERE `id` = ?",
			array(
				time(),
				$input['ReasonCode'],
				$reason,
				$id,
			));

	if($status)
		die("OK");
	else
		die("ERROR");
}

/************************/
/*	 REBILL		*/
/************************/
elseif ($_REQUEST["Action"] == "rebill")
{
	//rebill is merely an information call

	$input = Input::clean_array('r', array(
			'SUBSCRIPTION_ID'		=> 'UINT',
			'TRANSACTION_ID'		=> 'UINT',
			'Success'			=> 'UINT',
			'Retries'			=> 'UINT',
			'SiteID'			=> 'UINT',
			'Amount'			=> 'FLOAT',
			'Amount_Currency'		=> 'NOHTML',
			'AffiliateID'			=> 'UINT',
			'AffiliateCommission'		=> 'NOHTML',
		));

	//successful rebill
	//increase the rebill counter if rebill successfull
	if($input['Success'] === 1)
	{
		$info = $db->Row("SELECT `num_rebills`, `lifetime_revenue`
				FROM `bx_member`
				WHERE `subscription_id` = ?", array(
					$input['SUBSCRIPTION_ID']
				));

		//subscription was not found
		if(!$info) exit();

		//one more rebill!
		$info['num_rebills']++;
		$info['lifetime_revenue'] += ($input['Amount'] ? $input['Amount'] : 0);

		$db->Update("UPDATE `bx_member`
				SET `num_rebills` = ?,
				`lifetime_revenue` = ?
				WHERE `subscription_id` = ?",
				array(
					$info['num_rebills'],
					$info['lifetime_revenue'],
					$input['SUBSCRIPTION_ID']
		));
	}

	//does not expect a response
	exit();
}

/************************/
/*      CHARGEBACK      */
/************************/
elseif ($_REQUEST["Action"] == "chargeback")
{
	$input = Input::clean_array('r', array(
			'Identifier'			=> 'UINT',
			'SUBSCRIPTION_ID'		=> 'UINT',
			'TRANSACTION_ID'		=> 'UINT',
			'SiteID'			=> 'UINT',
			'Username'			=> 'UINT',
			'Amount'			=> 'FLOAT',
			'Amount_Currency'		=> 'NOHTML',
			'ReasonCode'			=> 'UINT',
			'LiabilityCode'			=> 'UINT',
			'ChargebackRatio'		=> 'NOHTML',
			'CloseDownWarning'		=> 'NOHTML',
		));

	//get user
	$id = $db->Column("SELECT `id` FROM `bx_member`
					WHERE `username` = ?
					AND `subscription_id` = ?", array(
						$input['Username'],
						$input['SUBSCRIPTION_ID']
					));
	if (!$id)	die("OK"); //USER_DOES_NOT_EXIST

/*
	//zombaio send email automatically
	if(@$_GET['CloseDownWarning'] == 'True')
	{
		//chargeback ratio over 5%
		$ratio = Input::clean(@$_GET['ChargebackRatio'],'NOHTML');
		//send email notice to admin
		//...
	}
*/

	//reason
	$reason = null;
	if(!$input['ReasonCode']) $input['ReasonCode'] = 0;
	else
		switch($input['ReasonCode'])
		{
			case 30:
				$reason = 'CB - Services/Merchandise Not Received';
				break;
			case 41:
				$reason = 'Cancelled Recurring Transaction';
				break;
			case 53:
				$reason = 'Not as Described or Defective';
				break;
			case 57:
				$reason = 'Fraudulent Multiple Drafts';
				break;
			case 73:
				$reason = 'Expired Card';
				break;
			case 74:
				$reason = 'Late Presentment';
				break;
			case 75:
				$reason = 'Cardholder Does Not Recognize';
				break;
			case 83:
				$reason = 'Fraudulent Transaction - Card Absent Environment';
				break;
			case 85:
				$reason = 'Credit Not Processed';
				break;
			case 86:
				$reason = 'Altered Amount / Paid by Other Means';
				break;
			case 93:
				$reason = 'Risk Identification Service (RISCE)';
				break;
			case 101:
				$reason = 'Zombaio - Not as Described or Defective';
				break;
			case 102:
				$reason = 'Zombaio - No Access to Website (Script Problem or Site Down)';
				break;
			default:
				$reason = 'Other';
				break;
		}

	//liability
	$liability = null;
	if(!$input['LiabilityCode']) $input['LiabilityCode'] = 0;
	else
		switch($input['LiabilityCode'])
		{
			case 1:
				$liability = 'Merchant';
				break;
			case 2:
				$liability = 'Card Issuer';
				break;
			case 3:
				$liability = 'Zombaio';
				break;
		}


	//set status to chargeback
	$status = $db->Update("UPDATE `bx_member`
				SET `status` = 'chargeback',
				WHERE `id` = ?",
				array(
					$id
				));

	//report received
	exit("OK");
}

/************************/
/*       DECLINE        */
/************************/
elseif ($_REQUEST["Action"] == "declined")
{
	$input = Input::clean_array('r', array(
			'Identifier'		=> 'NOHTML',
			'TRANSACTION_ID'	=> 'UINT',
			'SiteID'		=> 'UINT',
			'Amount'		=> 'FLOAT',
			'Amount_Currency'	=> 'NOHTML',
			'FIRSTNAME'		=> 'NOHTML',
			'LASTNAME'		=> 'NOHTML',
			'ADDRESS'		=> 'NOHTML',
			'POSTAL'		=> 'NOHTML',
			'REGION'		=> 'NOHTML',
			'CITY'			=> 'NOHTML',
			'COUNTRY'		=> 'NOHTML', //ISO CODE
			'EMAIL'			=> 'NOHTML',
			'ReasonCode'		=> 'UINT',
			'VISITOR_IP'		=> 'NOHTML',
			'CardHash'		=> 'STR',
		));

/*
	//get user
	$info = $db->Row("SELECT `id`, `lifetime_revenue`
			FROM `bx_member`
			WHERE `username` = ?
			AND `card_hash` = ?",
			array(
				$username,
				$card_hash
			));
	if (!$info)	die("USER_DOES_NOT_EXIST");

	$transaction_amount = (@$_GET['Amount'] ? Input::clean_single('g','Amount','NOHTML'):0);

	$lifetime_revenue = $info['lifetime_revenue'] - $transaction_amount;

	$reasonid = @$_GET['ReasonCode'];
	$reason = null;
	if($reasonid)
		switch($reasonid)
		{
			case 'B01':
				$reason = 'Declined by Issuing Bank';
				break;
			case 'B02':
				$reason = 'Card Expired';
				break;
			case 'B03':
				$reason = 'Card Lost or Stolen';
				break;
			case 'B04':
				$reason = 'Card on Negative List';
				break;
			case 'F01':
				$reason = 'Blocked by Anti Fraud System Level 1 - Velocity';
				break;
			case 'F02':
				$reason = 'Blocked by Anti Fraud System Level 2 - Geo Technology';
				break;
			case 'F03':
				$reason = 'Blocked by Anti Fraud System Level 3 - Blacklist';
				break;
			case 'F04':
				$reason = 'Blocked by Anti Fraud System Level 4 - Bayesian Probability';
				break;
			case 'F05':
				$reason = 'Blocked by Anti Fraud System Level 5 - Other';
				break;
			case 'H01':
				$reason = '3D Secure - Failed to Authenticate';
				break;
			case 'E01':
				$reason = 'Merchant Account Closed or Suspended';
				break;
			case 'E02':
				$reason = 'Routing Error';
				break;
			case 'E03':
				$reason = 'General Error';
				break;
			default:
				$reason = 'Other';
				break;
		}

	//make sure that user is not active
	$status = $db->Update("UPDATE `bx_member` SET `status`='chargeback', `expiration_date`=?, `expiration_reason`=?, `lifetime_revenue`=? WHERE `id`=?", array(time(), $reason, $lifetime_revenue, $info['id']));

*/
	//decline notice
	exit("OK");
}

else
{
	die("UNKNOWN_ACTION");
}

/*** disconnect ***/
unset($db);