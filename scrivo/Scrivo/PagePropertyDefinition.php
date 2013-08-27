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
 * $Id: PagePropertyDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PagePropertyDefinition class.
 */

namespace Scrivo;

/**
 * The PagePropertyDefinition is used to create definions for page properties.
 *
 * Page properties are page objects (text blocks, images, urls, colors, etc)
 * that can be edited using the Scrivo user interface. A page property
 * definition holds the definition of a page property.
 *
 * TODO: full tab html text entry properties still store their data into the
 * etPageDefinition_ELEMENT table.
 *
 * @property-read int $id The page property definition id (DB key).
 * @property \Scrivo\String $phpSelector A textual identification/key for this
 *    page property.
 * @property int $pageDefinitionId The id of the page definition to which this
 *    page property definition belongs.
 * @property int $pageDefinitionTabId An optional id of a page definition tab
 *    on which the page property is displayed.
 * @property \Scrivo\String $title A descriptive name for the page property.
 * @property \Scrivo\String $type The page property type, one out of
 *    PagePropertyDefinition::TYPE_* constants.
 * @property \stdClass $typeData Optional data needed to for this specific
 *    page property.
 */
class PagePropertyDefinition {

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
	 * Value indicating a color select list property.
	 */
	const TYPE_COLOR_LIST = "colorlist";

	/**
	 * Value indicating a color property.
	 */
	const TYPE_COLOR = "color";

	/**
	 * Value indicating a datetime property.
	 */
	const TYPE_DATE_TIME = "datetime";

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
	 * Value indicating a single large html textarea on a tab.
	 */
	const TYPE_HTML_TEXT_TAB = "html_text_tab";

	/**
	 * Value indicating there's an application the tab.
	 */
	const TYPE_APPLICATION_TAB = "application_tab";

	/**
	 * Value indicating a textarea property.
	 */
	const TYPE_TEXT = "text";

	/**
	 * The page property definition id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * The id of the pageDefinition to which this page property definition
	 * belongs.
	 * @var int
	 */
	private $pageDefinitionId = 0;

	/**
	 * A descriptive name for the page property.
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * A textual identification/key for this page property.
	 * @var \Scrivo\String
	 */
	private $phpSelector = null;

	/**
	 * The page property type, one out of PagePropertyDefinition::TYPE_*
	 * constants.
	 * @var \Scrivo\String
	 */
	private $type = "";

	/**
	 * Optional data needed to for this specific page property.
	 * @var \stdClass
	 */
	private $typeData = null;

	/**
	 * An optional id of a pageDefinition tab on which the page property is
	 * displayed.
	 * @var int
	 */
	private $pageDefinitionTabId = 0;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * A flag to indicate a resquence on update
	 * @var boolean
	 */
	private $resequence = false;

	/**
	 * Create an empty page property defintion object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->title = new \Scrivo\String();
			$this->phpSelector = new \Scrivo\String();
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
			case "pageDefinitionId": return $this->pageDefinitionId;
			case "title": return $this->title;
			case "phpSelector": return $this->phpSelector;
			case "type": return $this->type;
			case "typeData": return $this->typeData;
			case "pageDefinitionTabId": return $this->pageDefinitionTabId;
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
			case "pageDefinitionId": $this->setPageDefinitionId($value); return;
			case "title": $this->setTitle($value); return;
			case "phpSelector": $this->setPhpSelector($value); return;
			case "type": $this->setType($value); return;
			case "typeData": $this->setTypeData($value); return;
			case "pageDefinitionTabId":
				$this->setPageDefinitionTabId($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a page property defintion
	 * object from an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["page_property_definition_id"]);
		$this->pageDefinitionId = intval($rd["page_definition_id"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->phpSelector = new \Scrivo\String($rd["php_key"]);
		$this->type = (string)$rd["type"];
		$this->setTypeDataFromRS($rd);
		$this->pageDefinitionTabId = intval($rd["page_definition_tab_id"]);

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
			self::TYPE_COLOR_LIST,
			self::TYPE_COLOR,
			self::TYPE_DATE_TIME,
			self::TYPE_URL,
			self::TYPE_CHECKBOX,
			self::TYPE_HTML_TEXT,
			self::TYPE_HTML_TEXT_TAB,
			self::TYPE_APPLICATION_TAB,
			self::TYPE_TEXT
		);
	}

	/**
	 * Set the id of the page definition to which this page property definition
	 * belongs.
	 *
	 * @param int $pageDefinitionId The id of the page definition to which this
	 *    page property definition belongs.
	 */
	private function setPageDefinitionId($pageDefinitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->pageDefinitionId = $pageDefinitionId;
	}

