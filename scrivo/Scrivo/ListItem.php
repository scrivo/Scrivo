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
 * $Id: ListItem.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ListItem class.
 */

namespace Scrivo;

/**
 * A Scrivo list item is a simple data structure for list item data. List items
 * contain a fixed set of properties and a variable set of properties as
 * definded in its list item property definition.
 *
 * Instances of this class are created through objects of the \Scrivo\ItemList
 * class. Either by retrieving them as items that were previously added to the
 * list or by using the \Scrivo\ItemList::newItem(\Scrivo\String $type) method.
 *
 * @property-read \DateTime $dateCreated The date/time that this list item was created.
 * @property-read \DateTime $dateModified The last date/time that this list item was modified.
 * @property-read int $definitionId The id of the defintion of the list item.
 * @property-read int $id The list item's id (DB key).
 * @property-read int $linkedPageId An optional id of a linked page.
 * @property-read int $listId The id of the list where this list item belongs to.
 * @property-read int $pageId The id of the page where this list item belongs to.
 * @property \DateTime $dateOffline The date/time this list item need to go offline.
 * @property \DateTime $dateOnline The date/time this list item need to go online.
 * @property int $parentId The id of the parent list item.
 * @property \Scrivo\String $title The list item title.
 */
class ListItem {

	/**
	 * The list items id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * The id of the list where this list item belongs to.
	 * @var int
	 */
	private $listId = 0;

	/**
	 * The id of the page where this list item belongs to.
	 * @var int
	 */
	private $pageId = 0;

	/**
	 * The current version of the page: -1: scratch version, 0 live version, 1 and up versions.
	 * @var int
	 */
	private $version = 0;

	/**
	 * The id of the parent list item.
	 * @var int
	 */
	private $parentId = 0;

	/**
	 * The id of the defintion of the list item.
	 * @var int
	 */
	private $definitionId = 0;

	/**
	 * An optional id of a linked page.
	 * @var int
	 */
	private $linkedPageId = 0;

	/**
	 * The list item title.
	 * @var \Scrivo\String
	 */
	protected $title = null;

	/**
	 * The date/time that this list item was created.
	 * @var \DateTime
	 */
	protected $dateCreated = null;

	/**
	 * The last date/time that this list item was modified.
	 * @var \DateTime
	 */
	protected $dateModified = null;

	/**
	 * The date/time this list item need to go online.
	 * @var \DateTime
	 */
	protected $dateOnline = null;

	/**
	 * The date/time this list item need to go offline.
	 * @var \DateTime
	 */
	protected $dateOffline = null;

	/**
	 * The list item properties
	 * @var object
	 */
	protected $properties = null;

	/**
	 * Create an and initalize a list item object.
	 *
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 * @param \Scrivo\PropertySet $properties An array containing the list
	 *    item properties.
	 */
	public function __construct(array $rd, \Scrivo\PropertySet $properties) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null, null));

		$this->id = intval($rd["list_item_id"]);
		$this->listId = intval($rd["item_list_id"]);
		$this->pageId = intval($rd["page_id"]);
		$this->version = intval($rd["version"]);
		$this->parentId = intval($rd["parent_id"]);
		$this->definitionId = intval($rd["list_item_definition_id"]);
		$this->linkedPageId = intval($rd["link_id"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->dateCreated = new \DateTime($rd["date_created"]);
		$this->dateModified = new \DateTime($rd["date_modified"]);
		$this->dateOnline = new \DateTime($rd["date_online"]);
		$this->dateOffline = $rd["date_offline"] == null
			? null : new \DateTime($rd["date_offline"]);
		$this->properties = $properties;
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
			case "listId": return $this->listId;
			case "pageId": return $this->pageId;
			case "version": return $this->version;
			case "parentId": return $this->parentId;
			case "definitionId": return $this->definitionId;
			case "linkedPageId": return $this->linkedPageId;
			case "title": return $this->title;
			case "dateCreated": return $this->dateCreated;
			case "dateModified": return $this->dateModified;
			case "dateOnline": return $this->dateOnline;
			case "dateOffline": return $this->dateOffline;
			case "properties": return $this->properties;
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
			case "parentId": $this->setParentId($value); return;
			case "title": $this->setTitle($value); return;
			case "dateOnline": $this->setDateOnline($value); return;
			case "dateOffline": $this->setDateOffline($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Set The id of the parent list item.
	 *
	 * @param int $parentId The id of the parent list item.
	 */
	public function setParentId($parentId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		\Scrivo\ArgumentCheck::assert(
			$parentId, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		$this->parentId = $parentId;
	}

	/**
	 * Set The page title (&lt;title&gt;).
	 *
	 * @param \Scrivo\String $title The page title (&lt;title&gt;).
	 */
	public function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set The date/time this page need to go online.
	 *
	 * @param \DateTime $dateOnline The date/time this page need to go online.
	 */
	public function setDateOnline(\DateTime $dateOnline) {
		$this->dateOnline = $dateOnline;
	}

	/**
	 * Set The date/time this page need to go offline.
	 *
	 * @param \DateTime $dateOffline The date/time this page need to go offline.
	 */
	public function setDateOffline(\DateTime $dateOffline=null) {
		$this->dateOffline = $dateOffline;
	}

}

?>