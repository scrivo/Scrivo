<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: ca.php 840 2013-08-19 22:17:25Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

$agent = $_SERVER['HTTP_USER_AGENT'];

function instr($haystack, $needle) {
	return (strpos($haystack, $needle) !== false);

}

if ((!instr($agent, "MSIE 10.0") && !instr($agent, "MSIE 9.0") &&
		!instr($agent, "MSIE 8.0") && !instr($agent, "MSIE 7.0") &&
		!instr($agent, "MSIE 6.0"))	|| instr($agent, "Opera")) {
	if (!instr($agent, "Gecko")) {
		echo("MSIE 6.0 or up");
		die;
	}
}

$i = str_replace("//", "", str_replace("..", "", $_GET["i"]));
$j = str_replace("ca.php", "", $_SERVER["SCRIPT_FILENAME"]);
$path = $j.$i;
$path_parts = pathinfo($path);

switch ($path_parts["extension"]) {
case "css":
	header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Expires: Mon, 31 Dec 2017 05:00:00 GMT"); // Date in the future
	header("Content-type: text/css");
	break;
case "jpg":
	header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Expires: Mon, 31 Dec 2017 05:00:00 GMT"); // Date in the future
	header("Content-type: image/jpeg");
	break;
case "png":
	header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Expires: Mon, 31 Dec 2017 05:00:00 GMT"); // Date in the future
	header("Content-type: image/png");
	break;
case "gif":
	header("Last-Modified: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Expires: Mon, 31 Dec 2017 05:00:00 GMT"); // Date in the future
	header("Content-type: image/gif");
	break;
case "js":
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); // HTTP/1.0
	header("Content-type: text/javascript");
	break;
default:
	echo("Intruder alert");
	die;
}

readfile($path);

?>