	/**
	 * Set the descriptive name for the page property.
	 *
	 * @param \Scrivo\String $title A descriptive name for the page property.
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set the textual identification/key for this page property.
	 *
	 * @param \Scrivo\String $phpSelector A textual identification/key for
	 *    this page property.
	 */
	private function setPhpSelector(\Scrivo\String $phpSelector) {
		$this->phpSelector = $phpSelector;
	}

	/**
	 * Set the page property type, one out of the
	 * \Scrivo\PagePropertyDefinition::TYPE_* constants.
	 * Note that TYPE_HTML_TEXT_TAB and TYPE_HTML_TEXT_TAB behave differently
	 * than normal properties so type juggling between these and other types
	 * is not allowed.
	 *
	 * @param string $type The property type, one out of the
	 *    \Scrivo\PagePropertyDefinition::TYPE_* constants.
	 */
	private function setType($type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_STRING, $this->getTypes())
		));

		if ($this->type && (
				($this->type === self::TYPE_HTML_TEXT_TAB
					&& $type !== self::TYPE_HTML_TEXT_TAB) ||
				($this->type === self::TYPE_APPLICATION_TAB
					&& $type !== self::TYPE_APPLICATION_TAB) ||
				(($this->type !== self::TYPE_APPLICATION_TAB
					&& $this->type !== self::TYPE_HTML_TEXT_TAB) &&
				($type === self::TYPE_APPLICATION_TAB
					|| $type === self::TYPE_HTML_TEXT_TAB)))) {
			throw new \Scrivo\SystemException(
				"Type change '{$this->type}' to '{$type}' is not supported");
		}
		$this->type = $type;
	}

	/**
	 * Set the data (optional) needed to for this specific page property type.
	 *
	 * @param \stdClass $typeData Optional data needed to for this specific
	 *    page property type.
	 */
	private function setTypeData(\stdClass $typeData) {
		$this->typeData = $typeData;
	}

	/**
	 * Set the id of a page definition tab (optional) on which this page
	 * property is displayed.
	 * Note that TYPE_HTML_TEXT_TAB and TYPE_HTML_TEXT_TAB types have their
	 * own exclusive tabs and setting their tab is therefore prohibited.
	 *
	 * @param int $pageDefinitionTabId An optional id of a page definition tab
	 *    on which this page property is displayed.
	 */
	private function setPageDefinitionTabId($pageDefinitionTabId) {
		if ($this->type == self::TYPE_HTML_TEXT_TAB
				|| $this->type == self::TYPE_APPLICATION_TAB) {
			throw new \Scrivo\SystemException(
				"cant't set the tab for HTML and Application tabs");
		}
		if ($this->id
				&& ($this->pageDefinitionTabId != $pageDefinitionTabId)) {
			$this->resequence = true;
		}
		$this->pageDefinitionTabId = $pageDefinitionTabId;
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
	 * @param array $rs The type data as stored in the database.
	 */
	private function setTypeDataFromRS($rs) {
		$d = array();

		if ($rs["type"] == self::TYPE_HTML_TEXT_TAB) {
			$d["css_selector"] = new \Scrivo\String($rs["css_selector"]);
			$d["INITIAL_CONTENT"] = new \Scrivo\String("");
			$d["page_css"] = new \Scrivo\String($rs["page_css"]);
			$d["stylesheet"] = new \Scrivo\String($rs["stylesheet"]);
			$d["css_id"] = new \Scrivo\String($rs["css_id"]);
			$this->typeData = (object)$d;
		} else if ($rs["type"] == self::TYPE_APPLICATION_TAB) {
			$d["APPLICATION_DEFINITION_ID"] = intval($rs["application_definition_id"]);
			$this->typeData = (object)$d;
		} else {
			$this->setTypeDataAsString(new \Scrivo\String($rs["type_data"]));
		}
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
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);

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
	 * Check if the current php selector is unique.
	 *
	 * @throws ApplicationException If one or more of the fields contain
	 *   invalid data.
	 */
	private function checkUniquePhpSelector() {

		$sth = $this->context->connection->prepare(
				"SELECT COUNT(*) FROM page_property_definition WHERE
				instance_id = :instId AND page_property_definition_id <> :id AND
				page_definition_id = :templId AND php_key = :phpSelector");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
		$sth->bindValue(":templId", $this->pageDefinitionId, \PDO::PARAM_INT);
		$sth->bindValue(":phpSelector", $this->phpSelector, \PDO::PARAM_STR);

		$sth->execute();

		if ($sth->fetchColumn()) {
			throw new ApplicationException("PHP Selector not unique");
		}
	}

	/**
	 * Check if this page property defintion object can be inserted into the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
		$this->checkUniquePhpSelector();
	}

	/**
	 * Insert new page property defintion object data into the database.
	 *
	 * First it is checked if the data of this page property defintion object
	 * can be inserted into the database, then the data is inserted into the
	 * database. If no id was set a new object id is generated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			if ($this->type == self::TYPE_HTML_TEXT_TAB
					|| $this->type == self::TYPE_APPLICATION_TAB) {

				if (!$this->pageDefinitionTabId) {
					$tab = new \Scrivo\PageDefinitionTab($this->context);
					$tab->pageDefinitionId = $this->pageDefinitionId;
					$tab->title = $this->title;
					$tab->insert();
					$this->pageDefinitionTabId = $tab->id;
				}
				$this->id = $this->pageDefinitionTabId;
				if ($this->type == self::TYPE_HTML_TEXT_TAB) {
					$this->updateHtmlTextTab();
				} else {
					$this->updateApplicationTab();
				}

			} else {

				if (!$this->id) {
					$this->id = $this->context->connection->generateId();
				}

				$sth = $this->context->connection->prepare(
					"INSERT INTO page_property_definition (
						instance_id, page_property_definition_id, page_definition_id, sequence_no,
						title, php_key, type, type_data, page_definition_tab_id
					) VALUES (
						:instId, :id, :pageDefinitionId, 0, :title,
						:phpSelector, :type, :typeData, :pageDefinitionTabId
					)");

				$this->context->connection->bindInstance($sth);
				$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
				$sth->bindValue(":pageDefinitionId",
					$this->pageDefinitionId, \PDO::PARAM_INT);
				$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
				$sth->bindValue(
					":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
				$sth->bindValue(":type", $this->type, \PDO::PARAM_STR);
				$sth->bindValue(
					":typeData", $this->getTypeDataAsString(), \PDO::PARAM_STR);
				$sth->bindValue(":pageDefinitionTabId",
					$this->pageDefinitionTabId, \PDO::PARAM_INT);

				$sth->execute();

				\Scrivo\SequenceNo::position($this->context, "page_property_definition",
					"page_definition_tab_id", $this->id, \Scrivo\SequenceNo::MOVE_LAST);
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this page property defintion object can be updated in the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
		$this->checkUniquePhpSelector();
	}

	/**
	 * Update existing page property defintion object data in the database.
	 *
	 * First it is checked if the data of this page property defintion object
	 * can be updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			if ($this->type == self::TYPE_HTML_TEXT_TAB) {

				$this->updateHtmlTextTab();

			} else if ($this->type == self::TYPE_APPLICATION_TAB) {

				$this->updateApplicationTab();

			} else {

				$sth = $this->context->connection->prepare(
					"UPDATE page_property_definition SET
						page_definition_id = :pageDefinitionId,
						title = :title,
						php_key = :phpSelector, type = :type,
						type_data = :typeData, page_definition_tab_id = :pageDefinitionTabId
					WHERE instance_id = :instId AND page_property_definition_id = :id");

				$this->context->connection->bindInstance($sth);
				$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

				$sth->bindValue(":pageDefinitionId",
					$this->pageDefinitionId, \PDO::PARAM_INT);
				$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
				$sth->bindValue(
					":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
				$sth->bindValue(":type", $this->type, \PDO::PARAM_STR);
				$sth->bindValue(
					":typeData", $this->getTypeDataAsString(), \PDO::PARAM_STR);
				$sth->bindValue(":pageDefinitionTabId",
					$this->pageDefinitionTabId, \PDO::PARAM_INT);

				$sth->execute();

				// If marked (i.e. moved to other tab) move this property last.
				if ($this->resequence) {
					\Scrivo\SequenceNo::position($this->context,
						"page_property_definition", "page_definition_tab_id", $this->id,
						\Scrivo\SequenceNo::MOVE_LAST);
					$this->resequence = false;
				}

				unset($this->context->cache[$this->id]);
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Set page property definition data for html text tab definitions in the
	 * page_definition_tab table.
	 */
	private function updateHtmlTextTab() {

		$sth = $this->context->connection->prepare(
			"UPDATE page_definition_tab SET page_definition_id = :pageDefinitionId,
				php_key = :phpSelector, css_selector = :ccsSelector,
				page_css = :documentCss, stylesheet = :stylesheet,
				css_id = :cssId, application_definition_id = 0
			WHERE instance_id = :instId AND page_definition_tab_id = :id");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

		$sth->bindValue(
			":pageDefinitionId", $this->pageDefinitionId, \PDO::PARAM_INT);
		$sth->bindValue(
			":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
		$sth->bindValue(
			":ccsSelector", $this->typeData->css_selector, \PDO::PARAM_STR);
		$sth->bindValue(
			":documentCss", $this->typeData->page_css, \PDO::PARAM_STR);
		$sth->bindValue(
			":stylesheet", $this->typeData->stylesheet, \PDO::PARAM_STR);
		$sth->bindValue(
			":cssId", $this->typeData->css_id, \PDO::PARAM_STR);

		$sth->execute();
	}

	/**
	 * Set page property definition data for application definitions in the
	 * page_definition_tab table.
	 */
	private function updateApplicationTab() {

		$sth = $this->context->connection->prepare(
			"UPDATE page_definition_tab SET page_definition_id = :pageDefinitionId,
				php_key = :phpSelector, application_definition_id = :appId
			WHERE instance_id = :instId AND page_definition_tab_id = :id");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

		$sth->bindValue(
			":pageDefinitionId", $this->pageDefinitionId, \PDO::PARAM_INT);
		$sth->bindValue(
			":phpSelector", $this->phpSelector, \PDO::PARAM_STR);
		$sth->bindValue(":appId",
			$this->typeData->APPLICATION_DEFINITION_ID, \PDO::PARAM_INT);

		$sth->execute();

	}

	/**
	 * Check if deletion of page property defintion object data does not
	 * violate any business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page property defintion to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing page property defintion data from the database.
	 *
	 * First it is is checked if it's possible to delete page property
	 * defintion data, then the page property defintion data including its
	 * dependecies is deleted from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page property defintion to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the page property defintion data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			$tmp = self::fetch($context, $id);

			if ($tmp->type != self::TYPE_HTML_TEXT_TAB &&
					$tmp->type != self::TYPE_APPLICATION_TAB) {

				$sth = $context->connection->prepare(
					"DELETE FROM page_property_definition
					WHERE instance_id = :instId AND page_property_definition_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

			} else {

				\Scrivo\PageDefinitionTab::delete($context, $id);

			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Move a property one position up or down.
	 *
	 * @param int $dir Direction of the move, see \Scrivo\SequenceNo:::MOVE_*
	 */
	public function move($dir=\Scrivo\SequenceNo::MOVE_DOWN) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 0);

		$this->context->checkPermission(AccessController::WRITE_ACCESS);

		\Scrivo\SequenceNo::position($this->context, "page_property_definition",
			"page_definition_tab_id", $this->id, $dir);
	}

	/**
	 * Fetch a page property defintion object from the database using its
	 * object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page property defintion to select.
	 *
	 * @return \Scrivo\PagePropertyDefinition The requested page property
	 *    defintion object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the page property defintion from the cache ...
			if (isset($context->cache[$id])) {
				// ... get it from the cache and set the context.
				$pagePropertyDefinition = $context->cache[$id];
				$pagePropertyDefinition->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT page_property_definition_id, page_definition_id, sequence_no,
						title, php_key, type, type_data, page_definition_tab_id,
						'' css_selector, '' page_css, '' stylesheet,
						'' css_id, 0 application_definition_id
					FROM page_property_definition
					WHERE instance_id = :instId AND page_property_definition_id = :id
					UNION SELECT
						page_definition_tab_id page_property_definition_id,
						page_definition_id, sequence_no, title title, php_key,
						IF(application_definition_id > 0, :appTab, :htmlTab)
						type, '' type_data, page_definition_tab_id page_definition_tab_id,
						css_selector, page_css,	stylesheet, css_id,
						application_definition_id
					FROM page_definition_tab
					WHERE instance_id = :instId AND page_definition_tab_id = :id"
				);

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);
				$sth->bindValue(
					":appTab", self::TYPE_APPLICATION_TAB, \PDO::PARAM_STR);
				$sth->bindValue(
					":htmlTab", self::TYPE_HTML_TEXT_TAB, \PDO::PARAM_STR);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException(
						"Failed to load page definition property");
				}

				$pagePropertyDefinition =
					new \Scrivo\PagePropertyDefinition();
				$pagePropertyDefinition->setFields(
					$context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache[$id] = $pagePropertyDefinition;
			}

			return $pagePropertyDefinition;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select page property defintions from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $pageDefinitionId An id of a page definition for which to
	 *    select its properties.
	 *
	 * @return \Scrivo\PagePropertyDefinition[id] An array containing the
	 *    selected page property defintions.
	 */
	public static function select(\Scrivo\Context $context, $pageDefinitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			$sth = $context->connection->prepare(
				"SELECT * FROM (
				SELECT page_property_definition_id, page_definition_id, sequence_no, title,
					php_key, type, type_data, page_definition_tab_id, '' css_selector,
					'' page_css, '' stylesheet, '' css_id, 0 application_definition_id
				FROM page_property_definition
				WHERE instance_id = :instId AND page_definition_id = :templId
				UNION SELECT
					page_definition_tab_id page_property_definition_id,
					page_definition_id, sequence_no, title title, php_key,
					IF(application_definition_id > 0, :appTab, :htmlTab)
					type, '' type_data, page_definition_tab_id page_definition_tab_id,
					css_selector, page_css,	stylesheet, css_id,
					application_definition_id
				FROM page_definition_tab
				WHERE instance_id = :instId AND application_definition_id >= 0
					AND page_definition_id = :templId
				) TMP ORDER BY page_definition_tab_id, sequence_no");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":templId", $pageDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(
				":appTab", self::TYPE_APPLICATION_TAB, \PDO::PARAM_STR);
			$sth->bindValue(
				":htmlTab", self::TYPE_HTML_TEXT_TAB, \PDO::PARAM_STR);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new PagePropertyDefinition();
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