<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: get_redacteurenactiviteit.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

if ($_SERVER["REMOTE_ADDR"] != "81.28.82.46" && $_SERVER["REMOTE_ADDR"] != "212.123.179.13" && $_SERVER["REMOTE_ADDR"] != "::1" && $_SERVER["REMOTE_ADDR"] != "194.109.209.54") {
	header('Status: 401', true, 401);
	die;
}

include "../modules/constants.php";

$month = (int) $_GET["month"];
$year = (int) $_GET["year"];

include "function.redacteurenactiviteit.php";

$res = redacteurenactiviteit(DB_NAME, DB_USERCODE, DB_PASSWORD, $year, $month);

foreach ($res as $r) {

	echo "{$r["count"]}\t{$r["usercode"]}\t{$r["org"]}\t{$r["instance"]}\r\n";

}

?>