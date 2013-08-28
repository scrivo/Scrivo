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

/**
 * The admin interface controller.
 */

namespace ScrivoUi\DbConsole;

error_reporting(E_ALL);

$start = microtime();

session_start();

require_once("../Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

spl_autoload_register(function($class){
	return (substr($class, 0, 8) != "ScrivoUi") ?  false
		: include("../".str_replace("\\", "/", $class).".php");
});

include "actions.php";

$initialize = false;
try {
	$cfg = new \Scrivo\Config();
} catch (\Scrivo\SystemException $e) {
	$cfg = new \ScrivoUi\DbConsole\Lib\InitConfig();
}

$session = new \Scrivo\Session($cfg->SESSION_PREFIX."dbc");

// If there's no user set in the session, assume the anonymous user.
if (!isset($session->userId)) {
	$session->userId = \Scrivo\User::ANONYMOUS_USER_ID;
}

if (!isset($session->authenticated)) {
	$session->authenticated = false;
}

// If the current user is the admin user, ...
if ($session->userId == \Scrivo\User::PRIMARY_ADMIN_ID) {
	// ... check if the request came from one of the whitelisted IP adresses.
	if (!$cfg->ADMIN_IP_ADDRESSES->equals(new \Scrivo\String("*"))
			&& !$cfg->ADMIN_IP_ADDRESSES->contains(
				new \Scrivo\String($_SERVER["REMOTE_ADDR"]))
			&& !$cfg->ADMIN_IP_ADDRESSES->contains(new \Scrivo\String(
				// replace last digit with an * in remote_address
				preg_replace("/(\d+)$/i", "*", $_SERVER["REMOTE_ADDR"])))) {
		die("Authorization Error");
	}
}

// Get the action from the GET or POST variables, default to home ...
$actionId = \Scrivo\Request::request("a", \Scrivo\Request::TYPE_STRING,
	new \Scrivo\String("home"));

if ($cfg instanceof \ScrivoUi\DbConsole\Lib\InitConfig) {

	// No config file found: initialize Scrivo
	$ctx = new \ScrivoUi\DbConsole\Lib\InitContext();
	$session->userId = \Scrivo\User::ANONYMOUS_USER_ID;
	$session->authenticated = true;
	if ($actionId->indexOf(new \Scrivo\String("initialize.")) == -1) {
		$actionId = new \Scrivo\String("initialize.systemCheck");
	}

} else {

	try {

		if ($actionId->indexOf(new \Scrivo\String("initialize.")) >= 0) {
			$actionId = new \Scrivo\String("home");
		}
		$ctx = new \Scrivo\Context($cfg, $session->userId);

	} catch (\Scrivo\SystemException $e) {

		// No principal found: assume an empty instance.
		$ctx = new \ScrivoUi\DbConsole\Lib\InitContext();
		$session->userId = \Scrivo\User::ANONYMOUS_USER_ID;
		$session->authenticated = true;
		if ($actionId->indexOf(new \Scrivo\String("import.")) == -1) {
			$actionId = new \Scrivo\String("import.importInstanceForm");
		}

	}
}

$actionId = (string)$actionId;

// ... but reset it to login actions if not authenticated yet.
if (!$session->authenticated) {
	if (isset($_GET["key"])) {
		$actionId = "login_check";
	}
	if ($actionId != "login_check") {
		$actionId = "login";
	}
}

// Create the action ...
$act = \Scrivo\Action::create($ctx, $actions[$actionId],
	isset($session->userStatus)?$session->userStatus: 0, $session);

if ($act->type == \Scrivo\Action::VIEW) {

	$act->doAction();
	echo $act->getView();

} else if ($act->type == \Scrivo\Action::XHR) {
	try {
		$act->doAction();
		echo $act->getXhr();
	} catch (\Exception $e) {
		echo json_encode(array(
			"result"=>"ERROR",
			"data"=>$e->getMessage(),
			"trace" => $e->getTraceAsString()
		));
		die;
	}

} else if ($act->type == \Scrivo\Action::FORWARD) {

	try {
		$act->doAction();
	} catch (\Exception $e) {
		echo "<p><strong>Error:</strong> {$e->getMessage()}</br>";
		echo "<strong>Code:</strong> {$e->getCode()}</br>";
		echo "<strong>File:</strong> {$e->getFile()}</br>";
		echo "<strong>Line:</strong> {$e->getLine()}</br>";
		echo str_replace("\n", "<br>", $e->getTraceAsString())."</p>";
		die;
	}
	$act->forward();

} else if ($act->type == \Scrivo\Action::DOWNLOAD) {

	try {
		$act->doAction();
		header("Content-Description: File Download");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".
			$act->file->getFileName()."\"");
		header("Content-Transfer-Encoding: binary");
		//header("Content-Length: ".filesize($act->file->getFileName()));
		$act->file->outputData();
	} catch (\Exception $e) {
		echo "<p><strong>Error:</strong> {$e->getMessage()}</br>";
		echo "<strong>Code:</strong> {$e->getCode()}</br>";
		echo "<strong>File:</strong> {$e->getFile()}</br>";
		echo "<strong>Line:</strong> {$e->getLine()}</br>";
		echo str_replace("\n", "<br>", $e->getTraceAsString())."</p>";
		die;
	}

} else {

	// ... can't do much with an invalid action.
	echo("Error: unsupported action method");

}

//error_log("Script time ".(microtime()-$start));

?>
