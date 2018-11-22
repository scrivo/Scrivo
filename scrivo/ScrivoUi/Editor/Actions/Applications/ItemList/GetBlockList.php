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
 * $Id: GetBlockList.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\Applications\ItemList;

use \Scrivo\ItemList;
use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Str;
use \Scrivo\I18n;

/**
 * The PagePath class implements the action
 */
class GetBlockList extends Action {

	/**
	 * In this action the page
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$parentId = Request::get("parentId", Request::TYPE_INTEGER, 0);
		$pageId = Request::get("pageId", Request::TYPE_INTEGER);
		$pageNo = Request::get("pageNo", Request::TYPE_INTEGER, 1);
		$search = Request::get("search", Request::TYPE_STRING);
		$pagePropertyDefinitionId =
			Request::get("pagePropertyDefinitionId", Request::TYPE_INTEGER);

		$theList = ItemList::fetch(
			$this->context, $pageId, $pagePropertyDefinitionId);

		$first = true;
		$last = true;

		if (!$search->trim()->equals(new Str(""))) {

			$the_list->load_items($parent_id);
			$newlist = array();
			while(list($k, $v) = each($the_list->items)) {
				if (stripos($v->title, $search) !== false) {
					$newlist[$k] = $v;
				} else {
					foreach($v->fields as $fld) {
						if (stripos($fld, $search) !== false) {
							$newlist[$k] = $v;
							break;
						}
					}
				}
			}
			$first = false;
			$the_list->items = $newlist;

		} else {

			$l = array_slice($theList->getItems($parentId), ($pageNo-1)*10, 11);

			$first = $pageNo == 1;
			$last = true;
			if (count($l) == 11) {
				$last = false;
				array_pop($l);
			}

		}

		$list = array();

		$selectJson = array();

		foreach ($l as $li) {

			$liDef =
				ListItemDefinition::fetch($this->context, $li->definitionId);

			$row = array();

			$row[] = array(
				"label" => $i18n["Titel"],
				"type" => "text",
				"data" => (string)($li->title->length ?
					$li->title->substr(0, 100) : new Str(""))
			);

			$row[] = array(
				"label" => $i18n["On-/offline"],
				"type" => "date",
				"data" => $li->dateOnline->format("Y-m-d h:i:s")
			);

			$row[] = array(
				"label" => $i18n[""],
				"type" => "date",
				"data" => $li->dateOffline ?
					$li->dateOffline->format("Y-m-d h:i:s") : ""
			);

			$row[] = array(
				"label" => $i18n["Type"],
				"type" => "text",
				"data" => (string)$liDef->title
			);

			// distributed lis: add an extra row with the page id
/*
			if ($the_app->type == 5) {
				$url = WWW_ROOT."/index.php?p=".$li->item_pid;
				$txt = url_display($url);
				$row[] = array(
						"label" => "Pagina",
						"type" => "url",
						"data" => array(
								"href" => $url,
								"target" => "_blank",
								"title" => $txt,
								"text" => $txt
						)
				);
			}
*/

			foreach ($li->properties as $prp) {

				$def = ListItemPropertyDefinition::fetch($this->context,
					$prp->definitionId);

				if ($def->inList) {
					$row[] = array(
						"label" => (string)$def->title,
						"type" => $def->type,
						"data" => $this->getValue($prp, $def)
					);

				}

			}

			$list[] = array(
				"listItemId" => $li->id,
				"listItemDefinitionId" => $li->definitionId,
				"linkedPageId" =>
					$liDef->pageDefinitionId ? $li->linkedPageId : 0,
				"subItems" => count($liDef->childListItemDefinitionIds) > 0,
				"row" => $row);
		}

		$res["items"] = $list;
		$res["first"] = $first;
		$res["last"] = $last;

		$this->setResult(self::SUCCESS, $res);
	}

	private function getValue($prp, $def) {
		switch($def->type) {
			case ListItemPropertyDefinition::TYPE_CHECKBOX:
				$BALLOT_BOX = "\xE2\x96\xA1";
				$BALLOT_BOX_WITH_CHECK = "\xE2\x96\xA0";
				return $prp->checked?$BALLOT_BOX_WITH_CHECK:$BALLOT_BOX;
			case ListItemPropertyDefinition::TYPE_COLOR:
				return $prp->color;
			case ListItemPropertyDefinition::TYPE_DATE_TIME:
				return $prp->dateTime->format("Y-m-d h:i:s");
			case ListItemPropertyDefinition::TYPE_IMAGE:
				return array(
					"src" => $prp->src,
					"alt" => $prp->alt,
					"title" => $prp->title
				);
			case ListItemPropertyDefinition::TYPE_INPUT:
				return $prp->value;
			case ListItemPropertyDefinition::TYPE_SELECT:
				$opts2 = array();
				if (isset($def->typeData->FEED)) {
					$feed = $def->typeData->FEED;
					if (isset($selectJson[$feed])) {
						$opts2 = $selectJson[$feed];
					} else {
						$opts = json_decode(file_get_contents(
							"{$this->context->config->WWW_ROOT}/{$feed}"));
						foreach($opts->data as $opt) {
							$opts2[(string)$opt->value] =
							(string)$opt->text;
						}
					}
				} else if (isset($def->typeData->location)) {
					$opts = $def->typeData->location->split(new Str(";"));
					foreach ($opts as $opt) {
						$tv = $opt->split(new Str(":"));
						if (count($tv) > 1) {
							$opts2[(string)$tv[0]] = $tv[1];
						} else {
							$opts2[(string)$tv[0]] = $tv[0];
						}
					}
				}
				$opt = (string)$prp->value;
				return isset($opts2[$opt]) ? $opts2[$opt]: $opt;
			case ListItemPropertyDefinition::TYPE_URL:
				// $txt = url_display($url);
				return array(
					"href" => $prp->href,
					"title" => $prp->title,
					"target" => $prp->target,
					"text" => ""
				);
			case ListItemPropertyDefinition::TYPE_TEXT:
				return $prp->text;
			case ListItemPropertyDefinition::TYPE_HTML_TEXT:
				return $prp->html;
		}

	}
}

?>