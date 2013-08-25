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
 * $Id: UserInterfaceLanguage.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\UserInterfaceLanguage class.
 */

namespace Scrivo;

/**
 * The user interface languate class is a simple class to list the current
 * implemented language sets for the Scrivo user interface.
 *
 * @property \Scrivo\String $description A description for the language.
 * @property \Scrivo\String $isoCode The language ISO code.
 */
class UserInterfaceLanguage {

	/**
	 * The language ISO code.
	 * @var \Scrivo\String
	 */
	private $isoCode = null;

	/**
	 * A description for the language.
	 * @var \Scrivo\String
	 */
	private $description = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty user interface language object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
			$this->isoCode = new \Scrivo\String();
			$this->description = new \Scrivo\String();

			$this->context = $context;
		}
	}

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $name The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($name) {
		switch($name) {
			case "isoCode": return $this->isoCode;
			case "description": return $this->description;
		}
		throw new \Scrivo\SystemException("No such get-property '$name'.");
	}

	/**
	 * Implementation of the writable properties using the PHP magic
	 * method __set().
	 *
	 * @param string $name The name of the property to set.
	 * @param mixed $value The value of the property to set.
	 */
	public function __set($name, $value) {
		switch($name) {
			case "isoCode": $this->setIsoCode($value); return;
			case "description": $this->setDescription($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}


	/**
	 * Convenience method to set the fields of a user interface language object
	 * from an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->isoCode = new \Scrivo\String($rd["iso_code"]);
		$this->description = new \Scrivo\String($rd["description"]);

		$this->context = $context;
	}

	/**
	 * Set The language ISO code.
	 *
	 * @param \Scrivo\String $isoCode The language ISO code.
	 */
	private function setIsoCode(\Scrivo\String $isoCode) {
		$this->isoCode = $isoCode;
	}

	/**
	 * Set A description for the language.
	 *
	 * @param \Scrivo\String $description A description for the language.
	 */
	private function setDescription(\Scrivo\String $description) {
		$this->description = $description;
	}

	/**
	 * Check if this user interface language object can be inserted into the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new user interface language object data into the database.
	 *
	 * First it is checked if the data of this user interface language object
	 * can be inserted into the database, then the data is inserted into the
	 * database. If no id was set a new object id is generated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			$sth = $this->context->connection->prepare(
				"INSERT INTO ui_lang (
					instance_id, iso_code, description
				) VALUES (
					:instId, :isoCode, :description
				)");

			$this->context->connection->bindInstance($sth);

			$sth->bindValue(":isoCode", $this->isoCode, \PDO::PARAM_STR);
			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this user interface language object can be updated in the
	 * database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update existing user interface language object data in the database.
	 *
	 * First it is checked if the data of this user interface language object
	 * can be updated in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE ui_lang SET
					description = :description
				WHERE instance_id = :instId AND iso_code = :isoCode");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":isoCode", $this->isoCode, \PDO::PARAM_INT);

			$sth->bindValue(
				":description", $this->description, \PDO::PARAM_STR);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of user interface language object data does not
	 * violate any business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param \Scrivo\String $isoCode The object id of the user interface
	 *    language to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(
			\Scrivo\Context $context, \Scrivo\String $isoCode) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing user interface language data from the database.
	 *
	 * First it is is checked if it's possible to delete user interface
	 * language data, then the user interface language data including its
	 * dependecies is deleted from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param \Scrivo\String $isoCode $isoCode The object id of the user
	 *   interface language to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the user interface language data.
	 */
	public static function delete(
			\Scrivo\Context $context, \Scrivo\String $isoCode) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null, null));
		try {
			self::validateDelete($context, $isoCode);

			$sth = $context->connection->prepare(
				"DELETE FROM ui_lang
				WHERE instance_id = :instId AND iso_code = :isoCode");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":isoCode", $isoCode, \PDO::PARAM_INT);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Fetch a user interface language object from the database using its
	 * object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param \Scrivo\String $isoCode The ISO code of the language to select.
	 *
	 * @return \Scrivo\UserInterfaceLanguage The requested user interface
	 *    language object.
	 */
	public static function fetch(
			\Scrivo\Context $context, \Scrivo\String $isoCode) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null, null));
		try {
			$sth = $context->connection->prepare(
			"SELECT iso_code, description
			FROM ui_lang
			WHERE instance_id = :instId AND iso_code = :isoCode");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":isoCode", $isoCode, \PDO::PARAM_INT);

			$sth->execute();

			$rd = $sth->fetch(\PDO::FETCH_ASSOC);

			if ($sth->rowCount() != 1) {
				throw new \Scrivo\SystemException(
					"Failed to load user interface language");
			}

			$userInterfaceLanguage = new \Scrivo\UserInterfaceLanguage();
			$userInterfaceLanguage->setFields($context, $rd);

			return $userInterfaceLanguage;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select user interface languages from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 *
	 * @return \Scrivo\UserInterfaceLanguage[isoCode] An array containing the
	 *    selected user interface languages.
	 */
	public static function select(\Scrivo\Context $context) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {
			$sth = $context->connection->prepare(
				"SELECT iso_code, description
				FROM ui_lang
				WHERE instance_id = :instId");

			$context->connection->bindInstance($sth);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new UserInterfaceLanguage();
				$li->setFields($context, $rd);

				$res[(string)$li->isoCode] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>