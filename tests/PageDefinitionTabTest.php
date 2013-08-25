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
 * $Id: PageDefinitionTabTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageDefinitionTab
 *
 * TODO: create test for testing the sequence of tabs.
 */
class PageDefinitionTabTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\PageDefinitionTab property test data for running two tests with
	 * a single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("prop tab 1"),
					"type" => \Scrivo\PageDefinitionTab::TYPE_PROPERTY_TAB
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("prop tab 2"),
					"type" => \Scrivo\PageDefinitionTab::TYPE_PROPERTY_TAB
				)
			)
		);
	}

	/**
	 * \Scrivo\PageDefinitionTab property test data for running a single test
	 * with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("prop tab 1"),
					"type" => \Scrivo\PageDefinitionTab::TYPE_PROPERTY_TAB
				),
				"argument 2" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("prop tab 2"),
					"type" => \Scrivo\PageDefinitionTab::TYPE_PROPERTY_TAB
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page_definition.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\PageDefinitionTab
	 * properties from test data.
	 *
	 * @param \Scrivo\PageDefinitionTab $o A reference to a
	 *   \Scrivo\PageDefinitionTab object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setTemplateTabProperties(&$o, $d) {

		$o->pageDefinitionId = $d->pageDefinitionId;
		$o->title = $d->title;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\PageDefinitionTab object equal those of the given data.
	 *
	 * @param \Scrivo\PageDefinitionTab $o The \Scrivo\PageDefinitionTab
	 *    object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkTemplateTabProperties($o, $d) {

		$this->assertEquals($o->pageDefinitionId, $d->pageDefinitionId);
		$this->assertTrue($o->title->equals($d->title));
		$this->assertEquals($o->type, $d->type);
	}

	/**
	 * Test if a \Scrivo\PageDefinitionTab object can be created/inserted into
	 * the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\PageDefinitionTab object and populate its
		// fields.
		$o = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\PageDefinitionTab::fetch(self::$context, $o->id);
		$this->checkTemplateTabProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\PageDefinitionTab::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\PageDefinitionTab(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\PageDefinitionTab(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\PageDefinitionTab object
	 * is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidTemplateTabCreation() {
		$o = \Scrivo\PageDefinitionTab::fetch(self::$context, 12345);
	}

	/**
	 * Test if the properties of an \Scrivo\PageDefinitionTab object can be
	 * updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\PageDefinitionTab object and populate its
		// fields.
		$o = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\PageDefinitionTab::fetch(self::$context, $o->id);
		$this->checkTemplateTabProperties($o, $d1);

		// Set the new properity values.
		$this->setTemplateTabProperties($o, $d2);
		$o->update();

		// Reload the \Scrivo\PageDefinitionTab object and check its property
		// values.
		$o2 = \Scrivo\PageDefinitionTab::fetch(self::$context, $o->id);
		$this->checkTemplateTabProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\PageDefinitionTab.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\PageDefinitionTab objects.
		$templateTabs = \Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);

		// Create two \Scrivo\PageDefinitionTab objects.
		$o1 = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\PageDefinitionTab objects.
		$templateTabs = \Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(5, $templateTabs);

		// Test if retrieved objects match against the test data.
		$this->checkTemplateTabProperties($templateTabs[$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\PageDefinitionTab objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\PageDefinitionTab objects.
		$o1 = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\PageDefinitionTab objects.
		$templateTabs =	\Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(5, $templateTabs);

		// Delete the first object.
		\Scrivo\PageDefinitionTab::delete(self::$context, $o1->id);

		// Select all \Scrivo\PageDefinitionTab objects.
		$templateTabs = \Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(4, $templateTabs);

		// Delete the second object.
		\Scrivo\PageDefinitionTab::delete(self::$context, $o2->id);

		// Select all \Scrivo\PageDefinitionTab objects.
		$templateTabs = \Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(3, $templateTabs);
	}

	/**
	 * Test it the order of the tabs can be adjusted.
	 */
	function testMove() {

		$list = array_values(\Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID));
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_TAB_ID, $list[0]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_CONTENT_TAB_ID, $list[1]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_APLLICATION_TAB_ID, $list[2]->id);

		$list[1]->move();

		$list = array_values(\Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID));
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_TAB_ID, $list[0]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_APLLICATION_TAB_ID, $list[1]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_CONTENT_TAB_ID, $list[2]->id);

		$list[1]->move(\Scrivo\SequenceNo::MOVE_UP);

		$list = array_values(\Scrivo\PageDefinitionTab::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID));
		$this->assertEquals(
			self::PAGE_DEFINITION_APLLICATION_TAB_ID, $list[0]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_TAB_ID, $list[1]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_CONTENT_TAB_ID, $list[2]->id);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\PageDefinitionTab object and do some crud
		// operations on it.
		$tmp = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\PageDefinitionTab object as editor
		// should not succeed.
		$test = "";
		$new = new \Scrivo\PageDefinitionTab($context);
		$this->setTemplateTabProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\PageDefinitionTab object as editor should
		// succeed ...
		$object = \Scrivo\PageDefinitionTab::fetch($context, $tmp->id);

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
			\Scrivo\PageDefinitionTab::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\PageDefinitionTab::select(
			$context, self::PAGE_DEFINITION_FORUM_ID);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\PageDefinitionTab object to do operations on
		$tmp = new \Scrivo\PageDefinitionTab(self::$context);
		$this->setTemplateTabProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\PageDefinitionTab object as member
		// should not succeed.
		$test = "";
		$new = new \Scrivo\PageDefinitionTab($context);
		$this->setTemplateTabProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\PageDefinitionTab object as member should succeed.
		$object = \Scrivo\PageDefinitionTab::fetch($context, $tmp->id);

		// Loading all \Scrivo\PageDefinitionTab objects as member should
		// succeed.
		$all = \Scrivo\PageDefinitionTab::select(
			$context, self::PAGE_DEFINITION_FORUM_ID);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\PageDefinitionTab(self::$context);
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\PageDefinitionTab::fetch(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\PageDefinitionTab($this->ctxDbFailureStub());
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
			$object = new \Scrivo\PageDefinitionTab($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform move operation.
		$test = "";
		try {
			// Note: improper use of 'move' method, just to generate an
			// exception.
			$object = new \Scrivo\PageDefinitionTab($this->ctxDbFailureStub());
			$object->move();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\PageDefinitionTab::delete(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$templateTabs = \Scrivo\PageDefinitionTab::select(
				$this->ctxDbFailureStub(), self::PAGE_DEFINITION_FORUM_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>