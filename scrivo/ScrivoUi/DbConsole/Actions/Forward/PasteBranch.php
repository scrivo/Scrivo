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
 * $Id: PasteBranch.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Forward\PasteBranch
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\Request;
use \Scrivo\Action;
use \ScrivoUi\DbConsole\Lib\Util;

/**
 * The PasteBranch action class implements an action for importing a content
 * branch into a Scrivo database. Note that the configuration of the instances
 * where you want to share data between must be in sync.
 */
class PasteBranch extends Action {

	private $DONE_KEYS = array();

	/**
	 * In this action the the uploaded file is parsed and the records in
	 * the file are given new ids. This data is then inserted into the
	 * database.
	 */
	function doAction() {

		set_time_limit(300);

		$data = null;

		if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			$data = $_FILES['userfile']['tmp_name'];
		}

		if (!$data) {

			$i18n = new I18n($this->context->config->UI_LANG);
			$res = $i18n["missing data file!"];
			$this->setResult(self::FAIL, $res);

		} else {

			$handle = @gzopen($data, "r");

			$first = true;
			if ($handle) {

				while (!feof($handle)) {

					$line = trim(gzgets($handle));

					if ($first) {
						$tmp = explode("KEY_".Util::GUID."_", $line);
						if (count($tmp) != 3) {
							$this->setResult(self::FAIL, "DB change?");
							return;
						}
						$tmp = $tmp[0]."KEY_".Util::GUID."_".$tmp[1].
							"SCRIVO_".Util::GUID."_".$tmp[2];
						$line = preg_replace(
							"|(SCRIVO_".Util::GUID."_)(\d*)|",
							"ROOT_".Util::GUID, $tmp);
					}

					$sql = $this->patchInsertStatment($line,
						$this->context->config->INSTANCE_ID,
						$this->context->config->WWW_ROOT,
						$this->context->config->UPLOAD_DIR);

					if ($first) {
						$first= false;
						$sql = str_replace("ROOT_".Util::GUID,
							Request::post("page_id", Request::TYPE_INTEGER),
							$sql);
					}
					$this->context->connection->exec($sql);

				}
				gzclose($handle);
			}
		}

		$this->setResult(self::SUCCESS);
	}


	private function newId($matches) {
		global $ids;
		$id = $matches[2];
		if ($id == 0)
			return 0;
		if (!isset($ids[$id])) {
			$ids[$id] = $this->context->connection->generateId();
		}
		return $ids[$id];
	}

	private function patchInsertStatment($statment, $inst_id, $base, $dir) {

		$statment = preg_replace_callback("|(KEY_".Util::GUID."_)(\d*)|",
			array($this, "newId"), $statment);

		return Util::patchInsertStatement($statment, $inst_id, $base, $dir);
	}

}
?>
