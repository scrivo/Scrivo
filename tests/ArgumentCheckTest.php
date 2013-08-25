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
 * $Id: ArgumentCheckTest.php 482 2012-12-25 16:42:41Z geert $
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

/**
 * Tests for class \Scrivo\ArgumentCheck
 */
class ArgumentCheckTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
	}

	public static function setUpAfterClass() {
	}

	/**
	 * Test data for argument checks that should succeed.
	 */
	function dataProviderValidScenarios() {
		return array(
			"test 1" => array(
				"value" => 3,
				"type" => \Scrivo\ArgumentCheck::TYPE_INTEGER,
				"set" => array(1, 2, 3)
			),
			"test 2" => array(
				"value" => true,
				"type" => \Scrivo\ArgumentCheck::TYPE_BOOLEAN,
				null
			),
			"test 3" => array(
				"value" => 2.4,
				"type" => \Scrivo\ArgumentCheck::TYPE_FLOAT,
				null
			),
			"test 4" => array(
				"value" => "Hi there",
				"type" => \Scrivo\ArgumentCheck::TYPE_STRING,
				null
			),
			"test 5" => array(
				"value" => array("This", "is", "a", "string", "array"),
				"type" => \Scrivo\ArgumentCheck::TYPE_STRING,
				null
			),
			"test 6" => array(
				"value" => array(3, 2, 3, 1),
				"type" => \Scrivo\ArgumentCheck::TYPE_INTEGER,
				"set" => array(1, 2, 3)
			),
			"test 7" => array(
				"value" => 1,
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => NULL
			),
			"test 8" => array(
				"value" => "Hi there",
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => NULL
			),
			"test 9" => array(
				"value" => array("Hi there", 1),
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => NULL
			),
			"test 10" => array(
				"value" => array("Hi there", 1),
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => array("Hi there", 1, 2, 3)
			),
		);
	}

	/**
	 * Test data for argement checks that should fail.
	 */
	function dataProviderInvalidScenarios() {
		return array(
			"test 1" => array(
				"value" => 4,
				"type" => \Scrivo\ArgumentCheck::TYPE_INTEGER,
				"set" => array(1, 2, 3)
			),
			"test 2" => array(
				"value" => 0,
				"type" => \Scrivo\ArgumentCheck::TYPE_BOOLEAN,
				null
			),
			"test 3" => array(
				"value" => 2,
				"type" => \Scrivo\ArgumentCheck::TYPE_FLOAT,
				null
			),
			"test 4" => array(
				"value" => true,
				"type" => \Scrivo\ArgumentCheck::TYPE_STRING,
				null
			),
			"test 5" => array(
				"value" => array("This", 1, "a", "string", "array"),
				"type" => \Scrivo\ArgumentCheck::TYPE_STRING,
				null
			),
			"test 6" => array(
				"value" => array(3, 2, 4, 1),
				"type" => \Scrivo\ArgumentCheck::TYPE_INTEGER,
				"set" => array(1, 2, 3)
			),
			"test 7" => array(
				"value" => false,
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => NULL
			),
			"test 8" => array(
				"value" => 3.14,
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => NULL
			),
			"test 9" => array(
				"value" => array("Hi there", false),
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => NULL
			),
			"test 10" => array(
				"value" => array("Hi there", 1),
				"type" => array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
					\Scrivo\ArgumentCheck::TYPE_STRING),
				"set" => array("Hi there!", 1, 2, 3)
			),
			"test 11" => array(
				"value" => 2,
				"type" => 34,
				"set" => null
			),
		);
	}

	/**
	 * Test scenarios in which the argument checks should succeed.
	 *
	 * @dataProvider dataProviderValidScenarios
	 */
	public function testValidScenarios($value, $type, $set) {

		$tmp = 3;

		\Scrivo\ArgumentCheck::assert($value, $type, $set);

		// Test no exception was thrown
		$this->assertEquals(3, $tmp);
	}

	/**
	 * Test scenarios in which the argument checks should fail.
	 *
	 * @dataProvider dataProviderInvalidScenarios
	 * @expectedException \Scrivo\SystemException
	 */
	public function testInvalidScenarios($value, $type, $set) {

		\Scrivo\ArgumentCheck::assert($value, $type, $set);
	}

}

?>