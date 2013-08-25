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
 * $Id: AssetList.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Asset;
use \Scrivo\File;

/**
 * The AssetList class implements an action for listing an asset folder.
 */
class AssetList extends Action {

	/**
	 * In this action the asset with the given is loaded. Then it is determined
	 * if the asset is a file or a folder. Then a normal listing and
	 * a path listing of the folder or file's parent folder is generated.
	 */
	function doAction() {

		//$uv = new user_var($scrivo_conn, $login_user_id, "last_asset_folder");
		$assetId = Request::get("assetId", Request::TYPE_INTEGER, 0);
		if (!$assetId) {
			$assetId = $this->context->config->ROOT_FOLDER_ID;
		}

		//if ($dirId === 0) {
		//	$dirId = intval($uv->get());
		//}

		$selId = -1;
		$selIndex = array();

		$dir = Asset::fetch($this->context, $assetId);
		if ($dir instanceof File) {
			// Whoops, its a file: get the parent folder.
			$selId = $dir->id;
			$dir =  Asset::fetch($this->context, $dir->parentId);
		} else {
			//$uv->set($asset->assetId);
		}

		// Create the files and folder listing.
		$list = array();
		foreach ($dir->children as $c) {
			$list[] = array(
				"assetId" => $c->id,
				"title" => (string)$c->title,
				"type" => $c->type,
				"mimetype" => $c instanceof File ? (string)$c->mimeType : "",
				"size" => $c instanceof File ? $c->size : "",
				"modified" => $c->dateModified->getTimeStamp()
			);
			if ($c->id == $selId) {
				$selIndex[] = count($list)-1;
			}
		}

		// Create the path listing.
		$path = array();
		foreach ($dir->path as $p) {
			$path[] = array(
				"assetId" => $p->id,
				"title" => (string)$p->title,
				"parentId" => $p->parentId
			);
		}
		$path[] = array(
			"assetId" => $dir->id,
			"title" => (string)$dir->title,
			"parentId" => $dir->parentId
		);

		// TODO: Feeds.
		$feeds = array();
		/*
		if ($dirId == 2) {
			// we're at root level: also check the existence of feeds
			$feedno = 1;
			while (defined("FILE_DIALOG_FEED_$feedno")) {
				$feed =
				Scrivo_utf8_split("#", constant("FILE_DIALOG_FEED_$feedno"));
				$res4[] = array(
						"feedId" => $feedno,
						"title" => $feed[0],
						"url" => $feed[1]
				);
				$feedno++;
			}
		}
		*/

		$res = array("list"=>$list, "path"=>$path, "feeds"=>$feeds,
				"indexSelected" => $selIndex);

		$this->setResult(self::SUCCESS, $res);
	}
}

?>