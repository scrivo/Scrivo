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
 * $Id: ApplicationDefinitionTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\ApplicationDefinition
 */
class ApplicationDefinitionTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\ApplicationDefinition property test data for running two tests
	 * with a single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"title" => new \Scrivo\Str(
						"an external application"),
					"description" => new \Scrivo\Str(
						"an external application description"),
					"location" => new \Scrivo\Str(
						"http://www.scrivo.nl/scrivo_apps/index.php"),
					"type" => \Scrivo\ApplicationDefinition::TYPE_URL,
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"title" => new \Scrivo\Str(
						"an internal application"),
					"description" => new \Scrivo\Str(
						"an internal application description"),
					"location" => new \Scrivo\Str(""),
					"type" => \Scrivo\ApplicationDefinition::TYPE_LISTVIEW,
				)
			)
		);
	}

	/**
	 * \Scrivo\ApplicationDefinition property test data for running a single
	 * test with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"title" => new \Scrivo\Str(
						"an external application"),
					"description" => new \Scrivo\Str(
						"an external application description"),
					"location" => new \Scrivo\Str(
						"http://www.scrivo.nl/scrivo_apps/index.php"),
					"type" => \Scrivo\ApplicationDefinition::TYPE_URL,
				),
				"argument 2" => (object) array(
					"title" => new \Scrivo\Str(
						"an internal application"),
					"description" => new \Scrivo\Str(
						"an internal application description"),
					"location" => new \Scrivo\Str(""),
					"type" => \Scrivo\ApplicationDefinition::TYPE_LISTVIEW,
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"application_definition.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\ApplicationDefinition
	 * properties from test data.
	 *
	 * @param \Scrivo\ApplicationDefinition $o A reference to a
	 *   \Scrivo\ApplicationDefinition object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setApplicationDefinitionProperties(&$o, $d) {

		$o->title = $d->title;
		$o->description = $d->description;
		$o->location = $d->location;
		$o->type = $d->type;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\ApplicationDefinition object equal those of the given data.
	 *
	 * @param \Scrivo\ApplicationDefinition $o The
	 *   \Scrivo\ApplicationDefinition object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkApplicationDefinitionProperties($o, $d) {

		$this->assertTrue($o->title->equals($d->title));
		$this->assertTrue($o->description->equals($d->description));
		$this->assertTrue($o->location->equals($d->location));
		$this->assertEquals($o->type, $d->type);
	}

	/**
	 * Test if a \Scrivo\ApplicationDefinition object can be created/inserted
	 * into the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\ApplicationDefinition object and populate
		// its fields.
		$o = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\ApplicationDefinition::fetch(self::$context, $o->id);
		$this->checkApplicationDefinitionProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\ApplicationDefinition::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\ApplicationDefinition(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\ApplicationDefinition(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test invalid property set access to the type property.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidType() {
		$o = new \Scrivo\ApplicationDefinition(self::$context);
		$o->type = 123456789;
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\ApplicationDefinition
	 * object is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidApplicationDefinitionCreation() {
		$o = \Scrivo\ApplicationDefinition::fetch(self::$context, 12345);
	}

	/**
	 * Test if the properties of an \Scrivo\ApplicationDefinition object can
	 * be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\ApplicationDefinition object and populate
		// its fields.
		$o = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\ApplicationDefinition::fetch(self::$context, $o->id);
		$this->checkApplicationDefinitionProperties($o, $d1);

		// Set the new properity values.
		$this->setApplicationDefinitionProperties($o, $d2);
		$o->update();

		// Reload the \Scrivo\ApplicationDefinition object and check its
		// property values.
		$o2 = \Scrivo\ApplicationDefinition::fetch(self::$context, $o->id);
		$this->checkApplicationDefinitionProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\ApplicationDefinition.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\ApplicationDefinition objects.
		$applications = \Scrivo\ApplicationDefinition::select(self::$context);

		// Create two \Scrivo\ApplicationDefinition objects.
		$o1 = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ApplicationDefinition objects.
		$applications = \Scrivo\ApplicationDefinition::select(self::$context);
		$this->assertCount(3, $applications);

		// Test if retrieved objects match against the test data.
		$this->checkApplicationDefinitionProperties(
			$applications[$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\ApplicationDefinition objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\ApplicationDefinition objects.
		$o1 = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ApplicationDefinition objects.
		$applications = \Scrivo\ApplicationDefinition::select(self::$context);
		$this->assertCount(3, $applications);

		// Delete the first object.
		\Scrivo\ApplicationDefinition::delete(self::$context, $o1->id);

		// Select all \Scrivo\ApplicationDefinition objects.
		$applications = \Scrivo\ApplicationDefinition::select(self::$context);
		$this->assertCount(2, $applications);

		// Delete the second object.
		\Scrivo\ApplicationDefinition::delete(self::$context, $o2->id);

		// Select all \Scrivo\ApplicationDefinition objects.
		$applications = \Scrivo\ApplicationDefinition::select(self::$context);
		$this->assertCount(1, $applications);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\ApplicationDefinition object and do some crud
		// operations on it.
		$tmp = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\ApplicationDefinition object as
		// editor should not succeed.
		$test = "";
		$new = new \Scrivo\ApplicationDefinition($context);
		$this->setApplicationDefinitionProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ApplicationDefinition object as editor should
		// succeed ...
		$object = \Scrivo\ApplicationDefinition::fetch($context, $tmp->id);

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
			\Scrivo\ApplicationDefinition::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\ApplicationDefinition::select($context);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\ApplicationDefinition object to do operations on
		$tmp = new \Scrivo\ApplicationDefinition(self::$context);
		$this->setApplicationDefinitionProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\ApplicationDefinition object as
		// member should not succeed.
		$test = "";
		$new = new \Scrivo\ApplicationDefinition($context);
		$this->setApplicationDefinitionProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ApplicationDefinition object as member should
		// succeed.
		$object = \Scrivo\ApplicationDefinition::fetch($context, $tmp->id);

		// Loading all \Scrivo\ApplicationDefinition objects as member should
		// succeed.
		$all = \Scrivo\ApplicationDefinition::select($context);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\ApplicationDefinition(self::$context);
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\ApplicationDefinition::fetch(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object =
				new \Scrivo\ApplicationDefinition($this->ctxDbFailureStub());
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
				new \Scrivo\ApplicationDefinition($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\ApplicationDefinition::delete(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$applications = \Scrivo\ApplicationDefinition::select(
				$this->ctxDbFailureStub());
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>