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
 * $Id: Crop.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\File;
use \Scrivo\String;
use \ScrivoUi\Editor\Lib\ImageUtilities;

/**
 * The Crop class implements an action for cropping and resizing a
 * Scrivo image file to a new Scrivo image file with the given dimensions.
 */
class Crop extends Action {

	/**
	 * In this action the Scrivo image file is loaded and cropped and resized
	 * based on the given parameters, a new Scrivo image file is created to
	 * save the result.
	 */
	function doAction() {

		$file = File::fetch(
			$this->context, Request::post("assetId", Request::TYPE_INTEGER));

		$filename = $file->location;

		// Get new sizes
		list($width, $height) = getimagesize($filename);

		$cw = round(Request::post("cropWidth", Request::TYPE_INTEGER) *
			$width / Request::post("originalWidth", Request::TYPE_INTEGER));
		$ch = round(Request::post("cropHeight", Request::TYPE_INTEGER) *
			$height / Request::post("originalHeight", Request::TYPE_INTEGER));

		$cl = round(Request::post("cropLeft", Request::TYPE_INTEGER) *
			$width / Request::post("originalWidth", Request::TYPE_INTEGER));
		$ct = round(Request::post("cropTop", Request::TYPE_INTEGER) *
			$height / Request::post("originalHeight", Request::TYPE_INTEGER));

		$newwidth = Request::post("newWidth", Request::TYPE_INTEGER);
		$newheight = Request::post("newHeight", Request::TYPE_INTEGER);

		// Load
		$thumb = imagecreatetruecolor($newwidth, $newheight);
		$source = ImageUtilities::imageCreateFromAsset($file);

		imagecopyresampled(
			$thumb, $source, 0, 0, $cl, $ct, $newwidth, $newheight, $cw, $ch);

		//create new image

		$name = $file->title;
		$pos = $name->indexOf(new String("."));
		if ($pos != -1) {
			$name = $name->substr(0, $pos);
		}

		$dr = new File($this->context);
		$dr->title = new String("{$name}_{$newwidth}x{$newheight}.jpg");
		$dr->mimeType = new String("image/jpeg");
		$dr->type = 1;
		$dr->parentId = $file->parentId;

		$dr->insert();

		$dr->location =
			new String("{$this->context->config->UPLOAD_DIR}/asset_{$dr->id}");
		imagejpeg($thumb, $dr->location);
		$dr->size = filesize($dr->location);

		$dr->update();

		$this->setResult(self::SUCCESS);
	}

}

?>