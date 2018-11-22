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
 * $Id: UpdateUser.php 845 2013-08-20 00:47:20Z geert $
 */

/**
 * Implementation of the ScrivoUi\Admin\Actions\Forward\UpdateUser action class.
 */

namespace ScrivoUi\Admin\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\ApplicationException;
use \Scrivo\Request;
use \Scrivo\User;
use \Scrivo\UserRole;
use \Scrivo\Str;

/**
 * The UpdateUser class implements the action for updating a user.
 */
class UpdateUser extends Action {

	/**
	 * In this action the user with the given id is loaded, its members
	 * set to the new values from the posted data and saved.
	 */
	function doAction() {

		try {

			// Load the user ...
			$user = User::fetch($this->context,
				Request::post("user_id", Request::TYPE_INTEGER));

			// ... set the members ...
			$user->status = Request::post(
				"status", Request::TYPE_INTEGER, 0);
			$user->userCode = Request::post(
				"user_code", Request::TYPE_STRING, new Str(""));
			$user->givenName = Request::post(
				"given_name", Request::TYPE_STRING, new Str(""));
			$user->familyNamePrefix = Request::post(
				"family_name_prefix", Request::TYPE_STRING, new Str(""));
			$user->familyName = Request::post(
				"family_name", Request::TYPE_STRING, new Str(""));
			$user->emailAddress = Request::post(
				"email_address", Request::TYPE_STRING, new Str(""));

			// ... and update the user.
			$user->update();

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