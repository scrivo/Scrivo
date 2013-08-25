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
 * $Id: create_database_form.tpl.php 860 2013-08-24 12:42:15Z geert $
 */

$this->beginSection("content", true);

?>
	<p>
	<?php echo $i18n["Please enter the MySQL root credentials and the ".
		"choose the connection details for the Scrivo database to create."]?>
	</p>

	<form action="index.php" method="post">
		<input type="hidden" name="a" value="initialize.createDatabase">
		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2"><?php echo $i18n["Database parameters"]?></th>
			</tr>
			<tr>
				<td><?php echo $i18n["MySQL root user"]?></td>
				<td><input name="root_user" value="<?php
					echo $fd["root_user"]?>"></td>
			</tr><tr>
				<td><?php echo $i18n["MySQL root password"]?></td>
				<td><input name="root_pwd" type="password" value="<?php
					echo $fd["root_pwd"]?>"></td>
			</tr><tr>
				<td><?php echo $i18n["MySQL host"]?></td>
				<td><input name="db_host" value="<?php
					echo $fd["db_host"]?>"></td>
			</tr><tr>
				<td><?php echo $i18n["Scrivo database name"]?></td>
				<td><input name="db_name" value="<?php
					echo $fd["db_name"]?>"></td>
			</tr><tr>
				<td><?php echo $i18n["Scrivo database user"]?></td>
				<td><input name="db_user" value="<?php
					echo $fd["db_user"]?>"></td>
			</tr><tr>
				<td><?php echo $i18n["Scrivo database password"]?></td>
				<td><input name="db_pwd" value="<?php
					echo $fd["db_pwd"]?>"></td>
			</tr>
		</table>
		<button type='button'
			onclick="this.parentNode.submit();this.disabled=true"><?php
				echo $i18n["Continue"]?></button>
	</form>
<?php

$this->endSection();

?>
