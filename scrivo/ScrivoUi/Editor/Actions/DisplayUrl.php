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
 * $Id: DisplayUrl.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Editor\Actions\DisplayUrl action class.
 */

namespace ScrivoUi\Editor\Actions;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\String;
use \Scrivo\Page;
use \Scrivo\Asset;

/**
 * The DisplayUrl class implements an action for retrieving a short human
 * readable representation of an url. For Example:
 *
 * www.scrivo.nl                       => www.scrivo.nl
 * www.scrivo.nl/act.php?test=1        => www.scrivo.nl/act.php
 * www.scrivo.nl/index.html            => www.scrivo.nl/index.html
 * www.scrivo.nl/sb1/sub2/index.html   => www.scrivo.nl/../index.html
 * www.mysite.nl/index.php?p=1         => Home
 * www.mysite.nl/scrivo/asset.php?id=2 => Uploaded files
 */
class DisplayUrl extends \Scrivo\Action {

	/**
	 * In this action it is tried to get a short human readable interpretation
	 * for any given url. First it is checked if the url is an url to a Scrivo
	 * page or asset, then the page or asset name is used. If not the url name
	 * is shortended by stripping off the query parameters and path sub-folder
	 * entries.
	 */
	function doAction() {

		$url = Request::get("url", Request::TYPE_STRING, new String(""));

		$res = "";
		if (!$url->equals(new String(""))) {
			$res = $this->urlDisplay($url);
		}

		$this->setResult(self::SUCCESS, array("url" => $res));
	}

	/**
	 * Construct a short human readable representation of an url by using
	 * only the host and last entry of the path.
	 *
	 * @param array $aUrl An array containing the url parts.
	 * @return \Scrivo\String A human readable representation of the url.
	 */
	private function urlDisplayWww(array $aUrl) {

		$end = new String("");

		if (!$aUrl["path"]->equals(new String(""))) {

			$dat = $aUrl["path"]->split(new String("/"));
			$res = array();

			foreach ($dat as $d) {
				if (!$d->equals(new String(""))) {
					array_unshift($res, $d);
				}
			}

			if (count($res) == 1) {
				$end = new String("/{$res[0]}");
			} else if (count($res) > 1)
				$end = new String("/../{$res[0]}");
		}

		return new String("{$aUrl["host"]}{$end}");
	}

	/**
	 * Get the page title for a given link to a Srivo page.
	 *
	 * @param array $aUrl An array containing the url parts of the Scrivo url.
	 * @return \Scrivo\String The page title.
	 */
	private function urlDisplayIntern(array $aUrl) {

		parse_str($aUrl["query"]);
		if (isset($p)) {
			try {
				$page = Page::fetch($this->context, intval($p));
				return $page->title;
			} catch (\Exception $e) {}
		}
		return new String("");
	}

	/**
	 * Get the asset title for a given link to a Srivo asset.
	 *
	 * @param array $aUrl An array containing the url parts of the Scrivo url.
	 * @return \Scrivo\String The asset title.
	 */
	private function urlDisplayAsset(array $aUrl) {

		parse_str($aUrl["query"]);
		if (isset($id)) {
			try {
				$asset = Asset::fetch($this->context, intval($id));
				return $asset->title;
			} catch (\Exception $e) {}
		}
		return new String("");
	}

	/**
	 * Get a human readable string representation for the given url.
	 *
	 * @param String $url The url to get a short string representation for.
	 * @return String A short string represenation of the url.
	 */
	private function urlDisplay(String $url) {

		if ($url->equals(new String(""))) {
			return "";
		}

		$scrivoUrl =
			new String("{$this->context->config->WWW_ROOT}/index.php");
		$assetUrl =
			new String("{$this->context->config->WWW_ROOT}/scrivo/asset.php");

		$aUrl = String::create((array)@parse_url($url) +
			array("host"=>"", "query"=>"", "path"=>""));

		$intern = false;
		if ($aUrl["host"]->equals(new String(""))) {
			// Relative url, so intern.
			$intern = true;
		} else if ($url->contains($scrivoUrl) || $url->contains($assetUrl)) {
			// Relative url, so intern.
			$intern = true;
		}

		if ($intern) {
			return $aUrl["path"]->contains(new String("asset.php"))
				? $this->urlDisplayAsset($aUrl)
				: $this->urlDisplayIntern($aUrl);
		}

		return $this->urlDisplayWww($aUrl);
	}

}

?>