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
 * $Id: PageDefinitionHints.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PageDefinitionHints class.
 */

namespace Scrivo;

/**
 * The PageDefinitionHints class is used to prevent page definition selection
 * in the Scrivo user interface.
 *
 * In principle it is possible to create pages of any page definition
 * underneath any other page. But from the viewpoint of the site designer this
 * is not always desirable. The site might require specific rules such as that
 * there are only 'main menu' pages allowed underneath the home.
 *
 * Likewise, the site editor will not be facilitated when offered long lists
 * of page definitions (with possible many irrelevant entries) each time he or
 * she wants to add a page.
 *
 * To guide the user in page definition selection the PageDefinitionHints class
 * lists the number of occurances of pages of a specific page definition that
 * is allowed under a page of a given page definition. For these lists it can
 * be decided if a page definition is selectable when creating a page.
 *
 * Suppose we have three page definitions: Home, Standard and Contact. Then a
 * likely scenario is that we don't want the the editor the select the 'Home'
 * page definition at any time, the 'Standard' page definition for as many
 * times as desired but only as a child of a page of page definition 'Home' or
 * 'Standard' and the 'Contact' page only once as child of the home page (the
 * page of page definition 'Home').
 *
 * Again these are merely hints. In the super-interfaces 'admin' and 'config'
 * your still allowed to make all combinations you want. These hints are only
 * used as a guide in the editor interface.
 */
class PageDefinitionHints implements \Iterator, \ArrayAccess {

	/**
	 * Constant to denote that we want to retrieve the list of how many times
	 * a page of this page definition may occur underneath pages of other
	 * page definitions.
	 */
	const PARENT_PAGE_DEFINITION_COUNT = 1;

	/**
	 * Constant to denote we want to retrieve the list of how many times pages
	 * of other page definitions (total count) may occur underneath the current
	 * page.
	 */
	const CHILD_PAGE_DEFINITION_COUNT = 2;

	/**
	 * Constant to denote we want to retrieve the list of how many times pages
	 * of other page definitions (remaining count) may occur underneath the
	 * current page.
	 */
	const CHILD_PAGE_DEFINITION_REMAINING = 3;

	/**
	 * The id of the page definition to retrieve the list.
	 * @var int
	 */
	private $pageDefinitionId = 0;

	/**
	 * The type of the list:
	 * \Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT,
	 * \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_COUNT or
	 * \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING.
	 * @var int
	 */
	private $type = 0;

	/**
	 * The hints array.
	 * @var object[]
	 */
	private $hints = null;

	/**
	 * A Scrivo context
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Construct a pageDefinition hints object. Depending on the context where
	 * you want to use these hints for a number of different lists can be
	 * constructed. For instance, when defining the user interface one need
	 * lists that contain the hints as stored in the database
	 * (PARENT_PAGE_DEFINITION_COUNT or CHILD_PAGE_DEFINITION_COUNT). But in
	 * the user interface itself you'll want to to use the rules as defined but
	 * corrected for the pages already created.
	 *
	 * Suppose there are three main menus are allowed under the a home page.
	 * The types PARENT_PAGE_DEFINITION_COUNT or CHILD_PAGE_DEFINITION_COUNT
	 * will give you that information. But if there are alreay two main menus
	 * are created under the home CHILD_PAGE_DEFINITION_REMAINING will give
	 * you the corrected result of just one main menu allowed under the a home
	 * page.
	 * 	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 * @param int $pageDefinitionId The id of the pageDefinition to create the
	 *   hints for, or the page id in the case of
	 *   CHILD_PAGE_DEFINITION_REMAINING.
	 * @param int $listType The list type to create: either
	 *   \Scrivo\PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT,
	 *   \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_COUNT or
	 *   \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING.
	 * @param int $listType If the listType parameter was set to
	 *   \Scrivo\PageDefinitionHints::CHILD_PAGE_DEFINITION_REMAINING then the
	 *   id for the page for which to correct the result needs to be supplied.
	 */
	function __construct(
			\Scrivo\Context $context, $pageDefinitionId, $listType) {

		$this->context = $context;
		$this->type = $listType;

		if (self::CHILD_PAGE_DEFINITION_COUNT == $listType) {
			$this->pageDefinitionId = $pageDefinitionId;
			$this->load(true);
		} else if (self::CHILD_PAGE_DEFINITION_REMAINING == $listType) {
			$this->loadCorrected($pageDefinitionId);
		} else if (self::PARENT_PAGE_DEFINITION_COUNT == $listType) {
			$this->pageDefinitionId = $pageDefinitionId;
			$this->load(false);
		} else {
			throw new \Scrivo\SystemException("invalid list type");
		}
	}

