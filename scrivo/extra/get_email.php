<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: get_email.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */
session_start();

include "../modules/constants.php";
include "../modules/db_util.php";
include "../modules/user_class.php";
include "../modules/user_var_class.php";
include "../modules/secure_login.php";

$base1 = Scrivo_utf8_split("/", str_replace(array("http://", "https://"), "", WWW_ROOT));

include "i18n.php";
include "login_common.php";
include "functions.change_user.php";

$email = post("email");

page_start("Scrivo: ".$i18n["login.title"].": ".$i18n["getemail.title"], "login");

?>
					<h1><?php echo $i18n["login.title"] ?>: <?php echo $i18n["getemail.title"] ?></h1>

					<h2><?php echo str_replace(array("http://", "https://"), "", WWW_ROOT) ?></h2>
<?php
if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
?>

					<form  action="get_email.php" method="post">

						<p class="error"><?php echo $i18n["error.invalidemail"] ?></p>

						<table class="form" cellpadding="0" border="0">
							<tr>
								<td class="label"><label for="email"><?php echo $i18n["label.email"] ?></label></td>
								<td><input type="text" id="email" name="email" size="30" maxlength="255" value="<?php echo $email?>"></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" value="<?php echo $i18n["button.verstuur"]?>"></td>
							</tr>
						</table>

					</form>
<?php

} else {

	$user = new user($scrivo_conn);
	$user->load(session("e_login_user_id"), true);

	$key = store_ucode_change_request($scrivo_conn, $user->user_id, $email);

	mail($email, $i18n["getemail.mail.subject"], str_replace("[MAIL.LINK]", WWW_ROOT."/scrivo/extra/confirm_ucode.php?activatekey=".urlencode($key), $i18n["getemail.mail.body"]), "From: scrivo.nl <info@scrivo.nl>"."\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");

	$dw = new user_var($scrivo_conn, $_SESSION["e_login_usercode"], 'screenwidth');
	$sw = $dw->get();
	if (!$sw) {
		$sw = 900;
		$dw->set($sw);
	}

	$dh = new user_var($scrivo_conn, $_SESSION["e_login_usercode"], 'screenheight');
	$sh = $dh->get();
	if (!$sh) {
		$sh = 600;
		$dh->set($sh);
	}

?>
					<?php echo str_replace("[email_address]", $email, $i18n["getemail.text.ok"]) ?>

					<script>

var w = parseInt("<?php echo $sw?>", 10);
if (isNaN(w) || w < 750) {
	w = 750;
}
var h = parseInt("<?php echo $sh?>", 10);
if (isNaN(h) || h < 450) {
	h = 450;
}

function openScrivo() {
	size();
	try {
		ModalDialog.show(w, h, "../scrivo.php", null, null, true);
	} catch (e) {}
}

					</script>

					<p><a href="javascript:openScrivo()"><?php echo $i18n["common.openwindow"] ?></a></p>

<?php

}

page_end();

?>