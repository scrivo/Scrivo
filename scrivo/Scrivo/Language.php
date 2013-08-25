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
 * $Id: Language.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Language class.
 */

namespace Scrivo;

/**
 * Class that represents a language.
 *
 * Pages (and possible HTML elements) can have a language attribute. For that
 * reason Scrivo maintains a table of (primary) language codes.
 *
 * To facilitate language selection a short list of languages can be compiled.
 * In the user interface language selection will be limited to those that
 * are on the short list.
 *
 * Since the primary purpose of the language class is to fill language
 * selection dialogs the full display name of the languages is stored in
 * both Dutch and English.
 *
 * TODO: This should be refactored. There is no need for a seperate class:
 * ISO language codes will do fully here. An other undesirable feature is
 * the storage of i18n data (the full language names) in the database.
 *
 * @property-read int $id The language id (DB key).
 * @property \Scrivo\String $family The language family (Germanic, Slavic).
 * @property \Scrivo\String $isoCode The language ISO code.
 * @property \Scrivo\String $nameEn The language name in English.
 * @property \Scrivo\String $nameNl The language name in Dutch.
 * @property boolean $shortList Whether a language is on the short list or not.
 */
class Language {

	/**
	 * The language id (DB key).
	 * @var int
	 */
	private $id = 0;

	/**
	 * Whether a language is on the short list or not.
	 * @var boolean
	 */
	private $shortList = false;

	/**
	 * The language ISO code.
	 * @var \Scrivo\String
	 */
	private $isoCode = null;

	/**
	 * The language family (Germanic, Slavic).
	 * @var \Scrivo\String
	 */
	private $family = null;

	/**
	 * The language name in English.
	 * @var \Scrivo\String
	 */
	private $nameEn = null;

