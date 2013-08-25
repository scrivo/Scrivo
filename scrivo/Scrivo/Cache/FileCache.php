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
 * $Id: FileCache.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\Cache\FileCache class.
 */

namespace Scrivo\Cache;

/**
 * Implementation of a file cache.
 *
 * This is an implmentation of a file cache in PHP. It allows you to store
 * serializable objects on disk. It was designed to be a fallback for the
 * APC user cache, but nonetheless a very reasonable alternative.
 *
 * Some configuration notes. If possible use a RAM disk, apart from the speed
 * benefits it probably is also easier to limit the amount of space used by
 * the cache when using a RAM disk, else you'll have to resort to disk quotas.
 *
 * There is no software limit on the amount of data the cache uses. A garbage
 * collector is run each FileCache::$gcInterval store requests. If data
 * cannot be written the cache will acively purge data, keeping a certain
 * percentage of the most frequently used entries in the cache.
 *
 * Garbage collection only removes expired entries. When purging data another
 * alogrithm is used: Data that was stored but never used will be deleted
 * inmediately, the rest is sorted based on access time and then a given
 * percentage (FileCache::$pctToKeepAfterPurge) of the last accessed files
 * will be saved, the rest is removed.
 *
 * Note: one of the former versions supported file locking with flock. But
 * that does not work in a threaded web server and ISAPI. Since that is the
 * common case nowadays file locking is dropped. Is it thread save? I believe
 * so, but that is only because unserialize will return null when reading
 * corrupted (not fully written) entries. Furthermore beacuse of PHP's
 * problems with file locking cache slams are not allowed. So only the first
 * thread is allowed to write a file, later threads are not.
 */
class FileCache implements \Scrivo\Cache {

	/**
	 * The location of the cache directory.
	 *
	 * @var \Scrivo\String
	 */
	private $dir;

	/**
	 * The gargbage collection interval. This interval is measured in
	 * number of store requests.
	 *
	 * @var int
	 */
	private $gcInterval;

	/**
	 * The counter for the number of store requests.
	 *
	 * @var int
	 */
	private $storeCount = 0;

	/**
	 * Percentage of most frequently accessed file that will be kept after
	 * the cache is purged when there's not enough space left.
	 *
	 * @var int
	 */
	private $pctToKeepAfterPurge;

	/**
	 * List of troublesome characters in file names.
	 *
	 * @var \Scrivo\String[]
	 */
	private $reservedCharacters;

	/**
	 * List of character sequences to escape troublesome characters in file
	 * names.
	 *
	 * @var \Scrivo\String[]
	 */
	private $escapedCharacters;

	/**
	 * Create a file cache.
	 *
	 * @param \Scrivo\String $dir The location where the cache schould
	 *   store the files. The default is the 'ScrivoCache' folder in the
	 *   system's temp directory.
	 * @param int $gcInterval The interval at which to run the garbage
	 *   collector measured in store requests.
	 * @param int $pctToKeepAfterPurge Percentage of items that were accessed
	 *   at least once that you want to keep after a cache purge due to
	 *   storage shortage.
	 */
	public function __construct(\Scrivo\String $dir=null, $gcInterval=50,
			$pctToKeepAfterPurge=50) {
		$this->gcInterval = $gcInterval;
		$this->pctToKeepAfterPurge = $pctToKeepAfterPurge;
		$this->reservedCharacters = \Scrivo\String::create(array(
			"@","/","\\","?","%","*",":","|","\"","<",">","."));
		$this->escapedCharacters = \Scrivo\String::create(array(
			"@0","@1","@2","@3","@4","@5","@6","@7","@8","@9","@A","@B"));
		if (!$dir) {
			$dir = sys_get_temp_dir()."/ScrivoCache";
		}
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		$this->dir = new \Scrivo\String($dir."/");
	}

	/**
	 * Just a wrapper for fopen that throws an exception instead of an error.
	 *
	 * @param string $file
	 */

