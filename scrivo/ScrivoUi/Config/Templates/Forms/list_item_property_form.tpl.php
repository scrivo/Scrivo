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
 * $Id: list_item_property_form.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;

$this->beginSection("content", true);

?>
<form action="?" method="post">

<?php
if ($listItemProperty->id) {
?>
	<input type="hidden" name="a" value="list_item_property_update">
	<input type="hidden" name="list_item_property_id" value="<?php
		echo $listItemProperty->id?>">
<?php
} else {
?>
	<input type="hidden" name="a" value="list_item_property_insert">
	<input type="hidden" name="application_definition_id" value="<?php
		echo $applicationId?>">
<?php
}
?>

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["List item property data"]?></th>
		</tr>
		<tr>
			<td width=20%"" class="label"><label
				for="list_item_definition_id"><?php
					echo $i18n["List item definition:"]?></label></td>
			<td>
				<select id="list_item_definition_id"
					name="list_item_definition_id" >
<?php
$liDefs = ListItemDefinition::select($this->context, $applicationId);
foreach ($liDefs as $dId => $liDef) {
	echo "<option ".
		($dId == $listItemProperty->listItemDefinitionId ? "selected " : "").
		"value=\"".$dId."\">".$liDef->title."</option>\n";
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="label"><?php
				echo $i18n["Property name:"]?></label></td>
			<td><input type="text" id="label" name="label" size="50"
				maxlength="50" value="<?php echo
				htmlspecialchars($listItemProperty->title)?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="php_selector"><?php
				echo $i18n["PHP selector:"]?></label></td>
			<td><input type="text" id="php_selector" name="php_selector"
				size="25" maxlength="50" value="<?php
				echo htmlspecialchars($listItemProperty->phpSelector)?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="config"><?php
				echo $i18n["Show in list:"]?></label></td>
			<td>
				<select name="config" id="config">
<?php
				echo "<option ".($listItemProperty->inList ? "selected " : "").
					"value=\"1\">Yes</option>\n";
				echo "<option ".($listItemProperty->inList ? "" : "selected ").
					"value=\"0\">No</option>\n";
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="type"><?php
				echo $i18n["Type:"]?></label></td>
			<td>
				<select name="type" id="type" onchange="selecttype(this.value)">
<?php

			foreach(ListItemPropertyDefinition::getTypes() as $type) {
				echo "<option ".
					($type == $listItemProperty->type ? "selected " : "").
					"value=\"{$type}\">{$type}</option>\n";
			}

?>
				</select>
			</td>
		</tr>
		<tr>
			<td	class="label"><label for="stylesheet"><?php
				echo $i18n["Additional data for the selected type:"]
			?></label></td>
			<td rowspan="2"><textarea id="type_data" name="type_data"
				cols="50" rows="25" ><?php
				echo htmlspecialchars($this->typeDataAsString(
					$listItemProperty->typeData))
			?></textarea></td>
		</tr>
		<tr>
			<td	id="info" style="width:20em">
<script>
function selecttype(val) {
	var l = document.getElementById("info").getElementsByTagName("DIV");
	for (i in l) {
		if (l[i].tagName == "DIV") {
			l[i].style.display = "None";
		}
	}
	document.getElementById("info_"+val).style.display = "block";
}
</script>

<?php
foreach(ListItemPropertyDefinition::getTypes() as $type) {
?>
			<div id="info_<?php echo $type?>" style="display:<?php
				echo $type === $listItemProperty->type?"block":"none"?>">
<?php
				if (isset($helpTexts[(string)$type])) {
					$this->formatHelp($helpTexts[(string)$type]);
				}
?>
			</div>
<?php
}
$this->formatHelp($helpTexts["col"]);
?>
			</td>
		</tr>
	</table>

	<button type="submit"><?php echo $i18n["Save"]?></button>
	<button type="button" onclick="document.location=
		'?a=application_definition_form&application_definition_id=<?php
		echo $applicationId?>';"><?php
			echo $i18n["Cancel"]?></button>

</form>
<?php

$this->endSection();

?>