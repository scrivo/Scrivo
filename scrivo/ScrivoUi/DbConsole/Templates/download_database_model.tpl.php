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
 * $Id: download_database_model.tpl.php 860 2013-08-24 12:42:15Z geert $
 */

$this->beginSection("content", true);

?>
	<p>
	<?php echo $i18n["Please download and run the following SQL script to ".
		"create a Scrivo database before continuing."]?></p>

	<p><a href="../ScrivoUi/DbConsole/Data/scrivo.sql"><?php
		echo $i18n["download"]?></a></p>
	<p><strong><?php echo $i18n["Note:"]?></strong>
		<?php echo $i18n["right click to download"]?></p>

	<p>
	<?php echo $i18n["After running this script you can continue the ".
		"installation process by creating a new instance in the ".
		"database."]?></p>
	<p>
	<button type='button'
		onclick="document.location=
				'index.php?a=initialize.selectDatabaseForm'"><?php
			echo $i18n["Create a new instance"]?></button></p>

<?php

$this->endSection();

?>
