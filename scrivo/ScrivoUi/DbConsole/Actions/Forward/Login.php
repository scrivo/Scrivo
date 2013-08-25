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
 * $Id: Login.php 860 2013-08-24 12:42:15Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Forward\Login action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\Action;
use \Scrivo\LoginKey;
use \Scrivo\Request;
use \Scrivo\User;

/**
 * The Login class implements the action of logging in into the Scrivo database
 * console interface.
 */
class Login extends Action {

	/**
	 * In this action the given login key is verified.
	 */
	function doAction() {

		$this->session->destroy();

		$loginKey = new LoginKey($this->context);
		$user = $loginKey->verify(Request::get("key", Request::TYPE_STRING));

		if ($user && $user->status <= User::STATUS_ADMIN) {

			$this->session->noOfLogins = 1;
			$this->session->userStatus = $user->status;
			$this->session->authenticated = true;
			$this->session->usercode = $user->userCode;
			$this->session->userId = $user->id;

			$this->setResult(self::SUCCESS);
		}
	}
}

?>
