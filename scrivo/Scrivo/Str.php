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
 * $Id: Str.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\Str class.
 */

namespace Scrivo;

/**
 * Wrapper class for PHP strings to enforce consistent and safe multi-byte
 * (UTF-8) string handling.
 *
 * \Scrivo\Str is a primitive wrapper class for PHP strings to make sure that
 * all operations performed on the string are UTF-8 safe. As PHP does not
 * enforce a consistent way to deal with multibyte strings we do it
 * ourselves. In the Scrivo code base UTF-8 is the only encoding that is
 * supported for operations on data and these operations should be done
 * through instances of the \Scrivo\Str class. If strings are used as byte
 * arrays, use the ByteArray class.
 *
 * \Scrivo\Str objects are imutable: once created you can't change them. All
 * operations on a \Scrivo\Str object will return a new \Scrivo\Str object.
 *
 * Although we'll be working with UTF-8 exclusively it is possible to create
 * \Scrivo\Str objects that contain characters from 8 byte encoding schemes.
 * Also a note on HTML entities, we work with UTF-8 so you don't need them:
 * they are evil. Except entities for the reserved HTML characters (<>&'")
 * there is really no use for them in UTF-8 strings. And when stored in a
 * database only cause sorting and lookup errors. Therefore when construction
 * \Scrivo\Str objects you can opt to convert existing HTML entities to their
 * corresonding UTF-8 characters.
 *
 * The current locale setting for LC_COLLATE is important.
 * \Scrivo\Str::compareTo() will use this setting when comparing strings.
 *
 * Please note: you might be tempted to do string comparison using
 * equality operators (==). Although this works in most cases don't do this:
 * you'll do PHP object comparison (i.e. comparing a
 * \Scrivo\Str object) and that is not what you want: use \Scrivo\Str::equals()
 * or \Scrivo\Str::compareTo() to compare strings.
 */
class Str implements \Iterator, \ArrayAccess, \Countable {

	/**
	 * Constant to denote ISO-8859-1 encoding. This is the default encoding
	 * for \Scrivo\Str uses for fixing and comparing.
	 */
	const ENC_ISO_8859_1 = "ISO-8859-1";

	/**
	 * Constant to denote CP-1251 encoding.
	 */
	const ENC_CP_1251 = "CP-1251";

	/**
	 * Constant to indicate that you don't want to decode any entities when
	 * constructing the string.
	 */
	const DECODE_NONE = 0;

	/**
	 * Constant to indicate that you want to decode all entities when
	 * constructing the string.
	 */
	const DECODE_ALL = 1;

	/**
	 * Constant to indicate that you want to decode all but the entities for
	 * reserved characters (&<>'") when constructing the string.
	 */
	const DECODE_UNRESERVED = 2;

	/**
	 * The primitive UTF-8 string.
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
	 * Collator used for sorting. This is a static shared amongst instances.
	 * @var \Collator
	 */
	private static $coll;

	/**
	 * Map to translate 8 byte code page characters to UTF-8 sequences.
	 * @var array[]
	 */
	private static $maps = array(
		self::ENC_ISO_8859_1 => array(128 =>
			"€","�","‚","ƒ","„","…","†","‡","ˆ","‰","Š","‹","Œ","�","Ž","�",
			"�","‘","’","“","”","•","–","—","˜","™","š","›","œ","�","ž","Ÿ",
			" ","¡","¢","£","¤","¥","¦","§","¨","©","ª","«","¬","­","®","¯",
			"°","±","²","³","´","µ","¶","·","¸","¹","º","»","¼","½","¾","¿",
			"À","Á","Â","Ã","Ä","Å","Æ","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï",
			"Ð","Ñ","Ò","Ó","Ô","Õ","Ö","×","Ø","Ù","Ú","Û","Ü","Ý","Þ","ß",
			"à","á","â","ã","ä","å","æ","ç","è","é","ê","ë","ì","í","î","ï",
			"ð","ñ","ò","ó","ô","õ","ö","÷","ø","ù","ú","û","ü","ý","þ","ÿ",
		),
		self::ENC_CP_1251 => array(128 =>
			"Ђ","Ѓ","‚","ѓ","„","…","†","‡","€","‰","Љ","‹","Њ","Ќ","Ћ","Џ",
			"ђ","‘","’","“","”","•","–","—","�","™","љ","›","њ","ќ","ћ","џ",
			" ","Ў","ў","Ј","¤","Ґ","¦","§","Ё","©","Є","«","¬","­","®","Ї",
			"°","±","І","і","ґ","µ","¶","·","ё","№","є","»","ј","Ѕ","ѕ","ї",
			"А","Б","В","Г","Д","Е","Ж","З","И","Й","К","Л","М","Н","О","П",
			"Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я",
			"а","б","в","г","д","е","ж","з","и","й","к","л","м","н","о","п",
			"р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я",
		),
	);

