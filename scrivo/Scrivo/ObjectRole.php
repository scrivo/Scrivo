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
 * $Id: ObjectRole.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ObjectRole class.
 */

namespace Scrivo;

/**
 * Class that represents an object-role relation.
 *
 * The object-role relationship is a 1 to n relation of Scrivo objects (pages
 * or assets) and Scrivo roles.
 */
class ObjectRole extends \Scrivo\Role {

	/**
	 * Select the object-roles for a given object by object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $objectId An id of an object to create the object-roles for.
	 *
	 * @return \Scrivo\ObjectRole[roleId] An array containing the selected
	 *   object-roles.
	 */
	public static function select(\Scrivo\Context $context, $objectId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {

			$sth = $context->connection->prepare(
				"SELECT R.role_id, R.type, R.title, R.description
				FROM role R, object_role DR
				WHERE R.instance_id = :instId AND DR.instance_id = :instId
				  AND R.role_id = DR.role_id AND DR.page_id = :objectId");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":objectId", $objectId, \PDO::PARAM_INT);

			$res = array();
			$sth->execute();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new ObjectRole($context);

				$li->id = intval($rd["role_id"]);
				$li->type = intval($rd["type"]);
				$li->title = new \Scrivo\String($rd["title"]);
				$li->description = new \Scrivo\String($rd["description"]);

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \scrivo\ResourceException($e);
		}
	}

	/**
	 * Set the object roles for a given object.
	 *
	 * The object roles to set is either an array of ObjectRole or stdObject
	 * objects. stdObject need to contain the member roleId.
	 *
	 * Note: this sets all the roles for the object at once. So not giving the
	 * the roles effectivily clears the roles for the given object.
	 *
	 * TODO: does noet clear the cache propery
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $objectId An id of an object to set the object-roles for.
	 * @param \Scrivo\ObjectRole[]|object[] $roles A new set of object-roles
	 *   for the given object.
	 */
	public static function set(\Scrivo\Context $context, $objectId, $roles) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
			null
		));
		try {

			$sth = $context->connection->prepare(
				"DELETE FROM object_role WHERE
				instance_id = :instId AND page_id = :objectId");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":objectId", $objectId, \PDO::PARAM_INT);

			$sth->execute();

			if ($roles) {
				$sth = $context->connection->prepare(
					"INSERT INTO object_role
					  (instance_id, role_id, page_id)
					VALUES (:instId, :roleId, :objectId)");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":objectId", $objectId, \PDO::PARAM_INT);

				foreach ($roles as $role) {
					$sth->bindValue(":roleId", $role->id, \PDO::PARAM_INT);

					$sth->execute();
				}
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>