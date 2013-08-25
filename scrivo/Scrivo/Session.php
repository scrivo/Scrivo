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
 * $Id: Session.php 708 2013-07-02 11:59:37Z geert $
 */

/**
 * Implementation of the \Scrivo\Session class.
 */

namespace Scrivo;

/**
 * The Scrivo Session class is a lightweight container for session variables.
 * It should enable you to create a section in to session variable space to
 * store a set of session variables and to enable you to destroy the complete
 * set at once.
 *
 * <?php
 * // script1.php
 * $session = new \Scrivo\Session("My_session");
 * $session->userId = 32;
 * header("Location: script2.php");
 * ?>
 *
 * <?php
 * // script2.php
 * $session = new \Scrivo\Session("My_session");
 * echo $session->userId;
 * ?>
 */
class Session {

	/**
	 * The name of the section.
	 * @var string
	 */
	private $section = "Scrivo";

	/**
	 * Construct a session, either using the default session name: "Scrivo" or
	 *   using your own name.
	 *
	 * @param string $section
	 */
	public function __construct($section=null) {
		if ($section) {
			$this->section = $section;
		}
		if (!isset($_SESSION[$this->section])) {
			$_SESSION[$this->section] = array();
		}
	}

	/**
	 * Implementation of the writable properties using the PHP magic
	 * method __set().
	 *
	 * @param string $name The name of the property to set.
	 * @param mixed $value The value of the property to set.
	 */
	public function __set($name, $value) {
		$_SESSION[$this->section][$name] = $value;
	}

	/**
	 * Property access to a session variable.
	 *
	 * @param string $name The session variable name.
	 * @return mixed The value of the session variable.
	 * @throws \Scrivo\SystemException if the variable does not exists.
	 */
	public function __get($name) {
		if ($this->__isset($name)) {
			return $_SESSION[$this->section][$name];
		}
		throw new \Scrivo\SystemException("Invalid session variable $name");
	}

	/**
	 * Check if a session variable was set.
	 *
	 * @param string $name The session variable name.
	 */
	public function __isset($name) {
		return isset($_SESSION[$this->section][$name]);
	}

	/**
	 * Unset/delete a session variable.
	 *
	 * @param string $name The session variable name.
	 */
	public function __unset($name) {
		unset($_SESSION[$this->section][$name]);
	}

	/**
	 * Destroy the session and all its variables.
	 */
	public function destroy() {
		$_SESSION[$this->section] = array();
	}
}

?>