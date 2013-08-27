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
 * $Id: sync_form.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

$this->beginSection("content", true);

?>
<form action="index.php" method="post">
<input type="hidden" name="a" value="sync_switch">
<table class="form" cellspacing="0">
	<tr>
		<th colspan="2">
			<?php echo $i18n
["Synchronize this configuration with the configuration data of another site"]
			?>
		</th>
	</tr>
	<tr>
		<td width="30%" class="label">
			<label for="site"><?php echo $i18n["Site:"]?></label>
		</td>
		<td>
			<input type="text" id="site" name="site" size="80"
				maxlength="250" value="">
		</td>
	</tr>
	<tr>
		<td width="30%" class="label">
		</td>
		<td>
			<input type="radio" id="page_definitions" name="what"
				value="page_definition" checked>
				<label for="page_definitions"><?php echo
					$i18n["Page definitions"]?></label><br />
			<input type="radio" id="lists" name="what"
				value="list_definition"> <label for="lists"><?php echo
				$i18n["lists"]?></label>
		</td>
	</tr>
</table>
<button type="submit"><?php
	echo $i18n["Synchronize"]?></button>
</form>
<?php

$this->endSection();

?>