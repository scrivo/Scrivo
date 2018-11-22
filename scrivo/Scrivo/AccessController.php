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
 * $Id: AccessController.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\AccessController class.
 */

namespace Scrivo;

/**
 * Class that deals with the user permissions on objects.
 *
 * The AccessController class implements the fuctionality to determine
 * user access level to objects like pages and assets.
 *
 * Access rights are expressed as bit flags. There are three different access
 * right defined:
 *
 * * AccessController::READ_ACCESS: first bit set (=1)
 * * AccessController::WRITE_ACCESS: second bit set (=2)
 * * AccessController::PUBLISH_ACCESS: third bit set (=4)
 *
 * It is important to note that Scrivo uses access levels internally. This
 * means that although there are different permissions (READ_ACCESS,
 * WRITE_ACCESS, etc.) defined they are not used indivudally:
 * WRITE_ACCESS always implies READ_ACCESS permission (WRITE_ACCESS
 * always means READ_ACCESS+WRITE_ACCESS, PUBLISH_ACCESS always means
 * READ_ACCESS+WRITE_ACCESS+PUBLISH_ACCESS).
 *
 * This means that when you retrieve a permission you are guaranteed to
 * retrieve one of the values (0, 1, 3 or 7). Using bit flags might suggest
 * otherwise, but note that other combinations are not possible.
 *
 * This also means that you can use either bitwise operations or comparison
 * when checking a permission:
 *
 * if (AccessController::getPermission($aContext, $anObjectId) >=
 * 		AccessController::ACCESS_LEVEL_READ) { ... }
 *
 * is equivalent to:
 *
 * if (AccessController::getPermission($aContext, $anObjectId) &
 * 		AccessController::ACCESS_LEVEL_READ) { ... }
 *
 * Note that AccessController::checkPermission(...) is probably easier
 * to use.
 *
 * Another feature is that Scrivo users are differentiated into three
 * catagories which limits the range of atainable permissions:
 *
 * * Members (Users::STATUS_MEMBER), these users represent the group of users
 *   that visit the actual site: Their users access levels can only be one
 *   of NO_ACCESS or READ_ACCESS.
 * * Editors (Users::STATUS_EDITOR), the users that login in to do editing
 *   work Scrivo: These users have access level can range from READ_ACCESS to
 *   PUBLISH_ACCESS.
 * * Super users (Users::STATUS_ADMIN), users that can access everything. These
 *   users always have PUBLISH_ACCESS.
 *
 * In other words members can read what they are allowed to but never write,
 * editors can write (and possibly publish) what they are allowed to and always
 * read and admins can do everthing.
 *
 * For a description of Scrivo user see the Scrivo::User class and to see
 * how access rights are granted to users see the Scrivo::Role class.
 */
class AccessController {

	/**
	 * Bit flag that indicates that the user has read access.
	 */
	const READ_ACCESS = 1;

	/**
	 * Bit flag that indicates that the user has write access.
	 */
	const WRITE_ACCESS = 2;

	/**
	 * Bit flag that indicates that the user has publiser rights.
	 */
	const PUBLISH_ACCESS = 4;

