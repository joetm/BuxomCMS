<?php

/***page setup***/
$commentlimit = 6;
$taglimit = 30;
/***page setup***/

/***get variables***/
//if (!isset($_GET['id'])){
//	//redirect to videos page
//	header("HTTP/1.0 404 Not Found");
//	header('Location: /videos.php');
//}



/***get variables***/

/***database connect***/
$db = DB::getInstance();

	/***stats***/
	$stats = new Stats($tpl);
	/***stats***/

/***content query***/

//***update data***//


/*
	if (!$updates) {
		//Update is not a video!!!

//			die('Update is not a video!');

			header("HTTP/1.0 404 Not Found");
			header("Location: /join.php"); // Redirect browser

	}
*/






/*

//comments
$sql = "SELECT `id`,`name`,`content`,`date` FROM `bx_comments` WHERE `updateid` = '".$update->id."' AND `date` <= NOW() LIMIT ".$commentlimit;
$result = $db->query($sql);
	if (!$result) {
//	    die('Invalid query: ' . mysql_error());
	}
	else{
		$i = 0;
		$comments = array();
		while ($ergebnis = mysql_fetch_array($result, MYSQL_ASSOC)) {
		    $comments[$i]['id'] = $ergebnis["id"];
		    $comments[$i]['name'] = $ergebnis["name"];
		    $comments[$i]['content'] = $ergebnis["content"];
		    $comments[$i]['date'] = $ergebnis["date"];
			$i++;
		}
	}

//tags
$sql = "SELECT `id`,`content` FROM `tags` WHERE `bx_update` = '".$update->id."' LIMIT ".$taglimit;
$result = $db->query($sql);
	if (!$result) {
//	    die('Invalid query: ' . mysql_error());
	}
	else{
		$i = 0;
		$tagstemp = array();
		while ($ergebnis = mysql_fetch_array($result, MYSQL_ASSOC)) {
			    $tagstemp[$i]['id'] = $ergebnis["id"];
			    $tagstemp[$i]['content'] = $ergebnis["content"];
				$i++;
		}
		//remove last comma:
		$i = 0;
		$last = count($tagstemp) - 1;
		foreach ($tagstemp as $tag)
		{
				$tlink = "<a href='".Path::Get(url:site')."/tags.php?tag=".$tag['id']."' class='taglink'>".$tag['content']."</a>";
				if ($i != $last) $tlink .= ", ";
				$update->tags[] = $tlink;
				$i++;
		}
		unset($tagstemp);
	}

*/

/***content query***/


/***disconnect***/
unset($db);


/***TEMPLATE ASSIGNMENTS***/

$tpl->assign("comments", $comments);
