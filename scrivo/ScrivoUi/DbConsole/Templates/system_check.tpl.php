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
 * $Id: system_check.tpl.php 860 2013-08-24 12:42:15Z geert $
 */

$this->beginSection("content", true);

?>

	<?php if (!isset($this->session->errorCode)) { ?>
	<p><?php echo $i18n["To install Scrivo on your system the following ".
		"system requirements need to be met."]?></p>
	<?php } ?>

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["System check"]?></th>
		</tr>
		<tr>
			<td><?php echo $i18n["PHP version"].
				" (".implode(".", $si->phpVersion).")"?></td>
			<td>
			<?php if ($si->phpVersion[0] >= 5 && $si->phpVersion[1] >= 3) { ?>
				<img src="../img/admin/tick.png">
			<?php } else { ?>
				<img src="../img/admin/no_access.png">
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $i18n["PHP library: zlib"]?></td>
			<td>
			<?php if ($si->zlib) { ?>
				<img src="../img/admin/tick.png">
			<?php } else { ?>
				<img src="../img/admin/no_access.png">
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $i18n["PHP library: PDO"]?></td>
			<td>
			<?php if ($si->pdo) { ?>
				<img src="../img/admin/tick.png">
			<?php } else { ?>
				<img src="../img/admin/no_access.png">
			<?php } ?>
			</td>
		</tr>
		<tr>
		<td><?php echo $i18n["PHP library: PDO MySQL driver"]?></td>
		<td>
			<?php if ($si->pdoMySql) { ?>
				<img src="../img/admin/tick.png">
			<?php } else { ?>
				<img src="../img/admin/no_access.png">
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td><?php echo
				$i18n["Write permission installation directory"]?><br>
				&nbsp;&nbsp;(<?php echo $si->homeDir ?>)
				</td>
			<td>
			<?php if ($si->homeWritable) { ?>
				<img src="../img/admin/tick.png">
			<?php } else { ?>
				<img src="../img/admin/no_access.png">
			<?php } ?>
			</td>
		</tr>
	</table>
<?php
if (isset($this->session->errorCode)) {
?>
	<button type='button'
		onclick="document.location='index.php?a=initialize.sysCheck'"><?php
			echo $i18n["Retry"]?></button>
<?php
} else {
?>
	<button type='button' onclick="document.location=
		'index.php?a=initialize.installationOptions'"><?php
			echo $i18n["All OK, continue"]?></button>
<?php
}

$this->endSection();

?>
