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
 * $Id: ExportInstance.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Forward\ExportInstance
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\Action;
use \ScrivoUi\DbConsole\Lib\Util;

/**
 * The ExportInstance class implements an action for exporting the data
 * of a Scrivo instance from the database.
 */
class ExportInstance extends Action {

	/**
	 * This action exports all data rows from the database for a given
	 * instance.
	 */
	function doAction() {

		set_time_limit(300);

		$outFile = sys_get_temp_dir()."/".Util::cleanWwwRoot(
			$this->context->config->WWW_ROOT)."_dbc_data.sql.gz";

		$out = gzopen($outFile, "w9");
		if (!$out) {

			$i18n = new I18n($this->context->config->UI_LANG);
			$res = $i18n["Could not open export file!"];
			$this->setResult(self::FAIL, $res);

		} else {

			$sth = $this->context->connection->prepare("SHOW TABLES");

			$sth->execute();

			while ($row_data = $sth->fetch(\PDO::FETCH_ASSOC)) {

				list($k, $table) = each($row_data);

				Util::dumpTable($this->context->connection, $out, $table,
					$this->context->config->INSTANCE_ID,
					$this->context->config->WWW_ROOT,
					$this->context->config->UPLOAD_DIR);
			}

			gzclose($out);
		}

		$this->setResult(self::SUCCESS);
	}
}

?>