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
 * $Id: PageList.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Layout\PageList action class.
 */

namespace ScrivoUi\Admin\Actions\Layout;

use \Scrivo\I18n;
use \Scrivo\LayoutAction;
use \Scrivo\Page;
use \Scrivo\Request;
use \ScrivoUi\Admin\Lib\PageListUtil;

/**
 * The PageList layout action class sets up the layout for the page list.
 */
class PageList extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->ui_lang);

		$roleId =
			Request::get("role_id", Request::TYPE_INTEGER, 0);

		$root = Page::fetch(
			$this->context, $this->context->config->ROOT_PAGE_ID);

		$labels = array();
		foreach ($this->context->labels as $id => $l) {
			$labels[$l] = $id;
		}

		$rows = array();
		$rows[] = PageListUtil::pageToData(
			$root, $i18n, $this->session, $labels, $roleId);
		foreach ($root->children as $c) {
			$rows[] = PageListUtil::pageToData(
				$c, $i18n, $this->session, $labels, $roleId);
		}

		$title = $i18n["Page overview"];

		include "../ScrivoUi/Admin/Templates/common.tpl.php";
		include "../ScrivoUi/Admin/Templates/Lists/page_list.tpl.php";
		$this->useLayout("../ScrivoUi/Admin/Templates/master.tpl.php");

		$this->setResult(self::SUCCESS);

	}

}

?>