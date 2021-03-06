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
 * $Id: UpdateListItemPropertyDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\UpdateListItemPropertyDefinition action
 * class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The UpdateListItemPropertyDefinition class implements the action for
 * updating a list item property definition.
 */
class UpdateListItemPropertyDefinition extends Action {

	/**
	 * In this action an existing list item property definition is loaded and
	 * its members set from post data and updated.
	 */
	function doAction() {

		try {

			$i18n = new I18n($this->context->config->UI_LANG);

			$id = Request::post("list_item_property_id", Request::TYPE_INTEGER);

			// Get the list item property definition ...
			$prop = ListItemPropertyDefinition::fetch($this->context, $id);

			// ... set the members ...
			$prop->listItemDefinitionId = Request::post(
				"list_item_definition_id", Request::TYPE_INTEGER, 0);
			$prop->title = Request::post(
				"label", Request::TYPE_STRING, $i18n["New property"]);
			$prop->type = (string)Request::post("type", Request::TYPE_STRING);
			$prop->typeData = $this->getTypeDataFromString(Request::post(
				"type_data", Request::TYPE_STRING, new Str("")));
			$prop->phpSelector = Request::post(
				"php_selector", Request::TYPE_STRING, new Str(""));
			$prop->inList = Request::post("config", Request::TYPE_BOOLEAN);

			// ... and update it.
			$prop->update();

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(array("application_definition_id" =>
				$prop->applicationDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e->getMessage(), $prop);

		}
	}

	/**
	 * Convert a string to its most likely type.
	 * TODO refactor
	 *
	 * @param string $val The value to convert to either an int, float or
	 *    Str.
	 *
	 * @return int|float|Str The given value converted to its
	 *    most likely type.
	 */
	private function readStr($val) {
		$str = (string)$val;
		if (is_numeric($str)) {
			if ((string)$str === (string)(int)$str) {
				return intval($str);
			}
			return floatval($str);
		}
		return $val;
	}

	/**
	 * Set the type data member form a string representation. The format of
	 * the string should be NAME1=VALUE1\nNAME2=VALUE2\nNAME3...etc.
	 * TODO refactor
	 *
	 * @param Str $str The type data string.
	 */
	private function getTypeDataFromString(Str $str) {
		$d = array();
		$parts = $str->split(new Str("\n"));
		foreach($parts as $line) {
			$p = $line->split(new Str("="), 2);
			if (count($p) == 2) {
				$d[(string)$p[0]->trim()] = $this->readStr($p[1]->trim());
			}
		}
		return (object)$d;
	}

}

?>