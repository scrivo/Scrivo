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
 * $Id: page_property_definition_copy_form.tpl.php 849 2013-08-20 19:19:50Z geert $
 */

use \Scrivo\PageDefinitionTab;

$this->beginSection("content", true);

?>
<form action="index.php" method="post">

<table cellspacing="0"><tr><td style="vertical-align: top">

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["To copy"]?></th>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="label"><?php
				echo $i18n["Property title:"]?></label></td>
			<td><?php echo(htmlspecialchars($prop->title)) ?></td>
		</tr>
		<tr>
			<td class="label"><label for="php_selector"><?php
				echo $i18n["Property selector:"]?></label></td>
			<td><?php echo(htmlspecialchars($prop->phpSelector)) ?></td>
		</tr>
		<tr>
			<td class="label"><label for="type"><?php
				echo $i18n["Type:"]?></label></td>
			<td><?php echo $prop->type ?></td>
		</tr>
		<tr>
			<td class="label"><label for="tab_id"><?php
				echo $i18n["Tab:"]?></label></td>
			<td>
<?php
foreach ($tabs as $k => $tab) {
	if ($k == $prop->pageDefinitionTabId) {
		echo $tab->title;
	}
}
?>
			</td>
		</tr>
	</table>

	<button type="submit"><?php echo $i18n["Copy"]?></button>
	<button type="button" onclick=
		"document.location='index.php?a=page_definition_form&page_definition_id=<?php
		echo $prop->pageDefinitionId?>';"><?php echo $i18n["Cancel"]?></button>

</td><td style="padding-left: 2em">

	<input type="hidden" name="page_property_definition_id" value="<?php
		echo $prop->id?>">
	<input type="hidden" name="a" value="page_property_definition_copy">

	<table class="list" cellspacing="0">

		<tr>
			<th colspan="2" class="table-heading"><?php
				echo $i18n["Copy to"]?></th>
		</tr>
<?php
$cnt = 0;
foreach ($pageDefinitions as $templId => $templ) {
?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td><?php echo $templ->title; ?></td>
			<td>
<?php
	$found = false;
	foreach ($templ->properties as $te) {
		if ($prop->phpSelector == $te->phpSelector) {
			$found = true;
			echo '<input type="checkbox" disabled="true" checked="true">';
			break;
		}
	}
	if (!$found) {

		echo '<input type="checkbox" name="page_definition_id[]" value="'.
			$templ->id.'"><label for="'.$templ->id.'">Eigenschappen</label> ';

		foreach ($templ->tabs as $te) {
			if (PageDefinitionTab::TYPE_PROPERTY_TAB == $te->type) {
				echo '<input type="checkbox" name="page_definition_id[]"
					id="'.$templ->id.'_'.$te->id. '"
					value="'.$templ->id.'_'.$te->id.'">
					<label for="'.$templ->id.'_'.$te->id.'">'.
						$te->title.'</label> ';
			}
		}
	}
?>
			</td>
		</tr>
<?php
}
?>
	</table>

</td></tr></table>

</form>

<?php

$this->endSection();

?>