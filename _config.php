<?php

/****************************************************************/
/*                        Configuration                         */
/****************************************************************/

/*--------------------------------------------------------------*/
/*                         Site Config                          */
/*--------------------------------------------------------------*/

/**
*	SITE ADDRESS
*
*	@example:	"http://yourdomain.com"
*/
	$_config['__siteurl']  = "http://yourdomain.com";

/*--------------------------------------------------------------*/
/*                       Database Config                        */
/*--------------------------------------------------------------*/

/**
*	DATABASE HOST
*
*	@example: localhost
*	@example: yourserver.hosting.net:3306
*/
	$_config['db']['__dbhost'] = "localhost";
/**
*	DATABASE USERNAME
*/
	$_config['db']['__dbuser'] = "bcuser";
/**
*	DATABASE PASSWORD
*/
	$_config['db']['__dbpass'] = "passwordhere";
/**
*	DATABASE NAME
*/
	$_config['db']['__dbname'] = "buxom_cms";

/*--------------------------------------------------------------*/
/*                         Admin Account                        */
/*--------------------------------------------------------------*/

/**
*	ADMIN DIRECTORY
*
*	@default:	"admin"
*/
	$_config['__admindir'] = 'admin'; //folder of admin directory

/**
*	ADMIN AREA NAVIGATION TOOLTIPS
*	(Leave the tooltips on during first use.)
*
*	@default: true
*/
	$_config['showadmintooltips'] = true;

/*--------------------------------------------------------------*/
/*                      Directories Setup                       */
/*--------------------------------------------------------------*/

//note: ANY changes you make here must be represented by the physical file structure.
//if you change the thumbnail directory, you need to rename that directory, etc...
//if you make changes and your database already contains entries, the database will need to be updated manually as well.

/**
*	relative path to members directory
*
*	@default: '/members'
*/
	$_config['__memberdir'] = '/members';

/**
*	url to members directory
*
*	@default: $_config['__siteurl'] . $_config['__memberdir']
*	(http://yourdomain.com/members)
*/
	$_config['__memberurl'] = $_config['__siteurl'] . $_config['__memberdir'];

/**
*	absolute path to members directory
*
*	@default: __sitepath . $_config['__memberdir']
*/
	$_config['__memberpath'] = __sitepath . $_config['__memberdir'];


/**
*	relative path to the member pictures directory
*	(relative to member directory)
*
*	@default: '/pics'
*/
	$_config['__picdir'] = 'pics';

/**
*	relative path to the member video directory
*	(relative to member area directory)
*
*	@default: '/videos'
*/
	$_config['__videodir'] = 'videos';

/**
*	relative path to the model thumbnails
*	(relative to member directory)
*
*	@default: '/models'
*/
	$_config['__modeldir'] = 'models';


/**
*	relative path to the freely accessible pictures and trailers directory
*	(relative to webroot (__siteurl))
*
*	@default: "/free"
*/
	$_config['__freedir'] = '/free';

/**
*	relative and absolute path to thumbnail directory
*
*	For the url, you can use a relative path like "/thumbs"
*	(for example if you use a content protection software like strongbox.)
*	Or add the siteurl: __siteurl."/thumbs"
*
*	@example: $_config['__thumbpath'] = __sitepath.'/thumbs/'
*	@example: $_config['__thumburl']  = '/thumbs/'
*/
	$_config['__thumbdir']  = '/thumbs';


/**
*	directory containing the template files and callback scripts for credit card processing
*	(relative to webroot)
*
*	@default: "/signup"
*/
	$_config['_processor_scripts'] = '/signup';

/**
*	ADMIN DIRECTORY
*
*	@default: "/admin"
*/
	$_config['__admindir'] = '/admin';

/*--------------------------------------------------------------*/
/*                     PAGE GET VARIABLES                       */
/*--------------------------------------------------------------*/

// by default, updates are accessed like this:
// for picture updates:
// http://domain.com/set/slug
// for video updates:
// http://domain.com/video/slug
// for model:
// http://domain.com/model/slug

// if you make changes here, you also need to change the .htaccess rewrite rules

/**
*	PICTURE SET SLUG
*	used in the file system AND the database
*
*	@default: "set"
*/
	$_config['__url_set'] = 'set';

