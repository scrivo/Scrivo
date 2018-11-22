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
 * $Id: AccessControllerTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\AccessController
 */
class AccessControllerTest extends ScrivoDatabaseTestCase {

	/**
	 * Static reference to the Scrivo config used in these tests.
	 */
	protected static $ctxs = array();

	/**
	 * Create a Scrivo context for each test user to use in the tests.
	 */
	private function setUpContexts() {
		$cfg = new Scrivo\Config(new \Scrivo\Str("test_config"));
		self::$ctxs[Scrivo\User::ANONYMOUS_USER_ID] =
			new \Scrivo\Context($cfg, Scrivo\User::ANONYMOUS_USER_ID);
		self::$ctxs[self::MEMBER_USER_ID] =
			new \Scrivo\Context($cfg, self::MEMBER_USER_ID);
		self::$ctxs[self::EDITOR_USER_ID] =
			new \Scrivo\Context($cfg, self::EDITOR_USER_ID);
		self::$ctxs[self::PUBLISHER_USER_ID] =
			new \Scrivo\Context($cfg, self::PUBLISHER_USER_ID);
		self::$ctxs[Scrivo\User::PRIMARY_ADMIN_ID] =
			new \Scrivo\Context($cfg, Scrivo\User::PRIMARY_ADMIN_ID);
		self::$ctxs[self::ADMIN_USER_ID] =
			new \Scrivo\Context($cfg, self::ADMIN_USER_ID);
	}

