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
 * $Id: ApplicationDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ApplicationDefinition class.
 */

namespace Scrivo;

/**
 * The ApplicationDefinition class is used to create references to applications.
 *
 * Scrivo applications are (small) specialized web applications that are hosted
 * on a tab in the user interface. These can be internal applications such
 * as list or form, or any custom made web application.
 *
 * The application definition class holds the entry point (an url to display
 * in an iframe or a Javascript class constructor) for the application.
 *
 * @property-read int $id The application definition id (DB key)
 * @property \Scrivo\String $description An additional description for the
 *    application.
 * @property \Scrivo\String $location The location of the application start
 *    page.
 * @property \Scrivo\String $title A descriptive title for the application.
 * @property int $type The application type: one out of the
 *    ApplicationDefinition::TYPE_* constants.
 */
class ApplicationDefinition {


	/**
	 * Constant to denote an external (custom) application.
	 */
	const TYPE_URL = 1;

	/**
	 * Constant to denote a news-like list application (manually sortable).
	 */
	const TYPE_LIST = 2;

	/**
	 * Constant to denote a column list application with sort option on headers.
	 */
	const TYPE_LISTVIEW = 3;

	/**
	 * Constant to denote a from application.
	 */
	const TYPE_FORM = 4;

	/**
	 * Constant to denote distributed list.
	 */
	const TYPE_DISTRIBUTED_LIST = 5;

	/**
	 * The application definition id (DB key)
	 * @var int
	 */
	private $id = 0;

	/**
	 * A descriptive title for the application.
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * An additional description for the application.
	 * @var \Scrivo\String
	 */
	private $description = null;

	/**
	 * The location of the application start page.
	 * @var \Scrivo\String
	 */
	private $location = null;

	/**
	 * The application type: one out of the
	 * ApplicationDefinition::TYPE_* constants.
	 * @var int
	 */
	private $type = self::TYPE_URL;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty application definition object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->title = new \Scrivo\String();
			$this->description = new \Scrivo\String();
			$this->location = new \Scrivo\String();

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
			case "title": return $this->title;
			case "description": return $this->description;
			case "location": return $this->location;
			case "type": return $this->type;
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
			case "title": $this->setTitle($value); return;
			case "description": $this->setDescription($value); return;
			case "location": $this->setLocation($value); return;
			case "type": $this->setType($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a application definition object
	 * from an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["application_definition_id"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->description = new \Scrivo\String($rd["description"]);
		$this->location = new \Scrivo\String($rd["action"]);
		$this->type = intval($rd["type"]);

		$this->context = $context;
	}

	/**
	 * Set the descriptive title for the application.
	 *
	 * @param \Scrivo\String $title A descriptive title for the application.
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set the additional description for the application.
	 *
	 * @param \Scrivo\String $description An additional description for the
	 *    application.
	 */
	private function setDescription(\Scrivo\String $description) {
		$this->description = $description;
	}

	/**
	 * Set the location of the application start page.
	 *
	 * @param \Scrivo\String $location The location of the application start
	 *    page.
	 */
	private function setLocation(\Scrivo\String $location) {
		$this->location = $location;
	}

	/**
	 * Set the application type: one out of the
	 * ApplicationDefinition::TYPE_* constants.
	 *
	 * @param int $type The application type: one out of the
	 *    ApplicationDefinition::TYPE_* constants.
	 */
	private function setType($type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER, array(
				self::TYPE_URL, self::TYPE_LIST, self::TYPE_LISTVIEW,
				self::TYPE_FORM, self::TYPE_DISTRIBUTED_LIST))
		));

		$this->type = $type;
	}

	/**
	 * Check if this application definition object can be inserted into the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new application definition object data into the database.
	 *
	 * First it is checked if the data of this application definition object
	 * can be inserted into the database, then the data is inserted into the
	 * database. If no id was set a new object id is generated.
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
				"INSERT INTO application_definition (
					instance_id, application_definition_id, title, description, action, type
				) VALUES (
					:instId, :id, :title, :description, :location, :type
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);
			$sth->bindValue(":location", $this->location, \PDO::PARAM_STR);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_INT);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this application definition object can be updated in the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing application definition object data in the database.
	 *
	 * First it is checked if the data of this application definition object
	 * can be updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE application_definition SET
					title = :title, description = :description,
					action = :location, type = :type
				WHERE instance_id = :instId AND application_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);
			$sth->bindValue(":location", $this->location, \PDO::PARAM_STR);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_INT);

			$sth->execute();

			unset($this->context->cache[$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of application definition object data does not violate
	 * any business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the application definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing application definition data from the database.
	 *
	 * First it is is checked if it's possible to delete application
	 * definition data, then the application definition data including
	 * its dependencies is deleted from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the application definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the application definition data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {

			self::validateDelete($context, $id);

			$sth = $context->connection->prepare(
				"DELETE FROM application_definition
				WHERE instance_id = :instId AND application_definition_id = :id");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			unset($context->cache[$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Fetch a application definition object from the database using its
	 * object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the application definition to select.
	 *
	 * @return \Scrivo\ApplicationDefinition The requested application
	 *    definition object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the application definition from the cache ...
			if (isset($context->cache[$id])) {
				// ... get it from the cache and set the context.
				$applicationDefintition = $context->cache[$id];
				$applicationDefintition->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT application_definition_id, title, description, action, type
					FROM application_definition
					WHERE instance_id = :instId AND application_definition_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException(
						"Failed to load application definition");
				}

				$applicationDefintition =
					new \Scrivo\ApplicationDefinition();
				$applicationDefintition->setFields(
					$context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache[$id] = $applicationDefintition;
			}

			return $applicationDefintition;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select application definitions from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 *
	 * @return \Scrivo\ApplicationDefinition[id] An array containing the
	 *    selected application definitions.
	 */
	public static function select(\Scrivo\Context $context) {
		try {
			$sth = $context->connection->prepare(
				"SELECT application_definition_id, title, description, action, type
				FROM application_definition
				WHERE instance_id = :instId
				ORDER BY title");

			$context->connection->bindInstance($sth);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new \Scrivo\ApplicationDefinition();
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