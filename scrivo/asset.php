<?php
/* Copyright (c) 2012, Geert Bergman (geert@scrivo.nl)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "Scrivo" nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * $Id: index.php 866 2013-08-25 16:22:35Z geert $
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