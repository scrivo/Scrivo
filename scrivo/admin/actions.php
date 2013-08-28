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
 * $Id: actions.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Definitions of the actions for the admin user interface.
 */

namespace ScrivoUi\Admin\Actions;

use \Scrivo\Action;
use \Scrivo\User;

$actions = array(

	"home" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\Home",
	),

	"login" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\Home",
	),
	"login_check" => array(
		Action::AUTH => User::STATUS_MEMBER,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\Login",
		Action::FORWARD => array(
			Action::SUCCESS => "home",
			Action::FAIL => "login",
		),
	),
	"logout" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\Logout",
		Action::FORWARD => array(
			Action::SUCCESS => "login",
		),
	),

	/* editors */

	"editor_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UserList",
		Action::PARAMS => array("type" => "editor"),
	),
	"editor_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UserForm",
		Action::PARAMS => array("type" => "editor"),
	),
	"editor_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUser",
		Action::FILE => "actions/user_update.php",
		Action::PARAMS => array("type" => "editor"),
		Action::FORWARD => array(
			Action::SUCCESS => "editor_list",
			Action::FAIL => "editor_form",
		),
	),
	"editor_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertUser",
		Action::PARAMS => array("type" => "editor"),
		Action::FORWARD => array(
			Action::SUCCESS => "editor_list",
			Action::FAIL => "editor_form",
		),
	),
	"editor_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteUser",
		Action::PARAMS => array("type" => "editor"),
		Action::FORWARD => array(
			Action::SUCCESS => "editor_list",
			Action::FAIL => "editor_list",
		),
	),
	"editor_password_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUserPassword",
		Action::PARAMS => array("type" => "editor"),
		Action::FORWARD => array(
			Action::SUCCESS => "editor_list",
			Action::FAIL => "editor_form",
		),
	),

	/* members */

	"member_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UserList",
		Action::PARAMS => array("type" => "member"),
	),
	"member_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UserForm",
		Action::PARAMS => array("type" => "member"),
	),
	"member_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUser",
		Action::PARAMS => array("type" => "member"),
		Action::FORWARD => array(
			Action::SUCCESS => "member_list",
			Action::FAIL => "member_form",
		),
	),
	"member_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertUser",
		Action::PARAMS => array("type" => "member"),
		Action::FORWARD => array(
			Action::SUCCESS => "member_list",
			Action::FAIL => "member_form",
		),
	),
	"member_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteUser",
		Action::PARAMS => array("type" => "member"),
		Action::FORWARD => array(
			Action::SUCCESS => "member_list",
			Action::FAIL => "member_list",
		),
	),
	"member_password_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUserPassword",
		Action::PARAMS => array("type" => "member"),
		Action::FORWARD => array(
			Action::SUCCESS => "member_list",
			Action::FAIL => "member_form",
		),
	),

	/* admins */

	"admin_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UserList",
		Action::PARAMS => array("type" => "admin"),
	),
	"admin_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\UserForm",
		Action::PARAMS => array("type" => "admin"),
	),
	"admin_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUser",
		Action::PARAMS => array("type" => "admin"),
		Action::FORWARD => array(
			Action::SUCCESS => "admin_list",
			Action::FAIL => "admin_form",
		),
	),
	"admin_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertUser",
		Action::PARAMS => array("type" => "admin"),
		Action::FORWARD => array(
			Action::SUCCESS => "admin_list",
			Action::FAIL => "admin_form",
		),
	),
	"admin_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteUser",
		Action::PARAMS => array("type" => "admin"),
		Action::FORWARD => array(
			Action::SUCCESS => "admin_list",
			Action::FAIL => "admin_list",
		),
	),
	"admin_password_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateUserPassword",
		Action::PARAMS => array("type" => "admin"),
		Action::FORWARD => array(
			Action::SUCCESS => "admin_list",
			Action::FAIL => "admin_form",
		),
	),

	/* requests */

	"requests_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\RequestsList",
	),
	"request_activate" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\ActivateEditor",
		Action::FORWARD => array(
			Action::SUCCESS => "editor_form",
			Action::FAIL => "requests_list",
		),
	),
	"request_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteUser",
		Action::PARAMS => array("type" => "request"),
		Action::FORWARD => array(
			Action::SUCCESS => "requests_list",
			Action::FAIL => "requests_list",
		),
	),


	/* roles */

	"role_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\RoleList",
	),
	"role_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\RoleForm",
	),
	"role_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateRole",
		Action::FORWARD => array(
			Action::SUCCESS => "role_list",
			Action::FAIL => "role_form",
		),
	),
	"role_insert" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InsertRole",
		Action::FORWARD => array(
			Action::SUCCESS => "role_list",
			Action::FAIL => "role_form",
		),
	),
	"role_delete" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteRole",
		Action::FORWARD => array(
			Action::SUCCESS => "role_list",
			Action::FAIL => "role_list",
		),
	),

	"page_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PageList",
	),
	"page_list_ajax" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Xhr\PageList",
	),
	"language_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\LanguageForm",
	),
	"language_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\UpdateLanguage",
		Action::FORWARD => array(
				Action::SUCCESS => "page_list",
				Action::FAIL => "language_form",
		),
	),
	"page_access_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::PARAMS => array("type" => "page"),
		Action::ACTION => __NAMESPACE__."\Layout\AccessForm",
	),
	"page_access_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::PARAMS => array("type" => "page"),
		Action::ACTION => __NAMESPACE__."\Forward\UpdateAccess",
		Action::FORWARD => array(
				Action::SUCCESS => "page_list",
				Action::FAIL => "page_access_form",
		),
	),

	"asset_list" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\AssetList",
	),
	"asset_list_ajax" => array(
		Action::AUTH => User::STATUS_EDITOR,
		Action::TYPE => Action::XHR,
		Action::ACTION => __NAMESPACE__."\Xhr\AssetList",
	),
	"asset_access_form" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::VIEW,
		Action::PARAMS => array("type" => "asset"),
		Action::ACTION => __NAMESPACE__."\Layout\AccessForm",
	),
	"asset_access_update" => array(
		Action::AUTH => User::STATUS_ADMIN,
		Action::TYPE => Action::FORWARD,
		Action::PARAMS => array("type" => "asset"),
		Action::ACTION => __NAMESPACE__."\Forward\UpdateAccess",
		Action::FORWARD => array(
			Action::SUCCESS => "asset_list",
			Action::FAIL => "asset_access_form",
		),
	)

);

?>