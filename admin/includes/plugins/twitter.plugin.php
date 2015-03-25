<?php

/* **************************************************************
 *  File: twitter.plugin.php
 *  Version: 1.0
 *  Copyright 2010 BuxomCMS.com - All Rights Reserved.
 * **************************************************************/

//class name of plugin must be in the form of "<name>_plugin"
class twitter_plugin extends SocialPlugin {

	public $updateposting_possible = true;

	public $auth_type = "oauth";

	private static $configuration = array(
		'requestTokenUrl' => 'http://twitter.com/oauth/request_token',
		'authorizeUrl' => 'http://twitter.com/oauth/authorize',
		'accessTokenUrl' => 'http://twitter.com/oauth/access_token',
		'signatureMethod' => 'HMAC-SHA1',
		'version' => '1.0',
	);


	public static function PostUpdate()
	{

		//get this later from the DB!
		//array with configuration
		self::$configuration += array(
			'callbackUrl' => __siteurl.'/callback.php',
			'consumerKey' => 'consumerKeygoeshere', //later: get this from DB
			'consumerSecret' => 'consumerSecretgoeshere',
			'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
			//not used
			//	'api_key' => 'apikeyhere',
		);












/**
 * Default Index Controller using Zend's MVC implementation.
 *
 * @author Raymond J. Kolbe <rkolbe@white-box.us>
 *
class IndexController extends Zend_Controller_Action
{
    **
     * Login Action
     *
     * Performs user login through Twitter using OAuth.  If user's have not
     * granted our application access to their account, they will be sent to
     * Twitter to do so.  Once done, they will return to this page again so
     * that we can handle them (e.g. run our application for them).
     *
     * @param void
     * @return void
     *
    public function loginAction()
    {
        // Check to see if the user already has an OAuth access token
        if ($this->_session->access_token) {
            // You would redirect the user to the main part of your
            // application that they needed to be authenticated for.
        }

        // Configuration for OAuth. @see Zend_Oauth_Consumer
        $config = array(
            'signatureMethod' => 'HMAC-SHA1',
            'callbackUrl' => 'http://localhost/login',
            'requestTokenUrl' => 'http://twitter.com/oauth/request_token',
            'authorizeUrl' => 'http://twitter.com/oauth/authorize',
            'accessTokenUrl' => 'http://twitter.com/oauth/access_token',
            'consumerKey' => 'jDrJud90Jhg66whddj876',
            'consumerSecret' => 'UdjneHdyGsj90Bsg2UdjneHdyGsj90Bsg2'
        );

        $consumer = new Zend_Oauth_Consumer($config);

        // If we do not have a request token, generate one now
        if (!$this->_session->request_token) {
            $request_token = $consumer->getRequestToken();

            // Save the token for when the user returns to this page.
            // This will be used to get the user's access token.
            $this->_session->request_token = serialize($request_token);

            // Send the user off to Twitter to grant our application access
            $consumer->redirect();
            return;
        }

        // If we made it here, the user has been to Twitter to grant our
        // application access and now we must get an access token that
        // will allow us to make API calls on behalf of the user.
        $access_token = $consumer->getAccessToken($this->_request->getQuery(), unserialize($this->_session->request_token));

        // We no longer need the request token so remove it
        unset($this->_session->request_token);

        // Save to session so that reloading of this page will send the user
        // to your application main page or wherever you want them to go.
        $this->_session->access_token = serialize($access_token);

        // This line is very important. Since Zend_Service_Twitter does
        // not have support for OAuth (yet), this is how we get it to work.
        // All we are doing is making Zend_Service_Twitter use OAuth's
        // HTTP Client instance, which will automatically append the proper
        // OAuth query info to any Twitter service call we make from here
        // on out.
        Zend_Service_Twitter::setHttpClient($access_token->getHttpClient($config));

        // Username and password are passed in as null because we will not be
        // authenticating using a user/pass combo (which uses basic
        // authentication).
        $twitter = new Zend_Service_Twitter(null, null);

        // This is not required but shows you as an example that OAuth did
        // in fact work.
        $response = $twitter->account->verifyCredentials();

        // Your code goes here.  This would be the point you want to save
        // the access token to the database for later use and/or save a cookie
        // on the user's system.
    }
}
*/





	} //PostUpdate


	//this function must be present in each option social plugin
	public static function GetDetails($classname = null) {
		//overwrite the classname
		$classname = get_class();
		return parent::GetDetails($classname);
	} //GetDetails

} //class
