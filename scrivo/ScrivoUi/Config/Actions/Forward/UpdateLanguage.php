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
 * $Id: UpdateLanguage.php 852 2013-08-21 12:43:09Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\UpdateLanguage action
 * class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\Language;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The UpdateLanguage class implements the action for updating a an
 * language.
 */
class UpdateLanguage extends Action {

	/**
	 * In this action the language with the given id is loaded, its members
	 * set to the new values from the posted data and saved.
	 */
	function doAction() {

		try {
			// Load the language ...
			$language = Language::fetch($this->context,
				Request::post("language_id", Request::TYPE_INTEGER));

			// ... set the members ...
			$language->shortList =
				Request::post("short_list", Request::TYPE_BOOLEAN);
			$language->isoCode =
				Request::post("code", Request::TYPE_STRING, new Str(""));
			$language->family =
				Request::post("family", Request::TYPE_STRING, new Str(""));
			$language->nameEn =
				Request::post("en", Request::TYPE_STRING, new Str(""));
			$language->nameNl =
				Request::post("nl", Request::TYPE_STRING, new Str(""));

			// ... and update the language.
			$language->update();

			// Set action result.
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $language);
		}
	}

}

?>