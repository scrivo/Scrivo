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
 * $Id: page_definition_tab_copy_form.tpl.php 849 2013-08-20 19:19:50Z geert $
 */

$this->beginSection("content", true);

?>
<form action="index.php" method="post">

<table cellspacing="0"><tr><td style="vertical-align: top">

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["To copy"]?></th>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="tab_name"><?php
				echo $i18n["Tab title:"]?></label></td>
			<td><?php echo(htmlspecialchars($tab->title)) ?></td>
		</tr>
	</table>

	<button type="submit"><?php echo $i18n["Copy"]?></button>
	<button type="button" onclick=
		"document.location='index.php?a=page_definition_form&page_definition_id=<?php
		echo $tab->pageDefinitionId?>';"><?php echo $i18n["Cancel"]?></button>

</td><td style="padding-left: 2em">

	<input type="hidden" name="page_definition_tab_id" value="<?php
		echo $tab->id?>">
	<input type="hidden" name="a" value="page_definition_tab_copy">

	<table class="list" cellspacing="0">

		<tr>
			<th colspan="2" class="table-heading"><?php
				echo $i18n["Copy to"]?></th>
		</tr>
<?php
$cnt = 0;
foreach ($pageDefinitions as $k=>$templ) {
?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td><label for="label_<?php echo $templ->id; ?>"><?php
				echo $templ->title; ?></label></td>
			<td>
<?php
	$found = false;
	foreach ($templ->tabs as $te) {
		if ($tab->title == $te->title) {
			$found = true;
			echo '<input type="checkbox" disabled="true" checked="true">';
			break;
		}
	}
	if (!$found) {
		echo '<input type="checkbox" name="page_definition_id[]" id="label_'.
			$templ->id.'" value="'.$templ->id.'">';
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