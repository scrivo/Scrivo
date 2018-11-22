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
 * $Id: UrlTest.php 801 2013-08-11 22:38:40Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageProperty\Url
 */
class PagePropertyUrlTest extends ScrivoDatabaseTestCase {


	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page_definition.yml" ,"page.yml"));
	}

	function testProperty() {

		$testHref = new \Scrivo\Str("http:\\www.url.com\url.html");
		$testTitle = new \Scrivo\Str("a title");
		$testTarget = new \Scrivo\Str("_blank");

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$pd = new \Scrivo\PagePropertyDefinition(self::$context);
		$pd->type = \Scrivo\PagePropertyDefinition::TYPE_URL;
		$pd->phpSelector = new \Scrivo\Str("anUrl");
		$pd->pageDefinitionId = $h->definition->id;
		$pd->insert();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$h->properties->anUrl->href = $testHref;

		$h->properties->anUrl->update();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$this->assertTrue(
			$h->properties->anUrl->href->equals($testHref));
		$this->assertTrue(
			$h->properties->anUrl->title->equals(new \Scrivo\Str("")));
		$this->assertTrue(
			$h->properties->anUrl->target->equals(new \Scrivo\Str("")));

		/***********/

		$h->properties->anUrl->title = $testTitle;

		$h->properties->anUrl->update();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$this->assertTrue(
				$h->properties->anUrl->href->equals($testHref));
		$this->assertTrue(
				$h->properties->anUrl->title->equals($testTitle));
		$this->assertTrue(
				$h->properties->anUrl->target->equals(new \Scrivo\Str("")));

		/***********/

		$h->properties->anUrl->target = $testTarget;

		$h->properties->anUrl->update();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$this->assertTrue(
				$h->properties->anUrl->href->equals($testHref));
		$this->assertTrue(
				$h->properties->anUrl->title->equals($testTitle));
		$this->assertTrue(
				$h->properties->anUrl->target->equals($testTarget));

		/***********/
	}

}

?>