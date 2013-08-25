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
 * $Id: login.php 841 2013-08-19 22:19:47Z geert $
 */

require_once("../Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

$usercode = \Scrivo\Request::post("usercode", \Scrivo\Request::TYPE_STRING);
$password = \Scrivo\Request::post("password", \Scrivo\Request::TYPE_STRING);

$context = new \Scrivo\Context(
	new \Scrivo\Config(), \Scrivo\User::ANONYMOUS_USER_ID);

$loginKey = new \Scrivo\LoginKey($context);

$emailExpr =
	"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i";

if ($loginKey instanceof \Scrivo\LoginKey) {

	$key = $loginKey->generate($usercode, $password);

	if ($key) {

		if ($usercode == "admin") {
			header("Location: ".
				"{$context->config->WWW_ROOT}/scrivo/index.php?key={$key}");
			die;
		}

		if (!preg_match($emailExpr, $usercode)) {
			header("Location: {$context->config->WWW_ROOT}".
				"/scrivo/extra/ask_email.php?usercode={$usercode}&key={$key}");
		} else {
			header("Location: ".
				"{$context->config->WWW_ROOT}/scrivo/index.php?key={$key}");
		}

		die;
	}
}

header("Location: index.php?error=1");

?>