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
 * $Id: actions.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Definitions of the actions for the config user interface.
 */

namespace ScrivoUi\Config\Actions;

use \Scrivo\Action;
use \Scrivo\User;

/**
 * The action array maps the user interface action identifiers to action
 * descriptors: definitions for creating actions.
 */
$actions = array(

	"home" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\Home",
	),

	"login" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\Home",
	),
	"login_check" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\Login",
		Action::FORWARD => array(
			Action::SUCCESS => "home",
			Action::FAIL => "login",
		),
	),
	"logout" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\Logout",
		Action::FORWARD => array(
			Action::SUCCESS => "login",
		),
	),

	"sync_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\SyncForm",
	),
	"sync_switch" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\SyncSwitch",
		Action::FORWARD => array(
			"page_definition" => "page_definition_sync_form",
			"list_definition" => "list_definition_sync_form",
			Action::FAIL => "sync_form",
		),
	),

	"page_definition_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionList",
	),
	"page_definition_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionForm",
	),
	"page_definition_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertPageDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_form",
		),
	),
	"page_definition_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdatePageDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_list",
			Action::FAIL => "page_definition_form",
		),
	),
	"page_definition_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeletePageDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_list",
			Action::FAIL => "page_definition_list",
		),
	),

	"page_definition_export" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionExport",
	),
	"page_definition_download" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::DOWNLOAD,
		Action::ACTION => __NAMESPACE__."\Download\ExportPageDefinition",
	),
	"page_definition_import" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\ImportPageDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_list",
		),
	),

	"page_definition_tab_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionTabForm",
	),
	"page_definition_content_tab_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionContentTabForm",
	),
	"page_definition_application_tab_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION =>
			__NAMESPACE__."\Layout\PageDefinitionApplicationTabForm",
	),
	"page_definition_tab_copy_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionTabCopyForm",
	),
	"page_definition_tab_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\InsertPageDefinitionPropertyTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_tab_form",
		),
	),
	"page_definition_tab_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdatePageDefinitionTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_tab_form",
		),
	),
	"page_definition_application_tab_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\InsertPageDefinitionApplicationTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_application_tab_form",
		),
	),
	"page_definition_application_property_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\UpdatePageDefinitionApplicationProperty",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_application_tab_form",
		),
	),
	"page_definition_content_tab_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\InsertPageDefinitionContentTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_content_tab_form",
		),
	),
	"page_definition_content_property_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\UpdatePageDefinitionContentProperty",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_content_tab_form",
		),
	),
	"page_definition_tab_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeletePageDefinitionTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_form",
		),
	),
	"page_definition_tab_copy" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\CopyPageDefinitionTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_form",
		),
	),
	"page_definition_tab_move" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\MovePageDefinitionTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_form",
		),
	),

	"page_property_definition_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PagePropertyDefinitionForm",
	),
	"page_property_definition_copy_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION =>
			__NAMESPACE__."\Layout\PagePropertyDefinitionCopyForm",
	),
	"page_property_definition_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertPagePropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_property_definition_form",
		),
	),
	"page_property_definition_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdatePagePropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_property_definition_form",
		),
	),
	"page_property_definition_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeletePagePropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_form",
		),
	),
	"page_property_definition_copy" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\CopyPagePropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_property_definition_copy_form",
		),
	),
	"page_property_definition_move" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\MovePagePropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_form",
			Action::FAIL => "page_definition_form",
		),
	),

	"page_definition_sync_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageDefinitionSyncForm",
	),
	"page_definition_sync" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\SyncPageDefinitions",
		Action::FORWARD => array(
			Action::SUCCESS => "page_definition_sync_form",
			Action::FAIL => "page_definition_sync_form",
		),
	),

	"application_definition_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ApplicationDefinitionList",
	),
	"application_definition_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ApplicationDefinitionForm",
	),
	"application_definition_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertApplicationDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "application_definition_form",
		),
	),
	"application_definition_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateApplicationDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_list",
			Action::FAIL => "application_definition_form",
		),
	),
	"application_definition_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteApplicationDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_list",
			Action::FAIL => "application_definition_list",
		),
	),

	"application_definition_export" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ApplicationDefinitionExport",
	),
	"application_definition_download" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::DOWNLOAD,
		Action::ACTION => __NAMESPACE__."\Download\ExportApplicationDefinition",
	),
	"application_definition_import" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\ImportApplicationDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::WARN => "application_definition_form",
			Action::FAIL => "application_definition_list",
		),
	),

	"list_item_definition_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ListItemDefinitionForm",
	),
	"list_item_definition_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertListItemDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "list_item_definition_form",
		),
	),
	"list_item_definition_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateListItemDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "list_item_definition_form",
		),
	),
	"list_item_definition_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteListItemDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "application_definition_form",
		),
	),
	"list_item_definition_move" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\MoveListItemDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "application_definition_form",
		),
	),

	"list_item_property_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ListItemPropertyForm",
	),
	"list_item_property_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\InsertListItemPropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "list_item_property_form",
		),
	),
	"list_item_property_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\UpdateListItemPropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "list_item_property_form",
		),
	),
	"list_item_property_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\DeleteListItemPropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "application_definition_form",
		),
	),
	"list_item_property_move" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION =>
			__NAMESPACE__."\Forward\MoveListItemPropertyDefinition",
		Action::FORWARD => array(
			Action::SUCCESS => "application_definition_form",
			Action::FAIL => "application_definition_form",
		),
	),

	"list_definition_sync_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ListDefinitionSyncForm",
	),
	"list_definition_sync" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\SyncListDefinitions",
		Action::FORWARD => array(
			Action::SUCCESS => "list_definition_sync_form",
			Action::FAIL => "list_definition_sync_form",
		),
	),

	"language_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\LanguageList",
	),
	"language_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\LanguageForm",
	),
	"language_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertLanguage",
		Action::FORWARD => array(
			Action::SUCCESS => "language_list",
			Action::FAIL => "language_form",
		),
	),
	"language_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateLanguage",
		Action::FORWARD => array(
			Action::SUCCESS => "language_list",
			Action::FAIL => "language_form",
		),
	),
	"language_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteLanguage",
		Action::FORWARD => array(
			Action::SUCCESS => "language_list",
			Action::FAIL => "language_list",
		),
	),

	"ui_language_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UiLanguageList",
	),
	"ui_language_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UiLanguageForm",
	),
	"ui_language_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertUserInterfaceLanguage",
		Action::FORWARD => array(
			Action::SUCCESS => "ui_language_list",
			Action::FAIL => "ui_language_form",
		),
	),
	"ui_language_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUserInterfaceLanguage",
		Action::FORWARD => array(
			Action::SUCCESS => "ui_language_list",
			Action::FAIL => "ui_language_form",
		),
	),
	"ui_language_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteUserInterfaceLanguage",
		Action::FORWARD => array(
			Action::SUCCESS => "ui_language_list",
			Action::FAIL => "ui_language_list",
		),
	),

	"modified_touched_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ModifiedTouchedForm",
	),
	"modified_touched_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertModifiedTouched",
		Action::FILE => "actions/modified_touched_insert",
		Action::FORWARD => array(
			Action::SUCCESS => "modified_touched_form",
			Action::FAIL => "modified_touched_form",
		),
	),
	"modified_touched_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteModifiedTouched",
		Action::FILE => "actions/modified_touched_delete",
		Action::FORWARD => array(
			Action::SUCCESS => "modified_touched_form",
			Action::FAIL => "modified_touched_form",
		),
	),

	"page_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageList",
	),
	"page_list_ajax" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Xhr\PageList",
	),
	"page_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageForm",
	),
	"page_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertPage",
		Action::FORWARD => array(
			Action::SUCCESS => "page_list",
			Action::FAIL => "page_form",
			Action::WARN => "page_list",
		),
	),
	"page_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdatePage",
		Action::FORWARD => array(
			Action::SUCCESS => "page_list",
			Action::FAIL => "page_form",
			Action::WARN => "page_list",
		),
	),
	"page_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeletePage",
		Action::FORWARD => array(
			Action::SUCCESS => "page_list",
			Action::FAIL => "page_list",
		),
	),

	"page_html_tab_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageHtmlTabList",
	),
	"page_html_tab_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageHtmlTabForm",
	),
	"page_html_tab_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdatePageHtmlTab",
		Action::FORWARD => array(
			Action::SUCCESS => "page_html_tab_list",
			Action::FAIL => "page_html_tab_form",
		),
	),

	"asset_list" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\AssetList",
	),
	"asset_list_ajax" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Xhr\AssetList",
	),
	"file_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\FileForm",
	),
	"file_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertFile",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "file_form",
			Action::WARN => "asset_list",
		),
	),
	"file_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateFile",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "file_form",
			Action::WARN => "asset_list",
		),
	),
	"file_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteFile",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "asset_list",
		),
	),
	"folder_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\FolderForm",
	),
	"folder_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertFolder",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "folder_form",
			Action::WARN => "asset_list",
		),
	),
	"folder_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateFolder",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "folder_form",
			Action::WARN => "asset_list",
		),
	),
	"folder_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteFolder",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "asset_list",
		),
	),
);

?>