	/**
	 * Test if a given byte sequence is a valid UTF-8 sequence.
	 *
	 * If the tested byte sequence is  a valid UTF-8 sequence the method
	 * returns the length of the sequence, else the method returns 0.
	 *
	 * @param string $seq The byte sequence to test.
	 *
	 * @return int The length of the UTF-8 sequence (2-4) or 0 if the
	 *   sequence is not an UTF-8 sequence.
	 */
	private function isUtf8Sequence($seq) {

		// check if the first byte is a UTF-8 marker and if not end it here
		$b1 = ord($seq[0]);
		if ($b1<0xC2 || $b1>=0xF5) {
		   return 0;
		}

		// get the length to prevent overshooting when reading
		$len = strlen($seq);

		// Get the 2nd and 3rd byte and test it, note that for a valid UTF-8
		// sequence we only allow the byte (here byte three) after the
		// sequence to be none, an ascii character, or a new UTF-8 marker
		// (which is more limiting than not to allow continuation bytes
		// (b3 < 0x80 && b3 >= OxBF) only).
		$b2 = ($len>1) ? ord($seq[1]) : 0;
		$b3 = ($len>2) ? ord($seq[2]) : 0;
		if ($b1>=0xC2 && $b1<0xE0 && $b2>=0x80 && $b2<0xC0
				&& ($b3<0x80 || ($b3>=0xC2 && $b3<0xF5))) {
			return 2;
		}

		// We're not there, test for a 3 byte byte sequence. See the comment
		// above on testing the 4th byte.
		$b4 = ($len>3) ? ord($seq[3]) : 0;
		if ($b1>=0xE0 && $b1<0xF0
				&& $b2>=0x80 && $b2<0xC0 && $b3>=0x80 && $b3<0xC0
				&& ($b4<0x80 || ($b4>=0xC2 && $b4<0xF5))) {
			return 3;
		}

		// We're not there, test for a 3 byte byte sequence. See the comment
		// above on testing the 5th byte. Also note that b2 is restricted
		// so that we keep in the <= U+10FFFF range
		$b5 = ($len>4) ? ord($seq[4]) : 0;
		if ($b1>=0xF0 && $b1<0xF5 && $b2>=0x80 && $b2<0xC0
				&& $b3>=0x80 && $b3<0xC0 && $b4>=0x80 && $b4<0xC0
				&& ($b5<0x80 || ($b5>=0xC2 && $b5<0xF5))) {
			return 4;
		}

		// This is not a valid UTF-8 sequence.
		return 0;
	}

