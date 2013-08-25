<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: bugreport.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

include "../modules/constants.php";
include "i18n.php";
include "login_common.php";

function epst($k) {
	if (isset($_POST[$k])) {
		return $_POST[$k];
	}
	return "";
}

$error = "";

$post = false;
if (isset($_POST["description"])) {

	function check_captcha() {
		$captchacheck = file_get_contents(
			"http://captcha.scrivo.nl/checkcaptcha.php?id=" . $_POST["captcha_id"] .
			"&ip=" . $_SERVER["REMOTE_ADDR"] . "&str=" . $_POST["captcha_str"]);
		return $captchacheck == "OK";
	}

	if (check_captcha()) {

		$txt =
			"IE=".$_POST["ie"].
			"\nOS=".$_POST["os"].
			"\nJS=".$_POST["js"].
			"\nEMAIL=".$_POST["email"].
			"\nTYPE=".(isset($_POST["errtype"]) ? $_POST["errtype"] : "ohtererror").
			"\nDESCR=".$_POST["description"];
		mail("bugs@scrivo.nl", "Bug Report", $txt);
		$post = true;

	} else {

		$error = "INVALIDCAPTCHA";

	}
}

page_start("Scrivo: ".$i18n["bugreport.title"], "bugreport");

if ($post) {
?>

					<h1><?php echo $i18n["bugreport.thanks"] ?></h1>

					<p>&nbsp;</p>

<?php
} else {
?>

					<h1><?php echo $i18n["bugreport.title"] ?></h1>

					<?php echo $i18n["bugreport.text.introduction"] ?>

<?php

	if ($error == "INVALIDCAPTCHA") {
?>
					<p class="error"><?php echo $i18n["error.invalidcaptcha"] ?></p>
<?php
	}
?>
					<form action="bugreport.php" method="post">

						<table class="form" cellpadding="0" cellspacing="4" border="0" width="100%">
							<tr>
								<td class="label" width="25%"><label for="ie os js"><?php echo $i18n["bugreport.text.sysinfo"] ?></label></td>
								<td><input type="text" id="ie" name="ie" size="10" maxlength="255" value="<?php echo epst("ie")?>">
								<input type="text" id="os" name="os" size="15" maxlength="255" value="<?php echo epst("os")?>">
								<input type="text" id="js" name="js" size="10" maxlength="255" value="<?php echo epst("js")?>"></td>
							</tr>
							<tr>
								<td class="label" width="25%"><label for="email"><?php echo $i18n["bugreport.text.email"] ?></label></td>
								<td><input style="width:100%" type="text" id="email" name="email" maxlength="255" value="<?php echo epst("email")?>"></td>
							</tr>
							<tr>
								<td class="label top" rowspan="3"><?php echo $i18n["bugreport.text.errortype"] ?></td>
								<td><input id="jserror" type="radio" name="errtype" size="10" maxlength="255" value="jserror" <?php echo "jserror" == epst("errtype") ? "checked" : "" ?>>
								<label for="jserror"><?php echo $i18n["bugreport.text.jserror"] ?></label><br><img align="right" src="support/js-error.png">
								<span style="font-size:75%"><?php echo $i18n["bugreport.text.jserrorinfo"] ?></span></td>
							</tr>
							<tr>
								<td><input id="apperror" type="radio" name="errtype" size="10" maxlength="255" value="apperror" <?php echo "apperror" == epst("errtype") ? "checked" : "" ?>>
								<label for="apperror"><?php echo $i18n["bugreport.text.apperror"] ?></label><br><img align="right" src="support/ie-error.png">
								<span style="font-size:75%"><?php echo $i18n["bugreport.text.apperrorinfo"] ?></span></td>
							</tr>
							<tr>
								<td><input id="anyerror" type="radio" name="errtype" size="10" maxlength="255" value="othererror" <?php echo "othererror" == epst("errtype") ? "checked" : "" ?>>
								<label for="anyerror"><?php echo $i18n["bugreport.text.othererr"] ?></label></td>
							</tr>
							<tr>
								<td colspan="2" class="label"><label for="description"><?php echo $i18n["bugreport.text.description"] ?></label></td>
							</tr>
							<tr>
								<td colspan="2"><textarea style="width:100%" id="description" name="description" rows="8"><?php echo epst("description")?></textarea></td>
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
								<td><input type="submit" value="<?php echo $i18n["bugreport.button.send"]?>"></td>
							</tr>
						</table>

					</form>

					<script type="text/javascript">

function getSystemParameters() {

	var b = navigator.userAgent;
	var o = "";

	var n = b.indexOf('MSIE');
	if (n !== -1) {
		b = b.substring(n);
		n = b.indexOf(';');
		if (n != -1) {
			o = b.substring(n+2);
			b = b.substring(0,n);
			n = o.indexOf(';');
			if (n == -1)
				n = o.indexOf(')');
			if (n != -1)
				o = o.substring(0, n);
		}
	}

	n = b.indexOf('Gecko');
	if (n !== -1) {
		b1 = b.substring(n);
		o = b.substring(0, n-1);
		b = b1;
		if (n != -1) {
			n = o.indexOf('(');
			if (n != -1)
				n2 = o.indexOf(')');
			if (n2 != -1)
				o = o.substring(n+1, n2);
		}
	}

	var r = new Array();
	r['ie'] = b;
	r['os'] = o;
	r['js'] = "";
	try {
		r['js'] = ScriptEngine() + ' ' + ScriptEngineBuildVersion();
	} catch (e) {}
	return r;
}

var info = getSystemParameters();
<?php
	if (!$error) {
?>
document.getElementById('ie').value = info['ie'];
document.getElementById('os').value = info['os'];
document.getElementById('js').value = info['js'];
<?php
	}
?>
					</script>

<?php

}

page_end();

?>