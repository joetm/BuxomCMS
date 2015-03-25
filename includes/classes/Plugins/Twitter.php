<?php

/* **************************************************************
 *  File: Twitter.php
 *  OAuth Library
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

/**
* Twitter Class
*
*/
class Twitter
{

/**
* Constructor
*
*/
	public function __construct()
	{
		$options = Config::GetDBOptions(array('twitter_public_key','twitter_private_key'));

		$this->debug = true;
	}





} // Twitter class