	/**
	 * The language name in Dutch.
	 * @var \Scrivo\String
	 */
	private $nameNl = null;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty language object.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));

		if ($context) {
			$this->isoCode = new\Scrivo\String();
			$this->family = new\Scrivo\String();
			$this->nameEn = new\Scrivo\String();
			$this->nameNl = new\Scrivo\String();

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
			case "id": return $this->id;
			case "shortList": return $this->shortList;
			case "isoCode": return $this->isoCode;
			case "family": return $this->family;
			case "nameEn": return $this->nameEn;
			case "nameNl": return $this->nameNl;
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
			case "shortList": $this->setShortList($value); return;
			case "isoCode": $this->setIsoCode($value); return;
			case "family": $this->setFamily($value); return;
			case "nameEn": $this->setNameEn($value); return;
			case "nameNl": $this->setNameNl($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a language object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->id = intval($rd["language_id"]);
		$this->shortList = intval($rd["short_list"]) == 1 ? true : false;
		$this->isoCode = new \Scrivo\String($rd["iso_code"]);
		$this->family = new \Scrivo\String($rd["family"]);
		$this->nameEn = new \Scrivo\String($rd["name_en"]);
		$this->nameNl = new \Scrivo\String($rd["name_nl"]);

		$this->context = $context;
	}

	/**
	 * Set the value to indicate if a language is on the short list or not.
	 *
	 * @param boolean $shortList Whether a language is on the short list or not.
	 */
	private function setShortList($shortList) {
		\Scrivo\ArgumentCheck::assert(
			$shortList, \Scrivo\ArgumentCheck::TYPE_BOOLEAN);
		$this->shortList = $shortList;
	}

	/**
	 * Set the language ISO code.
	 *
	 * @param \Scrivo\String $isoCode The language ISO code.
	 */
	private function setIsoCode(\Scrivo\String $isoCode) {
		$this->isoCode = $isoCode;
	}

	/**
	 * Set the language family (Germanic, Slavic).
	 *
	 * @param \Scrivo\String $family The language family (Germanic, Slavic).
	 */
	private function setFamily(\Scrivo\String $family) {
		$this->family = $family;
	}

	/**
	 * Set the language name in English.
	 *
	 * @param \Scrivo\String $nameEn The language name in English.
	 */
	private function setNameEn(\Scrivo\String $nameEn) {
		$this->nameEn = $nameEn;
	}

	/**
	 * Set the language name in Dutch.
	 *
	 * @param \Scrivo\String $nameNl The language name in Dutch.
	 */
	private function setNameNl(\Scrivo\String $nameNl) {
		$this->nameNl = $nameNl;
	}

	/**
	 * Check if this language object can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new language object data into the database.
	 *
	 * First it is checked if the data of this language object can be inserted
	 * into the database, then the data is inserted into the database. If no id
	 * was set a new object id is generated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			if (!$this->id) {
				$this->id = $this->context->connection->generateId();
			}

			$sth = $this->context->connection->prepare(
				"INSERT INTO language (
					instance_id, language_id, short_list, iso_code, family,
					name_en, name_nl
				) VALUES (
					:instId, :id, :shortList, :isoCode, :family,
					:nameEn, :nameNl
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);
			$sth->bindValue(":shortList", $this->shortList, \PDO::PARAM_INT);
			$sth->bindValue(":isoCode", $this->isoCode, \PDO::PARAM_STR);
			$sth->bindValue(":family", $this->family, \PDO::PARAM_STR);
			$sth->bindValue(":nameEn", $this->nameEn, \PDO::PARAM_STR);
			$sth->bindValue(":nameNl", $this->nameNl, \PDO::PARAM_STR);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if this language object can be updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	*/
	private function validateUpdate() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Update an existing language object data in the database.
	 *
	 * First it is checked if the data of this language object can be updated
	 * in the database, then the data is updated in the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function update() {
		try {
			$this->validateUpdate();

			$sth = $this->context->connection->prepare(
				"UPDATE language SET
					short_list = :shortList, iso_code = :isoCode, family = :family,
					name_en = :nameEn, name_nl = :nameNl
				WHERE instance_id = :instId AND language_id = :id");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":id", $this->id, \PDO::PARAM_INT);

			$sth->bindValue(":shortList", $this->shortList, \PDO::PARAM_INT);
			$sth->bindValue(":isoCode", $this->isoCode, \PDO::PARAM_STR);
			$sth->bindValue(":family", $this->family, \PDO::PARAM_STR);
			$sth->bindValue(":nameEn", $this->nameEn, \PDO::PARAM_STR);
			$sth->bindValue(":nameNl", $this->nameNl, \PDO::PARAM_STR);

			$sth->execute();

			unset($this->context->cache["L".$this->id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of language object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the language to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(\Scrivo\Context $context, $id) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing language data from the database.
	 *
	 * First it is is checked if it's possible to delete language data,
	 * then the language data including its dependecies is deleted from
	 * the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the language to select.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	public static function delete(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $id);

			$sth = $context->connection->prepare(
				"DELETE FROM language
				WHERE instance_id = :instId AND language_id = :id");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);

			$sth->execute();

			unset($context->cache["L".$id]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Fetch a language object by from the database using the object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $id The object id of the language to select.
	 *
	 * @return \Scrivo\Language The requested language object.
	 */
	public static function fetch(\Scrivo\Context $context, $id) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			// Try to retieve the language from the cache ...
			if (isset($context->cache["L".$id])) {
				// ... get it from the cache and set the context.
				$language = $context->cache["L".$id];
				$language->context = $context;
			} else {
				// ... else retrieve it and set it in the cache.
				$sth = $context->connection->prepare(
					"SELECT language_id, short_list, iso_code, family, name_en, name_nl
					FROM language
					WHERE instance_id = :instId AND language_id = :id");

				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);

				$sth->execute();

				if ($sth->rowCount() != 1) {
					throw new
						\Scrivo\SystemException("Failed to load language");
				}

				$language = new \Scrivo\Language();
				$language->setFields($context, $sth->fetch(\PDO::FETCH_ASSOC));

				$context->cache["L".$id] = $language;
			}

			return $language;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select languages from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param boolean $shortList A Scrivo context.
	 *
	 * @return Language[id] An array containing the selected languages.
	 */
	public static function select(\Scrivo\Context $context, $shortList=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_BOOLEAN)
		), 1);
		try {
			$sth = $context->connection->prepare(
				"SELECT language_id, short_list, iso_code, family, name_en, name_nl
				FROM language WHERE instance_id = :instId ".
				($shortList ? " AND short_list=1" : ""). " ORDER BY iso_code");

			$context->connection->bindInstance($sth);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new Language();
				$li->setFields($context, $rd);

				$res[$li->id] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>