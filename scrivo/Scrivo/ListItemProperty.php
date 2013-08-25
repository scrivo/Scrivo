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
 * $Id: ListItemProperty.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ListItemProperty class.
 */

namespace Scrivo;

/**
 * The ListItemProperty class is the base class for all list item properties.
 * Page properties are a part of the list item.
 *
 */
class ListItemProperty {

	/**
	 * The property type: one out of the TemplateProperty::TYPE_* constants.
	 * @var \Scrivo\String
	 */
//	protected $type;

	/**
	 * An textual identification/key for this property.
	 * @var \Scrivo\String
	 */
	protected $phpSelector;

	/**
	 * The property data.
	 * @var \Scrivo\String
	 */
	protected $data;

	/**
	 * Id of the property defintion.
	 * @var int
	 */
	private $definitionId;

	/**
	 * The id of the list item where this property belongs to.
	 * @var int
	 */
	private $listItemId;

	/**
	 * The id of the page where this property belongs to.
	 * @var int
	 */
	private $pageId;

	/**
	 * Create an empty list item property object or select a list item property
	 * from the database.
	 *
	 * @param array $rd An array (result set row) containing inital data.
	 */
	protected function __construct(array $rd) {
		$this->listItemId = intval($rd["list_item_id"]);
//		$this->type = new \Scrivo\String($rd["type"]);
		$this->phpSelector = new \Scrivo\String($rd["php_key"]);
		$this->setData(new \Scrivo\String($rd["value"]));
		$this->definitionId = intval($rd["ID_DEF"]);
		$this->pageId = intval($rd["page_id"]);
	}

	/**
	 * Factory method for creating a list item property. This method will
	 * return a list item property of the proper specialized type.
	 *
	 * @param array $rd An array (result set row) containing inital data.
	 */
	public static function create(array $rd) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		switch ($rd["type"]) {
		case \Scrivo\ListItemPropertyDefinition::TYPE_IMAGE:
			return new \Scrivo\ListItemProperty\ImageAltTitle($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_SELECT:
			return new \Scrivo\ListItemProperty\SelectList($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_COLOR:
			return new \Scrivo\ListItemProperty\Color($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_URL:
			return new \Scrivo\ListItemProperty\Url($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_CHECKBOX:
			return new \Scrivo\ListItemProperty\CheckBox($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_INPUT:
			return new \Scrivo\ListItemProperty\Input($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_TEXT:
			return new \Scrivo\ListItemProperty\Text($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_HTML_TEXT:
			return new \Scrivo\ListItemProperty\HtmlText($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_DATE_TIME:
			return new \Scrivo\ListItemProperty\DateTime($rd);

		case \Scrivo\ListItemPropertyDefinition::TYPE_TAB:
			return null;

		case \Scrivo\ListItemPropertyDefinition::TYPE_INFO:
			return null;
		}

		throw new \Scrivo\SystemException(
			"Invalid property type {$rd["type"]}");
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
		switch ($name) {
//			case "type": return $this->type;
			case "phpSelector": return $this->phpSelector;
			case "data": return $this->data;
			case "definitionId": return $this->definitionId;
			case "listItemId": return $this->listItemId;
			case "pageId": return $this->pageId;
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
		switch ($name) {
			case "data": $this->setData($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Set The property data.
	 *
	 * @param \Scrivo\String $data The property data.
	 */
	protected function setData(\Scrivo\String $data) {
		$this->data = $data;
	}

}

?>