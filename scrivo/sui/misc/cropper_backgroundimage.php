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

header("X-UA-Compatible: IE=edge");

include "../../modules/constants.php";
include "../../modules/authenticate.php";
include "../../modules/db_util.php";

include "../../modules/asset_class.php";

include "../../asset_dialog/dlg_crop/function.imagecreatefromasset.php";

$asset_id = get("asset_id");
$doelbreedte = get("width");

$asset = new asset($scrivo_conn, 2);
$asset->load($asset_id);

$bronvanhetbestand = $asset->data;

$dimensions = getimagesize($bronvanhetbestand);

$bronbreedte = $dimensions[0];
$bronhoogte  = $dimensions[1];
$doelhoogte = ($bronhoogte * $doelbreedte) / $bronbreedte;
$doelhoogte = round($doelhoogte, 0);

$image = imagecreatefromasset($asset);

$destination = imagecreatetruecolor($doelbreedte, $doelhoogte);
imagecopyresampled($destination, $image, 0, 0, 0, 0, $doelbreedte,
	$doelhoogte, $bronbreedte, $bronhoogte);

header("content-type: image/jpeg");
imagejpeg($destination);

imagedestroy($image);
imagedestroy($destination);

?>