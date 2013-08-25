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
 * $Id: ColorTest.php 676 2013-06-14 12:01:01Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Base class for testing list item properties.
 */
class ListItemPropertyBaseTestCase extends PHPUnit_Framework_TestCase {

	/**
	 * Convenience function for creating a list item property to use in the
	 * tests.
	 * @param \Scrivo\String $type A string that identifies the property type.
	 * @return \Scrivo\ListItemProperty a Scrivo list item property.
	 */
	protected function createItemProperty(\Scrivo\String $type) {
		return \Scrivo\ListItemProperty::create(
			array(
				"type" => (string)$type,
				"list_item_id" => 1,
				"php_key" => "key",
				"value" => "",
				"ID_DEF" => 4,
				"page_id" => 5
			)
		);
	}

	protected function dataTest(\Scrivo\ListItemProperty $lip) {

		$v = new \Scrivo\String("A");
		$lip->data = $v;
		$this->assertTrue($v->equals($lip->data));

	}

}

?>