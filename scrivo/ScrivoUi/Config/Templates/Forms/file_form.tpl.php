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
 * $Id: file_form.tpl.php 852 2013-08-21 12:43:09Z geert $
 */

use \Scrivo\File;

$this->beginSection("content", true);

?>

<form action="?" method="post">

<?php
	if ($file->id) {
?>
	<input type="hidden" name="a" value="file_update"> <input type="hidden"
		name="file_id" value="<?php echo $file->id?>">
<?php
} else {
?>
	<input type="hidden" name="a" value="file_insert">
<?php
}
?>

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["File data"]?></th>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="title"><?php
				echo $i18n["Title:"]?>
			</label></td>
			<td><input type="text" id="title" name="title" size="40"
				maxlength="255" value="<?php
				echo htmlspecialchars($file->title)?>">
			</td>
		</tr>
		<tr>
			<td class="label"><label for="file_pid"><?php
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
				<input type="hidden" id="file_pid" name="file_pid" value="<?php
					echo $file->parentId ? $file->parentId
						: $this->context->config->ROOT_FOLDER_ID ?>"
				><button type="button" onclick="selectFolder(this)"><?php
					echo $i18n["Select"]?></button><span><?php
					try {
						$tmp = File::fetch(
							$this->context, $file->parentId); echo $tmp->title;
					} catch (\Exception $e) {} ?>
			</span></td>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="title"><?php
				echo $i18n["Label:"]?>
			</label></td>
			<td><input type="text" id="label" name="label" size="30"
				maxlength="255"
				value="<?php echo htmlspecialchars(
					isset($labels[$file->id]) ? $labels[$file->id] : "")
				?>"></td>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="size"><?php
				echo $i18n["Size:"]?>
			</label></td>
			<td><?php echo htmlspecialchars($file->size) ?> kB</td>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="mime_type"><?php
				echo $i18n["Mime type:"]?>
			</label></td>
			<td><input type="text" id="mime_type" name="mime_type" size="30"
				maxlength="255"
				value="<?php echo htmlspecialchars($file->mimeType) ?>"></td>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="location"><?php
				echo $i18n["Location:"]?>
			</label></td>
			<td><input type="text" id="location" name="location" size="30"
				maxlength="255"
				value="<?php echo htmlspecialchars($file->location) ?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="created"><?php
				echo $i18n["Created (y-m-d):"]?></label></td>
			<td><?php echo $file->dateCreated->format("Y")?>
				- <?php echo $file->dateCreated->format("m")?>
				- <?php echo $file->dateCreated->format("d")?>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="modified"><?php
				echo $i18n["Last modified (y-m-d):"]?></label></td>
			<td><?php echo $file->dateModified->format("Y")?>
				- <?php echo $file->dateModified->format("m")?>
				- <?php echo $file->dateModified->format("d")?>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="online_on"><?php
				echo $i18n["Online on (y-m-d):"]?></label></td>
			<td><input type="text" name="online_on_y" size="4" maxlength="4"
				value="<?php echo $file->dateOnline->format("Y")?>"> -
				<input type="text" name="online_on_m" size="2" maxlength="2"
				value="<?php echo $file->dateOnline->format("m")?>"> -
				<input type="text" id="online_on" name="online_on_d" size="2"
				maxlength="2"
				value="<?php echo $file->dateOnline->format("d")?>">
			</td>
		</tr>
		<tr>
			<td class="label"><label for="remove_on"><?php
				echo $i18n["Offline on (y-m-d):"]?></label></td>
			<td><input type="text" name="remove_on_y" size="4" maxlength="4"
				value="<?php echo $file->dateOffline?
					$file->dateOffline->format("Y"):""?>"> -
				<input type="text" name="remove_on_m" size="2" maxlength="2"
				value="<?php echo $file->dateOffline?
					$file->dateOffline->format("m"):""?>"> -
				<input type="text" id="remove_on" name="remove_on_d" size="2"
					 maxlength="2"
				value="<?php echo $file->dateOffline?
					$file->dateOffline->format("d"):""?>">
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