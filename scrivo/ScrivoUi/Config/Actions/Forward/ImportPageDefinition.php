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
 * $Id: ImportPageDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\ImportPageDefinition
 * action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\I18n;
use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\PageDefinition;
use \Scrivo\PageDefinitionHints;
use \Scrivo\PageDefinitionTab;
use \Scrivo\PagePropertyDefinition;
use \Scrivo\String;

/**
 * The ImportPageDefinition class implements the action for importing a
 * previously exported page definition.
 */
class ImportPageDefinition extends Action {

	function doAction() {

		try {

			$i18n = new I18n($this->context->config->ui_lang);

			// Try to open and unserialize the import file.
			if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
				throw new ApplicationException(
					$i18n["No file in upload"]);
			}

			$exp = file_get_contents($_FILES['userfile']['tmp_name']);
			$data = @unserialize($exp);

			if (!$data || !isset($data["defaultTabId"])) {
				throw new ApplicationException(
					$i18n["Invalid data in upload"]);
			}

			// Map to map old tab ids (from the import) to the new ones.
			$tabmap = array(0=>0); //< def property tab

			// Create a new template using the import data.
			$templ = new PageDefinition($this->context);

			$templ->title = new String($data["title"]);
			$templ->description = new String($data["description"]);
			$templ->action = new String($data["action"]);
			$templ->configOnly = $data["configOnly"];
			$templ->typeSet = $data["typeSet"];
			$templ->defaultTabId = $data["defaultTabId"];

			$templ->insert();

			// Create the new tabs from the import data.
			foreach ($data["tabs"] as $tabdata) {

				$tab = new PageDefinitionTab($this->context);

				$tab->pageDefinitionId = $templ->id;

				$tab->title = new String($tabdata["title"]);

				$tab->insert();

				// Map old tab ids to the new tab ids.
				$tabmap[$tabdata["id"]] = $tab->id;

				// If this was the default tab, update the tempate too.
				if ($templ->defaultTabId == $tabdata["id"]) {
					$templ->defaultTabId = $tab->id;
					$templ->update();
				}

			}

			// Create the new properties from the import data.
			foreach ($data["properties"] as $propdata) {

				$prop = new PagePropertyDefinition($this->context);

				$prop->pageDefinitionId = $templ->id;

				// Use the map to get the tab ids.
				$prop->pageDefinitionTabId =
					$tabmap[$propdata["pageDefinitionTabId"]];
				$prop->title = new String($propdata["title"]);
				$prop->phpSelector =
					new String($propdata["phpSelector"]);
				$prop->type = $propdata["type"];
				$prop->typeData = (object)$propdata["typeData"];

				$prop->insert();
			}

			// Load the TemplateHints::PARENT_TEMPLATE_COUNT list ...
			$hnts = new PageDefinitionHints($this->context, $templ->id,
				PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
			// ... zero all the counts ...
			foreach ($hnts as $k=>$v) {
				$hnts[$k]->maxNoOfChildren = 0;
			}

			// ... and update the list.
			$hnts->update();

			// Set action result.
			$this->setParameters(array("page_definition_id" => $templ->id));
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {
var_dump($e); die;

			$this->setResult(self::FAIL, $e);

		}
	}
}

?>