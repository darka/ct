<?php

function getRandomString($length) {
	$chars="qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
	$rndstring = "";
	for ($i = 1; $i <= $length; $i++) {
		$c = rand(0, strlen($chars) - 1);
		$rndstring .= $chars[$c];
	}
	return $rndstring;
}

function getCurrentDate() {
	return gmdate("Y-m-d G:i:s");
}

function datetimeToPubDate($datetime) {
	//Sat, 07 Sep 2002 00:00:01 GMT
	$time = strtotime($datetime);
	$pubdate = date("D, d M Y H:i:s", $time);
	$pubdate .= " GMT";
	return $pubdate;
}

function getHostURL() {
	$host = $_SERVER['HTTP_HOST'];
	$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	return "http://$host$uri/";
}

?>
