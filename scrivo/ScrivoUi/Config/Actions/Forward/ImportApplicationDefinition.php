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
 * $Id: ImportApplicationDefinition.php 850 2013-08-20 23:16:37Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\ImportApplicationDefinition action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationDefinition;
use \Scrivo\ApplicationException;
use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\Str;

/**
 * The ImportApplicationDefinition class implements the action for importing a
 * previously exported application definition.
 */
class ImportApplicationDefinition extends Action {

	function doAction() {

		try {

			// Try to open and unserialize the import file.
			if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
				throw new ApplicationException(
					$i18n["No file in upload"]);
			}

			$exp = file_get_contents($_FILES['userfile']['tmp_name']);
			$data = @unserialize($exp);

			if (!$data || !isset($data["ApplicationDefinition"])) {
				throw new ApplicationException(
					$i18n["Invalid data in upload"]);
			}

			// Map to map old ids (from the import) to the new ones.
			$idmap = array();

			// Create a new application defintion using the import data.
			$appDef = new ApplicationDefinition($this->context);

			$tmp = $data["ApplicationDefinition"];
			$appDef->title = new Str($tmp["title"]);
			$appDef->description = new Str($tmp["description"]);
			$appDef->location = new Str($tmp["location"]);
			$appDef->type = $tmp["type"];

			$appDef->insert();

			// Create the new list item definitions from the import data.
			foreach ($data["ListItemDefinitions"] as $liDef) {

				$lid = new ListItemDefinition($this->context);

				$lid->applicationDefinitionId = $appDef->id;

				$lid->icon = new Str($liDef["icon"]);
				$lid->pageDefinitionId = $liDef["pageDefinitionId"];
				$lid->phpSelector = new Str($liDef["phpSelector"]);
				$lid->title = new Str($liDef["title"]);
				$lid->titleLabel = new Str($liDef["titleLabel"]);
				$lid->titleWidth = $liDef["titleWidth"];

				$lid->insert();

				// Map old ids to the new ids.
				$idmap[intval($liDef["id"])] = $lid;
			}

			// Now all ids are mapped set the parent ids (if any).
			foreach ($idmap as $oldId => $lid) {
				$pids = array();
				foreach($liDef["parentListItemDefinitionIds"] as $oId) {
					$pids[] = $idmap[$oId["id"]]->id;
				}
				$lid->updateParentListItemDefinitionIds($pids);
			}

			// Create the new list item property definitions from the import
			// data.
			foreach ($data["ListItemPropertyDefinitions"] as $tmp) {
				foreach ($tmp as $liPropDef) {

					$lipd = new ListItemPropertyDefinition($this->context);

					$lipd->applicationDefinitionId = $appDef->id;

					$lipd->listItemDefinitionId =
						$idmap[intval($liPropDef["listItemDefinitionId"])]->id;
					$lipd->inList = $liPropDef["inList"];
					$lipd->phpSelector =
						new Str($liPropDef["phpSelector"]);
					$lipd->title = new Str($liPropDef["title"]);
					$lipd->type = $liPropDef["type"];
					$lipd->typeData = (object)$liPropDef["typeData"];

					$lipd->insert();
				}
			}

			// Set action result.
			$this->setParameters(
				array("application_definition_id" => $appDef->id));
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e);

		}
	}
}

?>