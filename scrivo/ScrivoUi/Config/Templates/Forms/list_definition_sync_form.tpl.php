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
 * $Id: list_definition_sync_form.tpl.php 866 2013-08-25 16:22:35Z geert $
 */

use \ScrivoUi\Config\Lib\SyncUtilListDefinition;

$this->beginSection("content", true);

$cnt = 0;

?>
<script>
function sectionSelect(el) {
	var rw = el.parentNode.parentNode.nextSibling;
	do {
		if (rw.nodeType == 1) {
			if (rw.firstChild.nextSibling.tagName == "TH") {
				break;
			}
			var l = rw.getElementsByTagName("INPUT");
			if (l.length) {
				l[0].checked = el.checked;
			}
		}
	} while (rw = rw.nextSibling);
}
</script>

<?php
if (count($difft["add"]) + count($difft["rem"]) + count($difft["mod"])) {
?>

<form action="?" method="post">

	<input type="hidden" name="a" value="list_definition_sync">
	<input type="hidden" name="site" value="<?php echo $remoteSite?>">
<?php
	if (count($difft["add"])) {
?>
	<p><?php echo $i18n["Added list defintions"]?></p>

	<table class="list" cellspacing="0">
<?php
		echo
			"<tr><th>{$i18n["List definition name"]}</th><th>".
			"<img src=\"../img/admin/plus.png\" alt=\"{$i18n["add"]}\" ".
			"title=\"{$i18n["add"]}\"></th></tr>\n";

		foreach ($difft["add"] as $t) {
			$this->printRow($newcfg[$t]["title"],
				"add_application_definition:{$newcfg[$t]["application_definition_id"]}",
				++$cnt);
		}
?>
	</table>
<?php
	}

	if (count($difft["rem"])) {
?>

	<p><?php echo $i18n["Deleted list defintions"]?></p>

	<table class="list" cellspacing="0">
<?php
		$this->pr("<tr><th>{$i18n["List definition name"]}</th><th>".
			"<img src=\"../img/admin/delete.png\" alt=\"{$i18n["delete"]}\" ".
			"title=\"{$i18n["delete"]}\" ></th></tr>");

		foreach ($difft["rem"] as $t) {
			$this->printRow($curcfg[$t]["title"],
				"del_application_definition:{$curcfg[$t]["application_definition_id"]}",
				++$cnt);
		}
?>
	</table>
<?php
	}
?>

<?php
	if (count($difft["mod"])) {
?>

	<p><?php echo $i18n["Modified application definitions"]?></p>

	<table class="list" cellspacing="0">
<?php

		foreach ($difft["mod"] as $t) {

			$this->pr("<tr><th>{$curcfg[$t]["title"]}</th><th>".
				"{$i18n["Current value"]}</th><th>{$i18n["New value"]}</th>");
			$this->pr("<th><input type=\"checkbox\" ".
				"onclick=\"sectionSelect(this)\"</th></tr>");

			foreach ($curcfg[$t] as $k => $tm) {

				if ($k == "TYPES" || $k == "FIELDS") {
					continue;
				}

				if ($tm != $newcfg[$t][$k]) {

					if ("type" == $k) {
						$data1 = $curcfg[$t]['types'][$tm];
						$data2 = $newcfg[$t]['types'][$newcfg[$t][$k]];
					} else if (is_array($tm)) {
						$data1 = "(" . join(",",$tm) . ")";
						$data2 = "(" . join(",",$newcfg[$t][$k]) . ")";
					} else {
						$data1 = $tm;
						$data2 = $newcfg[$t][$k];
					}

					$this->printModRow(
						$item_labels[$k], $data1, $data2,
						"mod_application_definition:".
							"{$curcfg[$t]["application_definition_id"]}:property:{$k}",
						++$cnt);
				}
			}

			$diffe = SyncUtilListDefinition::keyDiff(
				$newcfg[$t]["TYPES"], $curcfg[$t]["TYPES"]);

			if (count($diffe["add"])) {
				foreach ($diffe["add"] as $te) {

					$this->printModRow(
						$i18n["Added list item definition"],
						"<img src=\"../img/admin/plus.png\" ".
							"alt=\"{$i18n["add"]}\" ".
							"title=\"{$i18n["add"]}\">",
						$newrecs[$te],
						"mod_application_definition:".
							"{$newcfg[$t]["application_definition_id"]}:".
							"add_list_item_definition:{$te}",
						++$cnt);
				}
			}

			if (count($diffe["rem"])) {
				foreach ($diffe["rem"] as $te) {

					$this->printModRow(
						$i18n["Deleted list item definition"],
						$oldrecs[$te],
						"<img src=\"../img/admin/delete.png\" ".
							"alt=\"{$i18n["delete"]}\" ".
							"title=\"{$i18n["delete"]}\">",
						"mod_application_definition:".
							"{$curcfg[$t]["application_definition_id"]}:".
							"del_list_item_definition:{$te}",
						++$cnt);
				}
			}

			if (count($diffe["mod"])) {
				foreach ($diffe["mod"] as $te) {

				$this->pr("<tr".(++$cnt%2==1?" class=\"row-color\"":"").
					"><td colspan=\"4\"><b>".
					"{$i18n["Modified list item definition"]} ".
					"({$oldrecs[$te]}):</b></td></tr>");

					foreach ($curcfg[$t]["TYPES"][$te] as $k => $tm) {

						if ($tm != $newcfg[$t]["TYPES"][$te][$k]) {

							if ("parent_type_ids" == $k) {

								$o = $tm;
								$n = $newcfg[$t]["TYPES"][$te][$k];
								array_walk($o, function(&$id, $k, $l)
									{ $id = $l[$k]; }, $oldrecs);
								array_walk($n, function(&$id, $k, $l)
									{ $id = $l[$k]; }, $newrecs);
								$data1 = join(",",$o);
								$data2 = join(",",$n);

							} else if (is_array($tm)) {

								$data1 = "(".join(",",$tm).")";
								$data2 = "(".
									join(",",$newcfg[$t]["TYPES"][$te][$k]).")";

							} else {

								$data1 = $tm;
								$data2 = $newcfg[$t]["TYPES"][$te][$k];

							}

							$this->printModRow(
								$record_labels[$k], $data1, $data2,
								"mod_application_definition:".
									"{$curcfg[$t]["application_definition_id"]}:".
									"mod_list_item_definition:{$te}:{$k}",
									++$cnt, 4);

						}
					}
				}
			}

			$diffe = SyncUtilListDefinition::keyDiff(
				$newcfg[$t]["FIELDS"], $curcfg[$t]["FIELDS"]);

			if (count($diffe["add"])) {
				foreach ($diffe["add"] as $te) {

					$this->printModRow(
						$i18n["Added list item property definition"],
						"<img src=\"../img/admin/plus.png\" ".
							"alt=\"{$i18n["add"]}\" ".
							"title=\"{$i18n["add"]}\">",
						$newfields[$te],
						"mod_application_definition:".
							"{$newcfg[$t]["application_definition_id"]}:".
							"add_list_item_property_definition:{$te}",
						++$cnt);
				}
			}

			if (count($diffe["rem"])) {
				foreach ($diffe["rem"] as $te) {

					$this->printModRow(
						$i18n["Deleted list item property definition"],
						$oldfields[$te],
						"<img src=\"../img/admin/delete.png\" ".
							"alt=\"{$i18n["delete"]}\" ".
							"title=\"{$i18n["delete"]}\">",
						"mod_application_definition:".
							"{$curcfg[$t]["application_definition_id"]}:".
							"del_list_item_property_definition:{$te}",
						++$cnt);
				}
			}

			if (count($diffe["mod"])) {
				foreach ($diffe["mod"] as $te) {

					$this->pr("<tr".(++$cnt%2==1?" class=\"row-color\"":"").">".
						"<td colspan=\"4\"><b>".
						"{$i18n["Modified list item property definition"]} ".
						"({$oldfields[$te]}):</b></td></tr>");

					foreach ($curcfg[$t]["FIELDS"][$te] as $k => $tm) {
						if ($tm != $newcfg[$t]["FIELDS"][$te][$k]) {

							if ("config" == $k) {
								$data1 =
									$curcfg[$t]["FIELDS"][$te]["in_list"][$tm];
								$data2 = $newcfg[$t]["FIELDS"][$te]["in_list"]
									[$newcfg[$t]["FIELDS"][$te][$k]];
							} else if ("list_item_definition_id" == $k) {
								$data1 = $oldrecs[$tm];
								$data2 =
									$newrecs[$newcfg[$t]["FIELDS"][$te][$k]];
							} else if (is_array($tm)) {
								$data1 = "(".join(",",$tm).")";
								$data2 = "(".join(",",
									$newcfg[$t]["FIELDS"][$te][$k]).")";
							} else {
								$data1 = $tm;
								$data2 = $newcfg[$t]["FIELDS"][$te][$k];
							}
							$this->printModRow(
								$field_labels[$k], $data1, $data2,
								"mod_application_definition:".
									"{$curcfg[$t]["application_definition_id"]}:".
									"mod_list_item_property_definition:".
									"{$te}:{$k}",
								++$cnt, 4);
						}
					}
				}
			}
		}
?>
	</table>
<?php
	}
?>
	<button type="submit"><?php echo $i18n["Synchronize"]?></button>
</form>
<?php
} else {
?>
<p><?php echo $i18n["Nothing to synchronize."]?></p>
<?php
}
?>
<?php

$this->endSection();

?>