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
 * $Id: CropCanvas.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\File;

/**
 * The CropCanvas class implements an action for retrieving the size of the
 * crop canvas based on the size of the image to crop: the crop canvas should
 * not become larger than some maximum size regardless of the size of the
 * image to crop.
 */
class CropCanvas extends Action {

	/**
	 * In this action the size of the given image is determined and used to
	 * calculate the canvas size for the crop dialog.
	 */
	function doAction() {

		$file = File::fetch(
			$this->context, Request::get("assetId", Request::TYPE_INTEGER));

		$s = getimagesize($file->location);

		$canvasMaxWidth = $s[0] < 600 ? $s[0] : 600;
		$canvasMaxHeight = $s[1] < 500 ? $s[1] : 500;

		$rat = $s[0]/$s[1];
		$ratc = $canvasMaxWidth/$canvasMaxHeight;
		if ($ratc < $rat) {
			$canvaswidth = $canvasMaxWidth;
			$canvasheight = round($canvaswidth/$rat);
		} else {
			$canvasheight = $canvasMaxHeight;
			$canvaswidth = round($canvasheight*$rat);
		}

		$this->setResult(
			self::SUCCESS, array("w" => $canvaswidth, "h" => $canvasheight));
	}
}

?>