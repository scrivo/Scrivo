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
 * $Id: ListItemDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ListItemDefinition class.
 */

namespace Scrivo;

/**
 * Class to hold the definition of a list item. Scrivo lists contain list
 * items and each list item can have a number of configurable fields. List
 * item definitions are used to store this configuration data.
 *
 * Note that a Scrivo list is not limited to items using one list item
 * definition only, a Scrivo list can contain items that use different list
 * item definitions.
 *
 * Also note that Scrivo lists are actually defined by these list item
 * defintions: a list is a collection of items using a particular set of
 * list item definitions. This set is determined by the application definition.
 *
 * Lists can be hierachically structured (like a threaded forum for instance).
 * Therefore list item definitions can structured accordingly: ie. list items
 * using the "reply" definition can only occur underneath list items using
 * the "topic" ore "reply" item.
 *
 * @property-read int $id The id of the list item definition (DB key).
 * @property int $applicationDefinitionId The id of the list/application
 *    where this list item definition belongs to.
 * @property \Scrivo\Str $icon The location of an icon to identify the
 *    item definition in the user interface.
 * @property int $pageDefinitionId The id of the template to use for linked
 *    pages (optional).
 * @property \Scrivo\Str $phpSelector A textual identification/key for
 *    this list item definition.
 * @property \Scrivo\Str $title A descriptive name for the list item
 *    definition.
 * @property \Scrivo\Str $titleLabel An alternative label for the title
 *    property of a list item in the user interface.
 * @property int $titleWidth The width of the title property in the user
 *    interface for a list item (column width in list view mode).
 * @property array $childListItemDefinitionIds List of possible list item
 *    definitions for child items.
 * @property array $parentListItemDefinitionIds List of possible list item
 *    definitions for parent items.
 */
class ListItemDefinition {

	/**
	 * The id of the list item definition (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * The id of the list/application where this list item definition belongs
	 * to.
	 * @var int
	 */
	private $applicationDefinitionId = 0;

	/**
	 * The id of the template to use for linked pages (optional).
	 * @var int
	 */
	private $pageDefinitionId = 0;

	/**
	 * A descriptive name for the list item definition.
	 * @var \Scrivo\Str
	 */
	private $title = null;

	/**
	 * The location of an icon to identify the item definition in the user
	 * interface.
	 * @var \Scrivo\Str
	 */
	private $icon = null;

	/**
	 * A textual identification/key for this list item definition.
	 * @var \Scrivo\Str
	 */
	private $phpSelector = null;

	/**
	 * The width of the title property in the user interface for a list item
	 * (column width in list view mode).
	 * @var int
	 */
	private $titleWidth = 0;

	/**
	 * An alternative label for the title property of a list item in the user
	 * interface.
	 * @var \Scrivo\Str
	 */
	private $titleLabel = null;

	/**
	 * When working with hierarchical type definitions this member contains an
	 * array with the child list item definitions of this list item definition.
	 * @var int[]
	 */
	private $childListItemDefinitionIds = null;

	/**
	 * When working with hierarchical type definitions this member contains an
	 * array with the parent list item definitions of this list item definition.
	 * @var int[]
	 */
	private $parentListItemDefinitionIds = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty list item definition object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		if ($context) {
			$this->title = new \Scrivo\Str();
			$this->icon = new \Scrivo\Str();
			$this->phpSelector = new \Scrivo\Str();
			$this->titleLabel = new \Scrivo\Str();

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
			case "pageDefinitionId": return $this->pageDefinitionId;
			case "title": return $this->title;
			case "icon": return $this->icon;
			case "phpSelector": return $this->phpSelector;
			case "titleWidth": return $this->titleWidth;
			case "titleLabel": return $this->titleLabel;
			case "childListItemDefinitionIds":
				return $this->getChildListItemDefinitionIds();
			case "parentListItemDefinitionIds":
				return $this->getParentListItemDefinitionIds();
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
			case "pageDefinitionId": $this->setPageDefinitionId($value); return;
			case "title": $this->setTitle($value); return;
			case "icon": $this->setIcon($value); return;
			case "phpSelector": $this->setPhpSelector($value); return;
			case "titleWidth": $this->setTitleWidth($value); return;
			case "titleLabel": $this->setTitleLabel($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a list item definition object
	 * from an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["list_item_definition_id"]);
		$this->applicationDefinitionId = intval($rd["application_definition_id"]);
		$this->pageDefinitionId = intval($rd["page_definition_id"]);
		$this->title = new \Scrivo\Str($rd["title"]);
		$this->icon = new \Scrivo\Str($rd["icon"]);
		$this->phpSelector = new \Scrivo\Str($rd["php_key"]);
		$this->titleWidth = intval($rd["title_width"]);
		$this->titleLabel = new \Scrivo\Str($rd["title_label"]);

		$this->context = $context;
	}

