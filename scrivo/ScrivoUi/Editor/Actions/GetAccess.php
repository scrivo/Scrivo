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
 * $Id: GetAccess.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the ScrivoUi\Editor\Actions\GetAccess action class.
 */

namespace ScrivoUi\Editor\Actions;

use \Scrivo\Action;
use \Scrivo\Request;
use \Scrivo\Page;
use \Scrivo\Asset;

/**
 * The Login class implements an action for retrieving the roles of an asset
 * or a page.
 */
class GetAccess extends \Scrivo\Action {

	/**
	 * In this action the given page or asset with the given id is loaded
	 * and it role settings are returned.
	 */
	function doAction() {

		$objId = \Scrivo\Request::get(
			"itemId", \Scrivo\Request::TYPE_INTEGER);

		try {
			$obj = \Scrivo\Page::fetch($this->context, $objId);
		} catch (\Exception $e) {
			$obj = \Scrivo\Asset::fetch($this->context, $objId);
		}

		$res1 = array();
		foreach (\Scrivo\Role::select(
				$this->context, \Scrivo\Role::PUBLIC_ROLE) as $role) {
			$res1[$role->id] = array("label" => (string)$role->title,
					"checked" => isset($obj->roles[$role->id]));
		}

		$res2 = array();
		foreach (\Scrivo\Role::select(
				$this->context, \Scrivo\Role::EDITOR_ROLE) as $role) {
			$res2[$role->id] = array("label" => (string)$role->title,
				"checked" => isset($obj->roles[$role->id]));
		}

		$res = array("publicRoles" => $res1, "editorRoles" => $res2);


		$this->setResult(self::SUCCESS, $res);
	}


}

?>