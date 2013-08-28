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
 * $Id: File.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Asset class.
 */

namespace Scrivo;

/**
 */
class File extends Asset {

	/**
	 * The size in bytes of the asset.
	 * @var int
	 */
	private $size = 0;

	/**
	 * The asset location.
	 * @var \Scrivo\String
	 */
	private $location = null;

	/**
	 * The asset mimeType.
	 * @var \Scrivo\String
	 */
	private $mimeType = null;

	/**
	 * The date/time this asset need to go online.
	 * @var \DateTime
	 */
	private $dateOnline = null;

	/**
	 * The date/time this asset need to go offline.
	 * @var \DateTime
	 */
	private $dateOffline = null;

	/**
	 * Create an empty asset object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {

			parent::__construct($context);

			$this->size = 0;
			$this->location = new \Scrivo\String();
			$this->mimeType = new \Scrivo\String();
			$this->dateOnline = new \DateTime("now");
			$this->dateOffline = null;
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
			case "size": return $this->size;
			case "location": return $this->location;
			case "mimeType": return $this->mimeType;
			case "dateOnline": return $this->dateOnline;
			case "dateOffline": return $this->dateOffline;
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
			case "size": $this->setSize($value); return;
			case "location": $this->setLocation($value); return;
			case "mimeType": $this->setMimeType($value); return;
			case "dateOnline": $this->setDateOnline($value); return;
			case "dateOffline": $this->setDateOffline($value); return;
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

		$this->size = intval($rd["size"]);
		$this->location = new \Scrivo\String($rd["location"]);
		$this->mimeType = new \Scrivo\String($rd["mime_type"]);
		$this->dateOnline = new \DateTime($rd["date_online"]);
		$this->dateOffline = $rd["date_offline"] == null
			? null : new \DateTime($rd["date_offline"]);
	}

	/**
	 * Set the id of the asset template.
	 *
	 * @param int $size The id ot the asset template.
	 */
	private function setSize($size) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		$this->size = $size;
	}

	/**
	 * Set the asset location (&lt;location&gt;).
	 *
	 * @param \Scrivo\String $location The asset location
	 *   (&lt;location&gt;).
	 */
	private function setLocation(\Scrivo\String $location) {
		$this->location = $location;
	}

	/**
	 * Set the mimeType for this asset (&lt;mimeType&gt;).
	 *
	 * @param \Scrivo\String $mimeType The mimeType for this asset
	 *   (&lt;mimeType&gt;).
	 */
	private function setMimeType(\Scrivo\String $mimeType) {
		$this->mimeType = $mimeType;
	}

	/**
	 * Set the date/time this asset needs to go online.
	 *
	 * @param \DateTime $dateOnline The date/time this asset needs to go online.
	 */
	private function setDateOnline(\DateTime $dateOnline) {
		$this->dateOnline = $dateOnline;
	}

	/**
	 * Set the date/time this asset need to go offline.
	 *
	 * @param \DateTime $dateOffline The date/time this asset need to go offline.
	 */
	private function setDateOffline(\DateTime $dateOffline=null) {
		$this->dateOffline = $dateOffline;
	}

	/**
	 * Get a unique filename within the scope of the selected directory.
	 */
	private function getUniqueName() {
	
		$base = $this->title;
		$ext = "";
		$pos = $this->title->lastIndexOf(new String("."));
		
		if ($pos !== -1) {
			$base = $this->title->substr(0, $pos);
			if (!$base[$base->length - 1]->equals(new String("_"))) {
				$base = "{$base}_";
			}
			$ext = $this->title->substr($pos);
		}
		
		$index = 1;
		while (!$this->checkUnique()) {
			$this->title = new String($base.$index.$ext);
			$index++;
		}
		
	}	

	/**
	 * Test if the asset title is unique within the scope of the directory.
	 * @return boolean
	 */
	private function checkUnique() {
	
		$sth = $this->context->connection->prepare(
			"SELECT COUNT(*) cnt FROM asset WHERE
				instance_id = :instId AND
				parent_id = :parentId AND title = :title");
		
		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":parentId", $this->parentId, \PDO::PARAM_INT);
		$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
		
		$sth->execute();
		
		if (intval($sth->fetchColumn(0)) == 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Check if the asset data can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible,
	 *   one or more of the fields contain invalid data or some other business
	 *   rule is not met.
	 */
	private function validateInsert() {

		if (!$this->parentId) {
			throw new \Scrivo\SystemException(
				"Files can't be created whithout folder");
		}

		$this->context->checkPermission(
			\Scrivo\AccessController::WRITE_ACCESS, $this->parentId);
		
		$this->getUniqueName();
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
					1, :size, now(), now(), :dateOnline, :dateOffline,
					:title, :location, :mimeType
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":parentId", $this->parentId, \PDO::PARAM_INT);
			$sth->bindValue(":size", $this->size, \PDO::PARAM_INT);
			$sth->bindValue(":dateOnline",
				$this->dateOnline->format("Y-m-d H:i:s"), \PDO::PARAM_STR);
			$sth->bindValue(":dateOffline", $this->dateOffline
				? $this->dateOffline->format("Y-m-d H:i:s")
				: null, \PDO::PARAM_STR);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":location", $this->location, \PDO::PARAM_STR);
			$sth->bindValue(":mimeType", $this->mimeType, \PDO::PARAM_STR);

			$sth->execute();

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
				"Can't move a asset underneath itself");
		}

		$this->getUniqueName();
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
					parent_id = :parentId, type = 1, size = :size,
					date_online = :dateOnline, date_offline = :dateOffline,
					date_modified = now(), title = :title, location = :location,
					mime_type = :mimeType
				WHERE instance_id = :instId AND asset_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(":parentId", $this->parentId, \PDO::PARAM_INT);
			$sth->bindValue(":size", $this->size, \PDO::PARAM_INT);
			$sth->bindValue(":dateOnline",
				$this->dateOnline->format("Y-m-d H:i:s"), \PDO::PARAM_STR);
			$sth->bindValue(":dateOffline", $this->dateOffline
				? $this->dateOffline->format("Y-m-d H:i:s")
				: null, \PDO::PARAM_STR);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":location", $this->location, \PDO::PARAM_STR);
			$sth->bindValue(":mimeType", $this->mimeType, \PDO::PARAM_STR);

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

	}

}

?>