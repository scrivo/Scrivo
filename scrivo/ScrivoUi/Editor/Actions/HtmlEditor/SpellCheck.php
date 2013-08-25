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
 * $Id: SpellCheck.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\HtmlEditor;

use \Scrivo\Action;
use \Scrivo\Request;

/**
 * The SpellCheck class implements an action for posting an retrieving
 * data to an external spell check web service.
 */
class SpellCheck extends Action {

	/**
	 * In this action the given content is posted to an external spell
	 * check web service and the results are returned.
	 */
	function doAction() {

		$cont = Request::post("content", Request::TYPE_STRING);
		$lang = Request::post("lang", Request::TYPE_STRING);

		$json = $this->postIt(
			array(
				"json" => 1,
				"content" => (string)$cont,
				"lang" => (string)$lang
			),
			$this->context->config->WEBSERVICE_SPELL
		);

		//TODO: we need to decode back to iso to decode utf8 later on
		$res = json_decode($json);

		$this->setResult(self::SUCCESS, $res->data);
	}

	/**
	 * Post data to an url.
	 * TODO: do we still need this stuff in 2013?
	 *
	 * @param array $datastring An associative array with key value pair that
	 *     will be posted as request variables.
	 * @param string $url The url to post to.
	 * @return string The content of the response (without the headers).
	 */
	private function postIt($datastream, $url) {

		$result = "";

		$url = preg_replace("@^http://@i", "", $url);
		$host = substr($url, 0, strpos($url, "/"));
		$uri = strstr($url, "/");

		$reqbody = "";
		foreach ($datastream as $key => $val) {
			if (!empty($reqbody))
				$reqbody .= "&";
			$reqbody .= $key."=".urlencode(stripslashes($val));
		}

		$contentlength = strlen($reqbody);

		$reqheader =
			"POST $uri HTTP/1.0\r\n"."Host: $host\n"."User-Agent: PostIt\r\n".
			"Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n"
			."Content-Length: $contentlength\r\n\r\n"."$reqbody\r\n";

		$socket = fsockopen($host, 80, $errno, $errstr);

		if (!$socket) {
			return $result;
		}

		fputs($socket, $reqheader);

		while (!feof($socket)) {
			$result .= fgets($socket, 4096);
		}

		fclose($socket);

		$p = strpos($result, "\r\n\r\n");

		return substr($result, $p);
	}

}

?>