<?php
/* Copyright (c) 2012, Geert Bergman (geert@scrivo.nl)
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
 * $Id: ScrivoDatabaseTestCase.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \ScrivoDatabaseTestCase class.
 */

require_once("../scrivo/Scrivo/Autoloader.php");
spl_autoload_register("\\Scrivo\\Autoloader::load");

require_once "PHPUnit/Extensions/Database/Operation/Truncate.php";
require_once "PHPUnit/Extensions/Database/TestCase.php";
require_once "PHPUnit/Extensions/Database/DataSet/YamlDataSet.php";

class PdoMock extends \Scrivo\PdoConnection {
	public function __construct() {}
}

/**
 * Our own implementation of the PHPUnit truncate operation. You can't truncate
 * in Scrivo tables, we'll need to remove all records with a specific instance
 * instead.
 */
class TruncateOperation
		extends \PHPUnit_Extensions_Database_Operation_Truncate {

	public function execute(
			\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection,
			\PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet) {

		foreach ($dataSet->getTableNames() as $table) {
			$connection->getConnection()->query(
				"DELETE FROM $table where instance_id = ".
				$connection->getConnection()->instanceId);
		}

	}
}

/**
 * When inserting test data we'll need to use the instance id as given in
 * the Scrivo configuration file. When inserting the test data the value of
 * the column "instance_id" should be overwritten with the instance id as defined
 * in the Scrivo config file.
 */
class InsertOperation
		extends \PHPUnit_Extensions_Database_Operation_Insert {

	/**
	 * The instance id as defined in the Scrivo config file.
	 */
	private $instanceId = 0;

	/**
	 * Just store the instance id and call the parent method.
	 */
	protected function buildOperationQuery(
			PHPUnit_Extensions_Database_DataSet_ITableMetaData
				$databaseTableMetaData,
			PHPUnit_Extensions_Database_DataSet_ITable $table,
			PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection) {
		$this->instanceId = $connection->getConnection()->instanceId;
		return parent::buildOperationQuery(
			$databaseTableMetaData, $table, $connection);
	}

	/**
	 * Return the field values as given in the yaml files except if the column
	 * is named "instance_id", then return the instance id member.
	 */
	protected function buildOperationArguments(
			PHPUnit_Extensions_Database_DataSet_ITableMetaData
				$databaseTableMetaData,
			PHPUnit_Extensions_Database_DataSet_ITable $table, $row) {
		$args = array();
		foreach ($table->getTableMetaData()->getColumns() as $columnName) {
			$args[] = $columnName === "instance_id"
				? $this->instanceId
				: $table->getValue($row, $columnName);
		}
		return $args;
	}
}

/**
 * Base class for Scrivo database test. The tests should use the information
 * from a Scrivo config file, and should take in account the database instance
 * when inserting an deleting test data.
 *
 * The test data is provide through yaml files in the testdata folder.
 * In this test data instance ids are given but will be overwritten with the
 * instance id given in the configuration file.
 *
 * It also provides a member
 */
