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
 * $Id: ApplicationDefinitionForm.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the
 * \ScrivoUi\Config\Actions\Layout\ApplicationDefinitionForm action class.
 */

namespace ScrivoUi\Config\Actions\Layout;

use \Scrivo\LayoutAction;
use \Scrivo\Request;
use \Scrivo\I18n;
use \Scrivo\ApplicationDefinition;

/**
 * The ApplicationDefinitionForm layout action class sets up the layout for
 * an application definition form.
 */
class ApplicationDefinitionForm extends LayoutAction {

	/**
	 * In this action the data to display is retrieved and used to generate
	 * the content sections.
	 */
	function doAction() {

		$i18n = new I18n($this->context->config->UI_LANG);

		$applicationId =
			Request::get("application_definition_id", Request::TYPE_INTEGER, 0);
		$application = $applicationId
			? ApplicationDefinition::fetch($this->context, $applicationId)
			: new ApplicationDefinition($this->context);

		$title = $application->id
			? $i18n["Edit application/list"]
			: $i18n["New application/list"];

		$types = array(
			ApplicationDefinition::TYPE_URL => $i18n["Custom"],
			ApplicationDefinition::TYPE_LIST => $i18n["List"],
			ApplicationDefinition::TYPE_LISTVIEW => $i18n["Listview"],
			ApplicationDefinition::TYPE_FORM => $i18n["Form"],
			ApplicationDefinition::TYPE_DISTRIBUTED_LIST => 
				$i18n["Distributed list"],
		);

		$tr = "../ScrivoUi/Config/Templates";
		include "{$tr}/common.tpl.php";
		include "{$tr}/Forms/application_definition_form.tpl.php";
		$this->useLayout("{$tr}/master.tpl.php");

		$this->setResult(self::SUCCESS);

	}
}

?>