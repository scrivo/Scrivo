<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: UpdateLanguage.php 846 2013-08-20 12:34:06Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Forward\UpdateLanguage action
 * class.
 */

namespace ScrivoUi\Admin\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\Page;
use \Scrivo\PageSet;
use \Scrivo\Request;

/**
 * The UpdateLanguage class implements the action for updating the language
 * of a page.
 */
class UpdateLanguage extends Action {

	/**
	 * Recurse into all child pages of a page and set their language ids.
	 *
	 * @param PageSet $ps The child pages.
	 * @param int $langId A language id.
	 */
	private function recurse(PageSet $ps, $langId) {
		foreach ($ps as $p) {
			$p->languageId = $langId;
			$p->update();
			$this->recurse($p->children, $langId);
		}
	}

	/**
	 * In this action the language of the page with the given id updated, and
	 * optionally this is recursively done for all child pages too.
	 */
	function doAction() {

		try {

			// Load the page ...
			$page = Page::fetch($this->context,
				Request::post("page_id", Request::TYPE_INTEGER));
			// ... set the language ...
			$page->languageId =
				Request::post("language_id", Request::TYPE_INTEGER);
			// ... and update the page.
			$page->update();

			// Also set the language of all children recursively if required.
			if (Request::post("rec", Request::TYPE_BOOLEAN)) {
				$this->recurse($page->children,
					Request::post("language_id", Request::TYPE_INTEGER));
			}

			// Set action result.
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $user);
		}
	}

}

?>