	/**
	 * Insert the initial test data for a test into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page.yml", "asset.yml"));
	}

	/**
	 * Set up a table of exptected values for the test data.
	 */
	public function dataProviderCheckPermission() {
		return array(array(array(
			Scrivo\User::ANONYMOUS_USER_ID => array(
				self::PAGE_HOME_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::PAGE_FORUM_ID => array(
					\Scrivo\AccessController::READ_ACCESS => false,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::folder_id => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::SUB_FOLDER_ID => array(
					\Scrivo\AccessController::READ_ACCESS => false,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				)
			),
			self::MEMBER_USER_ID => array(
				self::PAGE_HOME_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::PAGE_FORUM_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::folder_id => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::SUB_FOLDER_ID => array(
					\Scrivo\AccessController::READ_ACCESS => false,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				)
			),
			self::EDITOR_USER_ID => array(
				self::PAGE_HOME_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::PAGE_FORUM_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::folder_id => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::SUB_FOLDER_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				)
			),
			self::PUBLISHER_USER_ID => array(
				self::PAGE_HOME_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::PAGE_FORUM_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				),
				self::folder_id => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::SUB_FOLDER_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => false,
					\Scrivo\AccessController::PUBLISH_ACCESS => false
				)
			),
			\Scrivo\User::PRIMARY_ADMIN_ID => array(
				self::PAGE_HOME_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::PAGE_FORUM_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::folder_id => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::SUB_FOLDER_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				)
			),
			self::ADMIN_USER_ID => array(
				self::PAGE_HOME_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::PAGE_FORUM_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::folder_id => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				),
				self::SUB_FOLDER_ID => array(
					\Scrivo\AccessController::READ_ACCESS => true,
					\Scrivo\AccessController::WRITE_ACCESS => true,
					\Scrivo\AccessController::PUBLISH_ACCESS => true
				)
			)
		)));
	}

	/**
	 * Test checkPermission.
	 *
	 * @dataProvider dataProviderCheckPermission
	 */
	public function testCheckPermission($data) {

		$this->setUpContexts();

		// For each context/user ...
		foreach ($data as $userId => $objPerm) {
			// ... for each object ...
			foreach ($objPerm as $objId => $perm) {
				// ... for each permission type ...
				foreach ($perm as $permission => $expected) {
					// ... check the permission and assert the result
					$this->assertEquals($expected,
						\Scrivo\AccessController::checkPermission(
							self::$ctxs[$userId], $permission, $objId));
				}
			}
		}
	}

	/**
	 * Test getPermissionsOnPages.
	 *
	 * @dataProvider dataProviderCheckPermission
	 */
	public function testGetPermissionOnPages($data) {

		$this->setUpContexts();

		$sets = array(
			array("parentId" => -1, "count" => 2),
			array("parentId" => 0, "count" => 1),
			array("parentId" => self::PAGE_HOME_ID, "count" => 1)
		);

		// For each context/user ...
		foreach ($data as $userId => $objPerm) {
			// ... for each set ...
			foreach($sets as $set) {

				// ... get the permissions ...
				if ($set["parentId"] >= 0) {
					$perms = \Scrivo\AccessController::getPermissionsOnPages(
						self::$ctxs[$userId], $set["parentId"]);
				} else {
					$perms = \Scrivo\AccessController::getPermissionsOnPages(
						self::$ctxs[$userId]);
				}

				// ... check if number of pages in the set match the expected
				// count.
				$this->assertCount($set["count"], $perms);

				// For all pages in the set get the permissions field ...
				foreach($perms as $objId => $allPerm) {
					// ... for each permission type ...
					foreach ($objPerm[$objId] as $permission => $expected) {
						// ... check if the permission bit was set correctly
						// in the permissions field.
						$this->assertEquals($expected, $permission & $allPerm);
					}
				}
			}
		}
	}

	/**
	 * Test getPermissionsOnAssets.
	 *
	 * @dataProvider dataProviderCheckPermission
	 */
	public function testGetPermissionOnAssets($data) {

		$this->setUpContexts();

		$sets = array(
			array("parentId" => -1, "count" => 2),
			array("parentId" => 0, "count" => 1),
			array("parentId" => self::folder_id, "count" => 1)
		);

		// For each context/user ...
		foreach ($data as $userId => $objPerm) {
			// ... for each set ...
			foreach($sets as $set) {

				// ... get the permissions ...
				if ($set["parentId"] >= 0) {
					$perms = \Scrivo\AccessController::getPermissionsOnAssets(
						self::$ctxs[$userId], $set["parentId"]);
				} else {
					$perms = \Scrivo\AccessController::getPermissionsOnAssets(
						self::$ctxs[$userId]);
				}

				// ... check if number of assets in the set match the expected
				// count.
				$this->assertCount($set["count"], $perms);

				// For all pages in the set get the permissions field ...
				foreach($perms as $objId => $allPerm) {
					// ... for each permission type ...
					foreach ($objPerm[$objId] as $permission => $expected) {
						// ... check if the permission bit was set correctly
						// in the permissions field.
						$this->assertEquals($expected, $permission & $allPerm);
					}
				}
			}
		}
	}

	/**
	 * The permission functions should throw an exception if an invalid user
	 * id was set in the context.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidUser() {
		$cfg = new Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, 987655443);
		$d = \Scrivo\AccessController::getPermission($context, self::PAGE_HOME_ID);
	}

	/**
	 * Check if the permission function throw an exception if the database
	 * fails.
	 */
	public function testDbFailure() {

		// Setup a context with the anonymous user as principal
		$cfg = new Scrivo\Config(new \Scrivo\Str("test_config"));
		$context = new \Scrivo\Context($cfg, Scrivo\User::ANONYMOUS_USER_ID);

		// Perform getPermission operation.
		$test = "";
		try {
			$d = \Scrivo\AccessController::getPermission(
				$this->ctxDbFailureStub($context), self::PAGE_HOME_ID);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform getPermissionsOnDocuments operation.
		$test = "";
		try {
			$d = \Scrivo\AccessController::getPermissionsOnPages(
				$this->ctxDbFailureStub($context), 0);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

		// Perform getPermissionsOnAssets operation.
		$test = "";
		try {
			$d = \Scrivo\AccessController::getPermissionsOnAssets(
				$this->ctxDbFailureStub($context), 0);
		} catch (\Scrivo\ResourceException $e) {
			$test = "Catch and release";
		}
		$this->assertEquals("Catch and release", $test);

	}

}

?>