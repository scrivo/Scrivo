<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: Request.php 858 2013-08-22 13:03:03Z geert $
 */

/**
 * Implementation of the \Scrivo\Session class.
 */

namespace Scrivo;

/**
 */
class Request {

	const TYPE_INTEGER = 1;
	const TYPE_STRING = 2;
	const TYPE_BOOLEAN = 3;
	const TYPE_DATE_TIME = 4;

	const DEF = "_scrivo_def_param_";

	/* Fix GPC Variables */
	private static function fetch2($v, $t, $d) {
		if (is_array($v)) {

			foreach($v as $k2=>$v2) {
				$v[$k2] = self::fetch2($v2, $t, $d);
			}
			return $v;

		} else {

			if (self::TYPE_BOOLEAN != $t) {
				if (null === $v) {
					if ($d) {
						return $d;
					}
					throw new \Scrivo\SystemException(
							"Invalid request parameter");
				}
			}

			switch ($t) {
				case self::TYPE_STRING:
					return new \Scrivo\Str($v);
				case self::TYPE_INTEGER:
					return intval($v);
				case self::TYPE_BOOLEAN:
					return null !== $v;
				case self::TYPE_DATE_TIME:
					$dt = "\d{4}-\d{2}-\d{2}";
					if (preg_match("/^$dt \d{2}:\d{2}:\d{2}$|^$dt$/", $v)) {
						return new \DateTime($v);
					}
					return null;
			}

			throw new \Scrivo\SystemException("Invalid request parameter type");
		}
	}

	private static function fetch($arr, $name, $type, $default) {

		if (self::TYPE_BOOLEAN == $type) {
			return isset($arr[$name]) ?
				self::fetch2($arr[$name], $type, $default) : false;
		} else {
			if (array_key_exists($name, $arr)) {
				return self::fetch2(@$arr[$name], $type, $default);
			} else {
				if ($default !== self::DEF) {
					return $default;
				}
			}
		}
		throw new \Scrivo\SystemException(
			"Invalid request parameter type '$name'");
	}

	public static function get($name, $type, $default=self::DEF) {
		return self::fetch($_GET, $name, $type, $default);
	}
	public static function post($name, $type, $default=self::DEF) {
		return self::fetch($_POST, $name, $type, $default);
	}
	public static function request($name, $type, $default=self::DEF) {
		return self::fetch($_REQUEST, $name, $type, $default);
	}

}

?>