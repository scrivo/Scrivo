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
 * $Id: LocalCacheTest.php 847 2013-08-20 16:41:10Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\LocalCache
 */
class LocalCacheTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
	}

	public static function setUpAfterClass() {
	}

	/**
	 * Simple caching tests.
	 */
	public function testCache() {

		$c = new \Scrivo\LocalCache();

		$c[3] = 3;
		$this->assertTrue(isset($c[3]));
		$this->assertEquals(3, $c[3]);

		unset($c[3]);
		$this->assertFalse(isset($c[3]));

		$this->assertNull($c[2]);
	}

	/**
	 * Store data in an persistent cache through the local cache.
	 */
	public function testPersistentCache1() {

		$c = new \Scrivo\LocalCache(new \Scrivo\Cache\FileCache());

		// Delete the value from persistent cache (if set).
		unset($c["pc"]);

		// This will store the value in persistent cache too.
		$c["pc"] = 3;

		$this->assertEquals(3, $c["pc"]);
	}

	/**
	 * Load data from the persitent cache through the local cache.
	 */
	public function testPersistentCache2() {

		// Creating the cache using the persistent file cache will enable
		// us to retrieve values stored in previous tests.
		$c = new \Scrivo\LocalCache(new \Scrivo\Cache\FileCache());

		// This entry was set in 'testPersistentCache1'.
		$this->assertTrue(isset($c["pc"]));
	}

	/**
	 * Load data from the persitent cache through the local cache.
	 */
	public function testPersistentCache3() {

		// Creating the cache using the persistent file cache will enable
		// us to retrieve values stored in previous tests.
		$c = new \Scrivo\LocalCache(new \Scrivo\Cache\FileCache());

		// This entry was set in 'testPersistentCache1'.
		$this->assertEquals(3, $c["pc"]);

		unset($c["pc"]);
		$this->assertFalse(isset($c["pc"]));
	}
}

?>