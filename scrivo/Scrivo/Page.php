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
 * $Id: Page.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Page class.
 */

namespace Scrivo;

/**
 * A Scrivo Page is most essential Scrivo entity. A Scrivo page models a HTML
 * Page (most of the times). The idea is that a Web site is a set of pages. The
 * dynamic content that should be presented on these web pages is contained by
 * Scrivo Page objects.
 *
 * Therefore a Scrivo page contains some of the standard fields that are
 * typical for a HTML page suchs as the page title, description and keywords
 * along with some managerial (CMS) information as online and ofline date.
 *
 * What other content should be displayed on the page is determined by its
 * page defintion. Each page is constructed using a a page defintion and this
 * defintion holds the defintions for all other data (properties, texts,
 * lists, applications) that can added to the page.
 *
 * The Scrivo editor interface provides the means to the editor to create
 * pages and fill in the page properties.
 *
 * How the content that is assigned to the page should be displayed on the
 * page is determined by the page template. The page defintion has a member
 * that refers to the page template: a user defined script that renders the
 * actual page.
 *
 * @property-read int $id The page id (DB key).
 * @property-read \Scrivo\PageDefinition $definition The page definition of
 *    this page.
 * @property-read booleand $isOnline If this page is online or not.
 * @property-read \Scrivo\Language $language The main language for the
 *    page (&lt;html lang&gt;).
 * @property-read \Scrivo\PropertySet $properties The page properties as a
 *    PHP object in which the members correspond with the PHP selector names.
 * @property-read \Scrivo\RoleSet $roles The attached roles.
 * @property-read \Scrivo\PageSet $children The child pages of this page.
 * @property-read \Scrivo\PageSet $navigableChildren The navigable child 
 *    pages of this page.
 * @property-read \Scrivo\PageSet $path The parent pages of this page.
 * @property-read \DateTime $dateCreated The date/time that this page was
 *    created.
 * @property-read \DateTime $dateModified The last date/time that this page
 *    was modified.
 * @property \Scrivo\Context $context A Scrivo context of this page.
 * @property boolean $hasStaging Setting to indicate if the page can be staged.
 * @property int $parentId The id of the parent page.
 * @property int $type The page type: one out of the Page::TYPE_* constant
 *    values.
 * @property \Scrivo\String $title The page title (&lt;title&gt;).
 * @property \Scrivo\String $description The page description
 *    (&lt;description&gt;).
 * @property \Scrivo\String $keywords The keywords for this page
 *    (&lt;keywords&gt;).
 * @property \Scrivo\String $javascript A javascript script for this page
 *    (&lt;script&gt;).
 * @property \Scrivo\String $stylesheet Additional CSS syle rules for this
 *    page (&lt;stylesheet&gt;).
 * @property \DateTime $dateOnline The date/time this page need to go online.
 * @property \DateTime $dateOffline The date/time this page need to go offline.
 * @property-write int $definitionId The id of the page template.
 * @property-write int $languageId The id of the main language for the page
 *    (&lt;html lang&gt;).
 */
class Page {

	/**
	 * Value indicating a navigation item (page that only functions as a node).
	 */
	const TYPE_NAVIGATION_ITEM = 0;

	/**
	 * Value indicating a page that should be shown in the site menu.
	 */
	const TYPE_NAVIGABLE_PAGE = 1;

	/**
	 * Value indicating a page that should not be shown in the site menu.
	 */
	const TYPE_NON_NAVIGABLE_PAGE = 2;

	/**
	 * Value indicating an extra node to hold automatically generated pages
	 * that are linked to list items.
	 */
	const TYPE_SUB_FOLDER = 4;

	/**
	 * Value indicating an application: a page that has no functionality as
	 * a page but hosts an application in the scrivo user interface.
	 */
	const TYPE_APPLICATION = 5;

	/**
	 * The page id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * The current version of the page: -1: scratch version, 0 live version,
	 * 1 and up versions.
	 * @var int
	 */
	private $version = 0;

	/**
	 * Setting to indicate if the page can be staged.
	 * @var boolean
	 */
	private $hasStaging = 0;

