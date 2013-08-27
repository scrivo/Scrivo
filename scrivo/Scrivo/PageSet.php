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
 * $Id: RoleSet.php 547 2013-03-04 12:06:51Z geert $
 */

/**
 * Implementation of the \Scrivo\PageSet class.
 */

namespace Scrivo;

/**
 * A Scrivo Page set is a group of Scrivo pages with a common parent id. A
 * page set is actually a simple array of (child) pages but it does a
 * couple of things extra:
 *
 * - When serialized only the ids are stored.
 * - When iteratering, counting or getting pages from the array only the
 *   readable pages in the array will be taken into account.
 *
 * Page sets are a part of a Scrivo page and are used to retrieve and iterate
 * through the child pages (or path) of a page.
 */
class PageSet implements \ArrayAccess, \Iterator, \Countable {

	/**
	 * The array containing the pages or page ids.
	 * @var \Scrivo\Page[id]|int[id]
	 */
	private $items = array();

	/**
	 * The parent page of the set.
	 * @var \Scrivo\Page
	 */
	private $page = null;

	/**
	 * An array containing the keys of the items array, used when serializing.
	 * @var int[id]
	 */
	private $ids = null;

	/**
	 * When reading the items array always check of the actual entry is an
	 * instantiated Scrivo page. If not, get that page and store it in the
	 * items array.
	 *
	 * @param int $id The page id of the entry to check.
	 */
	private function check($id) {
		if (is_int($this->items[$id])) {
			$this->items[$id] =
				\Scrivo\Page::fetch($this->page->context, $this->items[$id]);
		}
		return $this->items[$id];
	}

	/**
	 * Construct a page set.
	 *
	 * @param \Scrivo\Page $page The parent page of the page set.
	 */
	public function __construct(\Scrivo\Page $page) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		$this->items = array();
		$this->page = $page;
	}

	/**
	 * Set a page in the page set.
	 *
	 * @param int $id A Scrivo page id.
	 * @param \Scrivo\Page $page The parent page of the page set.
	 */
	public function offsetSet($id, $page) {
		$this->items[$id] = $page;
	}

	/**
	 * Add a page to the beginning of the set.
	 *
	 * @param \Scrivo\Page $page The parent page of the page set.
	 */
	public function prepend($page) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		array_unshift($this->items, $page);
	}

	/**
	 * Test if page is set in the page set.
	 *
	 * @param int $id A Scrivo page id to test.
	 *
	 * @return boolean True of the page with the given id exists in the set.
	 */
	public function offsetExists($id) {
		return (isset($this->items[$id]) &&
			$this->check($id)->roles->canRead($this->page->context->principal));
	}

	/**
	 * It is not possible to unset pages in a page set. Calling this method
	 * will raise a system exception.
	 *
	 * @param int $id A Scrivo page id to unset.
	 */
	public function offsetUnset($id) {
		throw new \Scrivo\SystemException("Illegal method");
	}

	/**
	 * Get a page with a specific id from the page set.
	 *
	 * @param int $id A Scrivo page id to test.
	 *
	 * @return \Scrivo\Page The page with the given id.
	 */
	public function offsetGet($id) {
		if (!$this->offsetExists($id)) {
			throw new \Scrivo\SystemException("Not set");
		}
		return $this->items[$id];
	}

	/**
	 * Rewind the page set so that iterating will start at the beginning.
	 */
	function rewind() {
		reset($this->items);
	}

	/**
	 * Get the current page from the page set when iterating.
	 *
	 * @return \Scrivo\Page The current page in the page set.
	 */
	function current() {
		return current($this->items);
	}

	/**
	 * Get the key(id) of the current page from the page set when iterating.
	 *
	 * @return \Scrivo\Page The key (id) of the current page in the page set.
	 */
	function key() {
		return key($this->items);
	}

	/**
	 * Get the current page from the page set and move the internal pointer
	 * to the next page in the set.
	 *
	 * @return \Scrivo\Page The current page in the page set.
	 */
	function next() {
		return next($this->items);
	}

	/**
	 * Test if the current page is valid.
	 *
	 * @return boolean True if the current page is valid.
	 */
	function valid() {

		$k = key($this->items);
		$pr = $this->page->context->principal;

		// Get the current page ...
		if (isset($this->items[$k]) && $c = $this->check($k)) {
			// ... and check if it is readable ...
			if ($c->roles->canRead($pr)) {
				return true;
			}
			// ... and if not move to the next until a readable page is found.
			while (next($this->items)) {
				if ($this->check(key($this->items))->roles->canRead($pr)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * The page count of pages in the set. Note that returned page count
	 * depends on whether the pages are readable for the user defined in the
	 * Scrivo context. So the returned count is always equeal or less that the
	 * actual count of pages in the items array.
	 *
	 * @return int The number of pages in the set that are readable for the
	 *    users defined in the Scrivo context.
	 */
	public function count() {
		// Start counting
		$count = 0;
		foreach($this as $i) {
			if ($i->roles->canRead($this->page->context->principal)) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * When serializing retrieve the don't store the items array but just the
	 * keys.
	 *
	 * return array An array with the names of the serializable members.
	 */
	public function __sleep() {

		$this->ids = array_keys($this->items);

		// Note that page sets always will be serialized as a part of the
		// parent page. Therefore the page member will be serialized as a
		// reference value.
		return array("ids", "page");
	}

	/**
	 * When unserializing restore the items array only the id.
	 */
	public function __wakeup() {
		$this->items = array_combine($this->ids, $this->ids);
		unset($this->ids);
	}

}

?>