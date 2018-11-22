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
 * $Id: I18n.php 711 2013-07-04 12:05:36Z geert $
 */

/**
 * Implementation of the \Scrivo\I18n class.
 */

namespace Scrivo;

/**
 * Class for Scrivo internationalization (i18n) resources.
 *
 * This is a class to create a set of i18n key value pairs. By default the
 * created instance will use Scrivo's own set of i18n resources, but it is
 * possible to use your own set(s).
 *
 * An i18n resource file is a PHP script that declares a single array
 * variabele ($I18N_TEXT) that contains the i18n key value pairs, where the
 * keys are the english texts and the values contain the translations, f.i.
 * the file "nl_NL.php" might contain the following:
 *
 * <?php $I18N_TEXT = array("Cancel" => "Annuleer"); ?>
 *
 * The file name needs to be named after the language set it contains:
 * "nl_NL.php" contains the Dutch i18n resources. It is of course strongly
 * advised to use the UTF-8 encoding for your internationalization files.
 *
 * Because we're dealing with hard coded data and it will be excessively
 * cumbersome to deal I18N keys if you'll have to use the \Scrivo\Str wrappers,
 * you can use plain strings as i18n keys.
 *
 * Typical usage:
 *
 * $i18n = new \Scrivo\I18n(new \Scrivo\Str("nl_NL"));
 * echo $i18n["Cancel"];
 */
class I18n implements \ArrayAccess {

	/**
	 * The i18n key value pairs, where the keys are the English texts and
	 * the values contain the translations.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Create a set of internationalization key/value pairs for a given
	 * language.
	 *
	 * @param \Scrivo\Str $langCode The language code for which to create the
	 *    set of resources for.
	 * @param \Scrivo\Str $dir An optional directory for an alternative
	 *    location for the i18n resource file.
	 *
	 * @throws \Exception If no 1i8n resource file was found.
	 */
	function __construct(\Scrivo\Str $langCode, \Scrivo\Str $dir=null) {
		if (!$dir) {
			// The location of Scrivo UI language keys.
			$dir = dirname(__FILE__)."/I18n";
		}
		// Sanitize the file name for security reasons.
		$file = $dir."/".preg_replace("/[^_a-zA-Z]/u", "", $langCode).".php";
		if (file_exists($file)) {
			require $file;
			$this->data = $I18N_TEXT;
		} else {
			// TODO: only when in debug mode
			//throw new \Exception("I18n recourse file '$file' was not found.");
			$this->data = array();
		}
	}

	/**
	 * Get an i18n entry from a set using array brackets.
	 *
	 * Note that this method is part of the implementation of ArrayAccess and
	 * should not be called from another context.
	 *
	 * @param string $i18nKey A character offet in the string.
	 *
	 * @return string The translation of the key, or the key if the key
	 *    was not found.
	 */
	public function offsetGet($i18nKey) {
		return isset($this->data[$i18nKey]) ? $this->data[$i18nKey] : $i18nKey;
	}

	/**
	 * Illegal method: set a character at a specified index location.
	 *
	 * Note that this method is part of the implementation of ArrayAccess.
	 * I18n sets are inmutable so this method implementation is not relevant
	 * and throws an exception if called.
	 *
	 * @param int $offset
	 * @param string $value
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetSet($offset, $value) {
		throw new \Scrivo\SystemException(
			"offsetSet can't be called on I18n objects");
	}

	/**
	 * Check if the specified index location in this string is valid.
	 *
	 * Note that this method is part of the implementation of ArrayAccess.
	 * It is assumed that i18n keys are always set: if not it is pretended
	 * it's set and key itself is returned. So this method implementation is
	 * not relevant and throws an exception if called.
	 *
	 * @param int $offset
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetExists($offset) {
		throw new \Scrivo\SystemException(
			"offsetExists can't be called on I18n objects");
	}

	/**
	 * Illegal method: unset a character at a specified index location.
	 *
	 * Note that this method is part of the implementation of ArrayAccess.
	 * I18n sets are inmutable so this method implementation is not relevant
	 * and throws an exception if called.
	 *
	 * @param int $offset
	 *
	 * @throws \Scrivo\SystemException If this method is called.
	 */
	public function offsetUnset($offset) {
		throw new \Scrivo\SystemException(
			"offsetUnset can't be called on I18n objects");
	}

}
