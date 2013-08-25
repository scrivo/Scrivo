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
 * $Id: FolderList.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Asset;
use \Scrivo\Folder;

/**
 * The FolderList class implements an action for creating a listing of the
 * sub-folders of a folder.
 */
class FolderList extends Action {

	/**
	 * In this action the children of the folder with the given id are
	 * retrieved and only the children of the folder type are returned.
	 */
	function doAction() {

		$assetId = Request::get("assetId", Request::TYPE_INTEGER, 0);

		$children = !$assetId ? array(Asset::fetch(
				$this->context, $this->context->config->ROOT_FOLDER_ID))
			: Asset::fetch($this->context, $assetId)->children;

		$list = array();
		foreach ($children as $c) {
			if ($c instanceof Folder) {

				$a = "filedialog.folderList";

				$url = "";
				foreach ($c->children as $cc) {
					if ($cc instanceof Folder) {
						$url =
						"{$this->context->config->WWW_ROOT}/scrivo/index.php".
							"?assetId={$c->id}&a={$a}";
						break;
					}
				}

				$list[] = array(
					"id" => $c->id,
					"pid" => $c->parentId,
					"title" => (string)$c->title,
					"childListUrl" => $url,
					"type" => $c->type
				);

			}
		}

		$this->setResult(self::SUCCESS, $list);
	}
}

?>