	/**
	 * Get an array with the child list item definitions of this list item
	 * definition when working with hierarchical list type definitions.
	 *
	 * @return array An array with the child list item definitions for this
	 *    list item definition
	 */
	private function getChildListItemDefinitionIds() {
		if ($this->childListItemDefinitionIds === null) {
			$this->childListItemDefinitionIds =
				$this->selectChildListItemDefinitionIds();
		}
		return $this->childListItemDefinitionIds;
	}

	/**
	 * Get an array with the parent list item definitions of this list item
	 * definition when working with hierarchical list type definitions.
	 *
	 * @return array An array with the parent list item definitions for this
	 *    list item definition
	 */
	private function getParentListItemDefinitionIds() {
		if ($this->parentListItemDefinitionIds === null) {
			$this->parentListItemDefinitionIds =
				$this->selectParentListItemDefinitionIds();
		}
		return $this->parentListItemDefinitionIds;
	}

	/**
	 * Set the id of the list/application where this list item definition
	 * belongs to. It is only possible to set the application id for
	 * uninitialized list item definitions.
	 *
	 * @param int $appId The id of the list/application where this list item
	 *    definition belongs to.
	 */
	private function setApplicationDefinitionId($appId) {
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
	 * Set The id of the template to use for linked pages (optional).
	 *
	 * @param int $pageDefinitionId The id of the template to use for linked
	 *    pages (optional).
	 */
	private function setPageDefinitionId($pageDefinitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->pageDefinitionId = $pageDefinitionId;
	}

	/**
	 * Set A descriptive name for the list item definition.
	 *
	 * @param \Scrivo\Str $title A descriptive name for the list item
	 *    definition.
	 */
	private function setTitle(\Scrivo\Str $title) {
		$this->title = $title;
	}

	/**
	 * Set The location of an icon to identify the item definition in the user
	 * interface.
	 *
	 * @param \Scrivo\Str $icon The location of an icon to identify the
	 *    item definition in the user interface.
	 */
	private function setIcon(\Scrivo\Str $icon) {
		$this->icon = $icon;
	}

	/**
	 * Set A textual identification/key for this list item definition.
	 *
	 * @param \Scrivo\Str $phpSelector A textual identification/key for
	 *    this list item definition.
	 */
	private function setPhpSelector(\Scrivo\Str $phpSelector) {
		$this->phpSelector = $phpSelector;
	}

	/**
	 * Set The width of the title property in the user interface for a list
	 * item (column width in list view mode).
	 *
	 * @param int $titleWidth The width of the title property in the user
	 *    interface for a list item (column width in list view mode).
	 */
	private function setTitleWidth($titleWidth) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->titleWidth = $titleWidth;
	}

	/**
	 * Set An alternative label for the title property of a list item in the
	 * user interface.
	 *
	 * @param \Scrivo\Str $titleLabel An alternative label for the title
	 *    property of a list item in the user interface.
	 */
	private function setTitleLabel(\Scrivo\Str $titleLabel) {
		$this->titleLabel = $titleLabel;
	}

