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
 * Class to send autorized requests to a server using the OAuth 1.0 protocol. 
 * 
 * Example (using bogus values):
 * 
 * $oAuth = new OAuthRequest(
 *   "xvz1evFS4wEEPTGEFPHBog", //< consumer key
 *   "kAcSOqF21Fu85e7zjz7ZN2U4ZRhfV3WpwPAoE3Z7kBw", //< consumer secret
 *   "370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb", //< access token
 *   "LswwdoUaIvS8ltyTt5jkRh4J50vUPVVHtR2YPi5kE" //< access token secret
 * );
 * 
 * // Send an authorized reqeuest to a Twitter service.
 * echo $oAuth->request("get", 
 *    "https://api.twitter.com/1.1/statuses/user_timeline.json?count=2");
 */
class OAuthRequest extends OAuth {

	/**
	 * Perform an OAuth request to get temporary credentials.
	 * 
	 * @param string $requestMethod 
	 *   The HTTP request method to use in this request (GET or POST).
	 * @param string $url 
	 * 	 The URL of the service. Request parameters can be included in the URL.
	 *   Note: this is an unescaped URL: ampersands should be "&" (not "&amp;") 
	 *   and spaces should be " " (not "%20" or "+"), and this is not limited
	 *   to ampersands and spaces.
	 * @param string $callback
	 *   The callback URL to use, use "oob" if an out of bound configuration
	 *   is used.
	 * @param string[] $param 
	 *   Optional extra request parameters given as a set of name/value pairs. 
	 *   These parameters will get preceedence when name conflicts occur with 
	 *   parameters given in the $url parameter itself.
	 * @return \stdClass
	 *   An object containing the following members: statusCode (int) - 
	 *   the HTTP status code of the request (0 if it was hopeless), headers 
	 *   (array) - the response headers, data (string) - the response data.  
	 */
	public function requestTemporaryCredentials(
			$requestMethod, $url, $callback, array $param=array()) {
		
		$r = new Request();
		$od = $this->getTemporaryCredentialRequestData(
			$requestMethod, $url, $callback, $param);
		
		return $r->request($od->baseUrl, $od->requestMethod, $od->parameters,
			array("Authorization" => $od->authorisationHeader));
	}
	
	/**
	 * Get an OAuth access token.
	 * 
	 * @param string $requestMethod 
	 *   The HTTP request method to use in this request (GET or POST).
	 * @param string $url 
	 * 	 The URL of the service. Request parameters can be included in the URL.
	 *   Note: this is an unescaped URL: ampersands should be "&" (not "&amp;") 
	 *   and spaces should be " " (not "%20" or "+"), and this is not limited
	 *   to ampersands and spaces.
	 * @param string $verifier
	 *   A verification code received from the OAuth server.
	 * @param string[] $param 
	 *   Optional extra request parameters given as a set of name/value pairs. 
	 *   These parameters will get preceedence when name conflicts occur with 
	 *   parameters given in the $url parameter itself.
	 * @return \stdClass
	 *   An object containing the following members: statusCode (int) - 
	 *   the HTTP status code of the request (0 if it was hopeless), headers 
	 *   (array) - the response headers, data (string) - the response data.  
	 */
	public function requestAccessToken(
			$requestMethod, $url, $verfier, array $param=array()) {

		$r = new Request();
		$od = $this->getAccessTokenRequestData(
			$requestMethod, $url, $verfier, $param);

		return $r->request($od->baseUrl, $od->requestMethod, $od->parameters,
			array("Authorization" => $od->authorisationHeader));
	}

	/**
	 * Execute an OAuth autorized request.
	 * 
	 * @param string $requestMethod 
	 *   The HTTP request method to use in this request (GET or POST).
	 * @param string $url 
	 * 	 The URL of the service. Request parameters can be included in the URL.
	 *   Note: this is an unescaped URL: ampersands should be "&" (not "&amp;") 
	 *   and spaces should be " " (not "%20" or "+"), and this is not limited
	 *   to ampersands and spaces.
	 * @param string[] $param 
	 *   Optional extra request parameters given as a set of name/value pairs. 
	 *   These parameters will get preceedence when name conflicts occur with 
	 *   parameters given in the $url parameter itself.
	 * @return \stdClass
	 *   An object containing the following members: statusCode (int) - 
	 *   the HTTP status code of the request (0 if it was hopeless), headers 
	 *   (array) - the response headers, data (string) - the response data.  
	 */
	public function request($requestMethod, $url, array $param=array()) {
		
		$r = new Request();
		$od = $this->getAuthorizationData($requestMethod, $url, $param);
		
		return $r->request($od->baseUrl, $od->requestMethod, $od->parameters,
			array("Authorization" => $od->authorisationHeader));
	}
	
}

?>