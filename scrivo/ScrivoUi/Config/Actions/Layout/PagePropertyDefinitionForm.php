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
 * $Id: PagePropertyDefinitionForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * \ScrivoUi\Config\Actions\Layout\PagePropertyDefinitionForm action class.
 */

namespace ScrivoUi\Config\Actions\Layout;

use \Scrivo\LayoutAction;
use \Scrivo\I18n;
use \Scrivo\Request;
use \Scrivo\PageDefinitionTab;
use \Scrivo\PagePropertyDefinition;

/**
 * The PagePropertyDefinitionForm layout action class sets up the layout for
 * the page property definition form.
 */
class PagePropertyDefinitionForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->ui_lang);

		if (isset($this->session->errorCode)) {
			$property = unserialize($this->session->formData);
			$pageDefinitionId = $property->pageDefinitionId;
		} else {
			$propertyId = Request::get(
					"page_property_definition_id", Request::TYPE_INTEGER, 0);
			if ($propertyId) {
				$property =
					PagePropertyDefinition::fetch($this->context, $propertyId);
				$pageDefinitionId = $property->pageDefinitionId;
			} else {
				$property = new PagePropertyDefinition($this->context);
				$pageDefinitionId = Request::get(
					"page_definition_id", Request::TYPE_INTEGER, 0);
			}
		}

		$title = $property->id
			? $i18n["Edit template property"]
			: $i18n["Create new template property"];

		// Create an option list for the different property types
		$tmp = PageDefinitionTab::select($this->context, $pageDefinitionId);
		$tabs = array(0 => $i18n["[Property tab]"]);
		foreach ($tmp as $tab) {
			if (PageDefinitionTab::TYPE_PROPERTY_TAB == $tab->type) {
				$tabs[$tab->id] = $tab->title;
			}
		}

		$tr = "../ScrivoUi/Config/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/page_property_definition_form.tpl.php";
		$this->useLayout("{$tr}/master.tpl.php");

		$this->setResult(self::SUCCESS);
	}
}

?>