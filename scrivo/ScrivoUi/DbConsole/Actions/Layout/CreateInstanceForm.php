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
 * $Id: CreateInstanceForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Layout\CreateInstanceForm
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Layout;

use \Scrivo\I18n;
use \Scrivo\LayoutAction;
use \Scrivo\String;
use \Scrivo\Request;
use \ScrivoUi\DbConsole\Lib\DdlUtil;

/**
 * The CreateInstanceForm layout action class sets up the layout for
 * a form to enter Scrivo instance details.
 */
class CreateInstanceForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n(new String("en_US"));

		$title = $i18n["Instance details"];

		$fd = String::create(array(
			"db_host" => "", "db_name" => "",
			"db_user" => "", "db_pwd" => ""));
		if (isset($this->session->formData)) {
			$fd = unserialize($this->session->formData);
		}

		if (!array_key_exists("cfg_inst", $fd)) {
			$pth = $_SERVER["SCRIPT_FILENAME"];
			$pth = str_replace(array("/scrivo/dbc/index.php",
				"\\scrivo\dbc\\index.php"), "", $pth);

			$fd["cfg_inst"] = "";
			$fd["cfg_pth"] = $pth;
			$fd["cfg_upload"] = $pth."/upload";
			$fd["cfg_root"] = "http://";
			$fd["cfg_pwd"] = "";
		}

		$pdo = DdlUtil::getConnection(
			$fd["db_host"], $fd["db_name"], $fd["db_user"], $fd["db_pwd"]);

		$dat = $pdo->query("SELECT * FROM instance");
		if (!$dat) {
			throw new \Exception(
				"No instance table found: is this a Scrivo database?");
		} else {
			$instances = array();
			foreach($dat as $d) {
				$instances[$d["instance_id"]] = true;
			}
			$counter = 0;
			$opts = "";
			$first = true;
			for ($i = 1; $counter < 25; $i++) {
				if (!isset($instances[$i])) {
					if ($first) {
						$first = false;
						$opts .= "<option selected value='$i'>$i</option>";
					} else {
						$opts .= "<option value='$i'>$i</option>";
					}
					$counter++;
				}
			}
		}

		$tr = "../ScrivoUi/DbConsole/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/create_instance_form.tpl.php";
		$this->useLayout("{$tr}/master.tpl.php");

		$this->setResult(self::SUCCESS, null, $fd);
	}
}

?>