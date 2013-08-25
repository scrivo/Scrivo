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
 * $Id: ItemList.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ItemList class.
 */

namespace Scrivo;

/**
 * Item lists (or Scrivo list) are a very versitle way of adding list like
 * data to a page (faq, comments, news and even a forurm like)
 */
class ItemList {

	/**
	 * The list/application id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * Id of the list definition.
	 * @var int
	 */
	private $pagePropertyDefinitionId = 0;

	/**
	 * Id of the application definition.
	 * @var int
	 */
	private $applicationDefinitionId = 0;

	/**
	 * Id of the page where this list instance is associated with.
	 * @var int
	 */
	private $pageId = 0;

	/**
	 * Optional reference to a page that is parent for pages linked to
	 * list items.
	 * @var int
	 */
	private $folderId = 0;

	/**
	 * A list of list item pids.
	 * @var array[]
	 */
	private $listPids = null;

	/**
	 * The attached roles.
	 * @var \Scrivo\RoleSet
	 */
	private $roles;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Construct an item list. Item lists are Scrivo application and therefore
	 * linked to a page and an application definition.
	 *
	 * Scrivo lists are never created as explicitly, but assumed to exist if
	 * a page with a list type application definition does exist. If not the
	 * list is created on the fly based upon its defintion. Because actual
	 * list creation is an internal issue and not ment to do expicitly the
	 * constructor is not public.
	 *
	 * To create a list see 'fetch'.
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context
	 * @param string $pageId The id of the page that hosts the application.
	 * @param string $pagePropertyDefinitionId The page property definition id.
	 */
	protected function __construct(
			\Scrivo\Context $context, $pageId, $pagePropertyDefinitionId) {
		try {
			$this->context = $context;

			// Check if the list was create before (the list record exists).
			$sth = $context->connection->prepare(
				"SELECT L.item_list_id, L.page_definition_tab_id, L.page_id,
					L.folder_id, T.application_definition_id
				FROM item_list L, page_definition_tab T, page D
				WHERE L.instance_id = :instId AND D.instance_id = :instId AND
					T.instance_id = :instId AND D.page_definition_id = T.page_definition_id AND
					T.page_definition_tab_id = L.page_definition_tab_id AND
					L.version = D.version AND L.page_id = D.page_id AND
					(D.has_staging + D.version) = 0 AND
					L.page_definition_tab_id = :ppDefId AND
					L.page_id  = :pageId");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":pageId", $pageId, \PDO::PARAM_INT);
			$sth->bindValue(
				":ppDefId", $pagePropertyDefinitionId, \PDO::PARAM_INT);

			$sth->execute();

			if ($sth->rowCount() > 1) {
				throw new \Scrivo\SystemException(
					"Data corruption: failed to load list");
			} else if ($sth->rowCount() === 0) {
				// If not create it
				// Is it a valid page and application definition
				// Yes create the list
				// else throw up
				$this->pageId = $pageId;
				$this->pagePropertyDefinitionId = $pagePropertyDefinitionId;
				$this->insert();
			} else {
				$rd = $sth->fetch(\PDO::FETCH_ASSOC);
				$this->id = intval($rd["item_list_id"]);
				$this->pagePropertyDefinitionId =
					intval($rd["page_definition_tab_id"]);
				$this->pageId = intval($rd["page_id"]);
				$this->folderId = intval($rd["folder_id"]);
				$this->applicationDefinitionId = intval($rd["application_definition_id"]);
			}

			// Select it

			// TODO: when the list definition chances and a sub-folder is
			// required mark this in the list item definition (HAS_FOLDER or
			// something like that). Then is it is easliy decided if the sub
			// folder needs to be created at this point.
			// Or delegate it to list item creation.

			// "Borrow" the read access roles form the host page.
			$this->roles = new \Scrivo\RoleSet();

			$sth = $context->connection->prepare(
				"SELECT role_id FROM object_role
				WHERE instance_id = :instId AND page_id = :pageId");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":pageId", $pageId, \PDO::PARAM_INT);

			$sth->execute();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$this->roles[] = intval($rd["role_id"]);
			}

			$this->listPids = array();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
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
			case "items": return $this->getItems(0);
		}
		throw new \Scrivo\SystemException("No such get-property '$name'.");
	}

	/**
	 * Insert new list object data into the database. A list record is assumed
	 * to exists if page exists and is created silently.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function insert() {

		$this->id = $this->context->connection->generateId();

		$sth = $this->context->connection->prepare(
			"INSERT INTO item_list (
				instance_id, item_list_id, page_definition_tab_id, page_id,
				version, folder_id
			) VALUES (
				:instId, :id, :pagePropDefId, :pageId,
				0, :folderId
			)");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
		$sth->bindValue(
			":pagePropDefId", $this->pagePropertyDefinitionId, \PDO::PARAM_INT);
		$sth->bindValue(":pageId", $this->pageId, \PDO::PARAM_INT);
		$sth->bindValue(":folderId", $this->folderId, \PDO::PARAM_INT);

		$sth->execute();

	}

	/**
	 * Update list object data in the database. Only used to set the subfolder
	 * id.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function update() {

		$sth = $this->context->connection->prepare(
			"UPDATE item_list SET folder_id = :folderId
				WHERE instance_id = :instId AND item_list_id = :listId");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":folderId", $this->folderId, \PDO::PARAM_INT);
		$sth->bindValue(":listId", $this->id, \PDO::PARAM_INT);

		$sth->execute();

	}

	/**
	 * Retrieve an item list from the cache or database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $pageId The id of the page that hosts the list.
	 * @param int $defintionId The id of the page property definition.
	 *
	 * @throws \Scrivo\ApplicationException if the page was not readable for
	 *   the user defined in the context.
	 */
	public static function fetch(\Scrivo\Context $context, $pageId, $defintionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
		));

