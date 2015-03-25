<?php

/* **************************************************************
 *  File: Twitter.php
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
	 * Twitter user
	 */
	private $username = '';

	/**
	 * Twitter user password
	 */
	private $password = '';

	/**
	 * User-agent
	 */
	private $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)';

	/**
	 * Twitter response type
	 * JSON (requires PHP 5.2 or the json pecl module) or XML
	 * xml | json
	 */
	private $type = 'xml';

	/**
	 * Headers
	 * More discussion at http://tinyurl.com/3xtx66
	 */

	//these headers are optional
	//they can be left empty as they are
	private $headers = array(
			'Expect: ',
			'X-Twitter-Client: ',
			'X-Twitter-Client-Version: ',
			'X-Twitter-Client-URL: '
		);

	/**
	 * Response
	 */
	private $responseInfo = array();

	/**
	 * @var boolean
	 */
	 private $suppress_response_code = false;

	/**
	 * @var boolean
	 */
	 private $debug = false;

/**************************************************/

	/**
	 * Set user data and options
	 */
	public function __construct()
	{
		$options = Config::GetDBOptions(array('twitter_user','twitter_pass'));

		$this->username = $options['twitter_user'];
		$this->password = $options['twitter_pass'];

		$this->debug = true;
	}

	/**
	 * Send update to Twitter
	 * @param string $status
	 * Note: total length of the status update must be 140 chars or less.
	 * @return string|boolean
	 */
	public function Update($status)
	{
		if( !in_array($this->type, array( 'xml','json')))
			return false;

		$request = 'http://twitter.com/statuses/update.' . $this->type;
		//$status = $this->shorturl($status);

		$postargs = array( 'status' => $status );

		return $this->objectify( $this->process($request, $postargs) );
	}

	/**
	 * Get @ replies
	 * @param integer Optional. Paging of tweets. Number specifies which page of results
	 * @param string $since (HTTP-formatted date) Optional.  Narrows the resulting list of direct messages to just those sent after the specified date.
     * @param integer $since_id Optional. Returns results posted that have an ID greater than $since_id
     * @return string
     **/
	public function getReplies( $page = false, $since = false, $since_id = false )
	{
	    if( !in_array( $this->type, array( 'xml','json','rss','atom' ) ) )
	        return false;

	    $args = array();
	    if( $page )
	        $args['page'] = (int) $page;
	    if( $since )
	        $args['since'] = (string) $since;
	    if( $since_id )
	        $args['since_id'] = (int) $since_id;

	    $qs = '';
	    if( !empty( $args ) )
	        $qs = $this->_glue( $args );

	    $request = 'http://twitter.com/statuses/replies.' . $this->type . $qs;
	    return $this->objectify( $this->process( $request ) );
	}


	/**
	 * Destroy a tweet
	 * @param integer $id Required.
	 * @return string
	 **/
	public function deleteStatus( $id )
    {
        if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

        $request = 'http://twitter.com/statuses/destroy/' . (int) $id . '.' . $this->type;
        return $this->objectify( $this->process( $request, true ) );
    }


	/**
	 * Send an unauthenticated request to Twitter for the public timeline.
	 * Returns the last 20 updates by default
	 * @param boolean|integer $sinceid Returns only public statuses with an ID greater of $sinceid
	 * @return string
	 */
	public function publicTimeline( $sinceid = false )
	{
	    if( !in_array( $this->type, array( 'xml','json','rss','atom' ) ) )
	        return false;

        $qs='';
        if( $sinceid !== false )
            $qs = '?since_id=' . intval($sinceid);
        $request = 'http://twitter.com/statuses/public_timeline.' . $this->type . $qs;

		return $this->objectify( $this->process($request) );
	}

	/**
	 * Send an authenticated request to Twitter for the timeline of authenticating user.
	 * Returns the last 20 updates by default
	 * @param boolean|integer $id Specifies the ID or screen name of the user for whom to return the friends_timeline. (set to false if you want to use authenticated user).
	 * @param boolean|integer $since Narrows the returned results to just those statuses created after the specified date.
	 * @deprecated integer $count. As of July 7 2008, Twitter has requested the limitation of the count keyword. Therefore, we deprecate
	 * @return string
	 */
	public function userTimeline($id=false,$count=20,$since=false,$since_id=false,$page=false)
	{
	    if( !in_array( $this->type, array( 'xml','json','rss','atom' ) ) )
	        return false;

	    $args = array();
	    if( $id )
	        $args['id'] = $id;
	    if( $count )
	        $args['count'] = (int) $count;
	    if( $since )
	        $args['since'] = (string) $since;
	    if( $since_id )
	        $args['since_id'] = (int) $since_id;
	    if( $page )
	        $args['page'] = (int) $page;

	    $qs = '';
	    if( !empty( $args ) )
	        $qs = $this->_glue( $args );

        if( $id === false )
            $request = 'http://twitter.com/statuses/user_timeline.' . $this->type . $qs;
        else
            $request = 'http://twitter.com/statuses/user_timeline/' . rawurlencode($id) . '.' . $this->type . $qs;

		return $this->objectify( $this->process($request) );
	}

	/**
	 * Returns a single status, specified by the id parameter below.  The status's author will be returned inline.
	 * @param integer $id The id number of the tweet to be returned.
	 * @return string
	 */
	public function showStatus( $id )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

        $request = 'http://twitter.com/statuses/show/'.intval($id) . '.' . $this->type;
		return $this->objectify( $this->process($request) );
    }

    /**
	 * Returns the authenticating user's friends, each with current status inline.  It's also possible to request another user's friends list via the id parameter below.
	 * @param integer|string $id Optional. The user ID or name of the Twitter user to query.
	 * @param integer $page Optional.
	 * @return string
	 */
	public function friends( $id = false, $page = false )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

        $args = array();
	    if( $id )
	        $args['id'] = $page;
	    if( $page )
	        $args['page'] = (int) $page;

	    $qs = '';
	    if( !empty( $args ) )
	        $qs = $this->_glue( $args );

	    $request = ( $id ) ? 'http://twitter.com/statuses/friends/' . $id . '.' . $this->type . $qs : 'http://twitter.com/statuses/friends.' . $this->type . $qs;
		return $this->objectify( $this->process($request) );
	}

	/**
	 * Returns the authenticating user's followers, each with current status inline.
	 * @param integer $page Optional.
	 * @return string
	 */
	public function followers( $page = false )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

        $request = 'http://twitter.com/statuses/followers.' . $this->type;
        if( $page )
            $request .= '?page=' . (int) $page;

		return $this->objectify( $this->process($request) );
	}

	/****** Friendships ******/

	/**
	 * Checks to see if a friendship already exists
	 * @param string|integer $user_a Required. The username or ID of a Twitter user
	 * @param string|integer $user_b Required. The username or ID of a Twitter user
	 * @return string
	 */
	public function isFriend( $user_a, $user_b )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

		$qs = '?user_a=' . rawurlencode( $user_a ) . '&amp;' . rawurlencode( $user_b );
		$request = 'http://twitter.com/friendships/exists.' . $this->type . $qs;
		return $this->objectify( $this->process($request) );
	}

	/**
	 * Sends a request to follow a user specified by ID
	 * @param integer|string $id The twitter ID or screenname of the user to follow
	 * @param boolean $notifications Optional. If true, you will recieve notifications from the users updates
	 * @return string
	 */
	public function followUser( $id, $notifications = false )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

		$request = 'http://twitter.com/friendships/create/' . (int) $id . '.' . $this->type;
		if( $notifications )
		    $request .= '?follow=true';

		return $this->objectify( $this->process($request) );
	}

	/**
	 * Unfollows a user
	 * @param integer|string $id the username or ID of a person you want to unfollow
	 * @return string
	 */
	public function leaveUser( $id )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

		$request = 'http://twitter.com/friendships/destroy/' . $id . '.' . $this->type;
		return $this->objectify( $this->process($request) );
	}

	/**
	 * Sends a new direct message to the specified user from the authenticating user.  Requires both the user
	 * and text parameters below.
	 * @param string|integer Required. The ID or screen name of the recipient user.
	 * @param string $user The text of your direct message.  Be sure to URL encode as necessary, and keep it under 140 characters.
	 * @return string
	 */
	public function sendDirectMessage( $user, $text )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

        $request = 'http://twitter.com/direct_messages/new.' . $this->type;
        $postargs = 'user=' . rawurlencode($user) . '&text=' . rawurlencode($text);

		return $this->objectify( $this->process($request, $postargs) );
	}

	/****** Account ******/

	/**
	 * Updates delivery device
	 * @param string $device Required. Must be of type 'im', 'sms' or 'none'
	 * @return string
	 */
	public function updateDevice( $device )
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

		if( !in_array( $device, array('im','sms','none') ) )
			return false;

		$qs = '?device=' . $device;
		$request = 'http://twitter.com/account/update_delivery_device.' . $this->type . $qs;
		return $this->objectify( $this->process( $request ) );
	}

	/**
	 * Rate Limit API Call. Sometimes Twitter needs to degrade. Use this non-ratelimited API call to work your logic out
	 * @return integer|boolean
	 */
	public function ratelimit()
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;
		$request = 'http://twitter.com/account/rate_limit_status.' . $this->type;
		return $this->objectify( $out );
	}

	/**
	 * Rate Limit statuses (extended). Provides helper data like remaining-hits, hourly limit, reset time and reset time in seconds
	 * @deprecated
 	 */
	public function ratelimit_status()
	{
		return $this->ratelimit();
	}

	/*** TESTS ***/

	/**
	 * Detects if Twitter is up or down. Chances are, it will be down. ;-) Here's a hint - display CPM ads whenever Twitter is down
	 * @return boolean
	 */
	public function twitterAvailable()
	{
	    if( !in_array( $this->type, array( 'xml','json' ) ) )
	        return false;

		$request = 'http://twitter.com/help/test.' . $this->type;
		if( $this->objectify( $this->process($request) ) == 'ok' )
			return true;

		return false;
	}

	/****** Private and Helpers Methods ******/

	/**
	 * Uses the http://is.gd API to produce a shortened URL. Pluggable by extending the twitter class
	 * @param string $url The URL needing to be shortened
	 * @return string
	 */
	public static function shorturl( $url )
	{
		// Using is.gd because it's good
		$request = 'http://is.gd/api.php?longurl=' . $url;
		return $this->process( $request );
	}

	/**
	 * Internal function where all the juicy curl fun takes place
	 * this should not be called by anything external unless you are
	 * doing something else completely then knock youself out.
	 * @access private
	 * @param string $url Required. API URL to request
	 * @param string $postargs Optional. Urlencoded query string to append to the $url
	 */
	public function process($url,$postargs=false)
	{
	    $url = ( $this->suppress_response_code ) ? $url . '&suppress_response_code=true' : $url;
		$ch = curl_init($url);
		if($postargs !== false)
		{
			curl_setopt ($ch, CURLOPT_POST, true);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }

		if($this->username !== false && $this->password !== false)
			curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        if( $this->debug ) :
            curl_setopt($ch, CURLOPT_HEADER, true);
        else :
            curl_setopt($ch, CURLOPT_HEADER, false);
        endif;
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($ch);

        $this->responseInfo=curl_getinfo($ch);
        curl_close($ch);


        if( $this->debug ) :
            $debug = preg_split("#\n\s*\n|\r\n\s*\r\n#m", $response);
            echo'<pre>' . $debug[0] . '</pre>'; exit;
        endif;

        if( intval( $this->responseInfo['http_code'] ) == 200 )
			return $response;
        else
            return false;
	}

	/**
	 * Function to prepare data for return to client
	 * @access private
	 * @param string $data
	 */
	public function objectify( $data )
	{
		if( $this->type ==  'json' )
			return json_decode( $data );

		else if( $this->type == 'xml' )
		{
			if( function_exists('simplexml_load_string') ) :
			    $obj = simplexml_load_string( $data );
			endif;
			return $obj;
		}
		else
			return false;
	}

	/**
	 * Function to piece together a cohesive query string
	 * @access private
	 * @param array $array
	 * @return string
	 */
	public function _glue( $array )
	{
		$query_string = '';
		foreach( $array as $key => $val ) :
			$query_string .= $key . '=' . rawurlencode( $val ) . '&';
		endforeach;

		return '?' . substr( $query_string, 0, strlen( $query_string )-1 );
	}

} // Twitter class