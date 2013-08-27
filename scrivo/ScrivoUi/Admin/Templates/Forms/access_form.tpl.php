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
 * $Id: access_form.tpl.php 846 2013-08-20 12:34:06Z geert $
 */

use \Scrivo\Role;

$this->beginSection("content", true);

?>
	<form method="post" action="index.php">
		<input type="hidden" name="a" value="<?php echo $type?>_access_update">
		<input type="hidden" name="<?php echo $type?>_id" value="<?php
			echo $obj->id?>">

		<table class="form" cellspacing="0">
			<tr>
				<th colspan="2" class="table-heading"><?php
					echo $type == "page"
						? $i18n["Page access"] : $i18n["Asset access"]?></th>
			</tr>
			<tr>
				<td class="label">
					<label>
						<?php echo $i18n["Editor roles"]?>
					</label>
				</td>
				<td>
<?php
foreach (Role::select(
	$this->context, Role::EDITOR_ROLE) as $role) {
?>
					<input id="id2_<?php echo $role->id?>" type="checkbox"
						name="roles[]" value="<?php
						echo $role->id ?>"
					<?php if (isset($obj->roles[$role->id])) echo " checked";
						?>><label for="id2_<?php echo $role->id?>"><?php
							echo $role->title ?></label><br>
<?php
}
?>
				</td>
			</tr>
			<tr>
				<td class="label">
					<label>
						<?php echo $i18n["Public roles"]?>
					</label>
				</td>
				<td>
<?php
foreach (Role::select(
	$this->context, Role::PUBLIC_ROLE) as $role) {
?>
					<input type="checkbox" id="id1_<?php
						echo $role->id?>" name="roles[]" value="<?php
						echo $role->id ?>"
					<?php if (isset($obj->roles[$role->id])) echo " checked";
						?>><label for="id1_<?php echo $role->id?>"><?php
							echo $role->title ?></label><br>
<?php
}
?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="checkbox" name="rec" value="rec"
						id="rec"><label for="rec"><?php
						echo $i18n["Set this value for all child pages as well"]
					?></label>
				</td>
			</tr>

		</table>

		<button type="submit">
			<?php echo $i18n["Save"]?>
		</button>
		<button type="button" onclick="document.location='index.php?a=page_list'">
			<?php echo $i18n["Cancel"]?>
		</button>

	</form>

<?php

$this->endSection();

?>