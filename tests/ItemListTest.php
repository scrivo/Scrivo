<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: ItemListTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\ListItemDefinition
 */
class ItemListTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"application_definition.yml", "page_definition.yml", "page.yml",
			"list_item_definition.yml", "item_list.yml"));
	}

	/**
	 * Check what happends if we're using corrupted data.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidList() {
		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_INVALID_LIST_TAB_ID);
	}

	/**
	 * Check what happends if we're retrieving an invalid property.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidListProperty() {
		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);
		$tmp = $list->toedeledokie;
	}

	/**
	 * Test implicit creation of a new list when a page containing a list
	 * was created.
	 */
	function testNewList() {

		// Creating a new forum page will create a new list.
		$p = new \Scrivo\Page(self::$context);
		$p->definitionId = self::PAGE_DEFINITION_FORUM_ID;
		$p->parentId = self::PAGE_HOME_ID;
		$p->insert();

		$lst = \Scrivo\ItemList::fetch(self::$context, $p->id,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);
/*
		$this->assertEquals($p->id, $lst->pageId);
		$this->assertEquals(self::PAGE_DEFINITION_APLLICATION_TAB_ID,
			$lst->pagePropertyDefinitionId);
		$this->assertNotNull($lst->id);
		$this->assertEquals($p->RoleSet, $lst->RoleSet);
*/
		$this->assertEquals(array(), $lst->items);
	}

	/**
	 * Test list item operations new update and delete
	 */
	function testListOperation() {

		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);

		$subject = $list->newItem(new \Scrivo\String("SUBJECT"));

		$subject->title = new \Scrivo\String("A subject");
		$subject->properties->SUBJECT_HTML_TEXT->html =
			new \Scrivo\String("<p>A subject</p>");

		$list->saveItem($subject);

		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);

		$tmp = $list->items;

		$lastItem = array_pop($tmp);

		$this->assertTrue(
			$lastItem->properties->SUBJECT_HTML_TEXT->html->equals(
				$subject->properties->SUBJECT_HTML_TEXT->html));

		$lastItem->properties->SUBJECT_HTML_TEXT->html =
			new \Scrivo\String("<p>A subject 2</p>");

		$list->saveItem($lastItem);

		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);

		$tmp2 = $list->items;
		$count = count($list->items);
		$lastItem2 = array_pop($tmp2);

		$this->assertTrue(
				$lastItem2->properties->SUBJECT_HTML_TEXT->html->equals(
						$lastItem->properties->SUBJECT_HTML_TEXT->html));

		$list->deleteItem($lastItem->id);

		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);

		$this->assertEquals($count-1, count($list->items));
		$this->assertFalse(isset($list->items[$lastItem->id]));

	}

	/**
	 * @expectedException \Scrivo\ApplicationException
	 */
	function testFetchAsAnonymousUser() {

		$cfg = new Scrivo\Config(new \Scrivo\String("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::ANONYMOUS_USER_ID);

		$list = \Scrivo\ItemList::fetch($context, self::PAGE_FORUM_ID,
			self::PAGE_DEFINITION_APLLICATION_TAB_ID);
	}

	/**
	 */
	function testFetchAsMemberEditorAndAdmin() {

		$cfg = new Scrivo\Config(new \Scrivo\String("test_config"));

		foreach(array(self::MEMBER_USER_ID, self::EDITOR_USER_ID,
				self::PUBLISHER_USER_ID, self::ADMIN_USER_ID) as $u) {

			$list = \Scrivo\ItemList::fetch(new \Scrivo\Context($cfg, $u),
				self::PAGE_FORUM_ID, self::PAGE_DEFINITION_APLLICATION_TAB_ID);

		}

	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		// Hack:
		$class = new ReflectionClass("\Scrivo\ItemList");
		$property = $class->getProperty("context");
		$property->setAccessible(true);

		$list = \Scrivo\ItemList::fetch(self::$context, self::PAGE_FORUM_ID,
				self::PAGE_DEFINITION_APLLICATION_TAB_ID);
		$tmp = $list->items;
		$anItem = array_pop($tmp);

		$property->setValue($list, $this->ctxDbFailureStub());


		// Perform load operation.
		$test = "";
		try {
			$l = \Scrivo\ItemList::fetch($this->ctxDbFailureStub(),
				self::PAGE_FORUM_ID, self::PAGE_DEFINITION_INVALID_LIST_TAB_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform load operation.
		$test = "";
		try {
			$tmp = $list->getItems(3);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Try to create a new item.
		$test = "";
		try {
			$blah = $list->newItem(new \Scrivo\String("SUBJECT"));

		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert item operation.
		$test = "";
		try {
			$anItem->title = new \Scrivo\String("A subject");
			$list->saveItem($anItem);

		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete item operation.
		$test = "";
		try {
			$list->deleteItem(707);

		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

}

?>