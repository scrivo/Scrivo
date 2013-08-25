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
 * $Id: PdoConnectionTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * A config mock with invalid db data.
 */
class PdoConnectionTestConfig extends \Scrivo\Config {

	function __construct() {}

	public $DB_HOST = "localhost";
	public $DB_NAME = "no_db";
	public $DB_USER = "no_db";
	public $DB_PASSWORD = "no_db";
	public $INSTANCE_ID = 1;

}

/**
 * Tests for class \Scrivo\PdoConnectionTest
 */
class PdoConnectionTest extends ScrivoDatabaseTestCase {

	/**
	 * Insert the initial test data for a test into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml"));
	}

	/**
	 * General assertions for a \Scrivo\PdoConnection.
	 */
	public function testPdoConnection() {

		$conn = self::$context->connection;

		// Test instance id member
		$instId = self::$context->config->INSTANCE_ID;
		$this->assertEquals($instId, $conn->instanceId);

		// Test id generation
		$idA = $conn->generateId();
		$idB = $conn->generateId();
		$this->assertGreaterThan($idA, $idB);

		// Test binding of the instace id
		$sth = $conn->prepare("SELECT :instId as test");
		$conn->bindInstance($sth);
		$sth->execute();
		$rd = $sth->fetch(\PDO::FETCH_ASSOC);
		$this->assertEquals($rd["test"], "$instId");

	}

	/**
	 * Test \Scrivo\PDOException thrown by constructor.
	 * @expectedException \PDOException
	 */
	public function testPdoException() {

		$cfg = new PdoConnectionTestConfig();

		$conn = new \Scrivo\PdoConnection($cfg);

	}

	/**
	 * Test the magic __get method.
	 * @expectedException \Exception
	 */
	public function testMagicMethodGet() {

		$error = self::$context->connection->hatseflatse;

	}

	/**
	 * Test database failure
	 * @expectedException \PDOException
	 */
	public function testDbFailure() {

		$this->ctxDbFailureStub()->connection->generateId();

	}

}

?>