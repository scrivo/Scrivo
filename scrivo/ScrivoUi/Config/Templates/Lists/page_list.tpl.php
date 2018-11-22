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
 * $Id: page_list.tpl.php 841 2013-08-19 22:19:47Z geert $
 */

use \Scrivo\Str;

foreach ($rows as $d) {

	$this->beginSection("rows", false);

?>
	<tr id="page_<?php echo $d->pageId; ?>">
		<td class="limitcell"><?php
	echo str_repeat("&nbsp", $d->depth*3);
	if ($d->hasChildren) {
		echo "<a href='#' onclick='return openNode(".$d->pageId.")'>";
		echo "<img class=\"treeimg\" src=\"../img/admin/openclose.png\"></a>";
	} else {
		echo "<img class=\"treeimg\" src=\"../img/admin/none.png\"></a>";
	}
	echo $d->icon;
	echo $d->title?>
		</td>
		<td><?php echo $d->label ?></td>
		<td>
			<a href="index.php?a=page_definition_form&page_definition_id=<?php
				echo $d->pageDefinitionId ?>">
				<?php echo $d->pageDefinitionTitle ?>
			</a>
		</td>
		<td><?php echo $d->pageId; ?></td>
		<td class="img">
			<a href="index.php?a=page_html_tab_list&page_id=<?php echo $d->pageId; ?>">
				<img src="../img/admin/page_pencil.png" alt="<?php
					echo $i18n["edit content"]?>" title="<?php
					echo $i18n["edit content"]?>">
			</a>
		</td>
		<td class="img">
			<a href="index.php?a=page_form&page_id=<?php echo $d->pageId; ?>">
				<img src="../img/admin/properties.png" alt="<?php
					echo $i18n["edit properties"]?>" title="<?php
					echo $i18n["edit properties"]?>">
			</a>
		</td>
		<td class="img">
<?php
	if ($d->deletable) {
?>
			<a onclick="return confirm('<?php echo $i18n["delete page?"]?>')"
				href="index.php?a=page_delete&page_id=<?php echo $d->pageId ?>">
				<img src="../img/admin/delete.png" alt="<?php
					echo $i18n["delete"]?>" title="<?php
					echo $i18n["delete"]?>">
			</a>
<?php
	}
?>
		</td>
	</tr>
<?php

	$this->endSection();
}

$this->beginSection("content", true);
?>

<script>

function createRow(d) {
	var res = "";
	res += "<td class=\"limitcell\">";
	for (var i=0; i<d.depth; i++) {
		res += "&nbsp;&nbsp;&nbsp;";
	}
	if (d.hasChildren) {
		res += "<a href='#' onclick='return openNode("+d.pageId+")'>";
		res += "<img class=\"treeimg\" src=\"../img/admin/openclose.png\"></a>";
	} else {
		res += "<img class=\"treeimg\" src=\"../img/admin/none.png\"></a>";
	}
	res += d.icon;
	res += d.title;
	res += "</td>";
	res += "<td>"+d.label+"</td>";
	res += "<td>";
	res += "	<a href=\"index.php?a=page_definition_form&page_definition_id="+
		d.pageDefinitionId+"\">";
	res += "		"+d.pageDefinitionTitle;
	res += "	</a>";
	res += "</td>";
	res += "<td>"+d.pageId+"</td>";
	res += "<td class=\"img\">";
	res += "	<a href=\"index.php?a=page_html_tab_list&page_id="+d.pageId+"\">";
	res += "		<img src=\"../img/admin/page_pencil.png\" alt=\"<?php
		echo $i18n["edit content"]?>\" title=\"<?php
		echo $i18n["edit content"]?>\">";
	res += "	</a>";
	res += "</td>";
	res += "<td class=\"img\">";
	res += "	<a href=\"index.php?a=page_form&page_id="+d.pageId+"\">";
	res += "		<img src=\"../img/admin/properties.png\" alt=\"<?php
		echo $i18n["edit properties"]?>\" title=\"<?php
		echo $i18n["edit properties"]?>\">";
	res += "	</a>";
	res += "</td>";
	res += "<td class=\"img\">";
	if (d.deletable) {
		res += "	<a onclick=\"return confirm('<?php
			echo $i18n["delete page?"]?>')\"";
		res += "			href=\"index.php?a=page_delete&page_id="+d.pageId+"\">";
		res += "			<img src=\"../img/admin/delete.png\" alt=\"<?php
			echo $i18n["delete"]?>\" title=\"<?php
			echo $i18n["delete"]?>>\">";
		res += "	</a>";
	}
	res += "</td>";

	return res;
}
function openNode(id) {
	SUI.xhr.doGet("", {
		a:"page_list_ajax",
		page_id: id
	},
	function(res) { //console.log(res.data);
		var prow = document.getElementById("page_"+id);
		for (var i=res.data.length-1; i>=0; i--) {
			var x = document.getElementById("page_"+res.data[i].pageId);
			if (x) {
				prow.parentNode.removeChild(x);
			} else {
				var tr = document.createElement("TR");
				tr.id = "page_"+res.data[i].pageId;
				tr.innerHTML = createRow(res.data[i]);
				prow.parentNode.insertBefore(tr, prow.nextSibling);
			}
		}

	});
	return false;
}

</script>

	<p><?php echo
		Str::create(
			$i18n["Create a new [L]page[/L]"])->replace(
				Str::create(array("[L]", "[/L]")),
				Str::create(array("<a href=\"index.php?a=page_form\">", "</a>"))
			);
	?></p>

	<table class="list" cellspacing="0">
		<tr>
			<th class="table-heading"><?php echo $i18n["Page title"]?></th>
			<th width="20" class="table-heading"><?php
				echo $i18n["Label"]?></th>
			<th width="20" class="table-heading"><?php
				echo $i18n["Page definition"]?></th>
			<th width="20" class="table-heading"><?php
				echo $i18n["Page id"]?></th>
			<th class="table-heading"></th>
			<th class="table-heading"></th>
			<th class="table-heading"></th>
		</tr>
		<?php echo $this->getSection("rows")?>
	</table>
<?php

$this->endSection();

?>