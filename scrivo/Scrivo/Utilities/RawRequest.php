<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id$
 */

namespace Scrivo\Utilities;

/**
 * Class to facilitate executing HTTP requests using raw header data.
 *
 * Example usage:
 *
 * // Get ready to send some requests over https:
 * $rr = new RawRequest("https", "www.scrivo.nl");
 * 
 * // Retrieve some data using GET over https:
 * $data = $rr->getResponse("GET https://www.scrivo.nl?arg=3 HTTP/1.1\r\n".
 *     "Host: www.scrivo.nl\r\n". 
 *     "User-Agent: GetIt\r\n".
 *     "\r\n");
 * 
 * // Some data to post:
 * $data = "<document></document>";
 * $contentlength = strlen($data);
 * 
 * // Send and retrieve some data using POST over https:
 * $data = $rr->getResponse("POST https://www.scrivo.nl HTTP/1.1\r\n".
 *     "Host: www.scrivo.nl\r\n". 
 *     "User-Agent: PostIt\r\n".
 *     "Content-Type: text/xml; charset=utf-8\r\n".
 *     "Content-Length: $contentlength\r\n".
 *     "\r\n$data");
 *
 */
class RawRequest {

	/**
	 * The port to send the request to.
	 * @var int
	 */
	protected $port;

	/**
	 * The host to send the request to.
	 * @var string
	 */
	protected $host;

	/**
	 * The scheme to use for the request.
	 * @var string
	 */
	protected $scheme;
	
	/**
	 * Create a RawRequest object that can be used for multiple requests
	 * to the same host.
	 * @param string $scheme The scheme to use (http or https).
	 * @param string $host The name of the host to send the reqeusts to.
	 * @param int $port Optional: a specific port to send the request to,
	 *    defaults to 80 (http) or 443 (https).
	 */
	public function __construct($scheme, $host, $port=null) {
		$this->scheme = $scheme;
		if ($this->scheme === "https") {
			$this->host = "ssl://$host";
			$this->port = !$port ? 443 : $port;
		} else if ($scheme === "http") {
			$this->host = $host;
			$this->port = !$port ? 80 : $port;
		} else {
			throw new Exception("Only http and https are currently allowed.");
		}
	}

	/**
	 * Send raw header data to the specified host and get the raw response
	 * back.
	 * @param string $requestHeaders All header data (and content when posting)
	 *   in raw format.
	 * @return object The response, an object containting the fields:
	 *   headers (string): The response headers,
	 *   data (string): The response data.
	 * @throws Exception If there was a problem with opening, reading from or 
	 *   writing to the connection.
	 */
	public function getResponse($requstHeaders) {
		
		$socket = @fsockopen($this->host, $this->port, $errno, $errstr, 10);
		if (!$socket) {
			if (!$errno) {
				$errstr = "Could not connect to socket ({$this->host}).";
			}
			throw new Exception("$errstr", $errno);
		}
		if (!@fwrite($socket, $requstHeaders)) {
			@fclose($socket);
			throw new Exception("Could not write to socket ({$this->host}).");
		}
		if (($result = @stream_get_contents($socket)) === false) {
			@fclose($socket);
			throw new Exception("Could not read from socket ({$this->host}).");
		}
		@fclose($socket);

		$r = explode("\r\n\r\n", $result, 2);
		return (object)array("header"=>$r[0], "data"=>isset($r[1])?$r[1]:"");
	}

}

?>