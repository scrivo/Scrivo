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
 * $Id: PageDefinitionTab.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PageDefinitionTab class.
 */

namespace Scrivo;

/**
 * The class PageDefinitionTab is used to create tabs in the Scrivo user
 * interface.
 *
 * A page can have can a number of configurable properities, which are defined
 * by the page definition. The PageDefinitionTab class is used as a means to
 * distribute these properties over different tabs in the Scrivo user interface.
 *
 * With respect to the content this class has no function at all, it is used
 * for display purposes.
 *
 * TODO: The page_definition_tab table is still used to hold property data for
 * 'html_text' and 'application' tabs. Access to this data is handled bij the
 * PageProperty class now. In a later version the data model should be
 * changed also.
 *
 * @property-read int $id The page definition tab id (DB key).
 * @property int $pageDefinitionId The page definition id of the page
 *    definition where this tab belongs to.
 * @property \Scrivo\String $title A descriptive title for the tab.
 * @property int $type The type of the tab: one out of the
 *    PageDefinitionTab::TYPE_* constant values.
 */
class PageDefinitionTab {

	/**
	 * Constant to mark a tab as a property tab.
	 */
	const TYPE_PROPERTY_TAB = 1;

	/**
	 * Constant to mark a tab as an html text tab.
	 */
	const TYPE_HTML_TEXT_TAB = 2;

	/**
	 * Constant to mark a tab as an application tab.
	 */
	const TYPE_APPLICATION_TAB = 3;

	/**
	 *  The page definition tab id (DB key).
	 * @var int
	 */
	private $id = 0;


	/**
	 * The page definition id of the page definition where this tab belongs to.
	 * @var int
	 */
	private $pageDefinitionId = 0;

	/**
	 * A descriptive title for the tab.
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * The type of the tab: one out of the PageDefinitionTab::TYPE_* constant
	 * values.
	 * @var int
	 */
	private $type = self::TYPE_PROPERTY_TAB;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty page defintion tab object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->title = new \Scrivo\String();

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
			case "pageDefinitionId": $this->setPageDefinitionId($value); return;
			case "title": $this->setTitle($value); return;
			case "type": $this->setType($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a page defintion tab object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["page_definition_tab_id"]);
		$this->pageDefinitionId = intval($rd["page_definition_id"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->type = $this->toType($rd);

		$this->context = $context;
	}


	/**
	 * Set The page definition id of the page definition where this tab
	 * belongs to.
	 *
	 * @param int $pageDefinitionId The page definition id of the page
	 *     definition where this tab belongs to.
	 */
	private function setPageDefinitionId($pageDefinitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->pageDefinitionId = $pageDefinitionId;
	}

	/**
	 * Set A descriptive title for the tab.
	 *
	 * @param \Scrivo\String $title A descriptive title for the tab.
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set The type of the tab: one out of the PageDefinitionTab::TYPE_*
	 * constant values.
	 *
	 * @param array $rd Array (result set row) that contains the
	 *    application_definition_id field.
	 */
	private function toType(array $rd) {
		$val = intval($rd["application_definition_id"]);
		if ($val === -1) {
			return self::TYPE_PROPERTY_TAB;
		} else if ($val === 0) {
			return self::TYPE_HTML_TEXT_TAB;
		}
		return self::TYPE_APPLICATION_TAB;
	}

	/**
	 * Check if this page defintion tab object can be inserted into the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new page defintion tab object data into the database.
	 *
	 * First it is checked if the data of this page defintion tab object can be
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
				"INSERT INTO page_definition_tab (
					instance_id, page_definition_tab_id, page_definition_id, sequence_no,
					title, application_definition_id
				) VALUES (
					:instId, :id, :pageDefinitionId, 0,
					:title, -1
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(
				":pageDefinitionId", $this->pageDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);

			$sth->execute();

			\Scrivo\SequenceNo::position($this->context, "page_definition_tab",
				"page_definition_id", $this->id, \Scrivo\SequenceNo::MOVE_LAST);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this page defintion tab object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing page defintion tab object data in the database.
	 *
	 * First it is checked if the data of this page defintion tab object can be
	 * updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE page_definition_tab SET
					page_definition_id = :pageDefinitionId,
					title = :title
				WHERE instance_id = :instId AND page_definition_tab_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(
				":pageDefinitionId", $this->pageDefinitionId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);

			$sth->execute();

			unset($this->context->cache[$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of page defintion tab object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page defintion tab to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing page defintion tab data from the database.
	 *
	 * First it is is checked if it's possible to delete page defintion tab
	 * data, then the page defintion tab data including its dependencies is
	 * deleted from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page defintion tab to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the page defintion tab data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			$sth = $context->connection->prepare(
				"DELETE FROM page_definition_tab
				WHERE instance_id = :instId AND page_definition_tab_id = :id");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			unset($context->cache[$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Move an tab one position up (left) or down (right).
	 *
	 * @param int $dir Direction of the move, see \Scrivo\SequenceNo:::MOVE_*
	 */
	public function move($dir=\Scrivo\SequenceNo::MOVE_DOWN) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 0);

		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

		\Scrivo\SequenceNo::position($this->context, "page_definition_tab",
			"page_definition_id", $this->id, $dir);
	}

	/**
	 * Fetch a page defintion tab object from the database using its object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page defintion tab to select.
	 *
	 * @return \Scrivo\PageDefinitionTab The requested page defintion
	 *    tab object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the page defintion tab from the cache ...
			if (isset($context->cache["TAB_".$id])) {
				// ... get it from the cache and set the context.
				$pageDefinitionTab = $context->cache["TAB_".$id];
				$pageDefinitionTab->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT page_definition_tab_id, page_definition_id, sequence_no,
						title, application_definition_id
					FROM page_definition_tab
					WHERE instance_id = :instId AND page_definition_tab_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException(
						"Failed to load page defintion tab");
				}

				$pageDefinitionTab = new \Scrivo\PageDefinitionTab($context);
				$pageDefinitionTab->setFields(
					$context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache["TAB_".$id] = $pageDefinitionTab;
			}

			return $pageDefinitionTab;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select page defintion tabs from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $pageDefinitionId The id of the pageDefinition for which to
	 *    retrieve the tabs.
	 *
	 * @return \Scrivo\PageDefinitionTab[id] An array containing the selected
	 *    page defintion tabs.
	 */
	public static function select(\Scrivo\Context $context, $pageDefinitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			$sth = $context->connection->prepare(
				"SELECT page_definition_tab_id, page_definition_id, sequence_no,
					title, application_definition_id
				FROM page_definition_tab
				WHERE instance_id = :instId AND page_definition_id = :pageDefinitionId
				ORDER BY sequence_no");

			$context->connection->bindInstance($sth);
			$sth->bindValue(
				":pageDefinitionId", $pageDefinitionId, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new PageDefinitionTab();
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