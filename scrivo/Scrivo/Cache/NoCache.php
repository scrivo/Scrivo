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
 * $Id: NoCache.php 629 2013-05-20 23:02:09Z geert $
 */

/**
 * Implementation of the \Scrivo\NoCache class.
 */

namespace Scrivo\Cache;

/**
 * No-cache implementation of the Cache interface.
 *
 * This is an implementation of the Cache interface that you can use when
 * you don't want any caching to take place. No data will be stored and
 * null is returned on all fetches.
 */
class NoCache implements \Scrivo\Cache {

	/**
	 * Create an APC cache wrapper.
	 */
	public function __construct() {
	}

	/**
	 * Pretend to store a variable in the cache.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED although no data will be stored.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value.
	 */
	public function store(\Scrivo\Str $key, $val, $ttl=3600) {
		if ($val === null) {
			throw new \Scrivo\SystemException(
				"Can't store null values in the cache");
		}
		return self::DATA_STORED;
	}

	/**
	 * Pretend to overwrite a variable in the cache.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED although no data will be stored.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value.
	 */
	public function overwrite(\Scrivo\Str $key, $val, $ttl=3600) {
		if ($val === null) {
			throw new \Scrivo\SystemException(
				"Can't store null values in the cache");
		}
		return self::DATA_STORED;
	}

	/**
	 * Pretend to delete/remove a cache entry.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 */
	public function delete(\Scrivo\Str $key) {
	}

	/**
	 * Perform a failed fetch from the cache.
	 *
	 * @param \Scrivo\Str $key The key for which to retrieve the value.
	 *
	 * @return mixed The value of the stored variable or NULL if the key
	 *   does not exists or is expired.
	 */
	public function fetch(\Scrivo\Str $key) {
		null;
	}

	/**
	 * List all (=none) entries in the cache.
	 *
	 * @return object[] An empty array.
	 */
	public function entryList() {
		return array();
	}

}