	/**
	 * Convert a string with UTF-8 and code page characters to a valid UTF-8
	 * string.
	 *
	 * When converting the input string to UTF-8 all bytes in the 0x80-0xFF
	 * range are first tested if they are is a valid UTF-8 byte sequences, if
	 * not it is assumed that it is an 8 byte code page character and
	 * converted according to the given encoding. Supported encodings are:
	 *
	 * * Utf8string::ENC_ISO_8859_1
	 * * Utf8string::ENC_CP_1251
	 *
	 * @param string $str The string with mixed UTF-8 and and 8 byte code
	 *   page characters.
	 * @param string $encoding The encoding to use when converting 8 byte code
	 *   page characters to UTF-8.
	 *
	 * @return string A valid UTF-8 string.
	 */
	private function fixCodePageString($str, $encoding) {

		// set the encoding
		if ($encoding != self::ENC_ISO_8859_1
				&& $encoding != self::ENC_CP_1251) {
			throw
				new \Scrivo\SystemException("Unsupported encoding: $encoding");
		}

		// Split the data on any occurance of a byte with the high bit set
		$parts =
			preg_split('/[\x80-\xFF]/', $str, -1, PREG_SPLIT_OFFSET_CAPTURE);

		// See if there's anything to do
		$c = count($parts);
		if ($c<=1) {
			return $str;
		}

		// Start with the first part
		$out = $parts[0][0];
		for ($i=1; $i<$c; $i++) {
			// Get a 6 byte sequence on a split location ...
			$seq = substr($str, $parts[$i][1]-1, 6);
			// ... and check if is a valid UTF-8 byte sequence, ...
			$utf8_seq_width = $this->isUtf8Sequence($seq);
			if ($utf8_seq_width) {
				// ... if so add it to output ...
				$res = substr($seq, 0, $utf8_seq_width);
				// ... and jump over the parts.
				$i += ($utf8_seq_width - 1);
			} else {
				// ... else treat it as a codepage character
				$res = self::$maps[$encoding][ord($seq[0])];
			}
			// add the UTF-8 character and next part to the output
			$out .= $res.$parts[$i][0];
		}

		return $out;
	}

	/**
	 * Convert a string with HTML entities, UTF-8 and code page characters
	 * to a valid UTF-8 string.
	 *
	 * When converting the input string to UTF-8 all bytes in the 0x80-0xFF
	 * range are first tested if they are is a valid UTF-8 byte sequences, if
	 * not it is assumed that it is an 8 byte code page character and
	 * converted according to the given encoding. Supported encodings are:
	 *
	 * * Utf8string::ENC_ISO_8859_1
	 * * Utf8string::ENC_CP_1251
	 *
	 * You can opt to convert HTML entities in the string to their
	 * corresponding characters. Possible choices are:
	 *
	 * * Utf8string::DECODE_NONE don't decode HTML entities
	 * * Utf8string::DECODE_ALL, decode all HTML entities;
	 * * Utf8string::DECODE_UNRESERVED, decode all but the HTML entities
	 *     for <>&' and ' (HTML/XML)
	 *
	 * @param string $str The source string, a possible mixture of HTML
	 *   entities, UTF-8 and code page characters.
	 * @param int $toDecode Which entities
	 * @param string $encoding The encoding to use when converting 8 byte code
	 *   page characters to UTF-8.
	 *
	 * @return string A valid UTF-8 string.
	 */
	private function fixString($str, $toDecode=self::DECODE_NONE,
			$encoding="UTF-8") {

		// List of HTML-entities we want to keep.
		$reserved = array(
			"&lt;", "&gt;", "&amp;", "&quot;", "&#39;",
			"&#60;", "&#62;", "&#38;", "&#34;", "&#039;",
			"&#060;","&#062;", "&#038;", "&#034;", "&apos;"
		);

		// List of HTML-entity markers to replace the ones you want to
		// keep, so html_entity_decode will leave them alone.
		$save = array(
			"#*@lt!;", "#*@gt!;", "#*@amp!;", "#*@quot!;", "#*@#039!;",
			"#*@lt!;", "#*@gt!;", "#*@amp!;", "#*@quot!;", "#*@#039!;",
			"#*@lt!;", "#*@gt!;", "#*@amp!;", "#*@quot!;", "#*@#039!;"
		);

		if ($toDecode == self::DECODE_UNRESERVED) {
			// 'Save' entities for reserved characters.
			$str = str_replace($reserved, $save, $str);
		}
		if ($encoding != "UTF-8") {
			// Fix characters that are not properly UTF-8 encoded
			$str = $this->fixCodePageString($str, $encoding);
		}
		if ($toDecode != self::DECODE_NONE) {
			// Change all entities to their corresponding UTF-8 characters.
			$str = html_entity_decode($str, ENT_QUOTES, "UTF-8");
		}
		if ($toDecode == self::DECODE_UNRESERVED) {
			// 'Restore' previously saved entities.
			$str = str_replace(array_slice($save, 0, 5),
				array_slice($reserved, 0, 5), $str);
		}

		return $str;
	}

