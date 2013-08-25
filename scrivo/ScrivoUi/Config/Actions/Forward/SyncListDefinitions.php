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
 * $Id: SyncListDefinitions.php 851 2013-08-21 00:14:32Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\SyncListDefinitions
 * action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \ScrivoUi\Config\Lib\SyncUtilListDefinition;;
use \Scrivo\Request;
use \Scrivo\String;

/**
 * The SyncListDefinitions class implements the action for synchronizing
 * list defintions with a remote ones.
 */
class SyncListDefinitions extends Action {

	function doAction() {

		$addAppDef = array();
		$delAppDef = array();
		$modAppDef = array();

		$remoteSite = Request::post("site", Request::TYPE_STRING);
		$newcfg = unserialize(file_get_contents(
			"{$remoteSite}/scrivo/config/dump_list_definition_config.php"));
		$curcfg = SyncUtilListDefinition::dumpConfig($this->context);

		$common = array_intersect(array_keys($newcfg), array_keys($curcfg));

		foreach ($_POST as $k => $p) {
			$k = new String($k);
			$tmp = $k->split(new String(":"));
			array_walk($tmp, function(&$i){$i=(string)$i;});
			switch ($tmp[0]) {
			case "del_application_definition":
				$delAppDef[$tmp[1]] = true;
				break;
			case "add_application_definition":
				$addAppDef[$tmp[1]] = $newcfg[$tmp[1]];
				break;
			case "mod_application_definition":
				if (!isset($modAppDef[$tmp[1]])) {
					$modAppDef[$tmp[1]] = array(
						"properties" => array(),
						"add_list_item_definition" => array(),
						"del_list_item_definition" => array(),
						"mod_list_item_definition" => array(),
						"add_list_item_property_definition" => array(),
						"del_list_item_property_definition" => array(),
						"mod_list_item_property_definition" => array()
					);
				}
				switch ($tmp[2]) {
				case "property":
					$modAppDef[$tmp[1]]
						["properties"][$tmp[3]] = $newcfg[$tmp[1]][$tmp[3]];
					break;
				case "del_list_item_definition":
					$modAppDef[$tmp[1]]
						["del_list_item_definition"][$tmp[3]] = true;
					break;
				case "add_list_item_definition":
					$modAppDef[$tmp[1]]
						["add_list_item_definition"][$tmp[3]] =
							$newcfg[$tmp[1]]["TYPES"][$tmp[3]];
					break;
				case "mod_list_item_definition":
					if (!isset($modAppDef[$tmp[1]]
							["mod_list_item_definition"][$tmp[3]])) {
						$modAppDef[$tmp[1]]
							["mod_list_item_definition"][$tmp[3]] = array();
					}
					$modAppDef[$tmp[1]]
						["mod_list_item_definition"][$tmp[3]][$tmp[4]] =
							$newcfg[$tmp[1]]["TYPES"][$tmp[3]][$tmp[4]];
					break;
				case "del_list_item_property_definition":
					$modAppDef[$tmp[1]]
						["del_list_item_property_definition"][$tmp[3]] = true;
					break;
				case "add_list_item_property_definition":
					$modAppDef[$tmp[1]]
						["add_list_item_property_definition"][$tmp[3]] =
							$newcfg[$tmp[1]]["FIELDS"][$tmp[3]];
					break;
				case "mod_list_item_property_definition":
					if (!isset($modAppDef[$tmp[1]]
							["mod_list_item_property_definition"][$tmp[3]])) {
						$modAppDef[$tmp[1]]
							["mod_list_item_property_definition"][$tmp[3]] =
								array();
					}
					$modAppDef[$tmp[1]]
						["mod_list_item_property_definition"][$tmp[3]][$tmp[4]]
							= $newcfg[$tmp[1]]["FIELDS"][$tmp[3]][$tmp[4]];
					break;
				}
				break;
			}

		}

		// Delete application definitions
		foreach ($delAppDef as $appDefId => $data) {
			if ($appDefId) {
				$appItemDefIds = isset($curcfg[$appDefId]["TYPES"])
					? array_keys($curcfg[$appDefId]["TYPES"]) : array();
				SyncUtilListDefinition::deleteApplicationDefinition(
					$this->context, $appDefId, $appItemDefIds);
				unset($this->context->cache[$appDefId]);
			}
		}

		// Add application definitions
		foreach ($addAppDef as $appDefId => $data) {
			if ($appDefId) {
				SyncUtilListDefinition::insertApplicationDefinition(
					$this->context, $appDefId, $data);
			}
		}

		// Update application definitions
		foreach ($modAppDef as $appDefId => $data) {
			if ($appDefId) {
				SyncUtilListDefinition::updateApplicationDefinition(
					$this->context, $data);
				unset($this->context->cache[$appDefId]);
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