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
 * $Id: Download.php 861 2013-08-24 14:14:00Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Download\Download
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Download;

use \Scrivo\Action;
use \Scrivo\Downloadable;
use \Scrivo\Str;
use \ScrivoUi\DbConsole\Lib\Util;

/**
 * The Download action class implements an action for downloading data.
 */
class Download extends Action {

	/**
	 * In this action the proper file is retrieved from the system temporary
	 * directory and send to the client for download.
	 */
	function doAction() {

		try {

			$file = "";

			switch ($this->parameters["a"]) {

				case "export_instance_download":
					$file = new Str(Util::cleanWwwRoot(
						$this->context->config->WWW_ROOT)."_dbc_data.sql.gz");
					break;

				case "export_assets_download":
					$file = new Str(Util::cleanWwwRoot(
						$this->context->config->WWW_ROOT)."_dbc_assets.tgz");
					break;

				case "copy_branch_download":
					$file = new Str(Util::cleanWwwRoot(
						$this->context->config->WWW_ROOT)."_dbc_branch.sql.gz");
					break;

			}

			$this->file = new Downloadable(
				$this->context, $file, Downloadable::TYPE_FILE,
				new Str(sys_get_temp_dir()."/$file"));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e);

		}
	}
}

?>
