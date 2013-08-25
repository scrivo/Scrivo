<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: asset.php 840 2013-08-19 22:17:25Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

use \Scrivo\Config;
use \Scrivo\Session;
use \Scrivo\String;
use \Scrivo\Request;
use \Scrivo\File;
use \Scrivo\Folder;

require_once("Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

$cfg = new Config();
$session = new Session($cfg->SESSION_PREFIX."scrivo");

// If there's no user set in the session, assume the anonymous user.
if (!isset($session->userId)) {
	$session->userId = \Scrivo\User::ANONYMOUS_USER_ID;
}
$context = new \Scrivo\Context($cfg, $session->userId);

$id = 0;
// trick for flv files in jw player
// file=187300.flv&streamer=http://test.intraxs.nl/nisb-portal/scrivo/asset.php
$file = Request::get("file", Request::TYPE_STRING, new String(""));
if (!$file->equals(new String(""))) {
	if ($file->substr(-4)->equals(new String(".flv"))) {
		$id = intval((string)$file->substr(5, -4));
	}
}

if (!$id) {
	$id = Request::get("id", Request::TYPE_INTEGER);
}

$f = File::fetch($context, $id);
$d = Folder::fetch($context, $f->parentId);

$tzGmt = new DateTimeZone('GMT');
$dtFmt ="D, d M Y H:i:s \G\M\T";

$f->dateModified->setTimezone($tzGmt);
$tmpMod = $f->dateModified->format($dtFmt);

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
	if ($tmpMod == $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
		header("HTTP/1.1 304 Not Modified");
		exit;
	}
}

header("Pragma: "); // IE Problemen with downloading assets over https

if ($d->cacheHeaderSettings->setting->equals(new String("expires"))) {

	$secs = array("MINUTE" => 60, "HOUR" => 3600, "DAY" => 86400,
		"WEEK" => 604800, "MONTH" => 2635200, "YEAR" => 31557600);

	$offset = $d->cacheHeaderSettings->timePeriod *
		$secs[(string)$d->cacheHeaderSettings->timeUnit];

	$dt = new DateTime("+{$offset} seconds", $tzGmt);

	header("Cache-Control: ");
	header("Last-Modified: ".$tmpMod);
	header("Expires: ".$dt->format($dtFmt));

} else if ($d->cacheHeaderSettings->setting->equals(new String("no-cache"))) {

	$dt = new DateTime("now", $tzGmt);

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".$dt->format($dtFmt));
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);

} else {

	header("Cache-Control: ");
	header("Last-Modified: ".$tmpMod);
}

header("Content-Type: {$f->mimeType}");
header("Content-Disposition: inline; filename=\"{$f->title}\"");
header("Content-length: {$f->size}");

readfile($f->location);

?>