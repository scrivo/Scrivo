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
 * $Id: GetListItem.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\Applications\Form;

use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\ItemList;
use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Str;
use \Scrivo\I18n;

/**
 * The GetFormProperties class implements the action of retrieving form
 * properties.
 */
class GetFormElements extends Action {

	/**
	 * In this action the page the list representing the form is retieved 
	 * and its custom data field is used for the form properties. 
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$pageId = Request::get("pageId", Request::TYPE_INTEGER);
		$pagePropertyDefinitionId =
			Request::get("pagePropertyDefinitionId", Request::TYPE_INTEGER);

		$theList = ItemList::fetch(
			$this->context, $pageId, $pagePropertyDefinitionId);

		$rows = array();
		foreach($theList->items as $item) {
			$row = @unserialize((string)$item->properties->typeData->text);
			if (!$row) {
				$row = new \stdClass;
			}
			$row->listItemId = $item->id;
			$rows[] = $row;
		}
		
		$res["elements"] = $rows;
		
		$this->setResult(self::SUCCESS, $res);
	}

}

?>