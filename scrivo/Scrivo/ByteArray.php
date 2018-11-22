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
 * $Id: ByteArray.php 708 2013-07-02 11:59:37Z geert $
 */

/**
 * Implementation of the \Scrivo\ByteArray class.
 */

namespace Scrivo;

/**
 * Wrapper class for 8 byte character strings.
 *
 * ByteArray is a primitive wrapper class for 8 byte character strings. So
 * this is a wrapper class for PHP native strings. It's purpose is to create
 * a clear distinction in the code between byte arrays and UTF-8 Strings.
 *
 * Using only these two classes to handle strings will force you to make a
 * consious decicion each time you work with string data, and thus hopefully
 * prevent error by preventing mixups. A secondary objective is to create a
 * more consistent interface for string handling as PHP itself provides.
 */
class ByteArray implements \Iterator, \ArrayAccess, \Countable {

	/**
	 * The primitive string/byte array.
	 * @var string
	 */
	private $str;

	/**
	 * The current position when iterating.
	 * @var string
	 */
	private $pos;

	/**
	 * The length of the string (characters not bytes).
	 * @var int
	 */
	private $len = -1;

	/**
	 * Get a substring from a string without first checking the boundaries.
	 *
	 * @param int $start Start offset for the substring, use a negative number
	 *   to use an offset from the end of the string.
	 * @param int $length The length of the substring.
	 *
	 * @return ByteArray The requested portion of this string.
	 */
	private function unsafeSubstr($start, $length) {
		return new ByteArray(substr($this->str, $start, $length));
	}

	/**
	 * Construct an ByteArray.
	 *
	 * @param string $str The source string.
	 */
	public function __construct($str="") {
		\Scrivo\ArgumentCheck::assert($str, \Scrivo\ArgumentCheck::TYPE_STRING);
		$this->str = $str;
		$this->pos = 0;
	}

	/**
	 * Factory method to construct an ByteArray.
	 *
	 * @see ByteArray::__construct()
	 *
	 * @param string|array $str The source strings.
	 *
	 * @return ByteArray|ByteArray[] An ByteArray wrapper object.
	 */
	public static function create($str="") {
		if (is_array($str)) {
			foreach($str as $k=>$v) {
				$str[$k] = self::create($v);
			}
			return $str;
		}
		return new ByteArray($str);
	}

