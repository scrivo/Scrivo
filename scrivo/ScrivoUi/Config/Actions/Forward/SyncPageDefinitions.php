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
 * $Id: SyncPageDefinitions.php 851 2013-08-21 00:14:32Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\SyncPageDefinitions
 * action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\PageDefinitionHints;
use \Scrivo\Request;
use \Scrivo\Str;
use \ScrivoUi\Config\Lib\SyncUtilPageDefinition;

/**
 * The SyncPageDefinitions class implements the action for synchronizing
 * page defintions with a remote ones.
 */
class SyncPageDefinitions extends Action {

	function updateHints($pageDefId, $common, $newcfg, $insert) {
		foreach ($common as $c) {
			$th = new PageDefinitionHints($this->context, $c,
					PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
			$th[$pageDefId]->maxNoOfChildren =
				$newcfg[$c]["CAN_BE_CHILD_OF"][$pageDefId];
			$th->update();
		}

		if ($insert) {
			$common[] = $pageDefId;
		}

		$th = new PageDefinitionHints($this->context, $pageDefId,
				PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
		foreach ($common as $c) {
			$th[$c]->maxNoOfChildren =
				$newcfg[$pageDefId]["CAN_BE_CHILD_OF"][$c];
		}
		$th->update();
	}

	function doAction() {

		$addPageDef = array();
		$delPageDef = array();
		$modPageDef = array();

		$remoteSite = Request::post("site", Request::TYPE_STRING);
		$newcfg = unserialize(file_get_contents(
			"{$remoteSite}/scrivo/config/dump_page_definition_config.php"));
		$curcfg = SyncUtilPageDefinition::dumpConfig($this->context);

		$common = array_intersect(array_keys($newcfg), array_keys($curcfg));

		foreach ($_POST as $k => $p) {
			$k = new Str($k);
			$tmp = $k->split(new Str(":"));
			array_walk($tmp, function(&$i){$i=(string)$i;});
			switch ($tmp[0]) {
			case "del_page_definition":
				$delPageDef[$tmp[1]] = true;
				break;
			case "add_page_definition":
				$addPageDef[$tmp[1]] = $newcfg[$tmp[1]];
				break;
			case "mod_page_definition":
				if (!isset($modPageDef[$tmp[1]])) {
					$modPageDef[$tmp[1]] = array(
						"properties" => array(),
						"add_page_definition_tab" => array(),
						"del_page_definition_tab" => array(),
						"mod_page_definition_tab" => array(),
						"add_page_property_definition" => array(),
						"del_page_property_definition" => array(),
						"mod_page_property_definition" => array()
					);
				}
				switch ($tmp[2]) {
				case "property":
					$modPageDef[$tmp[1]]["properties"][$tmp[3]] =
					$newcfg[$tmp[1]][$tmp[3]];
					break;
				case "del_page_definition_tab":
					$modPageDef[$tmp[1]]["del_page_definition_tab"][$tmp[3]] =
						true;
					break;
				case "add_page_definition_tab":
					$modPageDef[$tmp[1]]["add_page_definition_tab"][$tmp[3]] =
						$newcfg[$tmp[1]]["ELEMENTS"][$tmp[3]];
					break;
				case "mod_page_definition_tab":
					if (!isset($modPageDef[$tmp[1]]
							["mod_page_definition_tab"][$tmp[3]])) {
						$modPageDef[$tmp[1]]
							["mod_page_definition_tab"][$tmp[3]] = array();
					}
					$modPageDef[$tmp[1]]
						["mod_page_definition_tab"][$tmp[3]][$tmp[4]] =
							$newcfg[$tmp[1]]["ELEMENTS"][$tmp[3]][$tmp[4]];
					break;
				case "del_page_property_definition":
					$modPageDef[$tmp[1]]
						["del_page_property_definition"][$tmp[3]] = true;
					break;
				case "add_page_property_definition":
					$modPageDef[$tmp[1]]
						["add_page_property_definition"][$tmp[3]] =
							$newcfg[$tmp[1]]["PROPERTIES"][$tmp[3]];
					break;
				case "mod_page_property_definition":
					if (!isset($modPageDef[$tmp[1]]
							["mod_page_property_definition"][$tmp[3]])) {
						$modPageDef[$tmp[1]]
							["mod_page_property_definition"][$tmp[3]] = array();
					}
					$modPageDef[$tmp[1]]
						["mod_page_property_definition"][$tmp[3]][$tmp[4]] =
							$newcfg[$tmp[1]]["PROPERTIES"][$tmp[3]][$tmp[4]];
					break;
				}
				break;
			}

		}

		// delete templates
		foreach ($delPageDef as $pageDefId => $data) {
			if ($pageDefId) {
				SyncUtilPageDefinition::deletePageDefinition(
					$this->context, $pageDefId);
				unset($this->context->cache[$pageDefId]);
			}
		}

		// add templates
		foreach ($addPageDef as $pageDefId => $data) {
			if ($pageDefId) {
				SyncUtilPageDefinition::insertPageDefinition(
					$this->context, $data);
				$this->updateHints($pageDefId, $common, $newcfg, true);
			}
		}

		// update templates
		foreach ($modPageDef as $pageDefId => $data) {
			if ($pageDefId) {
				SyncUtilPageDefinition::updatePageDefinition(
					$this->context, $pageDefId, $data);
				$this->updateHints($pageDefId, $common, $newcfg, false);
				unset($this->context->cache[$pageDefId]);
			}
		}

		$res = OK;
		if ($res == OK) {
			$this->setResult(self::SUCCESS);
			$this->setParameters(array("site" => $remoteSite));
		} else {
			$this->setResult(self::FAIL, $res);
		}
	}

}

?>