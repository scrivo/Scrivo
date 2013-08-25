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
 * $Id: FolderProperties.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Folder;
use \Scrivo\String;

/**
 * The FolderProperties class implements an action for retrieving the properties
 * of a Scrivo folder.
 */
class FolderProperties extends Action {

	/**
	 * In this action the folder with the given id is retrieved and its property
	 * values returned. If no id was given, empty property values are returned.
	 */
	function doAction() {

		$assetId = Request::get("assetId", Request::TYPE_INTEGER, 0);

		if ($assetId) {
			$folder = Folder::fetch($this->context, $assetId);
		} else {
			$folder = new Folder($this->context);
		}

		$opt = "lastmod";
		$period = "";
		$timeunit = "";

		if ($folder->cacheHeaderSettings->setting->equals(
				new String("expires"))) {

			$opt = "expire";
			$period = $folder->cacheHeaderSettings->timePeriod;
			$timeunit = (string)$folder->cacheHeaderSettings->timeUnit;

		} else if ($folder->cacheHeaderSettings->setting->equals(
				new String("no-cache"))) {

			$opt = "nocache";

		}

		$res = array(
			"assetId" => $folder->id,
			"title" => (string)$folder->title,
			"opt" => $opt,
			"period" => $period,
			"timeunit" => $timeunit
		);

		$this->setResult(self::SUCCESS, $res);
	}
}

?>