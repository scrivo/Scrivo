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
 * $Id: RedirectToLogin.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the ScrivoUi\Editor\Actions\RedirectToLogin action class.
 */

namespace ScrivoUi\Editor\Actions;

/**
 * The RedirectToLogin class implements an action for redirecting to the
 * logging page.
 */
class RedirectToLogin extends \Scrivo\Action {

	/**
	 * This action destroys the current session and redirect to the login
	 * page.
	 */
	function doAction() {

		$this->session->destroy();

		$loginUrl = new \Scrivo\String(
			"{$this->context->config->WWW_ROOT}/scrivo/secure/index.php");

		if (defined("SECURE_LOGIN")) {
			$loginUrl->replace(array(
					new \Scrivo\String("http://"),
					new \Scrivo\String("https://"),
					new \Scrivo\String("/secure")
				), new \Scrivo\String(""));
			$loginUrl->replace(
				new \Scrivo\String("/"),
				new \Scrivo\String("_"));

			$loginUrl = new \Scrivo\String(SECURE_LOGIN."/{$loginUrl}");
		}

		header("Location: {$loginUrl}");
		die;
	}
}

?>