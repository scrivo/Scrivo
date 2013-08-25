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
 * $Id: ByteArrayTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");
use Scrivo\ByteArray;

class ByteArrayTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test string: note this is an UTF-8 string of 9 bytes width: the € sign
	 * is three bytes width (0xE2 0x82 0xAC), the ë character two (0xC3 0xAB).
	 */
	const testStr = "a€cDëf";

	/**
	 * Test if count and length return the character width and not the
	 * byte width.
	 */
	public function testCount() {
		$fancyUtf8 = new ByteArray("ƕƺ░☺൩𢯊");
		// byte width
		$this->assertEquals(17, strlen($fancyUtf8));
		// character width
		$this->assertEquals(17, count($fancyUtf8));
		$this->assertEquals(17, $fancyUtf8->length);
	}

	/**
	 * Test string creation with the factory method create().
	 */
	public function testCreate() {
		// Create an array with two Utf8Stings
		$str = ByteArray::create(array("string", "longer string"));
		// Check what we've got
		$this->assertInstanceOf("\Scrivo\ByteArray", $str[0]);
		$this->assertInstanceOf("\Scrivo\ByteArray", $str[1]);
		$this->assertTrue($str[0]->length < $str[1]->length);
	}

	/**
	 * ByteArray objects have a magic __get() so test invalid proterties.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidProperty() {
		$str = new ByteArray("test");
		$tmp = $str->hatseFlatse;
	}

	/**
	 * Test iterating through the characters of a string.
	 */
	public function testIterator() {
		$data = array("%", "#", "A", "a", "*", "?", "/", "P", "c", "+", "\\");
		$str = new ByteArray(implode("", $data));
		foreach ($str as $k=>$v) {
			$this->assertEquals($data[$k], (string)$v);
			$this->assertTrue($v->equals(new ByteArray($data[$k])));
		}
	}

	/**
	 * Test array access tot the individual characters of a string.
	 */
	public function testArrayAccess() {

		$data = array("%", "#", "A", "a", "*", "?", "/", "P", "c", "+", "\\");
		$str = new ByteArray(implode("", $data));
		for ($i = 0; $i < count($str); $i++) {
			$this->assertEquals($data[$i], (string)$str[$i]);
			$this->assertTrue($str[$i]->equals(new ByteArray($data[$i])));
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
		$str = new ByteArray(self::testStr);
		$a = $str[$str->length];
	}

	/**
	 * Strings are immuatble: test exception on replacing characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessSetCharacter() {
		$str = new ByteArray(self::testStr);
		$str[1] = new ByteArray("x");
	}

	/**
	 * Strings are immuatble: test exception on add characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessAddCharacter() {
		$str = new ByteArray(self::testStr);
		$str[] = new ByteArray("s");
	}

	/**
	 * Strings are immuatble: test exception on unsetting characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testArrayAccessUnsetCharacter() {
		$this->setExpectedException("\Scrivo\SystemException");
		$str = new ByteArray(self::testStr);
		unset($str[1]);
	}

	/**
	 * Test various substring constructs using substr.
	 */
	public function testSubstr() {

		$str = new ByteArray(self::testStr);

		$this->assertTrue($str->substr(-1)->equals(new ByteArray("f")));
		$this->assertTrue($str->substr(-3)->equals(new ByteArray("ëf")));
		$this->assertTrue($str->substr(-4, 1)->equals(new ByteArray("D")));
		$this->assertTrue($str->substr(0, -1)->equals(new ByteArray("a€cDë")));
		$this->assertTrue($str->substr(4, -1)->equals(new ByteArray("cDë")));
		$this->assertTrue($str->substr(5, -4)->equals(new ByteArray("")));
		$this->assertTrue($str->substr(-4, -1)->equals(new ByteArray("Dë")));
	}

	/**
	 * Strings are immuatble: test exception on unsetting characters.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testSubstrOutOfRange() {
		$str = new ByteArray(self::testStr);
		$tmp = $str->substr(9);
	}

	/**
	 * Test various substring constructs using substring.
	 */
	public function testSubstring() {
		$this->assertTrue(
			ByteArray::create("hamburger")->substring(4, 8)->equals(
				new ByteArray("urge")));
		$this->assertTrue(
			ByteArray::create("smiles")->substring(1, 5)->equals(
				new ByteArray("mile")));
	}

	/**
	 * Data provider for substring testing.
	 */
	public function providerSubstringOutOfRange() {
		   return array(array(1, 9), array(-1, 1), array(2, 1));
	}

	/**
	 * Test various situations where substring indexing raises exceptions.
	 *
	 * @dataProvider providerSubstringOutOfRange
	 */
	public function testSubstringOutOfRange($start, $end) {
		$str = new ByteArray(self::testStr);
		$this->setExpectedException("\Scrivo\SystemException");
		$tmp = $str->substring($start, $end);
	}

	/**
	 * Test string trimming.
	 */
	public function testTrim() {
		$str = new ByteArray("\r\n   \t Test\t\n");
		$this->assertTrue($str->trim()->equals(new ByteArray("Test")));
	}

	/**
	 * Test contains method of ByteArray.
	 */
	public function testContains() {

		$str = new ByteArray(self::testStr);

		$this->assertTrue($str->contains(new ByteArray("€")));
		$this->assertFalse($str->contains(new ByteArray("€"), 2));
		// can't do uppercase on mutlibyte characters:
		$this->assertFalse($str->contains(new ByteArray("Ë"), 2, true));
		$this->assertFalse($str->contains(new ByteArray("Cd")));
		$this->assertTrue($str->contains(new ByteArray("Cd"), 0, true));
		$this->assertTrue($str->contains(new ByteArray("ëf")));
		$this->assertFalse($str->contains(new ByteArray("Ëf")));
		$this->assertTrue($str->contains(new ByteArray("ëF"), 0, true));
		$this->assertFalse($str->contains(new ByteArray("q")));
	}

	/**
	 * Test string contains method when trying to find a string out of index
	 * range.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testContainsOutOfRange() {
		$str = new ByteArray(self::testStr);
		$tmp = $str->contains(new ByteArray("€"), $str->length);
	}

	/**
	 * Test indexOf method of ByteArray.
	 */
	public function testIndexOf() {

		$str = new ByteArray("a€cDëfa€cDëf");

		$this->assertEquals(1, $str->indexOf(new ByteArray("€")));
		$this->assertEquals(10, $str->indexOf(new ByteArray("€"), 2));
		// Can't do byte-type uppercase on UTF-8 characters
		$this->assertEquals(-1, $str->indexOf(new ByteArray("Ë"), 2, true));
		$this->assertEquals(-1, $str->indexOf(new ByteArray("Cd")));
		$this->assertEquals(4, $str->indexOf(new ByteArray("Cd"), 0, true));
		$this->assertEquals(6, $str->indexOf(new ByteArray("ëf")));
		$this->assertEquals(-1, $str->indexOf(new ByteArray("Ëf")));
		$this->assertEquals(6, $str->indexOf(new ByteArray("ëF"), 0, true));
		$this->assertEquals(-1, $str->indexOf(new ByteArray("q")));
	}

	/**
	 * Test lastIndexOf method of ByteArray.
	 */
	public function testLastIndexOf() {
		$str = new ByteArray("a€cDëfa€cDëf");
		$this->assertEquals(10, $str->lastIndexOf(new ByteArray("€")));
		$this->assertEquals(1, $str->lastIndexOf(new ByteArray("€"), -9));
		$this->assertEquals(-1, $str->lastIndexOf(new ByteArray("€"), 11));
		$this->assertEquals(-1,
			$str->lastIndexOf(new ByteArray("Ë"), 2, true));
		$this->assertEquals(-1, $str->lastIndexOf(new ByteArray("Cd")));
		$this->assertEquals(13,
			$str->lastIndexOf(new ByteArray("Cd"), 0, true));
		$this->assertEquals(15, $str->lastIndexOf(new ByteArray("ëf")));
		$this->assertEquals(-1, $str->lastIndexOf(new ByteArray("Ëf")));
		$this->assertEquals(15,
			$str->lastIndexOf(new ByteArray("ëF"), 0, true));
		$this->assertEquals(-1, $str->lastIndexOf(new ByteArray("q")));
	}

	/**
	 * Provider for (last)indexOf out of range testing.
	 */
	public function providerIndexOfOutOfRange() {
		return array(array(9), array(-9));
	}

	/**
	 * Test index out of range for the indexOf method of ByteArray.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @dataProvider providerIndexOfOutOfRange
	 */
	public function testIndexOfOutOfRange($off) {
		$str = new ByteArray(self::testStr);
		$tmp = $str->indexOf(new ByteArray("€"), $off);
	}

	/**
	 * Test index out of range for the lastIndexOf method of ByteArray.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @dataProvider providerIndexOfOutOfRange
	 */
	public function testLastIndexOfOutOfRange($off) {
		$str = new ByteArray(self::testStr);
		$tmp = $str->lastIndexOf(new ByteArray("€"), $off);
	}

	/**
	 * Test firstOccurranceOf method of ByteArray.
	 */
	public function testFirstOccurranceOf() {

		$str = new ByteArray("nä-a€cD-ëf");

		// Part after, case sensitive
		$this->assertTrue(
			$str->firstOccurranceOf(new ByteArray("-"))->equals(
				new ByteArray("-a€cD-ëf")));

		// Part before, case sensitive
		$this->assertTrue(
			$str->firstOccurranceOf(new ByteArray("-"), true)->equals(
				new ByteArray("nä")));

		// No match
		$this->assertNull($str->firstOccurranceOf(new ByteArray("at")));

		// Part after, case insensitive
		$this->assertTrue(
			$str->firstOccurranceOf(new ByteArray("A€"), false, true)->equals(
				new ByteArray("a€cD-ëf")));

		// Part before, case insensitive
		$this->assertTrue(
			$str->firstOccurranceOf(new ByteArray("€C"), true, true)->equals(
				new ByteArray("nä-a")));

		// No match
		$this->assertNull($str->firstOccurranceOf(new ByteArray("A€")));
	}

	/**
	 * Test exception thrown by firstOccurranceOf method of ByteArray.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testFirstOccurranceOfInvalidArgument() {
		$str = new ByteArray("nä-a€cD-ëf");
		$str->firstOccurranceOf(new ByteArray(""));
	}

	/**
	 * Test lastOccurranceOf method of ByteArray.
	 */
	public function testLastOccurranceOf() {

		$str = new ByteArray("nä-a€cD-ëf");
		// Part after, case sensitive
		$this->assertTrue(
			$str->lastOccurranceOf(new ByteArray("-"))->equals(
				new ByteArray("-ëf")));

		// Part before, case sensitive
		$this->assertTrue(
			$str->lastOccurranceOf(new ByteArray("-"), true)->equals(
				new ByteArray("nä-a€cD")));

		// No match
		$this->assertNull($str->lastOccurranceOf(new ByteArray("t")));

		// Part after, case insensitive
		$this->assertTrue(
			$str->lastOccurranceOf(new ByteArray("A"), false, true)->equals(
				new ByteArray("a€cD-ëf")));

		// Part before, case insensitive
		$this->assertTrue(
			$str->lastOccurranceOf(new ByteArray("C"), true, true)->equals(
				new ByteArray("nä-a€")));
	}

	/**
	 * Data provider for exception tests for lastOccurranceOf method of
	 * ByteArray.
	 */
	public function providerLastOccurranceOfInvalidArgument() {
		return array(array(new ByteArray("cD")), array(new ByteArray("")));
	}

	/**
	 * Exception tests for lastOccurranceOf method of ByteArray.
	 *
	 * @expectedException \Scrivo\SystemException
	 * @dataProvider providerLastOccurranceOfInvalidArgument
	 */
	public function testLastOccurranceOfInvalidArgument($delem) {
		$str = new ByteArray("nä-a€cD-ëf");
		$str->lastOccurranceOf($delem);
	}

	/**
	 * Test various replace scenarios.
	 */
	public function testReplace() {

		$str = new ByteArray("Lotje leerde Leentje lopen");

		// Arguments: string, string
		$str2 = $str->replace(
			new ByteArray("e"),
			new ByteArray("#")
		);
		$this->assertTrue($str2->equals(
			new ByteArray("Lotj# l##rd# L##ntj# lop#n")));

		// Arguments: array, array
		$str2 = $str->replace(
			array(
				new ByteArray("Lo"),
				new ByteArray("Le"),
				new ByteArray("e")
			),
			array(
				new ByteArray("Ma"),
				new ByteArray("Mo"),
				new ByteArray("#")
			)
		);
		$this->assertTrue($str2->equals(
			new ByteArray("Matj# l##rd# Mo#ntj# lop#n")));

		// Arguments: array, string
		$str2 = $str->replace(
			array(
				new ByteArray("Lo"),
				new ByteArray("Le"),
				new ByteArray("e")
			),
			new ByteArray("#")
		);
		$this->assertTrue($str2->equals(
			new ByteArray("#tj# l##rd# ##ntj# lop#n")));
	}

	/**
	 * Test invalid arguments for replace method of ByteArray.
	 * - two PHP strings.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments1() {
		$str = new ByteArray("Lotje leerde Leentje lopen");
		$str2 = $str->replace("e", "#");
	}

	/**
	 * Test invalid arguments for replace method of ByteArray.
	 * - Unequal size arrays.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments2() {
		$str = new ByteArray("Lotje leerde Leentje lopen");
		$str2 = $str->replace(
			array(
				new ByteArray("Lo"),
				new ByteArray("Le"),
				new ByteArray("e")),
			array(
				new ByteArray("Ma"),
				new ByteArray("Mo")
			)
		);
	}

	/**
	 * Test invalid arguments for replace method of ByteArray.
	 * - An array with (al least) one entry that is not an ByteArray when
	 *   using two arrays as arguments.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments3() {
		$str = new ByteArray("Lotje leerde Leentje lopen");
		$str2 = $str->replace(
			array(
				new ByteArray("Lo"),
				new ByteArray("Le"),
				new ByteArray("e")),
			array(
				new ByteArray("Ma"),
				"Mo",
				new ByteArray("#")
			)
		);
	}

	/**
	 * Test invalid arguments for replace method of ByteArray.
	 * - An array with (al least) one entry that is not an ByteArray when
	 *   using an array and a ByteArray as arguments.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testReplaceArguments4() {
		$str = new ByteArray("Lotje leerde Leentje lopen");
		$str2 = $str->replace(
			array(
				new ByteArray("Lo"),
				"Le",
				new ByteArray("e")
			),
			new ByteArray("#"));
	}

	/**
	 * Test split method of ByteArray.
	 */
	public function testSplit() {
		$str = new ByteArray("Lotje leerde Leentje lopen");

		// Split with no limit.
		$res = $str->split(new ByteArray("ee"));
		$this->assertTrue($res[0]->equals(new ByteArray("Lotje l")));
		$this->assertTrue($res[1]->equals(new ByteArray("rde L")));
		$this->assertTrue($res[2]->equals(new ByteArray("ntje lopen")));

		// Split with limit 1.
		$res = $str->split(new ByteArray("ee"), 1);
		$this->assertTrue($res[0]->equals(
			new ByteArray("Lotje leerde Leentje lopen")));

		// Split with limit 2.
		$res = $str->split(new ByteArray("ee"), 2);
		$this->assertTrue($res[0]->equals(new ByteArray("Lotje l")));
		$this->assertTrue($res[1]->equals(new ByteArray("rde Leentje lopen")));

		// Split using a delimiter that does not occur in the string.
		$res = $str->split(new ByteArray("asdf"));
		$this->assertTrue($res[0]->equals(
			new ByteArray("Lotje leerde Leentje lopen")));
	}

	/**
	 * Test exception thrown by split method of ByteArray when using an
	 * invalid argument.
	 *
	 * @expectedException \Scrivo\SystemException
	 */
	public function testSplitException() {
		$str = new ByteArray("Lotje leerde Leentje lopen");
		$res = $str->split(new ByteArray(""));
	}

	/**
	 * Test toUpperCase method of ByteArray.
	 */
	public function testToUpperCase() {
		$str = ByteArray::create("na-acD-ef")->toUpperCase();
		$this->assertTrue($str->equals(new ByteArray("NA-ACD-EF")));
	}

	/**
	 * Test toLowerCase method of ByteArray.
	 */
	public function testToLowerCase() {
		$str = ByteArray::create("NA-ACd-EF")->toLowerCase();
		$this->assertTrue($str->equals(new ByteArray("na-acd-ef")));
	}

	/**
	 * Data provider for testing the compare method of ByteArray. Just
	 * some ordering rules I found on the wikipedia page on alphabetical
	 * ordering.
	 */
	public function providerCompare() {
		return array(
			array("locale" => "C", "data" => array(
				"ssi", "ssl", "sso")),
			array("locale" => "C", "data" => array(
				"0", "1", "2", "a", "d", "e")),
		);
	}

	/**
	 * Test the compare method of ByteArray.
	 *
	 * @dataProvider providerCompare
	 */
	public function testCompare($locale, $data) {

		// An array with the original data.
		$orig = ByteArray::create($data);

		// As shuffled array.
		$shuffled = ByteArray::create($data);
		shuffle($shuffled);

		// Sort the shuffled array.
		usort($shuffled, function($a, $b) { return $a->compareTo($b); });

		// An assert the result.
		for ($i=0; $i<count($orig); $i++) {
			$this->assertTrue($orig[$i]->equals($shuffled[$i]));
		}
	}

}

?>