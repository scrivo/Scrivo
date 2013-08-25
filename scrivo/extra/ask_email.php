<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: ask_email.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

session_start();
session_destroy();
session_start();

$_SESSION["e_no_logins"] = "";
$_SESSION["e_login_authenticated"] = "";
$_SESSION["e_login_usercode"] = "";
$_SESSION["e_login_user_id"] = "1";

include "../modules/constants.php";
include "../modules/db_util.php";
include "../modules/user_class.php";
include "../modules/user_var_class.php";
include "../modules/secure_login.php";

$base1 = Scrivo_utf8_split("/", str_replace(array("http://", "https://"), "", WWW_ROOT));
$login_url = WWW_ROOT."/scrivo/secure";
$use_secure = false;
if (defined("SECURE_LOGIN")) {
	$use_secure = true;
	$login_url = SECURE_LOGIN."/".$base1[0]."/scrivo";
}
define("SCRIVO_LOGIN_URL", $login_url);

if (!isset($_GET["key"])) {
	header("Location: ".SCRIVO_LOGIN_URL);
	die;
}

include "i18n.php";
include "login_common.php";

$res = secure_login(get("key"));

if ($res) {

	if ($res["status"] <= 2) {

		/* Yes we are in */
		$_SESSION["e_no_logins"] = 1;
		$_SESSION["e_login_status"] = $res["status"];
		$_SESSION["e_login_authenticated"] = true;
		$_SESSION["e_login_usercode"] = $res["user_code"];
		$_SESSION["e_login_user_id"] = $res["user_id"];
	}
}

if (!$_SESSION["e_login_authenticated"]) {
	header("Location: ".SCRIVO_LOGIN_URL);
	die;
}

page_start("Scrivo: ".$i18n["login.title"].": ".$i18n["getemail.title"], "login");

?>

					<h1><?php echo $i18n["login.title"] ?>: <?php echo $i18n["getemail.title"] ?></h1>

					<h2><?php echo str_replace(array("http://", "https://"), "", WWW_ROOT) ?></h2>

					<?php echo $i18n["getemail.text.introduction"] ?>

					<form  action="get_email.php" method="post">

						<table class="form" cellpadding="0" border="0">
							<tr>
								<td class="label"><label for="email"><?php echo $i18n["label.email"] ?></label></td>
								<td><input type="text" id="email" name="email" size="30" maxlength="255" value=""></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" value="<?php echo $i18n["button.verstuur"]?>"></td>
							</tr>
						</table>

					</form>

<?php

page_end();

?>