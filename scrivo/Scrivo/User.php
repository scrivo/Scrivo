<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: User.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\User class.
 */

namespace Scrivo;

/**
 * The Scrivo User class represents the user entity in Scrivo. Access rights
 * to objects like pages and assets are determined using these user principals.
 *
 * An user is identified by it's object id, but also by it's user code.
 * Convention (but not mandatory) is to use email addresses for user codes.
 * Both the user id and user code can be used to retrieve a user.
 *
 * The User class defines some descriptive members for the user's name and
 * and email address. For additional user data to store along with a user
 * you can use the multipurose 'customData' field.
 *
 * Peudo users
 *
 * The situation that you'll need to autenticate to different user database
 * arises frequently. Since the existence of Scrivo users is a requirement
 * for dertermining access to pages and assest it is suggested to use psuedo
 * users. After autenticating to an other database the user should adopt a
 * predefined (or progamatically defined) Scrivo user to access Scrivo
 * resources. This is already the case for annymous access to Scrivo resources,
 * in that case the user will adopt the Scrivo anymous user identity.
 *
 * TODO currently object ids 1 and 2 are used for reserved user ids, this is
 * not in line with the policy for object ids. In the code the are patched to
 * the values 3 and 4.
 *
 * @property-read object $customData A facility for 'free' storage.
 * @property-read int $id The user's user id (DB key)
 * @property-read array[] $roles An array of role-ids representing the roles
 *    for the user.
 * @property \Scrivo\String $emailAddress The user's email address.
 * @property \Scrivo\String $familyName The user's family name.
 * @property \Scrivo\String $familyNamePrefix A prefix for the user's family
 *    name.
 * @property \Scrivo\String $givenName The user's given name.
 * @property \Scrivo\String $password The user's password (encrypted).
 * @property int $status The user's status, one out of the constants
 *    self::STATUS_*
 * @property \Scrivo\String $userCode A more descriptive identification
 * 	  for the user than the user id.*
 */
class User {

	/**
	 * Status value indicating an admin user.
	 */
	const STATUS_ADMIN = 1;

	/**
	 * Status value indicating an editor.
	 */
	const STATUS_EDITOR = 2;

	/**
	 * Status value indicating a member.
	 */
	const STATUS_MEMBER = 3;

	/**
	 * Reserved user id for primary admin user.
	 */
	const PRIMARY_ADMIN_ID = 4;

	/**
	 * Reserved user id for the anonymous user.
	 */
	const ANONYMOUS_USER_ID = 3;

	/**
	 * The user's user id (DB key)
	 * @var int
	 */
	private $id = 0;

	/**
	 * The user status, one out of the constants self::STATUS_*
	 * @var int
	 */
	private $status = self::STATUS_MEMBER;

	/**
	 * A more descriptive identification for the user than the user id.
	 * @var \Scrivo\String
	 */
	private $userCode = null;

	/**
	 * The user's password (encrypted).
	 * @var \Scrivo\String
	 */
	private $password = null;

	/**
	 * The user's given name.
	 * @var \Scrivo\String
	 */
	private $givenName = null;

	/**
	 * A prefix for the user's family name.
	 * @var \Scrivo\String
	 */
	private $familyNamePrefix = null;

	/**
	 * The user's family name.
	 * @var \Scrivo\String
	 */
	private $familyName = null;

	/**
	 * The user's email address.
	 * @var \Scrivo\String
	 */
	private $emailAddress = null;

	/**
	 * An array of role-ids representing the roles for the user.
	 * @var array[]
	 */
	private $roles = null;

	/**
	 * A facility for 'free' storage.
	 * @var object
	 */
	private $customData = null;

	/**
	 * A Scrivo context.
	 * @var Context
	 */
	private $context = null;