	/**
	 * Get a substring from a string without first checking the boundaries.
	 *
	 * @param int $start Start offset for the substring, use a negative number
	 *   to use an offset from the end of the string.
	 * @param int $length The length of the substring.
	 *
	 * @return \Scrivo\Str The requested portion of this string.
	 */
	private function unsafeSubstr($start, $length) {
		return new \Scrivo\Str(mb_substr($this->str, $start, $length, "UTF-8"));
	}

	/**
	 * Construct an \Scrivo\Str.
	 *
	 * You can either construct an \Scrivo\Str object from a valid UTF-8 string,
	 * or from a string that you expect not to contain valid UTF-8 data. In the
	 * latter case use the $toDecode and/or $encoding parameters.
	 *
	 * Possible choices for $toDecode are:
	 *
	 * * Utf8string::DECODE_NONE don't decode HTML entities
	 * * Utf8string::DECODE_ALL, decode all HTML entities;
	 * * Utf8string::DECODE_UNRESERVED, decode all but the HTML entities
	 *      for <>&' and ' (HTML/XML)
	 *
	 * If you expect that the source string contains 8 byte code page character
	 * then you can select the encoding to use to convert them to their
	 * corresponding UTF-8 characters. Supported encodings are:
	 *
	 * * Utf8string::ENC_ISO_8859_1
	 * * Utf8string::ENC_CP_1251
	 *
	 * Note: typical use of the $toDecode and $encoding parameters is when
	 * you want to 'sanitize' data before you store it into a database. Setting
	 * these parameters start CPU intensive procedures so it's best not to use
	 * them in bluk operations (like that inner loop or slashdotted home page).
	 * And remember when all data was safely stored as UTF-8, there will be
	 * no need to 'sanitize' it before displaying.
	 *
	 * @param string $str The source string, a possible mixture of HTML
	 *   entities, UTF-8 and code page characters.
	 * @param int $toDecode Which entities
	 * @param string $encoding The encoding to use when converting 8 byte code
	 *   page characters to UTF-8.
	 */
	public function __construct($str="", $toDecode=self::DECODE_NONE,
			$encoding="UTF-8") {
		$str = (string)$str;
		$this->str = $toDecode==self::DECODE_NONE && $encoding=="UTF-8" ? $str
			: $this->fixString($str, $toDecode, $encoding);
		$this->pos = 0;
	}

	/**
	 * Factory method to construct an \Scrivo\Str.
	 *
	 * @see \Scrivo\Str::__construct()
	 *
	 * @param string $str The string to create the wrapper for. It is assumed
	 *   that this will be a valid UTF-8 string. If this is not the case,
	 *   you'll need to set the additional parameters.
	 * @param int $toDecode Which entities
	 * @param string $encoding The encoding to use when converting 8 byte code
	 *
	 * @return \Scrivo\Str|\Scrivo\Str An \Scrivo\Str wrapper object.
	 */
	public static function create($str="", $toDecode=self::DECODE_NONE,
			$encoding="UTF-8") {
		if (is_array($str)) {
			foreach($str as $k=>$v) {
				$str[$k] = self::create($v, $toDecode, $encoding);
			}
			return $str;
		}
		return new \Scrivo\Str($str, $toDecode, $encoding);
	}

	/**
	 * Get the collator for sorting strings.
	 *
	 * @return \Collator The currently set collator for the \Scrivo\Str
	 *    class.
	 */
	public static function getCollator() {
		if (!self::$coll) {
			self::$coll = new \Collator(\Locale::getDefault());
		}
		return self::$coll;
	}