/**
*	VIDEO SET SLUG
*	used in the file system AND the database
*
*	@default: "video"
*/
	$_config['__url_video'] = 'video';

/**
*	MODEL SLUG
*	used in the file system AND the database
*
*	@default: "model"
*/
	$_config['__url_model'] = 'model';

/*--------------------------------------------------------------*/
/*                       Caching Setup                          */
/*--------------------------------------------------------------*/

//cache path is defined in _init.php

/**
*	MEMCACHED CONFIGURATION
*
*	@default:	(commented out)
*/

/*
	$_config['memcache']['memcacheserver'][$i]		= '127.0.0.1';
	$_config['memcache']['memcacheport'][$i]		= 11211;
	$_config['memcache']['memcachepersistent'][$i]		= true;
	$_config['memcache']['memcacheweight'][$i]		= 1;
	$_config['memcache']['memcachetimeout'][$i]		= 1;
	$_config['memcache']['memcacheretry_interval'][$i]	= 15;
*/

//note: admin area is not cached.
//note: The header stats are expensive and cached with the __elementcache time

/**
*	SITEWIDE PAGE CACHING
*
*	Note: the caching time should not exceed your update frequency.
*
*	@default: 7200 (= 2 hours)
*/
	define('__pagecache', false);

/**
*	CACHE TIME FOR PAGE ELEMENTS
*
*	If you make changes to any elements that are cached (e.g. translations),
*	then do not forget to clear the cache directory.
*	Note: the caching time should not exceed your update frequency.
*
*	@default: 7200 (= 2 hours)
*/
	define('__elementcache', 5000);

/**
*	HEADER STATS CACHING
*
*	Set this to zero to turn caching off for header site statistics (not recommended!).
*	Note: the caching time should not exceed your update frequency.
*
*	@default: 86400 (= 24 hours)
*/
	# the caching time should not exceed your update frequency
	define('__headerstatscache', 86400);

/**
*	CACHING FOR RSS FEEDS
*
*	Set this to zero to turn feed caching off.
*
*	@default: 7200 (= 2 hours)
*/
	define('__cachefeed', false);

/**
*	TEMPLATE CACHE TIME
*
*/
	define('__tpl_cache_time', 0);

/*--------------------------------------------------------------*/
/*               Image, Video and Thumbnail Config              */
/*--------------------------------------------------------------*/

/**
*	ALLOWED MIME TYPES
*
*	@default:	array('jpg','jpeg','gif','png')
*/
	$_config['image_extensions'] = array('jpg','jpeg','gif','png');

/**
*	ALLOWED VIDEO TYPES
*
*	@default:	array('flv','f4v','wmv','mpg','mpeg','mp4','m4v','mov','qt','ogg','ogv','webm')
*/
	$_config['video_extensions'] = array('flv','f4v','wmv','mpg','mpeg','mp4','m4v','mov','qt','ogg','ogv','webm');

/*--------------------------------------------------------------*/
/*                  Conversion and Framegrabbing                */
/*--------------------------------------------------------------*/

/**
*	IMAGE CONVERSION LIBRARY
*	and
*	PATH TO IMAGEMAGICK DIRECTORY
*
* 	Imagemagick is highly recommended.
* 	You could run into memory problems with GD and high-resolution images.
* 	If ImageMagick is used, version 6.3.8-3+ is required.
*
* 	@param:		string	'ImageMagick' or 'GD'
* 	@default:	string	'ImageMagick'
* 	@default:	'/usr/bin/'
*/
	$_config['imageprocessing'] = 'ImageMagick';
	$_config['_imagemagickpath'] = 'D:\Appserv\ImageMagick/';

/**
*	PATH TO MPLAYER EXECUTABLE (for video frame grabbing)
*
*	Mplayer will be prefered over ffmpeg.
*	FFmpeg (if present) is only used to get video information. It is not used to extract screenshots.
*	For video frame extraction, you need to have mplayer with jpeg support installed.
*
* 	@default:	'/usr/local/bin/mplayer'
*/
	$_config['_mplayerpath'] = 'D:\Appserv\MPlayer\mplayer.exe';

