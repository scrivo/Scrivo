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
 * $Id: PageProperty.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PageProperty class.
 */

namespace Scrivo;

/**
 * The PageProperty class is the base class for all page properties. Page
 * properties are a part of the page.
 *
 * Note that PageProperties can't be created nor deleted using this class.
 * PageProperties are created when the page itself is created, using the
 * PageDefintion rules.
 *
 * This is an abstract class that implements the methods for creating (factory
 * method "create"), listing and updating page properties.
 *
 * Legacy information: Page properties (including applications) are stored
 * in serveral Scrivo data tables (page_property_html, page_property) as
 * their definitions (page_definition_tab, page_property_definition, application_definition).
 * This distiction is dropped, all these are accessed though a the
 * single page property "properties".
 */
class PageProperty {

	/**
	 * The property type: one out of the TemplateProperty::TYPE_* constants.
	 * @var \Scrivo\Str
	 */
	private $type;

	/**
	 * An textual identification/key for this property.
	 * @var \Scrivo\Str
	 */
	private $phpSelector;

	/**
	 * The property data.
	 * @var \Scrivo\Str
	 */
	private $data;

	/**
	 * A derived data of the property data.
	 * @var \Scrivo\Str
	 */
	private $extData;

	/**
	 * The property defintion id.
	 * @var int
	 */
	private $definitionId;

	/**
	 * The page where this property belongs to.
	 * @var \Scrivo\Page
	 */
	private $page;

	/**
	 * Create an empty page property object.
	 *
	 * @param \Scrivo\Page $page This poperty's page.
	 * @param array $rd An array containing the initial data (result set row).
	 */
	protected function __construct(\Scrivo\Page $page, array $rd) {

		$this->type = new \Scrivo\Str($rd["type"]);
		$this->phpSelector = new \Scrivo\Str($rd["php_key"]);
		$this->setData(new \Scrivo\Str($rd["value"]));
		$this->setExtData(new \Scrivo\Str($rd["VALUE2"]));
		$this->definitionId = intval($rd["ID_DEF"]);

		$this->page = $page;
	}