	/**
	 * Create an empty user object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->userCode = new \Scrivo\String();
			$this->password = new \Scrivo\String();
			$this->givenName = new \Scrivo\String();
			$this->familyNamePrefix = new \Scrivo\String();
			$this->familyName = new \Scrivo\String();
			$this->emailAddress = new \Scrivo\String();
			$this->customData = new \stdClass;

			$this->context = $context;
		}
	}

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $name The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($name) {
		switch($name) {
			case "id": return $this->id;
			case "status": return $this->status;
			case "userCode": return $this->userCode;
			case "password": return $this->password;
			case "givenName": return $this->givenName;
			case "familyNamePrefix": return $this->familyNamePrefix;
			case "familyName": return $this->familyName;
			case "emailAddress": return $this->emailAddress;
			case "roles": return $this->getRoles();
			case "customData": return $this->customData;
		}
		throw new \Scrivo\SystemException("No such get-property '$name'.");
	}

	/**
	 * Implementation of the writable properties using the PHP magic
	 * method __set().
	 *
	 * @param string $name The name of the property to set.
	 * @param mixed $value The value of the property to set.
	 */
	public function __set($name, $value) {
		switch($name) {
			case "status": $this->setStatus($value); return;
			case "userCode": $this->setUserCode($value); return;
			case "password": $this->setPassword($value); return;
			case "givenName": $this->setGivenName($value); return;
			case "familyNamePrefix": $this->setFamilyNamePrefix($value); return;
			case "familyName": $this->setFamilyName($value); return;
			case "emailAddress": $this->setEmailAddress($value); return;
			case "context": $this->setContext($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a user object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$id = intval($rd["user_id"]);
		if ($id === 1) {
			$id = self::ANONYMOUS_USER_ID;
		}
		if ($id === 2) {
			$id = self::PRIMARY_ADMIN_ID;
		}

		$this->id = $id;
		$this->status = intval($rd["status"]);
		$this->userCode = new \Scrivo\String($rd["user_code"]);
		$this->password = new \Scrivo\String("- hidden -");
		$this->givenName = new \Scrivo\String($rd["given_name"]);
		$this->familyNamePrefix = new \Scrivo\String($rd["family_name_prefix"]);
		$this->familyName = new \Scrivo\String($rd["family_name"]);
		$this->emailAddress = new \Scrivo\String($rd["email_address"]);
		$this->customData = unserialize($rd["custom_data"]);
		if (is_array($this->customData)) {
			$this->customData = (object)$this->customData;
		}

		$this->context = $context;
	}

	/**
	 * Utility to patch legacy ids 1 and 2.
	 * @param int $id The id to patch.
	 * @return int The patched id.
	 */
	public static function patchId($id) {
		if ($id === self::ANONYMOUS_USER_ID) {
			return 1;
		}
		if ($id === self::PRIMARY_ADMIN_ID) {
			return 2;
		}
		return $id;
	}

	/**
	 * Get the user-roles for this user.
	 *
	 * @return UserRole[roleId] The set of user-roles for this user.
	 */
	private function getRoles() {
		if (!is_array($this->roles)) {
			$this->roles = UserRole::select($this->context, $this);
			$this->context->cache["U".$this->id] = $this;
		}
		return $this->roles;
	}

	/**
	 * Set the user status, one out of the constants User::STATUS_*
	 *
	 * @param int The user status, one out of the constants User::STATUS_*
	 */
	private function setStatus($status) {
		if ($status != self::STATUS_ADMIN && $status != self::STATUS_EDITOR
				&& $status != self::STATUS_MEMBER) {
			throw new \Scrivo\SystemException("Not a valid status");
		}
		$this->status = $status;
	}

	/**
	 * Set the user code.
	 *
	 * @param \Scrivo\String A more descriptive identification for the
	 *   user than the user id.
	 */
	private function setUserCode(\Scrivo\String $userCode) {
		$this->userCode = $userCode;
	}

	/**
	 * Set the user's password (not encrypted).
	 *
	 * @param \Scrivo\String The user's password (not encrypted).
	 */
	private function setPassword(\Scrivo\String $password) {
		$this->password = new \Scrivo\String($this->encrypt($password));
	}

	/**
	 * Set the user's given name.
	 *
	 * @param \Scrivo\String The user's given name.
	 */
	private function setGivenName(\Scrivo\String $givenName) {
		$this->givenName = $givenName;
	}

	/**
	 * Set a prefix for the user's family name.
	 *
	 * @param \Scrivo\String A prefix for the user's family name.
	 */
	private function setFamilyNamePrefix(\Scrivo\String $familyNamePrefix) {
		$this->familyNamePrefix = $familyNamePrefix;
	}

	/**
	 * Set the user's family name.
	 *
	 * @param \Scrivo\String The user's family name.
	 */
	private function setFamilyName(\Scrivo\String $familyName) {
		$this->familyName = $familyName;
	}

	/**
	 * Set the user's email address
	 *
	 * @param \Scrivo\String The user's email address
	 */
	private function setEmailAddress(\Scrivo\String $emailAddress) {
		$this->emailAddress = $emailAddress;
	}

	/**
	 * Set the user's context.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	private function setContext(\Scrivo\Context $context) {
		$this->context = $context;
	}

	/**
	 * Check if the current user code is valid.
	 *
	 * The user code must be unique in the current database instance and at
	 * least three characters in length.
	 *
	 * @throws ApplicationException If the user code does not comply.
	 */
	private function checkUserCode() {

		if ($this->userCode->length < 3) {
			throw new ApplicationException("User code too short",
				StatusCodes::USER_CODE_TOO_SHORT);
		}

		$sth = $this->context->connection->prepare(
			"SELECT COUNT(*) FROM user
			WHERE instance_id = :instId AND user_code = :userCode
			  AND user_id <> :id");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":id", self::patchId($this->id), \PDO::PARAM_INT);
		$sth->bindValue(":userCode", $this->userCode, \PDO::PARAM_STR);
		$sth->execute();

		$count = $sth->fetchColumn();

		if ($count != 0) {
			throw new \Scrivo\ApplicationException("User code already in use",
				StatusCodes::USER_CODE_IN_USE);
		}

	}

	/**
	 * Encrypt a password.
	 *
	 * @param \Scrivo\String $password
	 *
	 * @return string
	 */
	private function encrypt(\Scrivo\String $password) {
		$salt = base_convert(md5(mt_rand(0, 1000000)), 16, 36);
		$c =  crypt($password, "\$2a\$07\$".$salt."\$");
		return $c;
	}

	/**
	 * Check if this user object can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
		$this->checkUserCode();
	}

	/**
	 * Insert new user object data into the database.
	 *
	 * First it is checked if the data of this user object can be inserted
	 * into the database, then the data is inserted into the database. If no id
	 * was set a new object id is generated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			if (!$this->id) {
				$this->id = $this->context->connection->generateId();
			}

			$sth = $this->context->connection->prepare(
				"INSERT INTO user (
					instance_id, user_id, status, user_code, password, 
					given_name, f amily_name_prefix, family_name, email_address,
					custom_data
				) VALUES (
					:instId, :id, :status, :userCode, :password, 
					:givenName, :familyNamePrefix, :familyName, :emailAddress, 
					:customData
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":status", $this->status, \PDO::PARAM_INT);
			$sth->bindValue(":userCode", $this->userCode, \PDO::PARAM_STR);
			$sth->bindValue(":password", $this->password, \PDO::PARAM_STR);
			$sth->bindValue(":givenName", $this->givenName, \PDO::PARAM_STR);
			$sth->bindValue(
				":familyNamePrefix", $this->familyNamePrefix, \PDO::PARAM_STR);
			$sth->bindValue(":familyName", $this->familyName, \PDO::PARAM_STR);
			$sth->bindValue(
				":emailAddress", $this->emailAddress, \PDO::PARAM_STR);
			$sth->bindValue(
				":customData", serialize($this->customData), \PDO::PARAM_STR);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this user object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
		$this->checkUserCode();
	}

	/**
	 * Update existing user object data in the database.
	 *
	 * First it is checked if the data of this user object can be updated
	 * in the database, then the data is updated in the database.
	 *
	 * The user's password cannot be updated with this method. Use
	 * User::updatePassword() in order to do that.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE user SET
					status = :status, user_code = :userCode,
					given_name = :givenName,
					family_name_prefix = :familyNamePrefix, 
					family_name = :familyName, email_address = :emailAddress, 
					custom_data = :customData
				WHERE instance_id = :instId AND user_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", self::patchId($this->id), \PDO::PARAM_INT);

			$sth->bindValue(":status", $this->status, \PDO::PARAM_INT);
			$sth->bindValue(":userCode", $this->userCode, \PDO::PARAM_STR);
			$sth->bindValue(":givenName", $this->givenName, \PDO::PARAM_STR);
			$sth->bindValue(
				":familyNamePrefix", $this->familyNamePrefix, \PDO::PARAM_STR);
			$sth->bindValue(":familyName", $this->familyName, \PDO::PARAM_STR);
			$sth->bindValue(
				":emailAddress", $this->emailAddress, \PDO::PARAM_STR);
			$sth->bindValue(
				":customData", serialize($this->customData), \PDO::PARAM_STR);

			$sth->execute();

			unset($this->context->cache["U".$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Update the password of this user.
	 *
	 * The password property should be set to its new value. When saving
	 * a encrypted value will be stored into the database. When this user
	 * object is loaded again its password property will contain the encrypted
	 * value for the new password.
	 *
	 * @throws ApplicationException If one or more of the fields contain
	 *   invalid data.
	 */
	public function updatePassword() {
		try {
			$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

			$sth = $this->context->connection->prepare(
				"UPDATE user SET password = :password
				WHERE instance_id = :instId AND user_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", self::patchId($this->id), \PDO::PARAM_INT);

			$sth->bindValue(
				":password", $this->password, \PDO::PARAM_STR);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if a given password matches with the one of this user.
	 *
	 * @param \Scrivo\String $toTest Password to test.
	 *
	 * @return boolean True if given password matches the user's, false if not.
	 */
	public function checkPassword(\Scrivo\String $toTest) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {

			$sth = $this->context->connection->prepare(
				"SELECT password FROM user
				WHERE instance_id = :instId AND user_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", self::patchId($this->id), \PDO::PARAM_INT);

			$sth->execute();

			if ($sth->rowCount() != 1) {
				throw new \Scrivo\SystemException("Failed to load User");
			}

			$pw = $sth->fetchColumn();

			return $pw == crypt($toTest, $pw);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of user object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id a valid object id.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the user data.
	 */
	private function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
		if ($id == self::PRIMARY_ADMIN_ID || $id == self::ANONYMOUS_USER_ID) {
			throw new \Scrivo\ApplicationException("Can't delete system users",
				StatusCodes::CANNOT_DELETE_SYSTEM_USERS);
		}
	}

	/**
	 * Delete existing user data from the database.
	 *
	 * First it is is checked if it's possible to delete user data,
	 * then the user data including its dependecies is deleted from
	 * the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id a valid object id.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the user data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			foreach (array("user", "user_role") as $table) {

				$sth = $context->connection->prepare(
					"DELETE FROM $table
					WHERE instance_id = :instId AND user_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();
			}

			unset($context->cache["U".$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Assign user roles to this user.
	 *
	 * The user roles to set is either an array of UserRole or stdObject
	 * objects. stdObject need to contain the members roleId and isPublisher.
	 *
	 * Note: this sets all the roles for the user at once. So not giving the
	 * the roles effectivily clears the roles for the given user.
	 *
	 * @param UserRole[]|object[] $roles A new set of user-roles for the given
	 *   user.
	 */
	public function assignRoles($roles) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		UserRole::set($this->context, $this, $roles);
	}

	/**
	 * Fetch a user object from the database using the object id or
	 * user code.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int|\Scrivo\String $id a valid object id or user code.
	 *
	 * @return \Scrivo\User The requested user object.
	 *
	 * @throws \Scrivo\ApplicationException If the given user code was
	 *    not found (when selecting by user code).
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(array(\Scrivo\ArgumentCheck::TYPE_INTEGER, "Scrivo\String"))
		));
		try {

			if ($id instanceof \Scrivo\String) {
				$byUserCode = true;
			} else if (is_int($id)) {
				$byUserCode = false;
			} else {
				throw new \Scrivo\SystemException("Invalid argument type");
			}

			// Try to retieve the user from the cache ...
			if (!$byUserCode && isset($context->cache["U".$id])) {
				// ... get it from the cache and set the context.
				$user = $context->cache["U".$id];
				$user->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT user_id, status, user_code,
						given_name, family_name_prefix, family_name, email_address, custom_data
					FROM user
					WHERE instance_id = :instId AND
						".($byUserCode ? "user_code" : "user_id")." = :id");

				$user = new \Scrivo\User($context);

				$context->connection->bindInstance($sth);
				if ($byUserCode) {
					$sth->bindValue(":id", $id, \PDO::PARAM_STR);
				} else {
					$sth->bindValue(
						":id", $user->patchId($id), \PDO::PARAM_INT);
				}

				$sth->execute();

				if ($sth->rowCount() != 1) {
					if ($byUserCode) {
						throw new \Scrivo\ApplicationException(
							"Failed to load User $byUserCode");
					}
					throw new \Scrivo\SystemException("Failed to load User");
				}

				$rd = $sth->fetch(\PDO::FETCH_ASSOC);

				$user->setFields($context, $rd);

				$context->cache["U".$user->id] = $user;
			}

			return $user;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select users from the database.
	 *
	 * Depending on the given arguments all users or all users for a given
	 * role can be retrieved.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $roleId Optional role id for which to retrieve the users.
	 *
	 * @return User[id] An array containing the selected users.
	 */
	public static function select(\Scrivo\Context $context, $roleId = null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);
		try {
			$sth = $context->connection->prepare(
				"SELECT U.user_id, U.status, U.user_code, U.given_name, U.family_name_prefix,
					U.family_name, U.email_address, U.custom_data
				FROM user U" .($roleId? ", user_role": "")."
				WHERE U.instance_id = :instId" .($roleId
					? " AND user_role.instance_id = :instId AND
					user_role.user_id =	U.user_id AND role_id = :roleId"
					: ""));

			$context->connection->bindInstance($sth);
			if ($roleId) {
				$sth->bindValue(":roleId", $roleId, \PDO::PARAM_INT);
			}

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new User();
				$li->setFields($context, $rd);

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>