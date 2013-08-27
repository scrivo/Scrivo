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

namespace Scrivo\SocialMedia;

/**
 * Get a number of tweets from a user timeline.
 * 
 * Example (using bogus values):
 * 
 * $tut = new TwitterUserTimeline(
 *   "xvz1evFS4wEEPTGEFPHBog", //< consumer key
 *   "kAcSOqF21Fu85e7zjz7ZN2U4ZRhfV3WpwPAoE3Z7kBw", //< consumer secret
 *   "370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb", //< access token
 *   "LswwdoUaIvS8ltyTt5jkRh4J50vUPVVHtR2YPi5kE" //< access token secret
 * );
 * 
 * echo $tut->getTweets(array("screen_name"=>"yardinternet", "count"=>20));
 */
class TwitterUserTimeline extends OAuth {

	/**
	 * Get OAuth autorized data.
	 * @param string $url The URL of the service.
	 * @param array $param The request parameters given as an array.
	 * @return string The response data.
	 * @throws Exception If there was a problem with retrieving data from 
	 *   the $url.
	 */
	private function getData($url, array $param) {
	
		$od = $this->getAuthorizationData("get", $url, $param);
		
		$rr = new RawRequest($od->scheme, $od->hostname);

		$result = $rr->getResponse(
			"GET {$od->baseUrl}?{$od->parameterString} HTTP/1.1\r\n".
			"Accept: */*\r\n".
			"Connection: close\r\n".
			"User-Agent: Scrivo PHP OAuth\r\n".
			$od->authorisationHeader.
			"Host: {$od->hostname}\r\n".
			"\r\n");
		
		return $result->data;
	}
	
	/**
	 * Get tweets as a HTML unordered list.
	 * @param array $param The request parameters given as an array for the 
	 *   Twitter url "https://api.twitter.com/1.1/statuses/user_timeline.json".
	 * @return string HTML data containing the tweets.
	 */
	public function getTweets(array $param) {

		try {
			$twitter = json_decode($this->getData(
				"https://api.twitter.com/1.1/statuses/user_timeline.json", 
				$param));
		} catch (Excepetion $e) {
			return "<ul class='tweets'></ul>";
		}

		$res = "<ul class='tweets'>";
		foreach($twitter as $tweet) {
			$replace = array();
			$with = array();
			foreach ($tweet->entities->hashtags as $h) {
				$replace[] = "#{$h->text}";
				$with[] = "<a href='http://www.twitter.com/search?q=%23".
					"{$h->text}&amp;src=hash' target='_blank'>#{$h->text}</a>";
			}
			foreach ($tweet->entities->symbols as $symbol) {
				// Is this the bleeding edge of technology?
			}
			foreach ($tweet->entities->urls as $url) {
				$replace[] = $url->url;
				$with[] = "<a href='{$url->expanded_url}' target='_blank'>"
					."{$url->display_url}</a>";
			}
			foreach ($tweet->entities->user_mentions as $um) {
				$replace[] = "@{$um->screen_name}";
				$with[] = "<a href='http://www.twitter.com/{$um->screen_name}'"
					." target='_blank'>@{$um->screen_name}</a>";
			}
			$res .= "<li>".str_replace($replace, $with, $tweet->text)."</li>";
		}	
		$res .= "</ul>";
		return $res;
	}
}

?>