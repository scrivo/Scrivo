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
 * $Id: application_definition_form.tpl.php 866 2013-08-25 16:22:35Z geert $
 */

use \Scrivo\ListItemDefinition;
use \Scrivo\ListItemPropertyDefinition;

$this->beginSection("content", true);

?>
<form action="index.php" method="post">

<table cellspacing="0"><tr><td style="vertical-align: top">

<?php
if ($application->id) {
?>
<input type="hidden" name="a" value="application_definition_update">
<input type="hidden" name="application_definition_id" value="<?php
	echo $application->id?>">
<?php
} else {
?>
<input type="hidden" name="a" value="application_definition_insert">
<?php
}
?>
<table class="form" cellspacing="0">
	<tr>
		<th colspan="2"><?php echo
			$i18n["List/ApplicationDefinition data"]?></th>
	</tr>
	<tr>
		<td width="20%" class="label"><label for="title"><?php
			echo $i18n["Name:"]?></label></td>
		<td><input type="text" id="title" name="title" size="40"
			maxlength="50" value="<?php
				echo(htmlspecialchars($application->title))?>"></td>
	</tr>
	<tr>
		<td colspan="2" class="label"><label for="description"><?php
			echo $i18n["Description:"]?></label></td>
	</tr>
	<tr>
		<td colspan="2"><textarea id="description" name="description"
			cols="70" rows="5"><?php
				echo htmlspecialchars($application->description)
			?></textarea></td>
	</tr>
	<tr>
		<td class="label"><label for="type"><?php
			echo $i18n["Type:"]?></label></td>
		<td>
			<select id="type" name="type">
<?php
foreach ($types as $k => $v) {
	echo "<option value='$k'".
		($k == $application->type ? " selected" : "").">$v</option>";
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="label"><label for="location"><?php
			echo $i18n["url:"]?></label></td>
		<td><input type="text" id="location" name="location"
			size="40" maxlength="255" value="<?php
			echo htmlspecialchars($application->location)?>"></td>
	</tr>
</table>

<p>
<?php
if ($application->id) {
?>
	<button type="button" onclick="
document.location='index.php?a=list_item_definition_form&application_definition_id=<?php
		echo $application->id?>'"><?php
			echo $i18n["New list item definition"]?></button>
	<button type="button" onclick=
"document.location='index.php?a=list_item_property_form&application_definition_id=<?php
		echo $application->id?>'"><?php
			echo $i18n["New list item property"]?></button>
	<br>
<?php
}
?>
	<button type="submit"><?php echo $i18n["Save"]?></button>
	<button type="button"
		onclick="document.location='index.php?a=application_definition_list';"><?php
			echo $i18n["Cancel"]?></button>
</p>

</td><td style="padding-left: 2em">

<?php
if ($application->id) {
?>
<table class="list" cellspacing="0">

	<tr>
		<th class="table-heading"><?php
			echo $i18n["List item type/property"]?></th>
		<th class="table-heading"><?php
			echo $i18n["PHP selector"]?></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
		<th class="table-heading"></th>
	</tr>
<?php
	$cnt = 0;
	$defs = ListItemDefinition::select($this->context, $application->id);
	$props =
		ListItemPropertyDefinition::select($this->context, $application->id);
	foreach ($defs as $key => $def) {
?>
	<tr<?php if (++$cnt%2==1) echo ' class="row-color"'?>>
		<td><?php echo $def->title; ?></td>
		<td><?php echo $def->phpSelector; ?></td>
		<td class="img">
			<a href="index.php?a=list_item_definition_move&list_item_definition_id=<?php
				echo $def->id ?>&dir=-1">
				<img src="../img/admin/arrow_up_blue.png" alt="<?php
					echo $i18n["Move list item type up"]?>" title="<?php
					echo $i18n["Move list item type up"]?>">
			</a>
		</td>
		<td class="img">
			<a href="index.php?a=list_item_definition_move&list_item_definition_id=<?php
				echo $def->id ?>&dir=1">
				<img src="../img/admin/arrow_down_blue.png" alt="<?php
					echo $i18n["Move list item type down"]?>" title="<?php
					echo $i18n["Move list item type down"]?>">
			</a>
		</td>
		<td class="img">
			<a href="index.php?a=list_item_definition_form&list_item_definition_id=<?php
				echo $def->id; ?>">
				<img src="../img/admin/properties.png" alt="<?php
					echo $i18n["Edit list item type"]?>" title="<?php
					echo $i18n["Edit list item type"]?>">
			</a>
		</td>
		<td class="img">
			<a onclick="return confirm('<?php
				echo $i18n["Delete list item type?"]?>')"
			href="index.php?a=list_item_definition_delete&list_item_definition_id=<?php
				echo $def->id ?>">
				<img src="../img/admin/delete.png" alt="<?php
					echo $i18n["Delete list item type"]?>" title="<?php
					echo $i18n["Delete list item type"]?>">
			</a>
		</td>
	</tr>
<?php
		if (isset($props[$def->id])) {
			foreach ($props[$def->id] as $key2 => $value2) {
?>
	<tr<?php if (++$cnt%2==1) echo ' class="row-color"'?>>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value2->title; ?></td>
		<td><?php echo $value2->phpSelector; ?></td>
		<td class="img">
			<a href="index.php?a=list_item_property_move&list_item_definition_id=<?php
				echo $def->id ?>&list_item_property_id=<?php
					echo $value2->id?>&dir=-1">
				<img src="../img/admin/arrow_up_yellow.png" alt="<?php
					echo $i18n["Move list item property up"]?>" title="<?php
						echo $i18n["Move list item property up"]?>">
			</a>
		</td>
		<td class="img">
			<a href="index.php?a=list_item_property_move&list_item_definition_id=<?php
				echo $def->id ?>&list_item_property_id=<?php
				echo $value2->id?>&dir=1">
				<img src="../img/admin/arrow_down_yellow.png" alt="<?php
					echo $i18n["Move list item property down"]?>" title="<?php
					echo $i18n["Move list item property down"]?>">
			</a>
		</td>
		<td class="img">
			<a href="index.php?a=list_item_property_form&list_item_property_id=<?php
				echo $value2->id?>">
				<img src="../img/admin/properties.png" alt="<?php
					echo $i18n["Edit list item property"]?>" title="<?php
					echo $i18n["Edit list item property"]?>">
			</a>
		</td>
		<td class="img">
			<a onclick="return confirm('<?php
				echo $i18n["Delete list item property?"]?>')"
				href="index.php?a=list_item_property_delete&list_item_property_id=<?php
				echo $value2->id?>">
				<img src="../img/admin/delete.png" alt="<?php
					echo $i18n["Delete list item property"]?>" title="<?php
					echo $i18n["Delete list item property"]?>">
			</a>
		</td>
	</tr>
<?php
			}
			reset($props);
		}
	}
?>
</table>

<?php
}
?>
</td></tr></table>

</form>

<?php

$this->endSection();

?>