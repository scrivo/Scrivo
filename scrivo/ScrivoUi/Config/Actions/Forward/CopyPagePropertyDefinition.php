<?php
/* Copyright (c) 2012-2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: CopyPagePropertyDefinition.php 849 2013-08-20 19:19:50Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\CopyPagePropertyDefinition action
 * class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\PagePropertyDefinition;
use \Scrivo\Request;
use \Scrivo\String;

/**
 * The CopyPagePropertyDefinition class implements the action for copying a
 * page property definition to other page definition tabs.
 */
class CopyPagePropertyDefinition extends Action {

	function doAction() {
		try {

			$pageDefinitionIds = Request::post(
				"page_definition_id", Request::TYPE_STRING, array());

			$prop = PagePropertyDefinition::fetch(
				$this->context, Request::post(
					"page_property_definition_id", Request::TYPE_INTEGER));

			foreach ($pageDefinitionIds as $tid) {

				$tmp = $tid->split(new String("_"));
				$tid = intval($tmp[0]);
				$tab = intval(@$tmp[1]);

				$te = new PagePropertyDefinition($this->context);

				$te->pageDefinitionId = $tid;
				$te->pageDefinitionTabId = $tab;

				$te->title = $prop->title;
				$te->type = $prop->type;
				$te->typeData = $prop->typeData;
				$te->phpSelector = $prop->phpSelector;

				$te->insert();
			}

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(
				array("page_definition_id" => $prop->pageDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e);
			$this->setParameters(array(
				"page_definition_id" => $prop->pageDefinitionId,
				"page_property_definition_id" => $prop->id
			));

		}
	}
}

?>