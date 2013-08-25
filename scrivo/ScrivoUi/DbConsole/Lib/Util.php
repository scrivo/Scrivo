<?php
/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "Scrivo" nor the names of its contributors may be
 *    used to endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * $Id: Util.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\DbConsole\Lib;

class Util {

	const GUID = "C3D873A6_E040_4B2E_93AA_9215460169A0";

	public static function cleanWwwRoot($www_root) {
		return str_replace("/", "_",
			str_replace(array("https://", "http://"), "", $www_root));
	}

	public static function createInsertStatment(
			$row_data, $table, $inst_id, $base, $dir, $keys=null) {

		$data = array();
		$fields = array();

		while (list($k, $v) = each($row_data)) {
			if (!is_integer($k)) {

				if ($k == "instance_id")
					$v = "INST_".self::GUID;

				$fields[] = $k;

				if (is_null($v)) {
					$data[] = 'NULL';
				} else if (is_numeric($v) || $k == "instance_id") {
					if ($keys && isset($keys[$k])) {
						$data[] = "KEY_".self::GUID."_".$v;
					} else {
						$data[] = $v;
					}
				} else {
					$v = str_replace($base, "WWW_".self::GUID, $v);
					$v = str_replace($dir, "DIR_".self::GUID, $v);
					$data[] = "'".preg_replace(
						array("'\n'", "'\r'"),
						array('\\n', '\\r'), addslashes($v))."'";
				}
			}
		}
		return "INSERT INTO $table (".join(",", $fields).
			") VALUES (".join(",", $data).");\n";
	}

	public static function dumpTable(
			$conn, $out, $table, $inst_id, $old_base, $old_adir) {

		$res = true;

		$table = strtoupper($table);

		$sth = $conn->prepare(
			"SELECT * FROM {$table} where instance_id = :instId");

		$conn->bindInstance($sth);

		try {
			$sth->execute();

			while ($row_data = $sth->fetch(\PDO::FETCH_ASSOC)) {
				gzwrite($out, self::createInsertStatment(
					$row_data, $table, $inst_id, $old_base, $old_adir));
			}
		}
		catch (\PDOException $e) {
			if ($e->getCode() != "42S22") {
				throw $e;
			}
			$res = false;
		}

		return $res;
	}

	public static function patchInsertStatement(
			$statment, $inst_id, $base, $dir) {

		$statment = str_replace("INST_".self::GUID, $inst_id, $statment);
		$statment = str_replace("WWW_".self::GUID, $base, $statment);
		$statment = str_replace("DIR_".self::GUID, $dir, $statment);

		return $statment;
	}

	/*
	public static function getInstanceInfo() {

		$qry = sql::prepare(
			"SELECT COUNT(*) CNT FROM instance WHERE instance_id = {1:i}",
			INSTANCE_ID);
		$res = sql_query($scrivo_conn, $qry);
		$cnt = sql_result($res, 0, "CNT");
		if (!$cnt) {
			header("Location: import");
			die;
		}
	}
	*/

}

?>