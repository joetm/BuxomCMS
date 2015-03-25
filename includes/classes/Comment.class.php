<?php

/* **************************************************************
 *  File: Comment.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* Comment Class
*
*/
class Comment
{

/**
* Check comment for blacklisted words
*
* @access	public
* @param	mixed	$comment
* @return	true : comment not checked or no blacklisted word found
* @return	false: blacklisted word found; comment should be discarded
*/
	public function check_comment(&$comment)
	{
		$options = Config::GetDBOptions(array('comment_use_blacklist','blacklist_words'));

		if($options['comment_use_blacklist'] != 1)
		return true; // comment can be used

		$blacklist = trim($options['blacklist_words']);

		if(!empty($blacklist))
		//there are words in the blacklist
		{

			$words = explode(",", $blacklist);

			foreach ( (array) $words as $word) {
				$word = trim($word);

				// Skip empty
				if ( empty($word) )
					continue;

				// escape '%'
				$word = preg_quote($word, '%');

				$pattern = "%$word%i";
				//false: comment should not be used
//				if ( preg_match($pattern, $author) ) return false;
//				if ( preg_match($pattern, $email) ) return false;
//				if ( preg_match($pattern, $url) ) return false;
				if ( preg_match($pattern, $comment) ) return false;
//				if ( preg_match($pattern, $user_ip) ) return false;
//				if ( preg_match($pattern, $user_agent) ) return false;
			}

		}

	} //check_comment

} //class