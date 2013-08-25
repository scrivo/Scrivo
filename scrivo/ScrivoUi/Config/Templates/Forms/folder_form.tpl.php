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
 * $Id: folder_form.tpl.php 852 2013-08-21 12:43:09Z geert $
 */

use \Scrivo\Folder;

$this->beginSection("content", true);

?>

<form action="?" method="post">

	<?php
	if ($folder->id) {
?>
	<input type="hidden" name="a" value="folder_update"> <input type="hidden"
		name="folder_id" value="<?php echo $folder->id?>">
	<?php
} else {
?>
	<input type="hidden" name="a" value="folder_insert">
	<?php
}
?>

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["Folder data"]?></th>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="title"><?php
				echo $i18n["Title:"]?>
			</label></td>
			<td><input type="text" id="title" name="title" size="40"
				maxlength="255" value="<?php
					echo(htmlspecialchars($folder->title))?>">
			</td>
		</tr>
		<tr>
			<td class="label"><label for="folder_pid"><?php
				echo $i18n["Parent folder:"]?>
			</label></td>
			<td><script type="text/javascript">

function selectFolder(e) {
	   var d = new SUI.editor.FolderDialog({
		   assetId: parseInt(e.previousSibling.value, 10),
		   onOK: function(data) {
				e.previousSibling.value = data.id;
				e.nextSibling.innerHTML = data.title;
			}
		});
		d.center();
		d.show();
}
			</script>
			<input type="hidden" id="folder_pid" name="folder_pid" value="<?php
				echo $folder->parentId ? $folder->parentId
					: $this->context->config->ROOT_FOLDER_ID ?>"
			><button type="button" onclick="selectFolder(this)"><?php
				echo $i18n["Select"]?></button><span><?php
				try {
					$tmp = Folder::fetch(
						$this->context, $folder->parentId);
					echo $tmp->title;
				} catch (\Exception $e) {} ?>
			</span></td>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="title"><?php echo
				$i18n["Label:"]?>
			</label></td>
			<td><input type="text" id="label" name="label" size="30"
				maxlength="255"
				value="<?php
					echo(htmlspecialchars(isset($labels[$folder->id])
						? $labels[$folder->id] : "")) ?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="created"><?php
				echo $i18n["Created (y-m-d):"]?></label></td>
			<td><?php echo $folder->dateCreated->format("Y")?>
				- <?php echo $folder->dateCreated->format("m")?>
				- <?php echo $folder->dateCreated->format("d")?>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="modified"><?php
				echo $i18n["Last modified (y-m-d):"]?></label></td>
			<td><?php echo $folder->dateModified->format("Y")?>
				- <?php echo $folder->dateModified->format("m")?>
				- <?php echo $folder->dateModified->format("d")?>
			</td>
		</tr>
	</table>

	<button type="submit">
		<?php echo $i18n["Save"]?>
	</button>
	<button type="button" onclick="document.location='?a=asset_list';">
		<?php echo $i18n["Cancel"]?>
	</button>

</form>
<?php

$this->endSection();

?>