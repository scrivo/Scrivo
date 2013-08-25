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
 * $Id: UserList.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Layout\UserList action class.
 */

namespace ScrivoUi\Admin\Actions\Layout;

use \Scrivo\I18n;
use \Scrivo\LayoutAction;
use \Scrivo\User;

/**
 * The UserList layout action class sets up the layout for the user list.
 */
class UserList extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->ui_lang);

		$type = $this->parameters["type"];

		$userData = array(
			"admin" => (object)array(
				"title1" => $i18n["Super user"],
				"title2" => $i18n["Super user overview"],
				"title3" => $i18n["Create a new [L]super user account[/L]"],
				"status" => User::STATUS_ADMIN
			),
			"editor" => (object)array(
				"title1" => $i18n["Editor"],
				"title2" => $i18n["Editor overview"],
				"title3" => $i18n["Create a new [L]editor account[/L]"],
				"status" => User::STATUS_EDITOR
			),
			"member" => (object)array(
				"title1" => $i18n["Member"],
				"title2" => $i18n["Member overview"],
				"title3" => $i18n["Create a new [L]member account[/L]"],
				"status" => User::STATUS_MEMBER
			)
		);

		$userStatus = $userData[$type]->status;

		$title = $userData[$type]->title2;

		$items1 = User::select($this->context);

		$form_action = "?a={$type}_form";

		$items = array();

		foreach ($items1 as $u) {
			if ($userStatus == $u->status) {
				$items[] = $u;
			}
		}

		usort($items,
			function ($a, $b) { return $a->userCode > $b->userCode; });

		include "../ScrivoUi/Admin/Templates/common.tpl.php";
		include "../ScrivoUi/Admin/Templates/Lists/user_list.tpl.php";
		$this->useLayout("../ScrivoUi/Admin/Templates/master.tpl.php");

		$this->setResult(self::SUCCESS);

	}
}

?>