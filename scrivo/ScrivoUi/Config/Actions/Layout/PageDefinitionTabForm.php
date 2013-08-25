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
 * $Id: PageDefinitionTabForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \ScrivoUi\Config\Actions\Layout\PageDefinitionTabForm
 * action class.
 */

namespace ScrivoUi\Config\Actions\Layout;

use \Scrivo\LayoutAction;
use \Scrivo\Request;
use \Scrivo\PageDefinitionTab;
use \Scrivo\I18n;

/**
 * The PageDefinitionTabForm layout action class sets up the layout for a page
 * definition tab form.
 */
class PageDefinitionTabForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->ui_lang);

		$pageDefinitionId =
			Request::get("page_definition_id", Request::TYPE_INTEGER, 0);
		$tabId =
			Request::get("page_definition_tab_id", Request::TYPE_INTEGER, 0);

		$pageDefinitionTab = $tabId
			? PageDefinitionTab::fetch($this->context, $tabId)
			: new PageDefinitionTab($this->context);

		$title = $pageDefinitionTab->id
			? $i18n["Edit template tab"]
			: $i18n["New template property tab"];

		$tr = "../ScrivoUi/Config/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/page_definition_tab_form.tpl.php";
		$this->useLayout("{$tr}/master.tpl.php");

		$this->setResult(self::SUCCESS);

	}
}

?>