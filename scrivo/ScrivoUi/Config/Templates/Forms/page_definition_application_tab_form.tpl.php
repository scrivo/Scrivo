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
 * $Id: page_definition_application_tab_form.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\ApplicationDefinition;

$this->beginSection("content", true);

?>

<form action="?" method="post">
<?php
if ($property->id) {
?>
	<input type="hidden" name="a"
		value="page_definition_application_property_update">
	<input type="hidden" name="page_property_definition_id"
		value="<?php echo $property->id?>">
<?php
} else {
?>
	<input type="hidden" name="a"
		value="page_definition_application_tab_insert">
	<input type="hidden" name="page_definition_id"
		value="<?php echo $templateId?>">
<?php
}
?>

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php
				echo "ApplicationDefinition tab properties"?></th>
		</tr>
<?php
if (!$property->id) {
?>
		<tr>
			<td width="30%" class="label"><label for="tab_name"><?php
				echo "Tab name:"?></label></td>
			<td><input type="text" id="tab_name" name="tab_name"
				size="50" maxlength="50" value=""></td>
		</tr>
<?php
}
?>
		<tr>
			<td class="label"><label for="tab_name"><?php
				echo "ApplicationDefinition:"?></label></td>
			<td>
				<select name="application_definition_id">
<?php
$apps = ApplicationDefinition::select($this->context);
foreach ($apps as $k => $app) {
	echo "<option ".
		($app->id == $property->typeData->APPLICATION_DEFINITION_ID
			? "selected " : "")."value=\"{$app->id}\">{$app->title}</option>\n";
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="php_selector"><?php
				echo "PHP selector:"?></label></td>
			<td><input type="text" id="php_selector" name="php_selector"
				size="25" maxlength="50" value="<?php
				echo(htmlspecialchars($property->phpSelector))?>"></td>
		</tr>
	</table>

	<button type="submit"><?php echo "Save"?></button>
	<button type="button" onclick=
		"document.location='?a=page_definition_form&page_definition_id=<?php
			echo $templateId?>';return false;"><?php echo "Cancel"?></button>

</form>
<?php

$this->endSection();

?>