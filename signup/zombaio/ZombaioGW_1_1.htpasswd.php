<?php
	//----------------------------------------------------------------
	// File Generated:  2010-06-06
	// Site Name:
	// Site ID:
	// System:          ZOA - Manual Installation V1.2
	//----------------------------------------------------------------
	// modded for BuxomCMS Integration
	//----------------------------------------------------------------

/***config***/
require_once "./_init.php";


	$cfgSiteName	= "Buxom Curves";
	$ZombaioGWPass	= "0TZFZJTZD5XCB7JX7FX4";

	//If Zombaio is used with other billing companies they might already
	//use a .htpasswd file. In that case, specify the exact location
	//of the shared .htpasswd file below. Otherwise, leave this empty.

	$passFile	= "";

	//-----------------------------------------------------------------
	//                DO NOT EDIT BELOW THIS LINE
	//-----------------------------------------------------------------

	if(empty($folderperms = Config::Get('__folderPermission')))
		$folderperms = 0777;


	if (@$_GET["ZombaioGWPass"] != $ZombaioGWPass) {
		header("HTTP/1.0 401 Unauthorized");
		echo "<h1>Zombaio Gateway 1.1</h1><h3>Authentication failed.</h3>";
		exit;
	}


	$cfgBadChars	= '`~!@#$%^&*()+=[]{};\'\\:"|,/<>? ';
	$cfgBadCharsE	= '`~!#$%^&*()+=[]{};\'\\:"|,/<>?, ';
	$cfgBadCharsR	= '`~!@#$%^&*()+=[]{};\'\\:"|,/<>?';





	$passPath	= Path::Get('path:site')."/Zombaio_Data";
	$memberPath	= Path::Get('path:member');

	if (empty($passFile)) {
		$passFile	= Path::Get('path:site')."/Zombaio_Data/.htpasswd";
	}

	$passauthFile	= Path::Get('path:site')."/Zombaio_Data/.htaccess";
	$authFile		= Path::Get('path:member')."/.htaccess";
	$cfghtpasswdEXE = 'C:\Program Files\Apache Group\Apache\bin\htpasswd.exe';


	$htpUser = array();


	function is_valid_string($string) {
	  global $cfgBadChars;

	  if (empty($string))
	    return true;

	  for ($i = 0; $i < strlen($cfgBadChars); $i++) {
	    if(strpos($string, $cfgBadChars[$i]) !== false)
	      return true;
	  }
	  return false;
	}
	function is_valid_email($string) {
	  global $cfgBadCharsE;

	  if (empty($string))
	    return false;

	  for ($i = 0; $i < strlen($cfgBadCharsE); $i++) {
	    if(strpos($string, $cfgBadCharsE[$i]) !== false)
	      return true;
	  }
	  return false;
	}
	function is_valid_realname($string) {
	  global $cfgBadCharsR;

	  if (empty($string))
	    return false;

	  for ($i = 0; $i < strlen($cfgBadCharsR); $i++) {
	    if (strstr($string, $cfgBadCharsR[$i]))
	      return true;
	  }
	  return false;
	}


	function read_passwd_file() {
	  global $cfgHTPasswd, $htpUser, $passFile;

	  $htpUser = array();

	  if (!($fpHt     = fopen($passFile, "r"))) {
	      echo "PASSWD_FILE_NOT_READABLE|Password file not readable!";
	      exit;
	  }
	  $htpCount = 0;
	  while (!feof($fpHt)) {
	    $fpLine = fgets($fpHt, 512);
	    $fpLine = trim($fpLine);
	    $fpData = explode(":", $fpLine);
	    $fpData[0] = trim($fpData[0]);
	    if (isset($fpData[1]))
		$fpData[1] = chop(trim($fpData[1]));

	    if (empty($fpLine) || $fpLine[0] == '#' || $fpLine[0] == '*'
	    ||	empty($fpData[0]) || empty($fpData[1]))
	      continue;

	    $htpUser[$htpCount]['username'] = $fpData[0];
	    $htpUser[$htpCount]['password'] = $fpData[1];
	    $htpCount++;
	  }
	  fclose($fpHt);

	  return;
	}

	function init_passwd_file() {
	  global $passFile;

	  if (!file_exists($passFile)) {
	  echo "PASSWD_FILE_NOT_EXIST|Password file does not exist!";
	  exit;
	  }
	  if (!is_readable($passFile)) {
	  echo "PASSWD_FILE_NOT_READABLE|Password file not readable!";
	  exit;
	  }
	  if (!is_writeable($passFile)) {
	  echo "PASSWD_FILE_NOT_WRITABLE|Password file not writable!";
	  exit;
	  }
	}

	function write_passwd_file() {
	  global $cfgHTPasswd, $htpUser, $passFile;



	  if (($fpHt = fopen($passFile, "w"))) {
	    for ($i = 0; $i < count($htpUser); $i++) {
	      if (!empty($htpUser[$i]['username']))
	        fwrite($fpHt, $htpUser[$i]['username'].":".
			      $htpUser[$i]['password']."\n");
	    }
	    fclose($fpHt);
	  }
	  else {
	      echo "PASSWD_FILE_NOT_READABLE|Password file not readable!";
	      exit;
	  }
	  return;
	}

	function random() {
	  srand ((double) microtime() * 1000000);
	  return rand();
	}

	function crypt_password($username, $password) {
	  global $cfghtpasswdEXE;

	  if (empty($password))
	    return "** EMPTY PASSWORD **";

	  if (strstr(strtoupper(PHP_OS), "WINNT") ||
	      strstr(strtoupper(PHP_OS), "WINDOWS")) {
	    $temp = exec("\"".$cfghtpasswdEXE."\" -nmb $username $password", $result, $retval);
	    if ($retval == 0) {
	        $data = explode(":", $result[0], 2);
	        return $data[1];
	    }
	    else
	        return "** ERROR **";
	  }
	  else {
	    $salt = random();
	    $salt = substr($salt, 0, 2);
	    return crypt($password, $salt);
	  }
	}

	function is_user($username) {
	  global $htpUser;

	  if (empty($username))
	    return false;

	  for ($i = 0; $i < count($htpUser); $i++) {
	    if ($htpUser[$i]['username'] == $username)
	      return true;
	  }
	  return false;
	}

	function who_is_user($username) {
	  global $htpUser;

	  if (empty($username))
	    return '999999999999999';

	  for ($i = 0; $i < count($htpUser); $i++) {
	    if ($htpUser[$i]['username'] == $username)
	      return $i;
	  }
	  return '999999999999999';
	}

	//check if member area exists
	if (!is_dir($memberPath)) {
		echo "PATH_DOES_NOT_EXIST|Path \"$memberPath\" does not exist!";
		exit;
	}


	if (@$_GET["Action"] == "create.htaccess") {

		// sätter full behörighet på path
		chmod(Path::Get('path:site'), $folderperms);



		//Kolla om auth fil finns
		if (file_exists($authFile)) {

		  echo "AUTH_FILE_EXIST|.htaccess file already exists in member dir!";
		  exit;

		}

		//Kollar om Zombaio dir finns
		if (file_exists($passPath)) {

			//Pass path finns, kolla om .htpasswd finns
			if (file_exists($passFile)) {


			  echo "PASSWD_FILE_EXIST|.htpasswd file already exists!";
			  exit;

			  }
		  }



		  // Zombaio dir finns ej, skapa detta

		  mkdir($passPath, $folderperms);



		  // Skapar speciell .htaccess för password dir
		 if (!($fp = @fopen($passauthFile, "w"))) {
		      echo "PASS_AUTH_FILE_ERROR|Could not open .htaccess file for writing";
		      exit;
		    }

		      $content  = "AuthName \"Protected Zombaio Data - No Access\"\n";
		      $content .= "AuthType Basic\n";
		      $content .= "AuthGroupFile /dev/null\n\n";
		      $content .= "<limit GET POST PUT DELETE>\n";
		      $content .= "deny from all\n";
		      $content .= "</limit>\n";

		     fwrite($fp, $content);
		     fclose($fp);


		//Skapar .htaccess för medlems katalog
		if (!($fp = @fopen($authFile, "w"))) {
		      echo "AUTH_FILE_ERROR|Could not open .htaccess file for writing";
		      exit;
		    }

		      $content  = "#ATTENTION BILLING COMPANIES!!!\n";
		      $content .= "#This site is using the shared .htpasswd file specified in AuthUserFile. Please use the same file when you set up an aditional billing system!\n";
		      $content .= "AuthUserFile ".$passFile."\n";
		      $content .= "AuthType Basic\n";
		      $content .= "AuthName \"$cfgSiteName\"\n\n";
		      $content .= "require valid-user\n";

		     fwrite($fp, $content);
		     fclose($fp);


		//Skapar .htpasswd
		if (!($fp = @fopen($passFile, "w"))) {
		      echo "PASSWD_FILE_ERROR|Could not open .htpasswd file for writing";
		      exit;
		    }

		      $content  = "";

		     fwrite($fp, $content);
		     fclose($fp);

		chmod($passFile, $folderperms);

		 echo "OK|OK";
	}

	else if (@$_GET["Action"] == "get.path") {

	//return path
	echo Path::Get('path:site');

	}
	else if (@$_GET["Action"] == "get.passfile") {

	//return pass file
	echo $passFile;

	}
	else if (@$_GET["Action"] == "user.add") {



	$username  = trim(@$_GET['username']);
	$password  = trim(@$_GET['password']);

		init_passwd_file();
	 	read_passwd_file();

	 	$userid = count($htpUser);
	      	$htpUser[$userid]['username'] = $username;
	      	$htpUser[$userid]['password'] = crypt_password($username, $password);
	      	write_passwd_file();
	      	read_passwd_file();
	      	# clean form
	      	$username = '';
	      	$realname = '';
	      	$email    = '';


	  echo "OK|User added!";
	  exit;

	}

	else if (@$_GET["Action"] == "user.delete") {

	 // delete user from password file
	$username  = trim(@$_GET['username']);

		//del_from_passwd_file($username);

	 	init_passwd_file();
	 	read_passwd_file();

	        if (!is_user($username)) {

	          echo "USER_DOES_NOT_EXIST|User does not exist!";
	  	  exit;

	        }

	$userid = who_is_user($username);

	   if ($userid == '999999999999999') {
	     echo "USER_DOES_NOT_EXIST|User does not exist!";
	     exit;
	   }

	    $htpUser[$userid]['username'] = '';
	    $htpUser[$userid]['password'] = '';
	    write_passwd_file($id);
	    read_passwd_file($id);



	  echo "OK|User deleted!";
	  exit;



	}

	else {

		      echo "UNKNOW_ACTION|UNKNOW_ACTION";
		      exit;

	}





	//-----------------------------------------------------------------------
	// COPYRIGHT (C) 2007 - ZOMBAIO PAYMENT SERVICES LTD, ALL RIGHTS RESERVED
	//-----------------------------------------------------------------------
?>