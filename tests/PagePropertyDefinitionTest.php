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
 * $Id: PagePropertyDefinitionTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PagePropertyDefinition
 */
class PagePropertyDefinitionTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\PagePropertyDefinition property test data for running two tests
	 * with a single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("Image"),
					"phpSelector" => new \Scrivo\String("AN_IMAGE"),
					"type" => \Scrivo\PagePropertyDefinition::TYPE_IMAGE,
					"typeData" => (object)array(
						"FLOAT" => 6.5,
						"INT" => 6
					),
					"pageDefinitionTabId" => 0,
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("Select list"),
					"phpSelector" => new \Scrivo\String("A_SELECT_LIST"),
					"type" => \Scrivo\PagePropertyDefinition::TYPE_SELECT,
					"typeData" => (object)array(
						"location" => "0:yes;1:no"
					),
					"pageDefinitionTabId" =>
						self::PAGE_DEFINITION_PROPERTY_TAB_ID,
				)
			)
		);
	}

	/**
	 * \Scrivo\PagePropertyDefinition property test data for running a single
	 * test with two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("Image"),
					"phpSelector" => new \Scrivo\String("AN_IMAGE"),
					"type" => \Scrivo\PagePropertyDefinition::TYPE_IMAGE,
					"typeData" => (object)array(),
					"pageDefinitionTabId" => 0,
				),
				"argument 2" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("Color"),
					"phpSelector" => new \Scrivo\String("A_COLOR"),
					"type" => \Scrivo\PagePropertyDefinition::TYPE_COLOR,
					"typeData" => (object)array(),
					"pageDefinitionTabId" =>
						self::PAGE_DEFINITION_PROPERTY_TAB_ID,
				)
			)
		);
	}

	/**
	 * \Scrivo\PagePropertyDefinition property test data for running a single
	 * test with three arguments.
	 */
	function dataProviderThreeArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("Image"),
					"phpSelector" => new \Scrivo\String("AN_IMAGE"),
					"type" => \Scrivo\PagePropertyDefinition::TYPE_IMAGE,
					"typeData" => (object)array(),
					"pageDefinitionTabId" => 0,
				),
				"argument 2" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("HTML tab"),
					"phpSelector" => new \Scrivo\String("AN_HTML_TEXT_TAB"),
					"type" =>
						\Scrivo\PagePropertyDefinition::TYPE_HTML_TEXT_TAB,
					"typeData" => (object)array(
						"css_selector" => new \Scrivo\String(""),
						"INITIAL_CONTENT" => new \Scrivo\String(""),
						"page_css" => new \Scrivo\String(""),
						"stylesheet" => new \Scrivo\String(""),
						"css_id" => new \Scrivo\String(""),
					),
				),
				"argument 3" => (object) array(
					"pageDefinitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"title" => new \Scrivo\String("Application tab"),
					"phpSelector" =>
						new \Scrivo\String("AN_APPLICATION_TAB"),
					"type" =>
						\Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB,
					"typeData" => (object)array("APPLICATION_DEFINITION_ID" =>
							self::APPLICATION_DEFINITION_ID
					),
				),
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
	 * Utility function for setting all the \Scrivo\PagePropertyDefinition
	 * properties from test data.
	 *
	 * @param \Scrivo\PagePropertyDefinition $o A reference to a
	 *   \Scrivo\PagePropertyDefinition object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 */
	private function setTemplatePropertyProperties(&$o, $d, $setTabId=true) {

		$o->pageDefinitionId = $d->pageDefinitionId;
		$o->title = $d->title;
		$o->phpSelector = $d->phpSelector;
		$o->type = $d->type;
		$o->typeData = $d->typeData;
		if ($setTabId) {
			$o->pageDefinitionTabId = $d->pageDefinitionTabId;
		}
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\PagePropertyDefinition object equal those of the given data.
	 *
	 * @param \Scrivo\PagePropertyDefinition $o The
	 *    \Scrivo\PagePropertyDefinition object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkTemplatePropertyProperties(
			$o, $d, $checkTabId=true) {

		$this->assertEquals($o->pageDefinitionId, $d->pageDefinitionId);
		$this->assertTrue($o->title->equals($d->title));
		$this->assertTrue($o->phpSelector->equals($d->phpSelector));
		$this->assertEquals($o->type, $d->type);
		$this->assertEquals($o->typeData, $d->typeData);
		if ($checkTabId) {
			$this->assertEquals(
				$o->pageDefinitionTabId, $d->pageDefinitionTabId);
		}
	}

	/**
	 * Test the list of all possible property types.
	 */
	function testTypes() {
		$list = \Scrivo\PagePropertyDefinition::getTypes();
		// Check the list count.
		$this->assertCount(12, $list);
		// See if type image is in the list.
		$test = false;
		foreach ($list as $type) {
			if ($type === \Scrivo\PagePropertyDefinition::TYPE_IMAGE) {
				$test = true;
			}
		}
		if (!$test) {
			$this->fail("Type image not found in the list");
		}
	}

	/**
	 * Test if a \Scrivo\PagePropertyDefinition object can be created/inserted
	 * into the database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\PagePropertyDefinition object and populate
		// its fields.
		$o = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o->id);
		$this->checkTemplatePropertyProperties($o, $d);

		// Reload it from local cache.
		$oc = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o->id);
		$this->assertTrue($o === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\PagePropertyDefinition(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\PagePropertyDefinition(self::$context);
		$o->sabicasElRey = "el mejor";
	}


	/**
	 * Test exception thrown if an nonexisting \Scrivo\PagePropertyDefinition
	 * object is loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidTemplatePropertyCreation() {
		$o = \Scrivo\PagePropertyDefinition::fetch(self::$context, 12345);
	}

	/**
	 * Test the creation of different types: HTML tab and Apllication tab
	 * properties behave differently than normal properties.
	 * updated.
	 *
	 * @param object $d1 Data for a normal template property.
	 * @param object $d2 Data for an HTML tab template property.
	 * @param object $d3 Data for an application tab template property.
	 *
	 * @dataProvider dataProviderThreeArguments
	 */
	function testCreate2($d1, $d2, $d3) {

		// Create a blank \Scrivo\PagePropertyDefinition object and populate
		// its fields.
		$o1 = new \Scrivo\PagePropertyDefinition(self::$context);
		$o2 = new \Scrivo\PagePropertyDefinition(self::$context);
		$o3 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o1, $d1);
		$this->setTemplatePropertyProperties($o2, $d2, false);
		$this->setTemplatePropertyProperties($o3, $d3, false);
		$this->assertEquals($o1->id, 0);
		$this->assertEquals($o2->id, 0);
		$this->assertEquals($o3->id, 0);

		// Insert it into the database.
		$o1->insert();
		$o2->insert();
		$o3->insert();
		$this->assertNotEquals($o1->id, 0);
		$this->assertNotEquals($o2->id, 0);
		$this->assertNotEquals($o3->id, 0);

		// Reload it and check the object properties against the test data.
		$o1 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o1->id);
		$o2 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o2->id);
		$o3 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o3->id);
		$this->checkTemplatePropertyProperties($o1, $d1);
		$this->checkTemplatePropertyProperties($o2, $d2, false);
		$this->checkTemplatePropertyProperties($o3, $d3, false);
	}

	/**
	 * Test exception thrown when trying to set the type member to an invalid
	 * value.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidType() {
		$o = new \Scrivo\PagePropertyDefinition(self::$context);
		$o->type = new \Scrivo\String("Handy dandy type");
	}

	/**
	 * You can´t switch between normal, HTML tab or application tab types.
	 *
	 * @param object $d1 Data for a normal template property.
	 * @param object $d2 Data for an HTML tab template property.
	 * @param object $d3 Data for an application tab template property.
	 *
	 * @dataProvider dataProviderThreeArguments
	 */
	function testIllegalTypeJuggling($d1, $d2, $d3) {

		$o1 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o1, $d1);

		$o2 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o2, $d2, false);

		$o3 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o3, $d3, false);

		// normal -> HTML tab
		$test = "";
		try {
			$this->setTemplatePropertyProperties($o1, $d2, false);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// normal -> application tab
		$test = "";
		try {
			$this->setTemplatePropertyProperties($o1, $d3, false);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// HTML tab -> normal
		$test = "";
		try {
			$this->setTemplatePropertyProperties($o2, $d1, false);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// HTML tab -> application tab
		$test = "";
		try {
			$this->setTemplatePropertyProperties($o2, $d3, false);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// application tab -> normal
		$test = "";
		try {
			$this->setTemplatePropertyProperties($o3, $d1, false);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// application tab -> HTML tab
		$test = "";
		try {
			$this->setTemplatePropertyProperties($o3, $d2, false);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

	/**
	 * Test if the properties of an \Scrivo\PagePropertyDefinition object can
	 * be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\PagePropertyDefinition object and populate
		// its fields.
		$o = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o->id);
		$this->checkTemplatePropertyProperties($o, $d1);

		// Set the new properity values.
		$this->setTemplatePropertyProperties($o, $d2);
		$o->update();

		// Reload the \Scrivo\PagePropertyDefinition object and check its
		// property values.
		$o2 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o->id);
		$this->checkTemplatePropertyProperties($o2, $d2);
	}

	/**
	 * Test if the properties of an \Scrivo\PagePropertyDefinition object can
	 * be updated.
	 *
	 * @param object $d1 Data for a normal template property.
	 * @param object $d2 Data for an HTML tab template property.
	 * @param object $d3 Data for an application tab template property.
	 *
	 * @dataProvider dataProviderThreeArguments
	 */
	function testUpdate2($d1, $d2, $d3) {

		// Create a blank \Scrivo\PagePropertyDefinition object and populate
		// its fields.
		$o2 = new \Scrivo\PagePropertyDefinition(self::$context);
		$o3 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o2, $d2, false);
		$this->setTemplatePropertyProperties($o3, $d3, false);
		$o2->insert();
		$o3->insert();

		// Reload the objects
		$o2 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o2->id);
		$this->checkTemplatePropertyProperties($o2, $d2, false);
		$o3 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o3->id);
		$this->checkTemplatePropertyProperties($o3, $d3, false);

		// Set some new properity values.
		$o2->phpSelector = new \Scrivo\String("ABCDEFG");
		$o3->phpSelector = new \Scrivo\String("HILJKMN");
		$o2->update();
		$o3->update();

		// Reload the \Scrivo\PagePropertyDefinition object and check its
		// property values.
		$o2 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o2->id);
		$o3 = \Scrivo\PagePropertyDefinition::fetch(self::$context, $o3->id);
		$this->assertTrue($o2->phpSelector->equals(
			new \Scrivo\String("ABCDEFG")));
		$this->assertTrue($o3->phpSelector->equals(
			new \Scrivo\String("HILJKMN")));

		// Setting the tab id should fail for HTML tab properties
		$test = "";
		try {
			$o2->pageDefinitionTabId = self::PAGE_DEFINITION_PROPERTY_TAB_ID;
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Setting the tab id should fail for application tab properties
		$test = "";
		try {
			$o3->pageDefinitionTabId = self::PAGE_DEFINITION_PROPERTY_TAB_ID;
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

	/**
	 * Test that it is not possible to create duplicate selectors.
	 *
	 * @param object $d1 Data for the first template property.
	 * @param object $d2 Data for the second template property.
	 *
	 * @dataProvider dataProviderTwoArguments
	 * @expectedException \Scrivo\ApplicationException
	 */
	function testDuplicateSelector($d1, $d2) {

		// Create a blank \Scrivo\PagePropertyDefinition object and populate
		// its fields.
		$o = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o, $d1);
		$o->insert();

		// Create a blank \Scrivo\PagePropertyDefinition object and populate
		// its fields.
		$o2 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o2, $d2);
		$o2->insert();

		$o2->phpSelector = $o->phpSelector;
		$o2->update();
	}

	/**
	 * Test the creation of lists of type \Scrivo\PagePropertyDefinition.
	 *
	 * @param object $d1 Data for the first test object in the list.
	 * @param object $d2 Data for the second test object in the list.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testSelect($d1, $d2) {

		// Select all \Scrivo\PagePropertyDefinition objects.
		$template_properties = \Scrivo\PagePropertyDefinition::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);

		// Create two \Scrivo\PagePropertyDefinition objects.
		$o1 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o2, $d2);
		$o2->insert();

		// Select all \Scrivo\PagePropertyDefinition objects.
		$template_properties = \Scrivo\PagePropertyDefinition::select(
				self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(8, $template_properties);

		// Test if retrieved objects match against the test data.
		$this->checkTemplatePropertyProperties(
			$template_properties[$o1->id], $d1);
	}

	/**
	 * Test deletion of \Scrivo\PagePropertyDefinition objects.
	 *
	 * @param object $d1 Data for a normal template property.
	 * @param object $d2 Data for an HTML tab template property.
	 * @param object $d3 Data for an application tab template property.
	 *
	 * @dataProvider dataProviderThreeArguments
	 */
	function testDelete($d1, $d2, $d3) {

		// Create two \Scrivo\PagePropertyDefinition objects.
		$o1 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o1, $d1);
		$o1->insert();
		$o2 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o2, $d2, false);
		$o2->insert();
		$o3 = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($o3, $d3, false);
		$o3->insert();

		// Select all \Scrivo\PagePropertyDefinition objects.
		$template_properties = \Scrivo\PagePropertyDefinition::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(9, $template_properties);

		// Delete the first object.
		\Scrivo\PagePropertyDefinition::delete(self::$context, $o1->id);

		// Select all \Scrivo\PagePropertyDefinition objects.
		$template_properties = \Scrivo\PagePropertyDefinition::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(8, $template_properties);

		// Delete the second object.
		\Scrivo\PagePropertyDefinition::delete(self::$context, $o2->id);

		// Select all \Scrivo\PagePropertyDefinition objects.
		$template_properties = \Scrivo\PagePropertyDefinition::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(7, $template_properties);

		// Delete the third object.
		\Scrivo\PagePropertyDefinition::delete(self::$context, $o3->id);

		// Select all \Scrivo\PagePropertyDefinition objects.
		$template_properties = \Scrivo\PagePropertyDefinition::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$this->assertCount(6, $template_properties);
	}

	/**
	 * List the properties on a single tab.
	 * @param int $tabId
	 */
	function listTabProperties($tabId) {
		$template_properties = \Scrivo\PagePropertyDefinition::select(
			self::$context, self::PAGE_DEFINITION_FORUM_ID);
		$list = array();
		foreach ($template_properties as $item) {
			if ($item->pageDefinitionTabId == $tabId) {
				$list[] = $item;
			}
		}
		return $list;
	}

	/**
	 * Test it the order of the properties can be adjusted.
	 */
	function testMove() {

		$list = $this->listTabProperties(
			self::PAGE_DEFINITION_PROPERTY_TAB_ID);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_HTML_ID, $list[0]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_CHECKBOX_ID, $list[1]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_LINK_ID, $list[2]->id);

		$list[1]->move();

		$list = $this->listTabProperties(
			self::PAGE_DEFINITION_PROPERTY_TAB_ID);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_HTML_ID, $list[0]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_LINK_ID, $list[1]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_CHECKBOX_ID, $list[2]->id);

		$list[1]->move(\Scrivo\SequenceNo::MOVE_UP);

		$list = $this->listTabProperties(
			self::PAGE_DEFINITION_PROPERTY_TAB_ID);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_LINK_ID, $list[0]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_HTML_ID, $list[1]->id);
		$this->assertEquals(
			self::PAGE_DEFINITION_PROPERTY_CHECKBOX_ID, $list[2]->id);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\PagePropertyDefinition object and do some crud
		// operations on it.
		$tmp = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\PagePropertyDefinition object as
		// editor should not succeed.
		$test = "";
		$new = new \Scrivo\PagePropertyDefinition($context);
		$this->setTemplatePropertyProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\PagePropertyDefinition object as editor should
		// succeed ...
		$object = \Scrivo\PagePropertyDefinition::fetch($context, $tmp->id);

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
			\Scrivo\PagePropertyDefinition::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading lists should succeed.
		$all = \Scrivo\PagePropertyDefinition::select(
			$context, self::PAGE_DEFINITION_FORUM_ID);
	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\PagePropertyDefinition object to do operations on
		$tmp = new \Scrivo\PagePropertyDefinition(self::$context);
		$this->setTemplatePropertyProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\PagePropertyDefinition object as
		// member should not succeed.
		$test = "";
		$new = new \Scrivo\PagePropertyDefinition($context);
		$this->setTemplatePropertyProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		$object = \Scrivo\PagePropertyDefinition::fetch($context, $tmp->id);

		// Loading all \Scrivo\PagePropertyDefinition objects as member should
		// not succeed.
		$all = \Scrivo\PagePropertyDefinition::select(
			$context, self::PAGE_DEFINITION_FORUM_ID);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\PagePropertyDefinition(self::$context);
		$tmp->type = \Scrivo\PagePropertyDefinition::TYPE_URL;
		$tmp->insert();

		$tmpDel = new \Scrivo\PagePropertyDefinition(self::$context);
		$tmpDel->phpSelector = new \Scrivo\String("TMP_DEL");
		$tmpDel->insert();
		// Set "tmpDel" in the local cache.
		$tmpDel = \Scrivo\PagePropertyDefinition::fetch(
			self::$context, $tmpDel->id);

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\PagePropertyDefinition::fetch(
				$this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\PagePropertyDefinition(
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
			$object = new \Scrivo\PagePropertyDefinition(
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
			$object = new \Scrivo\PagePropertyDefinition(
				$this->ctxDbFailureStub());
			$object->move();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\PagePropertyDefinition::delete(
				$this->ctxDbFailureStub(), $tmpDel->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$template_properties = \Scrivo\PagePropertyDefinition::select(
				$this->ctxDbFailureStub(), self::PAGE_DEFINITION_FORUM_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>