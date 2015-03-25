<?php

/* **************************************************************
 *  File: Feedbuilder.class.php
 *  Version: 1.0
 *  Copyright © 2010-2011 BuxomCMS.com - All Rights Reserved.
 *  ---------------- BUXOMCMS IS NOT FREE SOFTWARE ----------------
 *  This file may not be redistributed in whole or significant part.
 * **************************************************************/

/**
* RSS Feed Builder
*
*/
class FeedBuilder
{

/**
* Build
*
*/
	public static function Build()
	{
		//get action
		$action = Zend_Registry::get('action');

		//get data
		$data = Zend_Registry::get('data');

		//create feed
		$feed = new Zend_Feed_Writer_Feed;

		//feed meta data
		$feed->setTitle(__sitename . " " . ucwords($action));
		$feed->setLink(Path::Get('url:site'));
		$feed->setGenerator( __sitename );
		$feed->setCopyright('(c) '.date('Y')." ".__sitename);
		$feed->setFeedLink(rtrim(Path::Get('url:site'), '/').$_SERVER["PHP_SELF"].'?type=atom', 'atom');
		$feed->setFeedLink(rtrim(Path::Get('url:site'), '/').$_SERVER["PHP_SELF"], 'rss');
		$feed->addAuthor(array(
		    'name'  => __sitename,
		    'email' => Config::GetDBOptions('email'),
		    'uri'   => Path::Get('url:site'),
		));
		$feed->setDateModified( time() );
		//end feed meta data


		switch ($action){ //models, updates or comments

		case 'models':

				foreach ($data as $m){
					$entry = $feed->createEntry();
					$entry->setTitle( $m['modelname'] );
					$entry->setLink(Path::Get('url:site/model') ."/".$m['slug']);
					$entry->setDateModified( $date );
					$entry->setDescription($m['description']);
					$entry->setContent($m['description']);
					$feed->addEntry($entry);
				} //foreach

			break;

		case 'updates':

			foreach ($data as $u){
				$entry = $feed->createEntry();
				$entry->setTitle($u['title']);

				$destination = ($u['type']=='videoset')?'video':'set';

				$entry->setLink(Path::Get('url:site') .'/'.$destination.".php?id=".$u['id']);
				$entry->setDateModified( time() );
				$entry->setDateCreated( time() );
				$entry->setDescription($u['description']);
				$entry->setContent($u['description']);
				$feed->addEntry($entry);
			} //foreach

			break;

		case 'comments':

			foreach ($data as $c){
				$entry = $feed->createEntry();
				$entry->setTitle($c['name']);

				$entry->setLink(Path::Get('url:site').'/'.$c['id']);
				$entry->addAuthor(array(
				    'name'  => $c['name']
				));

				//format date for feed

				$entry->setDateModified( strtotime('2010-02-03T18:32:15-05:00') );
				$entry->setDateCreated( strtotime('2010-02-03T18:32:15-05:00') );
				$entry->setDescription($c['content']);
				$entry->setContent($c['content']);
				$feed->addEntry($entry);
			} //foreach

			break;

		} //switch type


		return $feed;


	} //Builder function

} //class