	/**
	 * The id of the parent page.
	 * @var int
	 */
	private $parentId = 0;

	/**
	 * The page type: one out of the Page::TYPE_* constant values.
	 * @var int
	 */
	private $type = 0;

	/**
	 * The id of the page template.
	 * @var int
	 */
	private $definitionId = 0;

	/**
	 * The id the main language for the page (&lt;html lang&gt;).
	 * @var int
	 */
	private $languageId = 0;

	/**
	 * The page title (&lt;title&gt;).
	 * @var \Scrivo\String
	 */
	private $title = null;

	/**
	 * The page description (&lt;description&gt;).
	 * @var \Scrivo\String
	 */
	private $description = null;

	/**
	 * The keywords for this page (&lt;keywords&gt;).
	 * @var \Scrivo\String
	 */
	private $keywords = null;

	/**
	 * A javascript script for this page (&lt;script&gt;).
	 * @var \Scrivo\String
	 */
	private $javascript = null;

	/**
	 * Additional CSS syle rules for this page (&lt;stylesheet&gt;).
	 * @var \Scrivo\String
	 */
	private $stylesheet = null;

	/**
	 * The date/time that this page was created.
	 * @var \DateTime
	 */
	private $dateCreated = null;

	/**
	 * The last date/time that this page was modified.
	 * @var \DateTime
	 */
	private $dateModified = null;

	/**
	 * The date/time this page need to go online.
	 * @var \DateTime
	 */
	private $dateOnline = null;

	/**
	 * The date/time this page need to go offline.
	 * @var \DateTime
	 */
	private $dateOffline = null;

	/**
	 * The page properties as a PHP object in which the members correspond
	 * with the PHP selector names.
	 * @var \Scrivo\PropertySet
	 */
	private $properties = null;

	/**
	 * The child pages of this page.
	 * @var \Scrivo\PageSet
	 */
	private $children = null;

	/**
	 * The parent pages of this page.
	 * @var \Scrivo\PageSet
	 */
	private $path = null;

	/**
	 * The attached roles.
	 * @var \Scrivo\RoleSet
	 */
	private $roles = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty page object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->id = 0;
			$this->version = 0;
			$this->hasStaging = false;
			$this->parentId = 0;
			$this->type = 0;
			$this->definitionId = 0;
			$this->languageId = 0;
			$this->title = new \Scrivo\String();
			$this->description = new \Scrivo\String();
			$this->keywords = new \Scrivo\String();
			$this->javascript = new \Scrivo\String();
			$this->stylesheet = new \Scrivo\String();
			$this->dateCreated = new \DateTime("now");
			$this->dateModified = new \DateTime("now");
			$this->dateOnline = new \DateTime("now");
			$this->dateOffline = null;

			$this->properties = null;

