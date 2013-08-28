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
 * $Id: AssetDetails.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\I18n;
use \Scrivo\String;
use \Scrivo\Role;
use \Scrivo\Asset;
use \Scrivo\Folder;
use \Scrivo\File;

/**
 * The AssetDetails class implements an action for generating an overview
 * of asset details.
 */
class AssetDetails extends Action {

	const BUT_IMG_PATH = "../ca.php?i=img/fdlg/";

	private $i18n = null;

	/**
	 * In this action the asset(s) with given id(s) are loaded and an HTML
	 * report displaying the selected asset details is generated.
	 */
	function doAction() {

		$this->i18n = new I18n($this->context->config->UI_LANG);

		$ids = Request::get("assetIds", Request::TYPE_INTEGER);

		$html = "";
		$img = "";

		if (count($ids) == 1) {

			// Just one asset selected: fetch it.
			$asset = Asset::fetch($this->context, $ids[0]);

			// Create a linl to the asset.
			$link = "{$this->context->config->WWW_ROOT}/scrivo/asset.php?id=".
				$asset ->id;

			if ($asset instanceof Folder) {

				// If it's a folder get HTML code describing it.
				$html = $this->folderHtml($asset);

			} else {

				// If it's a file get HTML code describing it ...
				$html = $this->fileHtml($asset, $link);

				// ... and get a thumbnail image.
				$img = self::BUT_IMG_PATH."icon48_unknown.gif";
				if ($asset instanceof Folder) {
					$img = "";
				} else {
					if ($asset->mimeType->substr(0, 5)->equals(
							new String("image"))) {
						$img = $link;
					} else {
						$img = $this->iconFunc($asset->mimeType);
					}
				}

			}

		} else {

			//TODO; compund report;

		}

		$this->setResult(self::SUCCESS, array("html" => $html, "img" => $img));
	}
	/**
	 * Generate HTML describing a folder.
	 *
	 * @param \Scrivo\Folder $asset The folder to describe.
	 * @return string HTML code describing the folder.
	 */
	private function folderHtml(Folder $asset) {
		return
			"<p><b>{$asset->title}</b><br>
				{$this->i18n["Folder"]}</p>
			<p>{$this->i18n["Created on"]}:
				".$this->humanDate($asset->dateCreated)."</p>
			<p>{$this->i18n["Access"]}</p>
			<ul>".$this->publicRoleList($asset)."</ul>";
	}

	/**
	 * Generate HTML describing a file.
	 *
	 * @param \Scrivo\File $asset The file to describe.
	 * @param string $link A link to the file.
	 * @return string HTML code describing the file.
	 */
	private function fileHtml(File $asset, $link) {

		$html =
			"<p><a href=\"{$link}\" target=\"_blank\">
			<b>{$asset->title}</b></a>
			<br>{$this->i18n["Mime-type"]}: {$asset->mimeType}</p>
			<p id=\"scrivo_filedialog_img\" class=\"img\">
			<img src=\"{self::BUT_IMG_PATH}loading.gif\"></p>";

		$html .=
			"<p>{$this->i18n["Uploaded on"]}: ".
			$this->humanDate($asset->dateCreated)."</p>";

		$html .=
			"<p>{$this->i18n["Size"]}: ".
			$this->humanSize($asset->size)."</p>";

		$html .=
			"<p>{$this->i18n["Time to download"]}</p><ul>
				<li>{$this->i18n["Modem (56 Kb/s)"]}: ".
					$this->timeDownload($asset->size, 56).
					" {$this->i18n["sec"]}</li>
				<li>{$this->i18n["Adsl light (1024 Kb/s)"]}: ".
					$this->timeDownload($asset->size, 1024).
					" {$this->i18n["sec"]}</li>
				<li>{$this->i18n["Adsl fast (4096 Kb/s)"]}: ".
					$this->timeDownload($asset->size, 4096).
					" {$this->i18n["sec"]}</li>
			</ul>";

		$html .=
			"<p>{$this->i18n["Access (by folder)"]}</p>
			<ul>".$this->publicRoleList($asset)."</ul>";

		$html .=
			"<ul>
			<li>{$this->i18n["On line on"]}:
				<span style=\"white-space: nowrap\" " .
				($asset->dateOnline > new \DateTime() ?
				" class=\"red\"" : "").">".
				$asset->dateOnline->format("d-m-Y H:i") .
			"</span></li>";

		if ($asset->dateOffline) {
			$html .=
				"<li>{$this->i18n["Off line on"]}:
					<span style=\"white-space: nowrap\" ".
					($asset->dateOffline < new \DateTime() ?
					" class=\"red\"" : "").">".
					$asset->dateOffline->format("d-m-Y H:i").
			"</span></li>";
		}

		$html .= "</ul>";

		return $html;
	}

