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
 * $Id: PdoConnection.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\PdoConnection class.
 */

namespace Scrivo;

/**
 * Class to manage the connection to the Scrivo database.
 *
 * This is simply a direct descendent of the \PDO class with a modified
 * constructor that uses a Scrivo configuration object to open the connection.
 * It also assures that the connection is configured correctly (uses exceptions
 * and UTF-8).
 *
 * It is possible to store data of several instances (sites) in a single
 * database. The instance data is identified by an instance id which is also
 * defined in the configuration file and present a property of this class.
 *
 * Furthermore, Scrivo uses object ids instead of auto-numbering. Ids within
 * a specific instance need to be unique. To achieve this this class also
 * deals with the generation of unique ids.
 *
 *
 */
class PdoConnection extends \PDO {

	/**
	 * The database instance id.
	 * @var int
	 */
	private $instId;

	/**
	 * Construct a scrivo database connection using the setting in a Scrivo
	 * config file. The following settings are used:
	 *
	 * * DB_HOST
	 * * DB_NAME
	 * * DB_USER
	 * * DB_PASSWORD
	 * * INSTANCE_ID
	 *
	 * @param Config $config A Scrivo config object that contains the database
	 *   settings.
	 */
	public function __construct(Config $config) {

		parent::__construct(
			"mysql:host={$config->DB_HOST};dbname={$config->DB_NAME}",
			$config->DB_USER, $config->DB_PASSWORD,
			array(\PDO::ATTR_PERSISTENT => true));
		$this->setAttribute(
			\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->exec("SET NAMES utf8");

		$this->instId = $config->INSTANCE_ID;

	}

	/**
	 * Get the database instance id.
	 *
	 * @return int The database instance id.
	 */
	public function getInstanceId() {
		return $this->instId;
	}

	/**
	 * Generate a new object id.
	 *
	 * For the generation of an object id in MySQL we use a table with one
	 * autoincrement column.
	 *
	 * TODO: setup a test to see how this holds in concurrency situations
	 *  and document it (the behavoir not the test).
	 *
	 * @return int A new object id.
	 */
	public function generateId() {

		$this->exec("LOCK TABLES seq WRITE");
		$this->exec("INSERT INTO seq VALUES ()");
		$sth = $this->prepare("SELECT LAST_INSERT_ID()");
		$sth->execute();
		$this->exec("UNLOCK TABLES");
		$newId = $sth->fetchColumn();

		$sth = $this->prepare("DELETE FROM seq WHERE seq < :newID");
		$sth->bindValue(":newID", $newId, \PDO::PARAM_INT);
		$sth->execute();

		return intval($newId);
	}

	/**
	 * Convenience method for setting the instance variable in an prepared
	 * statment. This variable is usually named ":instId", but you can use an
	 * alternative name.
	 *
	 * @param \PDOStatement $sth The statement for which to set the instance id.
	 * @param string $label An optional alternative label for the instance id
	 *     variable.
	 */
	public function bindInstance(\PDOStatement $sth, $label=":instId") {
		$sth->bindValue($label, $this->instId, \PDO::PARAM_INT);
	}

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $field The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($field) {
		if ($field == "instanceId") {
			return $this->getInstanceId();
		} else {
			throw new \Scrivo\SystemException("Property $field not found");
		}
	}

	/**
	 * Overloaded version of PDO::prepare, just to be able to do some query
	 * logging.
	 *
	 * @param string $statement An SQL statement.
	 * @param array $options Driver options.
	 *
	 * @return \PDOStatement A PDO statement.
	 */
	public function prepare($statement, $options=array()) {
		//error_log("Prepare: $statement");
		return parent::prepare($statement, $options);
	}

}

?>