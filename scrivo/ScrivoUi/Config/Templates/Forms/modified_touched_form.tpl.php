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
 * $Id: modified_touched_form.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\ModifiedTouched;

$this->beginSection("content", true);

?>
<form action="?" method="post">
	<input type="hidden" name="modified_type" value="<?php
		echo ModifiedTouched::TYPE_DOCUMENT_ID?>">
	<input type="hidden" name="touch_type" value="<?php
		echo ModifiedTouched::TYPE_TEMPLATE_ID?>">
	<input type="hidden" name="a" value="modified_touched_insert">
	<table class="list" cellspacing="0">
		<tr>
			<th class="table-heading"><?php echo
				$i18n["When updating"];?></th>
			<th class="table-heading"><?php echo
				$i18n["Modification date will be updated too for"];?></th>
			<th class="table-heading"></th>
		</tr>
<?php
$cnt = 0;
foreach ($mtList as $value) {
?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td><?php echo
				$labels[$value->idModified]
			?></td>
			<td><?php echo
				@$templates[$value->idTouched]->title . ": " .
				@$templates[$value->idTouched]->action
			?></td>
			<td>
				<a onclick="return confirm('<?php echo
						$i18n["delete item?"]?>')"
					href="?a=modified_touched_delete&modified_id=<?php
						echo $value->idModified?>&touch_id=<?php
						echo $value->idTouched?>">
					<img src="../img/admin/delete.png"
						alt="<?php echo
							$i18n["delete"]?> title="<?php echo
							$i18n["delete"]?>">
				</a>
			</td>
		</tr>
<?php
}
?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td>
				<select name="modified_id">
<?php
foreach ($idLabel as $label => $id) {
	echo "<option value=\"$id\">{$label}</option>";
}
?>
				</select>
			</td>
			<td>
				<select name="touch_id">
<?php
foreach ($templates as $id => $t) {
	echo "<option value=\"$id\">{$t->title}: {$t->action}</option>";
}
?>
				</select>
			</td>
			<td colspan="1">
				<button type="submit"><?php
					echo $i18n["Add"]?></button>
			</td>
		</tr>
	</table>
</form>
<?php

$this->endSection();

?>