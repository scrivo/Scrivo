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
 * $Id: UpdateAccess.php 846 2013-08-20 12:34:06Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Forward\UpdateAccess action
 * class.
 */

namespace ScrivoUi\Admin\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\Asset;
use \Scrivo\File;
use \Scrivo\ObjectRole;
use \Scrivo\Page;
use \Scrivo\PageSet;
use \Scrivo\Request;

/**
 * The UpdateAccess class implements the action for updating the roles
 * of a page or file folder.
 */
class UpdateAccess extends Action {

	/**
	 * Recurse into all children a page or file folder and set their roles.
	 *
	 * @param PageSet|AssetSet $ps The child pages or folders.
	 * @param array $roles A set of role ids.
	 */
	private function recurse($ps, $roles) {
		foreach ($ps as $p) {
			if (!($p instanceof File)) {
				ObjectRole::set($this->context, $p->id, $roles);
				$this->recurse($p->children, $roles);
			}
		}
	}

	/**
	 * In this action the roles of the object with the given id are updated,
	 * and optionally this is recursively done for all child objects too.
	 */
	function doAction() {

		try {

			$type = $this->parameters["type"];

			$objId = Request::post(
				"{$type}_id", Request::TYPE_INTEGER);

			// Load the page or asset.
			if ($type == "page") {
				$obj = Page::fetch($this->context, $objId);
			} else {
				$obj = Asset::fetch($this->context, $objId);
			}

			// Convert the array with role ids.
			$rls = array();
			foreach (Request::post(
					"roles", Request::TYPE_INTEGER) as $id) {
				$r = new \stdClass;
				$r->id = intval($id);
				$rls[] = $r;
			}

			// Set the roles for the object.
			ObjectRole::set($this->context, $obj->id, $rls);

			// Also set the roles of all children recursively if required.
			if (Request::post("rec", Request::TYPE_BOOLEAN)) {
				$this->recurse($obj->children, $rls);
			}

			// Set action result.
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $user);
		}
	}

}

?>