/**
*	PATH TO FFMPEG EXECUTABLE (for video info)
*
* 	@default:	'/usr/local/bin/ffmpeg'
*/
	$_config['_ffmpegpath'] = 'D:\Appserv\FFmpeg\ffmpeg.exe';

/**
*	METADATA INJECTION with Yamdi
*
*	Automatically inject metadata into flv videos?
*	Enter the path to the yamdi executable here to enable this feature.
*	Otherwise leave blank or set to false.
*
* 	@param:		'<path>' | '' | false
* 	@default:	false
*/
	$_config['_yamdipath'] = 'D:\Appserv\yamdi\yamdi.exe';

/*--------------------------------------------------------------*/
/*                            Ratings                           */
/*--------------------------------------------------------------*/

/**
*	RATING SYSTEM
*
*	By default, a star rating system is used.
*	Alternatively, you can choose a "thumbs up/down" rating.
*	Both system are compatible. The star rating is divided in half.
*	Everything above the middle becomes a "thumbs up", everything
*	below the middle of the scale becomes a "thumbs down" rating.
*	A "thumbs up" is placed in the middle of the upper half of the scale,
*	a "thumbs down" respectively in the middle of the lower half.
*	For a star rating scale from 0...5 this means:
*		thumbs down rating = 1.25 stars
*		thumbs up rating   = 3.75 stars
*
* 	@default:	$_config['rating']['type'] = 'stars';
* 	@alternative:	$_config['rating']['type'] = 'updown';
*/
	$_config['rating']['type'] = 'stars';

/**
*	MINIMUM AND MAXIMUM RATINGS
*
*	By default, the cms uses a zero to five star rating system.
*
* 	@default:	$_config['rating']['min'] = 0;
* 	@default:	$_config['rating']['max'] = 5;
* 	@default:	$_config['rating']['step'] = 0.5;
*/
	$_config['rating']['min'] = 0;
	$_config['rating']['max'] = 5;
	$_config['rating']['step'] = 0.5;

/**
*	COUNT OF DECIMAL PLACES FOR RATINGS
*
* 	@default:	2
*/
	$_config['rating_decimal'] = 2;

/*--------------------------------------------------------------*/
/*                         Locale Config                        */
/*--------------------------------------------------------------*/

/**
*	SERVER TIME ZONE
*
*	Fixes php strict standard warnings.
*	Can be removed if strict error reporting is not used.
*
* 	@default:	'America/New_York'
*/
	date_default_timezone_set('America/New_York');

/**
*	DEFAULT COUNTRY for updates
*
*	Must match exactly the country in database table 'bx_country'.
*	Leave blank for '-undefined-'
*
* 	@param:		'<Country Name>' | ''
* 	@default:	'United States of America'
*/
	$_config['default_country'] = "United States of America";

/**
*	FORMAT OF DATE
*
*	Must match exactly the country in database table 'bx_country'.
*	Leave blank for '-undefined-'
*
* 	@param:		string	PHP date string
* 	@default:	'Y-m-d'
*/
	$_config['date_string'] = "Y-m-d";

/**
*	FORMAT OF DATETIME
*
* 	@default:	"Y-m-d H:i:s"
*/
	$_config['datetime_string'] = "Y-m-d H:i:s";


// the script can use a translation file based on the browser settings of the user
// check this link for a list of possible locales:
// http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/languages_and_territories.html
// invalid combinations are not supported

/**
*	L10N DETECTION SETTING
*
*	@param: 'auto' | 'browser' | 'environment' | false
*
*	'auto'		tries to detect the locale of the browser and falls back to the host locale
*	'browser'	works with the browser locale setting
*	'environment'	works with the information which is provided by the host server
*	false		turns locale detection off
*
* 	@default:	"auto"
*/
	$_config['l10n_detection'] = 'auto';

/**
*	L10N DEFAULT LOCALE (language_REGION)
*
*	Used when the locale detection fails.
*
* 	@default:	"en_US" (-> uses language file en_US.ini)
*/
	$_config['default_locale'] = 'en_US';

/**
*	Default Currency
*
*/
	$_config['default_currency'] = 'USD';

/*--------------------------------------------------------------*/
/*                      Mandatory Fields                        */
/*--------------------------------------------------------------*/

