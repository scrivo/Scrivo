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
 * $Id: PageDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PageDefinition class.
 */

namespace Scrivo;

/**
 * The PageDefinition class is used to create a definition for a page.
 *
 * In the Scrivo user interface the editor will select a page definition to
 * create a page from. The page definition holds a list of configurable
 * properties (text blocks, images, urls, colors, etc) that can be edited for
 * that page. See also the class PageDefinitionProperty that is used to define
 * the individual properties and the class PageDefinitionTab that is used to
 * distribute these properties over several tabs in the Scrivo user interface.
 *
 * Furthermore the page definition holds a reference to the PHP script that
 * should be executed when rendering pages with this page definition
 * definition.
 *
 * PageDefinitions can be suppressed in the user interface using the
 * 'configOnly'property: for instance, there is no need to let the editor
 * chose another page using the 'Home' page definition in a site. Or they can
 * be shown conditionally in the interface: you can only select pages of page
 * definition 'Main Menu' as sub pages of a page of page definition 'Home'.
 * See the 'PageDefinitionHints' class for more details.
 *
 * TODO: field type_set now is a semicolon seperated list (DB data), better
 *  to serialize this data.
 * TODO: field search_index_rule has no function anymore.
 *
 * @property-read int $id The page definition id (DB key).
 * @property-read object $properties The page definition properties.
 * @property-read \Scrivo\PageDefinitionTab[] $tabs The page definition tabs.
 * @property boolean $configOnly Setting to suppress this page definition in
 *   the user interface.
 * @property int $defaultTabId The id of the tab that should be shown in the
 *   user interface as the active tab when the editor selects a page using this
 *   page definition for editing.
 * @property \Scrivo\String $description An additional description for the
 *   page definition.
 * @property \Scrivo\String $action The location of the PHP script to
 *   execute when rendering pages using this page definition.
 * @property \Scrivo\String $title A descriptive title for the page definition.
 * @property int[] $typeSet The set of page types that the user can select
 *   in the user interface  when creating/modifing pages using this page
 *   definition.
 */
class PageDefinition {

	/**
	 * The page definition id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * A descriptive title for the page definition.
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * An additional description for the page definition.
	 * @var \Scrivo\String
	 */
	private $description = null;

	/**
	 * The location of the PHP script to execute when rendering pages using
	 * this page definition.
	 * @var \Scrivo\String
	 */
	private $action = null;

	/**
	 * Setting to suppress this page definition in the user interface.
	 * @var boolean
	 */
	private $configOnly = false;

	/**
	 * The set of page types that the user can select in the user interface
	 * when creating/modifing pages using this page definition.
	 * @var int[]
	 */
	private $typeSet = array();

	/**
	 * The id of the tab that should be shown in the user interface as the
	 * active tab when the editor selects a page using this page definition
	 * for editing.
	 * @var int
	 */
	private $defaultTabId = 0;

	/**
	 * The page definition properties as a PHP object in which the members
	 * correspond with the PHP selector names.
	 * @var \stdClass
	 */
	protected $properties = null;

