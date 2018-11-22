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
 * $Id: Cache.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\Cache interface.
 */

namespace Scrivo;

/**
 * The Scrivo cache interface defines the interface for cache classes. These
 * cache classes can be used to hold Scrivo data in shared storage between
 * requests and processes.
 */
interface Cache {

	/**
	 * Constant to indicate that a value was succesfully stored in the cache.
	 */
	const DATA_STORED = 1;

	/**
	 * Constant to indicate that a value was not stored in the cache because
	 * the entry was already taken.
	 */
	const CACHE_SLAM = 2;

	/**
	 * Constant to indicate that a value was not stored in the cache because
	 * there was not enough storage available.
	 */
	const NO_SPACE = 3;

	/**
	 * Store a variable in the cache.
	 *
	 * Store any serializable variable in the cache. Note that it is not
	 * possible to overwrite an existing entry (cache slam). Such an event
	 * will not raise an error but the function will report it.
	 *
	 * So subseqent calls to store should not made when the data to store
	 * changes between calls. If you activily want to store cache entries
	 * consider using overwrite() or first delete the entry before you store
	 * it.
	 *
	 * Not to allow cache slams was a design decision because PHP does not
	 * allow thread save access to files. Thus when implementing a file
	 * cache disallowing a store request because an other thread is writing
	 * is considered expected behavoir.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED if the variable was succesfully stored,
	 * 	  CACHE_SLAM if key already exists or NO_SPACE if there is not
	 *    enough space left to store the value.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value.
	 */
	public function store(\Scrivo\Str $key, $val, $ttl=3600);

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
	 * @return int DATA_STORED if the variable was succesfully stored or
	 * 	  NO_SPACE if there is not enough space left to store the value.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value.
	 */
	public function overwrite(\Scrivo\Str $key, $val, $ttl=3600);

	/**
	 * Delete/remove a cache entry.
	 *
	 * @param \Scrivo\Str $key A cache unique name for the key.
	 */
	public function delete(\Scrivo\Str $key);

	/**
	 * Retrieve a value from the cache.
	 *
	 * @param \Scrivo\Str $key The key for which to retrieve the value.
	 *
	 * @return mixed The value of the stored variable or NULL if the key
	 *   does not exists or is expired.
	 */
	public function fetch(\Scrivo\Str $key);

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
	public function entryList();

}
