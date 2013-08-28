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

namespace ScrivoUi\Editor\Actions\Applications\ItemList;

use \Scrivo\ItemList;
use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\String;
use \Scrivo\I18n;

/**
 * The PagePath class implements the action
 */
class GetListItem extends Action {

	/**
	 * In this action the page
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$parentId = Request::get("parentId", Request::TYPE_INTEGER, 0);
		$pageId = Request::get("pageId", Request::TYPE_INTEGER);
		$defId = Request::get("listItemDefinitionId", Request::TYPE_INTEGER, 0);
		$itemId = Request::get("listItemId", Request::TYPE_INTEGER);
		$pagePropertyDefinitionId =
			Request::get("pagePropertyDefinitionId", Request::TYPE_INTEGER);
		$copy = Request::get("copy", Request::TYPE_BOOLEAN);

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
		}

		$tabs = array();
		$items = array(array()); // inner array for items without tabs

		$pDefs = ListItemPropertyDefinition::select($this->context,
			$def->applicationDefinitionId);

		$pDefs = $pDefs[$def->id];

		$i = 0;
		foreach ($pDefs as $pDef) {

			// TODO: type data to camelCase: need to do this here?
			$tmp = new \stdClass;
			foreach($pDef->typeData as $k=>$v) {
				$k2 = preg_replace('/_([a-z])/e', "strtoupper('\\1')",
					trim(strtolower($k)));
				$tmp->{$k2} = $v;
			}
			$pDef->typeData = $tmp;

			if ($pDef->type == ListItemPropertyDefinition::TYPE_TAB) {
				$i++;
				$tabs[$i] = (string)$pDef->title;
				$items[$i] = array();
			} else if ($pDef->type == ListItemPropertyDefinition::TYPE_INFO) {
				$items[$i][] = array(
					"type" => $pDef->type,
					"typeData" => $pDef->typeData,
					"phpSelector" => $pDef->phpSelector,
					"label" => $pDef->title,
					"data" =>  $pDef->typeData
				);
			} else {
				$items[$i][] = array(
					"type" => $pDef->type,
					"typeData" => $pDef->typeData,
					"phpSelector" => $pDef->phpSelector,
					"label" => $pDef->title,
					"data" => $this->getValue(
						$item->properties->{$pDef->phpSelector}, $pDef, $itemId)
				);
			}
		}

		if (count($tabs) && count($items[0])) {
			throw new \ErrorException(count($tabs) .
				"fields list does not start with a tab".count($items));
		}

		$title = $i18n["Title"];
		if (!$def->titleLabel->equals(new String(""))) {
			$title = $def->titleLabel;
		}
		$tit = array (
			"type" => "input",
			"typeData" => "",
			"phpSelector" => "scrivo_li_title",
			"label" => $title,
			"data" => $item->title
		);
		array_unshift($items[count($tabs)?1:0], $tit);

		$items[$i][] = array (
			"type" => "datetime",
			"typeData" => array("type"=>"datetime"),
			"phpSelector" => "scrivo_li_online",
			"label" => $i18n["Online on"],
			"data" => $item->dateOnline->format("Y-m-d h:i:s")
		);

		$items[$i][] = array (
			"type" => "datetime",
			"typeData" => array("type"=>"datetime"),
			"phpSelector" => "scrivo_li_offline",
			"label" => $i18n["Offline on"],
			"data" => $item->dateOffline ?
				$item->dateOffline->format("Y-m-d h:i:s") : ""
		);

		$res["listItemDefinitionId"] = $def->id;
		$res["tabs"] = array_values($tabs);
		$res["items"] = array_values($items);

		$this->setResult(self::SUCCESS, $res);
	}

	private function getValue($prp, $def, $itemId) {
		switch($def->type) {

			case ListItemPropertyDefinition::TYPE_CHECKBOX:
				$BALLOT_BOX = "\xE2\x96\xA1";
				$BALLOT_BOX_WITH_CHECK = "\xE2\x96\xA0";
				return $prp->checked?$BALLOT_BOX_WITH_CHECK:$BALLOT_BOX;

			case ListItemPropertyDefinition::TYPE_COLOR:
				return $prp->color;

			case ListItemPropertyDefinition::TYPE_DATE_TIME:
				$defDate = NULL;
				$fmt = "Y-m-d h:i:s";
				if (!isset($def->typeData->defaultValue)) {
					$dt = new \DateTime();
					$defDate = $dt->format($fmt);
				} else if ($def->typeData->defaultValue &&
						!$def->typeData->defaultValue->equals(
							new String("NULL"))) {
					$dt = new \DateTime();
					$dt->setTimestamp(strtotime($def->typeData->defaultValue));
					$defDate = $dt->format($fmt);
				}
				return $itemId ? $prp->dateTime->format($fmt) : $defDate;

			case ListItemPropertyDefinition::TYPE_IMAGE:
				return "{$prp->src}\t{$prp->alt}\t{$prp->title}";

			case ListItemPropertyDefinition::TYPE_INPUT:
				return $prp->value;

			case ListItemPropertyDefinition::TYPE_SELECT:
				return $prp->value;

			case ListItemPropertyDefinition::TYPE_URL:
				return "{$prp->href}\t{$prp->title}\t{$prp->target}";

			case ListItemPropertyDefinition::TYPE_TEXT:
				return $prp->text;

			case ListItemPropertyDefinition::TYPE_HTML_TEXT:
				return $prp->html;
		}
	}

}

?>