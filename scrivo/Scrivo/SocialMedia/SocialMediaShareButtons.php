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
 * Implmentation of the most usefull social share buttons using their own API.
 * All buttons are implemented as iframe elements so they will keep working 
 * correctly when reloaded with AJAX using different parameter values.
 *
 * Note that this is not an attempt to create a uniform interface for 
 * different social media buttons, but simply a convenient way to output
 * them using PHP. The parameters that are used by the buttons are still those
 * as defined by the original API and therfore differ: (Facebook uses href,
 * Twitter and LinkedIn use data-url, Titter uses data-count and LinkedIn
 * uses data-counter, etc.).
 */
class SocialMediaShareButtons {

	/**
	 * The URL to share.
	 * @var string
	 */
	private $url = "http://www.scrivo.nl";
	
	/**
	 * The locale to use for the buttons.
	 * @var string
	 */
	private $locale = "nl_NL";
	
	/**
	 * Construct a ShareButtons object. Using this object you can conveniently
	 * print share buttons on your site, all using the same URL for sharing
	 * and using the same locale.
	 * @param string $url The URL to share.
	 * @param string $locale The locale to use for the buttons.
	 **/
	public function __construct($url, $locale="nl_NL") {
		$this->url = $url;
		$this->locale = $locale;
	}

	/**
	 * Get HTML code for the iframe that hosts the button.
	 * @param string $src The value for the iframe src parameter.
	 * @param string $width The CSS width of the iframe.
	 * @param string $height The CSS height of the iframe.
	 * @return string HTML iframe code.
	 **/
	private function getIFrame($src, $width, $height) {
		return "<iframe src=\"{$src}\" scrolling=\"no\" frameborder=\"0\" ".
			"style=\"display: inline; border:none; overflow:hidden; ".
			"width:{$width}; height:{$height};\" ".
			"allowTransparency=\"true\"></iframe>";		
	}

	/**
	 * Construct a javascript source string for an iframe. For instance:
	 * "javascript: '<html><body>\'t stoepje</body></html>'".
	 * @param string $data The HTML data to display.
	 * @return string A javascript iframe src string.
	 **/
	private function jsContent($data) {
		return "javascript: '".str_replace("'", "\\'", $data)."'";
	}
	
	/**
	 * Collect only the parameters that have keys starting with "data-" and
	 * contain a value. Encode these and return them as a parameter string.
	 * @param array $param A set of button parameters.
	 * @return string A parameterized string.
	 */
	private function getDataParameters($param) {
		$nb = array();
		foreach ($param as $k=>$v) {
			if (substr($k, 0, 5) === "data-" && $v) {
				$nb[] = $k . "='" . urlencode($v) . "'";
			}
		}
		return implode(" ", $nb);
	}
	
	/** 
	 * Get the HTML for a Facebook like button.
	 *
	 * $sb = new SocialMediaShareButtons(
	 *         "http://www.scrivo.nl/index.php?p=1093657");
	 *
	 * echo $sb->getFacebookLikeButton(array(
	 *         "action"=>"recommend", "width"=>"125px"));
	 *
	 * @param array $param A set of button parameters. For possible parameter
	 *   names and values see the Facebook site.
	 * @return string The HTML button code (iframe).
	 */
	public function getFacebookLikeButton($param) {

		$param += array(
			"href" => $this->url,
			"send" => "false",
			"layout" => "button_count",
			"show_faces" => "false",
			"font" => "",
			"colorscheme" => "light",
			"action" => "like",
			"locale" => $this->locale,
			"width" => "125px",
			"height" => "21px"
		);

		$nb = array();
		foreach ($param as $k=>$v) {
			$nb[] = $k . "=" . urlencode($v);
		}
		$prms = implode("&amp;",$nb);

		return $this->getIFrame("//www.facebook.com/plugins/like.php?{$prms}",
			$param["width"], $param["height"]);
	}

	/** 
	 * Get the HTML for a Twitter tweet button.
	 *
	 * $sb = new SocialMediaShareButtons(
	 *         "http://www.scrivo.nl/index.php?p=1093657");
	 *
	 * $sb->getTwitterTweetButton(array("width" => "95px"));
	 *
	 * @param array $param A set of button parameters. For possible parameter
	 *   names and values see the Twitter site.
	 * @return string The HTML button code (iframe).
	 */
	public function getTwitterTweetButton($param) {

		$param += array(
			"data-url" => $this->url,
			"data-via" => "",
			"data-lang" => substr($this->locale, 0, 2),
			"data-related" => "",
			"data-count" => "",
			"data-hashtags" => "",
			"width" => "125px",
			"height" => "21px"
		);

		// Template was directly copied from the Twitter site.
		$template = "<html style='margin:0; padding:0'>
			<body style='margin:0; padding:0'>
				<a href='https://twitter.com/share' class='twitter-share-button' 
				".$this->getDataParameters($param)."></a>
				<script>!function(d,s,id){
					var js,fjs=d.getElementsByTagName(s)[0],
					p=/^http:/.test(d.location)?'http':'https';
					if(!d.getElementById(id)){js=d.createElement(s);
					js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
					fjs.parentNode.insertBefore(js,fjs);
					}}(document, 'script', 'twitter-wjs');</script>
			</body></html>";

		return $this->getIFrame($this->jsContent($template),
			$param["width"], $param["height"]);
	}
	
	/** 
	 * Get the HTML for a LinkedIn share button.
	 *
	 * $sb = new SocialMediaShareButtons(
	 *         "http://www.scrivo.nl/index.php?p=1093657");
	 *
	 * echo $sb->getLinkedInShareButton(array("width" => "95px"));
	 *
	 * @param array $param A set of button parameters. For possible parameter
	 *   names and values see the LinkedIn site.
	 * @return string The HTML button code (iframe).
	 */
	public function getLinkedInShareButton($param) {

		$param += array(
			"data-url" => $this->url,
			"data-counter" => "right",
			"data-showzero" => "true", 
			"locale" => $this->locale,
			"width" => "125px",
			"height" => "21px"
		);

		// Template was directly copied from the LinkedIn site.
		$template = "<html style='margin:0; padding:0'>
			<body style='margin:0; padding:0'>
				<script src='//platform.linkedin.com/in.js' 
					type='text/javascript'>lang: {$param["locale"]}</script>
				<script type='IN/Share' ".$this->getDataParameters($param).">
				</script>
			</body></html>";

		return $this->getIFrame($this->jsContent($template),
			$param["width"], $param["height"]);
	}

}

?>