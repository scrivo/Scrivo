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
 * $Id: InsertUser.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Forward\InsertUser action class.
 */

namespace ScrivoUi\Admin\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\I18n;
use \Scrivo\Request;
use \Scrivo\User;
use \Scrivo\UserRole;
use \Scrivo\String;

/**
 * The InsertUser class implements the action for creating a new user.
 */
class InsertUser extends Action {

	/**
	 * In this action a new user is created, its members set to the values
	 * from the posted data and saved.
	 */
	function doAction() {

		try {

			$i18n = new I18n($this->context->config->ui_lang);

			$stt = Array(
				"admin" => User::STATUS_ADMIN,
				"editor" => User::STATUS_EDITOR,
				"member" => User::STATUS_MEMBER,
				"requests" => 4
			);

			// Create the user ...
			$user = new User($this->context);

			$pwd1 = Request::post(
				"pwd1", Request::TYPE_STRING. new String(""));
			$pwd2 = Request::post(
				"pwd2", Request::TYPE_STRING, new String(""));
			if (!$pwd1->equals($pwd2)) {
				throw new ApplicationException("Passwords differ");
			}

			// ... set the members ...
			$user->status = $stt[$this->parameters["type"]];
			$user->userCode =  Request::post(
				"user_code", Request::TYPE_STRING, new String(""));
			$user->password = $pwd1;
			$user->givenName = Request::post(
				"given_name", Request::TYPE_STRING, new String(""));
			$user->familyNamePrefix = Request::post(
				"family_name_prefix", Request::TYPE_STRING, new String(""));
			$user->familyName = Request::post(
				"family_name", Request::TYPE_STRING, new String(""));
			$user->emailAddress = Request::post(
				"email_address", Request::TYPE_STRING, new String(""));

			// ... and insert the user.
			$user->insert();

			// Now update the user roles.
			$roles = array();
			foreach (Request::post("roles",
					Request::TYPE_INTEGER, array()) as $roleId) {
				$roles[$roleId] = new \stdClass;
				$roles[$roleId]->id = $roleId;
			}
			foreach (Request::post("publisher_roles",
					Request::TYPE_INTEGER, array()) as $roleId) {
				if (isset($roles[$roleId])) {
					$roles[$roleId]->isPublisher = true;
				}
			}

			UserRole::set($this->context, $user, $roles);

			// Set action result.
			$this->setResult(self::SUCCESS);

		} catch(ApplicationException $e) {

			$this->setResult(self::FAIL, $e, $user);
		}
	}

}

?>