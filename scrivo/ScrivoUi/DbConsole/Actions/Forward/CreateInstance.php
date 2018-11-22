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
 * $Id: CreateInstance.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Layout\CreateInstance action
 * class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\I18n;
use \Scrivo\Action;
use \Scrivo\Str;
use \Scrivo\Request;
use \ScrivoUi\DbConsole\Lib\DdlUtil;

/**
 * The CreateInstance layout action class implements an action for adding
 * initial data to a Scrivo database instance, creating a configuration
 * file and a sample template.
 */
class CreateInstance extends Action {

	/**
	 * In this action inital instance data is inserted into the database and
	 * a sample implementation is copied into the application root folder.
	 */
	function doAction() {

		try {

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$fd = Str::create(array(
					"db_host" =>
						Request::post("db_host", Request::TYPE_STRING),
					"db_name" =>
						Request::post("db_name", Request::TYPE_STRING),
					"db_user" =>
						Request::post("db_user", Request::TYPE_STRING),
					"db_pwd" =>
						Request::post("db_pwd", Request::TYPE_STRING),
					"cfg_inst" =>
						Request::post("cfg_inst", Request::TYPE_STRING),
					"cfg_path" =>
						Request::post("cfg_path", Request::TYPE_STRING),
					"cfg_root" =>
						Request::post("cfg_root", Request::TYPE_STRING),
					"cfg_upload" =>
						Request::post("cfg_upload", Request::TYPE_STRING),
					"cfg_pwd" =>
						Request::post("cfg_pwd", Request::TYPE_STRING),
				));
			} else if (isset($this->session->formData)) {
				$fd = unserialize($this->session->formData);
			} else {
				throw \Exception("No data");
			}

			$pdo = DdlUtil::getConnection(
				$fd["db_host"], $fd["db_name"], $fd["db_user"], $fd["db_pwd"]);

			$dat = $pdo->query("SELECT * FROM instance");
			if (!$dat) {
				throw new \Exception(
					"No instance table found: is this a Scrivo database?");
			}

			$this->process($fd["db_host"], $fd["db_name"], $fd["db_user"],
				$fd["db_pwd"], $fd["cfg_inst"], $fd["cfg_path"],
				$fd["cfg_root"], $fd["cfg_upload"], $fd["cfg_pwd"]);

			$this->setResult(self::SUCCESS, null, $fd);

		} catch (\Exception $e) {
			
			$this->setResult(self::FAIL, $e, $fd);

		}

	}

	private function process($dbHost, $dbName, $dbUser, $dbPwd,
			$cfgInstId, $cfgPth, $cfgRoot, $cfgUpload, $cfgPwd) {

		$warn = array();
		$dataDir = "../ScrivoUi/DbConsole/Data";
		$pdo = DdlUtil::getConnection($dbHost, $dbName, $dbUser, $dbPwd);
		$filename = $cfgPth.'/.htscrivo';
		$tmp1 = str_replace("\\", "\\\\", $cfgPth);
		$tmp2 = str_replace("\\", "\\\\", $cfgUpload);
		$str = <<<EOD
; Scrivo configuration file
; Copyright 2002-2013 Scrivo
; All Rights Reserved

; database constants
DB_HOST = {$dbHost}
DB_USER = {$dbUser}
DB_PASSWORD = {$dbPwd}
DB_NAME = {$dbName}

; instance identifier database
INSTANCE_ID = {$cfgInstId}

; physical root of the site directory
DOC_ROOT = {$tmp1}

; http root of the site directory
WWW_ROOT = {$cfgRoot}

; physical location of the uploaded user files
UPLOAD_DIR = {$tmp2}

; comma seperated list of ip-addresses allowed for admin access
ADMIN_IP_ADDRESSES = {$_SERVER["REMOTE_ADDR"]}
EOD;

		if (!$handle = fopen($filename, 'w')) {
			throw new \Exception("Cannot open file '$filename'");
		}

		if (fwrite($handle, $str) === FALSE) {
			throw new \Exception("Cannot write to file '$filename'");
		}

		fclose($handle);
		$test = chmod($filename, 0664);
		if (!$test) {
			$warn[] = "Cannot chmod '$filename'";
		}

		include "$dataDir/init_data.php";

		$sth = $pdo->prepare("INSERT INTO instance ".
			"(instance_id, www_root, document_root, description) VALUES ".
			"(:instId, '', :docRoot, '')");
		$sth->bindParam(":instId", $cfgInstId, \PDO::PARAM_INT);
		$sth->bindParam(":docRoot", $cfgPth, \PDO::PARAM_STR);
		if (!$sth->execute()) {
			$e = $pdo->errorInfo();
			throw new \Exception($e[2]);
		}

		/*[init data]*/

		$salt = base_convert(md5(mt_rand(0, 1000000)), 16, 36);
		$adminww = crypt($cfgPwd, "\$2a\$07\${$salt}\$");
		$init = init_sql($cfgInstId, $adminww);
		foreach ($init as $l) {
			$pdo->exec($l);
			$e = $pdo->errorInfo();
			if ($e[2]) {
				throw new \Exception($e[2]);
			}
		}

		$this->copyFile(
			"$dataDir/sample/index.php", "$cfgPth/index.php", $warn);

		$this->createDir("$cfgPth/templates", $warn);
		$this->copyFile(
			"$dataDir/sample/home.php", "$cfgPth/templates/home.php", $warn);

		$this->createDir("$cfgPth/upload", $warn);

	}

	private function createDir($name, &$warn) {
		if (!file_exists($name)) {
			$test = mkdir($name);
			if (!$test) {
				$warn[] = "Cannot create '$name'";
			} else {
				$test = chmod($name, 0775);
				if (!$test) {
					$warn[] = "Cannot chmod '$name'";
				}
			}
		}
	}

	private function copyFile($from, $to, &$warn) {
		if (!file_exists($to)) {
			$test = copy($from, $to);
			if (!$test) {
				$warn[] = "Cannot create '$to'";
			} else {
				$test = chmod($to, 0664);
				if (!$test) {
					$warn[] = "Cannot chmod '$to'";
				}
			}
		}
	}

}

?>