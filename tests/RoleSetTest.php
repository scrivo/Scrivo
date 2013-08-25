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
 * $Id: RoleTest.php 628 2013-05-20 00:20:29Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\RoleSet
 */
class RoleSetTest extends PHPUnit_Framework_TestCase {

	/**
	 * Create a Scrivo user stub with member status and the role id's
	 * 2, 3 and 4 assigned to it.
	 *
	 * @return \Scrivo\User A Scrivo user stub.
	 */
	function memberStub() {
		$stub = $this->getMock("\Scrivo\User");
		$stub->expects($this->any())->method("__get")
				->will($this->returnCallback(
			function ($name) {
				switch ($name) {
					case "status": return \Scrivo\User::STATUS_MEMBER;
					case "roles": return array(
						(object)array(
							"id" => 2, "type" => \Scrivo\Role::PUBLIC_ROLE),
						(object)array(
							"id" => 3, "type" => \Scrivo\Role::PUBLIC_ROLE),
						(object)array(
							"id" => 4, "type" => \Scrivo\Role::PUBLIC_ROLE)
					);
				}
			}
		));
		return $stub;
	}

	/**
	 * Create a Scrivo user stub with editor status.
	 *
	 * @return \Scrivo\User A Scrivo user stub.
	 */
	function editorStub() {
		$stub = $this->getMock("\Scrivo\User");
		$stub->expects($this->any())->method("__get")
				->will($this->returnCallback(
			function ($name) {
				switch ($name) {
					case "status": return \Scrivo\User::STATUS_EDITOR;
				}
			}
		));
		return $stub;
	}


	/**
	 * Test the array access members of the RoleSet class.
	 */
	function testRoleSet() {

		$rs = new \Scrivo\RoleSet();

		$rs[] = 123;
		$rs[] = 124;
		$rs[] = 125;

		$this->assertEquals(123, $rs[123]);
		$this->assertEquals(124, $rs[124]);
		$this->assertEquals(125, $rs[125]);
		$this->assertNull($rs[3]);

		$this->assertTrue(isset($rs[123]));
		$this->assertFalse(isset($rs[3]));

		// Test exception thrown.
		$test = "";
		try {
			unset($rs[1]);
		} catch (\Scrivo\SystemException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

	/**
	 * Test canRead for an editor user (can always read).
	 */
	function testEditorCheck() {

		$rs = new \Scrivo\RoleSet();
		$rs[] = 123;

		$editor = $this->editorStub();

		$this->assertTrue($rs->canRead($editor));

		// Test exception thrown.
		$test = "";
		try {
			$rs->checkReadPermission($editor);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("", $test);

	}

	/**
	 * Test canRead for a member user.
	 */
	function testUserCheck() {

		$member = $this->memberStub();

		$rs = new \Scrivo\RoleSet();
		$rs[] = 1;
		$rs[] = 2;
		$rs[] = 3;
		$this->assertTrue($rs->canRead($member));

		// Test exception thrown.
		$test = "";
		try {
			$rs->checkReadPermission($member);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("", $test);

		$rs = new \Scrivo\RoleSet();
		$rs[] = 1;
		$rs[] = 5;
		$rs[] = 6;
		$this->assertFalse($rs->canRead($member));

		// Test exception thrown.
		$test = "";
		try {
			$rs->checkReadPermission($member);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

}

?>