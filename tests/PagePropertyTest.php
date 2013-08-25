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
 * $Id: PagePropertyTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageProperty\Image
 *
 * Not a complete set. See the derived properties for other tests.
 */
class PagePropertyTest extends ScrivoDatabaseTestCase {


	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page_definition.yml", "page.yml"));
	}

	/**
	 * Test exception thrown when using an invalid property type.
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidType() {

		$ad = \Scrivo\PageProperty::create(
			new \Scrivo\Page(), array(
				"type" => "Invalid_type",
				"php_key" =>  new \Scrivo\String("SEL"),
				"ID_DEF" => 3,
				"value" => "",
				"VALUE2" => ""
			));
	}

	/**
	 * Test exception thrown when setting an nonexistent property.
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidSet() {

		$ad = \Scrivo\PageProperty::create(
			new \Scrivo\Page(), array(
				"type" => \Scrivo\PagePropertyDefinition::TYPE_IMAGE,
				"php_key" =>  new \Scrivo\String("SEL"),
				"ID_DEF" => 3,
				"value" => "",
				"VALUE2" => ""
			));

		$ad->hatseFlatse = 2;
	}

	/**
	 * Test exception thrown when getting an nonexistent property.
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidGet() {

		$ad = \Scrivo\PageProperty::create(
			new \Scrivo\Page(), array(
				"type" => \Scrivo\PagePropertyDefinition::TYPE_IMAGE,
				"php_key" =>  new \Scrivo\String("SEL"),
				"ID_DEF" => 3,
				"value" => "",
				"VALUE2" => ""
			));

		$dummy = $ad->hatseFlatse;
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		// Perform delete operation.
		$test = "";
		try {

			$object = new \Scrivo\Page($this->ctxDbFailureStub());

			$ad = \Scrivo\PageProperty::create(
				$object, array(
					"type" => \Scrivo\PagePropertyDefinition::TYPE_IMAGE,
					"php_key" =>  new \Scrivo\String("SEL"),
					"ID_DEF" => 3,
					"value" => "",
					"VALUE2" => ""
				));

			$ad->update();

		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}
}

?>