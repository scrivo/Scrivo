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
 * $Id: FolderUpdate.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Folder;
use \Scrivo\I18n;
use \Scrivo\Str;

/**
 * The FolerUpdate class implements an action for updating or creating
 * a Scrivo Folder.
 */
class FolderUpdate extends Action {

	/**
	 * In this action the folder with the given id is retrieved and if no id
	 * is given a new folder is created. Then the folder properties are set
	 * from posted data and the folder is saved.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$assetId = Request::post("assetId", Request::TYPE_INTEGER, 0);

		//TODO: check on unique file name

		if ($assetId) {

			$folder = Folder::fetch($this->context, $assetId);

			$folder->title = Request::post("title", Request::TYPE_STRING);

			$folder->update();

		} else {

			$folder = new Folder($this->context);

			$folder->title = Request::post("title", Request::TYPE_STRING,
				new Str($i18n["New folder"]));
			$folder->parentId = Request::post("dirId", Request::TYPE_INTEGER);

			$folder->insert();

		}

		$this->setResult(self::SUCCESS);
	}
}

?>