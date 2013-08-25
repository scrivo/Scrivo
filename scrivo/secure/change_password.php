<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: change_password.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */
session_start();

include "constants.php";
include "../extra/i18n.php";
include "../extra/login_common.php";

$is_post = false;
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	function check_captcha() {
		$captchacheck = file_get_contents(
			"http://captcha.scrivo.nl/checkcaptcha.php?id=" . $_POST["captcha_id"] .
			"&ip=" . $_SERVER["REMOTE_ADDR"] . "&str=" . $_POST["captcha_str"]);
		return $captchacheck == "OK";
	}

	$is_post = true;

	include "../extra/functions.change_user.php";
	@include "../modules/db_util.php";
	define("OK", "OK");
	include "../modules/user_class.php";

	$email = $_POST["usercode"];
	$error = "";

	$user = new user($scrivo_conn);
	if ($user->load($email) != OK) {

		$error = "NOACCOUNT";

	} else {

		$password = $_POST["password"];
		$password2 = $_POST["password2"];

		if ($password2 != $password) {

			$error = "PWDDIFFER";
		}

		if (Scrivo_utf8_strlen($password) < 5) {

			$error = "PWDTOSHORT";

		}

		if (!check_captcha()) {

			$error = "INVALIDCAPTCHA";
		}
	}

	if (!$error) {

		$key = store_password_change_request($scrivo_conn, $user->user_id, $password);

		mail($email, $i18n["newpassword.mail.subject"], str_replace("[MAIL.LINK]", WWW_ROOT."/scrivo/extra/confirm_pwd.php?activatekey=".urlencode($key), $i18n["newpassword.mail.body"]), "From: scrivo.nl <info@scrivo.nl>"."\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");

	}

}

page_start("Scrivo: ".$i18n["login.title"].": ".$i18n["newpassword.title"], "login");

?>

<h1><?php echo $i18n["login.title"] ?>: <?php echo $i18n["newpassword.title"] ?></h1>

<h2><?php echo str_replace(array("http://", "https://"), "", $constants['WWW_ROOT']) ?></h2>

<?php
if ($is_post && !$error) {
	?>

	<?php echo str_replace("[email_address]", $email, $i18n["newpassword.text.ok"]) ?>

	<?php
} else {
	?>

<form action="change_password.php" method="post"><input type="hidden"
	name="user_id" value=""> <?php
	if ($is_post && $error) {
		?> <?php
		if ($error == "NOACCOUNT") {
			?>
<p class="error"><?php echo $i18n["error.noaccount"] ?></p>
			<?php
		}
		if ($error == "PWDDIFFER") {
			?>
<p class="error"><?php echo $i18n["error.pwddiffer"] ?></p>
			<?php
		}
		if ($error == "PWDTOSHORT") {
			?>
<p class="error"><?php echo $i18n["error.pwdtoshort"] ?></p>
			<?php
		}
		if ($error == "INVALIDCAPTCHA") {
			?>
<p class="error"><?php echo $i18n["error.invalidcaptcha"] ?></p>
			<?php
		}
	} else {
		?> <?php echo $i18n["newpassword.text.introduction"] ?> <?php
	}
	?>

<table class="form" cellspacing="0" border="0">
	<tr>
		<td class="label"><label for="usercode"><?php echo $i18n["label.email"] ?></label></td>
		<td><input type="text" id="usercode" name="usercode" size="30"
			maxlength="255" value="<?php echo $email?>"></td>
	</tr>
	<tr>
		<td class="label"><label for="password"><?php echo $i18n["label.pwd"] ?></label></td>
		<td><input type="password" id="password" name="password" size="10"
			maxlength="255" value=""></td>
	</tr>
	<tr>
		<td class="label"><label for="password2"><?php echo $i18n["label.pwdagain"] ?></label></td>
		<td><input type="password" id="password2" name="password2" size="10"
			maxlength="255" value=""></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo replace_links($i18n["captcha.infotext"], array(
				"NEWCAPTCHA" => "#\" onclick=\"document.getElementById('captcha').src='http://captcha.scrivo.nl/".session_id().".png?dummy='+(new Date()).getTime(); return false;",
				"AUDIOCAPTCHA" => "http://captcha.scrivo.nl/".session_id().".wav")) ?></td>
	</tr>
	<tr>
		<td><img id="captcha"
			src="http://captcha.scrivo.nl/<?php echo session_id()?>.png"></td>
		<td><input type="hidden" name="captcha_id"
			value="<?php echo session_id()?>"><input type="text" size="6"
			name="captcha_str"></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit"
			value="<?php echo $i18n["button.newpassword"]?>"></td>
	</tr>
</table>

</form>

		<?php
}

page_end();

?>