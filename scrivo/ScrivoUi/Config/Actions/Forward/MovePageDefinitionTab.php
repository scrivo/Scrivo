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
 * $Id: MovePageDefinitionTab.php 849 2013-08-20 19:19:50Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Actions\Forward\MovePageDefinitionTab
 * action class.
 */

namespace ScrivoUi\Config\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\PageDefinitionTab;
use \Scrivo\Request;
use \Scrivo\SequenceNo;

/**
 * The MovePageDefinitionTab class implements the action for changing the order
 * of a page definition tabs.
 */
class MovePageDefinitionTab extends Action {

	/**
	 * In this action the page definition tab with the given id is loaded and
	 * moved one step up or down.
	 */
	function doAction() {

		try {

			// Load the page definition tab ...
			$tab = PageDefinitionTab::fetch($this->context, Request::get(
				"page_definition_tab_id", Request::TYPE_INTEGER, 0));

			// ... and move it
			if (1 == Request::get("dir", Request::TYPE_INTEGER, 1)) {
				$tab->move(SequenceNo::MOVE_DOWN);
			} else {
				$tab->move(SequenceNo::MOVE_UP);
			}

			// Set action result.
			$this->setResult(self::SUCCESS);
			$this->setParameters(
				array("page_definition_id" => $tab->pageDefinitionId));

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $res);
			$this->setParameters(
				array("page_definition_id" => $tab->pageDefinitionId));

		}
	}
}

?>