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
 * $Id: PageDefinitionTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageDefinition
 */
class PageDefinitionTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\PageDefinition property test data for running two tests with a
	 * single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"title" => new \Scrivo\Str("A template"),
					"description" => new \Scrivo\Str("Blah 1"),
					"action" => new \Scrivo\Str("templates/a.php"),
					"configOnly" => true,
					"typeSet" => array(3),
					"defaultTabId" => 0,
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"title" => new \Scrivo\Str("Another template"),
					"description" => new \Scrivo\Str("Blah 2"),
					"action" => new \Scrivo\Str("templates/b.php"),
					"configOnly" => false,
					"typeSet" => array(1,2),
					"defaultTabId" => 0,
				)
			)
		);
	}

	/**
	 * \Scrivo\PageDefinition property test data for running a single test with
	 * two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"title" => new \Scrivo\Str("A template"),
					"description" => new \Scrivo\Str("Blah 1"),
					"action" => new \Scrivo\Str("templates/a.php"),
					"configOnly" => true,
					"typeSet" => array(3),
					"defaultTabId" => 0,
				),
				"argument 2" => (object) array(
					"title" => new \Scrivo\Str("Another template"),
					"description" => new \Scrivo\Str("Blah 2"),
					"action" => new \Scrivo\Str("templates/b.php"),
					"configOnly" => false,
					"typeSet" => array(1, 2),
					"defaultTabId" => 0,
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page_definition.yml", "page.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\PageDefinition properties
	 * from test data.
	 *
	 * @param \Scrivo\PageDefinition $o A reference to a \Scrivo\PageDefinition
	 *   object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setTemplateProperties(&$o, $d) {

		$o->title = $d->title;
		$o->description = $d->description;
		$o->action = $d->action;
		$o->configOnly = $d->configOnly;
		$o->typeSet = $d->typeSet;
		$o->defaultTabId = $d->defaultTabId;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\PageDefinition object equal those of the given data.
	 *
	 * @param \Scrivo\PageDefinition $o The \Scrivo\PageDefinition object under
	 *    test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkTemplateProperties($o, $d) {

		$this->assertTrue($o->title->equals($d->title));
		$this->assertTrue($o->description->equals($d->description));
		$this->assertTrue($o->action->equals($d->action));
		$this->assertEquals($o->configOnly, $d->configOnly);
		$this->assertEquals($o->typeSet, $d->typeSet);
		$this->assertEquals($o->defaultTabId, $d->defaultTabId);
	}

	/**
	 * Test if a \Scrivo\PageDefinition object can be created/inserted into the
	 * database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\PageDefinition object and populate its
		// fields.
		$o = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\PageDefinition::fetch(self::$context, $o->id);
		$this->checkTemplateProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\PageDefinition::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\PageDefinition(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\PageDefinition(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test if the correct amount of template properties and tabs are loaded
	 * from the database.
	 */
	function testLoad() {

		// Load the template from test data.
		$t = \Scrivo\PageDefinition::fetch(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);

		// It should have three tabs
		$this->assertCount(3, $t->tabs);

		// It should have six properties
		$this->assertCount(6, (array)$t->properties);
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\PageDefinition object is
	 * loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidTemplateCreation() {
		$o = \Scrivo\PageDefinition::fetch(self::$context, 12345);
	}

	/**
	 * Test if the properties of an \Scrivo\PageDefinition object can be
	 * updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\PageDefinition object and populate its
		// fields.
		$o = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\PageDefinition::fetch(self::$context, $o->id);
		$this->checkTemplateProperties($o, $d1);

		// Set the new properity values.
		$this->setTemplateProperties($o, $d2);
		$o->update();

		// Reload the \Scrivo\PageDefinition object and check its property
		// values.
		$o2 = \Scrivo\PageDefinition::fetch(self::$context, $o->id);
		$this->checkTemplateProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\PageDefinition.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\PageDefinition objects.
		$templates = \Scrivo\PageDefinition::select(self::$context);

		// Create two \Scrivo\PageDefinition objects.
		$o1 = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\PageDefinition objects.
		$templates = \Scrivo\PageDefinition::select(self::$context);
		$this->assertCount(4, $templates);

		// Test if retrieved objects match against the test data.
		$this->checkTemplateProperties($templates[$o1->id], $d1);
	}

	/**
	 * Test the creation of lists of type \Scrivo\PageDefinition.
	 *
	 */
	function testSelectSelectable() {

		// Select all \Scrivo\PageDefinition objects.
		$templates = \Scrivo\PageDefinition::selectSelectable(
			self::$context, self::PAGE_HOME_ID);

		$this->assertCount(1, $templates);
	}

	/**
	 * Test deletion of \Scrivo\PageDefinition objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\PageDefinition objects.
		$o1 = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\PageDefinition objects.
		$templates = \Scrivo\PageDefinition::select(self::$context);
		$this->assertCount(4, $templates);

		// Delete the first object.
		\Scrivo\PageDefinition::delete(self::$context, $o1->id);

		// Select all \Scrivo\PageDefinition objects.
		$templates = \Scrivo\PageDefinition::select(self::$context);
		$this->assertCount(3, $templates);

		// Delete the second object.
		\Scrivo\PageDefinition::delete(self::$context, $o2->id);

		// Select all \Scrivo\PageDefinition objects.
		$templates = \Scrivo\PageDefinition::select(self::$context);
		$this->assertCount(2, $templates);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\PageDefinition object and do some crud operations
		// on it.
		$tmp = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\PageDefinition object as editor
		// should not succeed.
		$test = "";
		$new = new \Scrivo\PageDefinition($context);
		$this->setTemplateProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\PageDefinition object as editor should succeed ...
		$object = \Scrivo\PageDefinition::fetch($context, $tmp->id);

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
			\Scrivo\PageDefinition::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\PageDefinition::select($context);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\PageDefinition object to do operations on
		$tmp = new \Scrivo\PageDefinition(self::$context);
		$this->setTemplateProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\PageDefinition object as member
		// should not succeed.
		$test = "";
		$new = new \Scrivo\PageDefinition($context);
		$this->setTemplateProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\PageDefinition object as member should succeed.
		$object = \Scrivo\PageDefinition::fetch($context, $tmp->id);

		// Loading all \Scrivo\PageDefinition objects as member should succeed.
		$all = \Scrivo\PageDefinition::select($context);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\PageDefinition(self::$context);
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\PageDefinition::fetch(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\PageDefinition($this->ctxDbFailureStub());
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
			$object = new \Scrivo\PageDefinition($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\PageDefinition::delete($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$templates =
				\Scrivo\PageDefinition::select($this->ctxDbFailureStub());
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>