abstract class ScrivoDatabaseTestCase
		extends PHPUnit_Extensions_Database_TestCase {

	/**
	 * Constants used in init.yml
	 */
	const PUBLIC_ROLE_ID = 1;
	const EDITOR_ROLE_ID = 2;
	const LANGUAGE_NL_ID = 85;
	const LANGUAGE_EN_ID = 25;

	/**
	 * Constants used in users_and_roles.yml
	 */
	const EDITOR_USER_ID = 40000;
	const PUBLISHER_USER_ID = 40001;
	const ADMIN_USER_ID = 40002;
	const MEMBER_USER_ID = 40003;
	const MEMBER_ROLE_ID = 50000;

	/**
	 * Constants used in page_definition.yml
	 */
	const PAGE_DEFINITION_HOME_ID = 70000;
	const PAGE_DEFINITION_FORUM_ID = 70001;

	const PAGE_DEFINITION_PROPERTY_TAB_ID = 70100;
	const PAGE_DEFINITION_PROPERTY_HTML_ID = 70600;
	const PAGE_DEFINITION_PROPERTY_CHECKBOX_ID = 70700;
	const PAGE_DEFINITION_PROPERTY_LINK_ID = 70800;

	const PAGE_DEFINITION_CONTENT_TAB_ID = 70200;
	const PAGE_DEFINITION_APLLICATION_TAB_ID = 70300;
	const PAGE_DEFINITION_INVALID_LIST_TAB_ID = 70400;

	/**
	 * Constants used in page.yml
	 */
	const PAGE_HOME_ID = 1;
	const PAGE_FORUM_ID = 200001;

	/**
	 * Constants used in asset.yml
	 */
	const folder_id = 2;
	const SUB_FOLDER_ID = 20000;

	/**
	 * Constants used in application_definition.yml
	 */
	const APPLICATION_DEFINITION_ID = 60000;

	/**
	 * Constants used in page_definition_hints.yml
	 * Note: page_definition_hints is emptied page_definition.yml
	 */
	const PAGE_DEFINITION_MENU_ID = 70501;
	const PAGE_DEFINITION_CONTENT_ID = 70502;
	const PAGE_MENU1_ID = 200501;
	const PAGE_MENU2_ID = 200502;
	const PAGE_CONTENT_ID = 200503;

	/**
	 * Constants used in list_item_definition.yml
	 */
	const LIST_ITEM_PROPERTY_DEFINITION_ID_1 = 130000;
	const LIST_ITEM_PROPERTY_DEFINITION_ID_2 = 130001;
	const LIST_ITEM_PROPERTY_DEFINITION_ID_3 = 130002;

	const LIST_ITEM_DEFINITION_SUBJECT_ID = 120000;
	const LIST_ITEM_DEFINITION_REPLY_ID = 120001;
	const LIST_ITEM_DEFINITION_MOVE_ID = 120002;

	/**
	 * Location of the test data files.
	 */
	const testDataDir = "/testdata/";

	/**
	 * Data that needs to be instantiated once.
	 */
	protected static $pdo = null;
	protected static $cfg = null;

	/**
	 * Static reference to the Scrivo context used in these tests.
	 */
	protected static $context;

	/**
	 * The per test instance of the database connection.
	 */
	private $conn = null;

	/**
	 * Create a Scrivo context to use in the tests.
	 */
	public static function setUpBeforeClass() {
		self::$cfg = new \Scrivo\Config(new \Scrivo\Str("test_config"));
		self::$pdo = new \Scrivo\PdoConnection(self::$cfg);
		self::$pdo->exec("DELETE FROM user WHERE instance_id = ".
			self::$pdo->getInstanceId());
		self::$pdo->exec("INSERT INTO user (instance_id, user_id, status,
			user_code, password, given_name, family_name_prefix, family_name, email_address,
			custom_data) VALUES (".(self::$pdo->getInstanceId()).
			", 2, 1, 'admin', '', '', '', '', '', '')");
		self::$context =
			new \Scrivo\Context(self::$cfg, Scrivo\User::PRIMARY_ADMIN_ID);
	}

	/**
	 * Use out own setup operations so that the instance id is taken into
	 * account.
	 */
	public function getSetUpOperation() {
		return new \PHPUnit_Extensions_Database_Operation_Composite(array(
			new TruncateOperation(false), new InsertOperation()));
	}

	/**
	 * Use \Scrivo\PdoConnection to access the database, that will use the
	 * settings from a Scrivo config file too.
	 */
	final public function getConnection() {
		if ($this->conn === null) {
			$this->conn = $this->createDefaultDBConnection(
				self::$pdo, self::$cfg->DB_NAME);
		}
		return $this->conn;
	}

	/**
	 * Convenience method to add several yaml test data files at once:
	 *
	 * function getDataSet() {
	 *	  return $this->addDataSets(array("init.yml", "document.yml"));
	 * }
	 *
	 * @param string[] File names of yaml test data files.
	 * @return PHPUnit_Extensions_Database_DataSet_YamlDataSet The dataset.
	 */
	protected function addDataSets($sets) {
		date_default_timezone_set("Europe/Amsterdam");
		$ds = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			dirname(__FILE__).self::testDataDir.array_shift($sets)
		);
		$sets = array_reverse($sets);
		foreach($sets as $set) {
			$ds->addYamlFile(dirname(__FILE__).self::testDataDir.$set);
		}
		return $ds;
	}

	private $ctxGetCtx;
	public function ctxGet($name) {
		switch ($name) {
			case "config": return $this->ctxGetCtx->config;
			case "connection":
				$dbc = $this->getMock('PdoMock',
				array('prepare', 'exec'));
				$dbc->expects($this->any())->method("prepare")
						->will($this->returnCallback(
					function ($statement, $options=null) {
						throw new \PDOException("stub");
					}
				));
				$dbc->expects($this->any())->method("exec")
						->will($this->returnCallback(
					function ($statement, $options=null) {
						throw new \PDOException("stub");
					}
				));
				return $dbc;
			case "principal": return $this->ctxGetCtx->principal;
			case "labels": return $this->ctxGetCtx->labels;
			case "cache": return $this->ctxGetCtx->cache;
		}
	}

	/**
	 * @return \Scrivo\User A Scrivo user stub.
	 */
	function ctxDbFailureStub(\Scrivo\Context $context=null) {
		$this->ctxGetCtx = $context ? $context : self::$context;
		$stub = $this->getMockBuilder('\Scrivo\Context')
			->disableOriginalConstructor()
			->getMock();
		$stub->expects($this->any())->method("__get")
			->will($this->returnCallback(array($this, "ctxGet")
		));
		return $stub;
	}

}

?>