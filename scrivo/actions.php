<?php
/* Copyright (c) 2011, Geert Bergman (geert@scrivo.nl)
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
 * $Id: actions.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Definitions of the actions for the config user interface.
 */

namespace ScrivoUi\Editor\Actions;

use \Scrivo\Action;
use \Scrivo\User;

/**
 * The action array maps the user interface action identifiers to action
 * descriptors: definitions for creating actions.
 */
$actions = array(

	"home" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\Home",
	),

	"login" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\RedirectToLogin",
	),
	"login_check" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Login",
		Action::FORWARD => array(
			Action::SUCCESS => "home",
			Action::FAIL => "login",
		),
	),
	"logout" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Logout",
		Action::FORWARD => array(
			Action::SUCCESS => "login",
		),
	),

	"loginXhr" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Login"),
	"getTabs" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\GetTabs"),
	"pagePath" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\PagePath"),
	"pageTree" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\PageTree"),
	"displayURL" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\DisplayUrl"),
	"getAccess" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\GetAccess"),
	"setAccess" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\SetAccess"),
	"folderTree" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FolderTree"),
	"folderPath" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FolderPath"),

	"menutree.getMenu" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\GetMenu"),
	"menutree.moveUp" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\MoveUp"),
	"menutree.moveDown" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\MoveDown"),
	"menutree.movePage" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\MovePage"),
	"menutree.getPageProperties" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\GetPageProperties"),
	"menutree.savePageProperties" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\SavePageProperties"),
	"menutree.deletePage" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\MenuTree\DeletePage"),

	"contenttabs.getPropertyList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\GetProperties"),
	"contenttabs.savePropertyList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\SaveProperties"),
	"contenttabs.getDefaultProperties" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\GetDefaultProperties"),
	"contenttabs.saveDefaultProperties" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\SaveDefaultProperties"),
	"contenttabs.getPageContent" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\GetPageContent"),
	"contenttabs.savePageContent" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\SavePageContent"),
	"contenttabs.getApplication" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\ContentTabs\GetApplication"),

	"htmleditor.anchors" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\HtmlEditor\FetchAnchors"),
	"htmleditor.languageList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\HtmlEditor\LanguageList"),
	"htmleditor.spell" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\HtmlEditor\SpellCheck"),

	"filedialog.assetDetails" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\AssetDetails"),
	"filedialog.assetList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\AssetList"),
	"filedialog.assetProperties" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\AssetProperties"),
	"filedialog.assetUpdate" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\AssetUpdate"),
	"filedialog.cacheSettings" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\CacheSettings"),
	"filedialog.crop" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\Crop"),
	"filedialog.cropCanvas" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\CropCanvas"),
	"filedialog.delete" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\Delete"),
	"filedialog.folderList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\FolderList"),
	"filedialog.feedList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\FeedList"),
	"filedialog.folderProperties" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\FolderProperties"),
	"filedialog.folderUpdate" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\FolderUpdate"),
	"filedialog.paste" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\FileDialog\Paste"),
		
	"apps.list.getListView" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Applications\ItemList\GetListView"),
	"apps.list.getBlockList" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Applications\ItemList\GetBlockList"),
	"apps.list.getListItemDefinitions" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION =>
			__NAMESPACE__."\Applications\ItemList\GetListItemDefinitions"),
	"apps.list.getBlockListPositions" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION =>
			__NAMESPACE__."\Applications\ItemList\GetBlockListPostions"),
	"apps.list.getListItem" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Applications\ItemList\GetListItem"),
	"apps.list.saveListItem" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Applications\ItemList\SaveListItem"),
	"apps.list.deleteListItems" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION =>
			__NAMESPACE__."\Applications\ItemList\DeleteListItems"),
	"apps.list.moveUp" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Applications\ItemList\MoveUp"),
	"apps.list.moveDown" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Applications\ItemList\MoveDown"),
	"apps.list.moveToPosition" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION =>
			__NAMESPACE__."\Applications\ItemList\MoveToPosition"),

);


?>