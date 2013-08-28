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
 * $Id: PageDefinitionSyncForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \ScrivoUi\Config\Actions\Layout\PageDefinitionSyncForm
 * action class.
 */

namespace ScrivoUi\Config\Actions\Layout;

use \Scrivo\LayoutAction;
use \Scrivo\I18n;
use \Scrivo\Request;
use \Scrivo\Page;
use \Scrivo\String;
use \ScrivoUi\Config\Lib\SyncUtilPageDefinition;

/**
 * The PageDefinitionSyncForm layout action class sets up the layout for a
 * form to synchronize a page defintion with a remote one.
 */
class PageDefinitionSyncForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$title = $i18n["Page definition synchronization"];

		$remoteSite = Request::get("site", Request::TYPE_STRING);

		$newcfg = unserialize(file_get_contents(
			"{$remoteSite}/scrivo/config/dump_page_definition_config.php"));
		$curcfg = SyncUtilPageDefinition::dumpConfig($this->context);

		SyncUtilPageDefinition::recKeySort($newcfg);
		SyncUtilPageDefinition::recKeySort($curcfg);

		// remove deleted or added page definitions out of the
		// "canbechildof" list
		$common = array_flip(
			array_intersect(array_keys($newcfg), array_keys($curcfg)));
		foreach ($newcfg as $k => $v) {
			$newcfg[$k]["CAN_BE_CHILD_OF"] =
			array_intersect_key($v["CAN_BE_CHILD_OF"], $common);
		}
		foreach ($curcfg as $k => $v) {
			$curcfg[$k]["CAN_BE_CHILD_OF"] =
			array_intersect_key($v["CAN_BE_CHILD_OF"], $common);
		}

		$tab_labels = array(
			"title" => $i18n["Page defintion name"],
			"description" => $i18n["Description"],
			"action" => $i18n["PHP template file"],
			"config_only" => $i18n["Configuration only"],
			"type_set" => $i18n["Page types"],
			"default_tab_id" => $i18n["Default tab"],
			"application_definition_id" => $i18n["ApplicationDefinition"],
			"sequence_no" => $i18n["Sequence number"],
			"stylesheet" => $i18n["Local stylesheet"],
			"title" => $i18n["Tab name"],
			"css_selector" => $i18n["CSS selector"],
			"css_id" => $i18n["CSS id"],
			"php_key" => $i18n["PHP selector"],
			"page_css" => $i18n["External stylesheet"],
			"CAN_BE_CHILD_OF" => $i18n["Child of count"],
			"" => "",
		);

		$prop_labels = array(
			"sequence_no" => $i18n["Sequence number"],
			"type" => $i18n["Type"],
			"type_data" => $i18n["Type data"],
			"php_key" => $i18n["PHP selector"],
			"title" => $i18n["Property name"],
			"in_list" => $i18n["Configuration only"],
			"page_definition_tab_id" => $i18n["Tab"],
			"" => "",
		);

		$templ_types = array(
			Page::TYPE_NAVIGATION_ITEM => $i18n["Navigation element"],
			Page::TYPE_NAVIGABLE_PAGE => $i18n["Navigable page"],
			Page::TYPE_NON_NAVIGABLE_PAGE => $i18n["Non-navigable page"],
			Page::TYPE_SUB_FOLDER => $i18n["Subfolder"],
			Page::TYPE_APPLICATION => $i18n["ApplicationDefinition"],
		);

		$oldtabs = SyncUtilPageDefinition::extractField(
			$curcfg, "ELEMENTS", "title");
		$oldprops = SyncUtilPageDefinition::extractField(
			$curcfg, "PROPERTIES", "title");
		$newtabs = SyncUtilPageDefinition::extractField(
			$newcfg, "ELEMENTS", "title");
		$newprops = SyncUtilPageDefinition::extractField(
			$newcfg, "PROPERTIES", "title");

		$oldtabs[0] = $i18n["Property tab"];
		$newtabs[0] = $i18n["Property tab"];

		$difft = SyncUtilPageDefinition::keyDiff($newcfg, $curcfg);

		$tr = "../ScrivoUi/Config/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/page_definition_sync_form.tpl.php";
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