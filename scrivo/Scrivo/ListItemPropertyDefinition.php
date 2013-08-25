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
 * $Id: ListItemPropertyDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ListItemPropertyDefinition class.
 */

namespace Scrivo;

/**
 * Class to hold the property definitions for list item properties. It holds
 * the information to propery instantiate a list item property.
 *
 * These properties are all derived from the ListItemProperty the class. The
 * latter accounts for the persistent storage of the actual property data.
 * But when an list item property is instantiated it should be in the form
 * of a 'typed' list item property (such as image, url). The list item
 * property defintion determines the type property and may hold addition
 * configuration data.
 *
 * @property-read int $id The list item property defintion id (DB key).
 * @property int $listItemDefinitionId The id of the list item definition
 *    where this list item property defintion belongs to.
 * @property int $applicationDefinitionId The id of the list/application
 *    definition where this list item property defintion belongs to.
 * @property boolean $inList Setting to show or hide this property in item
 *    lists in the user interface.
 * @property \Scrivo\String $phpSelector A textual identification/key for this
 *    list item property.
 * @property \Scrivo\String $title A descriptive name for the list item
 *    property.
 * @property \Scrivo\String $type The list item property type, one out of
 *    ListItemProperty::TYPE_* constants.
 * @property \stdClass $typeData Optional data needed to for this specific
 *    property type.
 */
class ListItemPropertyDefinition {

	/**
	 * Value indicating an img property.
	 */
	const TYPE_IMAGE = "img";

	/**
	 * Value indicating an input field property.
	 */
	const TYPE_INPUT = "input";

	/**
	 * Value indicating a select list property.
	 */
	const TYPE_SELECT = "select";

	/**
	 * Value indicating a color property.
	 */
	const TYPE_COLOR = "color";

	/**
	 * Value indicating a url property.
	 */
	const TYPE_URL = "url";

	/**
	 * Value indicating a checkbox property.
	 */
	const TYPE_CHECKBOX = "checkbox";

	/**
	 * Value indicating a html textarea property.
	 */
	const TYPE_HTML_TEXT = "html_text";

	/**
	 * Value indicating a textarea property.
	 */
	const TYPE_TEXT = "text";

	/**
	 * Value indicating a date property.
	 */
	const TYPE_DATE_TIME = "datetime";

	/**
	 * Value indicating a tab in the user interface.
	 */
	const TYPE_TAB = "tab";

	/**
	 * Value indicating an informative text.
	 */
	const TYPE_INFO = "info";

	/**
	 * The list item property defintion id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * The id of the list/application definition where this list item property
	 * defintion belongs to.
	 * @var int
	 */
	private $applicationDefinitionId = 0;

	/**
	 * The id of the list item definition where this list item property
	 * defintion belongs to.
	 * @var int
	 */
	private $listItemDefinitionId = 0;

	/**
	 * The sequence number of the list item definition defintion.
	 * @var int
	 */
	private $sequenceNo = 0;

	/**
	 * The list item property type, one out of ListItemProperty::TYPE_*
	 * constants.
	 * @var \Scrivo\String
	 */
	private $type = "";

	/**
	 * Optional data needed to for this specific property type.
	 * @var \stdClass
	 */
	private $typeData = null;

	/**
	 * A textual identification/key for this list item property.
	 * @var \Scrivo\String
	 */
	private $phpSelector = null;