	/**
	 * Factory method to create page properties. The method will nog return
	 * a base PageProperty object but a sepacialized descendant based upon
	 * the given data.
	 *
	 * @param \Scrivo\Page $page The page for which to create the page property.
	 * @param array $rd Array (result set row data) with the page property
	 *    data.

	 * @return \Scrivo\PageProperty\Image|\Scrivo\PageProperty\SelectList|
	 *     \Scrivo\PageProperty\ColorList|\Scrivo\PageProperty\Color|
	 *     \Scrivo\PageProperty\Url|\Scrivo\PageProperty\CheckBox|
	 *     \Scrivo\PageProperty\Input|\Scrivo\PageProperty\Text|
	 *     \Scrivo\PageProperty\HtmlText|\Scrivo\PageProperty\HtmlContent|
	 *     \Scrivo\PageProperty\Application
	 */
	public static function create(\Scrivo\Page $page, $rd) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null, null));

		switch ($rd["type"]) {
		case \Scrivo\PagePropertyDefinition::TYPE_IMAGE:
		case "imgalttit":
			return new \Scrivo\PageProperty\Image($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_SELECT:
			return new \Scrivo\PageProperty\SelectList($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_COLOR_LIST:
			return new \Scrivo\PageProperty\ColorList($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_COLOR:
			return new \Scrivo\PageProperty\Color($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_URL:
			return new \Scrivo\PageProperty\Url($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_CHECKBOX:
			return new \Scrivo\PageProperty\CheckBox($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_DATE_TIME:
			return new \Scrivo\PageProperty\DateTime($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_INPUT:
			return new \Scrivo\PageProperty\Input($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_TEXT:
			return new \Scrivo\PageProperty\Text($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_HTML_TEXT:
			return new \Scrivo\PageProperty\HtmlText($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_HTML_TEXT_TAB:
			return new \Scrivo\PageProperty\HtmlContent($page, $rd);

		case \Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB:
			return new \Scrivo\PageProperty\Application($page, $rd);
		}
		throw new \Scrivo\SystemException(
			"Invalid property type '{$rd["type"]}'");
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
			case "type": return $this->type;
			case "phpSelector": return $this->phpSelector;
			case "data": return $this->data;
			case "extData": return $this->extData;
			//case "definitionId": return $this->definitionId;
			case "definition": return $this->definitionId ?
				\Scrivo\PagePropertyDefinition::fetch(
					$this->page->context, $this->definitionId)
				: new \Scrivo\PageDefinition($this->page->context);
			case "page": return $this->page;
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
			case "data": $this->setData($value); return;
			case "extData": $this->setExtData($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Set the property data.
	 *
	 * @param \Scrivo\Str $data The property data.
	 */
	protected function setData(\Scrivo\Str $data) {
		$this->data = $data;
	}

	/**
	 * Set the property extended data.
	 *
	 * @param \Scrivo\Str $extData A derived data of the property data.
	 */
	protected function setExtData(\Scrivo\Str $extData) {
		$this->extData = $extData;
	}

	/**
	 * Check if this page property object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->page->context->checkPermission(
			\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update page property object data in the database.
	 *
	 * First it is checked if the data of this page property object can be
	 * updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	function update() {
		try {
			//$dummy = $this->getData();

			$this->validateUpdate();

			if ((string)$this->type ===
					\Scrivo\PagePropertyDefinition::TYPE_HTML_TEXT_TAB) {

				$this->updateContent();

			} else if ((string)$this->type !==
					\Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB) {

				$this->updateProperty();
			}


			unset($this->page->context->cache[$this->page->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Update property data in the page_property table.
	 */
	private function updateProperty() {

		$sth = $this->page->context->connection->prepare(
			"DELETE FROM page_property WHERE instance_id = :instId AND
			page_id = :pageId AND page_property_definition_id = :idDef AND
			version = (SELECT version FROM page WHERE
				instance_id = :instId AND page_id = :pageId
				AND (has_staging+version) = 0)");

		$this->page->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $this->page->id, \PDO::PARAM_INT);
		$sth->bindValue(":idDef", $this->definitionId, \PDO::PARAM_STR);

		$sth->execute();

		$sth = $this->page->context->connection->prepare(
			"INSERT INTO page_property (
				instance_id, page_id, version, page_property_definition_id,
				value
			 ) VALUES (:instId, :pageId, (SELECT version FROM page WHERE
				instance_id = :instId AND page_id = :pageId
				AND (has_staging+version) = 0), :idDef, :data)");

		$this->page->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $this->page->id, \PDO::PARAM_INT);
		$sth->bindValue(":idDef", $this->definitionId, \PDO::PARAM_STR);
		$sth->bindValue(":data", $this->data, \PDO::PARAM_STR);

		$sth->execute();

	}

	/**
	 * Update property data in the page_property_html table.
	 */
	private function updateContent() {

		$sth = $this->page->context->connection->prepare(
			"DELETE FROM page_property_html WHERE instance_id = :instId AND
			page_id = :pageId AND page_definition_tab_id = :defId AND
			version = (SELECT version FROM page WHERE
				instance_id = :instId AND page_id = :pageId
				AND (has_staging+version) = 0)");

		$this->page->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $this->page->id, \PDO::PARAM_INT);
		$sth->bindValue(":defId", $this->definitionId, \PDO::PARAM_STR);

		$sth->execute();

		$sth = $this->page->context->connection->prepare(
			"INSERT INTO page_property_html (
				instance_id, page_id, version, page_definition_tab_id,
				raw_html, html
			 ) VALUES (:instId, :pageId, (SELECT version FROM page WHERE
				instance_id = :instId AND page_id = :pageId
				AND (has_staging+version) = 0), :defId, :data2, :data)");

		$this->page->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $this->page->id, \PDO::PARAM_INT);
		$sth->bindValue(":defId", $this->definitionId, \PDO::PARAM_STR);
		$sth->bindValue(":data", $this->data, \PDO::PARAM_STR);
		$sth->bindValue(":data2", $this->extData, \PDO::PARAM_STR);

		$sth->execute();

	}


}

?>