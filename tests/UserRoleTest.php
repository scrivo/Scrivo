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
 * $Id: UserRoleTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\UserRole
 */
class UserRoleTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for a test into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml"));
	}

	/**
	 * User test data.
	 */
	function dataProvider() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"status" => \Scrivo\User::STATUS_MEMBER,
					"userCode" => new \Scrivo\String("testusercode"),
					"password" => new \Scrivo\String("verysecret"),
					"givenName" => new \Scrivo\String("A given name"),
					"familyNamePrefix" =>
					new \Scrivo\String("A family name prefix"),
					"familyName" => new \Scrivo\String("A family name"),
					"emailAddress" => new \Scrivo\String("A mail address"),
					"customData_tel" => new \Scrivo\String("0123456789"),
					"customData_city" => new \Scrivo\String("Utrecht"),
				),
				"argument 2" => (object) array(
					"status" => \Scrivo\User::STATUS_EDITOR,
					"userCode" => new \Scrivo\String("testusercode2"),
					"password" => new \Scrivo\String("verysecret2"),
					"givenName" => new \Scrivo\String("A given name 2"),
					"familyNamePrefix" =>
					new \Scrivo\String("A family name prefix 2"),
					"familyName" => new \Scrivo\String("A family name 2"),
					"emailAddress" =>
					new \Scrivo\String("A mail address 2"),
					"customData_tel" => new \Scrivo\String("0123456789 2"),
					"customData_city" => new \Scrivo\String("Utrecht 2"),
				)
			)
		);
	}

	/**
	 * Utility function for setting all user properties from test data.
	 *
	 * @param \Scrivo\User $u A reference to a user object for which to set
	 *   its properties.
	 * @param object $d An object that contains the data for the user
	 *   properties.
	 */
	private function setUserProperties(&$u, $d) {

		$u->status = $d->status;
		$u->userCode = $d->userCode;
		$u->password = $d->password;
		$u->givenName = $d->givenName;
		$u->familyNamePrefix = $d->familyNamePrefix;
		$u->familyName = $d->familyName;
		$u->emailAddress = $d->emailAddress;
		$u->customData->tel = $d->customData_tel;
		$u->customData->city = $d->customData_city;
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\UserRole(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\UserRole(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test invalid select by 'not a user object'.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidSelect() {
		$o = \Scrivo\UserRole::select(
			self::$context, \Scrivo\User::PRIMARY_ADMIN_ID);
	}

	/**
	 * Test inserting ande retrieving user roles.
	 * @param object $d1 User data for the first test user.
	 * @param object $d2 User data for the second test user.
	 *
	 * @dataProvider dataProvider
	 */
	function testUserRoles($d1, $d2) {

		// The public role (defined in initial db data)
		$pr = new stdClass;
		$pr->id = self::PUBLIC_ROLE_ID;
		$pr->isPublisher = false;

		// The editor role (defined in initial db data)
		$er = new \Scrivo\UserRole(self::$context);
		$er->isPublisher = true;
		$er->type = \Scrivo\Role::EDITOR_ROLE;
		$er->insert();

		// Create a user with the public role.
		$u1 = new \Scrivo\User(self::$context);
		$u1->userCode = new \Scrivo\String("userrole1");
		$u1->password = new \Scrivo\String("");
		$u1->insert();
		$u1->assignRoles(array($pr));

		// Create a user with the editor role.
		$u2 = new \Scrivo\User(self::$context);
		$u2->userCode = new \Scrivo\String("userrole2");
		$u2->password = new \Scrivo\String("");
		$u2->insert();
		$u2->assignRoles(array($er, $pr));

		// Load the edit user...
		$u3 = \Scrivo\User::fetch(self::$context, $u2->id);

		// ... and check its roles.
		$this->assertFalse($u3->roles[self::PUBLIC_ROLE_ID]->isPublisher);
		$this->assertTrue($u3->roles[$er->id]->isPublisher);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 *
	 * @param object $d1 User data for the first test user.
	 * @param object $d2 User data for the second test user.
	 *
	 * @dataProvider dataProvider
	 */
	function testDbFailure($d1, $d2) {

		// The public role (defined in initial db data)
		$pr = new stdClass;
		$pr->roleId = self::PUBLIC_ROLE_ID;
		$pr->isPublisher = false;

		// Create a user with the public role.
		$u1 = new \Scrivo\User($this->ctxDbFailureStub());
		$u1->userCode = new \Scrivo\String("userrole1");
		$u1->password = new \Scrivo\String("");

		$test = "";
		try {
			$r = $u1->roles;
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		$test = "";
		try {
			$u1->assignRoles(array($pr));
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>