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
 * $Id: NoCacheTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\Cache\NoCache
 */
class NoCacheTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test basic functionality of the Scrivo file cache.
	 */
	public function testNoCache() {

		// Just some data
		$str = new \Scrivo\String("Hatseflatse");

		// Create a cache
		$cache = new \Scrivo\Cache\NoCache();

		// Store some values (the second with a ttl of 1 sec).
		$cache->store(new \Scrivo\String("aString"), $str);
		$cache->store(new \Scrivo\String("anInt"), 3, 1);

		// Retrieve them and check the values.
		$this->assertNull($cache->fetch(new \Scrivo\String("aString")));
		$this->assertNull($cache->fetch(new \Scrivo\String("anInt")));

		// Also the cache object count should be 0.
		$this->assertCount(0, $cache->entryList());

		// Slam one of the entries and check the result.
		$this->assertEquals($cache::DATA_STORED,
			$cache->store(new \Scrivo\String("aString"),
				new \Scrivo\String("new string")));

		// Overwrite one of the entries and check the result.
		$this->assertEquals($cache::DATA_STORED,
			$cache->overwrite(new \Scrivo\String("aString"),
				new \Scrivo\String("new string")));

		// The cache object count should be 0.
		$this->assertCount(0, $cache->entryList());

		// Delete the entry and check if it's gone.
		$cache->delete(new \Scrivo\String("aString"));
		$this->assertNull($cache->fetch(new \Scrivo\String("aString")));

		// The cache object count should be 0.
		$this->assertCount(0, $cache->entryList());
	}

	/**
	 * When storing NULL values a \Scrivo\SystemException should be raised.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testStoreNull() {

		$cache = new \Scrivo\Cache\NoCache();
		$cache->store(new \Scrivo\String("null1"), null);

	}

	/**
	 * When storing NULL values a \Scrivo\SystemException should be raised.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testStoreNull2() {

		$cache = new \Scrivo\Cache\NoCache();
		$cache->store(new \Scrivo\String("null2"), 3);
		$cache->overwrite(new \Scrivo\String("null2"), null);
	}


}

?>