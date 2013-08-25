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
 * $Id: ListDefinitionSyncForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \ScrivoUi\Config\Actions\Layout\ListDefinitionSyncForm
 * action class.
 */

namespace ScrivoUi\Config\Actions\Layout;

use \Scrivo\LayoutAction;
use \Scrivo\I18n;
use \Scrivo\Request;
use \Scrivo\String;
use \ScrivoUi\Config\Lib\SyncUtilListDefinition;

/**
 * The ListDefinitionSyncForm layout action class sets up the layout for a
 * form to synchronize a application/list defintion with a remote one.
 */
class ListDefinitionSyncForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->ui_lang);

		$title = $i18n["List definition synchronization"];

		$remoteSite = Request::get("site", Request::TYPE_STRING);

		$newcfg = unserialize(file_get_contents(
			"{$remoteSite}/scrivo/config/dump_list_config.php"));
			$curcfg = SyncUtilListDefinition::dumpConfig($this->context);

		SyncUtilListDefinition::recKeySort($newcfg);
		SyncUtilListDefinition::recKeySort($curcfg);

		$item_labels = array(
			"APPLICATION_DEFINITION_ID" => "",
			"PAGE_DEFINITION_ID" => "",
			"title" => $i18n["Name"],
			"description" => $i18n["Description"],
			"action" => $i18n["url"],
			"type" => $i18n["Type"],
			"TYPES" => "",
		);
		$record_labels = array(
			"LIST_ITEM_DEFINITION_ID" => "",
			"sequence_no" => $i18n["Sequence no"],
			"APPLICATION_DEFINITION_ID" => "",
			"PAGE_DEFINITION_ID" => $i18n["Page definition linked page"],
			"PARENT_TYPE_IDS" => $i18n["Sub-type of:"],
			"title" => $i18n["List item definition name"],
			"icon" => $i18n["Icon"],
			"php_key" => $i18n["PHP selector"],
			"title_width" => $i18n["Width titlefield"],
			"title_label" => $i18n["Alternative title lable"],
			"LIST_ITEM_PARENT_TYPE_IDS" => $i18n["Sub-type of"],
		);
		$field_labels = array(
			"LIST_ITEM_PORPERTY_ID" => "",
			"APPLICATION_DEFINITION_ID" => "",
			"LIST_ITEM_DEFINITION_ID" => $i18n["List item definition id"],
			"sequence_no" => $i18n["Sequence no"],
			"type" => $i18n["Type"],
			"type_data" => $i18n["Type data"],
			"php_key" => $i18n["PHP selector"],
			"title" => $i18n["Label"],
			"in_list" => $i18n["Display in list"],
		);

		$oldrecs =
			SyncUtilListDefinition::extractField($curcfg, "TYPES", "title");
		$newrecs =
			SyncUtilListDefinition::extractField($newcfg, "TYPES", "title");
		$oldfields =
			SyncUtilListDefinition::extractField($curcfg, "FIELDS", "title");
		$newfields =
			SyncUtilListDefinition::extractField($newcfg, "FIELDS", "title");

		$difft = SyncUtilListDefinition::keyDiff($newcfg, $curcfg);

		$tr = "../ScrivoUi/Config/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/list_definition_sync_form.tpl.php";
		$this->useLayout("{$tr}/master.tpl.php");

		$this->setResult(self::SUCCESS);
	}

	/**
	 * Format and print HTML table row data.
	 *
	 * @param string $rw HTML code for a table row.
	 */
	private function pr($rw) {
		foreach (String::create($rw)->split(new String("<t")) as $c) {
			if ($c->length) {
				echo str_repeat("\t", "r" == $c[0] ? 6 : 7)."<t".$c."\n";
			}
		}
	}

	/**
	 * Print a row with just one cell and a check box.
	 *
	 * @param string $title Data for the first cell in the row.
	 * @param string $data1 Data for the seconde cell in the row.
	 * @param string $data2 Data for the third cell in the row.
	 * @param string $checkname The name attribute of the check box.
	 * @param string $cnt A count for the even/odd zebra.
	 * @param string $indent Indent width fot the first cell.
	 * @return string The HTML code for the row.
	 */
	private function printRow($title, $checkname, $cnt=0) {

		$this->pr("<tr".(++$cnt%2==1?" class=\"row-color\"":"").
				"><td>{$title}</td>");
		$this->pr("<td><input type=\"checkbox\" name=\"{$checkname}\">".
				"</td></tr>");
	}

	/**
	 * Print a row with three cells and a check box.
	 *
	 * @param string $title Data for the first cell in the row.
	 * @param string $data1 Data for the seconde cell in the row.
	 * @param string $data2 Data for the third cell in the row.
	 * @param string $checkname The name attribute of the check box.
	 * @param string $cnt A count for the even/odd zebra.
	 * @param string $indent Indent width fot the first cell.
	 * @return string The HTML code for the row.
	 */
	private function printModRow(
			$title, $data1, $data2, $checkname, $cnt=0, $indent=0) {

		$this->pr("<tr".(++$cnt%2==1?" class=\"row-color\"":"").
				"><td>".str_repeat("&nbsp", $indent)."{$title}</td>");
		$this->pr("<td>$data1</td><td>$data2</td>");
		$this->pr("<td><input type=\"checkbox\" name=\"{$checkname}\">".
				"</td></tr>");
	}


}

?>