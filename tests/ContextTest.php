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
 * $Id: ContextTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\Context
 */
class ContextTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
	}

	public static function setUpAfterClass() {
	}

	/**
	 * Test if all members are set after instantitation.
	 */
	public function testInstance() {

		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, \Scrivo\User::PRIMARY_ADMIN_ID);

		// Test principal
		$this->assertEquals(
			$context->principal->id, \Scrivo\User::PRIMARY_ADMIN_ID);

		// Check if connection was set (that includes coverage of accessing
		// config data)
		$this->assertNotNull($context->connection);

		// Check if label was set
		$this->assertNotNull($context->labels);

		// Check if label was set
		$this->assertNotNull($context->config);

		// The primary admin should have write access
		$test = false;
		try {
			$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
			$test = true;
		} catch (\Scrivo\ApplicationException $e) {
			/*....*/
		}
		$this->assertTrue($test);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new Scrivo\Context($cfg, Scrivo\User::ANONYMOUS_USER_ID);
		$data = $context->sabicasElRey;
	}

	/**
	 * Test if the anonnymous users has no write access indeed.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 */
	public function testPermissionAnonymousUser() {
		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new Scrivo\Context($cfg, Scrivo\User::ANONYMOUS_USER_ID);
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Test serialize.
	 */
	public function testSerialize() {
		$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new Scrivo\Context($cfg, Scrivo\User::ANONYMOUS_USER_ID);
		$test = unserialize(serialize($context));
		$this->assertNull($test->config);
		$this->assertNull($test->principal);
		$this->assertNull($test->connection);
		$this->assertNull($test->labels);
		$this->assertNull($test->cache);
	}

}

?>