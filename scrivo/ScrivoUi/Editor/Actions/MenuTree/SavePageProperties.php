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
 * $Id: SavePageProperties.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\MenuTree;

use \Scrivo\Action;
use \Scrivo\Page;
use \Scrivo\Request;

/**
 * The SavePageProperties class implements an action for saving the properties
 * of an existing page or to craete a new page.
 */
class SavePageProperties extends Action {

	/**
	 * In this action the page with the given id is retrieved or if no id was
	 * given a new page is created. Then the properties of the page are set
	 * from posted data and the page is saved.
	 */
	function doAction() {

		$pageId = Request::post("pageId", Request::TYPE_INTEGER, 0);
		$pagePid = Request::post("pagePid", Request::TYPE_INTEGER, 0);

		if ($pageId) {

			$page = Page::fetch($this->context, $pageId);

			$page->dateOnline =
				Request::post("onlineOn", Request::TYPE_DATE_TIME);
			$page->dateOffline =
				Request::post("offlineOn", Request::TYPE_DATE_TIME);
			$page->title =
				Request::post("title", Request::TYPE_STRING);
			$page->type =
				Request::post("pageType", Request::TYPE_INTEGER);

			$page->update();

			$page->move(Request::post("position", Request::TYPE_INTEGER));

		} else if ($pagePid) {

			$page = new Page($this->context);

			$page->parentId = $pagePid;

			$page->dateOnline =
				Request::post("onlineOn", Request::TYPE_DATE_TIME);
			$page->dateOffline =
				Request::post("offlineOn", Request::TYPE_DATE_TIME);
			$page->title =
				Request::post("title", Request::TYPE_STRING);
			$page->type =
				Request::post("pageType", Request::TYPE_INTEGER);
			$page->definitionId =
				Request::post("pageDefinitionId", Request::TYPE_INTEGER);

			$page->insert();

			$page->move(Request::post("position", Request::TYPE_INTEGER));

		} else {

			throw new \Scrivo\SystemException("Missing parameters");

		}

		$this->setResult(self::SUCCESS);

	}
}

?>