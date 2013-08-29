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
 * $Id: index.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("../Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

$cfg = new \Scrivo\Config();

$title = $cfg->WWW_ROOT->replace(
		\Scrivo\String::create(array("http://", "https://")),
		new \Scrivo\String(""));

$i18n = new \Scrivo\I18n($cfg->UI_LANG);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//name_en">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title>Login Administratie Module: <?php echo $title?></title>
		<meta name="author" content="www.scrivo.nl">
		<meta name="copyright" content="© 2002-2013 Scrivo, www.scrivo.nl">
		<meta name="description" content="">
		<style type="text/css">
<?php echo file_get_contents("../css/admin/admin.css")?>
<?php echo file_get_contents("../css/admin/fancy.css")?>
#header div {
	background-image: url(purple_tulips.png);
}
		</style>
	</head>
	<body>
		<div id="header" style="">
			<div>
				<span id="headerlogo"><span class="scrivologo">scr<span class="it">i</span>vo<sup>®</sup></span></span>
				<h1 style="font-style: italic"><?php echo $i18n["Login CMS:"]." ".$title?></h1>
			</div>
		</div>
		<div id="menu">
			<ul><li><a href="#">&nbsp;</a></li></ul>
		</div>
		<div id="maindiv">
			<table cellspacing="0" id="centercontent"><tr><td>
			<div id="content">
				<form action="login.php" method="post">
					<table class="form" cellspacing="0">
						<tr>
							<th colspan="2"><?php echo $i18n["Login CMS"]?></th>
						</tr>
						<tr>
							<td class="label"><?php echo $i18n["Site:"]?></td>
							<td><?php echo $title?></td>
						</tr>
						<tr>
							<td class="label"><label for="usercode"><?php echo $i18n["User code:"]?></label></td>
							<td><input type="text" id="usercode" name="usercode" size="20" maxlength="255" value=""></td>
						</tr>
						<tr>
							<td class="label"><label for="password"><?php echo $i18n["Password:"]?></label></td>
							<td><input type="password" id="password" name="password" size="10" maxlength="255" value=""></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="<?php echo $i18n["Login"]?>"></td>
						</tr>
					</table>

				</form>
			</div>
			</td></tr></table>
		</div>
	</body>
</html>