	private function fopen($file) {
		if (!$fp = fopen($file, "w")) {
			throw new \Scrivo\SystemException(
				"Could not create cache file '$file'");
		}
		return $fp;
	}

	/**
	 * Just a wrapper for touch that throws an exception instead of returning
	 * a status.
	 *
	 * @param string $file
	 * @param string $ttl
	 */
	private function touch($file, $ttl) {
		if (!touch($file, time()+$ttl, time()-2)) {
			throw new \Scrivo\SystemException(
				"Could not touch cache file '$file'");
		}
	}

	/**
	 * Just a wrapper for unlink that throws an exception instead of returning
	 * a status.
	 *
	 * @param string $file
	 */
	private function unlink($file) {
		if (!unlink($file)) {
			throw new \Scrivo\SystemException(
				"Could not delete cache file '$file'");
		}
	}

	/**
	 * Create a file name from a key, taking in account problematic characters
	 * for a file name.
	 *
	 * @param \Scrivo\String $key Key name to create a file name for.
	 */
	private function getFile(\Scrivo\String $key) {
		return $this->dir . $key->replace($this->reservedCharacters,
			$this->escapedCharacters);
	}

	/**
	 * Clear the cache from its most irrelevant items.
	 *
	 * You can use this function to free up a certain amount of the file cache
	 * First it deletes the items that were stored but never accessed. The
	 * items that were accessed at least once are sorted on access time and
	 * prec_to_keep of the most recently accessed files will be kept in the
	 * cache. The others will be deleted.
	 *
	 * @param int $percentageToKeep Percentage of items that were accessed
	 *    at least once that you want to keep.
	 */
	public function purge($percentageToKeep=0) {
		clearstatcache();
		$m=0;
		$ref = array();
		if ($dh = opendir($this->dir)) {
			while (($file = readdir($dh)) !== false) {
				$s = stat($this->dir.$file);
				if (!($s["mode"] & 040000)) {
					if ($s["atime"] < $s["ctime"]) {
						// remove not yet accessed items
						$this->unlink($this->dir.$file);
						$m++;
					} else {
						$ref[$file] = $s["atime"];
					}
				}
			}
			closedir($dh);
			// remove files that were accessed last
			arsort($ref);
			$n=0;
			$i = intval(count($ref)*$percentageToKeep/100);
			$rem = array_slice($ref, 0, $i);
			foreach ($rem as $k=>$d) {
				$this->unlink($this->dir.$k);
				$n++;
			}
		}
		//error_log("Cache purged: $m items that were never accessed and " .
		//	"$n of the most infrequently used items were removed.");
	}

	/**
	 * Run the garbage collector: delete all expired cache entries.
	 */
	private function gc() {
		clearstatcache();
		$i=0;
		if ($dh = opendir($this->dir)) {
			while (($file = readdir($dh)) !== false) {
				$s = stat($this->dir.$file);
				if (!($s["mode"] & 040000)) {
					if ($s["mtime"] < time()) {
						$this->unlink($this->dir.$file);
						$i++;
					}
				}
			}
			closedir($dh);
		}
		//error_log("File cache garbage collected: $i items removed");
	}

