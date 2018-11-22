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
 * $Id: Url.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PageProperty\Url class.
 */

namespace Scrivo\PageProperty;

/**
 * Property to hold html link data.
 *
 * @property \Scrivo\Str $href The link url (href attribute).
 * @property \Scrivo\Str $title A descriptive tilte for the link (title
 *    attribute).
 * @property \Scrivo\Str $target A link target (target attribute).
 */
class Url extends \Scrivo\PageProperty {

	/**
	 * The href property of the url.
	 * @var \Scrivo\Str
	 */
	private $href;

	/**
	 * The title property of the url.
	 * @var \Scrivo\Str
	 */
	private $title;

	/**
	 * The target property of the url.
	 * @var \Scrivo\Str
	 */
	private $target;

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $name The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($name) {
		if (!$this->href) {
			$this->fromData();
		}
		switch($name) {
			case "href": return $this->href;
			case "title": return $this->title;
			case "target": return $this->target;
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
			case "href": $this->href = $value; $this->toData(); return;
			case "title": $this->title = $value; $this->toData(); return;
			case "target": $this->target = $value; $this->toData(); return;
		}
		parent::__set($name, $value);
	}

	/**
	 * Convert the property data field to the link members.
	 */
	protected function fromData() {
		$t = parent::__get("data")->split(new \Scrivo\Str("\t"));
		$c = count($t);
		$this->href = $c>0 ? $t[0] : new \Scrivo\Str("");
		$this->title = $c>1 ? $t[1] : new \Scrivo\Str("");
		$this->target = $c>2 ? $t[2] : new \Scrivo\Str("");
	}

	/**
	 * Convert the property data field to the link members.
	 */
	private function toData() {
		$this->data = new \Scrivo\Str(
			(string)$this->href."\t".
			(string)$this->title."\t".
			(string)$this->target
		);
	}

}

?>