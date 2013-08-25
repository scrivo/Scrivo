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
 * $Id: import_form.tpl.php 862 2013-08-24 16:27:35Z geert $
 */

$this->beginSection("content", true);

?>
	<form enctype="multipart/form-data" action="index.php" method="post">
		<input type="hidden" name="a" value="import.importInstance">
		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2"><?php echo
					$i18n["Import instance data"]?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $i18n["Instance id"]?></td>
				<td><?php echo $config->INSTANCE_ID?></td>
			</tr>
						<tr>
				<td class="label"><?php echo $i18n["Web root"]?></td>
				<td><?php echo $config->WWW_ROOT?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $i18n["Upload folder"]?></td>
				<td><?php echo $config->UPLOAD_DIR?></td>
			</tr>
			<tr><td class="label"><?php
					echo $i18n["Upload instance data, or"]?></td>
				<td>
					<input type="hidden" name="MAX_FILE_SIZE"
						value="100000000" />
					<input name="userfile" type="file" />
				</td>
			</tr>
			<tr>
				<td class='label'><?php
					echo $i18n["use instance data on the server"]?></td>
				<td>
					<input name="serverfile" type="text" size="64" />
				</td>
			</tr>
		</table>
		<button type='button'
			onclick="this.parentNode.submit();this.disabled=true"><?php
				echo $i18n["Import"]?>
	</form>
<?php

$this->endSection();

?>