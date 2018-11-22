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
 * $Id: InsertListItemDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\InsertListItemDefinition action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;
use \Scrivo\ListItemDefinition;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The InsertListItemDefinition class implements the action for creating a new
 * list item definition.
 */
class InsertListItemDefinition extends Action {

	/**
	 * In this action a new list item definition is created and populated
	 * from post data.
	 */
	function doAction() {

		try {

			$i18n = new I18n($this->context->config->UI_LANG);

			$appDefId = intval(Request::post(
				"application_definition_id", Request::TYPE_INTEGER, 0));

			// Create a new lite item definition ...
			$liDef = new ListItemDefinition($this->context);

			// ... set the members ...
			$liDef->applicationDefinitionId = $appDefId;
			$liDef->title = Request::post(
				"title", Request::TYPE_STRING);
			$liDef->phpSelector = Request::post(
				"php_selector", Request::TYPE_STRING, new Str(""));
			$liDef->titleWidth = Request::post(
				"title_width", Request::TYPE_INTEGER, "");
			$liDef->titleLabel = Request::post(
				"title_label", Request::TYPE_STRING, new Str(""));
			$liDef->icon = Request::post(
				"icon", Request::TYPE_STRING, new Str(""));
			$liDef->pageDefinitionId = Request::post(
				"page_definition_id", Request::TYPE_INTEGER, 0);

			// ... and insert it.
			$liDef->insert();

			$liDef->updateParentListItemDefinitionIds(Request::post(
				"list_item_definition_ids", Request::TYPE_INTEGER, array()));

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(
				array("application_definition_id" => $appDefId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e->getMessag(), $liDef);

		}
	}
}

?>