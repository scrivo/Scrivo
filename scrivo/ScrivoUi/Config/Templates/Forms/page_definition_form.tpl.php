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
 * $Id: page_definition_form.tpl.php 849 2013-08-20 19:19:50Z geert $
 */

use \Scrivo\Page;
use \Scrivo\PagePropertyDefinition;
use \Scrivo\PageDefinitionHints;

$this->beginSection("content", true);

?>

<form action="?" method="post">

<table cellspacing="0">

<?php

if ($pageDefinition->id) {

?>
	<tr><td style="vertical-align: top" colspan="2">
		<input type="hidden" name="a" value="page_definition_update">
		<input type="hidden" name="page_definition_id" value="<?php
			echo $pageDefinition->id?>">

		<button type="button" onclick="document.location=
			'?a=page_definition_tab_form&page_definition_id=<?php
			echo $pageDefinition->id?>'"><?php
				echo $i18n["New property tab"]?></button>
		<button type="button" onclick="document.location=
			'?a=page_property_definition_form&page_definition_id=<?php
			echo $pageDefinition->id?>'"><?php
				echo $i18n["New property"]?></button>
		<button type="button" onclick="document.location=
			'?a=page_definition_content_tab_form&page_definition_id=<?php
			echo $pageDefinition->id?>'"><?php
				echo $i18n["New content tab"]?></button>
		<button type="button" onclick="document.location=
			'?a=page_definition_application_tab_form&page_definition_id=<?php
			echo $pageDefinition->id?>'"><?php
				echo $i18n["New application tab"]?></button>
	</td></tr>
<?php

} else {

?>
	<input type="hidden" name="a" value="page_definition_insert">
<?php
}
?>
	<tr><td style="vertical-align: top">
	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["Template data"]?></th>
		</tr>
		<tr>
			<td width="20%" class="label"><label for="label"><?php
				echo $i18n["Template name:"]?></label></td>
			<td><input type="text" id="label" name="label" size="50"
				maxlength="50" value="<?php
					echo(htmlspecialchars($pageDefinition->title))?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="description"><?php
				echo $i18n["Description:"]?></label></td>
			<td><textarea id="description" name="description"
				cols="50" rows="5"><?php
					echo(htmlspecialchars($pageDefinition->description))
				?></textarea></td>
		</tr>
		<tr>
			<td class="label"><label for="file_name"><?php
				echo $i18n["PHP template file:"]?></label></td>
			<td><input type="text" id="file_name" name="file_name"
				size="50" maxlength="255" value="<?php
					echo(htmlspecialchars($pageDefinition->fileName))?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="admin_only"><?php
				echo $i18n["Configuration only:"]?></label></td>
			<td><input type="checkbox" id="admin_only" name="admin_only"
				value="true" <?php
					if ($pageDefinition->configOnly) echo(" checked")?>></td>
		</tr>
		<tr>
			<td class="label"><label for="file_name"><?php
				echo $i18n["Page types:"]?></label></td>
			<td>
<?php
$tmp = array_flip($pageDefinition->typeSet);
?>
			<input type="checkbox" id="type_set" name="type_set[]"
				value="<?php echo Page::TYPE_NAVIGATION_ITEM ?>"<?php
					if (isset($tmp[Page::TYPE_NAVIGATION_ITEM])) {
						echo(" checked");
					}?>>
				<?php echo $i18n["Navigation item"]?><br>
			<input type="checkbox" name="type_set[]"
				value="<?php echo Page::TYPE_NAVIGABLE_PAGE ?>"<?php
					if (isset($tmp[Page::TYPE_NAVIGABLE_PAGE])) {
						echo(" checked");
					}?>>
				<?php echo $i18n["Navigable page"]?><br>
			<input type="checkbox" name="type_set[]"
				value="<?php echo Page::TYPE_NON_NAVIGABLE_PAGE ?>"<?php
					if (isset($tmp[Page::TYPE_NON_NAVIGABLE_PAGE])) {
						echo(" checked");
					}?>>
				<?php echo $i18n["Non-navigable page"]?><br>
			<input type="checkbox" name="type_set[]"
				value="<?php echo Page::TYPE_SUB_FOLDER?>"<?php
					if (isset($tmp[Page::TYPE_SUB_FOLDER])) {
						echo(" checked");
					}?>>
				<?php echo $i18n["Other"]?><br>
			</td>
		</tr>
	</table>

