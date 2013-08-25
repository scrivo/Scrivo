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
 * $Id: SystemInfo.php 860 2013-08-24 12:42:15Z geert $
 */

namespace ScrivoUi\DbConsole\Lib;

class SystemInfo {

	function __construct() {

		preg_match("/^(\d+)\.(\d+)\.(\d+)/", phpversion(), $m);

		$this->phpVersion = array(intval($m[1]), intval($m[2]), intval($m[3]));

		$this->zlib = phpversion('zlib');
		$this->pdo = phpversion('pdo');
		$this->pdoMySql = phpversion('pdo_mysql');

		$dir = str_replace(
			array("/scrivo/dbc/index.php", "/\scrivo/\dbc/\index.php")
				, "", $_SERVER["SCRIPT_FILENAME"]);

		$fp = fileperms($dir);
		$this->homePermissions = array(
			"user" => array($fp&0x100?1:0, $fp&0x80?1:0, $fp&0x40?1:0),
			"group" => array($fp&0x20?1:0, $fp&0x10?1:0, $fp&0x8?1:0),
			"other" => array($fp&0x4?1:0, $fp&0x2?1:0, $fp&0x1?1:0)
		);

		$this->homeDir = $dir;
		$this->homeWritable = is_writable($dir);

	}
}

?>
