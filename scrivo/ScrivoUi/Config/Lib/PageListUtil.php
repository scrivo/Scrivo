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
 * $Id: PageListUtil.php 852 2013-08-21 12:43:09Z geert $
 */

namespace ScrivoUi\Config\Lib;

class PageListUtil {

	public static function icon($type, $i18n) {
		$f = ($type == \Scrivo\Page::TYPE_NAVIGATION_ITEM
			? "navigate"
			: ($type == \Scrivo\Page::TYPE_NAVIGABLE_PAGE
				? "page_navigate"
				: ($type == \Scrivo\Page::TYPE_NON_NAVIGABLE_PAGE
					? "page"
					: ($type == \Scrivo\Page::TYPE_SUB_FOLDER
						? "folder"
						: ($type == \Scrivo\Page::TYPE_APPLICATION
							? "tools"
							: "")))));
		$t = ($type == \Scrivo\Page::TYPE_NAVIGATION_ITEM
			? $i18n["Navigation item"]
			: ($type == \Scrivo\Page::TYPE_NAVIGABLE_PAGE
				? $i18n["Navigatable page"]
				: ($type ==	\Scrivo\Page::TYPE_NON_NAVIGABLE_PAGE
					? $i18n["Not navigatable page"]
					: ($type == \Scrivo\Page::TYPE_SUB_FOLDER
						? $i18n["subfolder"]
						: ($type == \Scrivo\Page::TYPE_APPLICATION
							? $i18n["application"]
							: "")))));
		return "<img class=\"treeimg\" src=\"../img/admin/{$f}.png\" ".
			"alt=\"{$t}\" title=\"{$t}\">";
	}

	public static function pageToData(\Scrivo\Page $p, $i18n, $labels) {

		$r = new \stdClass;

		$r->title = (string)$p->title;
		$r->icon = self::icon($p->type, $i18n);
		$r->label = isset($labels[$p->id]) ? (string)$labels[$p->id] : "";
		$r->pageDefinitionId = $p->definition->id;
		$r->pageDefinitionTitle = (string)$p->definition->title;
		$r->pageId = $p->id;
		$r->deletable = !count($p->children);
		$r->hasChildren = count($p->children);
		$r->depth = count($p->path);

		return $r;
	}

}

?>