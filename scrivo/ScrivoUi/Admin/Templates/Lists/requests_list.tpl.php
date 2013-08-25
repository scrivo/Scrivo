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
 * $Id: requests_list.tpl.php 846 2013-08-20 12:34:06Z geert $
 */

use \Scrivo\User;

$this->beginSection("requestList", true);

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
			<td class="img"><a href="?a=request_activate&user_id=<?php
					echo $user->id?>">
				<img src="../img/admin/properties.png" alt="<?php echo
					$i18n["Activate user"]?>" title="<?php echo
					$i18n["Activate user"]?>"></a></td>
			<td class="img">
				<a onclick="return confirm('<?php echo
					$i18n["Delete user?"]?>')"
					href="?a=request_delete&user_id=<?php echo $user->id?>">
				<img src="../img/admin/delete.png" alt="<?php echo
					$i18n["Delete user"]?>" title="<?php echo
					$i18n["Delete user"]?>"></a>
			</td>
<?php
	}
?>
		</tr>
<?php
}

$this->endSection();

$this->beginSection("content", true);

?>
	<table class="list" cellspacing="0">

		<tr>
			<th class="table-heading"><?php echo $i18n["user code"]?></th>
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
<?php echo $this->getSection("requestList"); ?>
	</table>

<?php

$this->endSection();

?>