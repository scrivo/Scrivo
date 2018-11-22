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
 * $Id: master.tpl.php 866 2013-08-25 16:22:35Z geert $
 */

use \Scrivo\Action;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//name_en">
<html>
	<head>
		<?php //echo $this->sections->head ?>
		<?php echo $this->getSection("head") ?>
	</head>
	<body>
		<div id="header" style="">
			<?php echo $this->getSection("header") ?>
			<?php //echo $this->sections->header ?>
		</div>
		<div id="menu">
			<?php echo $this->getSection("menu") ?>
			<?php //echo $this->sections->menu ?>
		</div>
		<div id="maindiv">
			<table cellspacing="0" id="centercontent"><tbody><tr><td>
			<div id="content">
<?php
	if (isset($this->session->errorCode)) {
		$error = $this->session->errorCode;
		if (Action::FAIL == $this->session->forwardResult) {
?>
			<div id="error">
				<?php echo $error?>
			</div>
<?php
		} else {
?>
			<div id="alert">
				<?php echo $error?>
			</div>
<?php
		}
	}
?>
			<?php echo $this->getSection("content") ?>
			<?php //echo $this->sections->content ?>
			</div>
			</td></tr></tbody></table>
		</div>
	</body>
</html>
