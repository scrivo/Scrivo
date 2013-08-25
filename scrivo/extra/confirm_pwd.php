<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: confirm_pwd.php 6 2011-02-22 04:29:39Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

include "../modules/constants.php";
include "../modules/db_util.php";
include "../modules/user_class.php";

include "i18n.php";
include "login_common.php";
include "functions.change_user.php";

$res = confirm_pwd_change_request($scrivo_conn, get("activatekey"));

page_start("Scrivo: ".$i18n["login.title"].": ".$i18n["newpassword.title"], "login");

?>

					<h1><?php echo $i18n["login.title"] ?>: <?php echo $i18n["newpassword.title"] ?></h1>

					<h2><?php echo str_replace(array("http://", "https://"), "", $constants['WWW_ROOT']) ?></h2>

<?php
if ($res == OK) {
?>

					<?php echo replace_links($i18n["newpassword.text.confirmok"], array("LOGIN" => WWW_ROOT."/scrivo")) ?>

<?php
} else {
?>

					<?php echo $i18n["newpassword.text.confirmerror"] ?>

<?php
}

page_end();

?>