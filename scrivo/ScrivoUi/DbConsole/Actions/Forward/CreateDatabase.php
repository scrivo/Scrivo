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
 * $Id: CreateDatabase.php 861 2013-08-24 14:14:00Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Forward\CreateDatabase
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\Request;
use \ScrivoUi\DbConsole\Lib\DdlUtil;

/**
 * The CreateDatabase class implements an action for creating and initializing
 * a Scrivo database.
 */
class CreateDatabase extends Action {

	/**
	 * In this action a Scrivo database is created and the Scrivo database
	 * installation script is run.
	 */
	function doAction() {

		try {

			$rootUser = Request::post("root_user", Request::TYPE_STRING);
			$rootPwd = Request::post("root_pwd", Request::TYPE_STRING);
			$dbHost = Request::post("db_host", Request::TYPE_STRING);
			$dbName = Request::post("db_name", Request::TYPE_STRING);
			$dbUser = Request::post("db_user", Request::TYPE_STRING);
			$dbPwd = Request::post("db_pwd", Request::TYPE_STRING);

			DdlUtil::createDatabase(
				$rootUser, $rootPwd, $dbHost, $dbName, $dbUser, $dbPwd);

			$dbm = DdlUtil::prepareDbModel(
				file_get_contents("../ScrivoUi/DbConsole/Data/scrivo.sql"));

			$pdo = DdlUtil::getConnection($dbHost, $dbName, $dbUser, $dbPwd);
			foreach ($dbm->commands as $sql) {
				if (trim($sql)) {
					$pdo->exec($sql);
				}
			}

			$fd = array("root_user" => $rootUser, "root_pwd" => $rootPwd,
				"db_host" => $dbHost, "db_name" => $dbName,
				"db_user" => $dbUser, "db_pwd" => $dbPwd);

			$this->setResult(self::SUCCESS, null, $fd);

		} catch (\Exception $e) {

			$this->setResult(self::FAIL, $e, $fd);

		}
	}
}

?>
