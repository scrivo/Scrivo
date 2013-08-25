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
 * $Id: ModifiedTouchedTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\ModifiedTouched
 */
class ModifiedTouchedTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\ModifiedTouched property test data for running two tests with a
	 * single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"idModified" => 20000,
					"idTouched" => 30000,
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"idModified" => 20001,
					"idTouched" => 30001,
				)
			)
		);
	}

	/**
	 * \Scrivo\ModifiedTouched property test data for running a single test
	 * with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"idModified" => 20000,
					"idTouched" => 30000,
				),
				"argument 2" => (object) array(
					"idModified" => 20001,
					"idTouched" => 30001,
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(
			array("init.yml", "users_and_roles.yml", "modified_touched.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\ModifiedTouched properties
	 * from test data.
	 *
	 * @param \Scrivo\ModifiedTouched $o A reference to a
	 *   \Scrivo\ModifiedTouched object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setModifiedTouchedProperties(&$o, $d) {

		$o->idModified = $d->idModified;
		$o->idTouched = $d->idTouched;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\ModifiedTouched object equal those of the given data.
	 *
	 * @param \Scrivo\ModifiedTouched $o The \Scrivo\ModifiedTouched object
	 *    under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkModifiedTouchedProperties($o, $d) {

		$this->assertEquals($o->idModified, $d->idModified);
		$this->assertEquals($o->idTouched, $d->idTouched);
	}

	/**
	 * Test if a \Scrivo\ModifiedTouched object can be created/inserted into
	 * the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\ModifiedTouched object and populate its
		// fields.
		$o = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($o, $d);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->idModified, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\ModifiedTouched::fetch(
			self::$context, $o->idModified, $o->idTouched);
		$this->checkModifiedTouchedProperties($o, $d);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\ModifiedTouched(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\ModifiedTouched(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\ModifiedTouched object
	 * is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidModifiedTouchedCreation() {
		$o = \Scrivo\ModifiedTouched::fetch(self::$context, 12345, 12345);
	}

	/**
	 * Test exception thrown when using invalid parameters to when loading
	 * a \Scrivo\ModifiedTouched object from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidModifiedTouchedParameters2() {
		$o = \Scrivo\ModifiedTouched::fetch(self::$context, null, 12345);
	}

	/**
	 * Test the creation of lists of type \Scrivo\ModifiedTouched.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\ModifiedTouched objects.
		$mt = \Scrivo\ModifiedTouched::select(self::$context);

		// Create two \Scrivo\ModifiedTouched objects.
		$o1 = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ModifiedTouched objects.
		$mt = \Scrivo\ModifiedTouched::select(self::$context);
		$this->assertCount(2, $mt);

		// Test if retrieved objects match against the test data.
		$this->checkModifiedTouchedProperties($mt[0], $d1);
	}

	/**
	 * Test deletion of \Scrivo\ModifiedTouched objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\ModifiedTouched objects.
		$o1 = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ModifiedTouched objects.
		$mt = \Scrivo\ModifiedTouched::select(self::$context);
		$this->assertCount(2, $mt);

		// Delete the first object.
		\Scrivo\ModifiedTouched::delete(
			self::$context, $o1->idModified, $o1->idTouched);

		// Select all \Scrivo\ModifiedTouched objects.
		$mt = \Scrivo\ModifiedTouched::select(self::$context);
		$this->assertCount(1, $mt);

		// Delete the second object.
		\Scrivo\ModifiedTouched::delete(
			self::$context, $o2->idModified, $o2->idTouched);

		// Select all \Scrivo\ModifiedTouched objects.
		$mt = \Scrivo\ModifiedTouched::select(self::$context);
		$this->assertCount(0, $mt);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\ModifiedTouched object and do some crud operations
		// on it.
		$tmp = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\ModifiedTouched object as editor
		// should not succeed.
		$test = "";
		$new = new \Scrivo\ModifiedTouched($context);
		$this->setModifiedTouchedProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ModifiedTouched object as editor should
		// succeed ...
		$object = \Scrivo\ModifiedTouched::fetch(
			$context, $tmp->idModified, $tmp->idTouched);

		// And deleting should not be possible too.
		$test = "";
		try {
			\Scrivo\ModifiedTouched::delete(
				$context, $object->idModified, $object->idTouched);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\ModifiedTouched::select($context);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\ModifiedTouched object to do operations on
		$tmp = new \Scrivo\ModifiedTouched(self::$context);
		$this->setModifiedTouchedProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\ModifiedTouched object as member
		// should not succeed.
		$test = "";
		$new = new \Scrivo\ModifiedTouched($context);
		$this->setModifiedTouchedProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ModifiedTouched object as member should succeed.
		$object = \Scrivo\ModifiedTouched::fetch(
			$context, $tmp->idModified, $tmp->idTouched);

		// Loading all \Scrivo\ModifiedTouched objects as member should
		// succeed.
		$all = \Scrivo\ModifiedTouched::select($context);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\ModifiedTouched(self::$context);
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\ModifiedTouched::fetch(
				$this->ctxDbFailureStub(), $tmp->idModified, $tmp->idTouched);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\ModifiedTouched($this->ctxDbFailureStub());
			$object->insert();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\ModifiedTouched::delete(
				$this->ctxDbFailureStub(), $tmp->idModified, $tmp->idTouched);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$mt =
				\Scrivo\ModifiedTouched::select($this->ctxDbFailureStub());
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>