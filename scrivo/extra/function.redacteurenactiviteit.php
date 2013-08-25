<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: function.redacteurenactiviteit.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

function redacteurenactiviteit($db_name, $db_user, $db_passwd, $year, $month) {

	if (!$year && !$month) {
		$from = strtotime('-2 year');
		$to = strtotime('now');
	} else if ($year && $month) {
		$from = strtotime("$year-$month-01");
		$to = strtotime("+1 month", $from);
	} else {
		die("No valid date selection");
	}

	$mysqli = new mysqli("localhost", $db_user, $db_passwd, $db_name);

	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	function cmp($a, $b) {
		return $a["count"] < $b["count"];
	}

	if ($stmt = $mysqli->prepare("SELECT
			T.user_code,
			SUBSTRING_INDEX(SUBSTRING_INDEX(user_code, '@', -1),'.',1) AS ORG,
			T.instance_id,
			instance.title,
			COUNT(*) CNT
		FROM
		  (
			  SELECT
				  DISTINCT user.user_code, user.instance_id, DATE(date_login)
			  FROM
				  login_events, user
			  WHERE
				  login_events.instance_id = user.instance_id AND
				  login_events.user_id = user.user_id AND
				  return_code = 3 AND
				  date_login >= ? AND
				  date_login < ? AND
				  (user.status = 2 OR user.status = 1) AND
				  user_code <> 'admin'
			) T,
			instance
		WHERE
			instance.instance_id = T.instance_id
		GROUP BY
			T.user_code,
			T.instance_id
		ORDER BY
			user_code")) {

		$stmt->bind_param("ss", date("Y-m-d", $from), date("Y-m-d", $to));
		$stmt->execute();
		$stmt->bind_result($usercode, $org, $inst_id, $instance, $count);

		$res = array();

		while ($stmt->fetch()) {
			if (!isset($res[$usercode])) {
				$res[$usercode] = array(
					"usercode" => $usercode,
					"org" => $org,
					"instance" => "$instance ($count)",
					"count" => (int) $count
				);
			} else {
				$res[$usercode]["instance"] .= ", $instance ($count)";
				$res[$usercode]["count"] += $count;
			}
		}

		usort($res, "cmp");

		$stmt->close();
	}

	$mysqli->close();

	return $res;
}

?>