/**
*	MANDATORY FORM FIELDS ON THE NEWMODEL PAGE
*
*	You can add more to this array.
*	Available options:
*	'title' (= modelname), 'slug', 'birthdate', 'gender', 'location', 'state',
*	'zipcode','country', 'LocLatLng', 'rating', 'description',
*	'tags', 'realname', 'aliases', 'idurl', 'miscurl', 'notes'
*	Removing crucial parts (like 'slug') can cause problems if input is missing.
*
*	@example:	array('title', 'slug', 'birthdate', 'gender')
*/
	$_config['mandatory_model'] = array('title', 'slug', 'gender');

/**
*	REMOVE NEWMODEL FORM FIELDS
*
*	You can remove form fields from the newmodel page by adding the form field names to this array.
*	If you want to remove the 2257 info, add '2257' to the array
*
*	@example:	$_config['form_remove_model'] = array('state','zipcode');
*/
	$_config['form_remove_model'] = array();

/**
*	MANDATORY FORM FIELDS ON THE NEWUPDATE PAGE
*
*	You can add more to this array.
*	available options: ...
*	Removing crucial parts (like 'slug' and 'models') can cause problems on missing input.
*
*	@default:	 array('videodirectory', 'videourl', 'picturefolder', 'title', 'slug', 'models')
*/
	$_config['mandatory_update'] = array('videodirectory', 'videourl', 'picturefolder', 'title', 'slug', 'models');

/**
*	REMOVE NEWUPDATE FORM FIELDS
*
*	You can remove form fields from the newupdate page by adding the form field names to this array.
*	If you want to remove the 2257 info, add '2257' to the array
*
*	@example:	$_config['form_remove_update'] = array('state','zipcode');
*/
	$_config['form_remove_update'] = array();

/*--------------------------------------------------------------*/
/*                           Misc. Config                       */
/*--------------------------------------------------------------*/

/**
*	COOKIE PATH AND HOST
*
*	@default:	$_config['cookie_path'] = '/';
*	@default:	$_config['cookie_domain'] = 'yoursite.com';
*/
	$_config['cookie_path'] = '/';
	$_config['cookie_domain'] = 'yoursite.com';

/**
*	SITE-WIDE DEBUGGING
*
*	Displays debug information (progress on processes, number of queries, load time, etc.)
*/
	$_config['debug'] = true;

/**
*	SHOW THE QUERIES IN THE DEBUG OUTPUT?
*
*	Never (ever!) set this to true on a production site!
*/
	define("__showdebugqueries", true);

/***Akismet API Key***/
//This feature is not available, yet, because
//an akismet commercial license is expensive and
//the focus is on helping webmasters start out.
//Upcoming versions will allow  you to automatically check
//comments for spam.
//you can get an Akismet key here: http://akismet.com/commercial/
//if you leave this empty, all comments need to be moderated before going live.
//	$_config['AkismetAPIKey'] = '7cf905ddac55';

/**
*	SITE-WIDE GZIP COMPRESSION
*
*	requires the php zlib extension.
*/
	//ob_start("ob_gzhandler");

/**
*	File Permission
*
* 	@default:	0777
*/
	$_config['__filePermission'] = 0777;
/**
*	Folder Permission
*
* 	@default:	0777
*/
	$_config['__folderPermission'] = 0777;

/**
*	SITE CHARSET
*
*	used in headers
*/
	define("__CHARSET", "iso-8859-1");

/**
*	CAPTCHA CONFIG
*
*/
	$_config['_captcha'] = array(
		'font' => './img/fonts/times.ttf',
		'Expiration' => 3000,
		'ImgDir' => './img/captcha/',
		'imgUrl' => '/img/captcha/',
		'wordLen' => 5,
		'fsize' => 50,
		'width' => 150,
		'height' => 50,
		'dotNoiseLevel' => 45,
		'lineNoiseLevel' => 4
	);

/*--------------------------------------------------------------*/
/*             Additional Translations (optional)               */
/*--------------------------------------------------------------*/

/*
# you can create new translations
# comment out the lines below, create a translation ini file and set the right locale here
//$translate = Zend_Registry::get('translate');
//$translate->addTranslation(__sitepath.'/languages/de_DE.ini', 'DE');
*/
