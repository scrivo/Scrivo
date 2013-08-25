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
 * $Id: UserRole.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\UserRole class.
 */

namespace Scrivo;

/**
 * Class that represents a user-role relation.
 *
 * The User-Role relationship is a 0 to n relation of Scrivo users and Scrivo
 * roles. This relation can carry an additional entity: the publisher status.
 * This status is only relevant for editor roles and determines if an editor
 * is allowed to publish data in an staging environment.
 *
 * Note that both static methods are also exposed through the User::getRoles()
 * and User::assignRoles($roles) methods. This class should not be considered
 * part of the public API but as private to the User class.
 */
class UserRole extends \Scrivo\Role {

	/**
	 * The publisher status.
	 * @var boolean
	 */
	private $isPublisher;

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $name The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($name) {
		if ($name === "isPublisher") {
			return $this->isPublisher;
		}
		return parent::__get($name);
	}

	/**
	 * Implementation of the writable properties using the PHP magic
	 * method __set().
	 *
	 * @param string $name The name of the property to set.
	 * @param mixed $value The value of the property to set.
	 */
	public function __set($name, $value) {
		if ($name === "isPublisher") {
			$this->setIsPublisher($value);
			return;
		}
		parent::__set($name, $value);
	}

	/**
	 * Set the publisher status for this user-role relation.
	 *
	 * @param boolean $status The publisher status.
	 */
	public function setIsPublisher($status) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_BOOLEAN)
		));

		$this->isPublisher = $status;
	}

	/**
	 * Select the user-roles for a given user.
	 *
	 * @param \Scrivo\Context $context A connection to a Scrivo database.
	 * @param \Scrivo\User $user A user to create the user-roles for.
	 *
	 * @return \Scrivo\UserRole[roleId] An array containing the selected
	 *    user-roles.
	 */
	public static function select(\Scrivo\Context $context, $user) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null, null));
		try {
			if (!$user instanceof \Scrivo\User) {
				throw new \Scrivo\SystemException("Invalid argument type");
			}

			$sth = $context->connection->prepare(
				"SELECT R.role_id, R.type, R.title, R.description,
					UR.is_publisher FROM role R, user_role UR
				WHERE R.instance_id = :instId AND UR.instance_id = :instId
					AND R.role_id = UR.role_id AND UR.user_id = :userId");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":userId",
				\Scrivo\User::patchId($user->id), \PDO::PARAM_INT);

			$res = array();
			$sth->execute();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new UserRole();
				$li->setFields($context, $rd);
				$li->isPublisher = $rd["is_publisher"] == 1;

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Set the user roles for a given user.
	 *
	 * The user roles to set is either an array of UserRole or stdClass
	 * objects. stdClass need to contain the members id and isPublisher.
	 *
	 * Note: this sets all the roles for the user at once. So not giving the
	 * the roles effectivily clears the roles for the given user.
	 *
	 * @param \Scrivo\Context $context A connection to a Scrivo database.
	 * @param \Scrivo\User $user A user to set the user-roles for.
	 * @param \Scrivo\UserRole[]|object[] $roles A new set of user-roles for
	 *   the given user.
	 */
	public static function set(
			\Scrivo\Context $context, \Scrivo\User $user, array $roles) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null, null, null));
		try {

			$sth = $context->connection->prepare(
				"DELETE FROM user_role WHERE
				instance_id = :instId AND user_id = :userId");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":userId", $user->id, \PDO::PARAM_INT);

			$sth->execute();

			if ($roles) {
				$sth = $context->connection->prepare(
					"INSERT INTO user_role
					  (instance_id, role_id, user_id, is_publisher)
					VALUES (:instId, :roleId, :userId, :publisher)");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":userId", $user->id, \PDO::PARAM_INT);

				foreach ($roles as $role) {
					$sth->bindValue(":roleId", $role->id, \PDO::PARAM_INT);
					$sth->bindValue(":publisher",
						(bool)$role->isPublisher, \PDO::PARAM_BOOL);

					$sth->execute();
				}
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>