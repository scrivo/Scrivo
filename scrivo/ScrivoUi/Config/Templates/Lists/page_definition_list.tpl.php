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
 * $Id: page_definition_list.tpl.php 849 2013-08-20 19:19:50Z geert $
 */

use \Scrivo\PageDefinition;

$this->beginSection("content", true);

?>

<table cellspacing="0"><tr><td style="vertical-align: top">

<table class="list" cellspacing="0">

	<tr>
		<th class="table-heading"><?php echo $i18n["Page definitions"]?></th>
		<th class="table-heading"><?php echo $i18n["Description"]?></th>
		<th class="table-heading"><?php echo $i18n["Location"]?></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
	</tr>
<?php
$cnt = 0;
foreach(PageDefinition::select($this->context) as $key=>$value) {
?>
	<tr<?php if (++$cnt%2==1) echo ' class="row-color"'?>>
		<td>
			<?php echo $value->title; ?>
		</td>
		<td>
			<?php echo $value->description ?>
		</td>
		<td>
			<?php echo $value->action; ?>
		</td>
		<td class="img">
			<a href="index.php?a=page_definition_form&page_definition_id=<?php
					echo $value->id?>">
				<img src="../img/admin/properties.png" alt="<?php
					echo $i18n["Edit template"]?>" title="<?php
					echo $i18n["Edit template"]?>">
			</a>
		</td>
		<td class="img">
			<a href="index.php?a=page_definition_export&page_definition_id=<?php
					echo $value->id?>">
				<img src="../img/admin/database_export.png" alt="<?php
					echo $i18n["Export template"]?>" title="<?php
					echo $i18n["Export template"]?>">
			</a>
		</td>
		<td class="img">
			<a onclick="return confirm('<?php
					echo $i18n["Delete template ?"]?>')"
				href="index.php?a=page_definition_delete&page_definition_id=<?php
					echo $value->id?>">
				<img src="../img/admin/delete.png" alt="<?php
					echo $i18n["Delete template"]?>" title="<?php
					echo $i18n["Delete template"]?>">
			</a>
		</td>
	</tr>
<?php
}
?>
</table>

</td><td style="vertical-align: top; padding-left: 2em">

	<form method="get" action="index.php">
		<input type="hidden" name="a" value="page_definition_form">
		<button><?php echo $i18n["New template"]?></button>
	</form>

	<form enctype="multipart/form-data" action="index.php" method="post">
		<input type="hidden" name="a" value="page_definition_import">
		<table class="form" cellspacing="0">
			<tr>
				<th>
					<?php echo $i18n["Import template definition"]?>
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