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
 * $Id: CacheSettings.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Folder;
use \Scrivo\Str;

/**
 * The CacheSettings class implements an action for setting the cache settings
 * of a Scrivo file folder.
 */
class CacheSettings extends Action {

	/**
	 * In this action the folder with the given id is retrieved, its cache
	 * settings set and saved.
	 */
	function doAction() {

		$asset = Folder::fetch(
			$this->context, Request::post("assetId", Request::TYPE_INTEGER));

		$opt = Request::post("opt", Request::TYPE_STRING);

		if ($opt->equals(new Str("expire"))) {
			$asset->cacheHeaderSettings = (object)array(
				"setting" => new Str("expires"),
				"timePeriod" => Request::post("period", Request::TYPE_INTEGER),
				"timeUnit" => Request::post("timeunit", Request::TYPE_STRING)
			);
		} else if ($opt->equals(new Str("nocache"))) {
			$asset->cacheHeaderSettings = (object)array(
				"setting" => new Str("no-cache"));
		} else {
			$asset->cacheHeaderSettings = (object)array(
				"setting" => new Str("last-modified"));
		}

		$asset->update();

		$this->setResult(self::SUCCESS);
	}
}

?>