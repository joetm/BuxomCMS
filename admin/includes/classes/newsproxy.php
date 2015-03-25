<?php

error_reporting(E_ALL & ~E_NOTICE & ~8192);

header('Content-Type: text/xml; charset=utf-8');

if ($result = fetch_file_via_socket('http://buxomcms.com/news.xml?v=' . VERSION . '&id='. $license .', array('type' => '')))
{
	echo $result['body'];
}
else
{
	echo 'Error';
}
