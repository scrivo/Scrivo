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
 * $Id: FolderTree.php 841 2013-08-19 22:19:47Z geert $
 */

 /**
 * Implementation of the ScrivoUi\Editor\Actions\FolderTree action class.
 */

namespace ScrivoUi\Editor\Actions;

use \Scrivo\Action;
use \Scrivo\Folder;
use \Scrivo\Request;

/**
 * The FolderTree class implements an action for retrieving the sub-folders of
 * a folder.
 */
class FolderTree extends Action {

	/**
	 * In this action the sub-folders of the folder with the given id are
	 * retrieved and only the root folder if no id was given.
	 */
	function doAction() {

		$res = array();
		$id = Request::get("assetId", Request::TYPE_INTEGER, 0);
		try {
			$folder = Folder::fetch($this->context, $id);
			foreach ($folder->children as $c) {
				if ($c instanceof Folder) {
					$r = $this->createRow($c);
					if ($r) {
						$res[] = $r;
					}
				}
			}
		} catch (\Exception $e) {
			$res[] = $this->createRow(Folder::fetch(
				$this->context, $this->context->config->ROOT_FOLDER_ID));
		}

		$this->setResult(self::SUCCESS, $res);

	}

	private function createRow(Folder $c) {

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

		return array(
			"id" => $c->id,
			"title" => (string)$c->title,
			"childListUrl" => $url,
			"type" => $c->type
		);

	}

}

?>