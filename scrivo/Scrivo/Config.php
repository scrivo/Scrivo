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
 * $Id: Config.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Config class.
 */

namespace Scrivo;

/**
 * Class to hold Scrivo configuration data.
 *
 * Scrivo configuration data is stored in instances of this class. Scrivo
 * configuration data comes from two different sources:
 *
 * * Default configuration data: entries that always exist and which have
 *   values that can be overwritten by entries in the Scrivo configuration
 *   file.
 * * Optional configuration data: all entries in the Scrivo configuration
 *   file that are no part of the default set will also become members of the
 *   configuration class.
 *
 * There are also id-label pairs: certain id's can be labbelled in the database,
 * this configuration data can be accessed through the class \Scrivo\IdLabel.
 *
 * When instantiating this class it will search up the directory tree for a
 * Scrivo config file named ".htscrivo". Alternatively you can specify a file
 * when instantiating a configuration data object.
 *
 * Note that only string and numeric values can be specified. These will be
 * converted to a \Scrivo\String, int or float value. It is not possible
 * to specify boolean values due to limitations of the php function
 * parse_ini_file. So also note that specifing values like true, false, yes,
 * no, on and off without quotes might not give you the expected results.
 * When you need boolean values it is preferred to use the numeric values 0
 * or 1.
 *
 * @property-read \Scrivo\String $ADMIN_IP_ADDRESSES
 * @property-read \Scrivo\String $CACHE_DIR
 * @property-read int $CACHE_DIR_GC
 * @property-read \Scrivo\String $CACHE_TYPE
 * @property-read \Scrivo\String $DB_API
 * @property-read int $HTML_TIDY
 * @property-read \Scrivo\String $HTTP_PROTOCOL
 * @property-read int $JS_DEBUG
 * @property-read \Scrivo\String $KEY_PREFIX
 * @property-read int $ROOT_PAGE_ID
 * @property-read int $ROOT_FOLDER_ID
 * @property-read \Scrivo\String $SESSION_PREFIX
 * @property-read \Scrivo\String $UI_LANG
 * @property-read \Scrivo\String $WEBSERVICE_SPELL
 * @property-read \Scrivo\String $WEBSERVICE_TIDY
 */
class Config {

	/**
	 * Array to hold all data from the config file.
	 * @var array
	 */
	protected $ini;

	/**
	 * Convert a string read from an ini file to its most likely type.
	 *
	 * @param string $val The value read from the ini file.
	 *
	 * @return int|float|\Scrivo\String The given value converted to its
	 *   most likely type.
	 */
	private function convertStr(&$val) {
		if (is_numeric($val)) {
			if ((string)$val === (string)(int)$val) {
				$val = intval($val);
			} else {
				$val = floatval($val);
			}
		} else {
			$val = new \Scrivo\String($val);
		}
	}

	/**
	 * Try to find a Scrivo config file higher up the directory tree.
	 *
	 * @param string $cfgFile The name of the configuration file to locate.
	 *
	 * @return string A Scrivo configuration file name.
	 */
	private function findConfigFile($cfgFile) {
		$parts = \Scrivo\String::create($_SERVER["SCRIPT_FILENAME"])->split(
			new \Scrivo\String(DIRECTORY_SEPARATOR));
		for ($i = count($parts)-2; $i >=0; $i--) {
			array_pop($parts);
			$file = implode("/", $parts)."/$cfgFile";
			if (file_exists($file)) {
				return($file);
			}
		}
		throw new \Scrivo\SystemException("Could not locate config file");
	}

	/**
	 * Create a configuration data object. As input the constructor will
	 * look for an Scrivo config file at expected locations or at an
	 * alternative location if provided.
	 *
	 * @param \Scrivo\String If you don't want/can't use the standard Scrivo
	 *   configuration file you can provide the location of an alternative file.
	 */
	public function __construct(\Scrivo\String $path=null) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(null), 0);

		// Read the ini file.
		if (!$path) {
			$path = $this->findConfigFile(".htscrivo");
		}
		try {
			$this->ini = parse_ini_file((string)$path);
		} catch (\Exception $e) {
			throw new \Scrivo\SystemException(
				"Invalid config file: ".$e->getMessage());
		}

		// The basic set of properties and their default values.
		$def = array(
			"ADMIN_IP_ADDRESSES" => "127.0.0.1",
			"CACHE_DIR" => "",
			"CACHE_DIR_GC" => 0,
			"CACHE_TYPE" => function_exists("apc_fetch") ? "APC" : "",
			"DB_API" => "mysql",
			"HTML_TIDY" => 0,
			"HTTP_PROTOCOL" => "http://",
			"JS_DEBUG" => 0,
			"KEY_PREFIX" => strtoupper(preg_replace("/[^a-zA_Z0-9]+/", "_",
				isset($this->ini["WWW_ROOT"])?$this->ini["WWW_ROOT"]:""))."_",
			"ROOT_PAGE_ID" => "1",
			"ROOT_FOLDER_ID" => "2",
			"SESSION_PREFIX" =>
				(isset($this->ini["DB_NAME"])?$this->ini["DB_NAME"]:"")."_".
				(isset($this->ini["INSTANCE_ID"])?$this->ini["INSTANCE_ID"]:"").
				"_",
			"UI_LANG" => "en_US",
			"WEBSERVICE_SPELL" => "http://www.scrivo.nl/spell/spell.php",
			"WEBSERVICE_TIDY" => "http://www.scrivo.nl/tidy/tidy.php",
		);

		// Set missing entries to the default values.
		foreach ($def as $d=>$v) {
			if (!isset($this->ini[$d])) {
				$this->ini[$d] = $v;
			}
		}

		// Rename legacy key names
		if (!isset($this->ini["DB_HOST"]) && isset($this->ini["DB_SERVER"])) {
			$this->ini["DB_HOST"] = $this->ini["DB_SERVER"];
		}
		if (!isset($this->ini["DB_USER"]) && isset($this->ini["DB_USERCODE"])) {
			$this->ini["DB_USER"] = $this->ini["DB_USERCODE"];
		}

		// Convert types
		array_walk($this->ini, array($this, 'convertStr'));
	}

	/**
	 * Test if a particular property was set.
	 *
	 * @param string $name The name of the configuration property to test.
	 *
	 * @return boolean True if the property was set, false if not.
	 */
	public function __isset($name) {
		return isset($this->ini[$name]);
	}

	/**
	 * Accessor method for configuration data members.
	 *
	 * @param string $name The configuration property name to retrieve for
	 *   which to retrieve it's value.
	 *
	 * @return \Scrivo\String|float|int The value of the property $name.
	 */
	public function __get($name) {
		if (!$this->__isset($name)) {
			throw new \Scrivo\SystemException(
				"Invalid configuration property '$name'");
		}
		return $this->ini[$name];
	}

}

?>
