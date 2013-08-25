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
 * $Id: SessionTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\Session
 */
class SessionTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
	}

	public static function setUpAfterClass() {
	}

	/**
	 * Test basic functionality of the Scrivo session object.
	 */
	public function testSession() {

		// Construct a session ...
		$sess = new \Scrivo\Session();

		// ... set some vars ...
		$sess->var1 = 1;
		$sess->var2 = "Test";

		// ... and assert the set values.
		$this->assertEquals(1, $sess->var1);
		$this->assertEquals("Test", $sess->var2);

		// Test the isset member ...
		$this->assertTrue(isset($sess->var1));
		$this->assertFalse(isset($sess->var3));

		// ... and the unset member.
		unset($sess->var1);
		$this->assertFalse(isset($sess->var1));

		// At last destroy the session.
		$sess->destroy();
		$this->assertFalse(isset($sess->var2));

	}

	/**
	 * Test working with two Scrivo sessions.
	 */
	public function testTwoSessions() {

		// Create two sessions ...
		$sessA = new \Scrivo\Session();
		$sessB = new \Scrivo\Session("NamedSession");

		// And set a variable in each one.
		$sessA->var1 = 1;
		$sessB->var2 = "Test";

		// Recreate the session ...
		$sessA = new \Scrivo\Session();
		$sessB = new \Scrivo\Session("NamedSession");

		// ... and assert the set values.
		$this->assertEquals(1, $sessA->var1);
		$this->assertEquals("Test", $sessB->var2);

		// Destroy the first session and test if the second survives.
		$sessA->destroy();
		$this->assertEquals("Test", $sessB->var2);

	}

	/**
	 * Test the application thrown by a Scrivo session if a nonexisting
	 * property is accessed.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testSessionException() {
		$sessA = new \Scrivo\Session();
		$a = $sessA->var1;
	}

}

?>