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
 * $Id: page_form.tpl.php 852 2013-08-21 12:43:09Z geert $
 */

use \Scrivo\Page;
use \Scrivo\PageDefinition;

$this->beginSection("content", true);

?>

<form action="index.php" method="post">

	<?php
	if ($page->id) {
?>
	<input type="hidden" name="a" value="page_update"> <input type="hidden"
		name="page_id" value="<?php echo $page->id?>">
	<?php
} else {
?>
	<input type="hidden" name="a" value="page_insert">
	<?php
}
?>

	<table class="form" cellspacing="0">
		<tr>
			<th colspan="2"><?php echo $i18n["Page data"]?></th>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="title"><?php
				echo $i18n["Title:"]?>
			</label></td>
			<td><input type="text" id="title" name="title" size="40"
				maxlength="255" value="<?php
					echo(htmlspecialchars($page->title))?>">
			</td>
		</tr>
		<tr>
			<td class="label"><label for="page_pid"><?php
				echo $i18n["Parent page:"]?>
			</label></td>
			<td><script type="text/javascript">

function selectPage(e) {
	   var d = new SUI.editor.PageDialog({
			pageId: parseInt(e.previousSibling.value, 10),
			onOK: function(data) {
				e.previousSibling.value = data.id;
				e.nextSibling.innerHTML = data.title;
			}
		});
		d.center();
		d.show();
}
</script>
			<input type="hidden" id="page_pid" name="page_pid" value="<?php
				echo $page->parentId ? $page->parentId
					: $this->context->config->ROOT_PAGE_ID ?>"
			><button type="button" onclick="selectPage(this)"><?php
				echo $i18n["Select"]?></button><span><?php
				try {
					$tmp = Page::fetch($this->context, $page->parentId);
					echo $tmp->title;
				}
				catch (\Exception $e) {} ?>
			</span></td>
		</tr>
		<tr>
			<td class="label"><label for="type"><?php
				echo $i18n["Page type:"]?></label>
			</td>
			<td><select name="type">
					<option value="<?php
						echo Page::TYPE_NAVIGATION_ITEM ?>"
						<?php if ($page->type==Page::TYPE_NAVIGATION_ITEM)
							echo(" selected");?>>
						<?php echo $i18n["Navigation item"]?>
					</option>
					<option value="<?php
						echo Page::TYPE_NAVIGABLE_PAGE ?>"
						<?php if($page->type==Page::TYPE_NAVIGABLE_PAGE)
							echo(" selected");?>>
						<?php echo $i18n["Navigable page"]?>
					</option>
					<option value="<?php
						echo Page::TYPE_NON_NAVIGABLE_PAGE ?>"
						<?php if($page->type==Page::TYPE_NON_NAVIGABLE_PAGE)
							echo(" selected");?>>
						<?php echo $i18n["Non-navigable page"]?>
					</option>
					<option value="<?php
						echo Page::TYPE_APPLICATION ?>"
						<?php if($page->type==Page::TYPE_APPLICATION)
							echo(" selected");?>>
						<?php echo $i18n["Application"]?>
					</option>
					<option value="<?php
						echo Page::TYPE_SUB_FOLDER ?>"
						<?php if($page->type==Page::TYPE_SUB_FOLDER)
							echo(" selected");?>>
						<?php echo $i18n["Folder"]?>
					</option>
			</select>
			</td>
		</tr>
		<tr>
			<td width="30%" class="label"><label for="title"><?php
				echo $i18n["Label:"]?>
			</label></td>
			<td><input type="text" id="label" name="label" size="30"
				maxlength="255"
				value="<?php
					echo(htmlspecialchars(isset($labels[$page->id])
						? $labels[$page->id] : ""))
					?>"></td>
		</tr>
		<tr>
			<td class="label"><label for="page_definition_id"><?php echo
				$i18n["Page definition:"]?>
			</label></td>
			<td><select name="page_definition_id">
					<?php

					$list = PageDefinition::select($this->context);
					foreach($list as $v) {
						echo "<option ".
						($v->id == $page->definition->id ? "selected " : "").
							"value=\"".$v->id."\">".$v->title."</option>\n";
}

?>
			</select>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="language_id"><?php
				echo $i18n["Page language:"]?></label>
			</td>
			<td><select name="language_id">
			<?php
				foreach ($this->context->config->LANGUAGES as $iso => $name) {
					echo "<option ".
						($iso == $page->language ? "selected " : "").
						"value=\"{$iso}\">{$iso}: {$name}</option>\n";
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
		</tr>
		<tr>
			<td class="label"><label for="created"><?php
				echo $i18n["Created (y-m-d):"]?></label></td>
			<td><?php echo $page->dateCreated->format("Y")?>
				- <?php echo $page->dateCreated->format("m")?>
				- <?php echo $page->dateCreated->format("d")?>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="modified"><?php
				echo $i18n["Last modified (y-m-d):"]?></label></td>
			<td><?php echo $page->dateModified->format("Y")?>
				- <?php echo $page->dateModified->format("m")?>
				- <?php echo $page->dateModified->format("d")?>
			</td>
		</tr>
		<tr>
			<td class="label"><label for="online_on"><?php
				echo $i18n["Online on (y-m-d):"]?></label></td>
			<td><input type="text" name="online_on_y" size="4" maxlength="4"
				value="<?php echo $page->dateOnline->format("Y")?>"> -
				<input type="text" name="online_on_m" size="2" maxlength="2"
				value="<?php echo $page->dateOnline->format("m")?>"> -
				<input type="text" id="online_on" name="online_on_d" size="2"
				maxlength="2" value="<?php
					echo $page->dateOnline->format("d")?>">
			</td>
		</tr>
		<tr>
			<td class="label"><label for="remove_on"><?php
				echo $i18n["Offline on (y-m-d):"]?></label></td>
			<td><input type="text" name="remove_on_y" size="4" maxlength="4"
				value="<?php echo $page->dateOffline
					?$page->dateOffline->format("Y"):""?>"> -
				<input type="text" name="remove_on_m" size="2" maxlength="2"
				value="<?php echo $page->dateOffline
					?$page->dateOffline->format("m"):""?>"> -
				<input type="text" id="remove_on" name="remove_on_d"
					size="2" maxlength="2"
				value="<?php echo $page->dateOffline
					?$page->dateOffline->format("d"):""?>">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="label"><label for="description"><?php
				echo $i18n["Description:"]?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea id="description" name="description" cols="80"
					rows="5"><?php echo(htmlspecialchars($page->description))
					?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" class="label"><label for="keywords"><?php
				echo $i18n["Keywords:"]?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea id="keywords" name="keywords" cols="80"
					rows="5"><?php
					echo(htmlspecialchars($page->keywords)) ?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" class="label"><label for="stylesheet"><?php
				echo $i18n["Style sheet:"]?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea id="stylesheet" name="stylesheet" cols="80"
					rows="5"><?php
					echo(htmlspecialchars($page->stylesheet)) ?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" class="label"><label for="javascript"><?php
				echo $i18n["Javascript:"]?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea id="javascript" name="javascript" cols="80"
					rows="5"><?php
					echo(htmlspecialchars($page->javascript)) ?></textarea></td>
		</tr>
	</table>

	<button type="submit">
		<?php echo $i18n["Save"]?>
	</button>
	<button type="button" onclick="document.location='index.php?a=page_list';">
		<?php echo $i18n["Cancel"]?>
	</button>

</form>
<?php

$this->endSection();

?>