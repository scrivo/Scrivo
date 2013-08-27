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
 * $Id: page_html_tab_form.tpl.php 852 2013-08-21 12:43:09Z geert $
 */

/**
 * Form for editing the raw HTML of page HTML tabs .
 */

$this->beginSection("content", true);

?>

<form action="index.php" method="post">

	<input type="hidden" name="page_id" value="<?php echo $page->id?>">
	<input type="hidden" name="php_selector" value="<?php echo $phpSelector?>">
	<input type="hidden" name="a" value="page_html_tab_update">

	<table class="form" cellspacing="0">


	<!-- label for="cleaned_html">Cleaned:</label><br>
	<textarea id="cleaned_html" name="cleaned_html" style="width:100%" rows="5"
		><?php //echo(htmlentities($item->cleaned_html))?></textarea -->

	<!-- label for="stripped">Stripped:</label><br>
	<textarea id="stripped" name="stripped" style="width:100%" rows="5"
		><?php //echo(htmlentities($item->stripped))?></textarea -->

			<tr>
				<th><?php echo $i18n["Edit HTML content"]?></th>
			</tr>
			<tr>
				<td><textarea id="raw_html" name="raw_html"
					rows="25" cols="80"><?php echo htmlspecialchars(
						$page->properties->{$phpSelector}->rawHtml)
					?></textarea></td>
			</tr>
	</table>

	<button type="submit">
		<?php echo $i18n["Save"]?>
	</button>
	<button type="button"
		onclick="document.location='index.php?a=page_html_tab_list&page_id=<?php
			echo $page->id?>';">
		<?php echo $i18n["Cancel"]?>
	</button>

</form>
<?php

$this->endSection();

?>