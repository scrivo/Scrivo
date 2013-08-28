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
 *    this list of conditions and the following disclaimer in the pageumentation
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
 * $Id: GetPageProperties.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\MenuTree;

use \Scrivo\Action;
use \Scrivo\I18n;
use \Scrivo\Page;
use \Scrivo\String;
use \Scrivo\Request;
use \Scrivo\PageDefinition;

/**
 * The GetPageProperties class implements an action for getting the properties
 * of a page.
 */
class GetPageProperties extends Action {

	/**
	 * In this action the page with the given id is retrieved and its
	 * properties returned in the action array as an array. If no id was
	 * given a new page will be constructed.
	 */
	function doAction() {

		$res = array();

		$i18n = new I18n($this->context->config->UI_LANG);

		// Create an arrary of page types where the editor can select from
		$res["pageTypes"] = array(
			Page::TYPE_NAVIGATION_ITEM => $i18n["Navigation item"],
			Page::TYPE_NAVIGABLE_PAGE => $i18n["Navigable page"],
			Page::TYPE_NON_NAVIGABLE_PAGE => $i18n["Non navigable page"]
		);

		// Get the page id of given, 0 othewise.
		$pageId = Request::get("pageId", Request::TYPE_INTEGER, 0);

		if ($pageId) {

			// Retrieve the page if an id was given.
			$page = Page::fetch($this->context, $pageId);

			$res["pageDefinition"] = (string)$page->definition->title;
			$res["types"] = $page->definition->typeSet;

			// This is a little patch: the editor is not given the option
			// to select Page::TYPE_APPLICATION. But if the fetched page
			// is if that type this should be the only selectable option.
			if ($page->type === Page::TYPE_APPLICATION) {
				$res["pageTypes"] = array(
					Page::TYPE_APPLICATION => $i18n["Application"]);
				$res["types"] = array(Page::TYPE_APPLICATION);
			}

		} else {

			// Create a new page if no id was given.
			$page = new Page($this->context);
			$page->title = new String($i18n["New Page"]);
			$page->parentId =
				Request::get("pagePid", Request::TYPE_INTEGER);

			// Get the the list of possible page definitions.
			$pDefs = array();
			foreach (PageDefinition::selectSelectable(
					$this->context, $page->parentId) as $pDef) {
				$pDefs[] = array(
					"types" => $pDef->typeSet,
					"value" => $pDef->id,
					"text" => (string)$pDef->title
				);
			}

			$res["pageDefinitions"] = $pDefs;
		}

		// Create an array for a select box containing the postion information
		// of the neigbouring pages.
		$selPos = 0;
		$positions = array();

		if ($page->parentId) {

			// If there a parent page there are (can) be neighbours.
			$i = 1;
			// An initial entry to allow the editor to move a page to
			// the top.
			$positions[] = array(
				"value" => 0,
				"text" => $i18n["above"]
			);
			$parent = Page::fetch($this->context, $page->parentId);
			foreach ($parent->children as $c) {
				if ($c->type !== Page::TYPE_SUB_FOLDER) {
					$positions[] = array(
						"value" => $i,
						"text" => (string)$c->title
					);
					if ($c->id == $page->id) {
						$selPos = $i;
					}
					$i++;
				}
			}

		} else {

			// The home has no parent page: set it as the only option.
			$positions[] = array(
				"value" => 1,
				"text" => (string)$page->title
			);

		}

		$res["positions"] = $positions;

		// Get page data.
		$res["page"] = array(
			"title" => (string)$page->title,
			"type" => $page->type,
			"online" => $page->dateOnline->format("Y-m-d h:i:s"),
			"offline" => $page->dateOffline ?
				$page->dateOffline->format("Y-m-d h:i:s") : "",
			"selPos" => $selPos
		);

		// And set the action result.
		$this->setResult(self::SUCCESS, $res);
	}

}

?>