	/**
	 * Set the collator for sorting strings.
	 *
	 * @param \Collator $coll The collator to use.
	 */
	public static function setCollator(\Collator $coll) {
		self::$coll = $coll;
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
			case "collator": return self::getCollator();
		}
		throw new \Scrivo\SystemException("No such property '$name'.");
	}

	/**
	 * Return the primitive UTF-8 string for this instance.
	 *
	 * @return string The primitive UTF-8 string for this instance.
	 */
	public function __toString() {
		return $this->str;
	}

	/**
	 * Test if this string equals another \Scrivo\Str object.
	 *
	 * When you want test \Scrivo\Str object for equality, use this method
	 * and never the equality operator (==) because then you'll compare
	 * objects and therefore all data members of \Scrivo\Str and this can
	 * give you other results (or cast the \Scrivo\Str strings to PHP strings
	 * before comparing).
	 *
	 * @param \Scrivo\Str $str The string to compare this string to.
	 *
	 * @return boolean True if the given string equals this string.
	 */
	public function equals(\Scrivo\Str $str) {
		return (string)$this->str == (string)$str;
	}

	/**
	 * Get the length of the string.
	 *
	 * @return int The length of the string in characters (not bytes).
	 */
	public function getLength() {
		if ($this->len == -1) {
			$this->len = mb_strlen($this->str, "UTF-8");
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
	 * Return the current UTF-8 character when iterating.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 *
	 * @return string The current UTF-8 character in this string when
	 *   iterating.
	 */
	public function current() {
		// note: iterator will call valid() before current().
		return $this->unsafeSubstr($this->pos, 1);
	}

	/**
	 * Return the index of the current UTF-8 character when iterating.
	 *
	 * Note that this method is part of the implementation of Iterator and
	 * should not be called from an other context.
	 *
	 * @return int The index of the current UTF-8 character in this string
	 *   when iterating.
	 */
	public function key() {
		return $this->pos;
	}

	/**
	 * Move forward in this string to the next UTF-8 character when iterating.
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
	 * \Scrivo\Strings are immutable and therefore it is prohibited to set
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
			"offsetSet can't be called on \Scrivo\Str objects");
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
		return ($offset >= 0 && $offset < $this->getLength());
	}

	/**
	 * Illegal method: unset a character at a specified index location.
	 *
	 * Note that this method is part of the implementation of ArrayAccess.
	 * \Scrivo\Strings are immutable and therefore it is prohibited to unset
	 * elements (characters) in a string, so this method implementation is
	 * not relevant and throws an exception if called.
	 *
	 * @param int $offset
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetUnset($offset) {
		throw new \Scrivo\SystemException(
			"offsetUnset can't be called on \Scrivo\Str objects");
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
	 * @return \Scrivo\Str The portion of this string specified by the $start
	 *   and $length parameter.
	 *
	 * @throws \Scrivo\SystemException if the requested offset was out of range.
	 */
	public function substr($start, $length=0xFFFF) {
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
	 * @return \Scrivo\Str The portion of this string specified by the $start
	 *   and $end parameter.
	 *
	 * @throws \Scrivo\SystemException if the requested offset was out of range.
	 */
	public function substring($start, $end) {
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
	 * removed. Whitespace characters are: ' ', \t, \r, \n, the character
	 * for a non breaking space.
	 *
	 * @return \Scrivo\Str A copy of this string with leading and trailing
	 *   white space removed.
	 */
	public function trim() {
		return new \Scrivo\Str(
			preg_replace("/(^[\s ]+)|([\s ]+$)/us", "", $this->str));
	}

	/**
	 * Check if the string contains the given substring.
	 *
	 * This is the test you normally use strpos(...) !== false for.
	 *
	 * @param \Scrivo\Str $str The string to search for.
	 * @param int $offset An offset from where to start the search.
	 * @param boolean $ignoreCase Set to perform an case insensitive lookup.
	 *
	 * @return boolean True if the given string is contained by this string.
	 *
	 * @throws \Scrivo\SystemException If the $offset is out of range.
	 */
	public function contains(\Scrivo\Str $str, $offset=0, $ignoreCase=false) {
		if ($offset && !$this->offsetExists($offset)) {
			throw new \Scrivo\SystemException(
				"Str index [$offset] out of bounds");
		}
		if ($ignoreCase) {
			return mb_stripos(
				$this->str, (string)$str, $offset, "UTF-8") !== false;
		} else {
			// binary is ok to do
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
	 * @param \Scrivo\Str $str The string to search for.
	 * @param int $offset An offset from where to start the search.
	 * @param boolean $ignoreCase Set to perform an case insensitive lookup.
	 *
	 * @return int The index of the first occurance of the substring after
	 *   $offset and -1 if the substring was not found.
	 *
	 * @throws \Scrivo\SystemException If the $offset is out of range.
	 */
	public function indexOf(\Scrivo\Str $str, $offset=0, $ignoreCase=false) {
		if ($offset && !$this->offsetExists($offset)) {
			throw new \Scrivo\SystemException(
				"Str index [$offset] out of bounds");
		}
		$res = -1;
		if ($ignoreCase) {
			$res = mb_stripos($this->str, $str, $offset, "UTF-8");
		} else {
			$res = mb_strpos($this->str, $str, $offset, "UTF-8");
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
	 * @param \Scrivo\Str $str The string to search for.
	 * @param int $offset An offset from where to start the search. A positive
	 *   value indicates an offset measured from the start of the string, a
	 *   negative value from the end of the string.
	 * @param boolean $ignoreCase Perform an case insensitive lookup.
	 *
	 * @return int The index of the last occurance of the substring after
	 *   $offset.
	 * @throws \Scrivo\SystemException If the $offset is out of range.
	 */
	public function lastIndexOf(\Scrivo\Str $str, $offset=0, $ignoreCase=false) {
		if ($offset) {
			$tmp = $offset < 1 ? -$offset : $offset;
			if (!$this->offsetExists($tmp)) {
				throw new \Scrivo\SystemException(
					"Str index [$offset] out of bounds");
			}
		}
		$res = -1;
		if ($ignoreCase) {
			$res = mb_strripos($this->str, $str, $offset, "UTF-8");
		} else {
			$res = mb_strrpos($this->str, $str, $offset, "UTF-8");
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
	 * @param \Scrivo\Str $str The string to search for.
	 * @param int $part Flag to indicate to return the part of the string
	 *   before the first occurance of the given substring i.o. the part
	 *   after the substring.
	 * @param boolean $ignoreCase Perform an case insensitive lookup.
	 *
	 * @return \Scrivo\Str The substring plus the part of the string after the
	 *   the first occurance of the substring, or the part of the string before
	 *   the first occurance of the substring (excluding the substring) or NULL
	 *   if not found.
	 *
	 * @throws \Scrivo\SystemException If an empty search string was given.
	 */
	public function firstOccurranceOf(\Scrivo\Str $str, $part=false,
			$ignoreCase=false) {
		if (!$str->getLength()) {
			throw new \Scrivo\SystemException(
				"firstOccurranceOf requires a search string");
		}
		$res = NULL;
		if ($ignoreCase) {
			$res = mb_stristr($this->str, $str, $part, "UTF-8");
		} else {
			$res = mb_strstr($this->str, $str, $part, "UTF-8");
		}
		return $res !== false ? new \Scrivo\Str($res) : NULL;
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
	 * @param \Scrivo\Str $str The character to search for.
	 * @param int $part Flag to indicate to return part of the string before
	 *   the last occurance of the given character i.o. the part after the
	 *   character.
	 * @param boolean $ignoreCase Perform an case insensitive lookup.
	 *
	 * @return \Scrivo\Str The substring plus the part of the string after the
	 *   the last occurance of the character, or the part of the string before
	 *   the last occurance of the character (excluding the character) or NULL
	 *   if not found.
	 *
	 * @throws \Scrivo\SystemException If a search string of not exactly one
	 *   character in length was given.
	 */
	public function lastOccurranceOf(\Scrivo\Str $str, $part=false,
			$ignoreCase=false) {
		if ($str->getLength() != 1) {
			throw new \Scrivo\SystemException(
				"lastOccurranceOf accepts single charaters only");
		}
		$res = NULL;
		if ($ignoreCase) {
			$res = mb_strrichr($this->str, $str, $part, "UTF-8");
		} else {
			$res = mb_strrchr($this->str, $str, $part, "UTF-8");
		}
		return $res !== false ? new \Scrivo\Str($res) : NULL;
	}

	/**
	 * Replace a substring or set of substrings in this string.
	 *
	 * You can use this method in favour of PHP's native str_replace and strtr
	 * functions. This method will do proper type checking for you.
	 *
	 * @param \Scrivo\Str|\Scrivo\Str[] $from A (set of) string(s) to replace
	 *   in this string.
	 * @param \Scrivo\Str|\Scrivo\Str[] $to A (set of) replacement string(s) to
	 *   replace the found string(s).
	 *
	 * @return \Scrivo\Str A string with the replaced values.
	 *
	 * @throws \Scrivo\SystemException If the input data is not of type
	 *	 \Scrivo\Str or \Scrivo\Str[], of if the $to parameter is an array
	 *	 and $from isn't or hasn't the same number of elements.
	 */
	public function replace($from, $to) {
		if ($from instanceof \Scrivo\Str && $to instanceof \Scrivo\Str) {
			return new \Scrivo\Str(str_replace($from, $to, $this->str));
		} else if (is_array($from) && $to instanceof \Scrivo\Str) {
			foreach ($from as $k=>$v) {
				if (!($v instanceof \Scrivo\Str)) {
					throw new \Scrivo\SystemException("From element is"
						. " not an \Scrivo\Str as array position [$k]");
				}
			}
			return new \Scrivo\Str(str_replace($from, $to, $this->str));
		} else if (is_array($from) && is_array($to)) {
			if (count($from) != count($to)) {
				throw new \Scrivo\SystemException(
					"Input arrays are not the same size");
			}
			foreach ($from as $k=>$v) {
				if (!($v instanceof \Scrivo\Str)
						|| !($to[$k] instanceof \Scrivo\Str)) {
					throw new \Scrivo\SystemException("To or from element is"
						. " not an \Scrivo\Str as array position [$k]");
				}
			}
			return new \Scrivo\Str(str_replace($from, $to, $this->str));
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
	 * @param \Scrivo\Str $delimiter The boundary string.
	 * @param int $limit If limit is set and positive, the returned array
	 *	 will contain a maximum of limit elements with the last element
	 *	 containing the rest of string. If the limit parameter is negative,
	 *	 all components except the last -limit are returned. If the limit is
	 *	 not set or 0 no limit wil be used.
	 *
	 * @return \Scrivo\Str[] An array of strings created by splitting the
	 *	 string parameter on boundaries formed by the delimiter. If the
	 *	 delimiter was not found and array containing a copy of this string
	 *	 will be returned except if limit was negative, in that case an
	 *	 empty array will be returned.
	 *
	 * @throws \Scrivo\SystemException If an empty search string was given.
	 */
	public function split(\Scrivo\Str $delimiter, $limit=0) {
		if ($delimiter == "") {
			throw new \Scrivo\SystemException(
					"split cannot use an empty \"\" delimiter.");
		}
		$r = $limit ? explode($delimiter, $this->str, $limit)
			: explode($delimiter, $this->str);
		foreach ($r as $k=>$v) {
			$r[$k] = new \Scrivo\Str($v);
		}
		return $r;
	}

	/**
	 * Get a copy of this string with all of its characters converted to lower
	 * case.
	 *
	 * @return \Scrivo\Str A string containing only lower case characters.
	 */
	public function toLowerCase() {
		return new \Scrivo\Str(mb_strtolower($this->str, "UTF-8"));
	}

	/**
	 * Get a copy of this string with all of its characters converted to upper
	 * case.
	 *
	 * @return \Scrivo\Str A string containing only upper case characters.
	 */
	public function toUpperCase() {
		return new \Scrivo\Str(mb_strtoupper($this->str, "UTF-8"));
	}

	/**
	 * Compare this string to another \Scrivo\Str object.
	 *
	 * Note that this method requires the \Scrivo\Str collator to be set,
	 * else the method falls back to the default locale for creating a
	 * collator and generates a warning.
	 *
	 * @param \Scrivo\Str $str The string to compare this string to.
	 *
	 * @return int Less than 0 if this string is less than the given
	 *   string $str; more than 0 if this string is greater than $str, and
	 *   0 if they are equal.
	 */
	public function compareTo(\Scrivo\Str $str) {
		return self::getCollator()->compare($this->str, $str);
	}

	/**
	 * Check if this string exists an array of \Scrivo\Str-s.
	 *
	 * @param \Scrivo\Str $arr The array to search.
	 *
	 * @return mixed If found the key of the first occurance of the string
	 *    in the array, else null.
	 */
	public function inArray($arr) {
		foreach ($arr as $k=>$v) {
			if ($v->equals($this)) {
				return $k;
			}
		}
		return null;
	}
}

?>