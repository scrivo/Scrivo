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
 * $Id: SelectDatabase.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Layout\SelectDatabase
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\I18n;
use \Scrivo\Action;
use \Scrivo\String;
use \Scrivo\Request;
use \ScrivoUi\DbConsole\Lib\DdlUtil;

/**
 * The SelectDatabase action verifies the database parameters given.
 */
class SelectDatabase extends Action {

	/**
	 * In this action the given database parameters are checked and it is
	 * verified that the database is an actual Scrivo database.
	 */
	function doAction() {

		try {

			if (isset($this->session->formData)) {
				$fd = unserialize($this->session->formData);
			} else {
				$fd = String::create(array(
					"db_host" => Request::post("db_host", Request::TYPE_STRING),
					"db_name" => Request::post("db_name", Request::TYPE_STRING),
					"db_user" => Request::post("db_user", Request::TYPE_STRING),
					"db_pwd" => Request::post("db_pwd", Request::TYPE_STRING),
				));
			}

			$pdo = DdlUtil::getConnection(
				$fd["db_host"], $fd["db_name"], $fd["db_user"], $fd["db_pwd"]);

			$dat = $pdo->query("SELECT * FROM instance");
			if (!$dat) {
				throw new \Exception(
					"No instance table found: is this a Scrivo database?");
			}

			$this->setResult(self::SUCCESS, null, $fd);

		} catch (\Exception $e) {

			$this->setResult(self::FAIL, $e, $fd);

		}

	}
}

?>
