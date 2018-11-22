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
 * $Id: Asset.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Asset class.
 */

namespace Scrivo;

/**
 */
abstract class Asset {

	/**
	 * Value indicating that the asset is a folder.
	 */
	const TYPE_FOLDER = 0;

	/**
	 * Value indicating that the asset is a file.
	 */
	const TYPE_FILE = 1;

	/**
	 * The asset id (DB key).
	 * @var int
	 */
	protected $id = 0;

	/**
	 * The id of the parent asset.
	 * @var int
	 */
	protected $parentId = 0;

	/**
	 * The asset type: one out of the Asset::TYPE_* constant values.
	 * @var int
	 */
	protected $type = 0;

	/**
	 * The asset title.
	 * @var \Scrivo\Str
	 */
	protected $title = null;

	/**
	 * The date/time that this asset was created.
	 * @var \DateTime
	 */
	protected $dateCreated = null;

	/**
	 * The last date/time that this asset was modified.
	 * @var \DateTime
	 */
	protected $dateModified = null;

	/**
	 * The child assets of this asset.
	 * @var \Scrivo\AssetSet
	 */
	protected $children = null;

	/**
	 * The parent assets of this asset.
	 * @var \Scrivo\AssetSet
	 */
	protected $path = null;

	/**
	 * The attached roles.
	 * @var \Scrivo\RoleSet
	 */
	protected $roles = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	protected $context = null;

	/**
	 * Create an empty asset object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->id = 0;
			$this->parentId = 0;
			$this->type = 0;
			$this->title = new \Scrivo\Str();
			$this->dateCreated = new \DateTime("now");
			$this->dateModified = new \DateTime("now");

			$this->roles = new \Scrivo\RoleSet();

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
			case "parentId": return $this->parentId;
			case "type": return $this->type;
			case "title": return $this->title;
			case "dateCreated": return $this->dateCreated;
			case "dateModified": return $this->dateModified;
			case "children": return $this->getChildren();
			case "path": return $this->getPath();
			case "roles": return $this->roles;
			case "context": return $this->context;
		}
		throw new \Scrivo\SystemException("No such property-get '$name'.");
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
			case "parentId": $this->setParentId($value); return;
			case "type": $this->setType($value); return;
			case "title": $this->setTitle($value); return;
			case "context": $this->setContext($value); return;
		}
		throw new \Scrivo\SystemException("No such property-set '$name'.");
	}

	/**
	 * Convenience method to set the fields of a asset definition object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	protected function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["asset_id"]);
		$this->parentId = intval($rd["parent_id"]);
		$this->type = intval($rd["type"]);
		$this->title = new \Scrivo\Str($rd["title"]);
		$this->dateCreated = new \DateTime($rd["date_created"]);
		$this->dateModified = new \DateTime($rd["date_modified"]);

		$this->context = $context;
	}

	/**
	 * Get the child assets of this asset.
	 *
	 * @return \Scrivo\AssetSet The child assets of the asset.
	 */
	private function getChildren() {
		if ($this->children === null) {
			$this->children = self::selectChildren($this);
			$this->context->cache[$this->id] = $this;
		}
		return $this->children;
	}

	/**
	 * Get the child assets of this asset.
	 *
	 * @return \Scrivo\AssetSet All assets above the current asset.
	 */
	private function getPath() {
		if ($this->path === null) {
			$this->path = self::selectPath($this);
			$this->context->cache[$this->id] = $this;
		}
		return $this->path;
	}

	/**
	 * Set the id of the parent asset.
	 *
	 * @param int $parentId The id of the parent asset.
	 */
	private function setParentId($parentId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		$this->parentId = $parentId;
	}

