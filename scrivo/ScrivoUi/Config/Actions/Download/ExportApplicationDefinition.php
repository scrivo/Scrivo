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
 * $Id: ExportApplicationDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Download\ExportApplicationDefinition action class.
 */

namespace ScrivoUi\Config\Actions\Download;

use \Scrivo\Action;
use \Scrivo\ApplicationDefinition;
use \Scrivo\ApplicationException;
use \Scrivo\Downloadable;
use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;
use \Scrivo\Request;
use \Scrivo\Str;

/**
 * The ExportApplicationDefinition class implements the action of exporting a
 * application definition.
 */
class ExportApplicationDefinition extends Action {

	/**
	 * Convert an object to an array (recursive). Convert Str
	 * members to strings and throw out references to contexts. So we'll end
	 * up with a PHP 4 like style array, just containing the bare data.
	 *
	 * @param object $obj The object to convert.
	 *
	 * @return array The object converted to an array.
	 */
	private function objToArr($obj) {
		$dmp = (array)$obj;
		$e = array();
		foreach ($dmp as $k => $d) {
			$t = explode("\0", $k);
			if (count($t) == 3) {
				$k = $t[2];
			}
			if ($k == "context") {
				continue;
			} else if ($d instanceof Str) {
				$d = (string)$d;
			} else if (is_object($d) || is_array($d)) {
				$d = $this->objToArr($d);
			}
			$e[$k] = $d;
		}
		return $e;
	}

	/**
	 * In this action the application with the given id is loaded together
	 * with optional list item definitions and then coverted to an array
	 * (recursive) holding all the members including the properties and tabs,
	 * but not the references to contexts.
	 */
	function doAction() {

		try {
			$id = Request::get("id", Request::TYPE_INTEGER);

			// Get the template.
			$appDef = ApplicationDefinition::fetch($this->context, $id);
			$liDefs = ListItemDefinition::select($this->context, $id);
			$liPropDefs =
				ListItemPropertyDefinition::select($this->context, $id);

			foreach ($liDefs as $liDef) {
				$dummy = $liDef->parentListItemDefinitionIds;
			}

			// Convert to an array.
			$res = array(
				"ApplicationDefinition" => $this->objToArr($appDef),
				"ListItemDefinitions" => $this->objToArr($liDefs),
				"ListItemPropertyDefinitions" => $this->objToArr($liPropDefs)
			);

			// Construct a name for the downloadable file.
			$fname = new Str(
				"application_definition_".$appDef->title.".dat");
			$fname = $fname->replace(new Str(" "),
				new Str("_"))->toLowerCase();

			// And create a downloadable with the file name and serialized data.
			$this->file = new Downloadable($this->context, $fname,
				Downloadable::type_data, serialize($res));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e);

		}
	}
}

?>