	/**
	 * Check the permission of a user on an object (page or asset).
	 *
	 * Note that a valid user and object id are assumed. Invalid user ids
	 * will raise an exception but invalid object ids are accepted and
	 * the given permission will then be the checked against the minimum
	 * access permission for the given user.
	 *
	 * @param Context $context A connection to a Scrivo database.
	 * @param int $perm The permission to test (READ_ACCESS || WRITE_ACCESS
	 *    || PUBLISH_ACCESS)
	 * @param int $objectId A valid object id of a page or asset.
	 *
	 * @return boolean True if the user has the specified permission on the
	 *    object.
	 */
	public static function checkPermission(
			Context $context, $perm, $objectId=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER, array(0,1,2,3,4,5,6,7)),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 2);

		return self::getPermission($context, $objectId) >= $perm;
	}

	/**
	 * Get the permission of a user on an object (page or asset).
	 *
	 * Note that a valid user and object id are assumed. Invalid user ids
	 * will raise an exception but invalid object ids are accepted and
	 * assigned the minimum access permission for the given user.
	 *
	 * @param Context $context A connection to a Scrivo database.
	 * @param int $objectId A valid object id of a page or asset.
	 *
	 * @return int The user's permission on the object (A bitwise combination
	 *   of READ_ACCESS, WRITE_ACCESS and PUBLISH_ACCESS).
	 */
	public static function getPermission(Context $context, $objectId=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);
		try {

			// Status (admin/editor/member) of given user.
			$us = $context->principal->status;

			// Determine minimum access permission for given user.
			$p = 0;

			if ($us == User::STATUS_EDITOR) {
				$p = self::READ_ACCESS;
			}
			if ($us == User::STATUS_ADMIN) {
				$p = self::READ_ACCESS | self::WRITE_ACCESS
					| self::PUBLISH_ACCESS;
			}

			// For admin users we're finished, more work for others.
			if ($us != User::STATUS_ADMIN) {

				// Check if there are object roles match the user roles.
				$sth = $context->connection->prepare(
					"SELECT MAX(is_publisher)
					FROM user_role UR, object_role DR, role R
					WHERE UR.instance_id = :instId AND DR.instance_id = :instId
					  AND R.instance_id = :instId AND R.type = :type
					  AND R.role_id = UR.role_id AND R.role_id = DR.role_id
					  AND UR.user_id = :userId AND DR.page_id = :objectId");

				$context->connection->bindInstance($sth);
				$sth->bindValue(
					":userId", $context->principal->id, \PDO::PARAM_INT);
				$sth->bindValue(":type", $us == User::STATUS_MEMBER ?
					Role::PUBLIC_ROLE : Role::EDITOR_ROLE, \PDO::PARAM_INT);
				$sth->bindValue(":objectId", $objectId, \PDO::PARAM_INT);

				$sth->execute();

				// Add additional permissions depending on the values retrieved
				// from the query.
				if (null !== ($publ = $sth->fetchColumn(0))) {
					if ($us == User::STATUS_EDITOR) {
						$p |= self::WRITE_ACCESS;
						if ($publ) {
							$p |= self::PUBLISH_ACCESS;
						}
					} else if ($us = User::STATUS_MEMBER) {
						$p |= self::READ_ACCESS;
					}
				}
			}

			return $p;

		} catch(\PDOException $e) {
			throw new ResourceException($e);
		}
	}

	/**
	 * Get the permissions on a series of objects for a given user.
	 *
	 * @deprecated Access to pages and assets should be checked through role
	 *   mapping.
	 *
	 * @param Context $context A connection to a Scrivo database.
	 * @param string[] $queryParts An array that contains SQL fragments to
	 *    do the proper select statments for the given case.
	 * @param int $parentId An optional parent id to use in the selection of
	 *    the objects.
	 *
	 * @return int[] Array in which the keys are the object ids and the
	 *    values the user's permissions (A bitwise combination of READ_ACCESS,
	 *    WRITE_ACCESS and PUBLISH_ACCESS) on the objects.
	 */
	private static function getPermissionsOnObjects(
			Context $context, $queryParts, $parentId=-1) {
		try {
			// Array for the return values.
			$ret = array();

			// Prepare the parent clause to us in queries.
			$pc = $parentId != -1 ? $queryParts["parentClause"] : "";

			// Status (admin/editor/member) of given user.
			$us = $context->principal->status;

			// Determine minimum access permission for given user.
			$p = 0;
			if ($us == User::STATUS_EDITOR) {
				$p = self::READ_ACCESS;
			}
			if ($us == User::STATUS_ADMIN) {
				$p = self::READ_ACCESS | self::WRITE_ACCESS
					| self::PUBLISH_ACCESS;
			}

			// Get the object ids from the database.
			$sth = $context->connection->prepare(
				$queryParts["listIds"] . $pc);

			$context->connection->bindInstance($sth);
			if ($parentId != -1) {
				$sth->bindValue(":parentId", $parentId, \PDO::PARAM_INT);
			}

			$sth->execute();

			// Fill the result array with the object id as keys and the
			// minimum access permission for the values.
			while ($id = $sth->fetchColumn()) {
				$ret[$id] = $p;
			}

			// For admin users we're finished, more work for others.
			if ($us != User::STATUS_ADMIN) {

				// Select the object ids of objects with object roles that
				// match the user roles.
				$sth = $context->connection->prepare(
					$queryParts["listIdsForRoles"] . $pc .
						" GROUP BY id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(
					":userId", $context->principal->id, \PDO::PARAM_INT);
				$sth->bindValue(":type", $us == User::STATUS_MEMBER ?
					Role::PUBLIC_ROLE : Role::EDITOR_ROLE, \PDO::PARAM_INT);
				if ($parentId != -1) {
					$sth->bindValue(":parentId", $parentId, \PDO::PARAM_INT);
				}

				$sth->execute();

				// Add additional permissions depending on the values retrieved
				// from the query.
				while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
					$p2 = $p ? $ret[$rd["id"]] : 0;
					if ($us == User::STATUS_EDITOR) {
						$p2 |= self::WRITE_ACCESS;
						if ($rd["is_publisher"]) {
							$p2 |= self::PUBLISH_ACCESS;
						}
					} else if ($us = User::STATUS_MEMBER) {
						$p2 |= self::READ_ACCESS;
					}
					$ret[$rd["id"]] = $p2;
				}
			}

			return $ret;

		} catch(\PDOException $e) {
			throw new ResourceException($e);
		}
	}

	/**
	 * Get the permissions of a user on a set of pages.
	 *
	 * You can either get the permissions of the user on all pages or retrieve
	 * them for all pages directly underneath (not recursive) the page that is
	 * identified by the optional parent id.
	 *
	 * @deprecated Access to pages and assets should be checked through role
	 *   mapping.
	 *
	 * @param Context $context A connection to a Scrivo database.
	 * @param int $parentId An optional parent id to make a subselection of
	 *    pages.
	 *
	 * @return int[] Array in which the keys are the object ids and the
	 *    values the user's permissions (A bitwise combination of READ_ACCESS,
	 *    WRITE_ACCESS and PUBLISH_ACCESS) on the objects.
	 */
	public static function getPermissionsOnPages(Context $context, $parentId=-1) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);

		$sql = array(
			"listIds" =>
				"SELECT DISTINCT page_id id
				FROM page D WHERE D.instance_id = :instId AND version <= 0",
			"parentClause" =>
				" AND D.parent_id = :parentId",
			"listIdsForRoles" =>
				"SELECT DR.page_id id, MAX(is_publisher) is_publisher
				FROM role R, user_role UR, object_role DR, page D
				WHERE D.instance_id = :instId AND UR.instance_id = :instId
				AND DR.instance_id = :instId AND R.instance_id = :instId
				AND R.role_id = UR.role_id AND R.role_id = DR.role_id
				AND DR.page_id = D.page_id AND R.type = :type
				AND UR.user_id = :userId AND D.version <= 0"
		);

		return self::getPermissionsOnObjects($context, $sql, $parentId);
	}

	/**
	 * Get the permissions of a user on a set of assets.
	 *
	 * You can either get the permissions of the user on all assets or retrieve
	 * them for all assets in a folder (not recursive) that is identified by
	 * the optional parent id.
	 *
	 * @deprecated Access to pages and assets should be checked through role
	 *   mapping.
	 *
	 * @param Context $context A connection to a Scrivo database.
	 * @param int $parentId An optional parent id to make a subselection of
	 *    pages.
	 *
	 * @return int[] Array in which the keys are the object ids and the
	 *    values the user's permissions (A bitwise combination of READ_ACCESS,
	 *    WRITE_ACCESS and PUBLISH_ACCESS) on the objects.
	 */
	public static function getPermissionsOnAssets(Context $context, $parentId=-1) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);

		$sql = array(
			"listIds" =>
				"SELECT DISTINCT asset_id id
				FROM asset D WHERE D.instance_id = :instId",
			"parentClause" =>
				" AND D.parent_id = :parentId",
			"listIdsForRoles" =>
				"SELECT DR.page_id id, MAX(is_publisher) is_publisher
				FROM role R, user_role UR, object_role DR, asset D
				WHERE UR.instance_id = :instId AND DR.instance_id = :instId
				AND R.instance_id = :instId AND D.instance_id = :instId
				AND R.role_id = UR.role_id AND R.role_id = DR.role_id
				AND DR.page_id = D.asset_id AND R.type = :type
				AND UR.user_id = :userId"
		);

		return self::getPermissionsOnObjects($context, $sql, $parentId);
	}

}

?>