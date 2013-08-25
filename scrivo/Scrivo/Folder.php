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
 * $Id: Folder.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Asset class.
 */

namespace Scrivo;

/**
 */
class Folder extends Asset {

	/**
	 * The cache headers settings for the files in this folder.
	 * @var \Scrivo\String
	 */
	private $cacheHeaderSettings = null;

	/**
	 * Create an empty asset object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {

			parent::__construct($context);

			$this->cacheHeaderSettings = new \Scrivo\String();
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
			case "cacheHeaderSettings": return $this->getCacheHeaderSettings();
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
		switch($name) {
			case "cacheHeaderSettings":
				$this->setCacheHeaderSettings($value); return;
		}
		parent::__set($name, $value);
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

		parent::setFields($context, $rd);

		$this->cacheHeaderSettings = new \Scrivo\String($rd["location"]);
	}

	/**
	 * Get the cache header settings.
	 *
	 * @return \stdClass The cache header settings data.
	 */
	private function getCacheHeaderSettings() {
		$res = new \stdClass;
		$res->setting = new \Scrivo\String("last-modified");
		if ($this->cacheHeaderSettings)
			if ($this->cacheHeaderSettings->equals(
					new \Scrivo\String("NOCACHE"))) {
			$res->setting = new \Scrivo\String("no-cache");
		} else if ($this->cacheHeaderSettings->length >= 5 &&
				$this->cacheHeaderSettings->substr(0,5)->equals(
				new \Scrivo\String("CACHE"))) {
			$dat = $this->cacheHeaderSettings->split(new \Scrivo\String(":"));
			$res->setting = new \Scrivo\String("expires");
			$res->timePeriod = intval((string)$dat[1]);
			$res->timeUnit = $dat[2];
		}
		return $res;
	}

	/**
	 * Set the cache header settings.
	 *
	 * @param \stdClass $settingData The cache header settings data.
	 */
	private function setCacheHeaderSettings(\stdClass $settingData) {
		if ($settingData->setting->equals(new \Scrivo\String("expires"))) {
			$this->cacheHeaderSettings = new \Scrivo\String(
				"CACHE:{$settingData->timePeriod}:{$settingData->timeUnit}");
		} else if ($settingData->setting->equals(
				new \Scrivo\String("no-cache"))) {
			$this->cacheHeaderSettings = new \Scrivo\String("NOCACHE");
		} else {
			$this->cacheHeaderSettings = new \Scrivo\String("");
		}
	}

	/**
	 * Check if the asset data can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible,
	 *   one or more of the fields contain invalid data or some other business
	 *   rule is not met.
	 */
	private function validateInsert() {

		if ($this->parentId) {

			$this->context->checkPermission(
				\Scrivo\AccessController::WRITE_ACCESS, $this->parentId);

		} else {

			// If we're trying to insert a new root, check if there there is
			// none yet.
			$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

			$sth = $this->context->connection->prepare(
				"SELECT COUNT(*) FROM asset
					WHERE instance_id = :instId AND parent_id = 0");

			$this->context->connection->bindInstance($sth);

			$sth->execute();

			if ($sth->fetchColumn(0) > 0) {
				throw new \Scrivo\SystemException(
					"Trying to create a new root folder");
			}

		}

	}

	/**
	 * Insert a new asset into the database.
	 *
	 * First the data fields of this asset will be validated. If no id
	 * is set a new object id is generated. Then the data is inserted into to
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If one or more of the fields
	 *   contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			if (!$this->id) {
				$this->id = $this->context->connection->generateId();
			}

			$sth = $this->context->connection->prepare(
				"INSERT INTO asset (
					instance_id, asset_id, parent_id, sequence_no,
					type, size, date_created, date_modified, date_online, date_offline,
					title, location, mime_type
				) VALUES (
					:instId, :id, :parentId, 0,
					0, 0, now(), now(), now(), null,
					:title,	:ch, ''
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":parentId", $this->parentId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":ch", $this->cacheHeaderSettings, \PDO::PARAM_STR);

			$sth->execute();

			ObjectRole::set($this->context, $this->id,
				ObjectRole::select($this->context, $this->parentId));

			unset($this->context->cache[$this->parentId]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this the asset data can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible,
	 *   one or more of the fields contain invalid data or some other business
	 *   rule is not met.
	 */
	private function validateUpdate() {

		$this->context->checkPermission(
			\Scrivo\AccessController::WRITE_ACCESS, $this->id);

		try {
			$newPath = self::selectPath($this);
		} catch (\Scrivo\SystemException $e) {
			throw new \Scrivo\ApplicationException(
				"Can't move a folder underneath itself");
		}

	}

	/**
	 * Update an existing asset in the database.
	 *
	 * First the data fields of this user will be validated, then the data
	 * is updated in to database.
	 *
	 * @throws \Scrivo\ApplicationException If one or more of the fields
	 *   contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$isParentWritable = false;
			if ($this->parentId) {
				try {
					$this->context->checkPermission(
						\Scrivo\AccessController::WRITE_ACCESS,
						$this->parentId);
					$isParentWritable = true;
				} catch (\Scrivo\ApplicationException $e) {}
			}

			$sth = $this->context->connection->prepare(
				"UPDATE asset SET
					parent_id = :parentId, title = :title, location = :ch
				WHERE instance_id = :instId AND asset_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(":parentId", $this->parentId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":ch", $this->cacheHeaderSettings, \PDO::PARAM_STR);

			$sth->execute();

			unset($this->context->cache[$this->id]);
			unset($this->context->cache[$this->parentId]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of asset object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the asset definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	protected static function validateDelete(\Scrivo\Context $context, $id) {

		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS, $id);

		// Is it a labeled asset?
		$sth = $context->connection->prepare(
			"SELECT COUNT(*) FROM id_label
			WHERE instance_id = :instId AND id = :id");

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		$sth->execute();

		if ($sth->fetchColumn(0) > 0) {
			throw new \Scrivo\ApplicationException(
				"Trying to delete a labelled asset");
		}

		// Check the child assets.
		$sth = $context->connection->prepare(
			"SELECT asset_id, type FROM asset WHERE instance_id = :instId
				AND parent_id = :id");

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		$sth->execute();

		$folders = array();
		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
			throw new \Scrivo\ApplicationException(
				"Trying to delete an asset with child assets");
		}
	}

}

?>