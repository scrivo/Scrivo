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
 * $Id: DeleteListItemDefinition.php 850 2013-08-20 23:16:37Z geert $
 */

/**
 * Implementation of the
 * ScrivoUi\Config\Actions\Forward\DeleteListItemDefinition action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\ListItemDefinition;
use \Scrivo\Request;

/**
 * The DeleteListItemDefinition class implements the action for deleting a
 * lsit item definition.
 */
class DeleteListItemDefinition extends Action {

	/**
	 * In this action the list item definition with the given id is deleted.
	 */
	function doAction() {
		try {

			$id = Request::get(
				"list_item_definition_id", Request::TYPE_INTEGER);

			// Load it first: we need the applicationDefinitionId afterwards.
			$liDef = ListItemDefinition::fetch($this->context, $id);

			// Delete the list item definition.
			ListItemDefinition::delete($this->context, $id);

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(array("application_definition_id" =>
				$liDef->applicationDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $res);
			$this->setParameters(array("application_definition_id" =>
				$liDef->applicationDefinitionId));

		}
	}
}

?>