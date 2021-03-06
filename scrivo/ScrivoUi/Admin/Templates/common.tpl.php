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
 * $Id: common.tpl.php 858 2013-08-22 13:03:03Z geert $
 */

use \Scrivo\Request;
use \Scrivo\Str;

/**
 * Section to use as HTML head content.
 */
$this->beginSection("head", true);
//$this->sections->head->start();

?>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="author" content="www.scrivo.nl">
		<meta name="copyright" content="© 2003-2013 Scrivo, www.scrivo.nl">
		<meta name="description" content="">
		<title><?php echo $title ?></title>
		<link rel="stylesheet" type="text/css" href="../css/admin/admin.css">
		<link rel="stylesheet" type="text/css" href="../css/admin/fancy.css">
		<link rel="stylesheet" type="text/css" href="../css/sui.css">
		<link rel="shortcut icon" href="../../img/admin/tulip_blue.png">
		<script type="text/javascript">

var SCRIVO_BASE_DIR = "<?php echo $this->context->config->WWW_ROOT?>";
var SUI_DIR = "/<?php echo $this->context->config->WWW_ROOT?>/scrivo/sui";
var ROOT_PAGE_ID = <?php echo Request::get("page_id",
	Request::TYPE_INTEGER, $this->context->config->ROOT_PAGE_ID)?>;

		</script>
		<script type="text/javascript" src="../js/scrivo.js.php"></script>
		<script type="text/javascript">

SUI.initialize();
SUI.imgDir = "../img/editor";

		</script>
		<style type="text/css">
#header div {
	background-image: url(../img/header/red_tulips.png);
}
		</style>
<?php

$this->endSection();
//$this->sections->head->end();


/**
 * Logo bar with title.
 */
$this->beginSection("header", true);
//$this->sections->header->start();

?>
<div>
<span id="headerlogo">
<span class="scrivologo">scr<span class="it">i</span>vo<sup>®</sup></span>
</span>
<h1 style="font-style: italic"><?php echo $i18n["Scrivo Administration:"]?>
	<?php echo $title ?></h1>
</div>
<?php

$this->endSection();
//$this->sections->header->end();

/**
 * The admin menu.
 */
$this->beginSection("menu", true);
//$this->sections->menu->start();

if ($this->session->authenticated) {

?>
	<a id="loginout" href="index.php?a=logout"><?php
		echo $i18n["Log out"]?></a>
	<ul>
		<li>
			<span><?php
				echo $i18n["Users"]?></span>
			<ul>
				<li><a href="index.php?a=editor_list"><?php
					echo $i18n["Editors"]?></a></li>
				<li><a href="index.php?a=member_list"><?php
					echo $i18n["Members"]?></a></li>
				<li><a href="index.php?a=admin_list"><?php
					echo $i18n["Super-users"]?></a></li>
				<li><a href="index.php?a=requests_list"><?php
					echo $i18n["Requests"]?></a></li>
				<li><a href="index.php?a=role_list"><?php
					echo $i18n["Roles"]?></a></li>
			</ul>
		</li>
		<li><a href="index.php?a=page_list"><?php
			echo $i18n["Pages"]?></a></li>
		<li><a href="index.php?a=asset_list"><?php
			echo $i18n["Files"]?></a></li>
	</ul>
<?php

	} else {

		$base1 = Str::create(
			str_replace(array("http://", "https://"), "",
			$this->context->config->WWW_ROOT))->split(new Str("/"));
		$login_url = "{$this->context->config->WWW_ROOT}/scrivo/secure/admin/index.php";
		$use_secure = false;
		if (defined("SECURE_LOGIN")) {
			$use_secure = true;
			$login_url = SECURE_LOGIN."/".implode("_", $base1)."/scrivo/admin/index.php";
		}

?>
	<ul><li><a href="<?php echo $login_url?>"><?php
		echo $i18n["Log in"]?></a></li></ul>
<?php

}

$this->endSection();
//$this->sections->menu->end();

?>