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
 * $Id: list_item_definition_form.tpl.php 850 2013-08-20 23:16:37Z geert $
 */

use \Scrivo\String;
use \Scrivo\PageDefinition;
use \Scrivo\ListItemDefinition;

$this->beginSection("content", true);

?>

<form action="index.php" method="post">

<?php if ($listItemDefinition->id) { ?>
	<input type="hidden" name="a" value="list_item_definition_update">
	<input type="hidden" name="list_item_definition_id" value="<?php
		echo $listItemDefinition->id?>">
<?php } else { ?>
	<input type="hidden" name="a" value="list_item_definition_insert">
	<input type="hidden" name="application_definition_id" value="<?php
		echo $applicationId?>">
<?php } ?>
	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["List item definition data"]?></th>
		</tr>
		<tr>
			<td width="20%" class="label"><label for="title"><?php
				echo $i18n["Name:"]?></label></td>
			<td><input type="text" id="title" name="title" size="50"
				maxlength="50" value="<?php
				echo(htmlspecialchars($listItemDefinition->title))?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="php_selector"><?php
				echo $i18n["PHP selector:"]?></label></td>
			<td><input type="text" id="php_selector" name="php_selector"
				size="25" maxlength="50" value="<?php
				echo htmlspecialchars($listItemDefinition->phpSelector)
			?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="title_width"><?php
				echo $i18n["Width title field:"]?></label></td>
			<td><input type="text" id="php_selector" name="title_width"
				size="10" maxlength="50" value="<?php
				echo htmlspecialchars($listItemDefinition->titleWidth)
			?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="title_label"><?php
				echo $i18n["Alternative label for title field:"]?></label></td>
			<td><input type="text" id="php_selector" name="title_label"
				size="50" maxlength="50" value="<?php
					echo htmlspecialchars($listItemDefinition->titleLabel)
				?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="icon"><?php
				echo $i18n["Icon:"]?></label></td>
			<td>
<?php
$dir = "img/editor";
$i=0;
$val = $listItemDefinition->icon->equals(new String(""))
	? new String("$dir/page.png") : $listItemDefinition->icon;
if ($handle = opendir("../".$dir)) {
	while (false !== ($file = readdir($handle))) {
		$file = new String($file);
		if (!$file->equals(new String(".")) &&
				!$file->equals(new String(".."))) {
			$sel = "";
			if ($val->contains($file)) {
				$sel = " checked='true'";
			}
			if ($file->contains(new String(".png"))) {
				$s = getimagesize("../$dir/$file");
				if ($s[0] == 16 && $s[1] == 16) {
					echo "<span style='white-space:nowrap'>";
					echo "<input name='icon' type='radio' id='$file' ".
						"value='$dir/$file' $sel><label for='$file'>";
					echo "<img src='../$dir/$file'></label></span>\n";
					if ($i++%10==9) {
						echo "<br>";
					}
				}
			}
		}
	}
	closedir($handle);
}
?>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="page_definition_id"><?php
				echo $i18n["Template for linked page:"]?></label></td>
			<td><select id="page_definition_id" name="page_definition_id">
			<option value="0">[none]</option>
<?php
$list = PageDefinition::select($this->context);
foreach ($list as $k => $template) {
	echo "<option ".
		($template->id == $listItemDefinition->pageDefinitionId?"selected ":"").
		"value=\"".$template->id."\">".$template->title."</option>\n";
}
?>
			</select></td>
		</tr>
		<tr>
			<td class="label"><label for="list_item_parent_ids"><?php
				echo $i18n["Sub-type of:"]?></label></td>
			<td>
<?php
$defs = ListItemDefinition::select($this->context, $applicationId);
foreach ($defs as $k => $def) {
	echo "<input type='checkbox' name='list_item_definition_ids[]' value='";
	echo "{$def->id}'";
	if (isset($listItemDefinition->parentListItemDefinitionIds[$def->id]))
		echo " checked";
	echo ">".$def->title;
	echo "<br/>";
}
?>
			</td>
		</tr>
	</table>

	<button type="submit"><?php echo $i18n["Save"]?></button>
	<button type="button" onclick="document.location=
		'index.php?a=application_definition_form&application_definition_id=<?php
		echo $applicationId?>'"><?php echo $i18n["Cancel"]?></button>

</form>
<?php

$this->endSection();

?>