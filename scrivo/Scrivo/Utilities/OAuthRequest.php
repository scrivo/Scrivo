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
 * Class to send autorized reqeuests to a server using the OAuth 1.0 protocol. 
 * 
 * Example (using bogus values):
 * 
 * $oAuth = new OAuth_1_0(
 *   "xvz1evFS4wEEPTGEFPHBog", //< consumer key
 *   "kAcSOqF21Fu85e7zjz7ZN2U4ZRhfV3WpwPAoE3Z7kBw", //< consumer secret
 *   "370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb", //< access token
 *   "LswwdoUaIvS8ltyTt5jkRh4J50vUPVVHtR2YPi5kE" //< access token secret
 * );
 * 
 * // Send an authorized reqeuest to a Twitter service.
 * echo $oAuth->sendRequest("get", 
 *    "https://api.twitter.com/1.1/statuses/user_timeline.json?count=2");
 */
class OAuthRequest extends OAuth {

	/**
	 * Get OAuth autorized data.
	 * @param string $requestMethod The HTTP request method to use in this 
	 *   request (GET or POST).
	 * @param string $url The URL of the service. Request parameters 
	 *   should be included in the URL.
	 *   Note: this is an unescaped URL: ampersands should be "&" (not "&amp;") 
	 *   and spaces should be " " (not "%20" or "+"), and this is not limited
	 *   to ampersands and spaces.
	 * @param string[] $param Optional extra request parameters given as a set
	 *   of name/value pairs. These parameters will get preceedence when 
	 *   name conflicts occur with parameters given in the $url parameter 
	 *   itself.
	 * @return string The response data.
	 * @throws Exception If there was a problem with retrieving data from 
	 *   the $url.
	 */
	public function sendRequest($requestMethod, $url, array $param=array()) {
	
		$od = $this->getAuthorizationData($requestMethod, $url, $param);
		
		$rr = new RawRequest($od->scheme, $od->hostname);

		if ($od->requestMethod === "POST") {

			$result = $rr->getResponse(
				"POST {$od->baseUrl} HTTP/1.1\r\n".
				"Accept: */*\r\n".
				"Connection: close\r\n".
				"User-Agent: Scrivo PHP OAuth\r\n".
				$od->authorisationHeader.
				"Content-Type: application/x-www-form-urlencoded\r\n".
				"Content-Length: ".strlen($od->parameterString)."\r\n".
				"Host: {$od->hostname}\r\n".
				"\r\n{$od->parameterString}");

		} else if ($od->requestMethod === "GET") {

			$result = $rr->getResponse(
				"GET {$od->baseUrl}?{$od->parameterString} HTTP/1.1\r\n".
				"Accept: */*\r\n".
				"Connection: close\r\n".
				"User-Agent: Scrivo PHP OAuth\r\n".
				$od->authorisationHeader.
				"Host: {$od->hostname}\r\n".
				"\r\n");

		} else {
			throw new Exception("Only GET and POST are supported.");
		}
		
		return $result->data;
	}
	
}

?>