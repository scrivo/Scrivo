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
 * $Id: CopyPageDefinitionTab.php 849 2013-08-20 19:19:50Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\CopyPageDefinitionTab action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\PageDefinition;
use \Scrivo\PageDefinitionTab;
use \Scrivo\PagePropertyDefinition;
use \Scrivo\Request;

/**
 * The CopyPageDefinitionTab class implements the action for copying a page
 * definiton tab to other page definitions.
 *
 * TODO: currently ony property definitions of app and text tabs are
 * copied together with the tab, in other cases only the tab. Preferably
 * the whole tab including properties should be copied.
 */
class CopyPageDefinitionTab extends Action {

	function doAction() {
		try {

			$page_definition_ids = Request::post(
				"page_definition_id", Request::TYPE_INTEGER);

			$tab = PageDefinitionTab::fetch($this->context,
				Request::post("page_definition_tab_id", Request::TYPE_INTEGER));

			$pageDef = PageDefinition::fetch(
				$this->context, $tab->pageDefinitionId);

			// Find out if is an HTML or application tab.
			$prop = null;
			foreach($pageDef->properties as $p) {
				if ((PagePropertyDefinition::TYPE_HTML_TEXT_TAB
							== $p->type
						|| PagePropertyDefinition::TYPE_APPLICATION_TAB
							== $p->type)
						&& $p->pageDefinitionTabId == $tab->id) {
					$prop = $p;
					break;
				}
			}

			foreach ($page_definition_ids as $tid) {

				$newtab = new PageDefinitionTab($this->context);

				$newtab->pageDefinitionId = intval($tid);
				$newtab->title = $tab->title;

				if ($prop) {
					// HTML or application tab: set tab and property.
					$te = new PagePropertyDefinition($this->context);
					$te->pageDefinitionId = intval($tid);
					$te->title = $prop->title;
					$te->type = $prop->type;
					$te->typeData = $prop->typeData;
					$te->phpSelector = $prop->phpSelector;
					$te->insert();
				} else {
					// Just a tab.
					$newtab->insert();
				}

			}

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(
				array("page_definition_id" => $tab->pageDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e);
			$this->setParameters(
				array("page_definition_id" => $tab->pageDefinitionId));

		}
	}
}

?>