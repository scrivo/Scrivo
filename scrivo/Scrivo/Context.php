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
 * $Id: Context.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\Context class.
 */

namespace Scrivo;

/**
 * Class that defines a Scrivo context.
 *
 * Working with Scrivo data requires a number of resources to be present. Of
 * course there should be a database connection to a Scrivo database and also
 * an instance id that refers to the data if a scrivo instance in the database
 * (a Scrivo database can store data for multiple instances/websites).
 *
 * Also a principal id is required. This principal id is used to determine the
 * permissions for actions that are excecuted on behalf of the principal.
 *
 * Then there is the configuration data: the entries in the ".htscrivo" file
 * and the object id labels.
 *
 * All this information is what we call the Scrivo context. Working with Scrivo
 * data will almost always require you to setup a Scrivo context first. The
 * parameters for the database connection and the instance id are mandatory
 * entries in the Scrivo config file, a principal id will usually be retrieved
 * from a session or is set progammatically. Thus configuration data and a
 * principal id is all what is required to instantiate a Scrivo context.
 *
 * $context = new \Scrivo\Context(
 *		new \Scrivo\Config(), \Scrivo\User::PRIMARY_ADMIN_ID);
 * $homepage = new \Scrivo\Page($context, $context->labels->HOME);
 *
 * Some other usage examples:
 *
 * // Get the current (user) principal from the context.
 * $currentUser = new \Scrivo\User($context, $context->principalId);
 *
 * // Get a reference to the contexts PDO connection and instance id.
 * $conn = $context->connection;
 * $instId = $context->instanceId;
 *
 * // Get the value of a configuration entry
 * $wwwRoot = $context->config->WWW_ROOT;
 *
 * // Get an object id by its label.
 * $contactPageId = $context->labels->CONTACT;
 *
 * // Check write permission on the page labelled contact.
 * $context->checkPermission(
 *		\Scrivo\AccessController::WRITE_ACCESS, $context->labels->CONTACT);
 *
 * @property-read \Scrivo\LocalCache $cache The object cache.
 * @property-read \Scrivo\Config $config A Config object holding this site's
 *    configuration data
 * @property-read \Scrivo\PdoConnection $connection A PDO connection to a
 *    Scrivo database.
 * @property-read \Scrivo\IdLabel $labels A IdLabel object that holding this
 *    sites id-label pairs.
 * @property-read \Scrivo\User $principal The principal.
 */
class Context {

	/**
	 * A Config object holding this site's configuration data
	 * @var \Scrivo\Config
	 */
	private $config = null;

	/**
	 * A PDO connection to a Scrivo database.
	 * @var \Scrivo\PdoConnection
	 */
	private $conn = null;

	/**
	 * The principal.
	 * @var \Scrivo\User
	 */
	private $principal = null;

	/**
	 * A IdLabel object that holding this sites id-label pairs.
	 * @var \Scrivo\IdLabel
	 */
	private $idLabel = null;

	/**
	 * The object cache.
	 * @var \Scrivo\LocalCache
	 */
	private $cache = null;

	/**
	 * Construct a Scrivo context using a configuration file and a user id.
	 *
	 * @param Config $config A Scrivo configuration object.
	 * @param int $userId A Scrivo user id.
	 */
	public function __construct(Config $config, $userId) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		));

		$this->config = $config;
		$this->conn = new \Scrivo\PdoConnection($config);
		$this->cache = new \Scrivo\LocalCache(
//			new \Scrivo\Cache\FileCache()
		);
		$this->idLabel = \Scrivo\IdLabel::select($this);
		$this->principal = \Scrivo\User::fetch($this, $userId);
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
			case "config": return $this->config;
			case "connection": return $this->conn;
			case "principal": return $this->principal;
			case "labels": return $this->idLabel;
			case "cache": return $this->cache;
		}
		throw new \Scrivo\SystemException("No such get-property '$name'.");
	}

	/**
	 * Check a permission on an object using this Scrivo context.
	 *
	 * @param int $perm The permission to test (READ_ACCESS || WRITE_ACCESS
	 *    || PUBLISH_ACCESS)
	 * @param int $objectId A valid object id of a page or asset.
	 *
	 * @throws \Scrivo\ApplicationException if no access was granted.
	 */
	public function checkPermission($perm, $objectId=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER, array(
				\Scrivo\AccessController::READ_ACCESS,
				\Scrivo\AccessController::WRITE_ACCESS,
				\Scrivo\AccessController::PUBLISH_ACCESS)),
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER)
		), 1);

		if (!AccessController::checkPermission($this, $perm, $objectId)) {
			throw new \Scrivo\ApplicationException("Access violation");
		}
	}

	/**
	 * Prepare for serialization. Context data can never be stored in caches,
	 * so prevent this by returning an empty array when the context is
	 * serialized.
	 *
	 * @return array An empty array.
	 */
	public function __sleep() {
		return array();
	}

}

?>