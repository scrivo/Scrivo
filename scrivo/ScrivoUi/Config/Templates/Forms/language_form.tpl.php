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
 * $Id: language_form.tpl.php 852 2013-08-21 12:43:09Z geert $
 */

$this->beginSection("content", true);

?>
<form action="index.php" method="post">
<?php
if ($language->id) {
?>
	<input type="hidden" name="a" value="language_update">
	<input type="hidden" name="language_id" value="<?php
		echo $language->id?>">
<?php
} else {
?>
	<input type="hidden" name="a" value="language_insert">
<?php
}
?>
	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2">
				<?php echo $i18n["Language data"]?>
			</th>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="short_list"><?php echo
					$i18n["Active:"]?></label>
			</td>
			<td>
				<input id="short_list" name="short_list" type="checkbox"<?php
					if ($language->shortList) echo(" checked");?>>
			</td>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="code"><?php echo
					$i18n["ISO Code:"]?></label>
			</td>
			<td>
				<input type="text" id="code" name="code"
					size="15" maxlength="25" value="<?php echo
						htmlspecialchars($language->isoCode)
					?>">
			</td>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="family"><?php echo
					$i18n["Language family:"]?></label>
			</td>
			<td>
				<input type="text" id="family" name="family"
					size="30" maxlength="50" value="<?php echo
						htmlspecialchars($language->family)
					?>">
			</td>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="en"><?php echo
					$i18n["Name (English):"]?></label>
			</td>
			<td>
				<input type="text" id="en" name="en"
					size="30" maxlength="50" value="<?php echo
						htmlspecialchars($language->nameEn)
					?>">
			</td>
		</tr>
		<tr>
			<td width="30%" class="label">
				<label for="nl"><?php echo
					$i18n["Name (Dutch):"]?></label>
			</td>
			<td>
				<input type="text" id="nl" name="nl"
					size="30" maxlength="50" value="<?php echo
						htmlspecialchars($language->nameNl)
					?>">
			</td>
		</tr>
	</table>
	<button type="submit"><?php echo
		$i18n["Save"]?></button>
	<button onclick="document.location='index.php?a=language_list';"
		type="button"><?php echo
		$i18n["Cancel"]?></button>
</form>
<?php

$this->endSection();

?>