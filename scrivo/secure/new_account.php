<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: new_account.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */
session_start();

include "constants.php";
include "../extra/i18n.php";
include "../extra/login_common.php";

$is_post = false;
$naam = "";
$tussenvoegsels = "";
$achternaam = "";
$telefoon = "";
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

	$naam = Scrivo_byte_array_trim($_POST["naam"]);
	$tussenvoegsels = Scrivo_byte_array_trim($_POST["tussenvoegsels"]);
	$achternaam = Scrivo_byte_array_trim($_POST["achternaam"]);
	$telefoon = Scrivo_byte_array_trim($_POST["telefoon"]);
	$email = Scrivo_byte_array_trim($_POST["email"]);
	$error = "";

	if ($naam == "" || $achternaam == "" || $telefoon == "" || $email == "") {

		$error = "NODATA";

	} else {

		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$user = new user($scrivo_conn);

		if ($user->load($email) == OK) {

			$error = "ACCOUNTEXISTS";

		} else if ($password2 != $password) {

			$error = "PWDDIFFER";

		} else if (Scrivo_utf8_strlen($password) < 5) {

			$error = "PWDTOSHORT";

		} else if (!check_captcha()) {

			$error = "INVALIDCAPTCHA";

		} else {

			$user->usercode = $email;
			$user->first_name = $naam;
			$user->prefix = $tussenvoegsels;
			$user->surname = $achternaam;
			$user->email = $telefoon;
			$user->status = 4;
			$user->insert();

			$user->password = $password;
			$user->update_password();

		}

	}

	if (!$error) {

		$body = str_replace("[SITE]", WWW_ROOT, $i18n["newaccount.mail.body"]);
		$body .= "\n";
		$body .= "$naam $tussenvoegsels $achternaam\n";
		$body .= $i18n["label.email"].": $email\n";
		$body .= $i18n["label.phone"].": $telefoon\n";
		$body .= "\n";

		mail($i18n["newaccount.mail.mailto"],
		$i18n["newaccount.mail.subject"], $body, 'From: scrivo.nl <info@scrivo.nl>'."\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");

	}

}

page_start("Scrivo: ".$i18n["login.title"].": ".$i18n["newaccount.title"], "login");

?>

<h1><?php echo $i18n["login.title"] ?>: <?php echo $i18n["newaccount.title"] ?></h1>

<h2><?php echo str_replace(array("http://", "https://"), "", $constants['WWW_ROOT']) ?></h2>

<?php
if ($is_post && !$error) {
	?>

	<?php echo $i18n["newaccount.text.ok"] ?>

	<?php
} else {
	?>

<form action="new_account.php" method="post"><input type="hidden"
	name="user_id" value=""> <?php
	if ($is_post && $error) {
		?> <?php
		if ($error == "NODATA") {
			?>
<p class="error"><?php echo $i18n["error.missingdata"] ?></p>
			<?php
		}
		if ($error == "ACCOUNTEXISTS") {
			?>
<p class="error"><?php echo $i18n["error.accountexists"] ?></p>
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
		?> <?php
	} else {
		?> <?php echo $i18n["newaccount.text.introduction"] ?> <?php
	}
	?>

<table class="form" cellspacing="0" border="0">
	<tr>
		<td class="label"><label for="naam"><?php echo $i18n["label.firstname"] ?>
		*</label></td>
		<td><input type="text" id="naam" name="naam" size="40" maxlength="255"
			value="<?php echo $naam?>"></td>
	</tr>
	<tr>
		<td class="label"><label for="tussenvoegsels"><?php echo $i18n["label.prefix"] ?></label></td>
		<td><input type="text" id="tussenvoegsels" name="tussenvoegsels"
			size="10" maxlength="255" value="<?php echo $tussenvoegsels?>"></td>
	</tr>
	<tr>
		<td class="label"><label for="achternaam"><?php echo $i18n["label.surname"] ?>
		*</label></td>
		<td><input type="text" id="achternaam" name="achternaam" size="40"
			maxlength="255" value="<?php echo $achternaam?>"></td>
	</tr>
	<tr>
		<td class="label"><label for="email"><?php echo $i18n["label.email"] ?>
		*</label></td>
		<td><input type="text" id="email" name="email" size="30"
			maxlength="255" value="<?php echo $email?>"></td>
	</tr>
	<tr>
		<td class="label"><label for="telefoon"><?php echo $i18n["label.phone"] ?>
		*</label></td>
		<td><input type="text" id="telefoon" name="telefoon" size="15"
			maxlength="255" value="<?php echo $telefoon?>"></td>
	</tr>
	<tr>
		<td class="label"><label for="password"><?php echo $i18n["label.pwd"] ?>
		*</label></td>
		<td><input type="password" id="password" name="password" size="10"
			maxlength="255" value=""></td>
	</tr>
	<tr>
		<td class="label"><label for="password2"><?php echo $i18n["label.pwdagain"] ?>
		*</label></td>
		<td><input type="password" id="password2" name="password2" size="10"
			maxlength="255" value=""></td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo replace_links($i18n["captcha.infotext"], array(
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
			value="<?php echo $i18n["button.newaccount"]?>"></td>
	</tr>
</table>

</form>

	<?php
}

page_end();

?>