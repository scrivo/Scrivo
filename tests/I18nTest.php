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
 * $Id: I18nTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\I18n
 */
class I18nTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test basic functionality of the Scrivo I18n object.
	 */
	public function testI18n() {

		$i18n = new \Scrivo\I18n(new \Scrivo\String("nl_NL"));

		$this->assertEquals("Annuleer", $i18n["Cancel"]);
	}

	/**
	 * Test functionality of the Scrivo I18n object if no language data
	 * exists.
	 */
	public function testI18n2() {

		$i18n = new \Scrivo\I18n(new \Scrivo\String("nl_NL"),
			new \Scrivo\String("invaliddir"));

		$this->assertEquals("Boeh", $i18n["Boeh"]);
	}

	/**
	 * Test exception thrown when trying to modify an entry.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testOffsetSet() {

		$i18n = new \Scrivo\I18n(new \Scrivo\String("nl_NL"));

		$i18n["Cancel"] = "Blah blah";
	}

	/**
	 * Test exception thrown when trying to see if an entry is set.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testIsset() {

		$i18n = new \Scrivo\I18n(new \Scrivo\String("nl_NL"));

		$tmp = isset($i18n["Cancel"]);
	}

	/**
	 * Test exception thrown when trying to unset an entry.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testUnset() {

		$i18n = new \Scrivo\I18n(new \Scrivo\String("nl_NL"));

		unset($i18n["Cancel"]);
	}

}

?>