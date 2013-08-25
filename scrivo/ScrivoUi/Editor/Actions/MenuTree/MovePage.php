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
 * $Id: MovePage.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\MenuTree;

use \Scrivo\Action;
use \Scrivo\Page;
use \Scrivo\Request;
use \Scrivo\PageDefinition;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;

/**
 * The MovePage class implements an action for moving a page to another
 * parent page.
 */
class MovePage extends Action {

	/**
	 * In this action the page with the given id is retrieved and its
	 * position updated, but only after it is checked if the page is
	 * allowed under the new parent page.
	 */
	function doAction() {

		$page = Page::fetch(
			$this->context, Request::get("pageId", Request::TYPE_INTEGER));

		$newParentId = Request::get("parentId", Request::TYPE_INTEGER);

		$selectable =
			PageDefinition::selectSelectable($this->context, $newParentId);

		if (!isset($selectable[$page->definition->id])) {
			$i18n = new I18n($this->context->config->ui_lang);
			throw new ApplicationException(
				$i18n["This page is not allowed underneath the selected page"]);
		}

		$page->parentId = $newParentId;
		$page->update();

		$this->setResult(self::SUCCESS);

	}
}

?>