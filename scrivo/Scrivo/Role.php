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
 * $Id: Role.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Role class.
 */

namespace Scrivo;

/**
 * Class that represents a system role class.
 *
 * Access to pages and assets is restricted through roles. Users can be
 * registered to one or more roles as well as objects (pages and assets).
 * Do one or more roles of the object and the user match an access level
 * higher than the default for the user is granted.
 *
 * When access is granted, a distiction is made in editor roles and public
 * roles. A user with User::STATUS_EDITOR can only be registered to one or
 * more Role::EDITOR_ROLE-s, a user with status User::STATUS_MEMBER only
 * to one or more User::PUBLIC_ROLE-s.
 *
 * For role matching see \Scrivo\AccessController.
 *
 * TODO currently object ids 1 and 2 are used for system roles, this is not
 * in line with the policy for object ids.
 *
 * @property-read int $id The role id (DB key).
 * @property \Scrivo\String $description A descriptive name for this role.
 * @property \Scrivo\String $title The role title.
 * @property int $type The role type, \Scrivo\Role::EDITOR_ROLE or
 *    \Scrivo\Role::PUBLIC_ROLE
 */
class Role {

	/**
	 * Constant to denote a public role.
	 */
	const PUBLIC_ROLE = 3;

	/**
	 * Constant to denote an editor role.
	 */
	const EDITOR_ROLE = 2;

	/**
	 * The role id (DB key).
	 * @var int
	 */
	protected $id = 0;

	/**
	 * The role type, \Scrivo\Role::EDITOR_ROLE or \Scrivo\Role::PUBLIC_ROLE
	 * @var int
	 */
	private $type = self::PUBLIC_ROLE;

	/**
	 * The role title.
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * A descriptive name for this role.
	 * @var \Scrivo\String
	 */
	private $description = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty role object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->title = new \Scrivo\String();
			$this->description = new \Scrivo\String();

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
			case "type": return $this->type;
			case "title": return $this->title;
			case "description": return $this->description;
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
			case "type": $this->setType($value); return;
			case "title": $this->setTitle($value); return;
			case "description": $this->setDescription($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a role object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	protected function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["role_id"]);
		$this->type = intval($rd["type"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->description = new \Scrivo\String($rd["description"]);

		$this->context = $context;
	}

	/**
	 * Set the role's type, \Scrivo\Role::EDITOR_ROLE or
	 *    \Scrivo\Role::PUBLIC_ROLE.
	 *
	 * @param int $type The role's type, \Scrivo\Role::EDITOR_ROLE or
	 *    \Scrivo\Role::PUBLIC_ROLE.
	 */
	private function setType($type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
				array(self::EDITOR_ROLE, self::PUBLIC_ROLE))
		));

		$this->type = $type;
	}

	/**
	 * Set the role title.
	 *
	 * @param \Scrivo\String $title The role's title.
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set the descriptive name for this role.
	 *
	 * @param \Scrivo\String $description A descriptive name for this role.
	 */
	private function setDescription(\Scrivo\String $description) {
		$this->description = $description;
	}

	/**
	 * Check if this role object can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new role object data into the database.
	 *
	 * First it is checked if the data of this role object can be inserted
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
				"INSERT INTO role (instance_id, role_id, type, title, description)
				VALUES (:instId, :id, :type, :title, :descr)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":descr", $this->description, \PDO::PARAM_STR);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this role object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing role object data in the database.
	 *
	 * First it is checked if the data of this role object can be updated
	 * in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE role SET
				  type = :type, title = :title, description = :descr
				WHERE instance_id = :instId AND role_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":descr", $this->description, \PDO::PARAM_STR);

			$sth->execute();

			unset($this->context->cache[$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of role object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the role to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing role data from the database.
	 *
	 * First it is is checked if it's possible to delete role data,
	 * then the role data including its dependecies is deleted from
	 * the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the role to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the role data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			foreach (array("role", "user_role", "object_role") as $table) {

				$sth = $context->connection->prepare(
					"DELETE FROM $table
					WHERE instance_id = :instId AND role_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();
			}

			unset($context->cache[$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Fetch a role object from the database using its object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the role to select.
	 *
	 * @return \Scrivo\Role The requested role object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the role from the cache ...
			if (isset($context->cache[$id])) {
				// ... get it from the cache and set the context.
				$role = $context->cache[$id];
				$role->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT role_id, type, title, description
					FROM role
				WHERE instance_id = :instId AND role_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException("Failed to load role");
				}

				$role = new \Scrivo\Role();
				$role->setFields($context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache[$id] = $role;
			}

			return $role;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select roles from the database.
	 *
	 * Depending on the given arguments the public or editor roles can be
	 * retrieved.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $type A role type: \Scrivo\Role::PUBLIC_ROLE or
	 *   \Scrivo\Role::EDITOR_ROLE for which to retrieve the roles.
	 *
	 * @return \Scrivo\Role[id] An array containing the selected roles.
	 */
	public static function select(\Scrivo\Context $context, $type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
				array(self::EDITOR_ROLE, self::PUBLIC_ROLE))
		));
		try {
			$sth = $context->connection->prepare(
				"SELECT role_id, type, title, description FROM role
				WHERE instance_id = :instId AND type = :type");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":type", $type, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new Role();
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