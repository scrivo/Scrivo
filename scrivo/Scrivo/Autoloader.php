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
 * $Id: Autoloader.php 801 2013-08-11 22:38:40Z geert $
 */

/**
 * Implementation of the \Scrivo\Autoloader class.
 */

namespace Scrivo;

/**
 * The autoloader class for Scrivo classes.
 *
 * Typical usage:
 *
 * <?php
 *
 * require_once("scrivo/Scrivo/Autoloader.php");
 * spl_autoload_register("\\Scrivo\\Autoloader::load");
 *
 * // Now use Scrivo classes:
 * $str = new \Scrivo\Str("A new string");
 * $parts = $str->split(" ");
 * ...
 * ?>
 *
 */
class Autoloader {

	/**
	 * The method to include the source file for a given class to use in
	 * the PHP spl_autoload_register function.
	 *
	 * @param string A name of a Scrivo class.
	 *
	 * @return boolean True if the source file was successfully included.
	 */
	public static function load($class) {
		if (substr($class, 0, 7) !== "Scrivo\\") {
			return false;
		}
		$c = str_replace("\\", "/", substr($class, 7)).".php";
		$res = @include(__DIR__."/$c");
		return $res==1 ? true : false;
	}

}

?>