<?php
if (!$pageDefinition->id) {
?>
	<button type="submit"><?php echo $i18n["Save"]?></button>
	<button onclick="document.location='?a=page_definition_list';return false;"
		type="button"><?php echo $i18n["Cancel"]?></button>

<?php
} else {
?>
	<table class="list" cellspacing="0">
		<tr>
			<th><?php echo $i18n["Template tab/property"]?></th>
			<th><?php echo $i18n["Def.tab"]?></th>
			<th><?php echo $i18n["PHP selector"]?></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
<?php
	$tmp = $pageDefinition->tabs;
	array_unshift($tmp, "propertytab");
	$cnt = 0;
	foreach ($tmp as $tab) {
		if ($tab == "propertytab") {
?>
		<tr class="row-color">
			<td><?php echo $i18n["Property tab"]?></td>
			<td><input type="radio" name="default_tab_id" value="-1" <?php
				if ($pageDefinition->defaultTabId == -1) {
					echo(" checked");
				}?>></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
<?php
		} else {
?>
		<tr<?php if ($cnt++%2==1) echo ' class="row-color"'?>>
			<td>
				<?php echo $tab->title; ?>
			</td>
			<td>
				<input type="radio" name="default_tab_id" value="<?php
					echo $tab->id; ?>"
					<?php if ($pageDefinition->defaultTabId == $tab->id) {
						echo(" checked");
					} ?>>
			</td>
			<td>
			</td>
			<td class="img">
				<a href=
					"?a=page_definition_tab_copy_form&page_definition_id=<?php
					echo $pageDefinition->id?>&page_definition_tab_id=<?php
					echo $tab->id; ?>">
					<img src="../img/admin/copy.png" alt="<?php
						echo $i18n["Copy tab"]?>" title="<?php
						echo $i18n["Copy tab"]?>">
				</a>
			</td>
			<td class="img">
				<a href="?a=page_definition_tab_move&page_definition_id=<?php
					echo $pageDefinition->id?>&page_definition_tab_id=<?php
					echo $tab->id; ?>&dir=-1">
					<img src="../img/admin/arrow_up_blue.png" alt="<?php
						echo $i18n["Move tab up"]?>" title="<?php
						echo $i18n["Move tab up"]?>">
				</a>
			</td>
			<td class="img">
				<a href="?a=page_definition_tab_move&page_definition_id=<?php
					echo $pageDefinition->id?>&page_definition_tab_id=<?php
					echo $tab->id; ?>&dir=1">
					<img src="../img/admin/arrow_down_blue.png" alt="<?php
						echo $i18n["Move tab down"]?>" title="<?php
						echo $i18n["Move tab down"]?>">
				</a>
			</td>
			<td class="img">
				<a href=
					"?a=page_definition_tab_form&page_definition_tab_id=<?php
					echo $tab->id?>">
					<img src="../img/admin/properties.png" alt="<?php
						echo $i18n["Edit tab"]?>" title="<?php
						echo $i18n["Edit tab"]?>">
				</a>
			</td>
			<td class="img">
				<a onclick="return confirm('<?php
					echo $i18n["Delete tab?"]?>');"
				href="?a=page_definition_tab_delete&page_definition_tab_id=<?php
					echo $tab->id ?>">
					<img src="../img/admin/delete.png" alt="<?php
						echo $i18n["Delete tab"]?>" title="<?php
						echo $i18n["Delete tab"]?>">
				</a>
			</td>
		</tr>
<?php
		}
foreach ($pageDefinition->properties as $prop) {
	$tmp = ("propertytab" == $tab) ? 0 : $tab->id;
	if ($prop->pageDefinitionTabId == $tmp) {
		$isprop = false;
?>
		<tr<?php if ($cnt++%2==1) echo ' class="row-color"'?>>
			<td colspan="2">
<?php if ($prop->type == PagePropertyDefinition::TYPE_HTML_TEXT_TAB) { ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<i><b><?php
					echo $i18n["HTML content tab"]?></b></i>
<?php } else if ($prop->type==PagePropertyDefinition::TYPE_APPLICATION_TAB) { ?>
				&nbsp;&nbsp;&nbsp;&nbsp;<i><b><?php
					echo $i18n["ApplicationDefinition tab"]?></b></i>
<?php } else {
			$isprop = true;
?>
			&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $prop->title; ?>
<?php } ?>
			</td>
			<td>
				<?php echo $prop->phpSelector; ?>
			</td>
			<td class="img">
<?php if ($isprop) { ?>
				<a href=
				"?a=page_property_definition_copy_form&page_definition_id=<?php
					echo $pageDefinition->id?>&page_property_definition_id=<?php
					echo $prop->id; ?>">
					<img src="../img/admin/copy.png" alt="<?php
						echo $i18n["Copy property"]?>" title="<?php
						echo $i18n["Copy property"]?>">
				</a>
<?php } ?>
			</td>
			<td class="img">
<?php if ($isprop) { ?>
				<a href=
			"?a=page_property_definition_move&page_property_definition_id=<?php
					echo $prop->id?>&tab_id=<?php
					echo $prop->pageDefinitionTabId?>&dir=-1">
					<img src="../img/admin/arrow_up_yellow.png" alt="<?php
						echo $i18n["Move property up"]?>" title="<?php
						echo $i18n["Move property up"]?>">
				</a>
<?php } ?>
			</td>
			<td class="img">
<?php if ($isprop) { ?>
				<a href=
			"?a=page_property_definition_move&page_property_definition_id=<?php
					echo $prop->id?>&tab_id=<?php
					echo $prop->pageDefinitionTabId?>&dir=1">
					<img src="../img/admin/arrow_down_yellow.png" alt="<?php
						echo $i18n["Move property down"]?>" title="<?php
						echo $i18n["Move property down"]?>">
				</a>
<?php } ?>
			</td>
			<td class="img">
<?php if ($prop->type == PagePropertyDefinition::TYPE_HTML_TEXT_TAB) { ?>
				<a href=
		"?a=page_definition_content_tab_form&page_property_definition_id=<?php
					echo $prop->id?>">
					<img src="../img/admin/properties.png" alt="<?php
						echo $i18n["Edit property"]?>" title="<?php
						echo $i18n["Edit"]?>">
				</a>
<?php } else if ($prop->type==PagePropertyDefinition::TYPE_APPLICATION_TAB) { ?>
				<a href=
	"?a=page_definition_application_tab_form&page_property_definition_id=<?php
					echo $prop->id?>">
					<img src="../img/admin/properties.png" alt="<?php
						echo $i18n["Edit property"]?>" title="<?php
						echo $i18n["Edit"]?>">
				</a>
<?php } else { ?>
				<a href=
			"?a=page_property_definition_form&page_property_definition_id=<?php
					echo $prop->id?>">
					<img src="../img/admin/properties.png" alt="<?php
						echo $i18n["Edit property"]?>" title="<?php
						echo $i18n["Edit"]?>">
				</a>
<?php } ?>
			</td>
			<td class="img">
<?php if ($isprop) { ?>
				<a onclick="return confirm('<?php
					echo $i18n["Delete property?"]?>')" href=
		"?a=page_property_definition_delete&page_property_definition_id=<?php
					echo $prop->id?>">
					<img src="../img/admin/delete.png" alt="<?php
						echo $i18n["Delete property"]?>" title="<?php
						echo $i18n["Delete property"]?>">
				</a>
<?php } ?>
			</td>
		</tr>
<?php
			}
		}
		$first = false;
	}
?>
	</table>

	<p>
	<button type="submit"><?php echo $i18n["Save"]?></button>
	<button type="button" onclick=
		"document.location='?a=page_definition_list';return false;"><?php
		echo $i18n["Cancel"]?></button>
	</p>

</td><td style="padding-left: 2em">

	<table class="list" cellspacing="0">
		<tr>
			<th colspan="2"><?php
			echo $i18n["Number of times a page of this template can occur".
				"<br>underneath pages of the following templates"]?></th>
		</tr>
<?php
	$cnt = 0;
	$hints = new PageDefinitionHints($this->context, $pageDefinition->id,
		PageDefinitionHints::PARENT_PAGE_DEFINITION_COUNT);
	foreach ($hints as $h) {

?>
		<tr<?php if (++$cnt%2==1) echo 'class="row-color"'?>>
			<td><?php echo $h->title ?></td>
			<td><input type="text" id="hint<?php echo $h->pageDefinitionId?>"
				name="hint<?php echo $h->pageDefinitionId?>"
				size="5" maxlength="5" value="<?php echo
				is_null($h->maxNoOfChildren)?"":$h->maxNoOfChildren?>"></td>
		</tr>
<?php
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