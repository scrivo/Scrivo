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
 * $Id: SyncUtil.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Lib\SyncUtils action class.
 */

namespace ScrivoUi\Config\Lib;

/**
 * private utility functions for use in config module
 * NB: do not use outside this scope
 */
class SyncUtil {

	/**
	 * Make sure no unallowed characters can be used for table names and
	 * fields. Limit the the character set used to [^a-zA-Z0-9_]
	 *
	 * @param string $name
	 * @return string cleaned string
	 */
	private static function clean($name) {
		return preg_replace("/[^a-zA-Z0-9_]/", "", $name);
	}

	/**
	 * Insert a record in the database using an array that contains field
	 * names for the keys and row data for the values.
	 *
	 * @param \Scrivo\Context $context A Scrivo context
	 * @param string $table The table name.
	 * @param array $fields An array in which the key names correspond with
	 *    the field names of the table excluding the instance_id field.
	 */
	public static function insert($context, $table, $fields) {

		$table = self::clean($table);
		$tmp = array();
		foreach ($fields as $n=>$v) {
			$n = self::clean($n);
			$tmp[$n] = ":val_$n";
		}

		$sql = "INSERT INTO {$table} (instance_id, ".
			implode(", ", array_keys($tmp)).
			") VALUES (:instId, ".implode(", ", $tmp).")";
		$sth = $context->connection->prepare($sql);

		$context->connection->bindInstance($sth);

		foreach ($fields as $n=>$v) {
			$n = self::clean($n);
			$sth->bindValue(":val_{$n}", $v, \PDO::PARAM_STR);
		}

		$sth->execute();
	}

	/**
	 * Update a record in the database using an array that contains field
	 * names for the keys and row data for the values.
	 *
	 * @param \Scrivo\Context $context A Scrivo context
	 * @param string $table The table name.
	 * @param string $idField The name of the id field.
	 * @param string $table The id values.
	 * @param array $fields An array in which the key names correspond with
	 *    the field names that need to be updated.
	 */
	public static function update($context, $table, $idField, $id, $fields) {

		if (!count($fields)) {
			return;
		}

		$table = self::clean($table);
		$idField = self::clean($idField);
		$tmp = array();
		foreach ($fields as $n=>$v) {
			$n = self::clean($n);
			$tmp[] = "$n = :val_$n";
		}

		$sql = "UPDATE {$table} SET ".implode(", ", $tmp).
			" WHERE instance_id = :instId AND {$idField} = :id";

		$sth = $context->connection->prepare($sql);

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		foreach ($fields as $n=>$v) {
			$n = self::clean($n);
			$sth->bindValue(":val_{$n}", $v, \PDO::PARAM_STR);
		}

		$sth->execute();
	}

	/**
	 * Delete a record from a database table.
	 *
	 * @param \Scrivo\Context $context A Scrivo context
	 * @param string $table The table name.
	 * @param string $idField The name of the id field.
	 * @param string $table The id values.
	 */
	public static function delete($context, $table, $idField, $id) {

		$table = self::clean($table);
		$idField = self::clean($idField);
		$sql = "DELETE FROM {$table} WHERE
			instance_id = :instId and {$idField} = :id";

		$sth = $context->connection->prepare($sql);

		$context->connection->bindInstance($sth);
		$sth->bindValue(":id", $id, \PDO::PARAM_INT);

		$sth->execute();
	}

	/**
	 * Sort an array on its keys recursively.
	 *
	 * @param array $a The array to sort.
	 */
	public static function recKeySort(&$a) {
		if (is_array($a)) {
			ksort($a);
			foreach ($a as $k=>$v) {
				self::recKeySort($a[$k]);
			}
		}
	}

	/**
	 * Compare the sets of arrays, return the keys that are not found in
	 * $arrOld in the 'add' member of the return values, those that are
	 * $not found in $arrNew in the 'rem' member of the return values and
	 * those that are changed in 'mod' member of the return values.
	 *
	 * $a = array(1 => "a", 3 => "c", 4 => "d");
	 * $b = array(2 => "b", 3 => "x", 4 => "d");
	 *
	 * SyncUtils::keyDiff($a, $b);
	 *
	 * yields:
	 *
	 * array("add" => array(1), "rem" => array(2), "mod" => array(3));
	 *
	 * @param array $arrNew The new array to compare to the old one.
	 * @param array $arrOld The old array to which the new one is compared to.
	 * @return array An array with the entries 'add', 'rem' and 'mod'
	 *   containing the keys of the added, removed and changed entries in
	 *   $arrNew when compared to $arrOld.
	 */
	public static function keyDiff($arrNew, $arrOld) {
		$kset_new = array_keys($arrNew);
		$kset_old = array_keys($arrOld);
		$kset_sm = array_intersect($kset_new, $kset_old);
		$mod = array();
		foreach ($kset_sm as $k) {
			if ($arrNew[$k] !== $arrOld[$k]) {
				$mod[] = $k;
			}
		}
		return array(
				"add" => array_diff($kset_new, $kset_old),
				"rem" => array_diff($kset_old, $kset_new),
				"mod" => $mod
		);
	}

	/**
	 * Extract the keys and a single field value from a sub array in a nested
	 * array.
	 *
	 * $arr = array(
	 *     1 => array("x" => 1, "y" => array(
	 *         2 => array("y1" => 10, "y2" => 11),
	 *         3 => array("y1" => 12, "y2" => 13))),
	 *     2 => array("x" => 2, "y" => array(
	 *         4 => array("y1" => 20, "y2" => 21),
	 *         5 => array("y1" => 22, "y2" => 23))),
	 * );
	 *
	 * SyncUtils::extractField($arr, "y", "y2")
	 *
	 * yields:
	 *
	 * array(2 => 11, 3 => 13, 4 => 21, 5 => 23);
	 *
	 * @param array $data The array to extract data from.
	 * @param string $subArrayKey The key name of the sub array to extract
	 *    data from.
	 * @param string $subArrayField The key name of the of a key in the sub
	 *    array.
	 * @return array Array containing the keys and field values from the
	 *    sub arrays in the array.
	 */
	public static function extractField($data, $subArrayKey, $subArrayField) {
		$res = array();
		foreach ($data as $t) {
			if (isset($t[$subArrayKey])) {
				foreach ($t[$subArrayKey] as $tab_id => $tab) {
					$res[$tab_id] = $tab[$subArrayField];
				}
			}
		}
		return $res;
	}

}

?>