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
 * $Id: ModifiedTouched.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\ModifiedTouched class.
 */

namespace Scrivo;

/**
 * The ModifiedTouched class models the relation of pages that need to be
 * touched (their modification date updated) when some other pages is updated.
 *
 * It is very well possible to create pages that change due to the modification
 * of another page. Suppose you have a selection of the last three news items
 * of a new page. Adding news obviously changes the modification date of the
 * new page which is important for indexing and caching, but the change on the
 * home page will go unnoticed for the indexing or caching functionality.
 *
 * @property-read int $typeModified The type of the subject that is being
 *    modified.
 * @property-read int $typeTouched The type of the subject that needs to be
 *    touched when the other subject.
 * @property int $idModified The id of the subject that is being modified.
 * @property int $idTouched The id of the subject that needs to be touched
 *    when the other subject is modified
 */
class ModifiedTouched {

	/**
	 * Constant to the denote that the subject id is a document id.
	 */
	const TYPE_DOCUMENT_ID = 1;

	/**
	 * Constant to the denote that the subject id is a template id.
	 */
	const TYPE_TEMPLATE_ID = 2;

	/**
	 * The id of the subject that is being modified.
	 * @var int
	 */
	private $idModified = 0;

	/**
	 * The type of the subject that is being modified.
	 * @var int
	 */
	private $typeModified = self::TYPE_DOCUMENT_ID;

	/**
	 * The id of the subject that needs to be touched when the other subject
	 * is modified.
	 * @var int
	 */
	private $idTouched = 0;

	/**
	 * The type of the subject that needs to be touched when the other subject
	 * is modified.
	 * @var int
	 */
	private $typeTouched = self::TYPE_TEMPLATE_ID;

	/**
	 * A Scrivo context.
	 * @var \Scrivo\Context
	 */
	private $context = null;

	/**
	 * Create an empty modified-touched relation entry or select a
	 * modified-touched relation entry from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public function __construct(\Scrivo\Context $context=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		if ($context) {
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
			case "idModified": return $this->idModified;
			case "typeModified": return $this->typeModified;
			case "idTouched": return $this->idTouched;
			case "typeTouched": return $this->typeTouched;
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
			case "idModified": $this->setIdModified($value); return;
			case "idTouched": $this->setIdTouched($value); return;
		}
		throw new \Scrivo\SystemException("No such set-property '$name'.");
	}

	/**
	 * Convenience method to set the fields of a modified touched object from
	 * an array (result set row).
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param array $rd An array containing the field data using the database
	 *    field names as keys.
	 */
	private function setFields(\Scrivo\Context $context, array $rd) {

		$this->idModified = intval($rd["modified_id"]);
		$this->typeModified = intval($rd["date_modified_touched"]);
		$this->idTouched = intval($rd["touched_id"]);
		$this->typeTouched = intval($rd["touched_type"]);

		$this->context = $context;
	}

	/**
	 * Set The id of the subject that is being modified.
	 *
	 * @param int $idModified The id of the subject that is being modified.
	 */
	private function setIdModified($idModified) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->idModified = $idModified;
	}

	/**
	 * Set The id of the subject that needs to be touched when the other
	 * subject is modified.
	 *
	 * @param int $idTouched The id of the subject that needs to be touched
	 *    when the other subject is modified.
	 */
	private function setIdTouched($idTouched) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->idTouched = $idTouched;
	}

	/**
	 * Check if this modified touched object can be inserted into the database.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	private function validateInsert() {
		$this->context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Insert new modified touched object data into the database.
	 *
	 * First it is checked if the data of this modified touched object can be
	 * inserted into the database, then the data is inserted into the database.
	 * If no id was set a new object id is generated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   one or more of the fields contain invalid data.
	 */
	public function insert() {
		try {
			$this->validateInsert();

			$sth = $this->context->connection->prepare(
				"INSERT INTO modified_touched (
					instance_id, modified_id, date_modified_touched,
					touched_id, touched_type
				) VALUES (
					:instId, :idModified, :typeModified,
					:idTouched, :typeTouched
				)");

			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":idModified", $this->idModified, \PDO::PARAM_INT);
			$sth->bindValue(
				":typeModified", $this->typeModified, \PDO::PARAM_INT);
			$sth->bindValue(":idTouched", $this->idTouched, \PDO::PARAM_INT);
			$sth->bindValue(
				":typeTouched", $this->typeTouched, \PDO::PARAM_INT);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Check if deletion of modified touched object data does not violate any
	 * business rules.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $idModified The object id the a subject that is modified.
	 * @param int $idTouched The object id of a subject that needs  to be
	 *    touched when the other subject is updated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the language data.
	 */
	private static function validateDelete(
			\Scrivo\Context $context, $idModified, $idTouched) {
		$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);
	}

	/**
	 * Delete existing modified touched data from the database.
	 *
	 * First it is is checked if it's possible to delete modified touched data,
	 * then the modified touched data including its dependencies is deleted
	 * from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $idModified The object id the a subject that is modified.
	 * @param int $idTouched The object id of a subject that needs  to be
	 *    touched when the other subject is updated.
	 *
	 * @throws \Scrivo\ApplicationException If the data is not accessible or
	 *   if it is not possible to delete the modified touched data.
	 */
	public static function delete(
			\Scrivo\Context $context, $idModified, $idTouched) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			self::validateDelete($context, $idModified, $idTouched);

			$sth = $context->connection->prepare(
				"DELETE FROM modified_touched
				WHERE instance_id = :instId AND modified_id = :idModified
				AND touched_id = :idTouched");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":idModified", $idModified, \PDO::PARAM_INT);
			$sth->bindValue(":idTouched", $idTouched, \PDO::PARAM_INT);

			$sth->execute();

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Fetch a modified touched object from the database using its object id.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 * @param int $idModified The object id the a subject that is modified.
	 * @param int $idTouched The object id of a subject that needs  to be
	 *    touched when the other subject is updated.
	 *
	 * @return \Scrivo\ModifiedTouched The requested modified touched object.
	 */
	public static function fetch(
			\Scrivo\Context $context, $idModified, $idTouched) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));
		try {
			$sth = $context->connection->prepare(
				"SELECT modified_id, date_modified_touched, touched_id, touched_type
				FROM modified_touched
				WHERE instance_id = :instId AND modified_id = :idModified
				AND touched_id = :idTouched");

			$context->connection->bindInstance($sth);
			$sth->bindValue(":idModified", $idModified, \PDO::PARAM_INT);
			$sth->bindValue(":idTouched", $idTouched, \PDO::PARAM_INT);

			$sth->execute();

			if ($sth->rowCount() != 1) {
				throw new \Scrivo\SystemException(
					"Failed to load modified-touched relation entry");
			}

			$modifiedTouched = new \Scrivo\ModifiedTouched();
			$modifiedTouched->setFields($context, $sth->fetch(\PDO::FETCH_ASSOC));

			return $modifiedTouched;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select modified-touched relation entries from the database.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 *
	 * @return ModifiedTouched[] An array containing the selected
	 *    modified-touched relation entrys.
	 */
	public static function select(\Scrivo\Context $context) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null));
		try {
			$sth = $context->connection->prepare(
				"SELECT modified_id, date_modified_touched, touched_id, touched_type
				FROM modified_touched
				WHERE instance_id = :instId");

			$context->connection->bindInstance($sth);

			$sth->execute();

			$res = array();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {

				$li = new ModifiedTouched();
				$li->setFields($context, $rd);

				$res[] = $li;
			}

			return $res;

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

}

?>