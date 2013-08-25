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
 * $Id: role_form.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\Role;

$this->beginSection("content", true);

?>

	<form method="post" action="?">
<?php
if ($role->id) {
?>
		<input type="hidden" name="a" value="role_update">
		<input type="hidden" name="role_id" value="<?php echo $role->id?>">
<?php
} else {
?>
		<input type="hidden" name="a" value="role_insert">
<?php
}
?>
		<input type="hidden" name="type" value="<?php echo($role->type)?>">

		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2"><?php echo
					$role->type == Role::EDITOR_ROLE
					? $i18n["Editor role data"]
					: $i18n["Public role data"]?></th>
			</tr>
			<tr>
				<td width="30%" class="label"><label for="title"><?php
					echo $i18n["Title:"]?></label></td>
				<td><input type="text" id="title" name="title" size="20"
					maxlength="50" value="<?php
					echo(htmlspecialchars($role->title))?>"></td>
			</tr>
			<tr>
				<td width="30%" class="label"><label for="description"><?php
					echo $i18n["Description:"]?></label></td>
				<td><input type="text" id="description" name="description"
					size="40" maxlength="50" value="<?php
					echo(htmlspecialchars($role->description))?>"></td>
			</tr>
		</table>

		<button type="submit">
			<?php echo $i18n["Save"]?>
		</button>
		<button type="button" onclick="document.location='?a=role_list'">
			<?php echo $i18n["Cancel"]?>
		</button>

	</form>
<?php
$this->endSection();
?>