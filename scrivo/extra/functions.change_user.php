<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: functions.change_user.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

function store_ucode_change_request($conn, $user_id, $email) {

	$key = md5(session_id());

	sql_query($conn, "delete from user_change_password where instance_id = ".INSTANCE_ID." and key = '$key'");
	sql_query($conn, "insert into user_change_password (instance_id, user_id, date_request, password, key, email_address) values (".
		INSTANCE_ID.",$user_id, now(), '', '$key', '".sql_esc($email)."')");

	return $key;
}

function store_password_change_request($conn, $user_id, $password) {

	$key = md5(session_id());

	sql_query($conn, "delete from user_change_password where instance_id = ".INSTANCE_ID." and key = '$key'");
	sql_query($conn, "insert into user_change_password (instance_id, user_id, date_request, password, key, email_address) values (".
		INSTANCE_ID.",$user_id, now(), '".sql_esc($password)."', '$key', '')");

	return $key;
}

function confirm_ucode_change_request($conn, $key) {

	$result = sql_query($conn, "SELECT user_id, email_address from user_change_password where instance_id = ".
		INSTANCE_ID." and key = '".sql_esc($key)."' ORDER BY date_request DESC");

	if ($row_data = sql_fetch_array($result)) {

		$u = new user($conn);
		$u->load($row_data["user_id"], true);
		$u->usercode = $row_data["email_address"];
		$res = $u->update();

		sql_query($conn, "DELETE from user_change_password where instance_id = ".INSTANCE_ID." and key = '".sql_esc($key)."'");

	} else {

		$res = "INVALID_KEY";

	}

	sql_free_result($result);

	return $res;

}

function confirm_pwd_change_request($conn, $key) {

	$result = sql_query($conn, "SELECT user_id, password from user_change_password where instance_id = ".
		INSTANCE_ID." and key = '".sql_esc($key)."' ORDER BY date_request DESC");

	if ($row_data = sql_fetch_array($result)) {

		$u = new user($conn);
		$u->load($row_data["user_id"], true);
		$u->password = $row_data["password"];
		$res = $u->update_password();

		sql_query($conn, "DELETE from user_change_password where instance_id = ".INSTANCE_ID." and key = '".sql_esc($key)."'");

	} else {

		$res = "INVALID_KEY";

	}

	sql_free_result($result);

	return $res;

}

?>