<?php

/* **************************************************************
 *  File: Merchant.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/* INFO: */
// this file contains a class to edit members on zombaio
// the following two methods are implemented so far:
// Cancel Subscription, Refund Transaction
//
//Cancel Subscription is called when you deactivate a member in the admin panel



//security
if(!defined('BX_CONTROL_PANEL')) die('Cannot be called directly.');


/**
* Merchant Class
*
*/
class Merchant
{
	const CANCEL = 'https://secure.zombaio.com/API/Cancel/?';
	const REFUND = 'https://secure.zombaio.com/API/Refund/?';

	private $currency = 'USD';


/**
* Cancel Subscription
*
* @access	public
* @param	int	$subscription_id
*
*/
	public function CALL_API($action, $id, $code){

		$code = intval($code);

		$action = strtoupper($action);

		/***database connect***/
		$db = DB::getInstance();

		//get the currency for the subscription so that we know what keys to use
		$currency = $db->Column("SELECT currency
				FROM `bx_member`
				WHERE subscription_id = ?", array(
				$id
		));
//		if(!$currency) return false;
		//currency must be EUR or USD
		$currency = $this->Check_Currency($currency);


		//get basic config variables for zombaio
		require_once Path::Get('path:site/signup').DIR_SEP.Config::GetDBOptions('processor')."/_config.php";

//		global $gwpass, $merchant_id;
		if(is_array($gwpass)) $gwpass = $gwpass[$currency];
		if(is_array($merchant_id)) $merchant_id = $merchant_id[$currency];

		//make the call to Zombaio

		$return = false;

/*
		//https stream wrapper?
		$https = (in_array('https', stream_get_wrappers()) ? true : false);

		if(!$https)
		{
			$data = http_build_query(
				array('SUBSCRIPTION_ID' => $id, 'MERCHANT_ID' => $merchant_id,
				'ZombaioGWPass' => $gwpass, 'ReasonCode' => $code)
				);

			$context_options = array (
				'http' => array (
				'method' => 'POST',
				'header'=> "Content-type: application/x-www-form-urlencoded\r\n"
				. "Content-Length: " . strlen($data) . "\r\n",
				'content' => $data
	          		  )
	        		);
			$context = stream_context_create($context_options);
		}
*/


		switch($action)
		{
			case 'CANCEL':

//				if($https)
//				{
					$status = intval(file_get_contents($url));
//				}
//				else
//				{
//					$url = self::CANCEL;
//					$fp = fopen($url, 'r', false, $context);

					//...







//				}

				switch($status)
				{
					case 0: //unknown error
					case 2: //wrong merchant id or ZombaioGWPass
					case 3: //unknown subscription id
					case 4: //system unable to process request
						$return = false;
					break;
					case 1: //cancellation successful
						$return = true;
					break;
				}
			break;

			case 'REFUND':

				$url = self::REFUND.'TRANSACTION_ID='. $id. '&MERCHANT_ID='.$merchant_id.'&ZombaioGWPass='. $gwpass .'&Refund_Type='. $code;

				$status = intval(file_get_contents($url));

				switch($status)
				{
					case 0: //unknown error
					case 2: //wrong merchant id or ZombaioGWPass
					case 3: //unknown transaction id
					case 4: //system unable to process request
					case 5: //refund rejected (insufficient funds)
					case 6: //refund rejected by bank
						$return = false;
					break;
					case 1: //refund successful
						$return = true;
					break;
				}

			break;

		}

		return $return;
	}

/**
* Switch Currency
* Zombaio only has EUR and USD
* We check the value stored in DB here
*
* @access	private
* @param	varchar(3)	$currency
*
*/
	private function Check_Currency($currency){

		$currency = strtoupper($currency);

		switch($currency)
		{
			case 'EUR':
			case 'USD':
			break;
			default:
				global $default_currency;
				if(!empty($default_currency))
					$currency = $default_currency;
				else
					$currency = 'USD';
			break;
		}

		return $currency;
	}

} //Merchant Class
