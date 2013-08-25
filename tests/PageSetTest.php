<?php
/* Copyright (c) 2012-2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: PageDefinitionHintsTest.php 628 2013-05-20 00:20:29Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageDefinitionHints
 *
 * TODO: If you add a template the hints for the new template are correctly
 * set to zero, but for other templates it is set to NULL (infinity) that is
 * a bug, create a test for that.
 */
class PageSetTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page.yml", "page_definition.yml", "page_definition_hints.yml"));
	}

	/**
	 * Some test data to see how an page set wil behave wen retrieved by
	 * different users.
	 *
	 * @return array:array:\Scrivo\Context,int
	 */
	function dataProviderCounts() {
		$cfg = new Scrivo\Config(new \Scrivo\String("test_config"));
		return array(
			"test 1" => array(
				"argument 1" =>	new \Scrivo\Context($cfg,
					\Scrivo\User::ANONYMOUS_USER_ID),
				"argument 2" => 0,
			),
			"test 2" => array(
				"argument 1" => new \Scrivo\Context($cfg,
					self::MEMBER_USER_ID),
				"argument 2" => 1,
			),
			"test 3" => array(
				"argument 1" => new \Scrivo\Context($cfg,
					self::EDITOR_USER_ID),
				"argument 2" => 3,
			),
		);
	}

	/**
	 * Test creation of a page set (through a page object).
	 */
	function testCreate() {

		$ps1 = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;

		$this->assertEquals(3, count($ps1));

		$ps2 = \Scrivo\Page::fetch(self::$context, self::PAGE_MENU1_ID)->children;

		$this->assertEquals(2, count($ps2));

	}

	/**
	 * Just loop through a page set to test the iteraton methods.
	 */
	public function testIterator() {

		$ps1 = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;
		$count = 0;
		foreach ($ps1 as $key => $value) {
			$count++;
		}
		$this->assertEquals(3, $count);
	}

	/**
	 * Test invalid method access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidMethodAccess() {
		$ps = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;
		unset($ps[1]);
	}

	/**
	 * Test array Access.
	 */
	public function testArrayAccess() {
		$ps = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;
		$this->assertEquals(self::PAGE_FORUM_ID, $ps[self::PAGE_FORUM_ID]->id);
	}

	/**
	 * Test invalid array access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidArryaAccess() {
		$ps = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;
		$ps[1234];
	}

	/**
	 * @dataProvider dataProviderCounts
	 */
	function testCounts(\Scrivo\Context $context, $exp1) {
		$ps1 = \Scrivo\Page::fetch($context, self::PAGE_HOME_ID)->children;
		$this->assertEquals($exp1, count($ps1));
	}

	/**
	 * Test re-retrieval from items after an item was unserialised.
	 * NOTE: this test depends on undesirable aspect of untyped paramters
	 *   to the function offsetSet.
	 */
	function testCeck() {
		$ps = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;
		$ps[self::PAGE_FORUM_ID] = self::PAGE_FORUM_ID;
		$this->assertEquals(self::PAGE_FORUM_ID, $ps[self::PAGE_FORUM_ID]->id);
	}

	/**
	 * Test seralization methods.
	 */
	function testSleepAndWakeup() {

		$ps1 = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID)->children;
		$kps1 = key($ps1);
		next($ps1);
		$kps2 = key($ps1);

		$ps1 = unserialize(serialize($ps1));

		$this->assertEquals($kps1, key($ps1));
		next($ps1);
		$this->assertEquals($kps2, key($ps1));

	}

}

?>