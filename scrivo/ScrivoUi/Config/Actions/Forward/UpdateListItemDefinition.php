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
 * $Id: UpdateListItemDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\UpdateListItemDefinition action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;
use \Scrivo\ListItemDefinition;
use \Scrivo\Request;
use \Scrivo\String;

/**
 * The UpdateListItemDefinition class implements the action for updating a
 * list item definition.
 */
class UpdateListItemDefinition extends Action {

	/**
	 * In this action an existing list item definition is loaded and its
	 * members set from post data and updated.
	 */
	function doAction() {

		try {

			$i18n = new I18n($this->context->config->ui_lang);

			$id = Request::post(
				"list_item_definition_id", Request::TYPE_INTEGER);

			// Get the list item definition ...
			$liDef = ListItemDefinition::fetch($this->context, $id);

			// ... set the members ...
			$liDef->title = Request::post(
				"title", Request::TYPE_STRING, new String(""));
			$liDef->phpSelector = Request::post(
				"php_selector", Request::TYPE_STRING, new String(""));
			$liDef->titleWidth = Request::post(
				"title_width", Request::TYPE_INTEGER, "");
			$liDef->titleLabel = Request::post(
				"title_label", Request::TYPE_STRING, new String(""));
			$liDef->icon = Request::post(
				"icon", Request::TYPE_STRING, new String(""));
			$liDef->pageDefinitionId = Request::post(
				"page_definition_id", Request::TYPE_INTEGER, 0);

			// ... and update it.
			$liDef->update();

			$liDef->updateParentListItemDefinitionIds(Request::post(
				"list_item_definition_ids", Request::TYPE_INTEGER, array()));

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(array("application_definition_id" =>
				$liDef->applicationDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e->getMessage(), $liDef);

		}
	}

}

?>