			$this->roles = new \Scrivo\RoleSet();

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
			case "hasStaging": return $this->hasStaging;
			case "parentId": return $this->parentId;
			case "type": return $this->type;
			case "definition": return $this->definitionId ?
				\Scrivo\PageDefinition::fetch($this->context, $this->definitionId)
				: new \Scrivo\PageDefinition($this->context);
			case "language": return $this->languageId ?
				\Scrivo\Language::fetch($this->context, $this->languageId)
				: new \Scrivo\Language($this->context);
			case "title": return $this->title;
			case "description": return $this->description;
			case "keywords": return $this->keywords;
			case "javascript": return $this->javascript;
			case "stylesheet": return $this->stylesheet;
			case "dateCreated": return $this->dateCreated;
			case "dateModified": return $this->dateModified;
			case "dateOnline": return $this->dateOnline;
			case "dateOffline": return $this->dateOffline;
			case "isOnline": return $this->getIsOnline();
			case "properties": return $this->getProperties();
			case "roles": return $this->roles;
			case "children": return $this->getChildren();
			case "navigableChildren": return $this->getNavigableChildren();
			case "path": return $this->getPath();
			case "context": return $this->context;
		}
		throw new \Scrivo\SystemException("No such property-get '$name'.");
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
			case "hasStaging": $this->setHasStaging($value); return;
			case "parentId": $this->setParentPageId($value); return;
			case "type": $this->setType($value); return;
			case "definitionId": $this->setDefinitionId($value); return;
			case "languageId": $this->setLanguageId($value); return;
			case "title": $this->setTitle($value); return;
			case "description": $this->setDescription($value); return;
			case "keywords": $this->setKeywords($value); return;
			case "javascript": $this->setJavascript($value); return;
			case "stylesheet": $this->setStylesheet($value); return;
			case "dateOnline": $this->setDateOnline($value); return;
			case "dateOffline": $this->setDateOffline($value); return;
			case "context": $this->setContext($value); return;
		}
		throw new \Scrivo\SystemException("No such property-set '$name'.");
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

		$this->id = intval($rd["page_id"]);
		$this->version = intval($rd["version"]);
		$this->hasStaging = intval($rd["has_staging"]) == 1 ? true : false;
		$this->parentId = intval($rd["parent_id"]);
		$this->type = intval($rd["type"]);
		$this->definitionId = intval($rd["page_definition_id"]);
		$this->languageId = intval($rd["language_id"]);
		$this->title = new \Scrivo\String($rd["title"]);
		$this->description = new \Scrivo\String($rd["description"]);
		$this->keywords = new \Scrivo\String($rd["keywords"]);
		$this->javascript = new \Scrivo\String($rd["javascript"]);
		$this->stylesheet = new \Scrivo\String($rd["stylesheet"]);
		$this->dateCreated = new \DateTime($rd["date_created"]);
		$this->dateModified = new \DateTime($rd["date_modified"]);
		$this->dateOnline = new \DateTime($rd["date_online"]);
		$this->dateOffline = $rd["date_offline"] == null
			? null : new \DateTime($rd["date_offline"]);

		$this->context = $context;
	}

	/**
	 * Get this pages's property list.
	 *
	 * @return object This pages's property list.
	 */
	private function getProperties() {
		if ($this->properties === null) {
			self::selectProperties($this->context, array($this->id => $this));
			$this->context->cache[$this->id] = $this;
		}
		return $this->properties;
	}

	/**
	 * Get the child pages of this page.
	 *
	 * @return \Scrivo\PageSet The child pages of the page.
	 */
	private function getChildren() {
		if ($this->children === null) {
			$this->children = self::selectChildren($this);
			$this->context->cache[$this->id] = $this;
		}
		return $this->children;
	}

	/**
	 * Get the navigable child pages of this page.
	 *
	 * @return \Scrivo\PageSet The navigable child pages of the page.
	 */
	private function getNavigableChildren() {
		$res = array();
		foreach ($this->getChildren() as $chld) {
			if ($chld->type === self::TYPE_NAVIGABLE_PAGE
					|| $chld->type === self::TYPE_NAVIGATION_ITEM) {
				$res[] = $chld;
			}
		}
		return $res;
	}
	
	/**
	 * Get the child pages of this page.
	 *
	 * @return \Scrivo\PageSet All pages above the current page.
	 */
	private function getPath() {
		if ($this->path === null) {
			$this->path = self::selectPath($this);
			$this->context->cache[$this->id] = $this;
		}
		return $this->path;
	}

	/**
	 * Check if this page is online.
	 *
	 * @return boolean True if this page is online else false.
	 */
	private function getIsOnline() {
		$n = new \DateTime();
		$online = true;
		if ($n < $this->dateOnline) {
			$online = false;
		} else {
			if ($this->dateOffline) {
				if ($n > $this->dateOffline) {
					$online = false;
				}
			}
		}
		return $online;
	}

	/**
	 * Set the setting to indicate if a page can be staged.
	 *
	 * @param boolean $hasStaging Setting to indicate if a page can be staged.
	 */
	private function setHasStaging($hasStaging) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_BOOLEAN)
		));
		$this->hasStaging = $hasStaging;
	}

	/**
	 * Set the id of the parent page.
	 *
	 * @param int $parentId The id of the parent page.
	 */
	private function setParentPageId($parentId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		$this->parentId = $parentId;
	}

	/**
	 * Set the page type: one out of the Page::TYPE_* constant values.
	 *
	 * @param int $type The page type: one out of the Page::TYPE_* constant
	 *    values.
	 */
	private function setType($type) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER, array(
				self::TYPE_NAVIGATION_ITEM,	self::TYPE_NAVIGABLE_PAGE,
				self::TYPE_NON_NAVIGABLE_PAGE, self::TYPE_SUB_FOLDER,
				self::TYPE_APPLICATION))
		));
		$this->type = $type;
	}

	/**
	 * Set the id of the page template.
	 *
	 * @param int $definitionId The id ot the page template.
	 */
	private function setDefinitionId($definitionId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		if (!$this->definitionId) {
			$this->definitionId = $definitionId;
		} else {
			throw new \Scrivo\SystemException("Can't reset the page template");
		}
	}

	/**
	 * Set the id the main language for the page (&lt;html lang&gt;).
	 *
	 * @param int $languageId The id the main language for the page
	 *    (&lt;html lang&gt;).
	 */
	private function setLanguageId($languageId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		$this->languageId = $languageId;
	}

	/**
	 * Set The page title (&lt;title&gt;).
	 *
	 * @param \Scrivo\String $title The page title (&lt;title&gt;).
	 */
	private function setTitle(\Scrivo\String $title) {
		$this->title = $title;
	}

	/**
	 * Set the page description (&lt;description&gt;).
	 *
	 * @param \Scrivo\String $description The page description
	 *   (&lt;description&gt;).
	 */
	private function setDescription(\Scrivo\String $description) {
		$this->description = $description;
	}

	/**
	 * Set the keywords for this page (&lt;keywords&gt;).
	 *
	 * @param \Scrivo\String $keywords The keywords for this page
	 *   (&lt;keywords&gt;).
	 */
	private function setKeywords(\Scrivo\String $keywords) {
		$this->keywords = $keywords;
	}

	/**
	 * Set a javascript script for this page (&lt;script&gt;).
	 *
	 * @param \Scrivo\String $javascript A javascript script for this
	 *    page (&lt;script&gt;).
	 */
	private function setJavascript(\Scrivo\String $javascript) {
		$this->javascript = $javascript;
	}

	/**
	 * Set additional CSS syle rules for this page (&lt;stylesheet&gt;).
	 *
	 * @param \Scrivo\String $stylesheet Additional CSS syle rules for this
	 *   page (&lt;stylesheet&gt;).
	 */
	private function setStylesheet(\Scrivo\String $stylesheet) {
		$this->stylesheet = $stylesheet;
	}

	/**
	 * Set the date/time this page needs to go online.
	 *
	 * @param \DateTime $dateOnline The date/time this page needs to go online.
	 */
	private function setDateOnline(\DateTime $dateOnline) {
		$this->dateOnline = $dateOnline;
	}

	/**
	 * Set the date/time this page need to go offline.
	 *
	 * @param \DateTime $dateOffline The date/time this page need to go offline.
	 */
	private function setDateOffline(\DateTime $dateOffline=null) {
		$this->dateOffline = $dateOffline;
	}

	/**
	 * Set the page context.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	private function setContext(\Scrivo\Context $context) {
		$this->context = $context;
	}

	/**
	 * Select the page properties from the database.
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 * @param array $pages the set of pages for which to retrieve the
	 *    properties.
	 */
	private static function selectProperties($context, array $pages) {

		$ids = implode(",", array_keys($pages));

		$sth = $context->connection->prepare(
			"SELECT P.page_id PAGE_ID, T.type, T.php_key,
				IFNULL(D.value, '') value, '' VALUE2,
				T.page_property_definition_id ID_DEF
			FROM page P, page_property_definition T
				LEFT JOIN page_property D ON (
				D.instance_id = :instId AND T.instance_id = :instId
				AND T.page_property_definition_id = D.page_property_definition_id
				AND D.page_id in ($ids) AND D.version = 0)
			WHERE T.instance_id = :instId AND P.instance_id = :instId AND
				P.page_definition_id = T.page_definition_id AND P.page_id in ($ids)
				AND P.version = 0
			UNION
			SELECT P.page_id PAGE_ID, 'html_text_tab' type,
				T.php_key, IFNULL(D.html, '') value,
				IFNULL(D.raw_html, '') VALUE2,
				T.page_definition_tab_id ID_DEF
			FROM page P, page_definition_tab T
				LEFT JOIN page_property_html D ON (
				D.instance_id = :instId AND T.instance_id = :instId
				AND T.page_definition_tab_id = D.page_definition_tab_id
				AND D.page_id in ($ids) AND D.version = 0)
			WHERE T.instance_id = :instId AND P.instance_id = :instId AND
				P.page_definition_id = T.page_definition_id AND P.page_id in ($ids)
				AND P.version = 0 AND T.application_definition_id = 0
			UNION
			SELECT D.page_id PAGE_ID, 'application_tab' type,
				T.php_key,	A.type value, A.application_definition_id VALUE2,
				T.page_definition_tab_id ID_DEF
			FROM page D, page_definition_tab T, application_definition A
			WHERE D.instance_id = :instId AND T.instance_id = :instId
				AND A.instance_id = :instId
				AND T.page_definition_id = D.page_definition_id
				AND A.application_definition_id = T.application_definition_id
				AND T.application_definition_id <> 0
				AND D.page_id in ($ids) AND D.version = 0");

		$context->connection->bindInstance($sth);

		$sth->execute();

		foreach (array_keys($pages) as $id) {
			$pages[$id]->properties = new \Scrivo\PropertySet();
		}

		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

			$pageId = intval($rd["PAGE_ID"]);

			$li = PageProperty::create($pages[$pageId],	$rd);

			if (!$li->phpSelector->equals(new \Scrivo\String(""))) {
				$pages[$pageId]->properties->{$li->phpSelector} = $li;
			}

		}

	}

	/**
	 * Select the roles for this page.
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 * @param array $pages the set of pages for which to retrieve the
	 *    properties.
	 */
	private static function selectRoles($context, array $pages) {

		$ids = implode(",", array_keys($pages));

		$sth = $context->connection->prepare(
			"SELECT page_id, role_id FROM object_role
			WHERE instance_id = :instId AND page_id in ($ids)");

		$context->connection->bindInstance($sth);

		$sth->execute();

		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

			$pages[intval($rd["page_id"])]->roles[] =
				intval($rd["role_id"]);

		}

	}

	/**
	 * Check if the page data can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible,
	 *   one or more of the fields contain invalid data or some other business
	 *   rule is not met.
	 */
	private function validateInsert() {

		if ($this->parentId) {

			// If there is parent page copy relevant properties for the parent
			// page.
			$parent = \Scrivo\Page::fetch($this->context, $this->parentId);

			$this->context->checkPermission(
				\Scrivo\AccessController::WRITE_ACCESS, $this->parentId);

			if ($this->languageId === 0) {
				$this->languageId = $parent->languageId;
			}

			$this->hasStaging == $parent->hasStaging;

		} else {

			// If we're trying to insert a new root, check if there there is
			// none yet.
			$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

			$sth = $this->context->connection->prepare(
				"SELECT COUNT(*) FROM page
					WHERE instance_id = :instId AND parent_id = 0");

			$this->context->connection->bindInstance($sth);

			$sth->execute();

			if ($sth->fetchColumn(0) > 0) {
				throw new \Scrivo\SystemException(
					"Trying to create a new root page");
			}

		}

	}

	/**
	 * Insert a new page into the database.
	 *
	 * First the data fields of this page will be validated. If no id
	 * is set a new object id is generated. Then the data is inserted into to
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If one or more of the fields
	 *   contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			if (!$this->id) {
				$this->id = $this->context->connection->generateId();
			}

			$sth = $this->context->connection->prepare(
				"INSERT INTO page (
					instance_id, page_id, version, has_staging, parent_id,
					sequence_no, type, page_definition_id, language_id, title,
					description, keywords, javascript, stylesheet,
					date_created, date_modified, date_online, date_offline
				) VALUES (
					:instId, :id, :version, :hasStaging, :parentId,
					0, :type, :definitionId, :languageId, :title,
					:description, :keywords, :javascript, :stylesheet,
					now(), now(), :dateOnline, :dateOffline
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":version", $this->version, \PDO::PARAM_INT);
			$sth->bindValue(":hasStaging", $this->hasStaging, \PDO::PARAM_INT);
			$sth->bindValue(
				":parentId", $this->parentId, \PDO::PARAM_INT);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_INT);
			$sth->bindValue(
				":definitionId", $this->definitionId, \PDO::PARAM_INT);
			$sth->bindValue(":languageId", $this->languageId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);
			$sth->bindValue(":keywords", $this->keywords, \PDO::PARAM_STR);
			$sth->bindValue(":javascript", $this->javascript, \PDO::PARAM_STR);
			$sth->bindValue(":stylesheet", $this->stylesheet, \PDO::PARAM_STR);
			$sth->bindValue(":dateOnline",
				$this->dateOnline->format("Y-m-d H:i:s"), \PDO::PARAM_STR);
			$sth->bindValue(":dateOffline", $this->dateOffline
				? $this->dateOffline->format("Y-m-d H:i:s")
				: null, \PDO::PARAM_STR);

			$sth->execute();

			if ($this->type != \Scrivo\Page::TYPE_SUB_FOLDER) {
				\Scrivo\SequenceNo::position($this->context, "page",
					"parent_id", $this->id, \Scrivo\SequenceNo::MOVE_LAST);
			}

			ObjectRole::set($this->context, $this->id,
				ObjectRole::select($this->context, $this->parentId));

			// TODO **************
			// $this->_commit_subfolder();
			// $this->set_pretty_path();

			unset($this->context->cache[$this->parentId]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if the page data can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible,
	 *   one or more of the fields contain invalid data or some other business
	 *   rule is not met.
	 */
	private function validateUpdate() {

		$this->context->checkPermission(
			\Scrivo\AccessController::WRITE_ACCESS, $this->id);

		try {
			$newPath = self::selectPath($this);
		} catch (\Scrivo\SystemException $e) {
			throw new \Scrivo\ApplicationException(
				"Can't move a page underneath itself");
		}

	}

	/**
	 * Update an existing page in the database.
	 *
	 * First the data fields of this user will be validated, then the data
	 * is updated in to database.
	 *
	 * @throws \Scrivo\ApplicationException If one or more of the fields
	 *   contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$isParentWritable = false;
			if ($this->parentId) {
				try {
					$this->context->checkPermission(
						\Scrivo\AccessController::WRITE_ACCESS,
						$this->parentId);
					$isParentWritable = true;
				} catch (\Scrivo\ApplicationException $e) {}
			}

			$sth = $this->context->connection->prepare(
				"UPDATE page SET
					version = :version, has_staging = :hasStaging,
					parent_id = :parentId,
					type = :type, page_definition_id = :definitionId,
					language_id = :languageId, title = :title,
					description = :description, keywords = :keywords,
					javascript = :javascript, stylesheet = :stylesheet,
					date_online = :dateOnline, date_offline = :dateOffline
				WHERE instance_id = :instId AND page_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(":version", $this->version, \PDO::PARAM_INT);
			$sth->bindValue(":hasStaging", $this->hasStaging, \PDO::PARAM_INT);
			$sth->bindValue(
				":parentId", $this->parentId, \PDO::PARAM_INT);
			$sth->bindValue(":type", $this->type, \PDO::PARAM_INT);
			$sth->bindValue(
				":definitionId", $this->definitionId, \PDO::PARAM_INT);
			$sth->bindValue(":languageId", $this->languageId, \PDO::PARAM_INT);
			$sth->bindValue(":title", $this->title, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);
			$sth->bindValue(":keywords", $this->keywords, \PDO::PARAM_STR);
			$sth->bindValue(":javascript", $this->javascript, \PDO::PARAM_STR);
			$sth->bindValue(":stylesheet", $this->stylesheet, \PDO::PARAM_STR);
			$sth->bindValue(":dateOnline",
				$this->dateOnline->format("Y-m-d H:i:s"), \PDO::PARAM_STR);
			$sth->bindValue(":dateOffline", $this->dateOffline
				? $this->dateOffline->format("Y-m-d H:i:s")
				: null, \PDO::PARAM_STR);

			$sth->execute();

			self::touch($this->context, $this->id);

			if ($this->type == \Scrivo\Page::TYPE_SUB_FOLDER) {
				\Scrivo\SequenceNo::position($this->context, "page",
					"parent_id", $this->id, 0);
			}

			// TODO **************
			// $this->_commit_subfolder();
			// $this->set_pretty_path();

			unset($this->context->cache[$this->id]);
			unset($this->context->cache[$this->parentId]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of page object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the page definition to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {

		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS, $id);

		// Is it a labeled page?
		$sth = $context->connection->prepare(
			"SELECT COUNT(*) FROM id_label
			WHERE instance_id = :instId AND id = :id");

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		$sth->execute();

		if ($sth->fetchColumn(0) > 0) {
			throw new \Scrivo\ApplicationException(
				"Trying to delete a labelled page");
		}

		// Check the child pages.
		$sth = $context->connection->prepare(
			"SELECT page_id, type FROM page WHERE instance_id = :instId
				AND parent_id = :id AND (has_staging+version) = 0");

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		$sth->execute();

		$folders = array();
		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
			if ($rd["type"] == \Scrivo\Page::TYPE_SUB_FOLDER) {
				// Try to delete it: will generate an exception if not empty.
				\Scrivo\Page::delete($context, intval($rd["page_id"]));
			} else {
				// Throw up on first 'normal' child page found.
				throw new \Scrivo\ApplicationException(
					"Trying to delete a page with child pages");
			}
		}
	}

	/**
	 * Touch (update modification time) a page.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The id of the page to touch.
	 */
	public static function touch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {

			$sth = $context->connection->prepare(
				"UPDATE page SET date_modified = NOW()
				WHERE instance_id = :instId AND page_id = :id");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			unset($context->cache[$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Delete an existing page from the database.
	 *
	 * First it is is checked if it's possible to delete this page
	 * then the page data including its dependecies is deleted from
	 * the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The id of the page to delete.
	 *
	 * @throws \Scrivo\ApplicationException If it is not possible to delete
	 *   this page.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			$p = \Scrivo\Page::fetch($context, $id);

			foreach (array("object_role" => "page_id",
					"page_property" => "page_id",
					"page_property_html" => "page_id",
					"item_list" => "page_id",
					"list_item" => "page_id",
					"list_item_property" => "page_id",
					"id_label" => "id",
					"page" => "page_id") as $table => $keyFld) {

				$sth = $context->connection->prepare(
					"DELETE FROM $table
					WHERE instance_id = :instId AND $keyFld = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();
			}

			unset($context->cache[$id]);
			unset($context->cache[$p->parentId]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Move a page one position up or down amongst its siblings.
	 *
	 * @param int $dir Direction of the move, see \Scrivo\SequenceNo:::MOVE_*
	 */
	function move($dir=\Scrivo\SequenceNo::MOVE_DOWN) {

		if ($this->type == \Scrivo\Page::TYPE_SUB_FOLDER) {
			throw new \Scrivo\SystemException("Can't move subfolders");
		}

		$this->context->checkPermission(
			\Scrivo\AccessController::WRITE_ACCESS, $this->id);

		\Scrivo\SequenceNo::position($this->context, "page",
			"parent_id", $this->id, $dir);

		unset($this->context->cache[$this->parentId]);

	}

	/**
	 * Retrieve a page from the database or cache.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id An object id of a page.
	 *
	 * @throws \Scrivo\ApplicationException if the page was not readable for
	 *   the user defined in the context.
	 */
	public static function fetch(\Scrivo\Context $context, $id=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);
		try {

			// Try to retieve form cache
			$p = null;
			if (isset($context->cache[$id])) {
				// Set the page from cache and set the context.
				$p = $context->cache[$id];
				$p->context = $context;
			} else {

				$sth = $context->connection->prepare(
					"SELECT page_id, version, has_staging,
						parent_id, sequence_no, type,
						page_definition_id, language_id, title, description, keywords,
						javascript, stylesheet,	date_created, date_modified, date_online,
						date_offline
					FROM page
					WHERE instance_id = :instId AND page_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				$rd = $sth->fetch(\PDO::FETCH_ASSOC);

				if ($sth->rowCount() != 1) {
					throw new \Scrivo\SystemException("Failed to load page");
				}

				$p = new \Scrivo\Page();
				$p->setFields($context, $rd);

				self::selectProperties($p->context, array($p->id => $p));

				$p->roles = new \Scrivo\RoleSet();
				self::selectRoles($p->context, array($p->id => $p));

				$context->cache[$id] = $p;
			}

			$p->roles->checkReadPermission($context->principal);
			return $p;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select child pages from the database.
	 *
	 * @param \Scrivo\Page $page A Scrivo page.
	 *
	 * @return \Scrivo\PageSet An array containing the selected pages.
	 */
	private static function selectChildren(\Scrivo\Page $page) {
		try {
			$sth = $page->context->connection->prepare(
				"SELECT D.page_id, D.version, D.has_staging, D.parent_id,
					D.sequence_no, D.type, D.page_definition_id, D.language_id,
					D.title, D.description, D.keywords, D.javascript,
					D.stylesheet, D.date_created, D.date_modified, D.date_online,
					D.date_offline, R.role_id
				FROM page D LEFT JOIN object_role R ON
					(D.instance_id = R.instance_id AND D.page_id = R.page_id)
				WHERE D.instance_id = :instId
					AND D.parent_id = :parentId
				ORDER BY sequence_no");

			$page->context->connection->bindInstance($sth);
			$sth->bindValue(":parentId", $page->id, \PDO::PARAM_INT);

			$sth->execute();
			$res = new \Scrivo\PageSet($page);
			$p = null;
			$lid = 0;
			$id = 0;

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$id = intval($rd["page_id"]);

				if ($lid != $id) {

					if ($lid !== 0) {
						$page->context->cache[$lid] = $p;
						$res[$lid] = $p;
					}
					$lid = $id;

					$p = new \Scrivo\Page();
					$p->setFields($page->context, $rd);
					$p->roles = new \Scrivo\RoleSet();

				}

				// Add the roles to the role set
				$p->roles[] = intval($rd["role_id"]);
			}

			if ($id) {
				$page->context->cache[$id] = $p;
				$res[$id] = $p;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select the page path.
	 *
	 * @param \Scrivo\Page $page A Scrivo page.
	 *
	 * @return \Scrivo\PageSet An array containing the selected pages.
	 */
	private static function selectPath(\Scrivo\Page $page) {
		try {

			$res = new \Scrivo\PageSet($page);
			$res->prepend($page);
			$target = $page->parentId;

			$i = 0;
			while ($target) {

				if ($target == $page->id) {
					throw new \Scrivo\SystemException("Path loop");
				}

				if (isset($page->context->cache[$target])) {

					$p = $page->context->cache[$target];

				} else {

					$sth = $page->context->connection->prepare(
						"SELECT D.page_id, D.version, D.has_staging,
							D.parent_id,	D.sequence_no, D.type,
							D.page_definition_id, D.language_id,
							D.title, D.description, D.keywords, D.javascript,
							D.stylesheet, D.date_created, D.date_modified, 
							D.date_online, D.date_offline, R.role_id
						FROM page D LEFT JOIN object_role R ON
							(D.instance_id = R.instance_id AND
								D.page_id = R.page_id)
						WHERE D.instance_id = :instId AND
							D.page_id = :parentId AND
							(D.has_staging+D.version) = 0");

					$page->context->connection->bindInstance($sth);
					$sth->bindValue(":parentId", $target, \PDO::PARAM_INT);

					$sth->execute();
					$p = null;

					while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

						if (!$p) {
							$p = new \Scrivo\Page();
							$p->setFields($page->context, $rd);
							$p->roles = new \Scrivo\RoleSet();
							$target = intval($rd["parent_id"]);
						}

						// Add the roles to the role set
						$p->roles[] = intval($rd["role_id"]);
					}

					if ($p) {
						$page->context->cache[$p->id] = $p;
					} else {
						throw new \Scrivo\SystemException(
							"Failed to load page");
					}

				}

				$res->prepend($p);

				$target = $p->parentId;

			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>