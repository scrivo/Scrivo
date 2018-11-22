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
 * $Id: page_property_definition_form.tpl.php 849 2013-08-20 19:19:50Z geert $
 */

use \Scrivo\PagePropertyDefinition;
use \Scrivo\Str;

$this->beginSection("content", true);

?>
<form action="index.php" method="post">
<?php
if ($property->id) {
?>
	<input type="hidden" name="a" value="page_property_definition_update">
	<input type="hidden" name="page_property_definition_id" value="<?php
		echo $property->id?>">
<?php
} else {
?>
	<input type="hidden" name="a" value="page_property_definition_insert">
	<input type="hidden" name="page_definition_id" value="<?php
		echo $pageDefinitionId?>">
<?php
}
?>
	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["Template property data"]?></th>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="label"><?php
				echo $i18n["Property name:"]?></label></td>
			<td><input type="text" id="label" name="label" size="50"
				maxlength="50" value="<?php
				echo(htmlspecialchars($property->title))?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="php_selector"><?php
				echo $i18n["PHP selector:"]?></label></td>
			<td><input type="text" id="php_selector" name="php_selector"
				size="25" maxlength="50" value="<?php
				echo(htmlspecialchars($property->phpSelector))?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="type"><?php
				echo $i18n["Type:"]?></label></td>
			<td>
				<select name="type" id="type">
<?php
foreach(PagePropertyDefinition::getTypes() as $type) {
	if ($type != PagePropertyDefinition::TYPE_APPLICATION_TAB
			&& $type != PagePropertyDefinition::TYPE_HTML_TEXT_TAB) {
		echo "<option ".($type == $property->type ? "selected " : "").
			"value=\"{$type}\">{$type}</option>\n";
	}
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="tab_id"><?php
				echo $i18n["Tab:"]?></label></td>
			<td>
				<select name="tab_id" id="tab_id">
<?php
foreach ($tabs as $tabId => $tabTitle) {
	echo "<option ".
		($tabId == $property->pageDefinitionTabId ? "selected " : "").
		" value=\"$tabId\">{$tabTitle}</option>\n";
}

function typeDataAsString($data) {
	$d = array();
	foreach($data as $k=>$v) {
		$d[] = $k."=".$v;
	};
	return new Str(implode("\n", $d));
}

?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="label"><label for="stylesheet"><?php
				echo $i18n["Additional data for the selected type:"]?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2"><textarea id="type_data" name="type_data"
				cols="80" rows="10"><?php
				echo(htmlspecialchars(typeDataAsString($property->typeData)))
				?></textarea></td>
		</tr>
	</table>

	<button type="submit"><?php echo $i18n["Save"]?></button>
	<button type="button" onclick=
		"document.location='index.php?a=page_definition_form&page_definition_id=<?php
			echo $pageDefinitionId?>';return false;"><?php echo
				$i18n["Cancel"]?></button>

</form>
<?php

$this->endSection();

?>