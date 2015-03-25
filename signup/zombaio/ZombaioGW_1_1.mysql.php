<?php

/* **************************************************************
 *  File: ZombaioGW_1_1.mysql.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

//get this from the Zombaio admin panel
$ZombaioGWPass = "0TZFZJTZD5XCB7JX7FX4";


/***config***/
require_once "./_init.php";

if (@$_GET["ZombaioGWPass"] != $ZombaioGWPass)
{
	header("HTTP/1.0 401 Unauthorized");
	exit("<h1>Zombaio Gateway 1.1</h1><h3>Authentication failed.</h3>");
}


/*** connect ***/
$db = DB::getInstance();


/************************/
/*		  SIGNUP		*/
/************************/
if (@$_GET["Action"] == "user.add")
{
	$username  = Input::clean_single('g','username','NOHTML');
	$password  = Input::clean_single('g','password','NOHTML');

	if (empty($username))
		die("USER_DOES_NOT_EXIST");
	if (empty($password))
		die("** EMPTY PASSWORD **");

	$salt = Session::CreateToken();

	$address = (@$_GET['ADDRESS'] ? Input::clean_single('g','ADDRESS','NOHTML'):null);
	$postal = (@$_GET['POSTAL'] ? Input::clean_single('g','POSTAL','NOHTML'):null);
	$region = (@$_GET['REGION'] ? Input::clean_single('g','REGION','NOHTML'):null);
	$city = (@$_GET['CITY'] ? Input::clean_single('g','CITY','NOHTML'):null);
	$country = (@$_GET['COUNTRY'] ? Input::clean_single('g','COUNTRY','NOHTML'):null);

	//insert user location in db
	//check if location does not exist
	$location_id = $db->QuerySingleColumn("SELECT `id` FROM `bx_location` WHERE `location`=? AND `zipcode`=? AND `state`=? AND `city`=? AND `country_iso`=?", array(
		$address,$postal,$region,$city,$country
	));

	if(!$location_id)
	{
		$db->Update("INSERT INTO `bx_location` SET `location`=?, `zipcode`=?, `state`=?, `city`=?, `country_iso`=?", array(
			$address, $postal, $region, $city, $country
		));
		$location_id = $db->LastInsertId();
	}

	if(!$location_id) $location_id = null;

	//insert user email
	$email = (@$_GET['EMAIL'] ? Input::clean_single('g','EMAIL','NOHTML'):null);

	$db->Update("INSERT INTO `bx_member_email` (`id`,`email`,`no_emails`,`email_status`,`last_mailing`,`num_mailings`) VALUES (?,?,?,?,?,?)",array(
		NULL, $email, 0, 'unmailed', NULL, 0
	));

	$email_id = $db->LastInsertId();
	if(!$email_id) $email_id = null;

	$subscription_id = (@$_GET['SUBSCRIPTION_ID'] ? Input::clean_single('g','SUBSCRIPTION_ID','NOHTML'):null);
	$name_on_cc = (@$_GET['NAME_ON_CARD'] ? @Input::clean_single('g','NAME_ON_CARD','NOHTML'):null);
	$card_hash = (@$_GET['CardHash'] ? Input::clean_single('g','CardHash','NOHTML'):null);

	$firstname = (@$_GET['FIRSTNAME'] ? Input::clean_single('g','FIRSTNAME','NOHTML'):null);
	$lastname = (@$_GET['LASTNAME'] ? Input::clean_single('g','LASTNAME','NOHTML'):null);

	//need to be added as extra variables to the join form
	$birthdate = (@$_GET['birthdate'] ? Input::clean_single('g','birthdate','NOHTML'):null);
	$gender = (@$_GET['gender'] ? Input::clean_single('g','gender','NOHTML'):'unknown');

	//write to db
	$status = $db->Update("INSERT INTO `bx_member` (
		`id`,`username`,`password`,`salt`,`status`,`email_id`,`firstname`,
		`lastname`,`birthdate`,`gender`,`location_id`,`subscription_id`,
		`name_on_cc`,`card_hash`,`join_date`,`expiration_reason`,`num_rebills`
	) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", array(
		NULL, $username, md5($salt.$password), 'active', $email_id, $firstname,
		$lastname, $birthdate, $gender, $location_id, $subscription_id,
		$name_on_cc, $card_hash, time(), NULL, '0'
	));

	if($status)
		die("OK");
	else
		die("ERROR");
}

/************************/
/*	   CANCELLATION		*/
/************************/
elseif (@$_GET["Action"] == "user.delete")
{
	$username = Input::clean_single('g','username','NOHTML');

	$id = $db->QuerySingleColumn("SELECT `id` FROM `bx_member` WHERE `username`=?", array($username));

	if (!$id)	die("USER_DOES_NOT_EXIST");

	$reasonid = @$_GET['ReasonCode'];
	$reason = null;
	if($reasonid)
		switch($reasonid)
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

	//delete
	$status = $db->Update("UPDATE `bx_member` SET `status`='inactive',`expiration_date`=?, `expiration_reason`=? WHERE `id`=?", array(time(), $reason, $id));

	if($status)
		die("OK");
	else
		die("ERROR");
}

/************************/
/*		  REBILL		*/
/************************/
elseif (@$_GET["Action"] == "rebill")
{
	//rebill is merely an information call

	$subscription_id = (@$_GET['SUBSCRIPTION_ID'] ? Input::clean_single('g','SUBSCRIPTION_ID','NOHTML'):null);

	//increase the rebill counter if rebill successfull
	if(@$_GET['Success'] == 1)
	{
		$info = $db->Row("SELECT `num_rebills`,`lifetime_revenue` FROM `bx_member` WHERE `subscription_id`=?", array($subscription_id));

		$info['num_rebills']++;
		$info['lifetime_revenue'] += (@$_GET['Amount'] ? Input::clean_single('g','Amount','NOHTML'):0);

		$db->Update("UPDATE `bx_member` SET `num_rebills`=?,`lifetime_revenue`=? WHERE `subscription_id`=?", array(
			$info['num_rebills'], $info['lifetime_revenue'], $subscription_id
		));
	}
}

/************************/
/*	   CHARGEBACK		*/
/************************/
elseif (@$_GET["Action"] == "chargeback")
{
	$username = Input::clean_single('g','Username','NOHTML');

	$id = $db->QuerySingleColumn("SELECT `id` FROM `bx_member` WHERE `username`=?", array($username));
	if (!$id)	die("USER_DOES_NOT_EXIST");

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

	$reasonid = @$_GET['ReasonCode'];
	$reason = null;
	if($reasonid)
		switch($reasonid)
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

	//set status to chargeback
	$status = $db->Update("UPDATE `bx_member` SET `status`='chargeback',`expiration_date`=?, `expiration_reason`=? WHERE `id`=?", array(time(), $reason, $id));

	if($status)
		die("OK");
}

/************************/
/*		 DECLINE		*/
/************************/
elseif (@$_GET["Action"] == "declined")
{
	$username = Input::clean_single('g','Identifier','NOHTML');
	$card_hash = Input::clean_single('g','CardHash','NOHTML');

	$info = $db->Row("SELECT `id`,`lifetime_revenue` FROM `bx_member` WHERE `username`=? AND `card_hash`=?", array($username, $card_hash));
	if (!$info)	die("USER_DOES_NOT_EXIST");

	$transaction_amount = (@$_GET['Amount'] ? Input::clean_single('g','Amount','NOHTML'):0)

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

	if($status) //received chargeback notice
		die("OK");
}


else
{
	die("UNKNOW_ACTION");
}

/*** disconnect ***/
unset($db);