	/**
	 * Store a variable in the cache.
	 *
	 * Store any serializable variable in the cache. Note that it is not
	 * possible to overwrite an existing entry (cache slam). Such an event
	 * will not raise an error but the function will report it.
	 *
	 * @param \Scrivo\String $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED if the variable was succesfully stored,
	 * 	  CACHE_SLAM if key already exists or NO_SPACE if there is not
	 *    enough space left to store the value.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value or
	 *    when a file operation fails.
	 */
	public function store(\Scrivo\String $key, $val, $ttl=3600) {
		if ($val === null) {
			throw new \Scrivo\SystemException(
				"Can't store null values in the cache");
		}
		// The name for the cache file.
		$file = $this->getFile($key);
		// If the file already exists, and other thread is already writing
		// the file and we're done here.
		if (file_exists($file)) {
			return self::CACHE_SLAM;
		}
		// Run the garbage collector at the specified probability.
		if (($this->storeCount % $this->gcInterval) == $this->gcInterval - 1) {
			$this->gc();
		}
		// Get the data to store.
		$data = new \Scrivo\ByteArray(serialize($val));
		// Create the file and bail out not succesfull.
		$fp = $this->fopen($file);
		// Write the data.
		if (fwrite($fp, (string)$data) != $data->length) {
			// Data was not fully written: drop infreq. used and unused entries.
			fclose($fp);
			$this->delete($key);
			$this->purge($this->pctToKeepAfterPurge);
			// try again
			$fp = $this->fopen($file);
			if (fwrite($fp, (string)$data) != $data->length) {
				// Data was not fully written again: close and remove the file.
				fclose($fp);
				$this->delete($key);
				return self::NO_SPACE;
			}
		}
		// Now we have a file touch it.
		fclose($fp);
		$this->touch($file, $ttl);

		$this->storeCount++;
		return self::DATA_STORED;
	}

	/**
	 * Store a variable in the cache, overwrite it if it already exists.
	 *
	 * Store any serializable variable in the cache. It is guaranteed that
	 * the data will be written. But note that it is not guaranteed that the
	 * next fetch will retrieve this value.
	 *
	 * @param \Scrivo\String $key A cache unique name for the key.
	 * @param mixed $val The (serializable) variabele to strore.
	 * @param int $ttl Time to live in seconds.
	 *
	 * @return int DATA_STORED if the variable was succesfully stored or
	 * 	 NO_SPACE if there is not enough space left to store the value.
	 *
	 * @throws \Scrivo\SystemException When trying to store a NULL value or
	 *   when a file operation fails.
	 */
	public function overwrite(\Scrivo\String $key, $val, $ttl=3600) {
		// Theoretically just after delete an other thread can store a value.
		for ($tmp=self::CACHE_SLAM; $tmp==self::CACHE_SLAM;) {
			$this->delete($key);
			$tmp = $this->store($key, $val, $ttl);
		}
		return $tmp;
	}

	/**
	 * Delete/remove a cache entry.
	 *
	 * @param \Scrivo\String $key A cache unique name for the key.
	 */
	public function delete(\Scrivo\String $key) {
		$file = $this->getFile($key);
		if (file_exists($file)) {
			$this->unlink($file);
		}
	}

	/**
	 * Retrieve a value from the cache.
	 *
	 * @param \Scrivo\String $key The key for which to retrieve the value.
	 *
	 * @return mixed The value of the stored variable or NULL if the key
	 *   does not exists or is expired.
	 */
	public function fetch(\Scrivo\String $key) {
		$file = $this->getFile($key);
		$res = null;
		// Suppressing the message is probably faster then calling file_exists
		// and stat.
		if ($s = @stat($file)) {
			if ($s["mtime"] < time()) {
				$this->delete($key);
			} else {
				$tmp = file_get_contents($file);
				// if (strtoupper(substr(PHP_OS,0,3))==='WIN')
				//	touch($file,date('U',filemtime($file)),time());
				//
				if ($tmp) {
					$res = unserialize($tmp);
				}
			}
		}
		return $res ? $res : null;
	}

	/**
	 * List all entries in the cache.
	 *
	 * The cache list is an array in which the cache keys are the keys of
	 * the array entries and the data of the entries are objects ot type
	 * stdClass that contain at least contain the following properties:
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
		if ($dh = opendir($this->dir)) {
			while (($file = readdir($dh)) !== false) {
				$s = stat($this->dir.$file);
				if (!($s['mode'] & 040000)) {
					$k = \Scrivo\String::create($file)->replace(
						$this->escapedCharacters, $this->reservedCharacters);
					$l[(string)$k] = (object)array(
						"accessed" => $s["atime"],
						"expires" => $s["mtime"],
						"created" => $s["ctime"],
						"size" => $s["size"]
					);
				}
			}
			closedir($dh);
		}
		return $l;
	}

}

?>