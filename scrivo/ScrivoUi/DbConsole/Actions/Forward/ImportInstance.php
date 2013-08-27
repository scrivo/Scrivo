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
 * $Id: ImportInstance.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Forward\ImportInstance
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\Request;
use \Scrivo\Action;
use \ScrivoUi\DbConsole\Lib\Util;

/**
 * The ImportInstance action class implements an action for importing Scrivo
 * content data into a Scrivo database.
 */
class ImportInstance extends Action {

	/**
	 * In this action the uploaded data is parsed and the references to
	 * absolute paths are replaced before the data is inserted into the
	 * database.
	 */
	function doAction() {

		try {

			set_time_limit(900);

			$data = null;

			if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
				$data = $_FILES['userfile']['tmp_name'];
			}

			if (!$data) {
				$userfile = Request::post("serverfile", Request::TYPE_INTEGER);
				if (!file_exists($userfile)) {
					throw new \Exception("No data file given");
				}
				$data = (string)$userfile;
			}

			$config = new \Scrivo\Config();

			$conn = new \Scrivo\PdoConnection($config);

			$handle = @gzopen($data, "r");

			if ($handle) {

				while (!feof($handle)) {

					$line = trim(gzgets($handle));

					$sql = Util::patchInsertStatement($line,
						$config->INSTANCE_ID,
						$config->WWW_ROOT,
						$config->UPLOAD_DIR);

					try {
						$conn->exec($sql);
					} catch (\PDOException $e) {
						// TODO report errors.
					}
				}
				gzclose($handle);
			}

			$mx_id = -1;
			$result1 = $conn->query("SHOW TABLES");
			while ($row_data = $result1->fetch(\PDO::FETCH_ASSOC)) {
				list($k, $table) = each($row_data);
				try {
					$rc = $conn->query(
						"SELECT MAX({$table}_id) MAXID FROM {$table}");
					$c = $rc->fetch(\PDO::FETCH_ASSOC);
					$mx = $c["MAXID"];
					if ($mx > $mx_id) {
						$mx_id = $mx;
					}
				} catch (\PDOException $e) {
					// Void
				}
			}
			$conn->exec("DELETE FROM seq");
			$conn->exec("INSERT INTO seq (seq) VALUES (".($mx_id + 1).")");

			$this->setResult(self::SUCCESS);

		} catch (\Exception $e) {

			$this->setResult(self::FAIL, $e);

		}

	}
}

?>