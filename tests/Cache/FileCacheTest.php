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
 * $Id: FileCacheTest.php 801 2013-08-11 22:38:40Z geert $
 */

/**
 * Pretend we're in the Scrivo\Cache namespace so we can fool PHP and
 * overwrite the file functions in this scope.
 */
namespace Scrivo\Cache;

/**
 * Set of globals to control the the file mocking functions operate.
 */
$mock_fwrite = false;
$mock_fwrite_firstfails = false;
$mock_unlink = false;
$mock_touch = false;
$mock_fopen = false;

/**
 * Mock fwrite so that:
 * global $mock_fwrite = false: default behavoir of fwrite.
 * global $mock_fwrite = true: will not write and not return not a proper
 * count of written characters.
 * global $mock_fwrite_firstfails = true: as $mock_fwrite = true, but only the
 * first call will fail.
 */
function fwrite($fp, $data) {
	global $mock_fwrite, $mock_fwrite_firstfails;
	if ($mock_fwrite) {
		return strlen($data) - 2;
	} else if ($mock_fwrite_firstfails) {
		$mock_fwrite_firstfails = false;
		return strlen($data) - 2;
	} else {
		return call_user_func_array('\fwrite', func_get_args());
	}
}

/**
 * Mock unlink so that:
 * global $mock_unlink = false: default behavoir of unlink.
 * global $mock_unlink = true: unlink will fail and return false.
 */
function unlink($file) {
	global $mock_unlink;
	if ($mock_unlink) {
		return false;
	} else {
		return call_user_func_array('\unlink', func_get_args());
	}
}

/**
 * Mock touch so that:
 * global $mock_touch = false: default behavoir of touch.
 * global $mock_touch = true: touch will fail and return false.
 */
function touch($file) {
	global $mock_touch;
	if ($mock_touch) {
		return false;
	} else {
		return call_user_func_array('\touch', func_get_args());
	}
}

/**
 * Mock fopen so that:
 * global $mock_fopen = false: default behavoir of fopen.
 * global $mock_fopen = true: fopen will fail and return false.
 */
function fopen($file) {
	global $mock_fopen;
	if ($mock_fopen) {
		return false;
	} else {
		return call_user_func_array('\fopen', func_get_args());
	}
}

/**
 * Switch to another namespace.
 */
namespace FileCacheTest;

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\Cache\FileCache
 */
class FileCacheTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Location if the cache directory on the system.
	 *
	 * @var \Scrivo\String
	 */
	private static $cacheDir = null;

	/**
	 * Utility function to remove the cache directory.
	 */
	private static function deleteCache() {
		if (file_exists(self::$cacheDir)) {
			$files = array_diff(scandir(self::$cacheDir), array('.','..'));
			foreach ($files as $file) {
				unlink(self::$cacheDir.DIRECTORY_SEPARATOR.$file);
			}
			rmdir(self::$cacheDir);
		}
	}

	/**
	 * Set up for tests: point out where we want our test cache directory and
	 * clear it if there are still files there.
	 */
	public static function setUpBeforeClass() {
		self::$cacheDir =
			new \Scrivo\String(sys_get_temp_dir()."/TestCache");
		self::deleteCache();
	}

	/**
	 * Clean up after tests: remove the test cache directory.
	 */
	public static function tearDownAfterClass() {
		self::deleteCache();
	}

	/**
	 * Test basic functionality of the Scrivo file cache.
	 */
	public function testFileCache() {

		// Just soms data
		$str = new \Scrivo\String("Hatseflatse");

		// Create a cache
		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir);

		// Store some values (the second with a ttl of 1 sec).
		$cache->store(new \Scrivo\String("aString"), $str);
		$cache->store(new \Scrivo\String("anInt"), 3, 1);

		// Retrieve them and check the values.
		$this->assertTrue($str->equals(
			$cache->fetch(new \Scrivo\String("aString"))));
		$this->assertEquals(3, $cache->fetch(new \Scrivo\String("anInt")));

		// Also the cache object count should be 2.
		$this->assertCount(2, $cache->entryList());

		// Test the ttl: the first fetch should succeed, the second not.
		usleep(500000);
		$this->assertEquals(3, $cache->fetch(new \Scrivo\String("anInt")));
		usleep(1500000);
		$this->assertNull($cache->fetch(new \Scrivo\String("anInt")));

		// Overwrite one of the entries and check the result.
		$cache->overwrite(new \Scrivo\String("aString"),
			new \Scrivo\String("new string"));
		$this->assertTrue(\Scrivo\String::create("new string")->equals(
			$cache->fetch(new \Scrivo\String("aString"))));

		// Delete the entry and check if it's gone.
		$cache->delete(new \Scrivo\String("aString"));
		$this->assertNull($cache->fetch(new \Scrivo\String("aString")));

		// The cache object count should be 0.
		$this->assertCount(0, $cache->entryList());
	}

	/**
	 * If somehow the file system fails a \Scrivo\SystemException should be
	 * raised when unlinking files (f.i. when deleting from the cache).
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testUnlink() {

		global $mock_unlink;
		$mock_unlink = true;

		$cache = new \Scrivo\Cache\FileCache();
		$cache->store(new \Scrivo\String("unlink"), 3);
		$cache->delete(new \Scrivo\String("unlink"));

		$mock_unlink = false;
	}

	/**
	 * If somehow the file system fails a \Scrivo\SystemException should be
	 * raised when touching files (f.i. when storing in the cache).
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testTouch() {

		global $mock_touch;
		$mock_touch = true;

		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir);
		$cache->store(new \Scrivo\String("touch"), 3);

		$mock_touch = false;
	}

	/**
	 * When storing NULL values a \Scrivo\SystemException should be raised.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testStoreNull() {

		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir);
		$cache->store(new \Scrivo\String("unlink"), null);
	}

	/**
	 * If somehow the file system fails a \Scrivo\SystemException should be
	 * raised when opening files (f.i. when storing in the cache).
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testFileOpen() {

		global $mock_fopen;
		$mock_fopen = true;

		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir);
		$cache->store(new \Scrivo\String("fopen"), 3);

		$mock_fopen = false;
	}

	/**
	 * Test what happens if there is no space left (fwrite fails).
	 */
	public function testNoSpace() {

		global $mock_fwrite;

		// Start with an empty cache.
		self::deleteCache();
		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir);

		// Insert some data (the first entry has a ttl of 0).
		$cache->store(new \Scrivo\String("exp"), 3, 0);
		$cache->store(new \Scrivo\String("data1"), 3);
		$cache->store(new \Scrivo\String("data2"), 3);
		$cache->store(new \Scrivo\String("data3"), 3);

		// There should be three items in the cache.
		$this->assertCount(4, $cache->entryList());

		// We access the "data2" and "data3" entry.
		$tmp = $cache->fetch(new \Scrivo\String("data2"));
		$tmp = $cache->fetch(new \Scrivo\String("data3"));

		// We let fwrite fail from this point onward.
		$mock_fwrite = true;

		// Storing new values will not succeed but also will trigger the
		// cache to be purged.
		$this->assertEquals(\Scrivo\Cache::NO_SPACE,
			$cache->store(new \Scrivo\String("nospace"),
				new \Scrivo\String("Lots of data")));

		// Expired and unaccessed items will be removed form the cache at
		// this point.
		$this->assertCount(1, $cache->entryList());

		$mock_fwrite = false;
	}

	/**
	 * Basically for more coverage: when writing fails, the cache is purged so
	 * it is likely that the second write will succeed.
	 */
	public function testNoSpace2() {

		global $mock_fwrite_firstfails;
		$mock_fwrite_firstfails = true;

		// Start with an empty cache.
		self::deleteCache();
		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir);

		// This store will write its data in two steps: one failed write then
		// a cache purge will be triggered and another write will be executed.
		$cache->store(new \Scrivo\String("nospace2"),
			new \Scrivo\String("Lots of data"));

		$this->assertTrue(\Scrivo\String::create("Lots of data")->equals(
			$cache->fetch(new \Scrivo\String("nospace2"))));

		$mock_fwrite_firstfails = false;

	}

	/**
	 * Test the garbage collector.
	 */
	public function testGC() {

		// Start with an empty cache.
		self::deleteCache();

		// Make the garbage collector run on every store request.
		$cache = new \Scrivo\Cache\FileCache(self::$cacheDir, 1);

		// Store some data.
		$cache->store(new \Scrivo\String("exp"), 3, 0);

		// Wait so the item in the cache will be expired.
		usleep(1000000);

		// Now a new call to store will trigger the garbage collector to run.
		$cache->store(new \Scrivo\String("data1"), 3);

		// The cache should contain one item now.
		$this->assertCount(1, $cache->entryList());
	}
}

?>