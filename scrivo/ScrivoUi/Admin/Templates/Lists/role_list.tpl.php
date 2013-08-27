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
 * $Id: role_list.tpl.php 846 2013-08-20 12:34:06Z geert $
 */

use \Scrivo\Role;
use \Scrivo\String;
use \Scrivo\User;

$this->beginSection("editorRoles", true);

$cnt = 0;
foreach (Role::select($this->context, Role::EDITOR_ROLE) as $role) {
?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td><?php echo $role->title ?></td>
<?php
	if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
			<td class="img">
				<a href="index.php?a=role_form&role_id=<?php echo $role->id ?>">
					<img src="../img/admin/properties.png" alt="<?php echo
						$i18n["Edit role"]?>" title="<?php echo
						$i18n["Edit role"]?>">
				</a>
			</td>
			<td class="img">
				<a onclick="return confirm('<?php echo
						$i18n["Delete role?"]?>')"
					href="index.php?a=role_delete&role_id=<?php echo $role->id ?>">
					<img src="../img/admin/delete.png" alt="<?php echo
						$i18n["Delete role"]?>" border="<?php echo
						$i18n["Delete role"]?>">
				</a>
			</td>
<?php
	}
?>
		</tr>
<?php
}

$this->endSection();

$this->beginSection("publicRoles", false);

$cnt = 0;
foreach (Role::select($this->context, Role::PUBLIC_ROLE) as $role) {
	?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td><?php echo $role->title ?></td>
<?php
	if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
			<td class="img">
				<a href="index.php?a=role_form&role_id=<?php echo $role->id ?>">
					<img src="../img/admin/properties.png" alt="<?php echo
						$i18n["Edit role"]?>" title="<?php echo
						$i18n["Edit role"]?>">
				</a>
			</td>
			<td class="img">
				<a onclick="return confirm('<?php echo
						$i18n["Delete role?"]?>')"
					href="index.php?a=role_delete&role_id=<?php echo $role->id ?>">
					<img src="../img/admin/delete.png" alt="<?php echo
						$i18n["Delete role"]?>" border="<?php echo
						$i18n["Delete role"]?>">
				</a>
			</td>
<?php
	}
?>
		</tr>
<?php
}

$this->endSection();

$this->beginSection("content", true);

if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
	<p><?php echo
		String::create(
				$i18n["Create a new [L]editor role[/L]"])->replace(
			String::create(array("[L]", "[/L]")),
			String::create(array(
				"<a href=\"index.php?a=role_form&type=".Role::EDITOR_ROLE."\">", "</a>"))
		);
	?></p>

<?php
}
?>
	<table class="list" cellspacing="0">
		<tr>
			<th class="table-heading"><?php echo $i18n["Editor roles"]?></th>
<?php
if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
			<th class="table-heading"></th>
			<th class="table-heading"></th>
<?php
}
?>
		</tr>
<?php echo $this->getSection("editorRoles"); ?>
		</table>

<?php
	if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
	<p><?php echo
		String::create(
				$i18n["Create a new [L]public role[/L]"])->replace(
			String::create(array("[L]", "[/L]")),
			String::create(array(
				"<a href=\"index.php?a=role_form&type=".Role::PUBLIC_ROLE."\">", "</a>"))
		);
	?></p>

<?php
}
?>
	<table class="list" cellspacing="0">
		<tr>
			<th class="table-heading"><?php echo $i18n["Public roles"]?></th>
<?php
if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
			<th class="table-heading"></th>
			<th class="table-heading"></th>
<?php
}
?>
		</tr>
<?php echo $this->getSection("publicRoles"); ?>
	</table>
<?php

$this->endSection();

?>