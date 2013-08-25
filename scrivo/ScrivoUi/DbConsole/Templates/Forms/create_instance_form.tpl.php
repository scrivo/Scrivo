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
 * $Id: create_instance_form.tpl.php 860 2013-08-24 12:42:15Z geert $
 */

$this->beginSection("content", true);

?>
	<p>
	<?php echo $i18n["Please check, correct and complete the following ".
		"data. These are the entries that will be added to the Scrivo ".
		"configuration file together with the the database connection ".
		"parameters."]?>
	</p>

	<form action="index.php" method="post">
		<input type="hidden" name="a" value="initialize.createInstance">

		<input type="hidden" name="db_host" value="<?php echo $fd["db_host"]?>">
		<input type="hidden" name="db_user" value="<?php echo $fd["db_user"]?>">
		<input type="hidden" name="db_pwd" value="<?php echo $fd["db_pwd"]?>">
		<input type="hidden" name="db_name" value="<?php echo $fd["db_name"]?>">

		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2"><?php echo $i18n["Configuration data"]?></th>
			</tr>
			<tr>
				<td><label for="INSTANCE_ID"><?php
					echo $i18n["Instance id"]?></label></td>
				<td><select id="INSTANCE_ID" name="cfg_inst"><?php
					echo $opts ?></select></td>
			</tr>
			<tr>
				<td><label for="DOC_ROOT"><?php echo
					$i18n["Document root"]?></label></td>
				<td><input size="60" type="text" id="DOC_ROOT"
					name="cfg_path" value="<?php
						echo $fd["cfg_pth"]?>"></td>
			</tr>
			<tr>
				<td><label for="WWW_ROOT"><?php
					echo $i18n["Web root"]?></label></td>
				<td><input size="60" type="text" id="WWW_ROOT"
					name="cfg_root" value="<?php
						echo $fd["cfg_root"]?>"></td>
			</tr>
			<tr>
				<td><label for="UPLOAD_DIR"><?php
					echo $i18n["Upload folder"]?></label></td>
				<td><input size="60" type="text" id="UPLOAD_DIR"
					name="cfg_upload" value="<?php
						echo $fd["cfg_upload"]?>"></td>
			</tr>
			<tr>
				<td><label for="ADMIN_WW"><?php
					echo $i18n["Admin password"]?></label></td>
				<td><input type="text" id="ADMIN_WW"
					name="cfg_pwd" value="<?php
						echo $fd["cfg_pwd"]?>"></td>
			</tr>
		</table>
	<button type='button'
		onclick="this.parentNode.submit();this.disabled=true"><?php
			echo $i18n["Create instance"]?></button>
	</form>
	</form>
<?php

$this->endSection();

?>