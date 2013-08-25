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
 * $Id: GetMenu.php 841 2013-08-19 22:19:47Z geert $
 */

namespace ScrivoUi\Editor\Actions\MenuTree;

use \Scrivo\Action;
use \Scrivo\Page;
use \Scrivo\Request;

/**
 * The GetMenu class implements an action for retrieving the children of
 * a page.
 */
class GetMenu extends Action {

	/**
	 * In this action the children of the page with the given id are
	 * retrieved and only the home page if no id was given. Folder type
	 * pages are omitted in the retrieved data.
	 */
	function doAction() {

		$res = array();
		$id = Request::get("pageId", Request::TYPE_INTEGER, 0);
		try {
			// Try to load the childeren ...
			$page = Page::fetch($this->context, $id);
			foreach ($page->children as $c) {
				if ($c->type !== Page::TYPE_SUB_FOLDER) {
					$res[] = $this->createRow($c);
				}
			}
		} catch (\Exception $e) {
			// ... or try the home page.
			$res[] = $this->createRow(Page::fetch(
				$this->context, $this->context->config->ROOT_PAGE_ID));
		}

		$this->setResult(self::SUCCESS, $res);

	}

	/**
	 * Fill a data array with page data.
	 *
	 * @param Page $p The page to use as source for the data.
	 * @return array() The page data.
	 */
	private function createRow(Page $p) {

		$nch = false;
		foreach($p->children as $c) {
			if ($c->type !== Page::TYPE_SUB_FOLDER) {
				$nch = true;
				break;
			}
		}

		$a = "menutree.getMenu";
		$url = "?pageId={$p->id}&a=".$a;

		$style = array();
		if (!$p->roles->canWrite($this->context->principal)) {
			$style[] = "color:gray";
		}
		if (!$p->isOnline) {
			$style[] = "text-decoration: line-through";
		}

		return array(
			"id" => $p->id,
			"title" => (string)$p->title,
			"childListUrl" => $nch ? $url : "",
			"type" => $p->type,
			// TODO refactor: ugly hack
			"style" => implode(";", $style)
		);

	}

}

?>