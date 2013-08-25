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
 * $Id: AssetSet.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\AssetSet class.
 */

namespace Scrivo;

/**
 * A Scrivo Asset set is a group of Scrivo assets with a common parent id. A
 * asset set is actually a simple array of (child) assets but it does a
 * couple of things extra:
 *
 * - When serialized only the ids are stored.
 * - When iteratering, counting or getting assets from the array only the
 *   readable assets in the array will be taken into account.
 *
 * Asset sets are a part of a Scrivo asset and are used to retrieve and iterate
 * through the child assets (or path) of an asset.
 */
class AssetSet implements \ArrayAccess, \Iterator, \Countable {

	/**
	 * The array containing the assets or asset ids.
	 * @var \Scrivo\Asset[id]|int[id]
	 */
	private $items = array();

	/**
	 * The parent asset of the set.
	 * @var \Scrivo\Asset
	 */
	private $asset = null;

	/**
	 * An array containing the keys of the items array, used when serializing.
	 * @var int[id]
	 */
	private $ids = null;

	/**
	 * When reading the items array always check of the actual entry is an
	 * instantiated Scrivo asset. If not, get that asset and store it in the
	 * items array.
	 *
	 * @param int $id The asset id of the entry to check.
	 */
	private function check($id) {
		if (is_int($this->items[$id])) {
			$this->items[$id] =
				\Scrivo\Asset::fetch($this->asset->context, $this->items[$id]);
		}
		return $this->items[$id];
	}

	/**
	 * Construct an asset set.
	 *
	 * @param \Scrivo\Asset $asset The parent asset of the asset set.
	 */
	public function __construct(\Scrivo\Asset $asset) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		$this->items = array();
		$this->asset = $asset;
	}

	/**
	 * Set an asset in the asset set.
	 *
	 * @param int $id A Scrivo asset id.
	 * @param \Scrivo\Asset $asset The parent asset of the asset set.
	 */
	public function offsetSet($id, $asset) {
		$this->items[$id] = $asset;
	}

	/**
	 * Add an asset to the beginning of the set.
	 *
	 * @param \Scrivo\Asset $asset The parent asset of the asset set.
	 */
	public function prepend($asset) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		$this->items = array($asset->id => $asset) + $this->items;
	}

	/**
	 * Test if asset is set in the asset set.
	 *
	 * @param int $id A Scrivo asset id to test.
	 *
	 * @return boolean True of the asset with the given id exists in the set.
	 */
	public function offsetExists($id) {
		return (isset($this->items[$id]) &&
			$this->check($id)->roles->canRead($this->asset->context->principal));
	}

	/**
	 * It is not possible to unset assets in an asset set. Calling this method
	 * will raise a system exception.
	 *
	 * @param int $id A Scrivo asset id to unset.
	 */
	public function offsetUnset($id) {
		throw new \Scrivo\SystemException("Illegal method");
	}

	/**
	 * Get an asset with a specific id from the asset set.
	 *
	 * @param int $id A Scrivo asset id to test.
	 *
	 * @return \Scrivo\Asset The asset with the given id.
	 */
	public function offsetGet($id) {
		if (!$this->offsetExists($id)) {
			throw new \Scrivo\SystemException("Not set");
		}
		return $this->items[$id];
	}

	/**
	 * Rewind the asset set so that iterating will start at the beginning.
	 */
	function rewind() {
		reset($this->items);
	}

	/**
	 * Get the current asset from the asset set when iterating.
	 *
	 * @return \Scrivo\Asset The current asset in the asset set.
	 */
	function current() {
		return current($this->items);
	}

	/**
	 * Get the key(id) of the current asset from the asset set when iterating.
	 *
	 * @return \Scrivo\Asset The key (id) of the current asset in the asset set.
	 */
	function key() {
		return key($this->items);
	}

	/**
	 * Get the current asset from the asset set and move the internal pointer
	 * to the next asset in the set.
	 *
	 * @return \Scrivo\Asset The current asset in the asset set.
	 */
	function next() {
		return next($this->items);
	}

	/**
	 * Test if the current asset is valid.
	 *
	 * @return boolean True if the current asset is valid.
	 */
	function valid() {

		$k = key($this->items);
		$pr = $this->asset->context->principal;

		// Get the current asset ...
		if (isset($this->items[$k]) && $c = $this->check($k)) {
			// ... and check if it is readable ...
			if ($c->roles->canRead($pr)) {
				return true;
			}
			// ... and if not move to the next until a readable asset is found.
			while (next($this->items)) {
				if ($this->check(key($this->items))->roles->canRead($pr)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * The asset count of assets in the set. Note that returned asset count
	 * depends on whether the assets are readable for the user defined in the
	 * Scrivo context. So the returned count is always equeal or less that the
	 * actual count of assets in the items array.
	 *
	 * @return int The number of assets in the set that are readable for the
	 *    users defined in the Scrivo context.
	 */
	public function count() {
		// Start counting
		$count = 0;
		foreach($this as $i) {
			if ($i->roles->canRead($this->asset->context->principal)) {
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

		// Note that asset sets always will be serialized as a part of the
		// parent asset. Therefore the asset member will be serialized as a
		// reference value.
		return array("ids", "asset");
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