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
 * $Id: ApplicationTest.php 866 2013-08-25 16:22:35Z geert $
 */

require_once("ScrivoDatabaseTestCase.php");

/**
 * Tests for class \Scrivo\PageProperty\Image
 */
class PagePropertyApplicationTest extends ScrivoDatabaseTestCase {


	/**
	 * Insert the initial test data for the tests into the database.
	 */
	function getDataSet() {
		return $this->addDataSets(array("init.yml", "users_and_roles.yml",
			"page_definition.yml", "page.yml"));
	}

	function testProperty() {

		$testData = new \Scrivo\String("<p>some text</p>");

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$list = new \Scrivo\ApplicationDefinition(self::$context);
		$list->type = \Scrivo\ApplicationDefinition::TYPE_LISTVIEW;
		$list->title = new \Scrivo\String("Listview app");
		$list->description = new \Scrivo\String("Listview descr");
		$list->insert();

		$pd = new \Scrivo\PagePropertyDefinition(self::$context);
		$pd->type = \Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB;
		$pd->phpSelector = new \Scrivo\String("aList");
		$pd->pageDefinitionId = $h->definition->id;
		$pd->typeData = (object) array(
			"APPLICATION_DEFINITION_ID" => $list->id
		);
		$pd->insert();

		$form = new \Scrivo\ApplicationDefinition(self::$context);
		$form->type = \Scrivo\ApplicationDefinition::TYPE_FORM;
		$form->title = new \Scrivo\String("Form app");
		$form->description = new \Scrivo\String("Form descr");
		$form->insert();

		$pd = new \Scrivo\PagePropertyDefinition(self::$context);
		$pd->type = \Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB;
		$pd->phpSelector = new \Scrivo\String("aForm");
		$pd->pageDefinitionId = $h->definition->id;
		$pd->typeData = (object) array(
			"APPLICATION_DEFINITION_ID" => $form->id
		);
		$pd->insert();

		$custom = new \Scrivo\ApplicationDefinition(self::$context);
		$custom->type = \Scrivo\ApplicationDefinition::TYPE_URL;
		$custom->title = new \Scrivo\String("Custom app");
		$custom->description = new \Scrivo\String("Custom descr");
		$custom->insert();

		$pd = new \Scrivo\PagePropertyDefinition(self::$context);
		$pd->type = \Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB;
		$pd->phpSelector = new \Scrivo\String("aCustomApp");
		$pd->pageDefinitionId = $h->definition->id;
		$pd->typeData = (object) array(
			"APPLICATION_DEFINITION_ID" => $custom->id
		);
		$pd->insert();

		unset(self::$context->cache[self::PAGE_HOME_ID]);

		$h = \Scrivo\Page::fetch(self::$context, self::PAGE_HOME_ID);

		$this->assertInstanceOf("\Scrivo\ItemList",
			$h->properties->aList->application);
		//TODO
		//$this->assertNull($h->properties->aForm->application);
		//TODO
		//$this->assertNull($h->properties->aCustomApp->application);

	}

	/**
	 * Test exception thrown when using an invalid application id.
	 * @expectedException \Scrivo\SystemException
	 */
	function testInvalidApplicationType() {
		$ad = \Scrivo\PageProperty::create(
			new \Scrivo\Page(), array(
				"type" =>
					\Scrivo\PagePropertyDefinition::TYPE_APPLICATION_TAB,
				"php_key" =>  new \Scrivo\String("SEL"),
				"ID_DEF" => 3,
				"value" => "",
				"VALUE2" => ""
			));
		$ad->data = new \Scrivo\String("123");
		$app = $ad->application;
	}

}

?>