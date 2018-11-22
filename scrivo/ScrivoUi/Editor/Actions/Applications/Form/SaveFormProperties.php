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
 * $Id: SaveListItem.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\Applications\Form;

use \Scrivo\I18n;
use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\ItemList;
use \Scrivo\Request;
use \Scrivo\Str;
use \Scrivo\Action;

/**
 * The PagePath class implements the action
 */
class SaveFormProperties extends Action {

	/**
	 * In this action the page
	 */
	function doAction() {

		$pageId = Request::post("pageId", Request::TYPE_INTEGER);
		$pagePropertyDefinitionId =
			Request::post("pagePropertyDefinitionId", Request::TYPE_INTEGER);
		
		$form = ItemList::fetch(
			$this->context, $pageId, $pagePropertyDefinitionId);

		$form->customData->subject = 
			Request::post("emailSubject", Request::TYPE_STRING);
		$form->customData->mailTo = 
			Request::post("mailTo", Request::TYPE_STRING);
		$form->customData->captcha = 
			Request::post("captcha", Request::TYPE_BOOLEAN);
		$form->customData->captchaText = 
			Request::post("captchaText", Request::TYPE_STRING);
		
		$form->update();

		$this->setResult(self::SUCCESS);
	}

}

?>