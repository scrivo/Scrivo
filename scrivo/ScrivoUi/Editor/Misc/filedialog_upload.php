<?php
/* Copyright (c) 2011, Geert Bergman (geert@scrivo.nl)
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
 * $Id: cropper_backgroundimage.php 786 2013-08-09 13:26:51Z geert $
 */

/**
 * Simple script to upload a file to the Scrivo asset repository.
 */

use Scrivo\I18n;
use Scrivo\File;
use Scrivo\Request;
use Scrivo\Context;
use Scrivo\Str;

session_start();

function fix_mime($filename, $sugg_mime) {
	$sugg_mime = str_replace("x-citrix-", "", $sugg_mime);
	if (strtolower(substr($filename, -4)) == ".doc") {
		return new Str("application/msword");
	}
	return new Str($sugg_mime);
}

require_once("../../../Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

spl_autoload_register(function($class){
	return (substr($class, 0, 8) != "ScrivoUi") ?  false
		: include("../../../".str_replace("\\", "/", $class).".php");
});

header("X-UA-Compatible: IE=edge");

$cfg = new \Scrivo\Config();
$session = new \Scrivo\Session($cfg->SESSION_PREFIX."edt");

if (!isset($session->authenticated)) {
	die;
}

$ctx = new Context($cfg, $session->userId);
$i18n = new I18n($ctx->config->UI_LANG);

$dir = Request::request(
	"dirId", Request::TYPE_INTEGER, $ctx->config->ROOT_FOLDER_ID);

if (!is_numeric($dir)) {
	$dir = $ctx->config->ROOT_FOLDER_ID;
}

$i = 0;

$len = count($_FILES["userfile"]["name"]);

//$si = new search_index($scrivo_conn);

$overwriteId = Request::request("assetId", Request::TYPE_INTEGER, 0);

if (!$overwriteId) {

	for ($i = 0; $i < $len; $i++) {
		if (is_uploaded_file($_FILES["userfile"]["tmp_name"][$i])) {
			
			$uf = new File($ctx);
			$proposed = new Str($_FILES["userfile"]["name"][$i]);
			$uf->title = new Str($_FILES["userfile"]["tmp_name"][$i]);
			$uf->mimeType =
				fix_mime($uf->title, $_FILES["userfile"]["type"][$i]);
			$uf->size = $_FILES["userfile"]["size"][$i];
			$uf->type = File::TYPE_FILE;
			$uf->parentId = $dir;
			
			try {
				
				$uf->insert();
				$uf->location = 
					new Str("{$ctx->config->UPLOAD_DIR}/asset_{$uf->id}");
				if (!move_uploaded_file(
						$_FILES["userfile"]["tmp_name"][$i], $uf->location)) {
					File::delete($ctx, $uf->id);
					throw new \Exception("Permission error");
				}
				$uf->title = $proposed;
				$uf->update();
				
				if (!$uf->title->equals($proposed)) {
					echo "<script>alert(\"{$i18n["File renamed as"]}".
						" '{$uf->title}'\")</script>";
				}
				
				// $si->add_asset(
				// $uf->id, $uf->title, $uf->mimetype, $uf->location);
				
			} catch (\Exception $e) {
				echo "<script>alert(\""
					.$i18n["Error uploading the file"]
					."\")</script>";
				
			}

		}
	}

} else {

	if (is_uploaded_file($_FILES["userfile"]["tmp_name"][0])) {

		$uf = File::fetch($ctx, $overwriteId);
		//$old_mime_type = $uf->mimetype;
		$proposed = new Str($_FILES["userfile"]["name"][$i]);
		$uf->title = $proposed;
		$uf->mimeType =
			fix_mime($uf->title, $_FILES["userfile"]["type"][$i]);
		$uf->size = $_FILES["userfile"]["size"][$i];
		$uf->type = File::TYPE_FILE;
		$uf->parentId = $dir;

		try {

			$uf->location =
				new Str("{$ctx->config->UPLOAD_DIR}/asset_{$uf->id}");
			if (!move_uploaded_file(
					$_FILES["userfile"]["tmp_name"][$i], $uf->location)) {
				throw new \Exception("Permission error");
			}
			$uf->update();
			
			if (!$uf->title->equals($proposed)) {
				echo "<script>alert(\"{$i18n["File renamed as"]}".
				" '{$uf->title}'\")</script>";
			}
				
			// $si->delete_by_id($uf->id, $old_mime_type);
			// $si->add_asset(
			//    $uf->id, $uf->title, $uf->mimetype, $uf->location);
					
		} catch (\Exception $e) {
			echo "<script>alert(\""
				.$i18n["Error uploading the file"]
				."\")</script>";
		}
		
	}
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//name_en">
<html>
<head>
<script type="text/javascript">

var err = {};

window.onload = function() {
	if (parent.uploaded) parent.uploaded(err);
}

</script>
</head>
</html>