	/**
	 * Return the primitive string for this instance.
	 *
	 * @return string The primitive string for this instance.
	 */
	public function __toString() {
		return $this->str;
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
			case "length": return $this->getLength();
		}
		throw new \Scrivo\SystemException("No such property '$name'.");
	}

	/**
	 * Test if this string equals another ByteArray object.
	 *
	 * When you want test ByteArray object for equality, use this method
	 * and never the equality operator (==) because then you'll compare
	 * objects and therefore all data members of ByteArray and this can
	 * give you other results (or cast the ByteArray strings to PHP strings
	 * before comparing).
	 *
	 * @param ByteArray $str The string to compare this string to.
	 *
	 * @return boolean True if the given string equals this string.
	 */
	public function equals(ByteArray $str) {
		return (string)$this->str == (string)$str;
	}

	/**
	 * Get the length of the string.
	 *
	 * @return int The length of the string in characters (not bytes).
	 */
	public function getLength() {
		if ($this->len == -1) {
			$this->len = strlen($this->str);
		}
		return $this->len;
	}

	/**
	 * Return the character count of the string.
	 *
	 * This is an alias for getLength() and part of the implementation of
	 * Countable.
	 *
	 * @return int The length of the string in characters.
	 */
	public function count() {
		return $this->getLength();
	}

	/**
	 * Return the current character when iterating.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 *
	 * @return string The current character in this string when
	 *   iterating.
	 */
	public function current() {
		// note: iterator will call valid() before current().
		return $this->unsafeSubstr($this->pos, 1);
	}

	/**
	 * Return the index of the current character when iterating.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 *
	 * @return int The index of the current character in this string
	 *   when iterating.
	 */
	public function key() {
		return $this->pos;
	}

	/**
	 * Move forward in this string to the next character when iterating.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 */
	public function next() {
		$this->pos++;
	}

	/**
	 * Reset the current character index so iterating will (re)start at the
	 * beginning of this string.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 */
	public function rewind() {
		$this->pos = 0;
	}

	/**
	 * Check if the current character index for iterating is valid.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 *
	 * @return boolean True if the current character index is valid else false.
	 */
	public function valid() {
		return ($this->pos >= 0 && $this->pos < $this->getLength());
	}

	/**
	 * Illegal method: set a character at a specified index location.
	 *
	 * Note that this method is part of the implementation of ArrayAccess.
	 * ByteArrays are immutable and therefore it is prohibited to set
	 * elements (characters) in a string, so this method implementation is
	 * not relevant and throws an exception if called.
	 *
	 * @param int $offset
	 * @param string $value
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetSet($offset, $value) {
		throw new \Scrivo\SystemException(
			"offsetSet can't be called on ByteArray objects");
	}

	/**
	 * Get an UTF-8 character from a string using array brackets.
	 *
	 * Note that this method is part of the implementation of ArrayAccess and
	 * should not be called from an other context.
	 *
	 * @param int $offset A character offet in the string.
	 *
	 * @throws \Scrivo\SystemException If the requested offset was out of range.
	 */
	public function offsetGet($offset) {
		\Scrivo\ArgumentCheck::assert(
			$offset, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		if (!$this->offsetExists($offset)) {
			throw new \Scrivo\SystemException(
				"Str index [$offset] out of bounds");
		}
		return $this->unsafeSubstr($offset, 1);
	}

	/**
	 * Check if the specified index location in this string is valid.
	 *
	 * Note that this method is part of the implementation of ArrayAccess and
	 * should not be called from an other context.
	 *
	 * @param int $offset A character offet in the string.
	 *
	 * @return boolean True if the specified in index is within the valid range.
	 */
	public function offsetExists($offset) {
		\Scrivo\ArgumentCheck::assert(
			$offset, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		return ($offset >= 0 && $offset < $this->getLength());
	}

	/**
	 * Illegal method: unset a character at a specified index location.
	 *
	 * Note that this method is part of the implementation of ArrayAccess.
	 * ByteArrays are immutable and therefore it is prohibited to unset
	 * elements (characters) in a string, so this method implementation is
	 * not relevant and throws an exception if called.
	 *
	 * @param int $offset
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetUnset($offset) {
		\Scrivo\ArgumentCheck::assert(
			$offset, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		throw new \Scrivo\SystemException(
			"offsetUnset can't be called on ByteArray objects");
	}

	/**
	 * Get a substring from a string using an offset and a length.
	 *
	 * Just like PHP's native substr function this method returns a substring
	 * from this string using an offset and a length. But note that this
	 * method will throw an exception if the offset is invalid.
	 *
	 * @param int $start Start offset for the substring, use a negative number
	 *   to use an offset from the end of the string.
	 * @param int $length The length of the substring.
	 *
	 * @return ByteArray The portion of this string specified by the $start
	 *   and $length parameter.
	 *
	 * @throws \Scrivo\SystemException if the requested offset was out of range.
	 */
	public function substr($start, $length=0xFFFF) {
		\Scrivo\ArgumentCheck::assert(
			$start, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		\Scrivo\ArgumentCheck::assert(
			$length, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		$tmp = $start < 1 ? -$start : $start;
		if (!$this->offsetExists($tmp)) {
			throw new \Scrivo\SystemException(
				"Str index [$start] out of bounds");
		}
		return $this->unsafeSubstr($start, $length);
	}

	/**
	 * Get a substring from a string using a start and end index.
	 *
	 * This method is inspired by it's JAVA counterpart and returns a
	 * substring of this string using an start and end index.
	 *
	 * @param int $start Start offset for the substring.
	 * @param int $end The end offset for the substring.
	 *
	 * @return ByteArray The portion of this string specified by the $start
	 *   and $end parameter.
	 *
	 * @throws \Scrivo\SystemException if the requested offset was out of range.
	 */
	public function substring($start, $end) {
		\Scrivo\ArgumentCheck::assert(
			$start, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		\Scrivo\ArgumentCheck::assert(
			$end, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		if (!$this->offsetExists($start) || !$this->offsetExists($end)
				|| $start > $end) {
			throw new \Scrivo\SystemException(
				"Str index [$start, $end] out of bounds");
		}
		return $this->unsafeSubstr($start, $end-$start);
	}

	/**
	 * Get a trimmed copy of this string.
	 *
	 * Returns a copy of the string, with leading and trailing whitespace
	 * removed. Whitespace characters are: ' ', \t, \r, \n.
	 *
	 * @return ByteArray A copy of this string with leading and trailing
	 *   white space removed.
	 */
	public function trim() {
		return new ByteArray(
			preg_replace("/(^[\s]+)|([\s]+$)/s", "", $this->str));
	}

	/**
	 * Check if the string contains the given substring.
	 *
	 * This is the test you normally use strpos(...) !== false for.
	 *
	 * @param ByteArray $str The string to search for.
	 * @param int $offset An offset from where to start the search.
	 * @param boolean $ignoreCase Set to perform an case insensitive lookup.
	 *
	 * @return boolean True if the given string is contained by this string.
	 *
	 * @throws \Scrivo\SystemException If the $offset is out of range.
	 */
	public function contains(ByteArray $str, $offset=0, $ignoreCase=false) {
		\Scrivo\ArgumentCheck::assert(
			$offset, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		\Scrivo\ArgumentCheck::assert(
			$ignoreCase, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		if ($offset && !$this->offsetExists($offset)) {
			throw new \Scrivo\SystemException(
				"Str index [$offset] out of bounds");
		}
		if ($ignoreCase) {
			return stripos(
				$this->str, (string)$str, $offset) !== false;
		} else {
			return strpos($this->str, (string)$str, $offset) !== false;
		}
	}

	/**
	 * Returns the index of the given substring in this string.
	 *
	 * Just like the PHP's native strpos and stripos functions this method
	 * returns the index of a substring in this string. But there are two
	 * important differences: this method returns -1 if the substring was
	 * not found, and this method will raise an exception if the given
	 * offset was out of range.
	 *
	 * @param ByteArray $str The string to search for.
	 * @param int $offset An offset from where to start the search.
	 * @param boolean $ignoreCase Set to perform an case insensitive lookup.
	 *
	 * @return int The index of the first occurance of the substring after
	 *   $offset and -1 if the substring was not found.
	 *
	 * @throws \Scrivo\SystemException If the $offset is out of range.
	 */
	public function indexOf(ByteArray $str, $offset=0, $ignoreCase=false) {
		\Scrivo\ArgumentCheck::assert(
			$offset, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		\Scrivo\ArgumentCheck::assert(
			$ignoreCase, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		if ($offset && !$this->offsetExists($offset)) {
			throw new \Scrivo\SystemException(
				"Str index [$offset] out of bounds");
		}
		$res = -1;
		if ($ignoreCase) {
			$res = stripos($this->str, (string)$str, $offset);
		} else {
			$res = strpos($this->str, (string)$str, $offset);
		}
		return $res !== false ? $res : -1;
	}

	/**
	 * Returns the index of the last occurance of the given substring in this
	 * string.
	 *
	 * Just like the PHP's native strrpos and strripos functions this method
	 * returns the substring of this string that start with the first occurance
	 * of the given a substring in this string.  But note that this
	 * method will throw an exception if the offset is invalid.
	 * Also an negative offset to indicate an offset measured from the end
	 * of the string is allowed. But there are two important differences:
	 * this method returns -1 if the substring was not found, and this method
	 * will raise an exception if the given offset was out of range.
	 *
	 * @param ByteArray $str The string to search for.
	 * @param int $offset An offset from where to start the search. A positive
	 *   value indicates an offset measured from the start of the string, a
	 *   negative value from the end of the string.
	 * @param boolean $ignoreCase Perform an case insensitive lookup.
	 *
	 * @return int The index of the last occurance of the substring after
	 *   $offset.
	 * @throws \Scrivo\SystemException If the $offset is out of range.
	 */
	public function lastIndexOf(ByteArray $str, $offset=0, $ignoreCase=false) {
		\Scrivo\ArgumentCheck::assert(
			$offset, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		\Scrivo\ArgumentCheck::assert(
			$ignoreCase, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		if ($offset) {
			$tmp = $offset < 1 ? -$offset : $offset;
			if (!$this->offsetExists($tmp)) {
				throw new \Scrivo\SystemException(
					"Str index [$offset] out of bounds");
			}
		}
		$res = -1;
		if ($ignoreCase) {
			$res = strripos($this->str, (string)$str, $offset);
		} else {
			$res = strrpos($this->str, (string)$str, $offset);
		}
		return $res !== false ? $res : -1;
	}

	/**
	 * Returns the first occurance of a given substring in this string.
	 *
	 * Just like the PHP's native strstr and stristr functions this method
	 * returns the substring of this string that start with the first occurance
	 * of the given a substring in this string. Note that this method throws
	 * an exception if an empty string was given as search string and not
	 * a warning as strstr does.
	 *
	 * @param ByteArray $str The string to search for.
	 * @param boolean $part Flag to indicate to return the part of the string
	 *   before the first occurance of the given substring i.o. the part
	 *   after the substring.
	 * @param boolean $ignoreCase Perform an case insensitive lookup.
	 *
	 * @return ByteArray The substring plus the part of the string after the
	 *   the first occurance of the substring, or the part of the string before
	 *   the first occurance of the substring (excluding the substring) or NULL
	 *   if not found.
	 *
	 * @throws \Scrivo\SystemException If an empty search string was given.
	 */
	public function firstOccurranceOf(ByteArray $str, $part=false,
			$ignoreCase=false) {
		\Scrivo\ArgumentCheck::assert(
			$part, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		\Scrivo\ArgumentCheck::assert(
			$ignoreCase, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		if (!$str->getLength()) {
			throw new \Scrivo\SystemException(
				"firstOccurranceOf requires a search string");
		}
		$res = NULL;
		if ($ignoreCase) {
			$res = stristr($this->str, (string)$str, $part);
		} else {
			$res = strstr($this->str, (string)$str, $part);
		}
		return $res !== false ? new ByteArray($res) : NULL;
	}

	/**
	 * Returns the last occurance of a given character in this string.
	 *
	 * Just like the PHP's native strrchr and strrichr functions this method
	 * returns the substring of this string that start with the first occurance
	 * of the given a substring in this string. Note that this method throws
	 * an exception if an empty string was given as search string and not
	 * a warning as strstr does.
	 *
	 * @param ByteArray $str The character to search for.
	 * @param boolean $part Flag to indicate to return part of the string before
	 *   the last occurance of the given character i.o. the part after the
	 *   character.
	 * @param boolean $ignoreCase Perform an case insensitive lookup.
	 *
	 * @return ByteArray The substring plus the part of the string after the
	 *   the last occurance of the character, or the part of the string before
	 *   the last occurance of the character (excluding the character) or NULL
	 *   if not found.
	 *
	 * @throws \Scrivo\SystemException If a search string of not exactly one
	 *   character in length was given.
	 */
	public function lastOccurranceOf(ByteArray $str, $part=false,
			$ignoreCase=false) {
		\Scrivo\ArgumentCheck::assert(
			$part, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		\Scrivo\ArgumentCheck::assert(
			$ignoreCase, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		if ($str->getLength() != 1) {
			throw new \Scrivo\SystemException(
				"lastOccurranceOf accepts single charaters only");
		}
		$pos = $this->lastIndexOf($str, 0, $ignoreCase);
		if ($pos == -1) {
			return null;
		}
		if ($part) {
			return $this->unsafeSubstr(0, $pos);
		}
		return $this->unsafeSubstr($pos, $this->getLength()-$pos);
	}

	/**
	 * Replace a substring or set of substrings in this string.
	 *
	 * You can use this method in favour of PHP's native str_replace and strtr
	 * functions. This method will do proper type checking for you. Note
	 * that you can safely use str_replace: if all input parameter are
	 * correct UTF-8 this method is UTF-8 safe too.
	 *
	 * @param ByteArray|ByteArray[] $from A (set of) string(s) to replace
	 *   in this string.
	 * @param ByteArray|ByteArray[] $to A (set of) replacement string(s) to
	 *   replace the found string(s).
	 *
	 * @return ByteArray A string with the replaced values.
	 *
	 * @throws \Scrivo\SystemException If the input data is not of type
	 *   ByteArray or ByteArray[], of if the $to parameter is an array
	 *	 and $from isn't or hasn't the same number of elements.
	 */
	public function replace($from, $to) {
		if ($from instanceof ByteArray && $to instanceof ByteArray) {
			return new ByteArray(str_replace($from, $to, $this->str));
		} else if (is_array($from) && $to instanceof ByteArray) {
			foreach ($from as $k=>$v) {
				if (!($v instanceof ByteArray)) {
					throw new \Scrivo\SystemException("From element is"
						. " not an ByteArray as array position [$k]");
				}
			}
			return new ByteArray(str_replace($from, $to, $this->str));
		} else if (is_array($from) && is_array($to)) {
			if (count($from) != count($to)) {
				throw new \Scrivo\SystemException(
					"Input arrays are not the same size");
			}
			foreach ($from as $k=>$v) {
				if (!($v instanceof ByteArray)
						|| !($to[$k] instanceof ByteArray)) {
					throw new \Scrivo\SystemException("To or from element is"
						. " not an ByteArray as array position [$k]");
				}
			}
			return new ByteArray(str_replace($from, $to, $this->str));
		}
		throw new \Scrivo\SystemException("Invalid argument types");
	}

	/**
	 * Split this string using a delimiter.
	 *
	 * Just like PHP's native explode this method splits a string on
	 * boundaries formed by the string delimiter. Note that the behavoir
	 * of the limit parameter is a little bit different and that this method
	 * will throw an exception if an empty string is passed as a delimiter.
	 *
	 * @param ByteArray $delimiter The boundary string.
	 * @param int $limit If limit is set and positive, the returned array
	 *	 will contain a maximum of limit elements with the last element
	 *	 containing the rest of string. If the limit parameter is negative,
	 *	 all components except the last -limit are returned. If the limit is
	 *	 not set or 0 no limit wil be used.
	 *
	 * @return ByteArray[] An array of strings created by splitting the
	 *	 string parameter on boundaries formed by the delimiter. If the
	 *	 delimiter was not found and array containing a copy of this string
	 *	 will be returned except if limit was negative, in that case an
	 *	 empty array will be returned.
	 *
	 * @throws \Scrivo\SystemException If an empty search string was given.
	 */
	public function split(ByteArray $delimiter, $limit=0) {
		\Scrivo\ArgumentCheck::assert(
			$limit, \Scrivo\ArgumentCheck::TYPE_INTEGER);
		if ($delimiter == "") {
			throw new \Scrivo\SystemException(
					"split cannot use an empty \"\" delimiter.");
		}
		$r = $limit ? explode($delimiter, $this->str, $limit)
			: explode($delimiter, $this->str);
		foreach ($r as $k=>$v) {
			$r[$k] = new ByteArray($v);
		}
		return $r;
	}

	/**
	 * Get a copy of this string with all of its characters converted to lower
	 * case.
	 *
	 * @return ByteArray A string containing only lower case characters.
	 */
	public function toLowerCase() {
		return new ByteArray(strtolower($this->str));
	}

	/**
	 * Get a copy of this string with all of its characters converted to upper
	 * case.
	 *
	 * @return ByteArray A string containing only upper case characters.
	 */
	public function toUpperCase() {
		return new ByteArray(strtoupper($this->str));
	}

	/**
	 * Compare this string to another ByteArray object.
	 *
	 * @param ByteArray $str The string to compare this string to.
	 *
	 * @return int Less than 0 if this string is less than the given
	 *   string $str; more than 0 if this string is greater than $str, and
	 *   0 if they are equal.
	 */
	public function compareTo(ByteArray $str) {
		return strcmp($this->str, $str);
	}

}

?>