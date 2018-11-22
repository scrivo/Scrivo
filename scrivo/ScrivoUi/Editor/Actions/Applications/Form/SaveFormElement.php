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
use \Scrivo\ListItem;
use \Scrivo\Request;
use \Scrivo\Str;
use \Scrivo\Action;

/**
 * The PagePath class implements the action
 */
class SaveFormElement extends Action {

	/**
	 * In this action the page
	 */
	function doAction() {

		$pageId = Request::post("pageId", Request::TYPE_INTEGER);
		$itemId = Request::post("listItemId", Request::TYPE_INTEGER);
		$pagePropertyDefinitionId =
			Request::post("pagePropertyDefinitionId", Request::TYPE_INTEGER);
		
		$list = ItemList::fetch(
			$this->context, $pageId, $pagePropertyDefinitionId);

		if ($itemId) {
			$item = $list->items[$itemId];
		} else {
			$def = ListItemDefinition::fetch($this->context, 40076);
			$item = $list->newItem($def->phpSelector);
		}
		
		$typeData = new \stdClass;
		
		$typeData->name = Request::post(
				"itemInfo_NAME", Request::TYPE_STRING, "", Request::TYPE_STRING, "");
		$typeData->type = Request::post(
				"type", Request::TYPE_STRING, "", Request::TYPE_STRING, "");
		
		switch ($typeData->type) {
			case "input": $this->setTextfield($typeData); break;
			case "textarea": $this->setTextarea($typeData); break;
			case "radiogroup": $this->setRadiogroup($typeData); break;
			case "select": $this->setSelect($typeData); break;
			case "checkbox": $this->setCheckbox($typeData); break;
			case "checkgroup": $this->setCheckgroup($typeData); break;
			case "infotext": $this->setInfotext($typeData); break;
			case "file": $this->setFileinput($typeData); break;
			case "email": $this->setEmail($typeData); break;
		}
		
		$item->properties->typeData->text = serialize($typeData); 
		
		$list->saveItem($item);
		
		$this->setResult(self::SUCCESS);
	}

	function stripArray($a) {
		$r = array();
		while (list($k,$v)=each($a)) {
			if ($v) {
				$r[] = $v;
			}
		}
		return $r;
	}
	
	function bool($name) {
		return Request::post($name, Request::TYPE_STRING, "") == "true";
	}
	
	function setTextfield(\stdClass $td) {
		$td->width = Request::post("itemInfo_WIDTH", Request::TYPE_INTEGER, "");
		$td->required = $this->bool("itemInfo_REQUIRED"); 
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->maxLength = 
			Request::post("itemInfo_MAXLENGTH", Request::TYPE_INTEGER, "");
		$td->defaultValue = 
			Request::post("itemInfo_DEFAULT_VALUE", Request::TYPE_STRING, "");
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
	}
	
	function setEmail(\stdClass $td) {
		$td->width = Request::post("itemInfo_WIDTH", Request::TYPE_INTEGER, "");
		$td->required = $this->bool("itemInfo_REQUIRED"); 
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->maxLength = 
			Request::post("itemInfo_MAXLENGTH", Request::TYPE_INTEGER, "");
		$td->defaultValue = 
			Request::post("itemInfo_DEFAULT_VALUE", Request::TYPE_STRING, "");
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
		$td->replyTo = $this->bool("itemInfo_REPLYTO");
	}
	
	function setFileinput(\stdClass $td) {
		$td->width = Request::post("itemInfo_WIDTH", Request::TYPE_INTEGER, "");
		$td->required = $this->bool("itemInfo_REQUIRED"); 
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->maxLength = 
			Request::post("itemInfo_MAXLENGTH", Request::TYPE_INTEGER, "");
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
	}
	
	function setTextarea(\stdClass $td) {
		$td->width = Request::post("itemInfo_WIDTH", Request::TYPE_INTEGER, "");
		$td->required = $this->bool("itemInfo_REQUIRED"); 
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->rows = Request::post("itemInfo_ROWS", Request::TYPE_INTEGER, "");
		$td->defaultValue = 
			Request::post("itemInfo_DEFAULT_VALUE", Request::TYPE_STRING, "");
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
	}
	
	function setCheckbox(\stdClass $td) {
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->checked = $this->bool("itemInfo_CHECKED");
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
	}
	
	function setRadiogroup(\stdClass $td) {
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->items = $this->stripArray(	explode("\n", 
			Request::post("itemInfo_ITEMS", Request::TYPE_STRING, "")));
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
		$td->unchecked = $this->bool("itemInfo_UNCHECKED"); 
	}
	
	function setSelect(\stdClass $td) {
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->items = $this->stripArray(	explode("\n", 
			Request::post("itemInfo_ITEMS", Request::TYPE_STRING, "")));
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
	}
	
	function setCheckgroup(\stdClass $td) {
		$td->id = Request::post("itemInfo_ID", Request::TYPE_STRING, "");
		$td->label = Request::post("itemInfo_LABEL", Request::TYPE_STRING, "");
		$td->items = $this->stripArray(	explode("\n", 
			Request::post("itemInfo_ITEMS", Request::TYPE_STRING, "")));
		$td->info = Request::post("itemInfo_INFO", Request::TYPE_STRING, "");
	}
	
	function setInfotext(\stdClass $td) {
		$td->infoText = 
			Request::post("itemInfo_INFOTEXT", Request::TYPE_STRING, "");
	}
	
}

?>