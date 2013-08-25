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
 * $Id: filedialog_upload.php 866 2013-08-25 16:22:35Z geert $
 */

header("X-UA-Compatible: IE=edge");

include "../../modules/constants.php";
include "../../modules/authenticate.php";
//$login_user_id = 2;
include "../../modules/db_util.php";
include "../../modules/asset_class.php";
include "../../modules/search_class.php";

function fix_mime($filename, $sugg_mime) {
	$sugg_mime = str_replace("x-citrix-", "", $sugg_mime);
	if (strtolower(substr($filename, -4)) == ".doc") {
		return "application/msword";
	}
	return $sugg_mime;
}

$dir = getpost("dir_id", 2);
if (!is_numeric($dir)) {
	$dir = 2;
}

$i = 0;
$c = 0;

$len = count($_FILES["userfile"]["name"]);

$si = new search_index($scrivo_conn);

$overwrite_id = getpost("asset_id", 0);

if (!$overwrite_id) {

	for ($i = 0; $i < $len; $i++) {
		if (is_uploaded_file($_FILES["userfile"]["tmp_name"][$i])) {
			$c = 1;
			$dr = new asset($scrivo_conn, $login_user_id);
			$dr->title = $_FILES["userfile"]["name"][$i];
			$dr->mimetype =
				fix_mime($dr->title, $_FILES["userfile"]["type"][$i]);
			$dr->size = $_FILES["userfile"]["size"][$i];
			$dr->type = 1;
			$dr->parent_id = $dir;
			$test = $dr->insert(true);

			if ($test != OK && $test != "E00016") {
				echo "<script>alert(\""
					.i18n("Fout bij het oploaden van het bestand")
					."\")</script>";
			} else {
				$dr->data = UPLOAD_DIR."/asset_".$dr->asset_id;
				move_uploaded_file(
					$_FILES["userfile"]["tmp_name"][$i], $dr->data);
				$test2 = $dr->update();
				if ($test2 != OK) {
					echo "<script>alert(\""
						.i18n("Fout bij het oploaden van het bestand")
						."\")</script>";
				}
				if ($test == "E00016") {
					echo "<script>alert(\""
						.i18n("Bestand hernoemd naar")
						." '{$dr->title}'\")</script>";
				}
				$si->add_asset(
					$dr->asset_id, $dr->title, $dr->mimetype, $dr->data);
			}
		}
	}

} else {

	if (is_uploaded_file($_FILES["userfile"]["tmp_name"][0])) {

		$c = 1;
		$dr = new asset($scrivo_conn, $login_user_id);
		$dr->load($overwrite_id);
		$old_mime_type = $dr->mimetype;
		$dr->title = $_FILES["userfile"]["name"][0];
		$dr->mimetype = fix_mime($dr->title, $_FILES["userfile"]["type"][0]);
		$dr->size = $_FILES["userfile"]["size"][0];
		$dr->type = 1;
		$dr->parent_id = $dir;
		$test = $dr->update(true);

		if ($test != OK && $test != "E00016") {
			echo "<script>alert(\""
				.i18n("Fout bij het oploaden van het bestand")
				."\")</script>";
		} else {
			$dr->data = UPLOAD_DIR."/asset_".$dr->asset_id;
			move_uploaded_file($_FILES["userfile"]["tmp_name"][0], $dr->data);
			$test2 = $dr->update();
			if ($test2 != OK) {
				echo "<script>alert(\""
					.i18n("Fout bij het uploaden van het bestand")
					."\")</script>";
			}
			if ($test == "E00016") {
				echo "<script>alert(\""
					.i18n("Bestand hernoemd naar")
					." '{$dr->title}'\")</script>";
			}

			$si->delete_by_id($dr->asset_id, $old_mime_type);
			$si->add_asset(
				$dr->asset_id, $dr->title, $dr->mimetype, $dr->data);
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
