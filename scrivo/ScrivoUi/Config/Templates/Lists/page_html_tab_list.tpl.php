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
 * $Id: page_html_tab_list.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\PagePropertyDefinition;

$this->beginSection("content", true);

?>
	<table class="list" cellspacing="0">
		<tr>
			<th class="table-heading"><?php echo $i18n["Tab title"]?></th>
			<th class="table-heading"></th>
		</tr>
<?php
$cnt = 1;
foreach ($page->definition->properties as $prop) {
	if ($prop->type == PagePropertyDefinition::TYPE_HTML_TEXT_TAB) {
?>
		<tr<?php if (++$cnt%2==1) echo ' class="row-color"'?>>
			<td><?php echo $prop->title; ?></td>
			<td align="right">
				<a href="index.php?a=page_html_tab_form&page_id=<?php echo
					$page->id?>&php_selector=<?php echo
					$prop->phpSelector; ?>">
					<img src="../img/admin/properties.png" alt="<?php echo
						$i18n["Edit HTML content"]?>" title="<?php echo
						$i18n["Edit HTML content"]?>">
				</a>
			</td>
		</tr>
<?php
	}
}
?>

</table>
<?php

$this->endSection();

?>