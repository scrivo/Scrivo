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
 * $Id: PageTest.php 847 2013-08-20 16:41:10Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\Page
 */
class PageTest extends ScrivoDatabaseTestCase {

	/**
	 * \Scrivo\Page property test data for running two tests with a
	 * single argument.
	 */
	function dataProviderTwoTests() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"hasStaging" => false,
					"parentId" => self::PAGE_HOME_ID,
					"type" => \Scrivo\Page::TYPE_NAVIGATION_ITEM,
					"definitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"languageId" => self::LANGUAGE_NL_ID,
					"title" => new \Scrivo\String(""),
					"description" => new \Scrivo\String(""),
					"keywords" => new \Scrivo\String(""),
					"javascript" => new \Scrivo\String(""),
					"stylesheet" => new \Scrivo\String(""),
					"dateCreated" => new \DateTime("2013-05-27 00:36:35"),
					"dateModified" => new \DateTime("2013-05-27 00:36:35"),
					"dateOnline" => new \DateTime("2013-05-27 00:36:35"),
					"dateOffline" => null,
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"hasStaging" => false,
					"parentId" => self::PAGE_HOME_ID,
					"type" => \Scrivo\Page::TYPE_NAVIGATION_ITEM,
					"definitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"languageId" => self::LANGUAGE_NL_ID,
					"title" => new \Scrivo\String(""),
					"description" => new \Scrivo\String(""),
					"keywords" => new \Scrivo\String(""),
					"javascript" => new \Scrivo\String(""),
					"stylesheet" => new \Scrivo\String(""),
					"dateCreated" => new \DateTime("2013-05-27 00:36:35"),
					"dateModified" => new \DateTime("2013-05-27 00:36:35"),
					"dateOnline" => new \DateTime("2013-05-27 00:36:35"),
					"dateOffline" => null,
				)
			)
		);
	}

	/**
	 * \Scrivo\Page property test data for running a single test with
	 * two arguments.
	 */
	function dataProviderTwoArguments() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"hasStaging" => false,
					"parentId" => self::PAGE_HOME_ID,
					"type" => \Scrivo\Page::TYPE_NAVIGATION_ITEM,
					"definitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"languageId" => self::LANGUAGE_NL_ID,
					"title" => new \Scrivo\String(""),
					"description" => new \Scrivo\String(""),
					"keywords" => new \Scrivo\String(""),
					"javascript" => new \Scrivo\String(""),
					"stylesheet" => new \Scrivo\String(""),
					"dateCreated" => new \DateTime("2013-05-27 00:36:35"),
					"dateModified" => new \DateTime("2013-05-27 00:36:35"),
					"dateOnline" => new \DateTime("2013-05-27 00:36:35"),
					"dateOffline" => null,
				),
				"argument 2" => (object) array(
					"hasStaging" => false,
					"parentId" => self::PAGE_HOME_ID,
					"type" => \Scrivo\Page::TYPE_NAVIGATION_ITEM,
					"definitionId" => self::PAGE_DEFINITION_FORUM_ID,
					"languageId" => self::LANGUAGE_NL_ID,
					"title" => new \Scrivo\String(""),
					"description" => new \Scrivo\String(""),
					"keywords" => new \Scrivo\String(""),
					"javascript" => new \Scrivo\String(""),
					"stylesheet" => new \Scrivo\String(""),
					"dateCreated" => new \DateTime("2013-05-27 00:36:35"),
					"dateModified" => new \DateTime("2013-05-27 00:36:35"),
					"dateOnline" => new \DateTime("2013-05-27 00:36:35"),
					"dateOffline" => null,
				)
			)
		);
	}

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page.yml"));
	}

	/**
	 * Utility function for setting all the \Scrivo\Page properties from
	 * test data.
	 *
	 * @param \Scrivo\Page $o A reference to a \Scrivo\Page
	 *   object for which to set its properties.
	 * @param object $d An object that contains the data for the object
	 *   properties.
	 * @param boolean $update To indicate an update (in contrast to setting all
	 *   all fields).
	 */
	private function setPageProperties(&$o, $d, $update=false) {

		$o->hasStaging = $d->hasStaging;
		$o->parentId = $d->parentId;
		$o->type = $d->type;
		if (!$update) {
			$o->definitionId = $d->definitionId;
		}
		$o->languageId = $d->languageId;
		$o->title = $d->title;
		$o->description = $d->description;
		$o->keywords = $d->keywords;
		$o->javascript = $d->javascript;
		$o->stylesheet = $d->stylesheet;
		$o->dateOnline = $d->dateOnline;
		$o->dateOffline = $d->dateOffline;
	}

	/**
	 * Set of assertions to verify if the properties of the given
	 * \Scrivo\Page object equal those of the given data.
	 *
	 * @param \Scrivo\Page $o The \Scrivo\Page object under test.
	 * @param object $d The data to check the object properties against.
	 */
	private function checkPageProperties($o, $d) {

		$this->assertEquals($o->hasStaging, $d->hasStaging);
		$this->assertEquals($o->parentId, $d->parentId);
		$this->assertEquals($o->type, $d->type);
		$this->assertEquals($o->definition->id, $d->definitionId);
		$this->assertEquals($o->language->id, $d->languageId);
		$this->assertTrue($o->title->equals($d->title));
		$this->assertTrue($o->description->equals($d->description));
		$this->assertTrue($o->keywords->equals($d->keywords));
		$this->assertTrue($o->javascript->equals($d->javascript));
		$this->assertTrue($o->stylesheet->equals($d->stylesheet));
//		$this->assertTrue($o->dateCreated == $d->dateCreated);
//		$this->assertTrue($o->dateModified == $d->dateModified);
		$this->assertTrue($o->dateOnline == $d->dateOnline);
		$this->assertTrue($o->dateOffline == $d->dateOffline);
	}

	/**
	 * Test if a \Scrivo\Page object can be created/inserted into the
	 * database.
	 *
	 * @param object $d Test data to populate the object properties.
	 *
	 * @dataProvider dataProviderTwoTests
	 */
	function testCreate($d) {

		// Create a blank \Scrivo\Page object and populate its fields.
		$o = new \Scrivo\Page(self::$context);
		$this->setPageProperties($o, $d);
		$this->assertEquals($o->id, 0);

		// Insert it into the database.
		$o->insert();
		$this->assertNotEquals($o->id, 0);

		// Reload it and check the object properties against the test data.
		$o = \Scrivo\Page::fetch(self::$context, $o->id);
		$this->checkPageProperties($o, $d);
	}

	/**
	 * Test if a new root page can be inserted in the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testCreateNewRoot() {
		$o = new \Scrivo\Page(self::$context);
		$o->insert();
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\Page(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\Page(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * When set the page definition id can't be reset.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidSetPageDefinition() {

		$o = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$o->definitionId = 12345;
	}

	/**
	 * Test exception thrown if an nonexisting \Scrivo\Page object is
	 * loaded from the database.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPageCreation() {
		$o = \Scrivo\Page::fetch(self::$context, 12345);
	}

	/**
	 * Check property loading through a page object.
	 */
	function testPageProperties() {
		// Clean the context cache
		unset(self::$context->cache[self::PAGE_HOME_ID]);
		// A fetch will load the properties directly
		$o = \Scrivo\Page::fetch(self::$context, self::PAGE_FORUM_ID);
		$this->assertNotNull($o->properties->IMAGE);
		// Access through path will do a late binding
		$this->assertNotNull($o->path[self::PAGE_HOME_ID]->properties);
	}

	/**
	 * Check property loading through a page object.
	 */
	function testPageChildren() {
		$o = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);
		// Access through path will do a late binding
		$this->assertNotNull($o->children[self::PAGE_FORUM_ID]);
	}

	/**
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testPath($d1, $d2) {

		$o1 = new \Scrivo\Page(self::$context);
		$d1->parentId = self::PAGE_FORUM_ID;
		$this->setPageProperties($o1, $d1);
		$o1->insert();

		$o2 = new \Scrivo\Page(self::$context);
		$d2->parentId = $o1->id;
		$this->setPageProperties($o2, $d2);
		$o2->insert();

		$this->assertTrue(isset($o2->path[self::PAGE_FORUM_ID]));

		// A possible exception when loading a path.
		$o3 = new \Scrivo\Page(self::$context);
		$o3->parentId = -10;
		$test = "";
		try {
			$pth = $o3->path;
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

	/**
	 * Test if the properties of an \Scrivo\Page object can be updated.
	 *
	 * @param object $d1 Data for the initial values of the object properties.
	 * @param object $d2 Data for the modified values of the object properties.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testUpdate($d1, $d2) {

		// Create a blank \Scrivo\Page object and populate its fields.
		$o = new \Scrivo\Page(self::$context);
		$this->setPageProperties($o, $d1);
		$o->insert();

		// Reload the object.
		$o = \Scrivo\Page::fetch(self::$context, $o->id);
		$this->checkPageProperties($o, $d1);

		// Set the new properity values.
		$this->setPageProperties($o, $d2, true);
		$o->update();

		// Reload the \Scrivo\Page object and check its property values.
		$o2 = \Scrivo\Page::fetch(self::$context, $o->id);
		$this->checkPageProperties($o2, $d2);
	}

	/**
	 * Can't move a page underneath itself.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 */
	function testInvalidUpdateParentId() {
		$o = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);
		$o->parentId = self::PAGE_FORUM_ID;
		$o->update();
	}

	/**
	 * Test deletion of \Scrivo\Page objects.
	 *
	 * @param object $d1 Data for the first test object to insert and delete.
	 * @param object $d2 Data for the second test object to insert and delete.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testDelete($d1, $d2) {

		// Create two \Scrivo\Page objects.
		$o1 = new \Scrivo\Page(self::$context);
		$this->setPageProperties($o1, $d1);
		$o1->parentId = self::PAGE_FORUM_ID;
		$o1->insert();

		$o2 = new \Scrivo\Page(self::$context);
		$this->setPageProperties($o2, $d2);
		$o2->parentId = self::PAGE_FORUM_ID;
		$o2->insert();
		$fldr = new \Scrivo\Page(self::$context);
		$fldr->type = \Scrivo\Page::TYPE_SUB_FOLDER;
		$fldr->parentId = $o2->id;
		$fldr->insert();

		// Deleting a page with children should not be possible
		$test = "";
		try {
			\Scrivo\Page::delete(self::$context, self::PAGE_HOME_ID);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Deleting a labeled page should not be possible too
		$test = "";
		try {
			\Scrivo\Page::delete(self::$context, self::PAGE_FORUM_ID);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		$this->assertCount(
			2, \Scrivo\Page::fetch(self::$context, self::PAGE_FORUM_ID)->children);

		\Scrivo\Page::delete(self::$context, $o1->id);

		$this->assertCount(
			1, \Scrivo\Page::fetch(self::$context, self::PAGE_FORUM_ID)->children);

		\Scrivo\Page::delete(self::$context, $o2->id);

		$this->assertCount(
			0, \Scrivo\Page::fetch(self::$context, self::PAGE_FORUM_ID)->children);

	}

	/**
	 * Get the ids of a the children of a page.
	 * @param int a page $id
	 * @return array the child ids
	 */
	private function getChildIds($id) {

		$p = \Scrivo\Page::fetch(self::$context, $id);
		$ids = array();
		foreach ($p->children as $id=>$x) {
			$ids[] = $id;
		}
		return $ids;
	}

	/**
	 * Test it the order pages can be adjusted.
	 */
	function testMove() {

		$p2 = new \Scrivo\Page(self::$context);
		$p2->parentId = self::PAGE_HOME_ID;
		$p2->type = \Scrivo\Page::TYPE_NAVIGABLE_PAGE;

		$p3 = new \Scrivo\Page(self::$context);
		$p3->parentId = self::PAGE_HOME_ID;
		$p3->type = \Scrivo\Page::TYPE_NON_NAVIGABLE_PAGE;

		$f1 = new \Scrivo\Page(self::$context);
		$f1->parentId = self::PAGE_HOME_ID;
		$f1->type = \Scrivo\Page::TYPE_SUB_FOLDER;

		$p2->insert();
		$p3->insert();
		$f1->insert();

		$home = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[1]);
		$this->assertEquals($p2->id, $ids[2]);
		$this->assertEquals($p3->id, $ids[3]);

		$home->children[self::PAGE_FORUM_ID]->move(\Scrivo\SequenceNo::MOVE_UP);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[1]);
		$this->assertEquals($p2->id, $ids[2]);
		$this->assertEquals($p3->id, $ids[3]);

		$home->children[self::PAGE_FORUM_ID]->move();

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals($p2->id, $ids[1]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[2]);
		$this->assertEquals($p3->id, $ids[3]);

		$home->children[self::PAGE_FORUM_ID]->move(
			\Scrivo\SequenceNo::MOVE_FIRST);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[1]);
		$this->assertEquals($p2->id, $ids[2]);
		$this->assertEquals($p3->id, $ids[3]);

		$home->children[self::PAGE_FORUM_ID]->move(2);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals($p2->id, $ids[1]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[2]);
		$this->assertEquals($p3->id, $ids[3]);

		$home->children[self::PAGE_FORUM_ID]->move(
			\Scrivo\SequenceNo::MOVE_LAST);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals($p2->id, $ids[1]);
		$this->assertEquals($p3->id, $ids[2]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[3]);

		$home->children[self::PAGE_FORUM_ID]->move(2);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);

		$this->assertEquals($f1->id, $ids[0]);
		$this->assertEquals($p2->id, $ids[1]);
		$this->assertEquals(self::PAGE_FORUM_ID, $ids[2]);
		$this->assertEquals($p3->id, $ids[3]);

		$f1->type = \Scrivo\Page::TYPE_NAVIGABLE_PAGE;
		$f1->update();
		$f1->move(\Scrivo\SequenceNo::MOVE_LAST);

		$ids = $this->getChildIds(self::PAGE_HOME_ID);
		$this->assertEquals($f1->id, $ids[3]);

		$f1->type = \Scrivo\Page::TYPE_SUB_FOLDER;
		$f1->update();

		$ids = $this->getChildIds(self::PAGE_HOME_ID);
		$this->assertEquals($f1->id, $ids[0]);

		// Moving a subfolder should not be possible.
		$test = "";
		try {
			$f1->move();
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

	/**
	 * Test touching of a page
	 */
	function testTouch() {

		$home = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		\Scrivo\Page::touch(self::$context, self::PAGE_HOME_ID);

		$home1 = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$this->assertGreaterThan($home->dateModified, $home1->dateModified);

	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsEditor($d1, $d2) {

		// Create a \Scrivo\Page object and do some crud operations
		// on it.
		$tmp = new \Scrivo\Page(self::$context);
		$this->setPageProperties($tmp, $d1);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, self::EDITOR_USER_ID);

		// Creating/inserting a new \Scrivo\Page object as editor
		// should succeed (parent page).
		$test = "";
		$new = new \Scrivo\Page($context);
		$this->setPageProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("", $test);

		// Loading a \Scrivo\Page object as editor should succeed ...
		$object = \Scrivo\Page::fetch($context, $tmp->id);

		// ... but updating should succeed, page inherits roles from parent.
		$test = "";
		try {
			$object->update();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("", $test);

		// And deleting should succeed, page inherits roles from parent.
		$test = "";
		try {
			\Scrivo\Page::delete($context, $object->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("", $test);

	}

	/**
	 * Perform database operations as a member.
	 *
	 * @dataProvider dataProviderTwoArguments
	 */
	function testAccessAsMember($d1, $d2) {

		// Create a \Scrivo\Page object to do operations on
		$tmp = new \Scrivo\Page(self::$context);
		$this->setPageProperties($tmp, $d2);
		$tmp->insert();

		$cfg = new \Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		// Creating/inserting a new \Scrivo\Page object as member
		// should not succeed.
		$test = "";
		$new = new \Scrivo\Page($context);
		$this->setPageProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a \Scrivo\Page object as member should succeed, the parent
		// roles were inherited when it was created.
		$test = "";
		try {
			$object = \Scrivo\Page::fetch($context, $tmp->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("", $test);

	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		$tmp = new \Scrivo\Page(self::$context);
		$tmp->parentId = self::PAGE_HOME_ID;
		$tmp->insert();

		// Perform load operation.
		$test = "";
		try {
			$object = \Scrivo\Page::fetch($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$object = new \Scrivo\Page($this->ctxDbFailureStub());
			$object->insert();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			// Note: improper use of 'children' property, just to generate an
			// exception.
			$object = new \Scrivo\Page($this->ctxDbFailureStub());
			$c = $object->children;
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			// Note: improper use of 'path' property, just to generate an
			// exception.
			$object = new \Scrivo\Page($this->ctxDbFailureStub());
			$object->parentId = self::PAGE_HOME_ID;
			$pth = $object->path;
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform update operation.
		$test = "";
		try {
			// Note: improper use of 'update' method, just to generate an
			// exception.
			$object = new \Scrivo\Page($this->ctxDbFailureStub());
			$object->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform touch operation.
		$test = "";
		try {
			\Scrivo\Page::touch($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\Page::delete($this->ctxDbFailureStub(), $tmp->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

}

?>
