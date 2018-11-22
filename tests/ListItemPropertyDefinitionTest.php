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
 * $Id: ListItemPropertyDefinitionTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\ListItemPropertyDefinition
 */
class ListItemPropertyDefinitionTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\ListItemPropertyDefinition property test data for running two
	 * tests with a single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"listItemDefinitionId" =>
						self::LIST_ITEM_DEFINITION_SUBJECT_ID,
					"type" => \Scrivo\ListItemPropertyDefinition::TYPE_IMAGE,
					"typeData" => (object)array(
						"FLOAT" => 6.5,
						"INT" => 6
					),
					"phpSelector" => new \Scrivo\Str("IMAGE"),
					"title" => new \Scrivo\Str("an image"),
					"inList" => true,
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"listItemDefinitionId" =>
						self::LIST_ITEM_DEFINITION_SUBJECT_ID,
					"type" => \Scrivo\ListItemPropertyDefinition::TYPE_COLOR,
					"typeData" => (object)array(
						"location" => "0:yes;1:no"
					),
					"phpSelector" => new \Scrivo\Str("COLOR"),
					"title" => new \Scrivo\Str("a color"),
					"inList" => false,
				)
			)
		);
	}

	/**
	 * \Scrivo\ListItemPropertyDefinition property test data for running a
	 * single test with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"listItemDefinitionId" =>
						self::LIST_ITEM_DEFINITION_SUBJECT_ID,
					"type" => \Scrivo\ListItemPropertyDefinition::TYPE_IMAGE,
					"typeData" => (object)array(),
					"phpSelector" => new \Scrivo\Str("IMAGE"),
					"title" => new \Scrivo\Str("an image"),
					"inList" => true,
				),
				"argument 2" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"listItemDefinitionId" =>
						self::LIST_ITEM_DEFINITION_SUBJECT_ID,
					"type" => \Scrivo\ListItemPropertyDefinition::TYPE_COLOR,
					"typeData" => (object)array(),
					"phpSelector" => new \Scrivo\Str("COLOR"),
					"title" => new \Scrivo\Str("a color"),
					"inList" => false,
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"list_item_definition.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\ListItemPropertyDefinition
	 * properties from test data.
	 *
	 * @param \Scrivo\ListItemPropertyDefinition $o A reference to a
	 *   \Scrivo\ListItemPropertyDefinition object for which to set its
	 *   properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setListItemPropertyProperties(&$o, $d, $skipApp=false) {

		if (!$skipApp) {
			$o->applicationDefinitionId = $d->applicationDefinitionId;
		}
		$o->listItemDefinitionId = $d->listItemDefinitionId;
		$o->type = $d->type;
		$o->typeData = $d->typeData;
		$o->phpSelector = $d->phpSelector;
		$o->title = $d->title;
		$o->inList = $d->inList;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\ListItemPropertyDefinition object equal those of the given data.
	 *
	 * @param \Scrivo\ListItemPropertyDefinition $o The
	 *    \Scrivo\ListItemPropertyDefinition object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkListItemPropertyProperties($o, $d) {

		$this->assertEquals(
			$o->applicationDefinitionId, $d->applicationDefinitionId);
		$this->assertEquals(
			$o->listItemDefinitionId, $d->listItemDefinitionId);
		$this->assertEquals($o->type, $d->type);
		$this->assertEquals($o->typeData, $d->typeData);
		$this->assertTrue($o->phpSelector->equals($d->phpSelector));
		$this->assertTrue($o->title->equals($d->title));
		$this->assertEquals($o->inList, $d->inList);
	}

	/**
	 * Test if a \Scrivo\ListItemPropertyDefinition object can be
	 * created/inserted into the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\ListItemPropertyDefinition object and
		// populate its fields.
		$o = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\ListItemPropertyDefinition::fetch(self::$context, $o->id);
		$this->checkListItemPropertyProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\ListItemPropertyDefinition::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * When set the application id can't be reset.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidSetApplication() {

		$o = \Scrivo\ListItemPropertyDefinition::fetch(
			self::$context, self::LIST_ITEM_PROPERTY_DEFINITION_ID_1);

		$o->applicationDefinitionId = 12345;
	}

	/**
	 * Test exception thrown if an nonexisting
	 * \Scrivo\ListItemPropertyDefinition object is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidListItemPropertyCreation() {
		$o = \Scrivo\ListItemPropertyDefinition::fetch(self::$context, 12345);
	}

	/**
	 * Test if the properties of an \Scrivo\ListItemPropertyDefinition object
	 * can be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\ListItemPropertyDefinition object and
		// populate its fields.
		$o = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\ListItemPropertyDefinition::fetch(self::$context, $o->id);
		$this->checkListItemPropertyProperties($o, $d1);

		// Set the new properity values.
		$this->setListItemPropertyProperties($o, $d2, true);
		$o->update();

		// Reload the \Scrivo\ListItemPropertyDefinition object and check its
		// property values.
		$o2 = \Scrivo\ListItemPropertyDefinition::fetch(self::$context, $o->id);
		$this->checkListItemPropertyProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\ListItemPropertyDefinition.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\ListItemPropertyDefinition objects.
		$properties = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount(
			11, $properties[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);

		// Create two \Scrivo\ListItemPropertyDefinition objects.
		$o1 = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ListItemPropertyDefinition objects.
		$properties = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount(
			13, $properties[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);

		// Test if retrieved objects match against the test data.
		$this->checkListItemPropertyProperties(
			$properties[self::LIST_ITEM_DEFINITION_SUBJECT_ID][$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\ListItemPropertyDefinition objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\ListItemPropertyDefinition objects.
		$o1 = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ListItemPropertyDefinition objects.
		$properties = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount(
			13, $properties[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);

		// Delete the first object.
		\Scrivo\ListItemPropertyDefinition::delete(self::$context, $o1->id);

		// Select all \Scrivo\ListItemPropertyDefinition objects.
		$properties = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount(
			12, $properties[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);

		// Delete the second object.
		\Scrivo\ListItemPropertyDefinition::delete(self::$context, $o2->id);

		// Select all \Scrivo\ListItemPropertyDefinition objects.
		$properties = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount(
			11, $properties[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);
	}

	/**
	 * Test if the order of the list item definitions can be adjusted.
	 */
	function testMove() {

		$list = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$list = array_values($list[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_1, $list[0]->id);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_2, $list[1]->id);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_3, $list[2]->id);

		$list[1]->move();

		$list = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$list = array_values($list[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_1, $list[0]->id);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_3, $list[1]->id);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_2, $list[2]->id);

		$list[1]->move(\Scrivo\SequenceNo::MOVE_UP);

		$list = \Scrivo\ListItemPropertyDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$list = array_values($list[self::LIST_ITEM_DEFINITION_SUBJECT_ID]);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_3, $list[0]->id);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_1, $list[1]->id);
		$this->assertEquals(
			self::LIST_ITEM_PROPERTY_DEFINITION_ID_2, $list[2]->id);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\ListItemPropertyDefinition object and do some
		// crud operations on it.
		$tmp = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\ListItemPropertyDefinition
		// object as editor should not succeed.
		$test = "";
		$new = new \Scrivo\ListItemPropertyDefinition($context);
		$this->setListItemPropertyProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ListItemPropertyDefinition object as editor
		// should succeed ...
		$object = \Scrivo\ListItemPropertyDefinition::fetch($context, $tmp->id);

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
			\Scrivo\ListItemPropertyDefinition::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\ListItemPropertyDefinition::select(
			$context, self::APPLICATION_DEFINITION_ID);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\ListItemPropertyDefinition object to do operations
		// on
		$tmp = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$this->setListItemPropertyProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\ListItemPropertyDefinition object
		// as member should not succeed.
		$test = "";
		$new = new \Scrivo\ListItemPropertyDefinition($context);
		$this->setListItemPropertyProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ListItemPropertyDefinition object as member
		// should succeed.
		$object = \Scrivo\ListItemPropertyDefinition::fetch($context, $tmp->id);

		// Loading all \Scrivo\ListItemPropertyDefinition objects as member
		// should succeed.
		$all = \Scrivo\ListItemPropertyDefinition::select(
			$context, self::APPLICATION_DEFINITION_ID);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$tmp->insert();

		$tmpDel = new \Scrivo\ListItemPropertyDefinition(self::$context);
		$tmpDel->insert();
		// Set "tmpDel" in the local cache.
		$tmpDel = \Scrivo\ListItemPropertyDefinition::fetch(
			self::$context, $tmpDel->id);

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\ListItemPropertyDefinition::fetch(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\ListItemPropertyDefinition(
				$this->ctxDbFailureStub());
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
			$object = new \Scrivo\ListItemPropertyDefinition(
				$this->ctxDbFailureStub());
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
			$object = new \Scrivo\ListItemPropertyDefinition(
				$this->ctxDbFailureStub());
			$object->move();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\ListItemPropertyDefinition::delete(
				$this->ctxDbFailureStub(), $tmpDel->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$properties = \Scrivo\ListItemPropertyDefinition::select(
				$this->ctxDbFailureStub(), self::APPLICATION_DEFINITION_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>