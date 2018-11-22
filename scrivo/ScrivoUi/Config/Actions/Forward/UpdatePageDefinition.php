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
 * $Id: UpdatePageDefinition.php 849 2013-08-20 19:19:50Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\UpdatePageDefinition
 * action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\PageDefinition;
use \Scrivo\PageDefinitionHints;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The UpdatePageDefinition class implements the action for updating a page
 * definitition.
 */
class UpdatePageDefinition extends Action {

	/**
	 * In this action the page definitition with the given id is loaded, its
	 * members set to new values from posted data and saved. Then the
	 * PageDefinititionHints list is updated with new values from posted data.
	 */
	function doAction() {

		try {
			// Load the page definitition ...
			$pageDefinition = PageDefinition::fetch($this->context,
				Request::post("page_definition_id", Request::TYPE_INTEGER));

			// ... set the members ...
			$pageDefinition->title = Request::post(
				"label", Request::TYPE_STRING, new Str(""));
			$pageDefinition->description = Request::post(
				"description", Request::TYPE_STRING, new Str(""));
			$pageDefinition->action = Request::post(
				"action", Request::TYPE_STRING, new Str(""));
			$pageDefinition->configOnly = Request::post(
				"admin_only", Request::TYPE_BOOLEAN);
			$pageDefinition->typeSet = Request::post(
				"type_set", Request::TYPE_INTEGER);
			$pageDefinition->defaultTabId = Request::post(
				"default_tab_id", Request::TYPE_INTEGER, -1);

			// ... and update the page definitition.
			$res = $pageDefinition->update();

			// Load the PageDefinititionHints::PARENT_PAGE_DEFINITION_COUNT
			// list ...
			$hnts = new PageDefinitionHints($this->context,
				$pageDefinition->id,
				PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);

			// ... get the new values ...
			foreach ($hnts as $k=>$v) {
				$dat = $_POST["hint{$k}"];
				$hnts[$k]->maxNoOfChildren =
					is_numeric($dat) ? intval($dat) : NULL;
			}
			// ... and update the list.
			$hnts->update();

			// Set action result.
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $pageDefinition);
		}
	}

}

?>