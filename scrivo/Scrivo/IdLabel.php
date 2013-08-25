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
 * $Id: IdLabel.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\IdLabel class.
 */

namespace Scrivo;

/**
 * Class to represent id-label pairs.
 *
 * Object id's can be labeled in Scrivo, these labels then can be used as
 * constants in the template code. For instance suppose that the contact page
 * in a site has id 12349 then this id can be labeled "CONTACT". When loading
 * the page the label can be used instead of the id, thus allowing for clearer
 * program code. For example:
 *
 * \Scrivo\CachedData::load($context, 12349);
 * vs
 * \Scrivo\CachedData::load($context, $context->labels->CONTACT);
 *
 * Also deletion of labeled objects is not allowed: defining a label means
 * that the object has a special function in a project, deleting is likely
 * to cause an inconsistent project state.
 *
 * You can retrieve a labelled id by referring to its label as a member of
 * of an IdLabel object. A Scrivo Context object contains an IdLabel object
 * with all labelled ids for the contexts instance.
 *
 * $cfg = new Scrivo\Config();
 * $context = new Scrivo\Context($cfg, \Scrivo\User::PRIMARY_ADMIN_ID);
 * \Scrivo\CachedData::load($context, $context->labels->CONTACT);
 *
 * You can add and delete values using the set member:
 *
 * // Set an id-label pair
 * $context->labels->set($context, 123, \Scrivo\String("CONTACT"));
 * // Update an id-label pair
 * $context->labels->set($context, 123, \Scrivo\String("CONTACT_B"));
 * // Delete an id-label pair
 * $context->labels->set($context, 123);
 */
class IdLabel implements \Iterator {

	/**
	 * Array that holds the id-label pairs.
	 * @var int[string]
	 */
	private $labels;

	/**
	 * Create an IdLabel object, the object will contain all the id-label pairs
	 * that are defined for the given instance.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	protected function __construct(Context $context) {
		try {

			$sth = $context->connection->prepare(
				"SELECT id, label FROM id_label WHERE instance_id = :instId");
			$context->connection->bindInstance($sth);
			$sth->execute();
			$this->labels = array();
			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$this->labels[$rd["label"]] = intval($rd["id"]);
			}

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Select the id label pairs for a given context.
	 *
	 * @param \Scrivo\Context $context A Scrivo context.
	 */
	public static function select(\Scrivo\Context $context) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		// Try to retieve form cache
		$obj = null;
		if (isset($context->cache["LABELS"])) {
			// Set the page from cache and set the context.
			$obj = $context->cache["LABELS"];
		} else {
			// Load the page and set it in the cache.
			$obj = new \Scrivo\IdLabel($context);
			$context->cache["LABELS"] = $obj;
		}
		return $obj;
	}

	/**
	 * Test if a particular label was set.
	 *
	 * @param string $label The label to test.
	 *
	 * @return boolean True if the label was set, false if not.
	 */
	public function __isset($label) {
		return isset($this->labels[$label]);
	}

	/**
	 * Accessor method for id-label members.
	 *
	 * @param string $label The label to retrieve the object id for.
	 *
	 * @return int The object id for the label.
	 */
	public function __get($label) {
		if (!$this->__isset($label)) {
			throw new \Scrivo\SystemException("Invalid id-label '$label'");
		}
		return $this->labels[$label];
	}

	/**
	 * Set an id-label pair.
	 *
	 * Use this method to insert/update/delete an id-label pair in the database.
	 * To delete a value just ommit the $label parameter.
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 * @param int $id An object id.
	 * @param \Scrivo\String $label A label for the object id.
	 *
	 * @throws ApplicationException If the given label is not unique.
	 */
	static function set(
			\Scrivo\Context $context, $id, \Scrivo\String $label=null) {
		try {
			$context->checkPermission(\Scrivo\AccessController::WRITE_ACCESS);

			$l = "";

			// Check the label for uniqueness, throw an app exception if not.
			if ($label && $label != "") {
				$l = preg_replace(array("/[^ _A-Z0-9]/u", "/ /u"),
						array("", "_"), $label->toUpperCase());

				$sth = $context->connection->prepare(
					"SELECT COUNT(*) FROM id_label
					WHERE instance_id = :instId AND id <> :id AND label = :label");
				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);
				$sth->bindValue(":label", $l, \PDO::PARAM_INT);
				$sth->execute();
				if ($sth->fetchColumn(0) > 0) {
					throw new \Scrivo\ApplicationException("Label not unique",
						\Scrivo\StatusCodes::LABEL_NOT_UNIQUE);
				}
			}

			// Delete the label for this id.
			$sth = $context->connection->prepare(
				"DELETE FROM id_label WHERE instance_id = :instId AND id = :id");
			$context->connection->bindInstance($sth);
			$sth->bindValue(":id", $id, \PDO::PARAM_INT);
			$sth->execute();

			// Insert a the new label for the id if given.
			if ($l != "") {
				$sth = $context->connection->prepare(
					"INSERT INTO id_label (instance_id, id, label)
					VALUES (:instId, :id, :label)");
				$context->connection->bindInstance($sth);
				$sth->bindValue(":id", $id, \PDO::PARAM_INT);
				$sth->bindValue(":label", $l, \PDO::PARAM_STR);
				$sth->execute();
			}

			// Clear cache.
			unset($context->cache["LABELS"]);

		} catch(\PDOException $e) {
			throw new \Scrivo\ResourceException($e);
		}
	}

	/**
	 * Rewind the labels array so iterating will start at the beginning again.
	 */
	function rewind() {
		reset($this->labels);
	}

	/**
	 * Get the current label when iterating.
	 */
	function current() {
		return current($this->labels);
	}

	/**
	 * Get the key of the current label when iterating.
	 */
	function key() {
		return key($this->labels);
	}

	/**
	 * Get the next label when iterating.
	 */
	function next() {
		next($this->labels);
	}

	/**
	 * Check if the current key is valid.
	 */
	function valid() {
		return key($this->labels) ? true : false;
	}

}

?>