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
 * $Id: ObjectRoleTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\ObjectRole
 */
class ObjectRoleTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for a test into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml"));
	}

	/**
	 * Test inserting and retrieving object roles.
	 */
	function testObjectRoles() {

		// The public role (defined in initial db data)
		$pr = new stdClass;
		$pr->id = self::PUBLIC_ROLE_ID;

		// The editor role (defined in initial db data)
		$er = new stdClass;
		$er->id = self::EDITOR_ROLE_ID;

		// Set some roles for some fictional object ids.
		\Scrivo\ObjectRole::set(self::$context, 34, array($pr));
		\Scrivo\ObjectRole::set(self::$context, 35, array($er, $pr));

		// Get the roles for these ids.
		$roles34 = \Scrivo\ObjectRole::select(self::$context, 34);
		$roles35 = \Scrivo\ObjectRole::select(self::$context, 35);

		$this->assertTrue(isset($roles34[self::PUBLIC_ROLE_ID]));
		$this->assertFalse(isset($roles34[self::EDITOR_ROLE_ID]));

		$this->assertTrue(isset($roles35[self::PUBLIC_ROLE_ID]));
		$this->assertTrue(isset($roles35[self::EDITOR_ROLE_ID]));

	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		// The public role (defined in initial db data)
		$pr = new stdClass;
		$pr->id = self::PUBLIC_ROLE_ID;

		$test = "";
		try {
			$r = \Scrivo\ObjectRole::select($this->ctxDbFailureStub(), 34);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		$test = "";
		try {
			\Scrivo\ObjectRole::set($this->ctxDbFailureStub(), 34, array($pr));
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>