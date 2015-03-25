<?php
/* TEMPLATE CONFIGURATION */

	$themename 		= 'Default Theme';
	$themeversion	= '1.0';

/**
*	MODEL THUMBNAIL SIZE (for the manual thumbnail upload)
*
*/
	$_config['model_thumbnailsizes'] = array(0 => array('width' => 300, 'height' => 300));

/**
*	VIDEO UPDATE THUMBNAIL SIZE (for the manual thumbnail upload)
*
*/
	$_config['videoset_thumbnailsizes'] = array(0 => array('width' => 120, 'height' => 180));

/**
*	PICTURE UPDATE THUMBNAIL SIZE (for the manual thumbnail upload)
*
*/
	$_config['pictureset_thumbnailsizes'] = array(0 => array('width' => 120, 'height' => 180));

/*----------------------------------------*/

/**
*	PICTURE THUMBNAIL SIZE (for update thumbnail generation)
*
*	Following is the definition of the thumbnail size that all pictures
*	in a new update will be resized to.
*	These thumbnails size is used in the galleries (/set.php)
*	if you change this, you will have to rethumb all existing galleries and videos
*
*	@default:	array('width' => 122, 'height' => 200)
*/
	$_config['picturegrab_thumbnailsize'] = array('width' => 122, 'height' => 200);

/**
*	VIDEO THUMBNAIL SIZE (for framegrabs)
*
*	Following is the definition of the video thumbnail size of an update.
*	These thumbnail sizes are for example used in /video.php
*
*	@default:	array('width' => 122, 'height' => 200)
*/
	$_config['videograb_thumbnailsize'] = array('width' => 200, 'height' => 122);

?>