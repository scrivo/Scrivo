<?php

/* Copyright (c) 2011, Geert Bergman (geert@scrivo.nl)
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
 * $Id: GetPageContent.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\ContentTabs;

use \Scrivo\String;
use \Scrivo\Action;
use \Scrivo\Page;
use \Scrivo\Request;
use \Scrivo\SystemException;

/**
 * The PagePath class implements the action of retrieving the path of
 * a page.
 */
class GetPageContent extends Action {

	/**
	 * In this action the page with the given id is retrieved and the
	 * page ids of its path are returned.
	 */
	function doAction() {

		$pageDefinitionTabId =
			Request::get("pageDefinitionTabId", Request::TYPE_INTEGER);
		$page = Page::fetch(
			$this->context, Request::get("pageId", Request::TYPE_INTEGER));

		// Find the HTML property that matches this tab.
		$htmlPrp = null;
		foreach ($page->properties as $prp) {
			if ($prp->definition->pageDefinitionTabId == $pageDefinitionTabId) {
				$htmlPrp = $prp;
				break;
			}
		}

		if (!$htmlPrp) {
			throw new SystemException("HTML content tab without property");
		}


		$td = $htmlPrp->definition->typeData;

		$cssClass = (string)$td->css_selector;

		$cssIds = array();
		foreach ($td->css_id->split(new String(",")) as $id) {
			if (!$id->trim()->equals(new String(""))) {
				$cssIds[] = (string)$id->trim();
			}
		}

		if (count($cssIds) == 0) {
			$cssIds[0] = "my_own_id";
		}

		$stylesheets = array();
		if (!$td->page_css->equals(new String(""))) {
			$stylesheets = array(
				"{$this->context->config->WWW_ROOT}/{$td->page_css}?page_id=".
				$page->id);
		}

		// Set default content
		// TODO spellcheck
		$html = $htmlPrp->rawHtml;
		if ($html->equals(new String(""))) {
			$html = $td->INITIAL_CONTENT->replace(
				new String("<head>"), new String(
				"<head><base href=\"{$this->context->config->WWW_ROOT}\">")
			);
		}

		$res = array(
			"html" => (string)$html,
			"cssId" => $cssIds,
			"cssClass" => $cssClass,
			"templateCSS" => (string)$td->stylesheet,
			"stylesheets" => $stylesheets

		);

		$this->setResult(self::SUCCESS, $res);
	}
}

?>