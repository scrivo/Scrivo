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
 * $Id: application_definition_list.tpl.php 866 2013-08-25 16:22:35Z geert $
 */

$this->beginSection("content", true);

?>

<table cellspacing="0"><tr><td style="vertical-align: top">

<table class="list" cellspacing="0">

	<tr>
		<th class="table-heading"><?php
			echo $i18n["Lists/ApplicationDefinitions"]?></th>
		<th class="table-heading"><?php
			echo $i18n["Type"]?></th>
		<th class="table-heading"><?php
			echo $i18n["ApplicationDefinition url"]?></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
	</tr>
<?php
$cnt = 0;
foreach($items as $item) {
?>
	<tr<?php if (++$cnt%2==1) echo ' class="row-color"'?>>
		<td><?php echo $item->title; ?></td>
		<td><?php echo $types[$item->type]; ?></td>
		<td><?php echo $item->location; ?></td>
		<td class="img">
			<a
	href="?a=application_definition_form&application_definition_id=<?php
				echo $item->id; ?>">
				<img src="../img/admin/properties.png" alt="<?php
					echo $i18n["Edit list or application"]?>" title="<?php
					echo $i18n["Edit list or application"]?>">
			</a>
		</td>
		<td class="img">
			<a
	href="?a=application_definition_export&application_definition_id=<?php
				echo $item->id; ?>">
				<img src="../img/admin/database_export.png" alt="<?php
					echo $i18n["Export list or application"]?>" title="<?php
					echo $i18n["Export list or application"]?>">
			</a>
		</td>
		<td class="img">
			<a onclick="return confirm('<?php
				echo $i18n["Delete list or application?"]?>')"
	href="?a=application_definition_delete&application_definition_id=<?php
				echo $item->id ?>">
				<img src="../img/admin/delete.png" alt="<?php
					echo $i18n["Delete list or application"]?>" title="<?php
					echo $i18n["Delete list or application"]?>">
			</a>
		</td>
	</tr>
<?php
}
?>
	</table>

</td><td style="vertical-align: top; padding-left: 2em">

	<form method="get" action="?">
		<input type="hidden" name="a" value="application_definition_form">
		<button><?php echo $i18n["New list or application"]?></button>
	</form>

	<form enctype="multipart/form-data" action="?" method="post">
		<input type="hidden" name="a" value="application_definition_import">
		<table class="form" cellspacing="0">
			<tr>
				<th>
					<?php echo $i18n["Import list or application"]?>
				</th>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
					<input name="userfile" type="file" />
				</td>
			</tr>
		</table>
		<button type="submit"><?php echo $i18n["Import"]?></button>
	</form>

</td></tr></table>
<?php

$this->endSection();

?>