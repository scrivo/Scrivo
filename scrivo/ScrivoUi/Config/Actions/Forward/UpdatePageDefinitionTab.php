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
 * $Id: UpdatePageDefinitionTab.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\UpdatePageDefinitionTab
 * action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;
use \Scrivo\PageDefinitionTab;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The UpdatePageDefinitionTab class implements the action for updating a
 * page definition tab.
 */
class UpdatePageDefinitionTab extends Action {

	/**
	 * In this action the page definiton tab with the given id is loaded,
	 * its members set from post data and stored back into the database.
	 */
	function doAction() {

		try {

			$i18n = new I18n($this->context->config->UI_LANG);

			// Load a page definition tab ...
			$tab = PageDefinitionTab::fetch($this->context,
				Request::post("page_definition_tab_id", Request::TYPE_INTEGER));

			// ... set the members ...
			$tab->title =
				Request::post("tab_name", Request::TYPE_STRING, new Str(""));

			// ... and update it.
			$tab->update();

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(
				array("page_definition_id" => $tab->pageDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $tab);

		}
	}
}
?>