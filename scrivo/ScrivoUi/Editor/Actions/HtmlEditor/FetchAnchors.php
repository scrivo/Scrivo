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
 * $Id: FetchAnchors.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\HtmlEditor;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The FechAcnhors class implements an action for retrieving an anchor list
 * of an Web page (internal or external).
 */
class FetchAnchors extends Action {

	/**
	 * In this action the content given url is retrieved and its content
	 * stripped of all but the &ltg;A&gt; tags. This data is then returned.
	 */
	function doAction() {

		$url = Request::get("url", Request::TYPE_STRING, new Str(""));

		$res = array();
		if (!$url->equals(new Str(""))) {
			$res = 	strip_tags($this->open_url($url),"<A>");
		}

		$this->setResult(self::SUCCESS, array("html" => $res));
	}

	/**
	 * Try to read an HTML file from a network.
	 *
	 * @param \Scrivo\Str $filename The HTML file to read.
	 * @return string The file data.
	 */
	function open_url($filename) {

		$data = "";
		$cfg = $this->context->config;

		$context = null;

		// If the pages that we want to retieve are protected by basic
		// authentication get the credentials from the config.
		if (isset($cfg->BASIC_AUTHENTICATION_USERCODE)) {

			$tmp = array(
				"http" => array(
					"method"  => "GET",
					"header"  => sprintf("Authorization: Basic %s\r\n",
						base64_encode(
							(string)$cfg->BASIC_AUTHENTICATION_USERCODE.":"	.
							(string)$cfg->BASIC_AUTHENTICATION_PASSWORD)
					)
				)
			);
			$context = stream_context_create($tmp);
		}

		try {
			if ($context) {
				$data = file_get_contents($filename, false, $context);
			} else {
				$data = file_get_contents($filename);
			}
		} catch (Exception $e) {}

		return $data;
	}

}

?>