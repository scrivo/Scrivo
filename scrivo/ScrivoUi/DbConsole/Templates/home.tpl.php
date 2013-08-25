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
 * $Id: home.tpl.php 860 2013-08-24 12:42:15Z geert $
 */

/**
 * Home view: cleary show the location of the site.
 */
$this->beginSection("content", true);

if (false && $this->session->authenticated) {

?>
	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["Configuration details"]?></th>
		</tr>
<?php
	foreach ($this->context->labels as $k=>$v) {
?>
		<tr>
			<?php echo "<td class='label'>$k</td><td>$v&nbsp</td>" ?>
		</tr>
<?php
	}
?>

	</table>

<?php

} else {

?>

<div style="font-size:10em; margin-top:.2em">
	<span class="scrivologo">scr<span class="it">i</span>vo<sup>®</sup></span>
</div>

<h1 style="text-align: center"><?php echo
	$i18n["Scrivo database console"] ?></h1>

<h2 style="text-align: center"><?php echo $title ?></h2>

<?php

}

$this->endSection();

?>
