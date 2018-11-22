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
 * $Id: SystemCheck.php 860 2013-08-24 12:42:15Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Layout\SystemCheck action class.
 */

namespace ScrivoUi\DbConsole\Actions\Layout;

use \Scrivo\I18n;
use \Scrivo\LayoutAction;
use \Scrivo\Str;
use \ScrivoUi\DbConsole\Lib\SystemInfo;

/**
 * The SystemCheck layout action class sets up the layout a page that checks
 * for the system requirements to install Scrivo.
 */
class SystemCheck extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n(new Str("en_US"));

		$si = new SystemInfo();

		$ok =
			($si->phpVersion[0] >= 5 && $si->phpVersion[1] >= 3) &&
			$si->zlib &&
			$si->pdo &&
			$si->pdoMySql &&
			$si->homeWritable;

		if (!$ok) {
			$this->session->errorCode = $i18n[
				"Sorry, your system does not meet the requirements to use ".
				"this installer. Please check the requirements below!"];
		} else {
			unset($this->session->errorCode);
		}

		$title = $i18n["Scrivo installer system check"];

		include "../ScrivoUi/DbConsole/Templates/common.tpl.php";
		include "../ScrivoUi/DbConsole/Templates/system_check.tpl.php";
		$this->useLayout("../ScrivoUi/DbConsole/Templates/master.tpl.php");

		$this->setResult(self::SUCCESS);

	}
}

?>
