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
 * $Id: Downloadable.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Downloadable class.
 */

namespace Scrivo;

/**
 * The Scrivo Downloadable class is a simple conveniance class to pass
 * downloadable file data from an action.
 */
class Downloadable {

	/**
	 * Constant to denote that we pass the actual file content in a variable.
	 */
	const type_data = 1;

	/**
	 * Constant to denote that we pass the actual file content in a file.
	 */
	const TYPE_FILE = 2;

	/**
	 * The download source type: either type_data or TYPE_FILE
	 * @var int
	 */
	private $type;

	/**
	 * The file name to use in the download.
	 * @var \Scrivo\String
	 */
	private $action;

	/**
	 * The binary data to pass in case of type_data or the physical location
	 * of the file in case of TYPE_FILE
	 * @var string|\Scrivo\String
	 */
	private $data;

	/**
	 * Construct a Downloadable object to pass from an action.
	 *
	 * For smaller files we can simply pass the file data as a variable
	 * (type_data), for larger files you can write the data to a temporary file
	 * and pass the file name (TYPE_FILE).
	 *
	 * @param \Scrivo\Context $context A valid Scrivo context.
	 * @param \Scrivo\String $action The file name to use in the
	 *   download headers, this will be prepended with a string representation
	 *   of the WWW_ROOT variabele.
	 * @param int $type the type of data to download, either type_data or
	 *   TYPE_FILE.
	 * @param string|\Scrivo\String $data The file data (type_data), or
	 *   file name (TYPE_FILE).
	 */
	public function __construct(\Scrivo\Context $context,
			\Scrivo\String $action, $type, $data) {
		\Scrivo\ArgumentCheck::assertArgs(func_get_args(), array(
			null,
			null,
			array(\Scrivo\ArgumentCheck::TYPE_INTEGER,
				array(self::type_data, self::TYPE_FILE)),
			array(array(\Scrivo\ArgumentCheck::TYPE_STRING, "Scrivo\String"))
		));
		if ($type == self::TYPE_FILE) {
			if (!($data instanceof \Scrivo\String)) {
				throw new \Scrivo\SystemException("Invalid argument type");
			}
		} else {
			\Scrivo\ArgumentCheck::assert(
				$data, \Scrivo\ArgumentCheck::TYPE_STRING);
		}
		// Get a 'filename save' representation of the WWW_ROOT variable.
		$wr = $context->config->WWW_ROOT->replace(
			\Scrivo\String::create(array("http://", "https://")),
			new \Scrivo\String(""))->replace(
				new \Scrivo\String("/"), new \Scrivo\String("_"));

		$this->action = new \Scrivo\String($wr . "_" . $action);
		$this->type = $type;
		$this->data = $data;
	}

	/**
	 * Output the file data to stdout. If the file data was read from a
	 * temporary file, the file will be deleted afterwards.
	 */
	public function outputData() {
		if ($this->type == self::TYPE_FILE) {
			readfile($this->data);
			unlink($this->data);
		} else if ($this->type == self::type_data) {
			echo $this->data;
		}
	}

	/**
	 * Get the file name to use in the download headers.
	 */
	public function getFileName() {
		return $this->action;

	}

}

?>