	/**
	 * Retrieve the ids of the child list item definitions. List items
	 * definitions can have a parent child relation to enable you to create
	 * nested (hierarchical) lists.
	 *
	 * @return object[id] An array of objects containing the id field.
	 */
	private function selectChildListItemDefinitionIds() {
		try {
			$sth = $this->context->connection->prepare(
				"SELECT	list_item_definition_id
				FROM parent_list_item_definitions
				WHERE instance_id = :instId AND parent_list_item_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$id = intval($rd["list_item_definition_id"]);
				$res[$id] = (object)array("id" => $id);
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Retrieve the ids of the parent list item definitions. List items
	 * definitions can have a parent child relation to enable you to create
	 * nested (hierarchical) lists.
	 *
	 * @return object[id] An array of objects containing the id field.
	 */
	private function selectParentListItemDefinitionIds() {
		try {
			$sth = $this->context->connection->prepare(
				"SELECT	parent_list_item_definition_id
				FROM parent_list_item_definitions
				WHERE instance_id = :instId AND list_item_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$id = intval($rd["parent_list_item_definition_id"]);
				$res[$id] = (object)array("id" => $id);
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this list item definition object can be inserted into the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
		// application id not 0
	}

	/**
	 * Insert new list item definition object data into the database.
	 *
	 * First it is checked if the data of this list item definition object can
	 * be inserted into the database, then the data is inserted into the
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
				"INSERT INTO list_item_definition (
					instance_id, list_item_definition_id, sequence_no, application_definition_id,
					page_definition_id, title, icon, php_key, title_width,
					title_label
				) VALUES (
					:instId, :id, 0, :appDefId,
					:pageDefId, :title, :icon, :phpSelector, :titleWidth,
					:titleLabel
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(
				":appDefId", $this->applicationDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(
				":pageDefId", $this->pageDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":icon", $this->icon, \PDO::PARAM_STR);
			$sth->bindValue(
				":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
			$sth->bindValue(":titleWidth", $this->titleWidth, \PDO::PARAM_INT);
			$sth->bindValue(":titleLabel", $this->titleLabel, \PDO::PARAM_STR);

			$sth->execute();

			\Scrivo\SequenceNo::position($this->context, "list_item_definition",
				"application_definition_id", $this->id, \Scrivo\SequenceNo::MOVE_LAST);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Set the parent list item definitions for this list item definition in
	 * the database. List items definitions can have a parent child relation
	 * to enable you to create nested (hierarchical) lists.
	 *
	 * @param array $pids An array with ids of parent list item definitions.
	 */
	public function updateParentListItemDefinitionIds(array $pids) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {
			$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

			$sth = $this->context->connection->prepare(
				"DELETE FROM parent_list_item_definitions
				WHERE instance_id = :instId AND list_item_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->execute();

			if ($pids) {

				$sth = $this->context->connection->prepare(
					"INSERT INTO parent_list_item_definitions
					(instance_id, list_item_definition_id, parent_list_item_definition_id)
					VALUES (:instId, :id, :pid)");

				$this->context->connection->bindInstance($sth);
				$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

				foreach ($pids as $pid) {
					$sth->bindValue(":pid", $pid, \PDO::PARAM_INT);
					$sth->execute();

					unset($this->context->cache[$pid]);
				}
				unset($this->context->cache[$this->id]);
			}

			$this->childListItemDefinitionIds = null;
			$this->parentListItemDefinitionIds = null;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this list item definition object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing list item definition object data in the database.
	 *
	 * First it is checked if the data of this list item definition object can
	 * be updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE list_item_definition SET
					page_definition_id = :pageDefId, title = :title, icon = :icon,
					php_key = :phpSelector, title_width = :titleWidth,
					title_label = :titleLabel
				WHERE instance_id = :instId AND list_item_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(
				":pageDefId", $this->pageDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(":icon", $this->icon, \PDO::PARAM_STR);
			$sth->bindValue(
				":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
			$sth->bindValue(":titleWidth", $this->titleWidth, \PDO::PARAM_INT);
			$sth->bindValue(":titleLabel", $this->titleLabel, \PDO::PARAM_STR);

			$sth->execute();

			unset($this->context->cache[$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of list item definition object data does not  violate
	 * any business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the list item definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing list item definition data from the database.
	 *
	 * First it is is checked if it's possible to delete list item definition
	 * data, then the list item definition data including its dependencies is
	 * deleted from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the list item definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the list item definition data.
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
				"DELETE FROM parent_list_item_definitions
				WHERE instance_id = :instId AND
				(list_item_definition_id = :id OR parent_list_item_definition_id = :id)");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			$sth = $context->connection->prepare(
				"DELETE FROM list_item_definition
				WHERE instance_id = :instId AND list_item_definition_id = :id");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			unset($context->cache[$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Move a list item definition one position up or down.
	 *
	 * @param int $dir Direction of the move, see \Scrivo\SequenceNo:::MOVE_*
	 */
	function move($dir=\Scrivo\SequenceNo::MOVE_DOWN) {

		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

		\Scrivo\SequenceNo::position($this->context, "list_item_definition",
			"application_definition_id", $this->id, $dir);
	}

	/**
	 * Fetch a list item definition object from the database using its
	 * object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the list item definition to select.
	 *
	 * @return \Scrivo\ListItemDefinition The requested list item definition
	 *    object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the list item definition from the cache ...
			if (isset($context->cache[$id])) {
				// ... get it from the cache and set the context.
				$listItemDefinition = $context->cache[$id];
				$listItemDefinition->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT list_item_definition_id, sequence_no, application_definition_id,
					page_definition_id, title, icon, php_key, title_width,
					title_label
					FROM list_item_definition
					WHERE instance_id = :instId AND list_item_definition_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException(
						"Failed to load list item definition");
				}

				$listItemDefinition = new \Scrivo\ListItemDefinition($context);
				$listItemDefinition->setFields(
					$context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache[$id] = $listItemDefinition;
			}

			return $listItemDefinition;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select list item definitions from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $applicationDefinitionId The id of the application for which
	 *    to retrieve the list item definitions.
	 * @param int $parentId An optional parent list item definition id.
	 *
	 * @return \Scrivo\ListItemDefinition[id] An array containing the selected
	 *    list item definitions.
	 */
	public static function select(
			\Scrivo\Context $context, $applicationDefinitionId, $parentId=-1) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			$sth = $context->connection->prepare(
				"SELECT I.list_item_definition_id, I.sequence_no, I.application_definition_id,
					I.page_definition_id, I.title, I.icon, I.php_key,
					I.title_width, I.title_label
				FROM list_item_definition I ".
				($parentId != -1 ?
					"LEFT JOIN parent_list_item_definitions P ON (P.instance_id = :instId
						AND I.list_item_definition_id = P.list_item_definition_id) " : "").
				"WHERE I.instance_id = :instId AND I.application_definition_id = :appId ".
				($parentId != -1 ? "AND
					IFNULL(P.parent_list_item_definition_id, 0) = :parentId " : "").
				"ORDER BY sequence_no");

			$context->connection->bindInstance($sth);
			$sth->bindValue(
				":appId", $applicationDefinitionId, \PDO::PARAM_INT);
			if ($parentId != -1) {
				$sth->bindValue(
					":parentId", $parentId, \PDO::PARAM_INT);

			}

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new ListItemDefinition();
				$li->setFields($context, $rd);

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select child list item definitions for a given parent list item
	 * (not a list item definition!) from the database.
	 *
	 * TODO: 1) This method refereces content table list_item, should it be
	 * defined in this class? 2) is this obselete?
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $applicationDefinitionId The id of the application for which
	 *    to retrieve the list item definitions.
	 * @param int $listItemId The id of the list item
	 *
	 * @return ListItemDefinition[id] An array containing the selected list
	 *    item definitions.
	public static function selectChildListItemDefintions(
			\Scrivo\Context $context, $applicationDefinitionId, $listItemId) {
		try {
			$sth = $context->connection->prepare(
				"SELECT T.list_item_definition_id, T.sequence_no, T.application_definition_id,
					T.page_definition_id, T.title, T.icon, T.php_key,
					T.title_width, T.title_label
				FROM list_item_definition T
					LEFT JOIN parent_list_item_definitions P ON (P.instance_id = :instId
						AND	T.list_item_definition_id = P.list_item_definition_id)
					LEFT JOIN list_item I ON (I.instance_id = :instId
						AND	I.list_item_definition = P.parent_list_item_definition_id)
				WHERE instance_id = :instId AND application_definition_id = :appId AND
					IFNULL(I.list_item_id, 0) = :pid
				ORDER BY T.sequence_no");

			$context->connection->bindInstance($sth);
			$sth->bindValue(
				":appId", $applicationDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":pid", $listItemId, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new ListItemDefinition();
				$li->setFields($context, $rd);

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}
	 */
}

?>