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
 * $Id: ListItemTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\ListItemTest
 */
class ListItemTest extends PHPUnit_Framework_TestCase {

	/**
	 * Convenience function for creating a list item to use in the tests.
	 * @param \Scrivo\Str $tit A string to assign to the title field,
	 * @param \Scrivo\PropertySet $ps A property set to assign to this item's
	 *     property set.
	 * @return \Scrivo\ListItem a Scrivo list item.
	 */
	private function createItem(\Scrivo\Str $tit, \Scrivo\PropertySet $ps) {
		return new \Scrivo\ListItem(
			array(
				"list_item_id" => 1,
				"item_list_id" => 2,
				"page_id" => 3,
				"version" => 4,
				"parent_id" => 5,
				"list_item_definition_id" => 6,
				"link_id" => 7,
				"title" => (string)$tit,
				"date_created" => "now",
				"date_modified" => "now",
				"date_online" => "now",
				"date_offline" => null
			),
			$ps
		);
	}

	/**
	 * Just get and set the members of a list item.
	 */
	public function testListItem() {

		$ps = $this->getMock("\Scrivo\PropertySet");
		$str = new \Scrivo\Str("a");
		$item = $this->createItem($str, $ps);

		$now = new \DateTime("now");
		$then = new \DateTime("1969-06-27");

		$this->assertEquals(1, $item->id);
		$this->assertEquals(2, $item->listId);
		$this->assertEquals(3, $item->pageId);
		$this->assertEquals(5, $item->parentId);
		$this->assertEquals(6, $item->definitionId);
		$this->assertEquals(7, $item->linkedPageId);
		$this->assertTrue($str->equals($item->title));
		$this->assertGreaterThanOrEqual($now, $item->dateCreated);
		$this->assertGreaterThanOrEqual($now, $item->dateModified);
		$this->assertGreaterThanOrEqual($now, $item->dateOnline);
		$this->assertNull($item->dateOffline);

		$this->assertEquals($ps, $item->properties);

		$str2 = new \Scrivo\Str("b");

		$item->parentId = 100;
		$item->title = $str2;
		$item->dateOffline = $then;
		$item->dateOnline = $then;

		$this->assertEquals(100, $item->parentId);
		$this->assertTrue($str2->equals($item->title));
		$this->assertEquals($then, $item->dateOffline);
		$this->assertEquals($then, $item->dateOnline);

	}

	/**
	 * Test invalid property read access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {

		$ps = $this->getMock("\Scrivo\PropertySet");
		$str = new \Scrivo\Str("a");
		$item = $this->createItem($str, $ps);
		$tmp = $item->hatseFlatse;
	}

	/**
	 * Test invalid property write access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {

		$ps = $this->getMock("\Scrivo\PropertySet");
		$str = new \Scrivo\Str("a");
		$item = $this->createItem($str, $ps);
		$item->hatseFlatse = 2;
	}

}

?>