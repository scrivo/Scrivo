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
 * $Id: HtmlContentTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageProperty\Image
 */
class PagePropertyHtmlContentTest extends ScrivoDatabaseTestCase {


	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page_definition.yml", "page.yml"));
	}

	function testProperty() {

		$raw = new \Scrivo\Str("<P>some text");
		$clean = new \Scrivo\Str("<p>some text</p>");

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$pd = new \Scrivo\PagePropertyDefinition(self::$context);
		$pd->type = \Scrivo\PagePropertyDefinition::TYPE_HTML_TEXT_TAB;
		$pd->phpSelector = new \Scrivo\Str("anHtmlContent");
		$pd->pageDefinitionId = $h->definition->id;
		$pd->typeData = (object) array(
			"css_selector" => "cssSelector",
			"page_css" => "body {}",
			"stylesheet" => "http://www.scrivo.nl/style.css",
			"css_id" => "cssId"
		);
		$pd->insert();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$h->properties->anHtmlContent->rawHtml = $raw;
		$h->properties->anHtmlContent->html = $clean;

		$h->properties->anHtmlContent->update();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$this->assertTrue(
			$raw->equals($h->properties->anHtmlContent->rawHtml));
		//TODO fix this
		$this->assertTrue(
			$raw->equals($h->properties->anHtmlContent->html));
	}

}

?>