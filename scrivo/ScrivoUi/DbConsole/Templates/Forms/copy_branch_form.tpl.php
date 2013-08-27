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
 * $Id: copy_branch_form.tpl.php 866 2013-08-25 16:22:35Z geert $
 */

$this->beginSection("content", true);

?>
	<form action="index.php" method="post">
		<input type="hidden" name="a" value="copy_branch">
		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2"><?php echo
					$i18n["Export a branch from the content tree"]?></th>
			</tr>
			<tr>
				<td class="label"><label for="page_id"><?php
					echo $i18n["Start page"]?>
				</label></td>
				<td><script type="text/javascript">

function selectPage(e) {
		var d = new SUI.editor.PageDialog({
			pageId: parseInt(e.previousSibling.value, 10),
			onOK: function(data) {
				e.previousSibling.value = data.id;
				e.nextSibling.innerHTML = data.title;
			}
		});
		d.center();
		d.show();
}

</script>
				<input type="hidden" id="page_id" name="page_id" value=""
				><button type="button" onclick="selectPage(this)"><?php
					echo $i18n["Select"]?></button><span></span></td>
			</tr>
<?php
if ($data) {
?>
			<tr>
				<td class="label"><?php echo $i18n["Web root"]?></td>
				<td><?php echo $data["www_root"]?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $i18n["Root folder"]?></td>
				<td><?php echo $data["document_root"]?></td>
			</tr>
<?php
}
?>
			<tr>
				<td class="label"><?php echo $i18n["Folder for uploads"]?></td>
				<td><?php echo $this->context->config->UPLOAD_DIR?></td>
			</tr>
		</table>
<?php
if ($data) {
?>
		<button type='button'
			onclick="this.parentNode.submit();this.disabled=true"><?php
				echo $i18n["Export"]?>
<?php
}
?>
	</form>
<?php

$this->endSection();

?>