	/**
	 * Set the asset type: one out of the Asset::TYPE_* constant values.
	 *
	 * @param int $type The asset type: one out of the Asset::TYPE_* constant
	 *    values.
	 */
	private function setType($type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER, array(
				self::TYPE_FILE, self::TYPE_FOLDER))
		));
		$this->type = $type;
	}

	/**
	 * Set The asset title (&lt;title&gt;).
	 *
	 * @param \Scrivo\Str $title The asset title (&lt;title&gt;).
	 */
	private function setTitle(\Scrivo\Str $title) {
		$this->title = $title;
	}

	/**
	 * Set the asset context.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	private function setContext(\Scrivo\Context $context) {
		$this->context = $context;
	}

	/**
	 * Select the roles for this asset.
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 * @param array $assets the set of assets for which to retrieve the
	 *    properties.
	 */
	private static function selectRoles(\Scrivo\Context $context, array $assets) {

		$ids = implode(",", array_keys($assets));

		$sth = $context->connection->prepare(
			"SELECT page_id, role_id FROM object_role
			WHERE instance_id = :instId AND page_id in ($ids)");

		$context->connection->bindInstance($sth);

		$sth->execute();

		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

			$assets[intval($rd["page_id"])]->roles[] =
				intval($rd["role_id"]);

		}
	}

	/**
	 * Move a asset one position up or down amongst its siblings.
	 *
	 * @param int $dir Direction of the move, see \Scrivo\SequenceNo:::MOVE_*
	 */
	function move($dir=\Scrivo\SequenceNo::MOVE_DOWN) {

		$this->context->checkPermission(
			\Scrivo\AccessController::WRITE_ACCESS, $this->id);

		\Scrivo\SequenceNo::position($this->context, "asset",
			"parent_id", $this->id, $dir);

		unset($this->context->cache[$this->parentId]);

	}


	/**
	 * Delete an existing asset from the database.
	 *
	 * First it is is checked if it's possible to delete this asset
	 * then the asset data including its dependecies is deleted from
	 * the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The id of the asset to delete.
	 *
	 * @throws \Scrivo\ApplicationException If it is not possible to delete
	 *   this asset.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			static::validateDelete($context, $id);

			$p = \Scrivo\Asset::fetch($context, $id);

			foreach (array("object_role" => "page_id",
					"id_label" => "id",
					"asset" => "asset_id") as $table => $keyFld) {

				$sth = $context->connection->prepare(
					"DELETE FROM $table
					WHERE instance_id = :instId AND $keyFld = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();
			}

			unset($context->cache[$id]);
			unset($context->cache[$p->parentId]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Retrieve a asset from the database or cache.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id An object id of a asset.
	 *
	 * @throws \Scrivo\ApplicationException if the asset was not readable for
	 *   the user defined in the context.
	 */
	public static function fetch(\Scrivo\Context $context, $id=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);
		try {
			// Try to retieve form cache
			$a = null;
			if (isset($context->cache[$id])) {
				// Set the asset from cache and set the context.
				$a = $context->cache[$id];
				$a->context = $context;
			} else {

				$sth = $context->connection->prepare(
					"SELECT asset_id, parent_id, type, size,
						date_created, date_modified, date_online, date_offline,
						title, location, mime_type
					FROM asset
					WHERE instance_id = :instId AND asset_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException("Failed to load asset");
				}

				$rd = $sth->fetch(\PDO::FETCH_ASSOC);

				$a = intval($rd["type"]) ?
					new \Scrivo\File() : new \Scrivo\Folder();
				$a->setFields($context, $rd);

				$a->roles = new \Scrivo\RoleSet();
				self::selectRoles($a->context, array($a->type
					? $a->parentId : $a->id => $a));

				$context->cache[$id] = $a;
			}

			$a->roles->checkReadPermission($context->principal);
			return $a;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select child assets from the database.
	 *
	 * @param \Scrivo\Asset $asset A Scrivo asset.
	 *
	 * @return \Scrivo\AssetSet An array containing the selected assets.
	 */
	private static function selectChildren(\Scrivo\Asset $asset) {
		try {
			$sth = $asset->context->connection->prepare(
				"SELECT A.asset_id, A.parent_id, A.type, A.size,
					A.date_created, A.date_modified, A.date_online, A.date_offline,
					A.title, A.location, A.mime_type, R.role_id
				FROM asset A LEFT JOIN object_role R ON
					(A.instance_id = R.instance_id AND
					IF(A.type=0, A.asset_id, A.parent_id) = R.page_id)
				WHERE A.instance_id = :instId
					AND A.parent_id = :parentId
				ORDER BY sequence_no");

			$asset->context->connection->bindInstance($sth);
			$sth->bindValue(":parentId", $asset->id, \PDO::PARAM_INT);

			$sth->execute();
			$res = new \Scrivo\AssetSet($asset);
			$a = null;
			$lid = 0;
			$id = 0;

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$id = intval($rd["asset_id"]);

				if ($lid != $id) {

					if ($lid !== 0) {
						$asset->context->cache[$lid] = $a;
						$res[$lid] = $a;
					}
					$lid = $id;

					$a = intval($rd["type"]) ?
						new \Scrivo\File() : new \Scrivo\Folder();
					$a->setFields($asset->context, $rd);
					$a->roles = new \Scrivo\RoleSet();
				}

				// Add the roles to the role set
				$a->roles[] = intval($rd["role_id"]);
			}

			if ($id) {
				$asset->context->cache[$id] = $a;
				$res[$id] = $a;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select the asset path.
	 *
	 * @param \Scrivo\Asset $asset A Scrivo asset.
	 *
	 * @return \Scrivo\AssetSet An array containing the selected assets.
	 */
	protected static function selectPath(\Scrivo\Asset $asset) {
		try {

			$res = new \Scrivo\AssetSet($asset);
			$target = $asset->parentId;

			$i = 0;
			while ($target) {

				if ($target == $asset->id) {
					throw new \Scrivo\SystemException("Path loop");
				}

				if (isset($asset->context->cache[$target])) {

					$a = $asset->context->cache[$target];

				} else {

					$sth = $asset->context->connection->prepare(
						"SELECT A.asset_id, A.parent_id, A.type, A.size,
							A.date_created, A.date_modified, A.date_online, A.date_offline,
							A.title, A.location, A.mime_type, R.role_id
						FROM asset A LEFT JOIN object_role R ON
							(A.instance_id = R.instance_id AND
							IF(A.type=0, A.asset_id, A.parent_id) =
								R.page_id)
						WHERE A.instance_id = :instId
							AND A.asset_id = :parentId");

					$asset->context->connection->bindInstance($sth);
					$sth->bindValue(":parentId", $target, \PDO::PARAM_INT);

					$sth->execute();
					$a = null;

					while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

						if (!$a) {
							$a = intval($rd["type"]) ?
								new \Scrivo\File() : new \Scrivo\Folder();
							$a->setFields($asset->context, $rd);
							$a->roles = new \Scrivo\RoleSet();
							$target = intval($rd["asset_id"]);
						}

						// Add the roles to the role set
						$a->roles[] = intval($rd["role_id"]);
					}

					if ($a) {
						$asset->context->cache[$a->id] = $a;
					} else {
						throw new \Scrivo\SystemException(
							"Failed to load asset");
					}

				}

				$res->prepend($a);

				$target = $a->parentId;

			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>