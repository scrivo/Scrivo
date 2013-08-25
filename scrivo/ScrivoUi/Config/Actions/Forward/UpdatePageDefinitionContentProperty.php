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
 * $Id: UpdatePageDefinitionContentProperty.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\UpdatePageDefinitionContentProperty action
 * class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\PagePropertyDefinition;
use \Scrivo\Request;
use \Scrivo\String;

/**
 * The UpdatePageDefinitionApplicationProperty class implements the action
 * for updating an HTML content property definition.
 */
class UpdatePageDefinitionContentProperty extends Action {

	/**
	 * In this action the HTML content property definition with the given id
	 * is retrieved, its new values set from post and saved.
	 */
	function doAction() {

		try {

			// Load the page property definition...
			$prop = PagePropertyDefinition::fetch($this->context, Request::post(
				"page_property_definition_id", Request::TYPE_INTEGER));

			// ... set the members ...
			$prop->phpSelector = Request::post(
				"php_selector", Request::TYPE_STRING, new String(""));
			$prop->type = PagePropertyDefinition::TYPE_HTML_TEXT_TAB;
			$prop->typeData->css_selector = Request::post(
				"css_selector", Request::TYPE_STRING, new String(""));
			$prop->typeData->page_css = Request::post(
				"document_css", Request::TYPE_STRING, new String(""));
			$prop->typeData->stylesheet = Request::post(
				"stylesheet", Request::TYPE_STRING, new String(""));

			// ... and update it.
			$prop->update();

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(
				array("page_definition_id" => $prop->pageDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $prop);

		}
	}
}

?>