	/**
	 * A descriptive name for the list item property.
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * Setting to show or hide this property in item lists in the user
	 * interface.
	 * @var boolean
	 */
	private $inList = false;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty list item property definiton object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->phpSelector = new \Scrivo\String();
			$this->title = new \Scrivo\String();
			$this->typeData = new \stdClass;

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
			case "applicationDefinitionId":
				return $this->applicationDefinitionId;
			case "listItemDefinitionId": return $this->listItemDefinitionId;
			case "type": return $this->type;
			case "typeData": return $this->typeData;
			case "phpSelector": return $this->phpSelector;
			case "title": return $this->title;
			case "inList": return $this->inList;
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
			case "applicationDefinitionId":
				$this->setApplicationDefinitionId($value); return;
			case "listItemDefinitionId":
				$this->setListItemDefinitionId($value); return;
			case "type": $this->setType($value); return;
			case "typeData": $this->setTypeData($value); return;
			case "phpSelector": $this->setPhpSelector($value); return;
			case "title": $this->setTitle($value); return;
			case "inList": $this->setInList($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a list item property definiton
	 * object from an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["list_item_property_definition_id"]);
		$this->applicationDefinitionId = intval($rd["application_definition_id"]);
		$this->listItemDefinitionId = intval($rd["list_item_definition_id"]);
		$this->type = (string)$rd["type"];
		$this->setTypeDataFromRS($rd);
		$this->phpSelector = new \Scrivo\String($rd["php_key"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->inList = intval($rd["in_list"]) == 1 ? true : false;

		$this->context = $context;
	}

	/**
	 * Get an array of the possible property types.
	 *
	 * @return \Scrivo\String[] An array of all possible property types.
	 */
	public static function getTypes() {
		return array(
			self::TYPE_IMAGE,
			self::TYPE_INPUT,
			self::TYPE_SELECT,
			self::TYPE_COLOR,
			self::TYPE_URL,
			self::TYPE_CHECKBOX,
			self::TYPE_HTML_TEXT,
			self::TYPE_TEXT,
			self::TYPE_DATE_TIME,
			self::TYPE_TAB,
			self::TYPE_INFO
		);
	}

	/**
	 * Set the id of the list/application definition where this list item property
	 * belongs to. It is only possible to set the application id for
	 * uninitialized list item properties.
	 *
	 * @param int $appId The id of the list/application definition where this list item
	 *    property belongs to.
	 */
	public function setApplicationDefinitionId($appId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		if ($this->applicationDefinitionId) {
			throw new \Scrivo\SystemException(
				"Can't reset the the application id");
		}
		return $this->applicationDefinitionId = $appId;
	}

	/**
	 * Set the id of the list item definition where this list item property
	 * belongs to.
	 *
	 * @param int $defId The id of the list/application definition where this
	 *    list item property belongs to.
	 */
	private function setListItemDefinitionId($defId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		return $this->listItemDefinitionId = $defId;
	}

	/**
	 * Set The list item property's type, one out of
	 * ListItemPropertyDefinition::TYPE_* constants.
	 *
	 * @param string $type The list item property's type, one out of
	 *    ListItemPropertyDefinition::TYPE_* constants.
	 */
	private function setType($type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_STRING, $this->getTypes())
		));

		$this->type = $type;
	}

	/**
	 * Set Optional data needed to for this specific property type.
	 *
	 * @param \stdClass $typeData Optional data needed to for this specific
	 *    property type.
	 */
	private function setTypeData(\stdClass $typeData) {
		$this->typeData = $typeData;
	}

	/**
	 * Set A textual identification/key for this list item property.
	 *
	 * @param \Scrivo\String $phpSelector A textual identification/key for
	 *    this list item property.
	 */
	private function setPhpSelector(\Scrivo\String $phpSelector) {
		$this->phpSelector = $phpSelector;
	}

	/**
	 * Set A descriptive name for the list item property.
	 *
	 * @param \Scrivo\String $title A descriptive name for the list item
	 *    property.
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set Setting to show or hide this property in item lists in the user
	 * interface.
	 *
	 * @param boolean $inList Setting to show or hide this property in item
	 *    lists in the user interface.
	 */
	private function setInList($inList) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_BOOLEAN)
		));

		$this->inList = $inList;
	}

	/**
	 * Convert a string to its most likely type.
	 *
	 * @param string $val The value to convert to either an int, float or
	 *    \Scrivo\String.
	 *
	 * @return int|float|\Scrivo\String The given value converted to its
	 *    most likely type.
	 */
	private function readStr($val) {
		$str = (string)$val;
		if (is_numeric($str)) {
			if ((string)$str === (string)(int)$str) {
				return intval($str);
			}
			return floatval($str);
		}
		return $val;
	}

	/**
	 * Convert plain text type data to an object.
	 *
	 * @param array $rs The type data as stored in the database (result set
	 *    row).
	 */
	private function setTypeDataFromRS(array $rs) {
		$this->setTypeDataAsString(new \Scrivo\String($rs["type_data"]));
	}

	/**
	 * Create a string representation of the type data member.
	 *
	 * @return \Scrivo\String The type data as an string.
	 */
	private function getTypeDataAsString() {
		$d = array();
		foreach($this->typeData as $k=>$v) {
			$d[] = $k."=".$v;
		};
		return new \Scrivo\String(implode("\n", $d));
	}

	/**
	 * Set the type data member form a string representation. The format of
	 * the string should be NAME1=VALUE1\nNAME2=VALUE2\nNAME3...etc.
	 *
	 * @param \Scrivo\String $str The type data string.
	 */
	private function setTypeDataAsString(\Scrivo\String $str) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		$d = array();
		$parts = $str->split(new \Scrivo\String("\n"));
		foreach($parts as $line) {
			$p = $line->split(new \Scrivo\String("="), 2);
			if (count($p) == 2) {
				$d[(string)$p[0]->trim()] = $this->readStr($p[1]->trim());
			}
		}
		$this->typeData = (object)$d;
	}

	/**
	 * Check if this list item property definiton object can be inserted into
	 * the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new list item property definiton object data into the database.
	 *
	 * First it is checked if the data of this list item property definiton
	 * object can be inserted into the database, then the data is inserted into
	 * the database. If no id was set a new object id is generated.
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
				"INSERT INTO list_item_property_definition (
					instance_id, list_item_property_definition_id, application_definition_id,
					list_item_definition_id, sequence_no, type, type_data,
					php_key, title, in_list
				) VALUES (
					:instId, :id, :appDefId, :listItemDefId,
					0, :type, :typeData,
					:phpSelector, :title, :inList
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(
				":appDefId", $this->applicationDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(
				":listItemDefId", $this->listItemDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_STR);
			$sth->bindValue(
				":typeData", $this->getTypeDataAsString(), \PDO::PARAM_STR);
			$sth->bindValue(
				":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":inList", $this->inList, \PDO::PARAM_INT);

			$sth->execute();

			\Scrivo\SequenceNo::position($this->context, "list_item_property_definition",
				"list_item_definition_id", $this->id, \Scrivo\SequenceNo::MOVE_LAST);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this list item property definiton object can be updated in
	 * the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing list item property definiton object data in the database.
	 *
	 * First it is checked if the data of this list item property definiton
	 * object can be updated in the database, then the data is updated in the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE list_item_property_definition SET
					application_definition_id = :appDefId, list_item_definition_id = :listItemDefId,
					type = :type,
					type_data = :typeData, php_key = :phpSelector,
					title = :title, in_list = :inList
				WHERE instance_id = :instId AND list_item_property_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(
				":appDefId", $this->applicationDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(
				":listItemDefId", $this->listItemDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_STR);
			$sth->bindValue(
				":typeData", $this->getTypeDataAsString(), \PDO::PARAM_STR);
			$sth->bindValue(
				":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":inList", $this->inList, \PDO::PARAM_INT);

			$sth->execute();

			unset($this->context->cache[$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of list item property definiton object data does not
	 * violate any business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the list item property definiton to
	 *    select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *    if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing list item property definiton data from the database.
	 *
	 * First it is is checked if it's possible to delete list item property
	 * definiton data, then the list item property definiton data including
	 * its dependencies is deleted from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the list item property definiton to
	 *    select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *    if it is not possible to delete the list item property definiton data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			$tmp = self::fetch($context, $id);

			$sth = $context->connection->prepare(
				"DELETE FROM list_item_property_definition
				WHERE instance_id = :instId AND list_item_property_definition_id = :id");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			unset($context->cache[$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Move a list item property one position up or down.
	 *
	 * @param int $dir Direction of the move, see \Scrivo\SequenceNo:::MOVE_*
	 */
	function move($dir=\Scrivo\SequenceNo::MOVE_DOWN) {

		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

		\Scrivo\SequenceNo::position($this->context, "list_item_property_definition",
			"list_item_definition_id", $this->id, $dir);
	}

	/**
	 * Fetch a list item property definiton object from the database using its
	 * object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the list item property definiton to
	 *    select.
	 *
	 * @return \Scrivo\ListItemPropertyDefinition The requested list item
	 *    property definiton object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the list item property definiton from cache ...
			if (isset($context->cache[$id])) {
				// ... get it from the cache and set the context.
				$listItemPropertyDefinition = $context->cache[$id];
				$listItemPropertyDefinition->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT list_item_property_definition_id, application_definition_id,
						list_item_definition_id, sequence_no, type, type_data,
						php_key, title, in_list
					FROM list_item_property_definition
					WHERE instance_id = :instId AND list_item_property_definition_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException(
						"Failed to load list item property definiton");
				}

				$listItemPropertyDefinition =
					new \Scrivo\ListItemPropertyDefinition();
				$listItemPropertyDefinition->setFields(
					$context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache[$id] = $listItemPropertyDefinition;
			}

			return $listItemPropertyDefinition;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select list item property definitons from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $applicationDefinitionId The id of the application for which
	 *    to retrieve the list item properties.
	 *
	 * @return \Scrivo\ListItemPropertyDefinition[id] An array containing
	 *    the selected list item property definitons.
	 */
	public static function select(
			\Scrivo\Context $context, $applicationDefinitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			$sth = $context->connection->prepare(
				"SELECT list_item_property_definition_id, application_definition_id, list_item_definition_id,
					sequence_no, type, type_data, php_key, title, in_list
				FROM list_item_property_definition
				WHERE instance_id = :instId AND application_definition_id = :appId
				ORDER BY list_item_definition_id, sequence_no");

			$context->connection->bindInstance($sth);
			$sth->bindValue(
				":appId", $applicationDefinitionId, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();

			$lastListItemDef = -999;

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				if ($lastListItemDef != intval($rd["list_item_definition_id"])) {
					$lastListItemDef = intval($rd["list_item_definition_id"]);
					$res[$lastListItemDef] = array();
				}

				$li = new ListItemPropertyDefinition();
				$li->setFields($context, $rd);

				$res[$lastListItemDef][$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>