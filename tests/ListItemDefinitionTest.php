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
 * $Id: ListItemDefinitionTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\ListItemDefinition
 */
class ListItemDefinitionTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\ListItemDefinition property test data for running two tests
	 * with a single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"pageDefinitionId" => 0,
					"title" => new \Scrivo\String(""),
					"icon" => new \Scrivo\String(""),
					"phpSelector" => new \Scrivo\String(""),
					"titleWidth" => 250,
					"titleLabel" => new \Scrivo\String(""),
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"pageDefinitionId" => 0,
					"title" => new \Scrivo\String(""),
					"icon" => new \Scrivo\String(""),
					"phpSelector" => new \Scrivo\String(""),
					"titleWidth" => 350,
					"titleLabel" => new \Scrivo\String(""),
				)
			)
		);
	}

	/**
	 * \Scrivo\ListItemDefinition property test data for running a single
	 * test with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"pageDefinitionId" => 0,
					"title" => new \Scrivo\String(""),
					"icon" => new \Scrivo\String(""),
					"phpSelector" => new \Scrivo\String(""),
					"titleWidth" => 250,
					"titleLabel" => new \Scrivo\String(""),
				),
				"argument 2" => (object) array(
					"applicationDefinitionId" =>
						self::APPLICATION_DEFINITION_ID,
					"pageDefinitionId" => 0,
					"title" => new \Scrivo\String(""),
					"icon" => new \Scrivo\String(""),
					"phpSelector" => new \Scrivo\String(""),
					"titleWidth" => 350,
					"titleLabel" => new \Scrivo\String(""),
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
	 * Utility function for setting all the \Scrivo\ListItemDefinition
	 * properties from test data.
	 *
	 * @param \Scrivo\ListItemDefinition $o A reference to a
	 *   \Scrivo\ListItemDefinition object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setListItemDefinitionProperties(
			&$o, $d, $updateApp=true) {

		if ($updateApp) {
			$o->applicationDefinitionId = $d->applicationDefinitionId;
		}
		$o->pageDefinitionId = $d->pageDefinitionId;
		$o->title = $d->title;
		$o->icon = $d->icon;
		$o->phpSelector = $d->phpSelector;
		$o->titleWidth = $d->titleWidth;
		$o->titleLabel = $d->titleLabel;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\ListItemDefinition object equal those of the given data.
	 *
	 * @param \Scrivo\ListItemDefinition $o The \Scrivo\ListItemDefinition
	 *    object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkListItemDefinitionProperties($o, $d) {

		$this->assertEquals(
			$o->applicationDefinitionId, $d->applicationDefinitionId);
		$this->assertEquals($o->pageDefinitionId, $d->pageDefinitionId);
		$this->assertTrue($o->title->equals($d->title));
		$this->assertTrue($o->icon->equals($d->icon));
		$this->assertTrue($o->phpSelector->equals($d->phpSelector));
		$this->assertEquals($o->titleWidth, $d->titleWidth);
		$this->assertTrue($o->titleLabel->equals($d->titleLabel));
	}

	/**
	 * Test if a \Scrivo\ListItemDefinition object can be created/inserted
	 * into the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\ListItemDefinition object and populate its
		// fields.
		$o = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\ListItemDefinition::fetch(self::$context, $o->id);
		$this->checkListItemDefinitionProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\ListItemDefinition::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\ListItemDefinition(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\ListItemDefinition(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\ListItemDefinition
	 * object is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidListItemDefinitionCreation() {
		$o = \Scrivo\ListItemDefinition::fetch(self::$context, 12345);
	}

	/**
	 * When set the application id can't be reset.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidSetApplication() {

		$o = \Scrivo\ListItemDefinition::fetch(
			self::$context, self::LIST_ITEM_DEFINITION_SUBJECT_ID);

		$o->applicationDefinitionId = 12345;
	}

	/**
	 * Test loading of child list item definions.
	 */
	function testLoadChildDefinitions() {
		$o = \Scrivo\ListItemDefinition::fetch(
			self::$context, self::LIST_ITEM_DEFINITION_SUBJECT_ID);
		$this->assertFalse(isset($o->parentListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_SUBJECT_ID]));
		$this->assertFalse(isset($o->childListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_SUBJECT_ID]));
		$this->assertFalse(isset($o->parentListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_REPLY_ID]));
		$this->assertTrue(isset($o->childListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_REPLY_ID]));

		$o = \Scrivo\ListItemDefinition::fetch(
			self::$context, self::LIST_ITEM_DEFINITION_REPLY_ID);
		$this->assertTrue(isset($o->parentListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_SUBJECT_ID]));
		$this->assertFalse(isset($o->childListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_SUBJECT_ID]));
		$this->assertTrue(isset($o->parentListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_REPLY_ID]));
		$this->assertTrue(isset($o->childListItemDefinitionIds[
			self::LIST_ITEM_DEFINITION_REPLY_ID]));
	}

	/**
	 * Test loading of child list item definions.
	 */
	function testUpdateParentDefinitions() {
		$o = \Scrivo\ListItemDefinition::fetch(
			self::$context, self::LIST_ITEM_DEFINITION_REPLY_ID);

		foreach (
			array(
				array(
					array(),
					array(false, false, false, false)
				),
				array(
					array(self::LIST_ITEM_DEFINITION_SUBJECT_ID),
					array(true, false, false, false)
				),
				array(
					array(self::LIST_ITEM_DEFINITION_SUBJECT_ID,
						self::LIST_ITEM_DEFINITION_REPLY_ID),
					array(true, false, true, true)
				)
			) as $n) {

			$o->updateParentListItemDefinitionIds($n[0]);
			$this->assertEquals($n[1][0],
				isset($o->parentListItemDefinitionIds[
					self::LIST_ITEM_DEFINITION_SUBJECT_ID]));
			$this->assertEquals($n[1][1],
				isset($o->childListItemDefinitionIds[
					self::LIST_ITEM_DEFINITION_SUBJECT_ID]));
			$this->assertEquals($n[1][2],
				isset($o->parentListItemDefinitionIds[
					self::LIST_ITEM_DEFINITION_REPLY_ID]));
			$this->assertEquals($n[1][3],
				isset($o->childListItemDefinitionIds[
					self::LIST_ITEM_DEFINITION_REPLY_ID]));
		}
	}
	/**
	 * Test if the properties of an \Scrivo\ListItemDefinition object can be
	 * updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\ListItemDefinition object and populate its
		// fields.
		$o = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\ListItemDefinition::fetch(self::$context, $o->id);
		$this->checkListItemDefinitionProperties($o, $d1);

		// Set the new properity values.
		$this->setListItemDefinitionProperties($o, $d2, false);
		$o->update();

		// Reload the \Scrivo\ListItemDefinition object and check its property
		// values.
		$o2 = \Scrivo\ListItemDefinition::fetch(self::$context, $o->id);
		$this->checkListItemDefinitionProperties($o2, $d2);
	}

	/**
	 * Test the creation of lists of type \Scrivo\ListItemDefinition.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\ListItemDefinition objects.
		$listItemDefinitions = \Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$n = count($listItemDefinitions);

		// Create two \Scrivo\ListItemDefinition objects.
		$o1 = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ListItemDefinition objects.
		$listItemDefinitions = \Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount($n + 2, $listItemDefinitions);

		// Test if retrieved objects match against the test data.
		$this->checkListItemDefinitionProperties(
			$listItemDefinitions[$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\ListItemDefinition objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Select all \Scrivo\ListItemDefinition objects.
		$listItemDefinitions = \Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$n = count($listItemDefinitions);

		// Create two \Scrivo\ListItemDefinition objects.
		$o1 = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\ListItemDefinition objects.
		$listItemDefinitions = \Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount($n+2, $listItemDefinitions);

		// Delete the first object.
		\Scrivo\ListItemDefinition::delete(self::$context, $o1->id);

		// Select all \Scrivo\ListItemDefinition objects.
		$listItemDefinitions = \Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount($n+1, $listItemDefinitions);

		// Delete the second object.
		\Scrivo\ListItemDefinition::delete(self::$context, $o2->id);

		// Select all \Scrivo\ListItemDefinition objects.
		$listItemDefinitions = \Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID);
		$this->assertCount($n, $listItemDefinitions);
	}

	/**
	 * Test if the order of the list item definitions can be adjusted.
	 */
	function testMove() {

		$list = array_values(\Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID));
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_SUBJECT_ID, $list[0]->id);
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_REPLY_ID, $list[1]->id);
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_MOVE_ID, $list[2]->id);

		$list[1]->move();

		$list = array_values(\Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID));
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_SUBJECT_ID, $list[0]->id);
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_MOVE_ID, $list[1]->id);
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_REPLY_ID, $list[2]->id);

		$list[1]->move(\Scrivo\SequenceNo::MOVE_UP);

		$list = array_values(\Scrivo\ListItemDefinition::select(
			self::$context, self::APPLICATION_DEFINITION_ID));
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_MOVE_ID, $list[0]->id);
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_SUBJECT_ID, $list[1]->id);
		$this->assertEquals(
			self::LIST_ITEM_DEFINITION_REPLY_ID, $list[2]->id);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\ListItemDefinition object and do some crud
		// operations on it.
		$tmp = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\ListItemDefinition object as
		// editor should not succeed.
		$test = "";
		$new = new \Scrivo\ListItemDefinition($context);
		$this->setListItemDefinitionProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ListItemDefinition object as editor should
		// succeed ...
		$object = \Scrivo\ListItemDefinition::fetch($context, $tmp->id);

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
			\Scrivo\ListItemDefinition::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\ListItemDefinition::select(
			$context, self::APPLICATION_DEFINITION_ID);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\ListItemDefinition object to do operations on
		$tmp = new \Scrivo\ListItemDefinition(self::$context);
		$this->setListItemDefinitionProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\ListItemDefinition object as member
		// should not succeed.
		$test = "";
		$new = new \Scrivo\ListItemDefinition($context);
		$this->setListItemDefinitionProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\ListItemDefinition object as member should not
		// succeed.
		$object = \Scrivo\ListItemDefinition::fetch($context, $tmp->id);

		// Loading all \Scrivo\ListItemDefinition objects as member should not
		// succeed.
		$all = \Scrivo\ListItemDefinition::select(
			$context, self::APPLICATION_DEFINITION_ID);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\ListItemDefinition(self::$context);
		$tmp->insert();
		$tmpDel = new \Scrivo\ListItemDefinition(self::$context);
		$tmpDel->insert();
		// Set "tmpDel" in the local cache.
		$tmpDel = \Scrivo\ListItemDefinition::fetch(self::$context, $tmpDel->id);
		$op = \Scrivo\ListItemDefinition::fetch(
			self::$context, self::LIST_ITEM_DEFINITION_SUBJECT_ID);
		$oc = \Scrivo\ListItemDefinition::fetch(
			self::$context, self::LIST_ITEM_DEFINITION_REPLY_ID);

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\ListItemDefinition::fetch(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\ListItemDefinition($this->ctxDbFailureStub());
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
			$object = new \Scrivo\ListItemDefinition($this->ctxDbFailureStub());
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
			$object = new \Scrivo\ListItemDefinition($this->ctxDbFailureStub());
			$object->move();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\ListItemDefinition::delete(
				$this->ctxDbFailureStub(), $tmpDel->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$listItemDefinitions = \Scrivo\ListItemDefinition::select(
				$this->ctxDbFailureStub(), self::APPLICATION_DEFINITION_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Selecting child definitions
		$test = "";
		try {
			// Note: improper use of 'childListItemDefinitionIds' method, just to generate an
			// exception.
			$object = new \Scrivo\ListItemDefinition($this->ctxDbFailureStub());
			$tmpx = $object->childListItemDefinitionIds;
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Selecting parent definitions
		$test = "";
		try {
			// Note: improper use of 'parentListItemDefinitionIds' method, just to generate an
			// exception.
			$object = new \Scrivo\ListItemDefinition($this->ctxDbFailureStub());
			$tmpx = $object->parentListItemDefinitionIds;
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Updating parent definitions
		$test = "";
		try {
			// Note: improper use of 'updateParentListItemDefinitionIds' method, just to generate an
			// exception.
			$object = new \Scrivo\ListItemDefinition($this->ctxDbFailureStub());
			$object->updateParentListItemDefinitionIds(
				array(self::LIST_ITEM_DEFINITION_SUBJECT_ID));
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

}

?>