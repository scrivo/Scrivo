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
 * $Id: asset_list.tpl.php 846 2013-08-20 12:34:06Z geert $
 */

use \Scrivo\Asset;
use \Scrivo\Role;

foreach ($rows as $d) {

	$this->beginSection("rows", false);

?>

	<tr id="asset_<?php echo $d->assetId; ?>">
		<td class="limitcell"><?php
	echo str_repeat("&nbsp", $d->depth*3);
	if ($d->hasChildren) {
		echo "<a href='#' onclick='return openNode(".$d->assetId.")'>";
		echo "<img class=\"treeimg\" src=\"../img/admin/openclose.png\"></a>";
	} else {
		echo "<img class=\"treeimg\" src=\"../img/admin/none.png\"></a>";
	}
	echo $d->icon;
	echo $d->title?>
		</td>
		<td>
			<?php echo $d->mimeType ?>
		</td>
		<td><?php echo $d->assetId; ?></td>
		<td class="img">
			<?php if ($roleId && $d->access) {  ?>
			<img class="treeimg" src="../img/admin/tick.png" alt="<?php
				echo $i18n["accessible"]?>" title="<?php
				echo $i18n["accessible"]?>">
			<?php } ?>
			<?php if ($roleId && !$d->access) {  ?>
			<img class="treeimg" src="../img/admin/no_access.png" alt="<?php
				echo $i18n["inaccessible"]?>" title="<?php
				echo $i18n["inaccessible"]?>">
			<?php } ?>
			<?php if ($d->type == Asset::TYPE_FOLDER) {  ?>
			<a href="index.php?a=asset_access_form&asset_id=<?php echo $d->assetId; ?>">
				<img src="../img/admin/properties.png" alt="<?php
					echo $i18n["edit properties"]?>" title="<?php
					echo $i18n["edit properties"]?>">
			</a>
			<?php } ?>
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
		res += "<a href='#' onclick='return openNode("+d.assetId+")'>";
		res += "<img class=\"treeimg\" src=\"../img/admin/openclose.png\"></a>";
	} else {
		res += "<img class=\"treeimg\" src=\"../img/admin/none.png\"></a>";
	}
	res += d.icon;
	res += d.title;
	res += "</td>";
	res += "<td>";
	res += "		"+d.mimeType;
	res += "</td>";
	res += "<td>"+d.assetId+"</td>";
	res += "<td class=\"img\">";
<?php if ($roleId) { ?>
	if (d.access) {
		res += "	<img class=\"treeimg\" alt=\"<?php
			echo $i18n["accessible"]?>\" title=\"<?php
			echo $i18n["accessible"]?>\" src=\"../img/admin/tick.png\">";
	} else {
		res += "	<img class=\"treeimg\" alt=\"<?php
			echo $i18n["inaccessible"]?>\" title=\"<?php
			echo $i18n["inaccessible"]?>\" src=\"../img/admin/no_access.png\">";
	}
<?php } ?>
	if (d.type == <?php echo Asset::TYPE_FOLDER?>) {
		res += "	<a href=\"index.php?a=asset_access_form&asset_id="+d.assetId+"\">";
		res += "		<img src=\"../img/admin/properties.png\" alt=\"<?php
			echo $i18n["edit properties"]?>\" title=\"<?php
			echo $i18n["edit properties"]?>\">";
		res += "	</a>";
	}
	res += "</td>";

	return res;
}
function openNode(id) {
	SUI.xhr.doGet("", {
		a:"asset_list_ajax",
		asset_id: id,
		role_id: <?php echo $roleId?>
	},
	function(res) { //console.log(res.data);
		var prow = document.getElementById("asset_"+id);
		for (var i=res.data.length-1; i>=0; i--) {
			var x = document.getElementById("asset_"+res.data[i].assetId);
			if (x) {
				prow.parentNode.removeChild(x);
			} else {
				var tr = document.createElement("TR");
				tr.id = "asset_"+res.data[i].assetId;
				tr.innerHTML = createRow(res.data[i]);
				prow.parentNode.insertBefore(tr, prow.nextSibling);
			}
		}

	});
	return false;
}

</script>

	<form id="access_selector" action="index.php" method="get">
		<input type="hidden" name="a" value="asset_list">
		<select onchange="this.parentNode.submit()" name="role_id">
			<option value=""><?php echo $i18n["Show access"] ?></option>
<?php
foreach (Role::select(
	$this->context, Role::EDITOR_ROLE) as $role) {
?>
			<option value="<?php echo $role->id?>"<?php
				echo $role->id==$roleId?" selected":""?>>
				<?php echo $i18n["Editor role:"] ?> <?php echo $role->title ?>
			</option>
<?php
}
foreach (Role::select(
	$this->context, Role::PUBLIC_ROLE) as $role) {
?>
			<option value="<?php echo $role->id?>"<?php
				echo $role->id==$roleId?" selected":""?>>
				<?php echo $i18n["Public role:"] ?> <?php echo $role->title ?>
			</option>
<?php
}
?>
		</select>
	</form>

	<table class="list" cellspacing="0">

		<tr>
			<th class="table-heading"><?php
				echo $i18n["Title"]?></th>
			<th width="20" class="table-heading"><?php
				echo $i18n["Mime type"]?></th>
			<th width="20" class="table-heading"><?php
				echo $i18n["Asset id"]?></th>
			<th width="20" class="table-heading"><?php
				echo $i18n["Access"]?></th>
		</tr>
		<?php echo $this->getSection("rows")?>
	</table>
<?php

$this->endSection();

?>