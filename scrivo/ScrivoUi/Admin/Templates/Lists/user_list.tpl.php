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
 * $Id: user_list.tpl.php 846 2013-08-20 12:34:06Z geert $
 */

use \Scrivo\User;
use \Scrivo\String;

$this->beginSection("userList", true);

$cnt = 0;
foreach ($items as $user) {
?>
		<tr<?php if (++$cnt%2==1) echo '  class="row-color"'?>>
			<td><?php echo $user->userCode; ?></td>
			<td><?php echo $user->givenName; ?></td>
			<td><?php echo $user->familyNamePrefix; ?></td>
			<td><?php echo $user->familyName; ?></td>
<?php
	if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
			<td class="img"><a href="<?php echo $form_action?>&user_id=<?php
					echo $user->userCode?>">
				<img src="../img/admin/properties.png" alt="<?php echo
					$i18n["Edit user"]?>" title="<?php echo
					$i18n["Edit user"]?>"></a></td>
			<td class="img">
<?php
		if ($user->id != User::ANONYMOUS_USER_ID &&
			$user->id != User::PRIMARY_ADMIN_ID) {
?>
				<a onclick="return confirm('<?php echo
					$i18n["Delete user?"]?>')"
					href="?a=<?php echo $type?>_delete&user_id=<?php echo
						$user->id?>">
				<img src="../img/admin/delete.png" alt="<?php echo
					$i18n["Delete user"]?>" title="<?php echo
					$i18n["Delete user"]?>"></a>
<?php
		}
?>
			</td>
<?php
	}
?>
	</tr>
<?php
}

$this->endSection();

$this->beginSection("content", true);

if ($this->session->userStatus == User::STATUS_ADMIN && $type != "requests") {
?>
	<p><?php echo
		String::create($userData[$type]->title3)->replace(
			String::create(array("[L]", "[/L]")),
			String::create(array("<a href=\"?a={$type}_form\">", "</a>"))
		);
	?></p>

<?php
}
?>
	<table class="list" cellspacing="0">

		<tr>
			<th class="table-heading">
				<?php echo $i18n["user code"]?></th>
			<th class="table-heading"><?php echo $i18n["given name"]?></th>
			<th class="table-heading"><?php echo $i18n["prefix"]?></th>
			<th class="table-heading"><?php echo $i18n["family name"]?></th>
<?php
if ($this->session->userStatus == User::STATUS_ADMIN) {
?>
			<th class="table-heading"></th>
			<th class="table-heading"></th>
<?php
}
?>
		</tr>
<?php echo $this->getSection("userList"); ?>
	</table>
<?php

$this->endSection();

?>