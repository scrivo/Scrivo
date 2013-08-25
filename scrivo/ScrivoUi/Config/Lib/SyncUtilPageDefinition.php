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
 * $Id: SyncUtilPageDefinition.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\Config\Lib\SyncSyncUtilsPageDefinition
 * action class.
 */

namespace ScrivoUi\Config\Lib;

/**
 * private utility functions for use in config module
 * NB: do not use outside this scope
 */
class SyncUtilPageDefinition extends SyncUtil {

	/**
	 * Create a data structure containing all the page definition data.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @return array An (nested) array that contains the page definition
	 *    data in this context.
	 */
	public static function dumpConfig($context) {

		$sth1 = $context->connection->prepare(
			"SELECT * FROM page_definition WHERE instance_id = :instId");
		$context->connection->bindInstance($sth1);
		$sth1->execute();

		$sth2 = $context->connection->prepare(
			"SELECT * FROM page_definition_tab WHERE instance_id = :instId");
		$context->connection->bindInstance($sth2);
		$sth2->execute();

		$sth3 = $context->connection->prepare(
			"SELECT * FROM page_property_definition WHERE instance_id = :instId");
		$context->connection->bindInstance($sth3);
		$sth3->execute();

		$sth4 = $context->connection->prepare(
			"SELECT * FROM page_definition_hints WHERE instance_id = :instId");
		$context->connection->bindInstance($sth4);
		$sth4->execute();

		$res = array();
		while ($rd1 = $sth1->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd1["instance_id"]);
			$rd1["PROPERTIES"] = array();
			$rd1["ELEMENTS"] = array();
			$rd1["CAN_BE_CHILD_OF"] = array();
			$res[$rd1["page_definition_id"]] = $rd1;
		}

		while ($rd2 = $sth2->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd2["instance_id"]);
			if (isset($res[$rd2["page_definition_id"]])) {
				$res[$rd2["page_definition_id"]]["ELEMENTS"]
					[$rd2["page_definition_tab_id"]] = $rd2;
			}
		}
		while ($rd3 = $sth3->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd3["instance_id"]);
			if (isset($res[$rd3["page_definition_id"]])) {
				$res[$rd3["page_definition_id"]]["PROPERTIES"]
					[$rd3["page_property_definition_id"]] = $rd3;
			}
		}

		while ($rd4 = $sth4->fetch(\PDO::FETCH_ASSOC)) {
			unset($rd4["instance_id"]);
			if (isset($res[$rd4["page_definition_id"]])) {
				$res[$rd4["page_definition_id"]]["CAN_BE_CHILD_OF"]
					[$rd4["parent_page_definition_id"]] = $rd4["max_no_of_children"];
			}
		}

		return $res;
	}

	/**
	 * Insert page definition data into the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $data The defintion data to insert.
	 */
	public static function insertPageDefinition(
			\Scrivo\Context $context, $data) {

		$pr = $data["PROPERTIES"];
		unset($data["PROPERTIES"]);
		$el = $data["ELEMENTS"];
		unset($data["ELEMENTS"]);
		unset($data["CAN_BE_CHILD_OF"]);

		self::insert($context, "page_definition", $data);
		foreach ($pr as $p) {
			self::insert($context, "page_property_definition", $p);
		}
		foreach ($el as $e) {
			self::insert($context, "page_definition_tab", $e);
		}
	}

	/**
	 * Update page definition data in the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $pageDefId The page definition id for which to update
	 *    the data.
	 * @param array $data The defintion data for the update.
	 */
	public static function updatePageDefinition(
			\Scrivo\Context $context, $pageDefId, $data) {

		unset($data["properties"]["CAN_BE_CHILD_OF"]);

		if (count($data["properties"])) {
			self::update($context,
				"page_definition", "page_definition_id", $pageDefId, $data["properties"]);
		}

		foreach($data["mod_page_property_definition"] as $id=>$fields) {
			self::update($context,
				"page_property_definition", "page_property_definition_id", $id, $fields);
		}
		foreach($data["mod_page_definition_tab"] as $id=>$fields) {
			self::update($context,
				"page_definition_tab", "page_definition_tab_id", $id, $fields);
		}

		foreach($data["del_page_property_definition"] as $id=>$dummmy) {
			self::delete($context,
				"page_property_definition", "page_property_definition_id", $id);
		}
		foreach($data["del_page_definition_tab"] as $id=>$dummmy) {
			self::delete($context,
				"page_property_definition", "page_definition_tab_id", $id);
		}

		foreach($data["add_page_property_definition"] as $fields) {
			self::insert($context, "page_property_definition", $fields);
		}
		foreach($data["add_page_definition_tab"] as $fields) {
			self::insert($context, "page_definition_tab", $fields);
		}

	}

	/**
	 * Delete page definition data from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $pageDefId The page definition id for which to delete
	 *    the data.
	 */
	public static function deletePageDefinition(
			\Scrivo\Context $context, $pageDefId) {

		self::delete($context, "page_definition_hints", "page_definition_id", $pageDefId);
		self::delete(
			$context, "page_definition_hints", "parent_page_definition_id", $pageDefId);
		self::delete($context, "page_property_definition", "page_definition_id", $pageDefId);
		self::delete($context, "page_definition_tab", "page_definition_id", $pageDefId);
		self::delete($context, "page_definition", "page_definition_id", $pageDefId);
	}

}

?>