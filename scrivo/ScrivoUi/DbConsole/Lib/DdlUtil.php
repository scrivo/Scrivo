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
 * $Id: DdlUtil.php 860 2013-08-24 12:42:15Z geert $
 */

namespace ScrivoUi\DbConsole\Lib;

class DdlUtil {


	private static $errorName1 =
		"The %s can only contain only ASCII and no white
		space characters.</p>";

	private static $errorName2 =
		"The %s can only contain the charcters a to z (upper
		an lower case) the digits 0 to 9 and an underscore (_).";

	/**
	 * Convert an SQL defintion file into and array of single line statements
	 * and an array of table names.
	 * @return {\stdClass} An object containing the result data.
	 */
	public static function prepareDbModel($dbmodel) {
		$dbm = str_replace(array("\n", "\r"), "", $dbmodel);
		$dbm = preg_replace("/;\s*/", "\n", $dbm);
		$commands = explode("\n", $dbm);
		$tables = array();
		foreach ($commands as $c) {
			if (preg_match("/create table ([^\s]*)/i", $c, $m)) {
				$tables[] = $m[1];
			}
		}
		return (object)array("commands" => $commands, "tables" => $tables);
	}

	public static function validMySqlName($name) {
		$res = preg_replace("/[^a-zA-Z0-9_]/", "", $name);
		return $res == $name;
	}

	public static function validMySqlPassWord($name) {
		$res = preg_replace("/[\s\x80-\xFF]/", "", $name);
		return $res == $name;
	}

	public static function createDatabase(
			$root, $passwd, $host, $dbname, $dbuser, $dbpassword) {

		if (!self::validMySqlName($root)) {
			throw new \Exception(
				sprintf(self::$errorName1, "root username"));
		}
		if (!self::validMySqlPassWord($passwd)) {
			throw new \Exception(
				sprintf(self::$errorName2, "root password"));
		}
		if (!self::validMySqlPassWord($host)) {
			throw new \Exception(
				sprintf(self::$errorName2, "database host name"));
		}
		if (!self::validMySqlName($dbname)) {
			throw new \Exception(
				sprintf(self::$errorName1, "Scrivo database name"));
		}
		if (!self::validMySqlName($dbuser)) {
			throw new \Exception(
				sprintf(self::$errorName1, "Scrivo database username"));
		}
		if (!self::validMySqlPassWord($dbpassword)) {
			throw new \Exception(
				sprintf(self::$errorName2, "Scrivo database password"));
		}

		try {
			$dsn = "mysql:host=$host";
			$dbh = new \PDO($dsn, $root, $passwd);
		} catch (PDOException $e) {
			throw new \Exception($e->getMessage());
		}

		$sth = $dbh->prepare("SHOW DATABASES LIKE :dbName");
		$sth->bindParam(':dbName', $dbname, \PDO::PARAM_STR);
		$sth->execute();
		if ($sth->rowCount() > 0) {
			$dbh = null;
			throw new \Exception("Database '$dbname' already exists");
		}
		$sth->fetchAll();

		$sth = $dbh->prepare(
			"CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8");
		if (!$sth->execute()) {
			$r = $dbh->errorInfo();
			$dbh = null;
			throw new \Exception($r[2]);
		}

		$sth = $dbh->prepare(
			"GRANT ALL ON {$dbname}.* TO {$dbuser}@localhost ".
			"IDENTIFIED BY :dbPasswd");
		$sth->bindParam(':dbPasswd', $dbpassword, \PDO::PARAM_STR);
		if (!$sth->execute()) {
			$r = $dbh->errorInfo();
			$dbh = null;
			throw new \Exception($r[2]);
		}

		$dbh = null;
	}

	public static function getConnection($host, $dbname, $dbuser, $dbpwd)  {

		if (!self::validMySqlPassWord($host)) {
			throw new \Exception(
				sprintf(self::$errorName2, "database host name"));
		}
		if (!self::validMySqlName($dbname)) {
			throw new \Exception(
				sprintf(self::$errorName1, "database name"));
		}
		if (!self::validMySqlName($dbuser)) {
			throw new \Exception(
				sprintf(self::$errorName1, "database user"));
		}
		if (!self::validMySqlPassWord($dbpwd)) {
			throw new \Exception(
				sprintf(self::$errorName2, "database password"));
		}

		$dsn = "mysql:host=$host;dbname=$dbname";
		$pdo = new \PDO($dsn, $dbuser, $dbpwd);
		$pdo->exec("SET NAMES utf8");

		return $pdo;
	}

}

?>