	/**
	 * Generate an icon url based on a file mime type.
	 *
	 * @param \Scrivo\String $mimeType The mime type to create the icon url for.
	 * @return string An url to an icon.
	 */
	private function iconFunc(String $mimeType) {

		if ($mimeType->substr(0, 5)->equals(new String("audio"))) {
			return self::BUT_IMG_PATH."icon48_media.gif";
		}

		switch ((string)$mimeType) {
			case "<DIR>":
				return self::BUT_IMG_PATH."icon_folder_locked.gif";
			case "<DIR public>":
				return self::BUT_IMG_PATH."icon_folder.gif";
			case "text/html":
				return self::BUT_IMG_PATH."icon48_html.gif";
			case "text/plain":
				return self::BUT_IMG_PATH."icon48_txt.gif";
			case "application/pdf":
				return self::BUT_IMG_PATH."icon48_pdf.gif";
			case "application/vnd.ms-excel":
				return self::BUT_IMG_PATH."icon48_xls.gif";
			case "application/vnd.ms-powerpoint":
				return self::BUT_IMG_PATH."icon48_ppt.gif";
			case "application/msword":
				return self::BUT_IMG_PATH."icon48_doc.gif";
			case "application/msaccess":
				return self::BUT_IMG_PATH."icon48_mdb.gif";
			case "application/x-zip-compressed":
				return self::BUT_IMG_PATH."icon48_zip.gif";
		}

		return self::BUT_IMG_PATH."icon48_unknown.gif";
	}

	/**
	 * Return a human readable file size notation.
	 *
	 * @param int $size The file size in bytes.
	 * @param int $dec The number of decimals to use.
	 * @return string A human readable file size notation.
	 */
	private function humanSize($size, $dec=1){

		$sizeNames = array("Byte", "KB", "MB", "GB", "TB", "PB");

		$nameId = 0;
		while($size >= 1024 && ($nameId < count($sizeNames)-1)){
			$size /= 1024;
			$nameId++;
		}

		return round($size,$dec)." ".$sizeNames[$nameId];
	}

	/**
	 * Return an indication of the file download time.
	 *
	 * @param int $size The file size in bytes.
	 * @param int $kbps The download speed in kBit per second.
	 * @return number The donwload time in seconds.
	 */
	function timeDownload($size, $kbps){

		$sz = ($size * 8) / ($kbps * 1000);
		$rnd = $sz < 10 ? 1 : 0;

		return round($sz, $rnd);
	}

	/**
	 * Return a human readable file date notation.
	 *
	 * @param \DateTime $date The date to get the human readable notation for.
	 * @return string A human readable notation of the given date.
	 */
	function humanDate(\DateTime $date){

		$dt = $date->format("Y-m-d h:i:s");

		$y = substr($dt,2,2);
		$m = (int)substr($dt,5,2);
		$d = substr($dt,8,2);
		$h = substr($dt,11,2);
		$n = substr($dt,14,2);

		return $d."-".substr(
			$this->i18n["janfebmaraprmayjunjulaugsepoctnovdec"],($m-1)*3,3)
			."-".$y."&nbsp;".$h.":".$n;
	}

	/**
	 * Return the list of public roles (as HTML code) for the given asset.
	 *
	 * @param \Scrivo\Asset $asset The asset for which to get the public roles.
	 * @return string The list of public roles for the given asset (HTML code).
	 */
	function publicRoleList(Asset $asset) {

		$res= "";
		foreach (Role::select($this->context, Role::PUBLIC_ROLE) as $r) {
			if (isset($asset->roles[$r->id])) {
				$res .= "<li>{$r->title}</li>";

			}
		}
		if (!$res) {
			$res = "<li>{$this->i18n["No access"]}</li>";
		}

		return $res;
	}

}

?>