	/**
	 * Get the page definition hint for a given page definition (id).
	 *
	 * @param int $key A page definition id used as key in the hints array.
	 *
	 * @return object The hint for the given page definition. The hint has the
	 *   following fields: pageDefinitionId, title and maxNoOfChilds.
	 *
	 * @throws \Scrivo\SystemException If the requested offset was out of
	 *   range.
	 */
	public function offsetGet($key) {
		if (!isset($this->hints[$key])) {
			throw new \Scrivo\SystemException(
				"PageDefinitionHints invalid index");
		}
		return $this->hints[$key];
	}

	/**
	 * Part of the implementation of \ArrayAccess. Not applicable for
	 * PageDefinitionHints.
	 *
	 * @param int $key
	 * @param string $value
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetSet($key, $value) {
		throw new \Scrivo\SystemException(
			"offsetSet can't be called on PageDefinitionHints objects");
	}

	/**
	 * Test if a hint exists at the requested index location.
	 *
	 * @param int $key A page definition id used as key in the hints array.
	 */
	public function offsetExists($key) {
		return isset($this->hints[$key]);
	}

	/**
	 * Part of the implementation of \ArrayAccess. Not applicable for
	 * PageDefinitionHints.
	 *
	 * @param int $key
	 *
	 * @throws \Exception If this method is called.
	 */
	public function offsetUnset($key) {
		throw new \Scrivo\SystemException(
			"offsetUnset can't be called on PageDefinitionHints objects");
	}

	/**
	 * Rewind the hints array so iterating will start at the beginning again.
	 */
	function rewind() {
		reset($this->hints);
	}

	/**
	 * Get the current page definition hint when iterating.
	 */
	function current() {
		return current($this->hints);
	}

	/**
	 * Get the key of the current page definition hint when iterating.
	 */
	function key() {
		return key($this->hints);
	}

	/**
	 * Get the next page definition hint when iterating.
	 */
	function next() {
		next($this->hints);
	}

	/**
	 * Check if the current key is valid.
	 */
	function valid() {
		return key($this->hints) ? true : false;
	}