		// Try to retieve form cache
		$obj = null;
		$cId = "$pageId.$defintionId";
		if (isset($context->cache[$cId])) {
			// Get the list from cache and set the context.
			$obj = $context->cache[$cId];
		} else {
			// Load the list and set it in the cache.
			$obj = new \Scrivo\ItemList($context, $pageId, $defintionId);
			$context->cache[$cId] = $obj;
		}
		$obj->roles->checkReadPermission($context->principal);
		return $obj;
	}

	/**
	 * Retrieve all list items at root level.
	 *
	 * @return \Scrivo\ListItem[] An array if list items.
	public function getItems() {
		$res = $this->list[0];
		return $res ? $res : array();
	}
	 */

	/**
	 * Retrieve a sub list.
	 *
	 * @param int $parentId The id of the common parent for the list items.
	 *
	 * @return \Scrivo\ListItem[] An array if list items.
	 */
	public function getItems($parentId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		\Scrivo\ArgumentCheck::assert(
			$parentId, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		return $this->select($parentId);
	}

	/**
	 * Select list items from the database.
	 *
	 * @param int $parentId An optional parent id is case of a sub-list.
	 *
	 * @return \Scrivo\ListItem[id] An array containing the selected list items.
	 */
	private function select($parentId=0) {

		$cId = $this->pageId . "." . $this->pagePropertyDefinitionId;

		if (!isset($this->listPids[$parentId])) {
			$this->listPids[$parentId] = true;
			$this->context->cache[$cId] = $this;

		}

		if (isset($this->context->cache[$cId . "." . $parentId])) {
			return $this->context->cache[$cId . "." . $parentId];
		}

		try {
			$sth = $this->context->connection->prepare(
				"SELECT I.list_item_id, I.item_list_id, I.link_id,
					I.version, I.parent_id, I.sequence_no, I.list_item_definition_id,
					I.page_id, I.title, I.date_created, I.date_modified, I.date_online,
					I.date_offline
				FROM list_item I, item_list L
				WHERE I.instance_id = :instId AND L.instance_id = :instId
					AND L.item_list_id = I.item_list_id
					AND I.parent_id = :parentId
					AND L.page_id = :pageId
					AND L.page_definition_tab_id = :defId
					AND L.version = 0 AND I.version = 0
				ORDER BY parent_id, sequence_no");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":pageId", $this->pageId, \PDO::PARAM_INT);
			$sth->bindValue(":parentId", $parentId, \PDO::PARAM_INT);
			$sth->bindValue(
				":defId", $this->pagePropertyDefinitionId, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();
			$prps = null;

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				// TODO: get this out of the loop, its here because of the
				// list id
				if ($prps === null) {
					$prps = self::selectProperties(
						$this->context, intval($rd["item_list_id"]));
				}

				$id = intval($rd["list_item_id"]);

				$li = new \Scrivo\ListItem($rd,
					isset($prps[$id]) ? $prps[$id] : new \Scrivo\PropertySet());

				$res[$li->id] = $li;
			}

			$this->context->cache[$cId . "." . $parentId] = $res;

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select list item properties from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $listId The id of the list for which to retrieve the
	 *   properties.
	 *
	 * @return ListItemProperty[type] An array containing the selected list
	 *   item properties.
	 */
	private static function selectProperties(\Scrivo\Context $context,
			$listId) {

		$sth = $context->connection->prepare(
			"SELECT I.list_item_id, I.page_id,
				T.list_item_property_definition_id ID_DEF, T.type, T.php_key,
				IFNULL(V.value, '') value
			FROM
				list_item I
				JOIN list_item_property_definition T ON (
					I.instance_id = :instId AND T.instance_id = :instId
					AND I.list_item_definition_id = T.list_item_definition_id)
				LEFT JOIN list_item_property V ON (
					V.instance_id = :instId AND I.instance_id = :instId
					AND T.instance_id = :instId
					AND V.list_item_property_definition_id = T.list_item_property_definition_id
					AND V.list_item_id = I.list_item_id AND V.version = 0)
			WHERE
				I.instance_id = :instId
				AND I.item_list_id = :listId	AND I.version = 0"
//				.($itemId ? " AND I.list_item_id = :itemId" : "")
			);

		$context->connection->bindInstance($sth);
		$sth->bindValue(":listId", $listId, \PDO::PARAM_INT);
/*
		if ($itemId) {
			$sth->bindValue(":itemId", $itemId, \PDO::PARAM_INT);
		}
*/
		$sth->execute();

		$res = array();
		$id = -1;

		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

			$lid = intval($rd["list_item_id"]);

			if ($lid != $id) {
				$id = $lid;
				$res[$id] = new \Scrivo\PropertySet();
			}

			$li = ListItemProperty::create($rd);

			if ($li && !$li->phpSelector->equals(new \Scrivo\String(""))) {
				$res[$id]->{$li->phpSelector} = $li;
			}
		}

		return $res;
	}

	/**
	 * Create a linked page for a list item.
	 *
	 * @param \Scrivo\ListItem $item The list item for which to create the
	 *   linked page.
	 * @param \Scrivo\ListItemDefinition $def The list item defintion of the
	 *   list item for which to create the linked page.
	 *
	 * @return int The page id of the page to link.
	 */
	private function createLinkedPage(
			\Scrivo\ListItem $item, \Scrivo\ListItemDefinition $def) {

		// Load the reference page to copy the language and role settings.
		$mp = \Scrivo\Page::fetch($this->context, $this->pageId);

		// If no subfolder exists yet create one.
		if (!$this->folderId) {

			$propDef = \Scrivo\PagePropertyDefinition::fetch(
				$this->context, $this->pagePropertyDefinitionId);
			$tab = \Scrivo\PageDefinitionTab::fetch(
				$this->context, $propDef->pageDefinitionTabId);

			$folder = new \Scrivo\Page($this->context);
			$folder->parentId = $this->pageId;
			$folder->type = \Scrivo\Page::TYPE_SUB_FOLDER;
			$folder->title = $tab->title;
			$folder->insert();

			$this->folderId = $folder->id;
			$this->update();
		}

		// Create the sub-page.
		$sub = new \Scrivo\Page($this->context);
		$sub->definitionId = $def->pageDefinitionId;
		$sub->parentId = $this->folderId;
		$sub->type = \Scrivo\Page::TYPE_NON_NAVIGABLE_PAGE;
		$sub->title = $item->title;
		$sub->insert();

		return $sub->id;
	}

	/**
	 * Get the list item definition id using the phpSelector of the list
	 * item definition.
	 *
	 * @param \Scrivo\String $phpSelector The phpSelector of a list
	 *   item definition.
	 *
	 * @return int The list item definition id.
	 */
	private function getDefinitionId(\Scrivo\String $phpSelector) {

		$sth = $this->context->connection->prepare(
			"SELECT T.list_item_definition_id FROM list_item_definition T WHERE
				T.instance_id = :instId AND T.application_definition_id = :appDefId AND
				T.php_key = :sel");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(
			":appDefId", $this->applicationDefinitionId, \PDO::PARAM_INT);
		$sth->bindValue(":sel", $phpSelector, \PDO::PARAM_STR);

		$sth->execute();

		return intval($sth->fetchColumn());
	}

	/**
	 * Create a set of blank properties for a given list item definition.
	 *
	 * @param int $definitionId
	 *
	 * @return \Scrivo\PropertySet
	 */
	private function selectEmptyProperties($definitionId) {

		$sth = $this->context->connection->prepare(
			"SELECT
				0 list_item_id, null value, :pageId page_id,
				list_item_property_definition_id ID_DEF, type, php_key
			FROM list_item_property_definition
			WHERE instance_id = :instId AND list_item_definition_id = :sel");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $this->pageId, \PDO::PARAM_INT);
		$sth->bindValue(":sel", $definitionId, \PDO::PARAM_INT);

		$sth->execute();

		$res = new \Scrivo\PropertySet();

		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

			$li = ListItemProperty::create($rd);

			if ($li && !$li->phpSelector->equals(new \Scrivo\String(""))) {
				$res->{$li->phpSelector} = $li;
			}
		}

		return $res;
	}

	/**
	 * Update an existing list item property in the database.
	 *
	 * First the data fields of this user will be validated, then the data
	 * is updated in the database.
	 *
	 * @param \Scrivo\ListItemProperty $prp The list item property to update.
	 * @param int $itemId The id of the list item for which to update the
	 *    property.
	 *
	 * @throws \Scrivo\ApplicationException If one or more of the fields
	 *   contain invalid data.
	 */
	private function updateProperty(\Scrivo\ListItemProperty $prp, $itemId) {

		$sth = $this->context->connection->prepare(
			"DELETE FROM list_item_property WHERE instance_id = :instId AND
			list_item_id = :listItemId AND list_item_property_definition_id = :idDef AND
			version = 0");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":listItemId", $itemId, \PDO::PARAM_INT);
		$sth->bindValue(":idDef", $prp->definitionId, \PDO::PARAM_STR);

		$sth->execute();

		$sth = $this->context->connection->prepare(
			"INSERT INTO list_item_property (
				instance_id, list_item_id, version, list_item_property_definition_id,
				page_id, value
			 ) VALUES (:instId, :listItemId, 0, :idDef, :pageId, :data)");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":listItemId", $itemId, \PDO::PARAM_INT);
		$sth->bindValue(":idDef", $prp->definitionId, \PDO::PARAM_STR);
		$sth->bindValue(":pageId", $prp->pageId, \PDO::PARAM_STR);
		$sth->bindValue(":data", $prp->data, \PDO::PARAM_STR);

		$sth->execute();
	}

	/**
	 * Insert new list item object data into the database.
	 *
	 * First it is checked if the data of this list item object can be inserted
	 * into the database, then the data is inserted into the database. If no id
	 * was set a new object id is generated.
	 *
	 * @param \Scrivo\ListItem $item The list item to insert into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function insertItem(\Scrivo\ListItem $item) {

		// Create a sub-page if necessary
		$def = \Scrivo\ListItemDefinition::fetch(
			$this->context, $item->definitionId);
		$linkedPageId = 0;
		if ($def->pageDefinitionId) {
			$linkedPageId = $this->createLinkedPage($item, $def);
		}

		$id = $this->context->connection->generateId();

		$sth = $this->context->connection->prepare(
			"INSERT INTO list_item (
				instance_id, list_item_id, item_list_id, link_id,
				version, parent_id, sequence_no, list_item_definition_id,
				page_id, title, date_created, date_modified,
				date_online, date_offline
			) VALUES (
				:instId, :id, :listId, :pageId,
				:version, :parentId, :sequenceNo, :definitionId,
				:linkedPageId, :title, now(), now(),
				:dateOnline, :dateOffline
			)");

		$this->context->connection->bindInstance($sth);

		$sth->bindValue(":id", $id, \PDO::PARAM_INT);
		$sth->bindValue(":listId", $item->listId, \PDO::PARAM_INT);
		$sth->bindValue(":pageId", $item->pageId, \PDO::PARAM_INT);
		$sth->bindValue(":version", $item->version, \PDO::PARAM_INT);
		$sth->bindValue(":parentId", $item->parentId, \PDO::PARAM_INT);
		$sth->bindValue(":sequenceNo", 0, \PDO::PARAM_INT);
		$sth->bindValue(":definitionId",
			$item->definitionId, \PDO::PARAM_INT);
		// TODO: no item-> ???
		$sth->bindValue(":linkedPageId", $linkedPageId, \PDO::PARAM_INT);
		$sth->bindValue(":title", $item->title, \PDO::PARAM_STR);
		$sth->bindValue(":dateOnline",
			$item->dateOnline->format("Y-m-d H:i:s"), \PDO::PARAM_STR);
		$sth->bindValue(":dateOffline", $item->dateOffline
			? $item->dateOffline->format("Y-m-d H:i:s")
			: null, \PDO::PARAM_STR);

		$sth->execute();

		foreach ($item->properties as $prp) {
			$this->updateProperty($prp, $id);
		}

		\Scrivo\SequenceNo::position($this->context, "list_item",
			array("parent_id", "item_list_id"), $id, \Scrivo\SequenceNo::MOVE_FIRST);

	}

	/**
	 * Update existing list item object data in the database.
	 *
	 * First it is checked if the data of this list item object can be updated
	 * in the database, then the data is updated in the database.
	 *
	 * @param \Scrivo\ListItem $item The list item to update in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function updateItem(\Scrivo\ListItem $item) {

		// Create a sub-page if necessary
		$def = \Scrivo\ListItemDefinition::fetch(
			$this->context, $item->definitionId);
		$linkedPageId = $item->linkedPageId;
		if (!$linkedPageId && $def->pageDefinitionId) {
			$linkedPageId = $this->createLinkedPage($item, $def);
		}

		$sth = $this->context->connection->prepare(
			"UPDATE list_item SET
				parent_id = :parentId, title = :title, link_id = :linkedPage,
				date_online = :dateOnline, date_offline = :dateOffline
			WHERE instance_id = :instId AND list_item_id = :id");

		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":id", $item->id, \PDO::PARAM_INT);

		$sth->bindValue(":parentId", $item->parentId, \PDO::PARAM_INT);
		$sth->bindValue(":linkedPage", $linkedPageId, \PDO::PARAM_INT);
		$sth->bindValue(":title", $item->title, \PDO::PARAM_STR);
		$sth->bindValue(":dateOnline",
			$item->dateOnline->format("Y-m-d H:i:s"), \PDO::PARAM_STR);
		$sth->bindValue(":dateOffline", $item->dateOffline
			? $item->dateOffline->format("Y-m-d H:i:s")
			: null, \PDO::PARAM_STR);

		$sth->execute();

		foreach ($item->properties as $prp) {
			$this->updateProperty($prp, $item->id);
		}

	}

	/**
	 * Create a new list item to insert in the database.
	 *
	 * @param \Scrivo\String $phpSelector The selector of the list item
	 *   definition of the list item to create.
	 *
	 * @return \Scrivo\ListItem
	 */
	public function newItem(\Scrivo\String $phpSelector) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {
			$defId = $this->getDefinitionId($phpSelector);

			return new \Scrivo\ListItem(
				array(
					"list_item_id" => 0,
					"item_list_id" => $this->id,
					"page_id" => $this->pageId,
					"version" => 0,
					"parent_id" => 0,
					"sequence_no" => 0,
					"list_item_definition_id" => $defId,
					"link_id" => 0,
					"title" => new \Scrivo\String(""),
					"date_created" => "now",
					"date_modified" => "now",
					"date_online" => "now",
					"date_offline" => null
				),
				self::selectEmptyProperties($defId)
			);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Save list item data to the database.
	 *
	 * @param \Scrivo\ListItem $item The list item to save, either an existing
	 *   item updated item or a new one craeted with the newItem method.
	 *
	 * @throws \Scrivo\ApplicationException if the data is not accessible or
	 *   it is not possible to insert or update the list item data.
	 */
	public function saveItem(\Scrivo\ListItem $item) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {
			$this->context->checkPermission(
				\Scrivo\AccessController::WRITE_ACCESS, $this->pageId);

			if ($item->id === 0) {
				$this->insertItem($item);
			} else {
				$this->updateItem($item);
			}

			$this->removeFromCache();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Delete existing list item data from the database.
	 *
	 * First it is is checked if it's possible to delete list item data,
	 * then the list item data including its dependencies is deleted from
	 * the database.
	 *
	 * @param int $id The object id of the list item to delete.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the list item data.
	 */
	public function deleteItem($id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// TODO test delete when there are children
			$this->context->checkPermission(
				\Scrivo\AccessController::WRITE_ACCESS, $this->pageId);

			// TODO: delete sub page

			$sth = $this->context->connection->prepare(
				"DELETE FROM list_item
					WHERE instance_id = :instId AND list_item_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			$sth = $this->context->connection->prepare(
				"DELETE FROM list_item_property
					WHERE instance_id = :instId AND list_item_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			$this->removeFromCache();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Move a list item to another position amongst its siblings.
	 *
	 * @param int $id The object id of the list item to move.
	 * @param int $dir Position or direction of the move,
	 *      see \Scrivo\SequenceNo:::MOVE_*
	 */
	public function moveItem($id, $dir=\Scrivo\SequenceNo::MOVE_DOWN) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
				array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
				array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->context->checkPermission(
				\Scrivo\AccessController::WRITE_ACCESS, $this->pageId);

		\Scrivo\SequenceNo::position($this->context, "list_item",
			array("parent_id", "item_list_id"), $id, $dir);

		$this->removeFromCache();
	}

	/**
	 * Remove this list (including all sub lists from the cache).
	 */
	private function removeFromCache() {

		$cId = $this->pageId . "." . $this->pagePropertyDefinitionId;
		if ($this->listPids) {
			$listIds = array_keys($this->listPids);
			foreach ($listIds as $id) {
				unset($this->context->cache[$cId . "." . $id]);
			}
		}
		unset($this->context->cache[$cId]);
	}


}

?>