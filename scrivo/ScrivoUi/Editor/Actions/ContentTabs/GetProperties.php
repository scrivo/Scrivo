<?php

/* Copyright (c) 2011, Geert Bergman (geert@scrivo.nl)
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
 * $Id: GetProperties.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\ContentTabs;

use \Scrivo\String;
use \Scrivo\Action;
use \Scrivo\Page;
use \Scrivo\Request;

/**
 * The PagePath class implements the action of retrieving the path of
 * a page.
 */
class GetProperties extends Action {

	/**
	 * In this action the page with the given id is retrieved and the
	 * page ids of its path are returned.
	 */
	function doAction() {

		$pageDefinitionTabId =
			Request::get("pageDefinitionTabId", Request::TYPE_INTEGER);
		$page = Page::fetch(
			$this->context, Request::get("pageId", Request::TYPE_INTEGER));

		// Find the properties that matches this tab.
		$prps = array();
		foreach ($page->properties as $prp) {
			if ($prp->definition->pageDefinitionTabId == $pageDefinitionTabId) {
				$prps[] = $prp;
			}
		}

		$res = array();
		foreach ($prps as $prp) {

			// convert type data to plain strings and camelcase.
			$typeData = array();
			foreach ($prp->definition->typeData as $k=>$v) {
				$k = preg_replace('/_([a-z])/e', "strtoupper('\\1')",
						trim(strtolower($k)));
				$typeData[$k] = ($v instanceof String)
					? (string)$v->trim() : $v;
			}

			$def = "";
			if ($prp->type == "datetime") {
				$fmt = "Y-m-d H:i:s";
				if (!isset($typeData["defaultValue"])) {
					$dt = new \DateTime();
					$typeData["defaultValue"] = $dt->format($fmt);
				} else if ($typeData["defaultValue"]) {
					if ($typeData["defaultValue"] != "NULL") {
						$dt = new \DateTime($typeData["defaultValue"]);
						$typeData["defaultValue"] = $dt->format($fmt);
					}
				}
			}

			if ($prp->type == "select") {
				if (@$typeData["type"] == strtolower("multiple")) {
					$r = @unserialize((string)$prp->data);
					if ($r) {
						(string)$prp->data = $r;
					}
				}
			}

			$res[] = array(
				"phpSelector" => (string)$prp->definition->phpSelector,
				"type" => $prp->definition->type,
				"typeData" => $typeData,
				"value" => (string)$prp->data,
				"label" => (string)$prp->definition->title
			);

		}

		$this->setResult(self::SUCCESS, array("properties" => $res));
	}
}

?>