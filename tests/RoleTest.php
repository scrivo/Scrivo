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
 * $Id: RoleTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\Role
 */
class RoleTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\Role property test data for running two tests with a single
	 * argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"type" => \Scrivo\Role::PUBLIC_ROLE,
					"title" => new \Scrivo\Str("a public role"),
					"description" =>
						new \Scrivo\Str("a public role description"),
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"type" => \Scrivo\Role::EDITOR_ROLE,
					"title" => new \Scrivo\Str("an editor role"),
					"description" =>
						new \Scrivo\Str("an editor role description"),
				)
			)
		);
	}

	/**
	 * \Scrivo\Role property test data for running a single test with two
	 * arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"type" => \Scrivo\Role::PUBLIC_ROLE,
					"title" => new \Scrivo\Str("a public role"),
					"description" =>
						new \Scrivo\Str("a public role description"),
				),
				"argument 2" => (object) array(
					"type" => \Scrivo\Role::EDITOR_ROLE,
					"title" => new \Scrivo\Str("an editor role"),
					"description" =>
						new \Scrivo\Str("an editor role description"),
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\Role properties from
	 * test data.
	 *
	 * @param \Scrivo\Role $o A reference to a \Scrivo\Role object for
	 *   which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setRoleProperties(&$o, $d) {

		$o->type = $d->type;
		$o->title = $d->title;
		$o->description = $d->description;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\Role object equal those of the given data.
	 *
	 * @param \Scrivo\Role $o The \Scrivo\Role object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkRoleProperties($o, $d) {

		$this->assertEquals($o->type, $d->type);
		if (!$o->title->equals($d->title)) {
			echo("\n".$o->title. "-" . $d->title."\n");
		}
		$this->assertTrue($o->title->equals($d->title));
		$this->assertTrue($o->description->equals($d->description));
	}

	/**
	 * Test if a \Scrivo\Role object can be created/inserted into the
	 * database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\Role object and populate its fields.
		$o = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\Role::fetch(self::$context, $o->id);
		$this->checkRoleProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\Role::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\Role(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\Role(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\Role object is
	 * loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidRoleCreation() {
		$o = \Scrivo\Role::fetch(self::$context, 12345);
	}

	/**
	 * Test if the properties of an \Scrivo\Role object can be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\Role object and populate its fields.
		$o = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\Role::fetch(self::$context, $o->id);
		$this->checkRoleProperties($o, $d1);

		// Set the new properity values.
		$this->setRoleProperties($o, $d2);
		$o->update();

		// Reload the \Scrivo\Role object and check its property values.
		$o2 = \Scrivo\Role::fetch(self::$context, $o->id);
		$this->checkRoleProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\Role.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Create two \Scrivo\Role objects.
		$o1 = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\Role objects of the public role type: that's
		// two from initial database test data and one from passed data.
		$roles = \Scrivo\Role::select(self::$context, \Scrivo\Role::PUBLIC_ROLE);
		$this->assertCount(3, $roles);

		// Test if retrieved objects match against the test data.
		$this->checkRoleProperties($roles[$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\Role objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\Role objects.
		$o1 = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\Role objects of the public role type: one
		// from passed test data and two from initial test data.
		$roles = \Scrivo\Role::select(self::$context, \Scrivo\Role::PUBLIC_ROLE);
		$this->assertCount(3, $roles);

		// Delete the first object.
		\Scrivo\Role::delete(self::$context, $o1->id);

		// Select all \Scrivo\Role objects of the public role type: two
		// from initial test data.
		$roles = \Scrivo\Role::select(self::$context, \Scrivo\Role::PUBLIC_ROLE);
		$this->assertCount(2, $roles);

		// Delete the second object.
		\Scrivo\Role::delete(self::$context, $o2->id);

		// Select all \Scrivo\Role objects of the editor role type: one
		// from initial test data, the one that was created using passed
		// data is removed.
		$roles = \Scrivo\Role::select(self::$context, \Scrivo\Role::EDITOR_ROLE);
		$this->assertCount(1, $roles);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\Role object and do some crud operations on it.
		$tmp = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\Role object as editor should
		// not succeed.
		$test = "";
		$new = new \Scrivo\Role($context);
		$this->setRoleProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\Role object as editor should succeed ...
		$object = \Scrivo\Role::fetch($context, $tmp->id);

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
			\Scrivo\Role::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\Role::select($context, \Scrivo\Role::PUBLIC_ROLE);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\Role object to do operations on
		$tmp = new \Scrivo\Role(self::$context);
		$this->setRoleProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\Role object as member should not
		// succeed.
		$test = "";
		$new = new \Scrivo\Role($context);
		$this->setRoleProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\Role object as member should succeed.
		$object = \Scrivo\Role::fetch($context, $tmp->id);

		// Loading all \Scrivo\Role objects as member should succeed.
		$all = \Scrivo\Role::select($context, \Scrivo\Role::PUBLIC_ROLE);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\Role(self::$context);
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\Role::fetch($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\Role($this->ctxDbFailureStub());
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
			$object = new \Scrivo\Role($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\Role::delete($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$roles = \Scrivo\Role::select(
				$this->ctxDbFailureStub(), \Scrivo\Role::PUBLIC_ROLE);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>