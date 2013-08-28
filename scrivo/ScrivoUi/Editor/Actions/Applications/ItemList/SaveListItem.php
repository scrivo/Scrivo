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

namespace ScrivoUi\Editor\Actions\Applications\ItemList;

use \Scrivo\I18n;
use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\ItemList;
use \Scrivo\Request;
use \Scrivo\String;
use \Scrivo\Action;

/**
 * The PagePath class implements the action
 */
class SaveListItem extends Action {

	/**
	 * In this action the page
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$parentId = Request::post("parentId", Request::TYPE_INTEGER, 0);
		$pageId = Request::post("pageId", Request::TYPE_INTEGER);
		$defId = Request::post("listItemDefinitionId", Request::TYPE_INTEGER, 0);
		$itemId = Request::post("listItemId", Request::TYPE_INTEGER);
		$pagePropertyDefinitionId =
			Request::post("pagePropertyDefinitionId", Request::TYPE_INTEGER);

		$list = ItemList::fetch(
			$this->context, $pageId, $pagePropertyDefinitionId);

		if ($itemId) {

			$items = $list->getItems($parentId);
			$item = $items[$itemId];
			$def = ListItemDefinition::fetch(
				$this->context, $item->definitionId);

		} else {

			$def = ListItemDefinition::fetch($this->context, $defId);
			$item = $list->newItem($def->phpSelector);
			$item->parentId = $parentId;

		}

		$this->getListItemValues($item, $def);

		$list->saveItem($item);

		$this->setResult(self::SUCCESS);
	}

	private function getListItemValues($item, $def) {

		$item->dateOffline = Request::post(
			"prop_scrivo_li_offline", Request::TYPE_DATE_TIME, null);
		$item->dateOnline =
			Request::post("prop_scrivo_li_online", Request::TYPE_DATE_TIME);
		$item->title =
			Request::post("prop_scrivo_li_title", Request::TYPE_STRING,
				new String(""));

		$pDefs = ListItemPropertyDefinition::select($this->context,
			$def->applicationDefinitionId);

		$pDefs = $pDefs[$item->definitionId];

		foreach ($item->properties as $prp) {

			$def = $pDefs[$prp->definitionId];

			$name = "prop_{$prp->phpSelector}";

			switch($def->type) {
				case ListItemPropertyDefinition::TYPE_CHECKBOX:
					$prp->checked = Request::post($name, Request::TYPE_BOOLEAN);
					break;
				case ListItemPropertyDefinition::TYPE_COLOR:
					$prp->color = Request::post(
						$name, Request::TYPE_STRING, new String(""));
					break;
				case ListItemPropertyDefinition::TYPE_DATE_TIME:
					$prp->dateTime =
						Request::post($name, Request::TYPE_DATE_TIME, null);
					break;
				case ListItemPropertyDefinition::TYPE_IMAGE:
					$val = Request::post($name, Request::TYPE_STRING,
						new String(""))->split(new String("\t"));
					$c = count($val);
					$prp->src = $c>0 ? $val[0] : new String("");
					$prp->alt = $c>1 ? $val[1] : new String("");
					$prp->title =$c>2 ? $val[2] : new String("");
					break;
				case ListItemPropertyDefinition::TYPE_INPUT:
					$prp->value = Request::post(
						$name, Request::TYPE_STRING, new String(""));
					break;
				case ListItemPropertyDefinition::TYPE_SELECT:
					$prp->value = Request::post(
						$name, Request::TYPE_STRING, new String(""));
					break;
				case ListItemPropertyDefinition::TYPE_URL:
					$val = Request::post($name, Request::TYPE_STRING,
						new String(""))->split(new String("\t"));
					$c = count($val);
					$prp->href = $c>0 ? $val[0] : new String("");
					$prp->title = $c>1 ? $val[1] : new String("");
					$prp->target =$c>2 ? $val[2] : new String("");
					break;
				case ListItemPropertyDefinition::TYPE_TEXT:
					$prp->text = Request::post(
						$name, Request::TYPE_STRING, new String(""));
					break;
				case ListItemPropertyDefinition::TYPE_HTML_TEXT:
					$prp->html = Request::post(
						$name, Request::TYPE_STRING, new String(""));
					break;
			}

		}

	}

}

?>