	/**
	 * Load the page definition hints as defined in the database. This list
	 * can be generated from two different viewpoints:
	 * 1) pages of this page definition can be used x times under pages of
	 *    some other page definition
	 *    ($children==false/PARENT_PAGE_DEFINITION_COUNT), or
	 * 2) pages of some page definition can occur x times under a page using
	 *    this page definition ($children==true/CHILD_PAGE_DEFINITION_COUNT)
	 *
	 * @param boolean $children False (default) if you want to select how
	 *    many times a page using this page definition may occur under pages
	 *    of some other page definition, True if you want to select how many
	 *    times a pages of some other page definition may occur under a page
	 *    using this page definition.
	 */
	private function load($children=false) {
		try {
			$this->context->checkPermission(AccessController::READ_ACCESS);

			$sth = $this->context->connection->prepare(
				"SELECT	page_definition_id, title FROM page_definition
				WHERE instance_id = :instId ORDER BY title");

			$this->context->connection->bindInstance($sth);

			$sth->execute();

			$res = array();
			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$k = intval($rd["page_definition_id"]);
				$res[$k] = (object)array(
					"pageDefinitionId" => $k,
					"title" => $rd["title"],
					"maxNoOfChildren" => NULL
				);
			}

			if ($children) {
				// CHILD_PAGE_DEFINITION_COUNT
				$sth = $this->context->connection->prepare(
					"SELECT	page_definition_id parent_page_definition_id, max_no_of_children
					FROM page_definition_hints WHERE instance_id = :instId AND
						parent_page_definition_id = :templId");
			} else {
				// PARENT_PAGE_DEFINITION_COUNT
				$sth = $this->context->connection->prepare(
					"SELECT	parent_page_definition_id, max_no_of_children
					FROM page_definition_hints	WHERE instance_id = :instId AND
						page_definition_id = :templId");
			}

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(
				":templId", $this->pageDefinitionId, \PDO::PARAM_INT);

			$sth->execute();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$k = intval($rd["parent_page_definition_id"]);
				if (isset($res[$k])) {
					$res[$k]->maxNoOfChildren =
						intval($rd["max_no_of_children"]);
				}
			}

			$this->hints = $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Load the page definition hints as defined in the database, but corrected
	 * for the currently created pages. This is basically an extion of
	 * PageDefinitionHints::load(true)/pages of some page definition can occur
	 * x times under a page using this page
	 * definition/CHILD_PAGE_DEFINITION_COUNT and a correction for pages that
	 * are made already.
	 *
	 * The parent page is given as an argument, the list generated is a list
	 * of page definitions and how many times new pages of each page definition
	 * are still allowed underneath the given page.
	 *
	 * @param int $parentId The id of the parent page.
	 */
	private function loadCorrected($parentId) {
		try {
			$this->context->checkPermission(AccessController::READ_ACCESS);

			$sth = $this->context->connection->prepare(
				"SELECT page_definition_id FROM page WHERE instance_id = :instId
				AND (has_staging+version) = 0 AND page_id = :docPid");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":docPid", $parentId, \PDO::PARAM_INT);

			$sth->execute();

			if ($sth->rowCount() != 1) {
				throw new \Scrivo\SystemException("Failed to load page");
			}

			$rd = $sth->fetch(\PDO::FETCH_ASSOC);

			$this->pageDefinitionId = $rd["page_definition_id"];

			$this->load(true);

			$sth = $this->context->connection->prepare(
				"SELECT page_definition_id FROM page WHERE instance_id = :instId
				AND (has_staging+version) = 0 AND parent_id = :docPid");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":docPid", $parentId, \PDO::PARAM_INT);

			$sth->execute();

			$res = array();
			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$k = intval($rd["page_definition_id"]);
				if (!isset($res[$k])) {
					$res[$k] = 1;
				} else {
					$res[$k]++;
				}
			}

			foreach ($res as $used => $count) {
				if (isset($this->hints[$used]->maxNoOfChildren)) {
					$this->hints[$used]->maxNoOfChildren -= $count;
					if ($this->hints[$used]->maxNoOfChildren < 0) {
						$this->hints[$used]->maxNoOfChildren = 0;
					}
				}
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Update a set of page definition hints. Note: It is assumed that you're
	 * updating a list of type PARENT_PAGE_DEFINITION_COUNT otherwise a
	 * \Scrivo\SystemException is raised.
	 */
	public function update() {
		try {

			if ($this->type != self::PARENT_PAGE_DEFINITION_COUNT) {
				throw new \Scrivo\SystemException("Only PageDefinitionHints of".
					" type PARENT_PAGE_DEFINITION_COUNT can be updated");
			}

			$this->context->checkPermission(AccessController::WRITE_ACCESS);

			$sth = $this->context->connection->prepare(
				"DELETE FROM page_definition_hints	WHERE instance_id = :instId
				AND page_definition_id = :templId");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(
				":templId", $this->pageDefinitionId, \PDO::PARAM_INT);

			$sth->execute();

			foreach ($this->hints as $k=>$hint) {
				if (!is_null($hint->maxNoOfChildren)) {

					$sth = $this->context->connection->prepare(
						"INSERT	INTO page_definition_hints (
							instance_id, parent_page_definition_id,
							page_definition_id, max_no_of_children
						) VALUES (
							:instId, :templPid,
							:templId, :maxChld
						)"
					);

					$this->context->connection->bindInstance($sth);
					$sth->bindValue(":templPid", $k, \PDO::PARAM_INT);
					$sth->bindValue(
						":templId", $this->pageDefinitionId, \PDO::PARAM_INT);
					$sth->bindValue(
						":maxChld", $hint->maxNoOfChildren, \PDO::PARAM_INT);

					$sth->execute();
				}
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}
}
