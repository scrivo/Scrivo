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

namespace ScrivoUi\DbConsole\Actions;

use \Scrivo\Action;
use \Scrivo\User;

$actions = array(

	"home" => array(
		Action::AUTH => User::STATUS_EDITOR,
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
		Action::AUTH => User::STATUS_EDITOR,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\Logout",
		Action::FORWARD => array(
			Action::SUCCESS => "login",
		),
	),

	"export_instance_form" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ExportInstanceForm",
	),
	"export_instance" => array(
		Action::AUTH => 1,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\ExportInstance",
		Action::FORWARD => array(
			Action::SUCCESS => "export_instance_download_link",
			Action::FAIL => "export_instance_form",
		),
	),
	"export_instance_download_link" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::PARAMS => array("a" => "export_instance_download"),
		Action::ACTION => __NAMESPACE__."\Layout\DownloadLink",
	),
	"export_instance_download" => array(
		Action::AUTH => 1,
		Action::type => Action::DOWNLOAD,
		Action::PARAMS => array("a" => "export_instance_download"),
		Action::ACTION => __NAMESPACE__."\Download\Download",
	),

	"export_assets_form" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\AssetExportForm",
	),
	"export_assets" => array(
		Action::AUTH => 1,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\ExportAssets",
		Action::FORWARD => array(
			Action::SUCCESS => "export_assets_download_link",
			Action::FAIL => "export_assets_form",
		),
	),
	"export_assets_download_link" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::PARAMS => array("a" => "export_assets_download"),
		Action::ACTION => __NAMESPACE__."\Layout\DownloadLink",
	),
	"export_assets_download" => array(
		Action::AUTH => 1,
		Action::type => Action::DOWNLOAD,
		Action::PARAMS => array("a" => "export_assets_download"),
		Action::ACTION => __NAMESPACE__."\Download\Download",
	),

	"delete_instance_form" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\DeleteInstanceForm",
	),
	"delete_instance" => array(
		Action::AUTH => 1,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\DeleteInstance",
		Action::FORWARD => array(
			Action::SUCCESS => "export_instance_download_link",
			Action::FAIL => "delete_instance_form",
		),
	),

	"copy_branch_form" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\CopyBranchForm",
	),
	"copy_branch" => array(
		Action::AUTH => 1,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\CopyBranch",
		Action::FORWARD => array(
			Action::SUCCESS => "copy_branch_download_link",
			Action::FAIL => "copy_branch_form",
		),
	),
	"copy_branch_download_link" => array(
			Action::AUTH => 1,
			Action::type => Action::VIEW,
			Action::PARAMS => array("a" => "copy_branch_download"),
			Action::ACTION => __NAMESPACE__."\Layout\DownloadLink",
	),
	"copy_branch_download" => array(
			Action::AUTH => 1,
			Action::type => Action::DOWNLOAD,
			Action::PARAMS => array("a" => "copy_branch_download"),
			Action::ACTION => __NAMESPACE__."\Download\Download",
	),

	"paste_branch_form" => array(
		Action::AUTH => 1,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\PasteBranchForm",
	),
	"paste_branch" => array(
		Action::AUTH => 1,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\PasteBranch",
		Action::FORWARD => array(
			Action::SUCCESS => "home",
			Action::FAIL => "paste_branch_form",
		),
	),

	"initialize.systemCheck" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\SystemCheck",
	),
	"initialize.installationOptions" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\InstallationOptions",
	),
	"initialize.createDatabaseForm" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\CreateDatabaseForm",
	),
	"initialize.createDatabase" => array(
		Action::AUTH => 3,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\CreateDatabase",
		Action::FORWARD => array(
			Action::SUCCESS => "initialize.createInstanceForm",
			Action::FAIL => "initialize.createDatabaseForm",
		),
	),
	"initialize.initializeDatabaseForm" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::PARAMS => array("action" => "initializeDatabaseForm"),
		Action::ACTION => __NAMESPACE__."\Layout\DatabaseParametersForm",
	),
	"initialize.initializeDatabase" => array(
		Action::AUTH => 3,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\InitializeDatabase",
		Action::FORWARD => array(
			Action::SUCCESS => "initialize.createInstanceForm",
			Action::FAIL => "initialize.initializeDatabaseForm",
		),
	),
	"initialize.downloadDatabaseModel" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\DownloadDatabaseModel",
	),
	"initialize.selectDatabaseForm" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::PARAMS => array("action" => "selectDatabaseForm"),
		Action::ACTION => __NAMESPACE__."\Layout\DatabaseParametersForm",
	),
	"initialize.selectDatabase" => array(
		Action::AUTH => 3,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\SelectDatabase",
		Action::FORWARD => array(
			Action::SUCCESS => "initialize.createInstanceForm",
			Action::FAIL => "initialize.selectDatabaseForm",
		),
	),
	"initialize.createInstanceForm" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\CreateInstanceForm",
	),
	"initialize.createInstance" => array(
		Action::AUTH => 3,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\CreateInstance",
		Action::FORWARD => array(
			Action::SUCCESS => "home",
			Action::FAIL => "initialize.createInstanceForm",
		),
	),

	"import.importInstanceForm" => array(
		Action::AUTH => 3,
		Action::type => Action::VIEW,
		Action::ACTION => __NAMESPACE__."\Layout\ImportForm",
	),
	"import.importInstance" => array(
		Action::AUTH => 3,
		Action::type => Action::FORWARD,
		Action::ACTION => __NAMESPACE__."\Forward\ImportInstance",
		Action::FORWARD => array(
			Action::SUCCESS => "home",
			Action::FAIL => "import.importInstanceForm",
		),
	),

);

?>