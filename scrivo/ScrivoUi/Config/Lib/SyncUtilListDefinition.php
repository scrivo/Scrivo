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
 * $Id: SyncUtilListDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Lib\SyncUtilListDefinition
 * action class.
 */

namespace ScrivoUi\Config\Lib;

/**
 * private utility functions for use in config module
 * NB: do not use outside this scope
 */
class SyncUtilListDefinition extends SyncUtil {

	/**
	 * Create a data structure containing all the application and list
	 * definition data.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @return array An (nested) array that contains the application and list
	 *    definition data in this context.
	 */
	public static function dumpConfig(\Scrivo\Context $context) {

		$sth1 = $context->connection->prepare(
			"SELECT * FROM application_definition WHERE instance_id = :instId");
		$context->connection->bindInstance($sth1);
		$sth1->execute();

		$sth2 = $context->connection->prepare(
			"SELECT * FROM list_item_definition WHERE instance_id = :instId");
		$context->connection->bindInstance($sth2);
		$sth2->execute();

		$sth3 = $context->connection->prepare(
			"SELECT * FROM list_item_property_definition WHERE instance_id = :instId");
		$context->connection->bindInstance($sth3);
		$sth3->execute();

		$sth4 = $context->connection->prepare(
			"SELECT * FROM parent_list_item_definitions WHERE instance_id = :instId");
		$context->connection->bindInstance($sth4);
		$sth4->execute();

		$lipt = array();
		while ($rd4 = $sth4->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd4["instance_id"]);
			if (!isset($lipt[$rd4["list_item_definition_id"]])) {
				$lipt[$rd4["list_item_definition_id"]] = array();
			}
			$lipt[$rd4["list_item_definition_id"]][] =
				$rd4["parent_list_item_definition_id"];
		}

		$res = array();
		while ($rd1 = $sth1->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd1["instance_id"]);
			$rd1["TYPES"] = array();
			$rd1["FIELDS"] = array();
			$res[$rd1["application_definition_id"]] = $rd1;
		}

		while ($rd2 = $sth2->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd2["instance_id"]);
			$rd2["LIST_ITEM_PARENT_TYPE_IDS"] =
				isset($lipt[$rd2["list_item_definition_id"]]) ?
					$lipt[$rd2["list_item_definition_id"]] : array();
			if (isset($res[$rd2["application_definition_id"]])) {
				$res[$rd2["application_definition_id"]]["TYPES"]
					[$rd2["list_item_definition_id"]] = $rd2;
			}
		}
		while ($rd3 = $sth3->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd3["instance_id"]);
			if (isset($res[$rd3["application_definition_id"]])) {
				$res[$rd3["application_definition_id"]]["FIELDS"]
					[$rd3["list_item_definition_id"]] = $rd3;
			}
		}

		return $res;
	}

	/**
	 * Insert application and list definition data into the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $data The defintion data to insert.
	 */
	public static function insertApplicationDefinition(
			\Scrivo\Context $context, $data) {

		$pr = $data["FIELDS"];
		unset($data["FIELDS"]);
		$el = $data["TYPES"];
		unset($data["TYPES"]);

		self::insert($context, "application_definition", $data);

		foreach ($el as $e) {
			$pids = $e["LIST_ITEM_PARENT_TYPE_IDS"];
			unset($e["LIST_ITEM_PARENT_TYPE_IDS"]);
			self::insert($context, "list_item_definition", $e);
			foreach ($pids as $pid) {
				self::insert($context, "parent_list_item_definitions", array(
					"list_item_definition_id" => $e["list_item_definition_id"],
					"parent_list_item_definition_id" => $pid
				));
			}
		}

		foreach ($pr as $p) {
			self::insert($context, "list_item_property_definition", $p);
		}
	}

	/**
	 * Update application and list definition data in the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $appDefId The application definition id for which to update
	 *    the data.
	 * @param array $data The defintion data for the update.
	 */
	public static function updateApplicationDefinition(
			\Scrivo\Context $context, $appDefId, $data) {

		if (count($data["properties"])) {
			self::update($context,
				"application_definition", "application_definition_id", $appDefId,
				$data["properties"]);
		}

		foreach($data["mod_list_item_definition"] as $id=>$fields) {
			$pids = $fields["LIST_ITEM_PARENT_TYPE_IDS"];
			unset($fields["LIST_ITEM_PARENT_TYPE_IDS"]);
			self::delete($context,
				"parent_list_item_definitions", "list_item_definition_id", $id);
			foreach ($pids as $pid) {
				self::insert($context, "parent_list_item_definitions", array(
					"list_item_definition_id" => $id,
					"parent_list_item_definition_id" => $pid
				));
			}
			self::update($context,
				"list_item_definition", "list_item_definition_id", $id, $fields);
		}
		foreach($data["mod_list_item_property_definition"] as $id=>$fields) {
			self::update($context,
				"list_item_property_definition", "list_item_definition_id", $id, $fields);
		}

		foreach($data["del_list_item_definition"] as $id=>$dummmy) {
			self::delete($context,
				"parent_list_item_definitions", "list_item_definition_id", $id);
			self::delete($context,
				"parent_list_item_definitions", "parent_list_item_definition_id", $id);
			self::delete($context,
				"list_item_definition", "list_item_definition_id", $id);
		}
		foreach($data["del_list_item_property_definition"] as $id=>$dummmy) {
			self::delete($context,
				"list_item_property_definition", "list_item_definition_id", $id);
		}

		foreach($data["add_list_item_definition"] as $fields) {
			$pids = $fields["LIST_ITEM_PARENT_TYPE_IDS"];
			unset($fields["LIST_ITEM_PARENT_TYPE_IDS"]);
			self::insert($context, "list_item_definition", $fields);
			foreach ($pids as $pid) {
				self::insert($context, "parent_list_item_definitions", array(
					"list_item_definition_id" => $fields["list_item_definition_id"],
					"parent_list_item_definition_id" => $pid
				));
			}
		}
		foreach($data["add_list_item_property_definition"] as $fields) {
			self::insert($context, "list_item_property_definition", $fields);
		}

	}

	/**
	 * Delete application and list definition data from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $appDefId The application definition id for which to delete
	 *    the data.
	 * @param array $appItemDefIds A set of list item definitions ids for
	 *    which to delete the data.
	 */
	public static function deleteApplicationDefinition(
			\Scrivo\Context $context, $appDefId, $appItemDefIds) {
		self::delete($context, "list_item_property_definition", "application_definition_id", $appDefId);
		self::delete($context, "list_item_definition", "application_definition_id", $appDefId);
		foreach ($appItemDefIds as $id) {
			 self::delete($context,
				"parent_list_item_definitions", "list_item_definition_id", $id);
			 self::delete($context,
				"parent_list_item_definitions", "parent_list_item_definition_id", $id);
		}
		self::delete($context, "application_definition", "application_definition_id", $appDefId);
	}

}

?>