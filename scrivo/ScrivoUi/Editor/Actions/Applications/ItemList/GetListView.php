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
 * $Id: GetListView.php 866 2013-08-25 16:22:35Z geert $
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
class GetListView extends Action {

	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$parentId = Request::get("parentId", Request::TYPE_INTEGER, 0);
		$pageId = Request::get("pageId", Request::TYPE_INTEGER);
		$applicationDefinitionId =
			Request::get("applicationDefinitionId", Request::TYPE_INTEGER);
		$pagePropertyDefinitionId =
			Request::get("pagePropertyDefinitionId", Request::TYPE_INTEGER);
		$parentListItemDefinitionId =
			Request::get("parentListItemDefinitionId", Request::TYPE_INTEGER);

		$theList = ItemList::fetch(
			$this->context, $pageId, $pagePropertyDefinitionId);

		$defs = ListItemDefinition::select($this->context,
			$applicationDefinitionId, $parentListItemDefinitionId);

		if (count($defs) > 1) {
			throw "Multipe types found: use block representation";
		}
		$defs = array_values($defs);
		$def = isset($defs[0]) ? $defs[0] : null;

		$pDefs = null;
		if ($def) {
			$pDefs = ListItemPropertyDefinition::select($this->context,
				$def->applicationDefinitionId);
			$pDefs = $pDefs[$def->id];
		}

		$hdrs = $this->getListHeaders($def, $pDefs);

		$res = array(
			"pageId" => $pageId,
			"pagePropertyDefinitionId" => $pagePropertyDefinitionId,
			"type" => $def ? $def->id : 0,
			"subItems" => $def
				? count($def->childListItemDefinitionIds) > 0 : false,
			"headers" => $hdrs["headers"],
			"list" => $this->getListItems($theList->getItems($parentId),
				$def, $hdrs["pDefs"])
		);

		$this->setResult(self::SUCCESS, $res);
	}


	private function defParms($type) {
		switch($type) {
			case ListItemPropertyDefinition::TYPE_INPUT:
			case ListItemPropertyDefinition::TYPE_SELECT:
			case ListItemPropertyDefinition::TYPE_URL:
			case ListItemPropertyDefinition::TYPE_COLOR:
			case ListItemPropertyDefinition::TYPE_HTML_TEXT:
				return array(100,"left");
			case ListItemPropertyDefinition::TYPE_DATE_TIME:
				return array(70,"right");
			case ListItemPropertyDefinition::TYPE_CHECKBOX:
				return array(20,"center");
		}
	}

	private function getListHeaders($def, $pDefs) {

		$headers = array();
		$headers[] = array(
			"LABEL" => ($def && $def->titleLabel)
				? $def->titleLabel : $this->i18n["Titel"],
			"TYPE" => "input",
			"COL_TYPE" => "_SCRIVO_TITLE",
			"COL_WIDTH" => ($def && $def->titleWidth) ? $def->titleWidth : 150,
			"COL_ALIGN" => "left"
		);

		$resDefs = array();

		if ($pDefs) {
			foreach ($pDefs as $k=>$v) {
				if ($v->inList
						&& $v->type != ListItemPropertyDefinition::TYPE_INFO
						&& $v->type != ListItemPropertyDefinition::TYPE_TAB) {
					$resDefs[] = $v;
					switch($v->type) {
						case ListItemPropertyDefinition::TYPE_INPUT:
						case ListItemPropertyDefinition::TYPE_SELECT:
						case ListItemPropertyDefinition::TYPE_URL:
						case ListItemPropertyDefinition::TYPE_COLOR:
						case ListItemPropertyDefinition::TYPE_CHECKBOX:
						case ListItemPropertyDefinition::TYPE_DATE_TIME:
						case ListItemPropertyDefinition::TYPE_HTML_TEXT:
							/*
							 if (isset($data["COL_IS_NUMERIC"]) ?
								 @$data["COL_IS_NUMERIC"] : false) {
								 $sort_type[] = "numeric";
							 } else if ($v2->type == "date") {
								 $sort_type[] = "date";
							 } else {
								 $sort_type[] = "html_text";
							 }
							*/
							$def = $this->defParms($v->type);
							$headers[] = array(
								"LABEL" => $v->title,
								"TYPE" => $v->type,
								"COL_TYPE" => $v->phpSelector,
								"COL_WIDTH" => isset($v->typeData->COL_WIDTH)
									? intval($v->typeData->COL_WIDTH) : $def[0],
								"COL_ALIGN" => isset($v->typeData->COL_ALIGN)
									? $v->typeData->COL_ALIGN : $def[1]
							);
					}
				}
			}
		}

		return array("headers" => $headers, "pDefs" => $resDefs);
	}


	private function getListItems($theList, $def, $pDefs) {

		$list = array();
		$type = -1;

		foreach ($theList as $k=>$v) {
				
			$row = array();

			$row["_SCRIVO_ID"] = $v->id;
			$row["_SCRIVO_DEF_ID"] = $def ? $def->id : 0;
			$row["_SCRIVO_TITLE"] = $v->title->substr(0, 100);

			if ($type == -1) {
				$type = 0;
				if ($def && $def->childListItemDefinitionIds) {
					$type = 1;
				}
			}
			$row["_SCRIVO_TYPE"] = $type;

			$row["_SCRIVO_DOCUMENT_ID"] = NULL;

			if ($def && $def->pageDefinitionId) {
				$row["_SCRIVO_DOCUMENT_ID"] = $v->linkedPageId;
			}

			foreach ($pDefs as $pDef) {
				
				$row[(string)$pDef->phpSelector] =
					$this->getValue($v->properties->{$pDef->phpSelector},
					$pDef);
			}

			$list[] = $row;
		}

		return $list;
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
				return $prp->alt;
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
				return $prp->title;
			case ListItemPropertyDefinition::TYPE_TEXT:
				return $prp->text;
			case ListItemPropertyDefinition::TYPE_HTML_TEXT:
				return $prp->html;
		}

	}


}