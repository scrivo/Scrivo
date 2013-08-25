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
 * $Id: GetTabs.php 866 2013-08-25 16:22:35Z geert $
 */

 /**
 * Implementation of the ScrivoUi\Editor\Actions\GetTabs action class.
 */

namespace ScrivoUi\Editor\Actions;

use \Scrivo\PagePropertyDefinition;
use \Scrivo\I18n;
use \Scrivo\Action;
use \Scrivo\Page;
use \Scrivo\Request;
use \Scrivo\PageDefinitionTab;

/**
 * The GetTabs class implements an action for retrieving the page tabs of
 * a page.
 */
class GetTabs extends Action {

	/**
	 * In this action the page with the given id is retrieved and its tabs
	 * are returned.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->ui_lang);

		$res = array("defaultTab" => 0, "tabs" => array());

		$page = Page::fetch(
			$this->context, Request::get("pageId", Request::TYPE_INTEGER,
			$this->context->config->ROOT_PAGE_ID));

		$res["tabs"][] = array (
			"title" => $i18n["Properties"],
			"pageDefinitionTabId" => 0,
			"tab" => "default"
		);

		if ($page->roles->canWrite($this->context->principal)) {

			$c = 1;
			foreach ($page->definition->tabs as $tab) {
				$tb = "content";
				if ($tab->type == PageDefinitionTab::TYPE_APPLICATION_TAB) {
					$tb = "application";
				} else if ($tab->type == PageDefinitionTab::TYPE_PROPERTY_TAB) {
					$tb = "properties";
				}

				$res["tabs"][] = array (
					"title" => (string)$tab->title,
					"pageDefinitionTabId" => $tab->id,
					"tab" => $tb
				);

				if ($tab->id == $page->definition->defaultTabId) {
					$res["defaultTab"] = $c;
				}
				$c++;
			}

		}

		$this->setResult(self::SUCCESS, $res);
	}
}

?>