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
 * $Id: ArgumentsCheck.php 546 2013-03-01 12:51:42Z geert $
 */

/**
 * Implementation of the \Scrivo\ArgumentCheck class.
 */

namespace Scrivo;

/**
 * The ArgumentCheck class contains utility functions for type checking
 * of arguments. All public methods in the Scrivo library should implement
 * type checking of function arguments, either by PHP type hinting for
 * non-scalar types or runtime type checking of scalar types.
 */
class ArgumentCheck {

	/**
	 * Constant to denote an integer type.
	 */
	const TYPE_INTEGER = 1;

	/**
	 * Constant to denote a boolean type.
	 */
	const TYPE_BOOLEAN = 2;

	/**
	 * Constant to denote a float type.
	 */
	const TYPE_FLOAT = 3;

	/**
	 * Constant to denote a string type.
	 */
	const TYPE_STRING = 4;

	/**
	 * Check the argument count, types and values of all arguments passed
	 * to a method or function.  See also ArgumentCheck::assert.
	 *
	 * function aFunc(\Scrivo\String $aStr, $anInt=0) {
	 *     \Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
	 *             // String already tested by type hint
	 *             null
	 *             // Test for integer type and in the set (1,4,9)
	 * 	           array(\Scrivo\ArgumentCheck::TYPE_INTEGER, array(1,4,9)
	 *         ),
	 *     // At least one argument needs to be given.
	 *     1);
	 *
	 * @param array $arguments Argument list (func_get_args).
	 * @param array $args An array with values that the arguments need to
	 *    comply with. Use null to skip the test.
	 * @param number $minCount If a variable length argument list was used,
	 *    can use this to set the minimum number of arguments.
	 */

	public static function assertArgs($arguments, array $args, $minCount=-1) {
		$c = count($arguments);
		if ($c > count($args)) {
			throw new \Scrivo\SystemException("Too many arguments.");
		} else if ($minCount >= 0 && $c < $minCount) {
			throw new \Scrivo\SystemException("Missing arguments.");
		} else {
			for ($i=0; $i<$c; $i++) {
				$a = $args[$i];
				if ($a !== null) {
					if ($minCount >= 0 && $i >= $minCount
							&& $arguments[$i] === null) {
						// optional argument with null value.
					} else {
						self::assert(
							$arguments[$i], $a[0], isset($a[1]) ? $a[1] : null);
					}
				}
			}
		}
	}

	/**
	 * Test if a variable or set of variables in an array comply to a
	 * certain (set of) type(s), and optionally test if the variable value(s)
	 * exist in a given set of values.
	 *
	 * function aFunc($arg) {
	 *     // Test for boolean type
	 *     \Scrivo\ArgumentCheck::assert(
	 * 	       $arg, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
	 *
	 * function aFunc($arg) {
	 *     // Test for integer type and in the set (1,4,9)
	 *     \Scrivo\ArgumentCheck::assert(
	 * 	      $arg, \Scrivo\ArgumentCheck::TYPE_INTEGER, array(1,4,9));
	 *
	 * function aFunc($arg) {
	 *     // Test for integer or string type
	 *     \Scrivo\ArgumentCheck::assert(
	 * 	       $arg, array(\Scrivo\ArgumentCheck::TYPE_BOOLEAN,
	 *             \Scrivo\ArgumentCheck::TYPE_STRING));
	 *
	 * function aFunc(array $arg) {
	 *     // Test for array of string type: note array in function arguments
	 *     \Scrivo\ArgumentCheck::assert(
	 * 	       $arg, \Scrivo\ArgumentCheck::TYPE_STRING));
	 *
	 *
	 * @param mixed|mixed[] $arg The variable to test.
	 * @param mixed|mixed[] $type The assumed type(s) of the variable, one
	 *   or more (array) out of the \Scrivo\ArgumentsCheck::TYPE_* constants.
	 * @param mixed[] $set Optional to test if the given argument exists in
	 *   a specific set.
	 * @return boolean True argument is of the given type(s) and optionally
	 *   exists in the given set, false if not.
	 */
	public static function assert($arg, $type, array $set=null) {
		$res = true;
		if (is_array($arg)) {
			for ($i=0; $i<count($arg) && $res; $i++) {
				$res = self::assertArg($arg[$i], $type, $set);
			}
		} else {
				$res = self::assertArg($arg, $type, $set);
		}
		if (!$res) {
			throw new \Scrivo\SystemException("Invalid argument type");
		}
	}

	/**
	 * Test if a variable complies to a certain (set of) type(s), and
	 * optionally test if it exists in a given set.
	 *
	 * @param mixed $arg The variable to test.
	 * @param mixed|mixed[] $type The assumed type(s) of the variable, one
	 *   or more (array) out of the \Scrivo\ArgumentsCheck::TYPE_* constants.
	 * @param mixed[] $set Optional to test if the given argument exists in
	 *   a specific set.
	 * @return boolean True argument is of the given type(s) and optionally
	 *   exists in the given set, false if not.
	 */
	private static function assertArg($arg, $type, array $set=null) {
		$res = false;
		if (is_array($type)) {
			for ($i=0; $i<count($type) && !$res; $i++) {
				$res = self::isType($arg, $type[$i]);
			}
		} else {
			$res = self::isType($arg, $type);
		}
		if (!$set) {
			return $res;
		}
		foreach ($set as $v) {
			if ($v === $arg) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Simple test for scalar types.
	 *
	 * @param mixed $arg The variable to test.
	 * @param int $type The assumed type of the variable, one out of the
	 *    \Scrivo\ArgumentsCheck::TYPE_* constants.
	 * @throws \Scrivo\SystemException If the $type argument is not one out of
	 *    the \Scrivo\ArgumentsCheck::TYPE_* constants.
	 * @return boolean True if the assumed type is correct, false if not.
	 */
	private static function isType($arg, $type) {
		switch ($type) {
			case self::TYPE_INTEGER: return is_int($arg);
			case self::TYPE_BOOLEAN: return is_bool($arg);
			case self::TYPE_FLOAT: return is_float($arg);
			case self::TYPE_STRING: return is_string($arg);
			default:
				if (is_object($arg) && get_class($arg) === $type) {
					return true;
				}
		}
		throw new \Scrivo\SystemException("No such argument type");
	}

}

?>