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
 * $Id: UserTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\User
 */
class UserTest extends ScrivoDatabaseTestCase {

	/**
	 * User test data.
	 */
	function dataProvider() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"status" => \Scrivo\User::STATUS_MEMBER,
					"userCode" => new \Scrivo\Str("testusercode"),
					"password" => new \Scrivo\Str("verysecret"),
					"givenName" => new \Scrivo\Str("A given name"),
					"familyNamePrefix" =>
						new \Scrivo\Str("A family name prefix"),
					"familyName" => new \Scrivo\Str("A family name"),
					"emailAddress" => new \Scrivo\Str("A mail address"),
					"customData_tel" => new \Scrivo\Str("0123456789"),
					"customData_city" => new \Scrivo\Str("Utrecht"),
				)
			),
			"test 2" => array(
				"argument 1" => (object) array(
					"status" => \Scrivo\User::STATUS_MEMBER,
					"userCode" => new \Scrivo\Str("testusercode2"),
					"password" => new \Scrivo\Str("verysecret2"),
					"givenName" => new \Scrivo\Str("A given name 2"),
					"familyNamePrefix" =>
						new \Scrivo\Str("A family name prefix 2"),
					"familyName" => new \Scrivo\Str("A family name 2"),
					"emailAddress" =>
						new \Scrivo\Str("A mail address 2"),
					"customData_tel" => new \Scrivo\Str("0123456789 2"),
					"customData_city" => new \Scrivo\Str("Utrecht 2"),
				)
			)
		);
	}

	/**
	 * User test data.
	 */
	function dataProviderUpdate() {
		return array(
			"test 1" => array(
				"argument 1" => (object) array(
					"status" => \Scrivo\User::STATUS_MEMBER,
					"userCode" => new \Scrivo\Str("testusercode"),
					"password" => new \Scrivo\Str("verysecret"),
					"givenName" => new \Scrivo\Str("A given name"),
					"familyNamePrefix" =>
						new \Scrivo\Str("A family name prefix"),
					"familyName" => new \Scrivo\Str("A family name"),
					"emailAddress" => new \Scrivo\Str("A mail address"),
					"customData_tel" => new \Scrivo\Str("0123456789"),
					"customData_city" => new \Scrivo\Str("Utrecht"),
				),
				"argument 2" => (object) array(
					"status" => \Scrivo\User::STATUS_EDITOR,
					"userCode" => new \Scrivo\Str("testusercode2"),
					"password" => new \Scrivo\Str("verysecret2"),
					"givenName" => new \Scrivo\Str("A given name 2"),
					"familyNamePrefix" =>
						new \Scrivo\Str("A family name prefix 2"),
					"familyName" => new \Scrivo\Str("A family name 2"),
					"emailAddress" =>
						new \Scrivo\Str("A mail address 2"),
					"customData_tel" => new \Scrivo\Str("0123456789 2"),
					"customData_city" => new \Scrivo\Str("Utrecht 2"),
				)
			)
		);
	}

	/**
	 * Insert the initial test data for a test into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml"));
	}

	/**
	 * See if we can instantiate the system users (admin and anonymous). The
	 * admins password is secret. The two system users cannot be deleted.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 * @expectedExceptionMessage Can't delete system users
	 */
	function testSystemUsers() {

		$admin = \Scrivo\User::fetch(
			self::$context, Scrivo\User::PRIMARY_ADMIN_ID);
		$this->assertNotNull($admin);
		$this->assertEquals($admin->id, Scrivo\User::PRIMARY_ADMIN_ID);

		$this->assertTrue(
			$admin->checkPassword(new \Scrivo\Str("secret")));

		$user = \Scrivo\User::fetch(
			self::$context, Scrivo\User::ANONYMOUS_USER_ID);
		$this->assertNotNull($user);
		$this->assertEquals($user->id, Scrivo\User::ANONYMOUS_USER_ID);

		$this->assertCount(1, $user->roles);

		\Scrivo\User::delete(self::$context, $user->id);
	}

	/**
	 * Test if an exception was thrown if a invalid user status is set.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @expectedExceptionMessage Not a valid status
	 */
	function testInvalidStatus() {
		$usr = new \Scrivo\User(self::$context);
		$usr->status = -1;
	}

	/**
	 * Test if an exception was thrown if a invalid user was load.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @expectedExceptionMessage Failed to load User
	 */
	function testLoadInvalidUserById() {
		$usr = \Scrivo\User::fetch(self::$context, -1);
	}

	/**
	 * Test if an exception was thrown if a invalid user was load.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 * @expectedExceptionMessage Failed to load User
	 */
	function testLoadInvalidUserByUserCode() {
		$usr = \Scrivo\User::fetch(self::$context, new \Scrivo\Str("aa"));
	}

	/**
	 * Test if an exception was thrown if a illegal argument as used when
	 * loading a user.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @expectedExceptionMessage No such argument type
	 */
	function testLoadInvalidUserByInvalidUserCode() {
		$usr = \Scrivo\User::fetch(self::$context, "aa");
	}

	/**
	 * Test if an exception was thrown if a invalid user usercode is set.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 * @expectedExceptionMessage User code too short
	 */
	function testInvalidUserCode() {
		$usr = new \Scrivo\User(self::$context);
		$usr->userCode = new \Scrivo\Str("aa");
		$usr->insert();
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
	 * Set of assertions to vevify if object properties equal those of the
	 * given data.
	 *
	 * @param \Scrivo\User $u The user object under test.
	 * @param object $d The data to check the user properties against.
	 */
	private function checkUserProperties($u, $d) {

		$this->assertEquals($u->status, $d->status);
		$this->assertTrue($u->userCode->equals($d->userCode));
		$this->assertTrue($u->checkPassword($d->password));
		$this->assertTrue($u->givenName->equals($d->givenName));
		$this->assertTrue($u->familyNamePrefix->equals($d->familyNamePrefix));
		$this->assertTrue($u->familyName->equals($d->familyName));
		$this->assertTrue($u->emailAddress->equals($d->emailAddress));
		$this->assertTrue($u->customData->tel->equals($d->customData_tel));
		$this->assertTrue($u->customData->city->equals($d->customData_city));
	}

	/**
	 * Test if a user can be created/inserted into the database.
	 *
	 * @param object $d Test data to populate the user properties.
	 *
	 * @dataProvider dataProvider
	 */
	function testCreate($d) {

		// Create a blank user and populate its fields.
		$u = new \Scrivo\User(self::$context);
		$this->setUserProperties($u, $d);
		$this->assertEquals($u->id, 0);

		// Insert it into the data
		$u->insert();
		$this->assertNotEquals($u->id, 0);

		// Reload and check the user properties against the test data.
		$u = \Scrivo\User::fetch(self::$context, $u->userCode);
		$this->checkUserProperties($u, $d);

		// Reload it from local cache.
		$oc = \Scrivo\User::fetch(self::$context, $u->id);
		$this->assertTrue($u === $oc);
	}

	/**
	 * Test invalid property get access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertyGet() {
		$o = new \Scrivo\User(self::$context);
		$data = $o->sabicasElRey;
	}

	/**
	 * Test invalid property set access.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidPropertySet() {
		$o = new \Scrivo\User(self::$context);
		$o->sabicasElRey = "el mejor";
	}

	/**
	 * Test if is possible to insert a user with a usercode that already
	 * exists.
	 *
	 * @expectedException \Scrivo\ApplicationException
	 * @expectedExceptionMessage User code already in use
	 */
	function testCreateDuplicateUsercode() {
		$u = new \Scrivo\User(self::$context);
		$u->userCode = new \Scrivo\Str("admin");
		$u->insert();
	}

	/**
	 * Test checkPassword if the user was deleted already.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	function testCheckPasswordDeletedUser() {

		$u = \Scrivo\User::fetch(self::$context, self::EDITOR_USER_ID);
		\Scrivo\User::delete(self::$context, self::EDITOR_USER_ID);
		$u->checkPassword(new \Scrivo\Str("blahblah"));
	}

	/**
	 * Test if the user properties can be updated.
	 *
	 * @param object $d Data for the initial values of the user properties.
	 * @param object $d2 Data for the modified values of the user properties.
	 *
	 * @dataProvider dataProviderUpdate
	 */
	function testUpdate($d, $d2) {

		// Create a blank user and populate its fields.
		$u = new \Scrivo\User(self::$context);
		$this->setUserProperties($u, $d);
		$u->insert();

		// Reload the user.
		$u = \Scrivo\User::fetch(self::$context, $u->userCode);
		$this->checkUserProperties($u, $d);

		// Set the new user properities.
		$this->setUserProperties($u, $d2);
		$u->update();
		$u->updatePassword();

		// Reload the user and check the user properties.
		$u2 = \Scrivo\User::fetch(self::$context, $u->userCode);
		$this->checkUserProperties($u2, $d2);
	}

	/**
	 * Test the creation of user lists.
	 *
	 * @param object $d1 User data for the first test user in the list.
	 * @param object $d2 User data for the second test user in the list.
	 *
	 * @dataProvider dataProviderUpdate
	 */
	function testSelect($d1, $d2) {

		// The public role (defined in initial db data)
		$pr = new stdClass;
		$pr->id = self::PUBLIC_ROLE_ID;
		$pr->isPublisher = false;

		// The editor role (defined in initial db data)
		$er = new stdClass;
		$er->id = self::EDITOR_ROLE_ID;
		$er->isPublisher = true;

		// Create a user with the public role.
		$u1 = new \Scrivo\User(self::$context);
		$this->setUserProperties($u1, $d1);
		$u1->insert();
		$u1->assignRoles(array($pr));

		// Create a user with the editor role.
		$u2 = new \Scrivo\User(self::$context);
		$this->setUserProperties($u2, $d2);
		$u2->insert();
		$u2->assignRoles(array($er, $pr));

		// Select all users: 2 created here, 2+4 from test data.
		$users = \Scrivo\User::select(self::$context);
		$this->assertCount(8, $users);

		// Test if retrieved users match against the test data.
		$this->checkUserProperties($users[$u1->id], $d1);
		$this->checkUserProperties($users[$u2->id], $d2);

		// Select all users with the public role: 2 created here, 1+1 from test
		// data.
		$users = \Scrivo\User::select(self::$context, self::PUBLIC_ROLE_ID);
		$this->assertCount(4, $users);

		// Test if retrieved users match against the test data.
		$this->checkUserProperties($users[$u1->id], $d1);
		$this->checkUserProperties($users[$u2->id], $d2);

		// Select all users with the editer role: 1 created here, 0+2 from test
		// data.
		$users = \Scrivo\User::select(self::$context, self::EDITOR_ROLE_ID);
		$this->assertCount(3, $users);

		// Test if retrieved users match against the test data.
		$this->assertFalse(isset($users[$u1->id]));
		$this->checkUserProperties($users[$u2->id], $d2);
	}

	/**
	 * Test deletion of users.
	 *
	 * @param object $d1 User data for the first test user to insert and
	 *    delete.
	 * @param object $d2 User data for the second test user to insert and
	 *    delete.
	 *
	 * @dataProvider dataProviderUpdate
	 */
	function testDelete($d1, $d2) {

		// Create two users.
		$u1 = new \Scrivo\User(self::$context);
		$this->setUserProperties($u1, $d1);
		$u1->insert();
		$u2 = new \Scrivo\User(self::$context);
		$this->setUserProperties($u2, $d2);
		$u2->insert();

		// Select all users: 2 created here, 2+4 from test data.
		$users = \Scrivo\User::select(self::$context);
		$this->assertCount(8, $users);

		// Delete the first user.
		\Scrivo\User::delete(self::$context, $u1->id);

		// Select all users: 1 created here, 2+4 from test data.
		$users = \Scrivo\User::select(self::$context);
		$this->assertCount(7, $users);

		// Delete the second user.
		\Scrivo\User::delete(self::$context, $u2->id);

		// Select all users: 0 created here, 2+4 from test data.
		$users = \Scrivo\User::select(self::$context);
		$this->assertCount(6, $users);
	}

	/**
	 * Perform database operations as an editor.
	 *
	 * @dataProvider dataProviderUpdate
	 */
	function testAccessEditor($d1, $d2) {

		// Create a user with the editor status.
		$editor = new \Scrivo\User(self::$context);
		$this->setUserProperties($editor, $d2);
		$editor->insert();

		// Create a user to do operations on
		$tmp = new \Scrivo\User(self::$context);
		$this->setUserProperties($tmp, $d1);
		$tmp->insert();
		$testUserId = $tmp->id;

		$cfg = new Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, $editor->id);

		// Creating/inseting a new user as editor should not succeed.
		$test = "";
		$new = new \Scrivo\User($context);
		$this->setUserProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading a user as editor should succeed.
		$user = \Scrivo\User::fetch($context, $testUserId);

		// But updating not
		$test = "";
		try {
			$user->update();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// As for updating the passord
		$test = "";
		try {
			$user->updatePassword();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// And deleting should not be possible too
		$test = "";
		try {
			\Scrivo\User::delete($context, $user->id);
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading all users should succeed
		$all = \Scrivo\User::select($context);
	}

	/**
	 * Perform database operations as a public user.
	 *
	 * @dataProvider dataProviderUpdate
	 */
	function testAccessUser($d1, $d2) {

		// Create a user with the member status.
		$editor = new \Scrivo\User(self::$context);
		$this->setUserProperties($editor, $d1);
		$editor->insert();

		// Create a user to do operations on
		$tmp = new \Scrivo\User(self::$context);
		$this->setUserProperties($tmp, $d2);
		$tmp->insert();
		$testUserId = $tmp->id;

		$cfg = new Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, $editor->id);

		// Loading a user as editor should succeed.
		$user = \Scrivo\User::fetch($context, $testUserId);

		// Creating/inserting a new user as member should not succeed.
		$test = "";
		$new = new \Scrivo\User($context);
		$this->setUserProperties($new, $d2);
		try {
			$new->insert();
		} catch (\Scrivo\ApplicationException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Loading all users should succeed
		$all = \Scrivo\User::select($context);
	}

	/**
	 * For more coverage test the \Scrivo\ResourceExceptions thrown when
	 * the database connection fails during database operations.
	 */
	function testDbFailure() {

		// Perform load operation.
		$test = "";
		try {
			$user =
				\Scrivo\User::fetch($this->ctxDbFailureStub(), 1234);
			$this->assertNotNull($user);
			$this->assertEquals($user->id, Scrivo\User::ANONYMOUS_USER_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform insert operation.
		$test = "";
		try {
			$user = new \Scrivo\User($this->ctxDbFailureStub());
			$user->userCode = new \Scrivo\Str("12345");
			$user->insert();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform update operation.
		$test = "";
		try {
			$user->update();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform update password operation.
		$test = "";
		try {
			$user->checkPassword(new \Scrivo\Str("Dumbo"));
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform update password operation.
		$test = "";
		try {
			$user->updatePassword();
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform delete operation.
		$test = "";
		try {
			\Scrivo\User::delete($this->ctxDbFailureStub(), $user->id);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform select operation.
		$test = "";
		try {
			$users = \Scrivo\User::select($this->ctxDbFailureStub());
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);
	}

}

?>