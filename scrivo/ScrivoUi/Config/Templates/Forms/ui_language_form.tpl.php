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
 * $Id: ui_language_form.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\String;

$this->beginSection("content", true);

?>
<form action="?" method="post">
<?php
if (!$languageId->equals(new String(""))) {
?>
	<input type="hidden" name="a" value="ui_language_update">
	<input type="hidden" name="origlang" value="<?php echo
		htmlspecialchars($language->isoCode)?>">
<?php
} else {
?>
		<input type="hidden" name="a" value="ui_language_insert">
<?php
}
?>
	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2">
				<?php echo $i18n["User interface language data"]?>
			</th>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="lang"><?php echo
					$i18n["ISO Code:"]?></label>
			</td>
			<td>
				<input type="text" id="lang" name="lang"
					maxlength="2" size="2" value="<?php echo
						htmlspecialchars($language->isoCode)
					?>">
			</td>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="comment"><?php echo
					$i18n["Description:"]?></label>
			</td>
			<td>
				<input type="text" id="comment" name="comment"
					size="50" maxlength="50" value="<?php echo
						htmlspecialchars($language->description)
					?>">
			</td>
		</tr>
	</table>
	<button type="submit"><?php echo
		$i18n["Save"]?></button>
	<button onclick="document.location='?a=ui_language_list';"
		type="button"><?php echo
		$i18n["Cancel"]?></button>
</form>
<?php

$this->endSection();

?>