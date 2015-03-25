<?php
	//configuration variables
	//get this from the Zombaio admin panel

/*multiple accounts setup*/

	//Zombaio secret passphrase
	//if you have two accounts with Zombaio
	//and if you want to allow automatic switching between
	//the accounts based on the user's locale
	$gwpass['EUR']  = "0TZFZJTZD5XCB7JX7FX4";
	$gwpass['USD']  = "6CBCA6P99I2Y4U7166U6";

	//merchant ids
	$merchant_id['EUR']  = "62763456";
	$merchant_id['USD']  = "62792560";

	//form urls
	$formurl['EUR'] = "https://secure.zombaio.com/?287649262.1271820.ZOM";
	$formurl['USD'] = "https://secure.zombaio.com/?287652502.1268607.ZOM";

	$default_currency = 'EUR';

/*single account setup*/

	//if you only have one account, remove the above and uncomment the following lines
/*
	$gwpass  = "XXXXXXXXXXXXXXXXXXXX";
	$merchant_id  = "12345678";
	$formurl = "https://secure.zombaio.com/?123456789.1234567.ZOM";
*/