	/**
	 * The page definition tabs.
	 * @var \Scrivo\PageDefinitionTab[]
	 */
	protected $tabs = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty page definition object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->title = new \Scrivo\String();
			$this->description = new \Scrivo\String();
			$this->action = new \Scrivo\String();

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
			case "action": return $this->action;
			case "configOnly": return $this->configOnly;
			case "typeSet": return $this->typeSet;
			case "defaultTabId": return $this->defaultTabId;
			case "properties": return $this->getProperties();
			case "tabs": return $this->getTabs();
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
			case "action": $this->setFileName($value); return;
			case "configOnly": $this->setConfigOnly($value); return;
			case "typeSet": $this->setTypeSet($value); return;
			case "defaultTabId": $this->setDefaultTabId($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a page definition object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["page_definition_id"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->description = new \Scrivo\String($rd["description"]);
		$this->action = new \Scrivo\String($rd["action"]);
		$this->configOnly = intval($rd["config_only"]) == 1 ? true : false;
		$this->typeSet =
			$this->convertTypeSet(new \Scrivo\String($rd["type_set"]));
		$this->defaultTabId = intval($rd["default_tab_id"]);

		$this->context = $context;
	}

	/**
	 * Get this page definition's property list.
	 *
	 * @return object This page definition's property list.
	 */
	private function getProperties() {
		if ($this->properties === null) {
			$this->properties = (object)
				\Scrivo\PagePropertyDefinition::select($this->context, $this->id);
		}
		return $this->properties;
	}

	/**
	 * Get this page definition's tabs.
	 *
	 * @return \Scrivo\PageDefinitionTab[] This page definition's tabs.
	 */
	private function getTabs() {
		if ($this->tabs === null) {
			$this->tabs =
				\Scrivo\PageDefinitionTab::select($this->context, $this->id);
		}
		return $this->tabs;
	}

	/**
	 * Set A descriptive title for the page definition.
	 *
	 * @param \Scrivo\String $title A descriptive title for the page
	 *    definition.
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set the additional description for the page definition.
	 *
	 * @param \Scrivo\String $description An additional description for the
	 *    page definition.
	 */
	private function setDescription(\Scrivo\String $description) {
		$this->description = $description;
	}

	/**
	 * Set the location of the PHP script to execute when rendering pages
	 * using this page definition.
	 *
	 * @param \Scrivo\String $action The location of the PHP script to
	 *    execute when rendering pages using this page definition.
	 */
	private function setFileName(\Scrivo\String $action) {
		$this->action = $action;
	}

	/**
	 * Set the setting to suppress this page definition in the user interface.
	 *
	 * @param boolean $configOnly Setting to suppress this page definition in
	 *    the user interface.
	 */
	private function setConfigOnly($configOnly) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_BOOLEAN)
		));

		$this->configOnly = $configOnly;
	}

	/**
	 * Set the set of page types that the user can select in the user interface
	 * when creating/modifing pages using this page definition.
	 *
	 * @param int[] $typeSet The set of page types that the user can select in
	 *    the user interface  when creating/modifing pages using this page
	 *    definition.
	 */
	private function setTypeSet(array $typeSet) {
		$this->typeSet = $typeSet;
	}

	/**
	 * Set the id of the tab that should be shown in the user interface as
	 * the active tab when the editor selects a page using this page definition
	 * for editing.
	 *
	 * @param int $defaultTabId The id of the tab that should be shown in the
	 *    user interface as the active tab when the editor selects a page using
	 *    this page definition for editing.
	 */
	private function setDefaultTabId($defaultTabId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->defaultTabId = $defaultTabId;
	}

	/**
	 * Convert the semicolon seperated value from the database to an array.
	 *
	 * @param \Scrivo\String typeSetString An semicolon seperated string.
	 * @return int[] The type set as an array of integers.
	 */
	private function convertTypeSet(\Scrivo\String $typeSetString) {
		$ts = $typeSetString->split(new \Scrivo\String(";"));
		$ts2 = array();
		foreach ($ts as $pageId) {
			$ts2[] = intval((string)$pageId);
		}
		return $ts2;
	}

	/**
	 * Check if this page definition object can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new page definition object data into the database.
	 *
	 * First it is checked if the data of this page definition object can be
	 * inserted into the database, then the data is inserted into the database.
	 * If no id was set a new object id is generated.
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
				"INSERT INTO page_definition (
					instance_id, page_definition_id, title, description, action,
					config_only, type_set, default_tab_id
				) VALUES (
					:instId, :id, :title, :description, :action,
					:configOnly, :typeSet, :defaultTabId
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);
			$sth->bindValue(":action", $this->action, \PDO::PARAM_STR);
			$sth->bindValue(":configOnly",
				$this->configOnly ? 1 : 0, \PDO::PARAM_INT);
			$sth->bindValue(":typeSet", new \Scrivo\String(
				implode(";", $this->typeSet)), \PDO::PARAM_INT);
			$sth->bindValue(
				":defaultTabId", $this->defaultTabId, \PDO::PARAM_INT);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this page definition object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing page definition object data in the database.
	 *
	 * First it is checked if the data of this page definition object can be
	 * updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE page_definition SET
					title = :title, description = :description,
					action = :action, config_only = :configOnly,
					type_set = :typeSet, default_tab_id = :defaultTabId
				WHERE instance_id = :instId AND page_definition_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);
			$sth->bindValue(":action", $this->action, \PDO::PARAM_STR);
			$sth->bindValue(":configOnly",
				$this->configOnly ? 1 : 0, \PDO::PARAM_INT);
			$sth->bindValue(":typeSet", new \Scrivo\String(
				implode(";", $this->typeSet)), \PDO::PARAM_INT);
			$sth->bindValue(
				":defaultTabId", $this->defaultTabId, \PDO::PARAM_INT);

			$sth->execute();

			unset($this->context->cache[$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of page definition object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing page definition data from the database.
	 *
	 * First it is is checked if it's possible to delete page definition data,
	 * then the page definition data including its dependencies is deleted from
	 * the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the page definition data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			foreach (array("page_property_definition", "page_definition_tab", "page_definition")
				as $table) {

				$sth = $context->connection->prepare(
					"DELETE FROM $table
					WHERE instance_id = :instId AND page_definition_id = :id");

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
	 * Fetch a page definition object from the database using its object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page definition to select.
	 *
	 * @return \Scrivo\PageDefinition The requested page definition object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the page definition from the cache ...
			if (isset($context->cache[$id])) {
				// ... get it from the cache and set the context.
				$pageDefinition = $context->cache[$id];
				$pageDefinition->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT page_definition_id, title, description, action,
						config_only, type_set, default_tab_id
					FROM page_definition
					WHERE instance_id = :instId AND page_definition_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException(
						"Failed to load page definition $id");
				}

				$pageDefinition = new \Scrivo\PageDefinition();
				$pageDefinition->setFields(
					$context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache[$id] = $pageDefinition;
			}

			return $pageDefinition;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select page definitions from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 *
	 * @return \Scrivo\PageDefinition[id] An array containing the selected
	 *    page definitions.
	 */
	public static function select(\Scrivo\Context $context) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {
			$sth = $context->connection->prepare(
				"SELECT page_definition_id, title, description, action,
					config_only, type_set, default_tab_id
				FROM page_definition
				WHERE instance_id = :instId
				ORDER BY title");

			$context->connection->bindInstance($sth);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new PageDefinition();
				$li->setFields($context, $rd);

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select the list of page definitions that are selectable by a an editor.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $parentId The id of the page where the editor wants to
	 *   create a page underneath.
	 *
	 * @return PageDefinition[id] An array containing the selected page
	 *   definitions.
	 */
	public static function selectSelectable(
			\Scrivo\Context $context, $parentId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		// Select all the page definitions ...
		$list = self::select($context);

		// ... and the hints ...
		$hints = new \Scrivo\PageDefinitionHints($context, $parentId,
			\Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING);

		// ... and create a new list of page definitions that are not config
		// only and have a count left according the hints list.
		$pageDefinitions = array();
		foreach ($list as $k=>$pageDef) {
			if (!$pageDef->configOnly && $hints[$k]->maxNoOfChildren !== 0) {
				$pageDefinitions[$k] = $pageDef;
			}
		}

		return $pageDefinitions;
	}

}

?>