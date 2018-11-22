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
 * $Id: LocalCache.php 631 2013-05-21 15:14:11Z geert $
 */

/**
 * Implementation of the \Scrivo\LocalCache class.
 */

namespace Scrivo;

/**
 * The LocalCache is the cache that Scrivo uses to store already instantiated
 * objects. The local cache map through to a persisten cache (if defined). So
 * when an object is not found the local cache the persistent cache will be
 * checked next.
 *
 * So when you instantiate a Scrivo Page for instance first will be tried to
 * load it from the local cache, if not it is tried to retrieve it from the
 * persistent cache (apc or filecache).
 */
class LocalCache implements \ArrayAccess {

	/**
	 * The cache array.
	 * @var object[]
	 */
	private static $cache;

	/**
	 * The persitent cache to use.
	 * @var \Scrivo\Cache
	 */
	private static $persistentCache;

	/**
	 * Construct a local cache.
	 *
	 * @param \Scrivo\Cache $persistentCache The persistent cache to use behind
	 *   the local cache.
	 */
	function __construct(\Scrivo\Cache $persistentCache=null) {
		self::$cache = array();
		if ($persistentCache) {
			self::$persistentCache = $persistentCache;
		} else {
			self::$persistentCache = new \Scrivo\Cache\NoCache();
		}
	}

	/**
	 * Get an object from the cache.
	 *
	 * Note this is part of the implementation of ArrayAccess and should be
	 * invoked as array access:
	 *
	 * $obj = $myLocalCache["key"];
	 *
	 * @param int|string $key The key used to store the value.
	 *
	 * @return object The requested object or null if not found.
	 */
	public function offsetGet($key) {
		if (isset(self::$cache[$key])) {
//error_log("Local cache hit $key");
			return self::$cache[$key];
		}
		$res = self::$persistentCache->fetch(new \Scrivo\Str($key));
		if ($res) {
//error_log("Persistent cache hit $key");
			self::$cache[$key] = $res;
		}
		return $res;
	}

	/**
	 * Set an object in the cache.
	 *
	 * Note this is part of the implementation of ArrayAccess and should be
	 * invoked as array access:
	 *
	 * $myLocalCache["key"] = $obj;
	 *
	 * @param int|string $key The key used to store the value.
	 * @param mixed $value The object to set in the cache.
	 */
	public function offsetSet($key, $value) {
//error_log("cache set $key");
		self::$cache[$key] = $value;
		self::$persistentCache->overwrite(new \Scrivo\Str($key), $value);
	}

	/**
	 * Test if an object exists in the cache.
	 *
	 * Note this is part of the implementation of ArrayAccess and should be
	 * invoked as array access:
	 *
	 * isset($myLocalCache["key"]);
	 *
	 * @param int|string $key The key used to store the value.
	 *
	 * @return boolean True if an object exists in the cache at the
	 *    entry specified by the key.
	 */
	public function offsetExists($key) {
		if (isset(self::$cache[$key])) {
//error_log("Local cache hit $key");
			return true;
		}
		$t = self::$persistentCache->fetch(new \Scrivo\Str($key));
		if ($t) {
//error_log("Persistent cache hit $key");
			self::$cache[$key] = $t;
		}
		return $t?true:false;
	}

	/**
	 * Delete an object from the cache.
	 *
	 * Note this is part of the implementation of ArrayAccess and should be
	 * invoked as array access:
	 *
	 * usset($myLocalCache["key"]);
	 *
	 * @param int|string $key The key used to store the value.
	 */
	public function offsetUnset($key) {
		unset(self::$cache[$key]);
		self::$persistentCache->delete(new \Scrivo\Str($key));
	}

}

?>