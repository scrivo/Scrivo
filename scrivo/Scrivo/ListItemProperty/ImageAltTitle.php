<?php
/* Copyright (c) 2012, Geert Bergman (geert@scrivo.nl)
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
 * $Id: ImageAltTitle.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ListItemProperty\ImageAltTitle class.
 */

namespace Scrivo\ListItemProperty;

/**
 * Property to hold an image source (src) url.
 *
 * @property \Scrivo\Str $src The image source (src attribute).
 * @property \Scrivo\Str $alt The alternative text for the image (alt
 *    attribute).
 * @property \Scrivo\Str $title The image title (title attribute).
 */
class ImageAltTitle extends \Scrivo\ListItemProperty {

	/**
	 * The src property of the url.
	 * @var \Scrivo\Str
	 */
	private $src;

	/**
	 * The alt property of the url.
	 * @var \Scrivo\Str
	 */
	private $alt;

	/**
	 * The title property of the url.
	 * @var \Scrivo\Str
	 */
	private $title;

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $name The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($name) {
		if (!$this->src) {
			$this->fromData();
		}
		switch($name) {
			case "src": return $this->src;
			case "alt": return $this->alt;
			case "title": return $this->title;
		}
		return parent::__get($name);
	}

	/**
	 * Implementation of the writable properties using the PHP magic
	 * method __set().
	 *
	 * @param string $name The name of the property to set.
	 * @param mixed $value The value of the property to set.
	 */
	public function __set($name, $value) {
		switch($name) {
			case "src": $this->src = $value; $this->toData(); return;
			case "alt": $this->alt = $value; $this->toData(); return;
			case "title": $this->title = $value; $this->toData(); return;
		}
		parent::__set($name, $value);
	}

	/**
	 * Convert the property data field to the image members.
	 */
	private function fromData() {
		$t = parent::__get("data")->split(new \Scrivo\Str("\t"));
		$c = count($t);
		$this->src = $c>0 ? $t[0] : new \Scrivo\Str("");
		$this->alt = $c>1 ? $t[1] : new \Scrivo\Str("");
		$this->title = $c>2 ? $t[2] : new \Scrivo\Str("");
	}

	/**
	 * Convert the property data field to the image members.
	 */
	private function toData() {
		$this->data = new \Scrivo\Str(
			(string)$this->src."\t".
			(string)$this->alt."\t".
			(string)$this->title
		);
		return $this->data;
	}

}

?>