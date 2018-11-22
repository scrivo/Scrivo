<?php
/* Copyright (c) 2011, Geert Bergman (geert@scrivo.nl)
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
 * $Id: StringTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

class StringTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test if count and length return the character width and not the
	 * byte width.
	 */
	public function testCount() {
		$fancyUtf8 = new \Scrivo\Str("ƕƺ░☺൩𢯊");
		// byte width
		$this->assertEquals(17, strlen($fancyUtf8));
		// character width
		$this->assertEquals(6, count($fancyUtf8));
		$this->assertEquals(6, $fancyUtf8->length);
	}

	/**
	 * Test string creation with the factory method create().
	 */
	public function testCreate() {
		// Create an array with two Utf8Stings
		$str = \Scrivo\Str::create(array("string", "longer string"));
		// Check what we've got
		$this->assertInstanceOf("\Scrivo\Str", $str[0]);
		$this->assertInstanceOf("\Scrivo\Str", $str[1]);
		$this->assertTrue($str[0]->length < $str[1]->length);
	}

	/**
	 * When creating string you can feed the constructor with strings
	 * containing characters from another encodings (even mixed with UTF-8
	 * character) and the result should be a valid UTF-8 string.
	 */
	public function testStringFixing() {
		// This is what goes in:
		$garbage =
			"Entities to preserve: [&lt;&#60;&#060;&gt;&#62;&#062;".
			"&amp;&#38;&#038;&quot;&#34;&#034;&#39;&#039;], ".
			"Normal text to keep: [<>&\"'%)5aA?], ".
			"Entities to convert: [&euro;&copy;&trade;&#x263A;&#x628;&#x449;], "
			."Valid UTF-8 to keep: [¥ЉẶ░♪𢯊], ".
			"Invalid UTF-8 to convert: [".
			chr(0x80).chr(0xBC).chr(0xDF).chr(0xEB).chr(0xE0)."]";
		// This is what goes out using ISO-8859-1 encoding.
		$fixedIso8859_1 =
			"Entities to preserve: [&lt;&lt;&lt;&gt;&gt;&gt;".
			"&amp;&amp;&amp;&quot;&quot;&quot;&#39;&#39;], ".
			"Normal text to keep: [<>&\"'%)5aA?], ".
			"Entities to convert: [€©™☺بщ], ".
			"Valid UTF-8 to keep: [¥ЉẶ░♪𢯊], ".
			"Invalid UTF-8 to convert: [€¼ßëà]";
		// This is what goes out using CP-1251 encoding.
		$fixedCp1251 =
			"Entities to preserve: [&lt;&lt;&lt;&gt;&gt;&gt;".
			"&amp;&amp;&amp;&quot;&quot;&quot;&#39;&#39;], ".
			"Normal text to keep: [<>&\"'%)5aA?], ".
			"Entities to convert: [€©™☺بщ], ".
			"Valid UTF-8 to keep: [¥ЉẶ░♪𢯊], ".
			"Invalid UTF-8 to convert: [ЂјЯла]";

		// Two times the same test using ISO-8859-1 encoding:
		$this->assertEquals($fixedIso8859_1,
			(string)new \Scrivo\Str($garbage,
			\Scrivo\Str::DECODE_UNRESERVED,
			\Scrivo\Str::ENC_ISO_8859_1));
		$this->assertTrue(\Scrivo\Str::create($fixedIso8859_1)->equals(
			new \Scrivo\Str($garbage,
			\Scrivo\Str::DECODE_UNRESERVED,
			\Scrivo\Str::ENC_ISO_8859_1)));

		// Two times the same test using CP-1251 encoding:
		$this->assertEquals($fixedCp1251,
			(string)new \Scrivo\Str($garbage,
			\Scrivo\Str::DECODE_UNRESERVED,
			\Scrivo\Str::ENC_CP_1251));
		$this->assertTrue(\Scrivo\Str::create($fixedCp1251)->equals(
			new \Scrivo\Str($garbage,
			\Scrivo\Str::DECODE_UNRESERVED,
			\Scrivo\Str::ENC_CP_1251)));
	}

	/**
	 * Test the string fixing capabilities if nothing is to fix.
	 */
	public function testStringFixing2() {
		$this->assertEquals("noting to fix",
			new \Scrivo\Str("noting to fix",
			\Scrivo\Str::DECODE_UNRESERVED,
			\Scrivo\Str::ENC_ISO_8859_1));
	}
	/**
	 * Test string fixing capabilites using a bogus encoding.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testStringFixing3() {
		$this->assertEquals("noting to fix",
			new \Scrivo\Str("noting to fix",
			\Scrivo\Str::DECODE_UNRESERVED, "Blah"));
	}

	/**
	 * \Scrivo\Str objects have a magic __get() so test invalid proterties.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidProperty() {
		$str = new \Scrivo\Str("test");
		$tmp = $str->hatseFlatse;
	}

	/**
	 * Test setting and getting the static string collator object.
	 */
	public function testCollator() {

		$str = new \Scrivo\Str("test");

		// When not initialized it should return a collator with the default
		// locale.
		$this->assertEquals(\Locale::getDefault(),
			$str->collator->getLocale(\Locale::VALID_LOCALE));

		\Scrivo\Str::setCollator(new \Collator("en_US"));
		$this->assertEquals("en_US",
			$str->collator->getLocale(\Locale::VALID_LOCALE));
	}

	/**
	 * Test iterating through the characters of a string.
	 */
	public function testIterator() {
		$data = array("€", "©", "™", "☺", "ب", "щ", "¥", "Љ", "Ặ", "░", "♪");
		$str = new \Scrivo\Str(implode("", $data));
		foreach ($str as $k=>$v) {
			$this->assertEquals($data[$k], (string)$v);
			$this->assertTrue($v->equals(new \Scrivo\Str($data[$k])));
		}
	}

	/**
	 * Test array access tot the individual characters of a string.
	 */
	public function testArrayAccess() {

		$data = array("€", "©", "™", "☺", "ب", "щ", "¥", "Љ", "Ặ", "░", "♪");
		$str = new \Scrivo\Str(implode("", $data));
		for ($i = 0; $i < count($str); $i++) {
			$this->assertEquals($data[$i], (string)$str[$i]);
			$this->assertTrue($str[$i]->equals(new \Scrivo\Str($data[$i])));
		}

		$this->assertTrue(isset($str[count($data)-1]));
		$this->assertFalse(isset($str[count($data)]));

		$this->assertFalse(empty($str[count($data)-1]));
		$this->assertTrue(empty($str[count($data)]));
	}

	/**
	 * Test exception thrown on index out of range.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessOutOfRange() {
		$str = new \Scrivo\Str("a€cdëf");
		$a = $str[$str->length];
	}

	/**
	 * Strings are immuatble: test exception on replacing characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessSetCharacter() {
		$str = new \Scrivo\Str("a€cdëf");
		$str[1] = new \Scrivo\Str("x");
	}

	/**
	 * Strings are immuatble: test exception on add characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessAddCharacter() {
		$str = new \Scrivo\Str("a€cdëf");
		$str[] = new \Scrivo\Str("s");
	}

	/**
	 * Strings are immuatble: test exception on unsetting characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessUnsetCharacter() {
		$this->setExpectedException("\Scrivo\SystemException");
		$str = new \Scrivo\Str("a€cdëf");
		unset($str[1]);
	}

	/**
	 * Test various substring constructs using substr.
	 */
	public function testSubstr() {

		$str = new \Scrivo\Str("a€cdëf");

		$this->assertTrue(
			$str->substr(-1)->equals(new \Scrivo\Str("f")));
		$this->assertTrue(
			$str->substr(-2)->equals(new \Scrivo\Str("ëf")));
		$this->assertTrue(
			$str->substr(-3, 1)->equals(new \Scrivo\Str("d")));
		$this->assertTrue(
			$str->substr(0, -1)->equals(new \Scrivo\Str("a€cdë")));
		$this->assertTrue(
			$str->substr(2, -1)->equals(new \Scrivo\Str("cdë")));
		$this->assertTrue(
			$str->substr(4, -4)->equals(new \Scrivo\Str("")));
		$this->assertTrue(
			$str->substr(-3, -1)->equals(new \Scrivo\Str("dë")));
	}

	/**
	 * Strings are immuatble: test exception on unsetting characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testSubstrOutOfRange() {
		$str = new \Scrivo\Str("a€cdëf");
		$tmp = $str->substr(6);
	}

	/**
	 * Test various substring constructs using substring.
	 */
	public function testSubstring() {
		$this->assertTrue(
			\Scrivo\Str::create("hamburger")->substring(4, 8)->equals(
				new \Scrivo\Str("urge")));
		$this->assertTrue(
			\Scrivo\Str::create("smiles")->substring(1, 5)->equals(
				new \Scrivo\Str("mile")));
	}

	/**
	 * Data provider for substring testing.
	 */
	public function providerSubstringOutOfRange() {
		   return array(array(1, 6), array(-1, 1), array(2, 1));
	}

	/**
	 * Test various situations where substring indexing raises exceptions.
	 *
	 * @dataProvider providerSubstringOutOfRange
	 */
	public function testSubstringOutOfRange($start, $end) {
		$str = new \Scrivo\Str("a€cdëf");
		$this->setExpectedException("\Scrivo\SystemException");
		$tmp = $str->substring($start, $end);
	}

	/**
	 * Test string trimming.
	 */
	public function testTrim() {
		$str = new \Scrivo\Str("\r\n   \t Test\t\n" .
			html_entity_decode("&nbsp;", ENT_QUOTES, "UTF-8"));
		$this->assertTrue($str->trim()->equals(new \Scrivo\Str("Test")));
	}

	/**
	 * Test contains method of \Scrivo\Str.
	 */
	public function testContains() {

		$str = new \Scrivo\Str("a€cDëf");

		$this->assertTrue($str->contains(new \Scrivo\Str("€")));
		$this->assertFalse($str->contains(new \Scrivo\Str("€"), 2));
		$this->assertTrue($str->contains(new \Scrivo\Str("Ë"), 2, true));
		$this->assertFalse($str->contains(new \Scrivo\Str("Cd")));
		$this->assertTrue($str->contains(new \Scrivo\Str("Cd"), 0, true));
		$this->assertTrue($str->contains(new \Scrivo\Str("ëf")));
		$this->assertFalse($str->contains(new \Scrivo\Str("Ëf")));
		$this->assertTrue($str->contains(new \Scrivo\Str("Ëf"), 0, true));
		$this->assertFalse($str->contains(new \Scrivo\Str("q")));
	}

	/**
	 * Test string contains method when trying to find a string out of index
	 * range.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testContainsOutOfRange() {
		$str = new \Scrivo\Str("a€cDëf");
		$tmp = $str->contains(new \Scrivo\Str("€"), $str->length);
	}

	/**
	 * Test indexOf method of \Scrivo\Str.
	 */
	public function testIndexOf() {

		$str = new \Scrivo\Str("a€cDëfa€cDëf");

		$this->assertEquals(
			1, $str->indexOf(new \Scrivo\Str("€")));
		$this->assertEquals(
			7, $str->indexOf(new \Scrivo\Str("€"), 2));
		$this->assertEquals(
			4, $str->indexOf(new \Scrivo\Str("Ë"), 2, true));
		$this->assertEquals(
			-1, $str->indexOf(new \Scrivo\Str("Cd")));
		$this->assertEquals(
			2, $str->indexOf(new \Scrivo\Str("Cd"), 0, true));
		$this->assertEquals(
			4, $str->indexOf(new \Scrivo\Str("ëf")));
		$this->assertEquals(
			-1, $str->indexOf(new \Scrivo\Str("Ëf")));
		$this->assertEquals(
			4, $str->indexOf(new \Scrivo\Str("Ëf"), 0, true));
		$this->assertEquals(
			-1, $str->indexOf(new \Scrivo\Str("q")));
	}

	/**
	 * Test lastIndexOf method of \Scrivo\Str.
	 */
	public function testLastIndexOf() {
		$str = new \Scrivo\Str("a€cDëfa€cDëf");
		$this->assertEquals(
			7, $str->lastIndexOf(new \Scrivo\Str("€")));
		$this->assertEquals(
			1, $str->lastIndexOf(new \Scrivo\Str("€"), -6));
		$this->assertEquals(
			-1, $str->lastIndexOf(new \Scrivo\Str("€"), 8));
		$this->assertEquals(
			10, $str->lastIndexOf(new \Scrivo\Str("Ë"), 2, true));
		$this->assertEquals(
			-1, $str->lastIndexOf(new \Scrivo\Str("Cd")));
		$this->assertEquals(
			8, $str->lastIndexOf(new \Scrivo\Str("Cd"), 0, true));
		$this->assertEquals(
			10, $str->lastIndexOf(new \Scrivo\Str("ëf")));
		$this->assertEquals(
			-1, $str->lastIndexOf(new \Scrivo\Str("Ëf")));
		$this->assertEquals(
			10, $str->lastIndexOf(new \Scrivo\Str("Ëf"), 0, true));
		$this->assertEquals(
			-1, $str->lastIndexOf(new \Scrivo\Str("q")));
	}

	/**
	 * Provider for (last)indexOf out of range testing.
	 */
	public function providerIndexOfOutOfRange() {
		   return array(array(6), array(-6));
	}

	/**
	 * Test index out of range for the indexOf method of \Scrivo\Str.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @dataProvider providerIndexOfOutOfRange
	 */
	public function testIndexOfOutOfRange($off) {
		$str = new \Scrivo\Str("a€cdëf");
		$tmp = $str->indexOf(new \Scrivo\Str("€"), $off);
	}

	/**
	 * Test index out of range for the lastIndexOf method of \Scrivo\Str.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @dataProvider providerIndexOfOutOfRange
	 */
	public function testLastIndexOfOutOfRange($off) {
		$str = new \Scrivo\Str("a€cdëf");
		$tmp = $str->lastIndexOf(new \Scrivo\Str("€"), $off);
	}

	/**
	 * Test firstOccurranceOf method of \Scrivo\Str.
	 */
	public function testFirstOccurranceOf() {

		$str = new \Scrivo\Str("nä-a€cD-ëf");

		// Part after, case sensitive
		$this->assertTrue(
			$str->firstOccurranceOf(new \Scrivo\Str("-"))->equals(
				new \Scrivo\Str("-a€cD-ëf")));

		// Part before, case sensitive
		$this->assertTrue(
			$str->firstOccurranceOf(new \Scrivo\Str("-"), true)->equals(
				new \Scrivo\Str("nä")));

		// No match
		$this->assertNull($str->firstOccurranceOf(new \Scrivo\Str("at")));

		// Part after, case insensitive
		$this->assertTrue($str->firstOccurranceOf(
			new \Scrivo\Str("A€"), false, true)->equals(
				new \Scrivo\Str("a€cD-ëf")));

		// Part before, case insensitive
		$this->assertTrue($str->firstOccurranceOf(
			new \Scrivo\Str("€C"), true, true)->equals(
				new \Scrivo\Str("nä-a")));

		// No match
		$this->assertNull($str->firstOccurranceOf(new \Scrivo\Str("A€")));
	}

	/**
	 * Test exception thrown by firstOccurranceOf method of \Scrivo\Str.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testFirstOccurranceOfInvalidArgument() {
		$str = new \Scrivo\Str("nä-a€cD-ëf");
		$str->firstOccurranceOf(new \Scrivo\Str(""));
	}

	/**
	 * Test lastOccurranceOf method of \Scrivo\Str.
	 */
	public function testLastOccurranceOf() {

		$str = new \Scrivo\Str("nä-a€cD-ëf");

		// Part after, case sensitive
		$this->assertTrue(
			$str->lastOccurranceOf(new \Scrivo\Str("-"))->equals(
				new \Scrivo\Str("-ëf")));

		// Part before, case sensitive
		$this->assertTrue(
			$str->lastOccurranceOf(new \Scrivo\Str("-"), true)->equals(
				new \Scrivo\Str("nä-a€cD")));

		// No match
		$this->assertNull($str->lastOccurranceOf(new \Scrivo\Str("t")));

		// Part after, case insensitive
		$this->assertTrue(
			$str->lastOccurranceOf(
				new \Scrivo\Str("A"), false, true)->equals(
					new \Scrivo\Str("a€cD-ëf")));

		// Part before, case insensitive
		$this->assertTrue(
			$str->lastOccurranceOf(
				new \Scrivo\Str("C"), true, true)->equals(
					new \Scrivo\Str("nä-a€")));

		// No match
		$this->assertNull($str->firstOccurranceOf(new \Scrivo\Str("ö")));
	}

	/**
	 * Data provider for exception tests for lastOccurranceOf method of
	 * \Scrivo\Str.
	 */
	public function providerLastOccurranceOfInvalidArgument() {
		return array(array(new \Scrivo\Str("cD")),
			array(new \Scrivo\Str("")));
	}

	/**
	 * Exception tests for lastOccurranceOf method of \Scrivo\Str.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @dataProvider providerLastOccurranceOfInvalidArgument
	 */
	public function testLastOccurranceOfInvalidArgument($delem) {
		$str = new \Scrivo\Str("nä-a€cD-ëf");
		$str->lastOccurranceOf($delem);
	}

	/**
	 * Test various replace scenarios.
	 */
	public function testReplace() {

		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");

		// Arguments: string, string
		$str2 = $str->replace(
			new \Scrivo\Str("e"),
			new \Scrivo\Str("#")
		);
		$this->assertTrue($str2->equals(
			new \Scrivo\Str("Lotj# l##rd# L##ntj# lop#n")));

		// Arguments: array, array
		$str2 = $str->replace(
			array(
				new \Scrivo\Str("Lo"),
				new \Scrivo\Str("Le"),
				new \Scrivo\Str("e")
			),
			array(
				new \Scrivo\Str("Ma"),
				new \Scrivo\Str("Mo"),
				new \Scrivo\Str("#")
			)
		);
		$this->assertTrue($str2->equals(
			new \Scrivo\Str("Matj# l##rd# Mo#ntj# lop#n")));

		// Arguments: array, string
		$str2 = $str->replace(
			array(
				new \Scrivo\Str("Lo"),
				new \Scrivo\Str("Le"),
				new \Scrivo\Str("e")
			),
			new \Scrivo\Str("#")
		);
		$this->assertTrue($str2->equals(
			new \Scrivo\Str("#tj# l##rd# ##ntj# lop#n")));
	}

	/**
	 * Test invalid arguments for replace method of \Scrivo\Str.
	 * - two PHP strings.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments1() {
		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");
		$str2 = $str->replace("e", "#");
	}

	/**
	 * Test invalid arguments for replace method of \Scrivo\Str.
	 * - Unequal size arrays.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments2() {
		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");
		$str2 = $str->replace(
			array(
				new \Scrivo\Str("Lo"),
				new \Scrivo\Str("Le"),
				new \Scrivo\Str("e")),
			array(
				new \Scrivo\Str("Ma"),
				new \Scrivo\Str("Mo")
			)
		);
	}

	/**
	 * Test invalid arguments for replace method of \Scrivo\Str.
	 * - An array with (al least) one entry that is not an \Scrivo\Str when
	 *   using two arrays as arguments.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments3() {
		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");
		$str2 = $str->replace(
			array(
				new \Scrivo\Str("Lo"),
				new \Scrivo\Str("Le"),
				new \Scrivo\Str("e")),
			array(
				new \Scrivo\Str("Ma"),
				"Mo",
				new \Scrivo\Str("#")
			)
		);
	}

	/**
	 * Test invalid arguments for replace method of \Scrivo\Str.
	 * - An array with (al least) one entry that is not an \Scrivo\Str when
	 *   using an array and a \Scrivo\Str as arguments.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments4() {
		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");
		$str2 = $str->replace(
			array(
				new \Scrivo\Str("Lo"),
				"Le",
				new \Scrivo\Str("e")
			),
			new \Scrivo\Str("#"));
	}

	/**
	 * Test split method of \Scrivo\Str.
	 */
	public function testSplit() {
		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");

		// Split with no limit.
		$res = $str->split(new \Scrivo\Str("ee"));
		$this->assertTrue($res[0]->equals(new \Scrivo\Str("Lotje l")));
		$this->assertTrue($res[1]->equals(new \Scrivo\Str("rde L")));
		$this->assertTrue($res[2]->equals(new \Scrivo\Str("ntje lopen")));

		// Split with limit 1.
		$res = $str->split(new \Scrivo\Str("ee"), 1);
		$this->assertTrue($res[0]->equals(
			new \Scrivo\Str("Lotje leerde Leentje lopen")));

		// Split with limit 2.
		$res = $str->split(new \Scrivo\Str("ee"), 2);
		$this->assertTrue($res[0]->equals(new \Scrivo\Str("Lotje l")));
		$this->assertTrue($res[1]->equals(new \Scrivo\Str(
			"rde Leentje lopen")));

		// Split using a delimiter that does not occur in the string.
		$res = $str->split(new \Scrivo\Str("asdf"));
		$this->assertTrue($res[0]->equals(
			new \Scrivo\Str("Lotje leerde Leentje lopen")));
	}

	/**
	 * Test exception thrown by split method of \Scrivo\Str when using an
	 * invalid argument.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testSplitException() {
		$str = new \Scrivo\Str("Lotje leerde Leentje lopen");
		$res = $str->split(new \Scrivo\Str(""));
	}

	/**
	 * Test toUpperCase method of \Scrivo\Str.
	 */
	public function testToUpperCase() {
		$str = \Scrivo\Str::create("nä-a€cD-ëf")->toUpperCase();
		$this->assertTrue($str->equals(new \Scrivo\Str("NÄ-A€CD-ËF")));
	}

	/**
	 * Test toLowerCase method of \Scrivo\Str.
	 */
	public function testToLowerCase() {
		$str = \Scrivo\Str::create("NÄ-A€Cd-ËF")->toLowerCase();
		$this->assertTrue($str->equals(new \Scrivo\Str("nä-a€cd-ëf")));
	}

	/**
	 * Data provider for testing the compare method of \Scrivo\Str. Just
	 * some ordering rules I found on the wikipedia page on alphabetical
	 * ordering.
	 */
	public function providerCompare() {
		return array(
			array("locale" => "de_DE", "data" => array(
				"ssi", "ßl", "sso")),
			array("locale" => "de_DE", "data" => array(
				"o", "O", "ö", "Ö", "u", "U", "ü", "Ü")),
			array("locale" => "sv_SE", "data" => array(
				"z", "Z", "å", "Å", "ä", "Ä", "ö", "Ö")),
			array("locale" => "et_EE", "data" => array(
				"s", "š", "z", "ž", "w", "õ", "ä", "ö", "ü", "x")),
			array("locale" => "tr_TR", "data" => array(
				"c", "ç", "g", "ğ", "ı", "i", "o", "ö", "s", "ş", "u", "ü"))
		);
	}

	/**
	 * Test the compare method of \Scrivo\Str.
	 *
	 * @dataProvider providerCompare
	 */
	public function testCompare($locale, $data) {

		// An array with the original data.
		$orig = \Scrivo\Str::create($data);

		// As shuffled array.
		$shuffled = \Scrivo\Str::create($data);
		shuffle($shuffled);

		// Set the collator and sort the shuffled array.
		\Scrivo\Str::setCollator(new Collator($locale));
		usort($shuffled, function($a, $b) { return $a->compareTo($b); });

		// An assert the result.
		for ($i=0; $i<count($orig); $i++) {
			$this->assertTrue($orig[$i]->equals($shuffled[$i]));
		}
	}

	/**
	 * Data provider for testing the inArray method of \Scrivo\Str.
	 */
	public function providerInArray() {
		return array(
			\Scrivo\Str::create(array(
				"Lotje",
				"leerde",
				"Leentje",
				"lopen",
				"langs",
				"de lange",
				"lindelaan",
			))
		);
	}

	/**
	 * Test the inArray function of \Scrivo\Str.
	 *
	 * @dataProvider providerInArray
	 */
	public function testInArray($arrayData) {

		$e = count($arrayData)-1;
		$m = intval($e/2);
		$s = 0;

		$this->assertEquals($s, $arrayData[$s]->inArray($arrayData));
		$this->assertEquals($m, $arrayData[$m]->inArray($arrayData));
		$this->assertEquals($e, $arrayData[$e]->inArray($arrayData));

		$this->assertTrue(0 === $arrayData[$s]->inArray($arrayData));
		$this->assertFalse(null === $arrayData[$s]->inArray($arrayData));

		$this->assertNull(
			\Scrivo\Str::create("blah")->inArray($arrayData));
	}

}

?>