<?php
/*======================================================================*\
|| #################################################################### ||
   							  CP STATTISTICS
|| #################################################################### ||
\*======================================================================*/

//define database details:
/** MySQL hostname. Only use localhost if you run this script on the same server as the database. */
$STATS_DB_HOST = 'localhost';

/** The name of the database for the stats database (use a different database than vbulletin) */
$STATS_DB_NAME = 'cpstats';

/** MySQL database username */
$STATS_DB_USER = 'mungolo';

/** MySQL database password */
$STATS_DB_PASSWORD = 'C81K8A4f';


// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

// #################### DEFINE IMPORTANT CONSTANTS #######################
define('THIS_SCRIPT', 'stats');
define('CSRF_PROTECTION', true);

// ################### PRE-CACHE TEMPLATES AND DATA ######################
// get special phrase groups
$phrasegroups = array();

// get special data templates from the datastore
$specialtemplates = array();

// pre-cache templates used by all actions
$globaltemplates = array('CP_STATS');

// pre-cache templates used by specific actions
$actiontemplates = array();

// ######################### REQUIRE BACK-END ############################
//$temp = getcwd();
//chdir('./forum/');
require_once('./global.php');
//chdir($temp);


// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

/*
if (!$vbulletin->userinfo['userid'] OR !($permissions['forumpermissions'] & $vbulletin->bf_ugp_forumpermissions['canview']))
{
	print_no_permission();
}

*/

// main page:

// start navbar
$navbits = array(
	'usercp.php' . $vbulletin->session->vars['sessionurl_q'] => $vbphrase['user_control_panel'],
	'private.php' . $vbulletin->session->vars['sessionurl_q'] => $vbphrase['private_messages']
);

// select correct part of forumjump
$frmjmpsel['pm'] = 'class="fjsel" selected="selected"';
construct_forum_jump();

	$templatename = 'CP_STATS';


//sobald genug daten vorhanden sind koennen die folgenden Teile entfernt werden!
	//variables
	$totalthreads = array_fill(0,30,'44000');
	$totalposts = array_fill(0,30,'449000');
	$dates = array_fill(0,30,'0');
	$members = array_fill(0,30,'13000');
//	$avg_age = array_fill(0,30,'35');

/*
	//longterm
	$dates2 = array();
	for($i=0;$i<=29;$i++){
			$j = $i + 1;
			$dates2[$i] = "2008, 0, $j";
	}
*/

//query data
$con = mysql_connect($STATS_DB_HOST,$STATS_DB_USER,$STATS_DB_PASSWORD);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db($STATS_DB_NAME, $con);

//************select cp_stats***************
$result = mysql_query("SELECT * FROM CP_Stats ORDER BY ID DESC LIMIT 30");

$i = 0;

while($row = mysql_fetch_array($result))
  {

	$dates[$i] = $row['date'];

/*
//longterm
		$ingredients = explode("-",$row['date']);
		$year = $ingredients[0];
		$month = $ingredients[1] - 1;
		$day = $ingredients[2];
	$dates2[$i] = $year . ", " . $month . ", " . $day;
*/
	$totalposts[$i] = $row['totalposts'];
	$totalthreads[$i] = $row['totalthreads'];
	$members[$i] = $row['members'];
//	$avg_age[$i] = $row['avgage'];

	$i = $i + 1;

  }
//************select cp_stats***************

//************select fatforums_stats***************
$result = mysql_query("SELECT totalposts,totalthreads FROM FF_Stats ORDER BY ID DESC LIMIT 30");

$i = 0;

//zero overwrite!
$fftotalposts = array_fill(0, 30, '903773');
$fftotalthreads = array_fill(0, 30, '71314');

while($row = mysql_fetch_array($result))
  {

//	$ffdates[$i] = $row['date'];

	$fftotalposts[$i] = $row['totalposts'];
	$fftotalthreads[$i] = $row['totalthreads'];

	$i = $i + 1;

  }

//************select fatforums_stats***************

mysql_close($con);


//echo $dates[0];



// build navbar
$navbits = construct_navbits(array('' => $vbphrase['user_control_panel']));
eval('$navbar = "' . fetch_template('navbar') . '";');

eval('print_output("' . fetch_template('CP_STATS') . '");');


/*======================================================================*\
|| ####################################################################
|| # Downloaded: 03:44, Fri Jul 11th 2008
|| # CVS: $RCSfile$ - $Revision: 26399 $
|| ####################################################################
\*======================================================================*/
?>
