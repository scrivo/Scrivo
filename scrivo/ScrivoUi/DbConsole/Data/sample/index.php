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
 */

/**
 * Description:
 * Script that loads a Scrivo page and dipatches to the page template.
 */

use Scrivo\Config;
use Scrivo\Context;
use Scrivo\Session;
use Scrivo\User;
use Scrivo\Request;
use Scrivo\Page;

// Initialize the Scrivo autoloader.
require_once("scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

// Create the session ...
$session = new Session();
// ... and make sure a userId is set in this session.
if (!isset($session->userId)) {
	$session->userId = User::ANONYMOUS_USER_ID;
}

// Set up the Scrivo context.
$ctx = new Context(new Config(), $session->userId);

// Get the page id of the page we want to load
define("PAGE_ID",
	Request::request("p", Request::TYPE_INTEGER , $ctx->config->ROOT_PAGE_ID));

$page = Page::fetch($ctx, PAGE_ID);

include $page->definition->action;

?>