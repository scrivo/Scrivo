<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: index.php 800 2013-08-11 21:45:53Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

include "../extra/i18n.php";
include "../extra/login_common.php";

require_once("../Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

$cfg = new \Scrivo\Config();

page_start("Scrivo: ".$i18n["login.title"], "login");

?>
					<h1><?php echo $i18n["login.title"] ?></h1>

					<h2><?php echo str_replace(array("http://", "https://"), "", $cfg->WWW_ROOT) ?></h2>

					<form  action="login.php" method="post">

						<input type="hidden" name="user_id" value="">

						<table class="form" cellspacing="0" border="0">
							<tr>
								<td class="label"><label for="usercode"><?php echo $i18n["label.email_usercode"] ?></label></td>
								<td><input type="text" id="usercode" name="usercode" size="30" maxlength="255" value=""></td>
							</tr>
							<tr>
								<td class="label"><label for="password"><?php echo $i18n["label.pwd"] ?></label></td>
								<td><input type="password" id="password" name="password" size="10" maxlength="255" value=""></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" value="<?php echo $i18n["button.login"]?>"></td>
							</tr>
						</table>

					</form>

					<?php echo replace_links($i18n["login.text.introduction"], array("NEWACC" => "new_account.php", "NEWPWD" => "change_password.php")) ?>

<?php

page_end();

?>