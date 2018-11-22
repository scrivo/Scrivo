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
 * $Id: Application.php 847 2013-08-20 16:41:10Z geert $
 */

/**
 * Implementation of the \Scrivo\PageProperty\Application class.
 */

namespace Scrivo\PageProperty;

/**
 * Page property implementation for TYPE_APPLICATION_TAB
 */
class Application extends \Scrivo\PageProperty {

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
			case "definitionId": return intval((string)$this->extData);
			case "definitionType": return intval((string)$this->data);
			case "application": return $this->getApp();
		}
		return parent::__get($name);
	}

	/**
	 * Instantiate and return the application that is linked to this property.
	 *
	 * return mixed The application linked to this property.
	 */
	private function getApp() {
		switch ($this->definitionType) {
			case \Scrivo\ApplicationDefinition::TYPE_LIST:
			case \Scrivo\ApplicationDefinition::TYPE_DISTRIBUTED_LIST:
			case \Scrivo\ApplicationDefinition::TYPE_LISTVIEW:
			case \Scrivo\ApplicationDefinition::TYPE_FORM:
				return \Scrivo\ItemList::fetch($this->page->context,
					$this->page->id, $this->definition->id);
			case \Scrivo\ApplicationDefinition::TYPE_URL:
				return NULL;
		}
		throw new \Scrivo\SystemException(
			"Invalid application type '{$this->definitionType}'");
	}
}

?>