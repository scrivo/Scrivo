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
 * $Id: LanguageTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\Language
 */
class LanguageTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\Language property test data for running two tests with a
	 * single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"shortList" => true,
					"isoCode" => new \Scrivo\Str(
						""),
					"family" => new \Scrivo\Str(
						""),
					"nameEn" => new \Scrivo\Str(
						""),
					"nameNl" => new \Scrivo\Str(
						""),
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"shortList" => false,
					"isoCode" => new \Scrivo\Str(
						""),
					"family" => new \Scrivo\Str(
						""),
					"nameEn" => new \Scrivo\Str(
						""),
					"nameNl" => new \Scrivo\Str(
						""),
				)
			)
		);
	}

	/**
	 * \Scrivo\Language property test data for running a single test with
	 * two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"shortList" => true,
					"isoCode" => new \Scrivo\Str(
						""),
					"family" => new \Scrivo\Str(
						""),
					"nameEn" => new \Scrivo\Str(
						""),
					"nameNl" => new \Scrivo\Str(
						""),
				),
				"argument 2" => (object) array(
					"shortList" => false,
					"isoCode" => new \Scrivo\Str(
						""),
					"family" => new \Scrivo\Str(
						""),
					"nameEn" => new \Scrivo\Str(
						""),
					"nameNl" => new \Scrivo\Str(
						""),
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"language.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\Language properties from
	 * test data.
	 *
	 * @param \Scrivo\Language $o A reference to a \Scrivo\Language
	 *   object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setLanguageProperties(&$o, $d) {

		$o->shortList = $d->shortList;
		$o->isoCode = $d->isoCode;
		$o->family = $d->family;
		$o->nameEn = $d->nameEn;
		$o->nameNl = $d->nameNl;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\Language object equal those of the given data.
	 *
	 * @param \Scrivo\Language $o The \Scrivo\Language object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkLanguageProperties($o, $d) {

		$this->assertEquals($o->shortList, $d->shortList);
		$this->assertTrue($o->isoCode->equals($d->isoCode));
		$this->assertTrue($o->family->equals($d->family));
		$this->assertTrue($o->nameEn->equals($d->nameEn));
		$this->assertTrue($o->nameNl->equals($d->nameNl));
	}

	/**
	 * Test if a \Scrivo\Language object can be created/inserted into the
	 * database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\Language object and populate its fields.
		$o = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\Language::fetch(self::$context, $o->id);
		$this->checkLanguageProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\Language::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\Language(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\Language(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\Language object is
	 * loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidLanguageCreation() {
		$o = \Scrivo\Language::fetch(self::$context, 12345);
	}

	/**
	 * Test if the properties of an \Scrivo\Language object can be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\Language object and populate its fields.
		$o = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\Language::fetch(self::$context, $o->id);
		$this->checkLanguageProperties($o, $d1);

		// Set the new properity values.
		$this->setLanguageProperties($o, $d2);
		$o->update();

		// Reload the \Scrivo\Language object and check its property values.
		$o2 = \Scrivo\Language::fetch(self::$context, $o->id);
		$this->checkLanguageProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\Language.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\Language objects.
		$languages = \Scrivo\Language::select(self::$context, false);

		// Create two \Scrivo\Language objects.
		$o1 = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\Language objects.
		$languages = \Scrivo\Language::select(self::$context, false);
		$this->assertCount(4, $languages);

		// Test if retrieved objects match against the test data.
		$this->checkLanguageProperties($languages[$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\Language objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\Language objects.
		$o1 = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\Language objects.
		$languages = \Scrivo\Language::select(self::$context, false);
		$this->assertCount(4, $languages);

		// Delete the first object.
		\Scrivo\Language::delete(self::$context, $o1->id);

		// Select all \Scrivo\Language objects.
		$languages = \Scrivo\Language::select(self::$context, false);
		$this->assertCount(3, $languages);

		// Delete the second object.
		\Scrivo\Language::delete(self::$context, $o2->id);

		// Select all \Scrivo\Language objects.
		$languages = \Scrivo\Language::select(self::$context, false);
		$this->assertCount(2, $languages);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\Language object and do some crud operations
		// on it.
		$tmp = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\Language object as editor
		// should not succeed.
		$test = "";
		$new = new \Scrivo\Language($context);
		$this->setLanguageProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\Language object as editor should succeed ...
		$object = \Scrivo\Language::fetch($context, $tmp->id);

		// ... but updating not.
		$test = "";
		try {
			$object->update();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// And deleting should not be possible too.
		$test = "";
		try {
			\Scrivo\Language::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\Language::select($context, false);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\Language object to do operations on
		$tmp = new \Scrivo\Language(self::$context);
		$this->setLanguageProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\Language object as member
		// should not succeed.
		$test = "";
		$new = new \Scrivo\Language($context);
		$this->setLanguageProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\Language object as member should succeed.
		$object = \Scrivo\Language::fetch($context, $tmp->id);

		// Loading all \Scrivo\Language objects as member should succeed.
		$all = \Scrivo\Language::select($context, false);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\Language(self::$context);
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object =
				\Scrivo\Language::fetch($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\Language($this->ctxDbFailureStub());
			$object->insert();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform update operation.
		$test = "";
		try {
			// Note: improper use of 'update' method, just to generate an
			// exception.
			$object = new \Scrivo\Language($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\Language::delete($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$languages =
				\Scrivo\Language::select($this->ctxDbFailureStub(), false);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>