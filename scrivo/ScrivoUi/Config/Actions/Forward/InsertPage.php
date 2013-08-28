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
 * $Id: InsertPage.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\InsertPage action
 * class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;
use \Scrivo\IdLabel;
use \Scrivo\Page;
use \Scrivo\Request;
use \Scrivo\String;

/**
 * The InsertPage class implements the action for creating a new page.
 */
class InsertPage extends Action {

	/**
	 * In this action a new page is created, its members set to the values
	 * from the posted data and saved.
	 */
	function doAction() {

		try {

			$i18n = new I18n($this->context->config->UI_LANG);

			// Create the page ...
			$page = new Page($this->context);

			// ... set the members ...
			$page->parentId =
				Request::post("page_pid", Request::TYPE_INTEGER, 0);
			$page->type =
				Request::post("type", Request::TYPE_INTEGER, 0);
			$page->languageId =
				Request::post("language_id", Request::TYPE_INTEGER);

			$date = new \DateTime();
			$date->setDate(
				Request::post("online_on_y", Request::TYPE_INTEGER),
				Request::post("online_on_m", Request::TYPE_INTEGER),
				Request::post("online_on_d", Request::TYPE_INTEGER));
			if ($date) {
				$page->dateOnline = $date;
			}

			if (!Request::post("remove_on_y",
					Request::TYPE_STRING)->trim()->equals(new String(""))) {
				$date = new \DateTime();
				$date->setDate(
					Request::post("remove_on_y", Request::TYPE_INTEGER),
					Request::post("remove_on_m", Request::TYPE_INTEGER),
					Request::post("remove_on_d", Request::TYPE_INTEGER));
				if ($date) {
					$page->dateOffline = $date;
				}
			} else {
				$page->dateOffline = null;
			}

			$page->title = Request::post("title",
				Request::TYPE_STRING, $i18n["New page"]);
			$page->description = Request::post("description",
				Request::TYPE_STRING, new String(""));
			$page->keywords = Request::post("keywords",
				Request::TYPE_STRING, new String(""));
			$page->stylesheet = Request::post("stylesheet",
				Request::TYPE_STRING, new String(""));
			$page->javascript = Request::post("javascript",
				Request::TYPE_STRING, new String(""));

			// ... and insert the page.
			$page->insert();

			// Add the label if given.
			IdLabel::set($this->context, $page->id,
				Request::post("label", Request::TYPE_STRING, new String("")));

			// Set action result.
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $page);
		}
	}

}

?>