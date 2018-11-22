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
 * Simple class to facilitate sending requests and receiving responses while
 * having convenient access to end and received header data. The class
 * uses native PHP streams and stream contexts.
 * 
 * Example usage:
 * 
 * // Get the contents of www.scrivo.org:
 * $r = new Request();
 * $r->request("http://www.scrivo.org");
 * if ($r->statusCode == 200) {
 *     echo $r->data;
 * }
 * 
 * // Send some authenticated request:
 * $r = new Request(array("Autorization" => base64_encode("user:password")); 
 * $r->request("http://www.scrivo.org/index.html");
 * $r->request("http://www.scrivo.org/somepage.html");
 * 
 * // Post some data:
 * $r = new Request();
 * $r->request("http://www.scrivo.org", "POST", array("a"=>1, "b"=>2));
 * 
 */
class Request {

	/**
	 * The (default) headers to be used in the request.
	 * @var array
	 */
	protected $headers = array(
		"Accept" => "*/*",
		"Connection" => "close",
		"User-Agent" => "Scrivo/PHP"
	);

	/**
	 * Construct a request object. To use other headers than the default
	 * ones you can pass a new set to use as default when constructing
	 * the object.
	 * 
	 * @param array $headers 
	 *    An array in which the keys are the header names and the values
	 *    the header values.
	 */
	public function __construct(array $headers = null) {
		if ($headers) {
			$this->headers = $headers;
		}
	}

	/**
	 * Execute an HTTP request and get the response.
	 * 
	 * @param string $url
	 *    The request URL.
	 * @param string $method
	 *    The request method to use, GET (default) or POST.
	 * @param array|string $data
	 *    Optional data to send along with the request. Either as query string
	 *    when using GET or in the request body when using POST. When using a
	 *    string the data is appended to the URL (GET) or send as the
	 *    request body (POST). When using an array the keys and values in the 
	 *    array are used to create a parameter string which then is appended
	 *    to the URL or send in the request body.
	 * @param array $additionalHeaders
	 *    A set of additional headers to overwrite or append the default set
	 *    of request headers.
	 * @return \stdClass
	 *    An object containing the following members: statusCode (int) - 
	 *    the HTTP status code of the request (0 if it was hopeless), headers 
	 *    (array) - the response headers, data (string) - the response data.  
	 */
	public function request(
			$url, $method="GET", $data=null, array $additionalHeaders=array()) {
		
		// The request headers
		$hdrs = $additionalHeaders + $this->headers;
		$prms = array("http" => array("method" => $method));
		
		if ($data) {
			
			// If there is data given ...
			if (is_array($data)) {
				// ... and data was given as array create a query string ... 
				$tmp = array();
				foreach ($data as $key => $val) {
					$tmp[] = urlencode($key)."=".urlencode($val);
				}
				// ... and use that as data.
				$data = implode("&", $tmp);
				// Don't forget to set the content type header.
				if ($method === "POST") {
					$hdrs["Content-Type"] = 
						"application/x-www-form-urlencoded; charset=utf-8";
				}
			}
			
			if ($method === "GET") {
				// Append the data to the URL. 
				$url .= (strpos("?", $url) !== false ? "&" : "?") . $data;
			} else if ($method === "POST") {
				// Send data in the request body. 
				$prms["http"]["content"] = $data;
				$hdrs["Content-Length"] = strlen($data);
			}
		}

		// Create a header string.
		$hd = "";
		foreach ($hdrs as $k=>$v) {
			$hd .= "{$k}: {$v}\r\n";
		}
		
		// Create a result object.
		$resp = new \stdClass;
		
		// Execute the request ...
		$prms["http"]["header"] = $hd;
		$fp = @fopen($url, "rb", false, stream_context_create($prms));
		if ($fp) {
			// ... if it was succesfull get the data ...
			$resp->data = stream_get_contents($fp);
			fclose($fp);
		} else {
			// ... else an empty string.
			$resp->data = "";
		}

		if (!isset($http_response_header)) {
			// No response header wil be set after a utterly failed request ... 
			$resp->headers = array();
			$resp->statusCode = 0;
		} else {
			// ... else get the status code and the header as key value pairs. 
			$resp->statusCode =
				$this->parseStatusCode(array_shift($http_response_header));
			$resp->headers = $this->parseHeaders($http_response_header);
		}
		
		// We're done.
		return $resp;
	}
	
	/**
	 * Convert the raw header array to an array in which the keys are the
	 * header names and the values the header values.
	 * 
	 * @param array $hdrs 
	 *    The raw header array.
	 * @return array 
	 *    An header array with key/value pairs.
	 */
	private function parseHeaders($hdrs) {
		$res = array();
		foreach ($hdrs as $hdr) {
			$tmp = explode(":", $hdr);
			$res[trim($tmp[0])] = isset($tmp[1]) ? trim($tmp[1]) : "";
		}
		return $res;
	}
	
	/**
	 * Get the status code out of the first line of the response headers.
	 *  
	 * @param string $dat 
	 *    The first line of an HTTP response.
	 * @return int 
	 *    The HTTP request status code. 
	 */
	private function parseStatusCode($dat) {
		$tmp = explode(" ", $dat);
		return intval($tmp[1]);
	}
	
}

?>