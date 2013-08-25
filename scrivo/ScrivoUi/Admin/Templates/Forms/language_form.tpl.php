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
 * $Id: language_form.tpl.php 846 2013-08-20 12:34:06Z geert $
 */

use \Scrivo\Language;

$this->beginSection("content", true);

?>
	<form method="post" action="?">
		<input type="hidden" name="a" value="language_update">
		<input type="hidden" name="page_id" value="<?php echo $page->id?>">

		<table class="form" cellspacing="0" border="0">
			<tr>
				<th colspan="2"><?php echo $i18n["Page language setting"]?></th>
			</tr>
			<tr>
				<td class="label">
					<label for="language_id">
						<?php echo $i18n["Language:"]?>
					</label>
				</td>
				<td>
					<select id="language_id" name="language_id">
<?php
foreach (Language::select($this->context) as $lang) {
	echo "<option ".
		($lang->id == $page->language->id?"selected ":"").
		"value=\"{$lang->id}\">{$lang->isoCode}: {$lang->nameEn}</option>\n";
}
?>
					</select>
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
		<button type="button" onclick="document.location='?a=page_list'">
			<?php echo $i18n["Cancel"]?>
		</button>

	</form>
<?php

$this->endSection();

?>