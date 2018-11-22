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
 * $Id: ApcCache.php 629 2013-05-20 23:02:09Z geert $
 */

/**
 * Implementation of the \Scrivo\ApcCache class.
 */

namespace Scrivo\Cache;

/**
 * Implementation of the Cache interface using APC.
 *
 * This is an implementation of the Cache interface using APC. It is a very
 * straightforward mapping of the interface methods to the APC functions.
 * An notable difference is that cache slams are not allowed, so store will
 * fail to store where apc_store would succeed.
 */
class ApcCache implements \Scrivo\Cache {

	/**
	 * Create an APC cache wrapper.
	 */
	public function __construct() {
	}

	/**
	 * Store a variable in the cache.
	 *
	 * Store any serializable variable in the cache. Note that it is not
	 * possible to overwrite an existing entry (cache slam). Such an event
	 * will not raise an error but the function will report it.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED if the variable was succesfully stored or
	 * 	  CACHE_SLAM if key already exists.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value.
	 */
	public function store(\Scrivo\Str $key, $val, $ttl=3600) {
		if ($val === null) {
			throw new \Scrivo\SystemException(
				"Can't store null values in the cache");
		}
		if (apc_fetch((string)$key)) {
			return self::CACHE_SLAM;
		}
		apc_store((string)$key, $val, $ttl);
		return self::DATA_STORED;
	}

	/**
	 * Store a variable in the cache, overwrite it if it already exists.
	 *
	 * Store any serializable variable in the cache. It is guaranteed that
	 * the data will be written. But note that it is not guaranteed that the
	 * next fetch will retrieve this value.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED if the variable was succesfully stored.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value or
	 *   when a file operation fails.
	 */
	public function overwrite(\Scrivo\Str $key, $val, $ttl=3600) {
		if ($val === null) {
			throw new \Scrivo\SystemException(
				"Can't store null values in the cache");
		}
		apc_store((string)$key, $val, $ttl);
		return self::DATA_STORED;
	}

	/**
	 * Delete/remove a cache entry.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 */
	public function delete(\Scrivo\Str $key) {
		apc_delete((string)$key);
	}

	/**
	 * Retrieve a value from the cache.
	 *
	 * @param \Scrivo\Str $key The key for which to retrieve the value.
	 *
	 * @return mixed The value of the stored variable or NULL if the key
	 *   does not exists or is expired.
	 */
	public function fetch(\Scrivo\Str $key) {
		$res = apc_fetch((string)$key);
		return $res ? $res : null;
	}

	/**
	 * List all entries in the cache.
	 *
	 * This method returns an array in which the cache keys are the keys of
	 * the array entries and the data of each entry is an object of type
	 * stdClass that contains at least the following properties:
	 *
	 * * accessed: the access time
	 * * expires: the expiration time
	 * * created: the creation time
	 * * size: the size of the entry
	 *
	 * @return object[] A array of objects that describe the current cache
	 *    entries.
	 */
	public function entryList() {
		$l = array();
		$d = apc_cache_info('user');
		foreach ($d["cache_list"] as $v) {
			$l[(string)$v["info"]] = (object)array(
				"accessed" => $v["access_time"],
				"expires" => $v["creation_time"]+$v["ttl"],
				"created" => $v["creation_time"],
				"size" => $v["mem_size"]
			);
		}
		return $l;
	}

}
