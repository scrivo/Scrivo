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
 * $Id: PageDefinitionHintsTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageDefinitionHints
 *
 * TODO: If you add a template the hints for the new template are correctly
 * set to zero, but for other templates it is set to NULL (infinity) that is
 * a bug, create a test for that.
 */
class PageDefinitionHintsTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page.yml", "page_definition.yml", "page_definition_hints.yml"));
	}

	/**
	 * The hints as defined in the test data and as returned by
	 * \Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT
	 */
	function dataProviderCanBeParentOf() {
		return array(
			"test 1" => array(
				"argument 1" => array(
					self::PAGE_DEFINITION_HOME_ID => array(),
					self::PAGE_DEFINITION_MENU_ID =>
						array(self::PAGE_DEFINITION_HOME_ID => 3),
					self::PAGE_DEFINITION_CONTENT_ID =>
						array(self::PAGE_DEFINITION_MENU_ID => null),
					self::PAGE_DEFINITION_FORUM_ID =>
						array(self::PAGE_DEFINITION_MENU_ID => 1),
				)
			),
		);
	}

	/**
	 * The hints as defined in the test data and as returned by
	 * \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_COUNT
	 */
	function dataProviderCanBeChildOf() {
		return array(
			"test 1" => array(
				"argument 1" => array(
					self::PAGE_DEFINITION_HOME_ID =>
						array(self::PAGE_DEFINITION_MENU_ID => 3),
					self::PAGE_DEFINITION_MENU_ID =>
						array(
							self::PAGE_DEFINITION_CONTENT_ID => null,
							self::PAGE_DEFINITION_FORUM_ID => 1
						),
					self::PAGE_DEFINITION_CONTENT_ID => array(),
					self::PAGE_DEFINITION_FORUM_ID => array(),
				)
			),
		);
	}

	/**
	 * New hints data to use in the testUpdate test method.
	 */
	function dataProviderUpdate() {
		return array(
			"test 1" => array(
				"argument 1" => array(
					self::PAGE_DEFINITION_HOME_ID => array(),
					self::PAGE_DEFINITION_MENU_ID => array(
						self::PAGE_DEFINITION_HOME_ID => 2
					),
					self::PAGE_DEFINITION_CONTENT_ID => array(
						self::PAGE_DEFINITION_CONTENT_ID => null,
						self::PAGE_DEFINITION_MENU_ID => null
					),
					self::PAGE_DEFINITION_FORUM_ID => array(
						self::PAGE_DEFINITION_MENU_ID => 1
					),
				)
			),
		);
	}

	/**
	 * Load the set of hints for each template and check the results with
	 * the given data. Create the hints using the given template as template
	 * for a child page.
	 *
	 * @dataProvider dataProviderCanBeParentOf
	 */
	function testCreate($d) {

		$templates = array(
			self::PAGE_DEFINITION_HOME_ID,
			self::PAGE_DEFINITION_MENU_ID,
			self::PAGE_DEFINITION_CONTENT_ID,
			self::PAGE_DEFINITION_FORUM_ID
		);

		foreach ($templates as $id) {
			// Select the hints with $id as the child template id.
			$hints = new \Scrivo\PageDefinitionHints(self::$context, $id,
				\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
			foreach ($hints as $hint) {
				// Check for all templates if they may be used as template for
				// a parent page and if so, how many times pages using the
				// child template can occur under that parent page.
				$count = array_key_exists($hint->pageDefinitionId, $d[$id])
					? $d[$id][$hint->pageDefinitionId] : 0;
				$this->assertEquals($count, $hint->maxNoOfChildren);
			}
		}

	}

	/**
	 * Load the set of hints for each template and check the results with
	 * the given data. Create the hints using the given template as template
	 * for a parent page.
	 *
	 * @dataProvider dataProviderCanBeChildOf
	 */
	function testCreate2($d) {

		$templates = array(
			self::PAGE_DEFINITION_HOME_ID,
			self::PAGE_DEFINITION_MENU_ID,
			self::PAGE_DEFINITION_CONTENT_ID,
			self::PAGE_DEFINITION_FORUM_ID
		);

		foreach ($templates as $id) {
			// Select the hints with $id as the parent template id.
			$hints = new \Scrivo\PageDefinitionHints(self::$context, $id,
				\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_COUNT);
			foreach ($hints as $hint) {
				// Check for all templates how many times the may be used
				// for child-pages of a page using the given template.
				$count = array_key_exists($hint->pageDefinitionId, $d[$id])
					? $d[$id][$hint->pageDefinitionId] : 0;
				$this->assertEquals($count, $hint->maxNoOfChildren);
			}
		}

	}

	/**
	 * Load the hints sets for 'live' situations: that is the hints set as
	 * defined and corrected for the pages that are actually already created.
	 */
	function testLoadCorrected() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_HOME_ID,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);
		$this->assertEquals(
			1, $hints[self::PAGE_DEFINITION_MENU_ID]->maxNoOfChildren);
		$this->assertEquals(
			0, $hints[self::PAGE_DEFINITION_FORUM_ID]->maxNoOfChildren);

		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_MENU1_ID,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);
		$this->assertNull(
			$hints[self::PAGE_DEFINITION_CONTENT_ID]->maxNoOfChildren);
		$this->assertEquals(
			0, $hints[self::PAGE_DEFINITION_FORUM_ID]->maxNoOfChildren);

		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_MENU2_ID,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);
		$this->assertNull(
			$hints[self::PAGE_DEFINITION_CONTENT_ID]->maxNoOfChildren);
		$this->assertEquals(
			1, $hints[self::PAGE_DEFINITION_FORUM_ID]->maxNoOfChildren);
	}

	/**
	 * Update a hints set and check of the modifications are permanent.
	 *
	 * @dataProvider dataProviderUpdate
	 */
	function testUpdate($d) {

		$templates = array(
			self::PAGE_DEFINITION_HOME_ID,
			self::PAGE_DEFINITION_MENU_ID,
			self::PAGE_DEFINITION_CONTENT_ID,
			self::PAGE_DEFINITION_FORUM_ID
		);

		foreach ($templates as $id) {
			$hints = new \Scrivo\PageDefinitionHints(
				self::$context, $id,
				\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
			foreach ($hints as $key=>$hint) {
				$count = array_key_exists($hint->pageDefinitionId, $d[$id])
					? $d[$id][$hint->pageDefinitionId] : 0;
				$hints[$key]->maxNoOfChildren = $count;
			}
			$hints->update();
		}

		foreach ($templates as $id) {
			$hints = new \Scrivo\PageDefinitionHints(
				self::$context, $id,
				\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
			foreach ($hints as $hint) {
				$count = array_key_exists($hint->pageDefinitionId, $d[$id])
				? $d[$id][$hint->pageDefinitionId] : 0;
				$this->assertEquals($count, $hint->maxNoOfChildren);
			}
		}


	}

	/**
	 * Test exception thrown when creating an invalid list.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testCreateInvalidType() {
		$hints =
			new \Scrivo\PageDefinitionHints(
				self::$context, self::PAGE_DEFINITION_HOME_ID, -1);
	}

	/**
	 * Test exception thrown when creating an invalid list.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testCreateInvalidIdForChildTemplateRemaining() {
		// Should load
		// \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING with
		// a page id instead of a template id.
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_DEFINITION_HOME_ID,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);
	}

	/**
	 * Test exception thrown when accessing an invalid entry.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidAccess() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_DEFINITION_HOME_ID,
			\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
		$dummy = $hints[-1]->maxNoOfChildren;
	}

	/**
	 * Test exception thrown when setting an entry.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidSet() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_DEFINITION_HOME_ID,
			\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
		$hints[self::PAGE_DEFINITION_HOME_ID] = (object)array();
	}

	/**
	 * Test exception thrown when unsetting an entry.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidUnset() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_DEFINITION_HOME_ID,
			\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
		unset($hints[self::PAGE_DEFINITION_HOME_ID]);
	}

	/**
	 * Test isset on entries.
	 */
	function testIsset() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_DEFINITION_HOME_ID,
			\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
		$this->assertTrue(isset($hints[self::PAGE_DEFINITION_HOME_ID]));
		$this->assertFalse(isset($hints[-1]));
	}

	/**
	 * Test invalid update.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidUpdate1() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_DEFINITION_HOME_ID,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_COUNT);
		$hints->update();
	}

	/**
	 * Test invalid update.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidUpdate2() {
		$hints = new \Scrivo\PageDefinitionHints(
			self::$context, self::PAGE_HOME_ID,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);
		$hints->update();
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		// Perform load operation.
		$test = "";
		try {
			$hints = new \Scrivo\PageDefinitionHints($this->ctxDbFailureStub(),
				self::PAGE_DEFINITION_HOME_ID,
				\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_COUNT);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform update operation.
		$test = "";
		try {
			// Hack:
			$class = new ReflectionClass("\Scrivo\PageDefinitionHints");
			$property = $class->getProperty("context");
			$property->setAccessible(true);

			// Note: improper use of 'update' method, just to generate an
			// exception.
			$hints = new \Scrivo\PageDefinitionHints(self::$context,
				self::PAGE_DEFINITION_HOME_ID,
				\Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);

			$property->setValue($hints, $this->ctxDbFailureStub());

			$hints->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform load-corrected operation.
		$test = "";
		try {
			$hints = new \Scrivo\PageDefinitionHints(
				$this->ctxDbFailureStub(), self::PAGE_HOME_ID,
				\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

}

?>