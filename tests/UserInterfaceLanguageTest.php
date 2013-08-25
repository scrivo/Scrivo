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
 * $Id: UserInterfaceLanguageTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\UserInterfaceLanguage
 */
class UserInterfaceLanguageTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\UserInterfaceLanguage property test data for running two tests
	 * with a single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"isoCode" => new \Scrivo\String("name_nl"),
					"description" => new \Scrivo\String("Nederlands"),
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"isoCode" => new \Scrivo\String("name_en"),
					"description" => new \Scrivo\String("English"),
				)
			)
		);
	}

	/**
	 * \Scrivo\UserInterfaceLanguage property test data for running a single
	 * test with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"isoCode" => new \Scrivo\String("name_nl"),
					"description" => new \Scrivo\String("Nederlands"),
				),
				"argument 2" => (object) array(
					"isoCode" => new \Scrivo\String("name_en"),
					"description" => new \Scrivo\String("English"),
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"user_interface_language.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\UserInterfaceLanguage
	 * properties from test data.
	 *
	 * @param \Scrivo\UserInterfaceLanguage $o A reference to a
	 *   \Scrivo\UserInterfaceLanguage object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setUserInterfaceLanguageProperties(&$o, $d) {

		$o->isoCode = $d->isoCode;
		$o->description = $d->description;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\UserInterfaceLanguage object equal those of the given data.
	 *
	 * @param \Scrivo\UserInterfaceLanguage $o The
	 *    \Scrivo\UserInterfaceLanguage object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkUserInterfaceLanguageProperties($o, $d) {

		$this->assertTrue($o->isoCode->equals($d->isoCode));
		$this->assertTrue($o->description->equals($d->description));
	}

	/**
	 * Test if a \Scrivo\UserInterfaceLanguage object can be created/inserted
	 * into the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\UserInterfaceLanguage object and populate
		// its fields.
		$o = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($o, $d);
		$this->assertTrue($o->isoCode->equals($d->isoCode));

		// Insert it into the database.
		$o->insert();
		$this->assertNotNull($o->isoCode);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\UserInterfaceLanguage::fetch(self::$context, $o->isoCode);
		$this->checkUserInterfaceLanguageProperties($o, $d);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\UserInterfaceLanguage(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\UserInterfaceLanguage(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\UserInterfaceLanguage
	 * object is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidUserInterfaceLanguageCreation() {
		$o = \Scrivo\UserInterfaceLanguage::fetch(
			self::$context, new \Scrivo\String("12345"));
	}

	/**
	 * Test if the properties of an \Scrivo\UserInterfaceLanguage object can
	 * be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\UserInterfaceLanguage object and populate its
		// fields.
		$o = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\UserInterfaceLanguage::fetch(self::$context, $o->isoCode);
		$this->checkUserInterfaceLanguageProperties($o, $d1);

		// Set the new properity values.
		$o->description = $d2->description;
		$o->update();

		// Reload the \Scrivo\UserInterfaceLanguage object and check its
		// property values.
		$o2 = \Scrivo\UserInterfaceLanguage::fetch(self::$context, $d1->isoCode);
		$this->assertTrue($o2->description->equals($d2->description));
	}

	/**
	 * Test the creation of lists of type \Scrivo\UserInterfaceLanguage.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\UserInterfaceLanguage objects.
		$uiLanguages = \Scrivo\UserInterfaceLanguage::select(self::$context);

		// Create two \Scrivo\UserInterfaceLanguage objects.
		$o1 = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\UserInterfaceLanguage objects.
		$uiLanguages = \Scrivo\UserInterfaceLanguage::select(self::$context);
		$this->assertCount(2, $uiLanguages);

		// Test if retrieved objects match against the test data.
		$this->checkUserInterfaceLanguageProperties(
			$uiLanguages[(string)$o1->isoCode], $d1);
	}

	/**
	 * Test deletion of \Scrivo\UserInterfaceLanguage objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\UserInterfaceLanguage objects.
		$o1 = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\UserInterfaceLanguage objects.
		$uiLanguages = \Scrivo\UserInterfaceLanguage::select(self::$context);
		$this->assertCount(2, $uiLanguages);

		// Delete the first object.
		\Scrivo\UserinterfaceLanguage::delete(self::$context, $o1->isoCode);

		// Select all \Scrivo\UserInterfaceLanguage objects.
		$uiLanguages = \Scrivo\UserInterfaceLanguage::select(self::$context);
		$this->assertCount(1, $uiLanguages);

		// Delete the second object.
		\Scrivo\UserinterfaceLanguage::delete(self::$context, $o2->isoCode);

		// Select all \Scrivo\UserInterfaceLanguage objects.
		$uiLanguages = \Scrivo\UserInterfaceLanguage::select(self::$context);
		$this->assertCount(0, $uiLanguages);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\UserInterfaceLanguage object and do some crud
		// operations on it.
		$tmp = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\UserInterfaceLanguage object as
		// editor should not succeed.
		$test = "";
		$new = new \Scrivo\UserInterfaceLanguage($context);
		$this->setUserInterfaceLanguageProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\UserInterfaceLanguage object as editor should
		// succeed ...
		$object = \Scrivo\UserInterfaceLanguage::fetch($context, $tmp->isoCode);

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
			\Scrivo\UserinterfaceLanguage::delete($context, $object->isoCode);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\UserInterfaceLanguage::select($context);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\UserInterfaceLanguage object to do operations on
		$tmp = new \Scrivo\UserInterfaceLanguage(self::$context);
		$this->setUserInterfaceLanguageProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Loading a \Scrivo\UserInterfaceLanguage object as member should
		// succeed.
		$object = \Scrivo\UserInterfaceLanguage::fetch($context, $tmp->isoCode);

		// Creating/inserting a new \Scrivo\UserInterfaceLanguage object as
		// member should not succeed.
		$test = "";
		$new = new \Scrivo\UserInterfaceLanguage($context);
		$this->setUserInterfaceLanguageProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading all \Scrivo\UserInterfaceLanguage objects as member should
		// succeed.
		$all = \Scrivo\UserInterfaceLanguage::select($context);

		$this->assertEquals("Catch and release", $test);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\UserInterfaceLanguage(self::$context);

		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\UserInterfaceLanguage::fetch(
				$this->ctxDbFailureStub(), $tmp->isoCode);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object =
				new \Scrivo\UserInterfaceLanguage($this->ctxDbFailureStub());
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
			$object =
				new \Scrivo\UserInterfaceLanguage($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\UserinterfaceLanguage::delete(
				$this->ctxDbFailureStub(), $tmp->isoCode);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$uiLanguages = \Scrivo\UserInterfaceLanguage::select(
				$this->ctxDbFailureStub());
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>