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
 * $Id: user_form.tpl.php 845 2013-08-20 00:47:20Z geert $
 */

use \Scrivo\Role;
use \Scrivo\User;

$this->beginSection("content", true);
?>
	<script>
function setdisp(e) {

var ed = document.getElementById("roweditors");
var me = document.getElementById("rowmembers");

var isMSie = /*@cc_on!@*/false;

ed.style.display=e.value==<?php
	echo User::STATUS_EDITOR?>?(isMSie?"block":"table-row"):"none";
me.style.display=e.value==<?php
	echo User::STATUS_MEMBER?>?(isMSie?"block":"table-row"):"none";

}
	</script>

	<form method="post" action="?">

<?php
if ($user->id) {
?>
		<input type="hidden" name="a" value="<?php echo $type?>_update">
		<input type="hidden" name="user_id" value="<?php echo $user->id?>">
<?php
} else {
?>
		<input type="hidden" name="a" value="<?php echo $type?>_insert">
		<input type="hidden" name="status" value="<?php echo $user->status?>">
<?php
}
?>

		<table class="form" cellspacing="0" border="0">
			<tr>
				<th colspan="2"><?php echo $userData[$type]->title2?></th>
			</tr>
			<tr>
				<td width="20%" class="label"><label for="user_code"><?php
					echo $i18n["User code"]?></label></td>
				<td><input type="text" id="user_code"
						name="user_code" size="40" maxlength="50"
					value="<?php
					echo(htmlspecialchars($user->userCode))?>"></td>
			</tr>
			<tr>
				<td class="label"><label for="given_name"><?php
					echo $i18n["Given name"]?></label></td>
				<td><input type="text" id="given_name"
						name="given_name" size="25" maxlength="50"
					value="<?php
					echo(htmlspecialchars($user->givenName))?>"></td>
			</tr>
			<tr>
				<td class="label"><label for="family_name_prefix"><?php
					echo $i18n["Family name prefix"]?></label></td>
				<td><input type="text" id="family_name_prefix"
						name="family_name_prefix" size="5" maxlength="50"
					value="<?php
					echo(htmlspecialchars($user->familyNamePrefix))?>"></td>
			</tr>
			<tr>
				<td class="label"><label for="family_name"><?php
					echo $i18n["Family name"]?></label></td>
				<td><input type="text" id="family_name"
						name="family_name" size="40" maxlength="100"
					value="<?php
					echo(htmlspecialchars($user->familyName))?>">
				</td>
			</tr>
			<tr>
				<td class="label"><label for="email_address"><?php
					echo $i18n["E-mail address"]?></label></td>
				<td><input type="text" id="email_address"
						name="email_address" size="40" maxlength="100"
					value="<?php
					echo(htmlspecialchars($user->emailAddress))?>"></td>
			</tr>
<?php
if ($user->id) {
?>
			<tr>
				<td class="label"><label for="status"><?php
					echo $i18n["Status"]?></label></td>
				<td>
				<select  onchange="setdisp(this)"  id="status" name="status">
					<option value="1"<?php
						echo $user->status==User::STATUS_ADMIN
							?" selected":"";?>><?php echo $i18n["Super user"]
					?></option>
					<option value="2"<?php
						echo $user->status==User::STATUS_EDITOR
							?" selected":"";?>><?php echo $i18n["Editor"]
					?></option>
					<option value="3"<?php
						echo $user->status==User::STATUS_MEMBER
							?" selected":"";?>><?php echo $i18n["Member"]
					?></option>
				</select></td>
			</tr>
<?php
} else {
?>
			<tr>
				<td width="25%" class="label"><label for="pwd1"><?php
					echo $i18n["Password:"]?></label></td>
				<td><input type="password" id="pwd1" name="pwd1"
					size="10" maxlength="50" value=""></td>
			</tr>
			<tr>
				<td class="label"><label for="pwd2"><?php echo
					$i18n["Repeat password:"]?></label></td>
				<td><input type="password" id="pwd2" name="pwd2"
					size="10" maxlength="50" value=""></td>
			</tr>
<?php
}
?>
			<tr id="roweditors" style="<?php echo
				$user->status==User::STATUS_EDITOR?"":"display:none"?>">
				<td class="label"><label for="edit_roles"><?php echo
					$i18n["Editor roles:"]?></label></td>
				<td>
					<table cellspacing="0">
						<tr>
							<th></th>
							<th><?php echo $i18n["Editor"]?></th>
							<th><?php echo $i18n["Publisher (staging)"]?></th>
						</tr>
<?php

foreach (Role::select(
	$this->context, Role::EDITOR_ROLE) as $role) {
?>
						<tr>
							<td><?php echo $role->title; ?></td>
							<td><input type="checkbox" id="edit_roles"
								name="roles[]" value="<?php
									echo $role->id; ?>"
								<?php if (isset($user->roles[$role->id]))
									echo " checked"; ?>></td>
							<td><input type="checkbox" id="edit_publisher_roles"
								name="publisher_roles[]" value="<?php
								echo $role->id; ?>"<?php
								if (isset($user->roles[$role->id]) &&
									$user->roles[$role->id]->isPublisher)
								echo " checked"; ?>>
							</td>
						</tr>
<?php
}
?>
					</table>
				</td>
			</tr>
			<tr id="rowmembers" style="<?php
				echo $user->status==User::STATUS_MEMBER
					? "" : "display:none"?>">
				<td class="label"><label for="public_roles"><?php
					echo $i18n["Public/member roles:"]?></label></td>
				<td>
<?php
foreach (Role::select(
	$this->context, Role::PUBLIC_ROLE) as $role) {
?>
					<input type="checkbox" id="public_roles" name="roles[]"
						value="<?php
						echo $role->id; ?>"<?php
						if (isset($user->roles[$role->id]))
							echo " checked"; ?>><?php
						echo $role->title; ?><br>
<?php
}
?>
				</td>
			</tr>
		</table>

		<button type="submit">
			<?php echo $i18n["Save"]?>
		</button>
		<button type="button" onclick="document.location='?a=<?php
				echo $type?>_list'">
			<?php echo $i18n["Cancel"]?>
		</button>

	</form>

<?php
if ($user->id) {
?>

	<form action="?" method="post">

		<input type="hidden" name="a" value="<?php echo
			$type?>_password_update">
		<input type="hidden" name="user_id" value="<?php
			echo $user->id?>">

		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2"><?php echo $userData[$type]->title3?></th>
			</tr>
			<tr>
				<td width="25%" class="label"><label for="pwd1"><?php
					echo $i18n["Password:"]?></label></td>
				<td><input type="password" id="pwd1" name="pwd1"
					size="10" maxlength="50" value=""></td>
			</tr>
			<tr>
				<td class="label"><label for="pwd2"><?php echo
					$i18n["Repeat password:"]?></label></td>
				<td><input type="password" id="pwd2" name="pwd2"
					size="10" maxlength="50" value=""></td>
			</tr>
		</table>

		<button type="submit">
			<?php echo $i18n["Save"]?>
		</button>
		<button type="button" onclick="document.location='?a=<?php
			echo $type?>_list'"><?php echo $i18n["Cancel"]?>
		</button>

	</form>
<?php
}

$this->endSection();

?>