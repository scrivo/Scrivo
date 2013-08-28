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
 * $Id: ConfigTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\Config
 */
class ConfigTest extends PHPUnit_Framework_TestCase {

	protected static $emptyCfgFile = null;
	protected static $defaultCfgFile = "";

	public static function setUpBeforeClass() {
		// Create a temporary config file to hold configuration data.
		self::$emptyCfgFile = new \Scrivo\String(
			tempnam(sys_get_temp_dir(), "PHPUnit_Scrivo_ConfigTest"));
	}

	public static function AfterClass() {
		// Remove the temporary config file
		unlink(self::$emptyCfgFile);
		if (self::$defaultCfgFile) {
			unlink(self::$defaultCfgFile);
		}
	}

	/**
	 * Test different scenarios when constructing a configuration data object.
	 * When constructing the location of a valid configuration file should be
	 * passed to the constructor. If not it should look for an ".htscrivo" file
	 * in directory tree from the current directory upward. The latter scenario
	 * is not tested. When no or no valid configuration file was found an
	 * exception should be thrown.
	 */
	public function testConstruct() {

		// Create a config object based upon the local config file
		$this->assertNotNull(
			new \Scrivo\Config(new \Scrivo\String("test_config")));

		// Create a config object using this tests config data
		$this->assertNotNull(new \Scrivo\Config(self::$emptyCfgFile));

		// Force an exception for an invalid config file
		$this->setExpectedException(
			"\Scrivo\SystemException", "Invalid config file");
		$cfg = new \Scrivo\Config(new \Scrivo\String("ConfigTest.php"));

		// Force an exception for a nonexistent config file
		$this->setExpectedException(
			"\Scrivo\SystemException", "Could not locate config file");
		$cfg = new \Scrivo\Config("blah");
	}

	/**
	 * A number of members of the configuration object are guaranteed to be
	 * set. Check if all default members are set.
	 */
	public function testDefaultMembers() {

		$cfg = new \Scrivo\Config(self::$emptyCfgFile);

		$this->assertTrue($cfg->ROOT_PAGE_ID === 1);
		$this->assertTrue($cfg->HTML_TIDY === 0);
		$this->assertTrue($cfg->WEBSERVICE_TIDY->equals(
			new \Scrivo\String("http://www.scrivo.nl/tidy/tidy.php")));
		$this->assertTrue($cfg->WEBSERVICE_SPELL->equals(
			new \Scrivo\String("http://www.scrivo.nl/spell/spell.php")));
		$this->assertTrue($cfg->HTTP_PROTOCOL->equals(
			new \Scrivo\String("http://")));
		$this->assertTrue($cfg->ADMIN_IP_ADDRESSES->equals(
			new \Scrivo\String("127.0.0.1")));
		$this->assertTrue($cfg->SESSION_PREFIX->equals(
			new \Scrivo\String("__")));
		$this->assertTrue($cfg->KEY_PREFIX->equals(
			new \Scrivo\String("_")));
		$this->assertTrue($cfg->JS_DEBUG === 0);
		$this->assertTrue($cfg->UI_LANG->equals(
			new \Scrivo\String("en_US")));
		$this->assertTrue($cfg->DB_API->equals(
			new \Scrivo\String("mysql")));
		if (function_exists("apc_fetch")) {
			$this->assertTrue($cfg->CACHE_TYPE->equals(
				new \Scrivo\String("APC")));
		} else {
			$this->assertTrue($cfg->CACHE_TYPE->equals(
				new \Scrivo\String("")));
		}
		$this->assertTrue($cfg->CACHE_DIR->equals(
			new \Scrivo\String("")));
		$this->assertTrue($cfg->CACHE_DIR_GC === 0);
	}

	/**
	 * Test overwritten default value.
	 */
	public function testOverwrittenMember() {

		$cfgData = "DB_API=SQLSERVER\n";

		$handle = fopen(self::$emptyCfgFile, "w");
		fwrite($handle, $cfgData);
		fclose($handle);

		$cfg = new \Scrivo\Config(self::$emptyCfgFile);

		$this->assertTrue($cfg->DB_API->equals(
			new \Scrivo\String("SQLSERVER")));
	}

	/**
	 * Any optional keys in the config data should appear as members from
	 * the config object. Check optional configuration members.
	 */
	public function testOptionalMembers() {

		$cfgData =
			"CUSTOM_MEMBER_STR = \"a string\"\nCUSTOM_MEMBER_INT = 1001\n";

		$handle = fopen(self::$emptyCfgFile, "w");
		fwrite($handle, $cfgData);
		fclose($handle);

		$cfg = new \Scrivo\Config(self::$emptyCfgFile);

		$this->assertTrue($cfg->CUSTOM_MEMBER_INT === 1001);
		$this->assertTrue($cfg->CUSTOM_MEMBER_STR->equals(
			new \Scrivo\String("a string")));
	}

	/**
	 * Trying to access inexistent keys should throw an exception.
	 */
	public function testInvalidMember() {

		$cfg = new \Scrivo\Config(self::$emptyCfgFile);

		$this->assertFalse(isset($cfg->HANDY_DANDY_PROPERTY));

		$this->setExpectedException(
			"\Scrivo\SystemException" , "Invalid configuration property");
		$dummy = $cfg->HANDY_DANDY_PROPERTY;
	}

	/**
	 * Try to find a .htscrivo file higher in the directory tree. Note
	 * the test assumes the file isn't there.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testFindConfigFile() {
		$cfg = new \Scrivo\Config();
	}

	/**
	 * Create a .htscrivo file in the tmp directory and try to find
	 * the default config file again but now in that directory.
	 */
	public function testFindConfigFile2() {
		self::$defaultCfgFile =
			sys_get_temp_dir() . DIRECTORY_SEPARATOR . ".htscrivo";
		// It's an hack, but then again this global was assigned the value of
		// the phpunit PHP script anyway.
		$_SERVER["SCRIPT_FILENAME"] = self::$defaultCfgFile;
		copy("test_config", self::$defaultCfgFile);
		// This uses $_SERVER["SCRIPT_FILENAME"]
		$cfg = new \Scrivo\Config();
		$this->assertNotNull($cfg);
	}
}

?>
