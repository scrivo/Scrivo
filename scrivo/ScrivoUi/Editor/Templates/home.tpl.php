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
 * $Id: home.tpl.php 866 2013-08-25 16:22:35Z geert $
 */

use \Scrivo\Request;
use \Scrivo\Str;

header("Content-type: text/html; charset=utf-8");
header("X-UA-Compatible: IE=edge");

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//name_en">
<html>
	<head>
		<title>Scrivo: <?php echo $this->context->config->WWW_ROOT->replace(
			Str::create(array("http://", "https://")), new Str(""))
		?></title>
		<link rel="stylesheet" type="text/css" href="css/sui.css">
		<link rel="stylesheet" type="text/css" href="css/editor.css">
		<link rel="shortcut icon" href="img/editor/tulip_purple.png">
		<script type="text/javascript">

var SCRIVO_BASE_DIR = "<?php echo $this->context->config->WWW_ROOT?>";
var SUI_DIR = "/scrivo/sui";
var ROOT_PAGE_ID = <?php echo Request::get(
	"pageId", Request::TYPE_INTEGER, $this->context->config->ROOT_PAGE_ID)?>;

		</script>
<?php

if ($this->context->config->JS_DEBUG) {

?>
		<script type="text/javascript" src="js/sui.js"></script>
		<script type="text/javascript" src="js/editor/editor.js"></script>
<?php

} else {

?>
		<script type="text/javascript" src="js/scrivo.js.php"></script>
<?php

}

if ($this->context->config->UI_LANG == "nl_NL") {

?>
		<script type="text/javascript" src="i18n/nl_NL/js/sui.js"></script>
		<script type="text/javascript" src="i18n/nl_NL/js/editor.js"></script>
		<script type="text/javascript">
SUI.i18n.setLocale(SUI.i18n, SUI.i18n.nl_NL.sui);
SUI.i18n.setLocale(SUI.editor.i18n, SUI.i18n.nl_NL.editor);
		</script>
<?php

}

?>
		<script type="text/javascript">

function cachedImages(a) {
	for (var x in a) {
		if (a[x] instanceof Object) {
			cachedImages(a[x]);
		} else {
			var d = a[x].substr(-4);
			if (d === ".png" || d === ".gif" || d === ".jpg") {
				a[x] = "../img/ca.php?i=editor/" + a[x];
			}
		}
	}
}
cachedImages(SUI.resource);
cachedImages(SUI.editor.resource);

		</script>
		<script type="text/javascript">

SUI.initialize();

SUI.onStart = function() {

	SUI.editor.scrivo = null;
	SUI.browser.addEventListener(window, "resize", function() {
		if (SUI.editor.scrivo) {
			SUI.editor.scrivo.setRect(
				0, 0, SUI.browser.viewportWidth, SUI.browser.viewportHeight);
			SUI.editor.scrivo.draw();
		}
	});

	SUI.editor.scrivo = new SUI.editor.Scrivo({
		parent: {el: function() { return document.body; }},
		top: 0,
		left: 0,
		width: SUI.browser.viewportWidth,
		height: SUI.browser.viewportHeight,
		title: "<?php echo $this->context->config->WWW_ROOT->replace(
			Str::create(array("http://", "https://")), new Str("")) ?>"
	});
	SUI.editor.scrivo.draw();

	SUI.editor.scrivo.loadPage(ROOT_PAGE_ID);
};

		</script>
		<style type="text/css">
.header div {
	background-image: url(img/header/purple_tulips.png);
}
		</style>
	</head>
	<body>
	</body>
</html>