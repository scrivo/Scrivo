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
 * $Id: CopyBranch.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the ScrivoUi\DbConsole\Actions\Forward\CopyBranch
 * action class.
 */

namespace ScrivoUi\DbConsole\Actions\Forward;

use \Scrivo\Request;
use \Scrivo\Action;
use \ScrivoUi\DbConsole\Lib\Util;

/**
 * The CopyBranch action class implements an action for exporting a content
 * branch from a Scrivo database. Note that the configuration of the instances
 * where you want to share data between must be in sync.
 */
class CopyBranch extends Action {

	private $DONE_KEYS = array();

	/**
	 * In this action a export is made for all Scrivo data rows belonging
	 * to a specific page and its children. All referencing ids in the data
	 * are identified and marked.
	 */
	function doAction() {

		set_time_limit(300);

		$outFile = sys_get_temp_dir()."/".Util::cleanWwwRoot(
			$this->context->config->WWW_ROOT)."_dbc_branch.sql.gz";

		$out = gzopen($outFile, "w9");
		if (!$out) {

			$i18n = new I18n($this->context->config->ui_lang);
			$res = $i18n["Could not open export file!"];
			$this->setResult(self::FAIL, $res);

		} else {

			$this->recurse($out,
				Request::post("page_id", Request::TYPE_INTEGER));
			gzclose($out);

		}

		$this->setResult(self::SUCCESS);
	}


	private function dumpDoc($out, $id, $base, $dir) {

		$instId = $this->context->config->INSTANCE_ID;

		$doc_keys = Array(
			"page_id" => true,
			"parent_id" => true);
		$content_element_keys = Array(
			"page_id" => true);
		$document_property_keys = Array(
			"page_id" => true);

		$list_keys = Array(
			"item_list_id" => true,
			"page_id" => true,
			"folder_id" => true);
		$list_item_keys = Array(
			"list_item_id" => true,
			"parent_id" => true,
			"page_id" => true,
			"item_list_id" => true,
			"page_id" => true);
		$list_item_value_keys = Array(
			"list_item_id" => true,
			"page_id" => true);

		$form_keys = Array(
			"FORM_ID" => true,
			"page_id" => true,
			"FORM_ELEMENT_ID" => true);
		$form_element_keys = Array(
			"FORM_ELEMENT_ID" => true,
			"FORM_ID" => true);

		$this->dumpRec($out, "page", $instId,
			$base, $dir, $doc_keys, $id);
		$this->dumpRec($out, "page_property_html", $instId,
			$base, $dir, $content_element_keys, $id);
		$this->dumpRec($out, "page_property", $instId,
			$base, $dir, $document_property_keys, $id);

		// item_list

		$sth = $this->context->connection->prepare("select item_list_id from item_list
			where instance_id = :instId and page_id = :pageId");
		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $id, \PDO::PARAM_INT);
		$sth->execute();

		$ls = array();
		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$ls[] = $rd["item_list_id"];
		}

		foreach ($ls as $l) {
			$this->dumpRec($out, "item_list", $instId,
				$base, $dir, $list_keys, $l);
		}

		$lis = array();
		if (count($ls)) {

			$sth = $this->context->connection->prepare("select list_item_id
				from list_item where instance_id = :instId and item_list_id IN (:ids)");
			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":ids", implode(",", $ls), \PDO::PARAM_STR);
			$sth->execute();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$lis[] = $rd["list_item_id"];
			}

			foreach ($lis as $l) {
				$this->dumpRec($out, "list_item", $instId,
					$base, $dir, $list_item_keys, $l);
			}
		}

		if (count($lis)) {

			$sth = $this->context->connection->prepare("select list_item_id
				from list_item_property where instance_id = :instId and
				list_item_id IN (:ids)");
			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":ids", implode(",", $lis), \PDO::PARAM_STR);
			$sth->execute();

			$lvs = array();
			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$lvs[] = $rd["list_item_id"];
			}

			foreach ($lvs as $l) {
				$this->dumpRec($out, "list_item_property", $instId,
					$base, $dir, $list_item_value_keys, $l);
			}
		}

		// FORM

		$sth = $this->context->connection->prepare("select FORM_ID from FORM
			where instance_id = :instId and page_id = :pageId");
		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":pageId", $id, \PDO::PARAM_INT);
		$sth->execute();

		$ls = array();
		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$ls[] = $rd["FORM_ID"];
		}

		foreach ($ls as $l) {
			$this->dumpRec($out, "FORM", $instId,
				$base, $dir, $form_keys, $l);
		}

		$lis = array();
		if (count($ls)) {

			$sth = $this->context->connection->prepare(
				"select FORM_ELEMENT_ID from FORM_ELEMENT
				where instance_id = :instId and FORM_ID IN (:ids)");
			$this->context->connection->bindInstance($sth);
			$sth->bindValue(":ids", implode(",", $ls), \PDO::PARAM_STR);
			$sth->execute();

			while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
				$lis[] = $rd["FORM_ELEMENT_ID"];
			}

			foreach ($lis as $l) {
				$this->dumpRec($out, "FORM_ELEMENT", $instId,
					$base, $dir, $form_element_keys, $l);
			}
		}

	}

	private function dumpRec(
			$out, $table, $inst_id, $old_base, $old_adir, $keys, $id) {

		$sql = "";
		list($k) = each($keys);

		$sth = $this->context->connection->prepare("select * from {$table}
			where instance_id = :instId and {$k} = :k");
		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":k", $id, \PDO::PARAM_INT);
		$sth->execute();

		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$stmts = Util::createInsertStatment(
				$rd, $table, $inst_id, $old_base, $old_adir, $keys);
			// if its only content it should not be necessary to do this:
			//    no duplication of keys possible
			if (isset($this->DONE_KEYS[$stmts])) {
				return;
			}
			$this->DONE_KEYS[$stmts] = true;
			gzwrite($out, $stmts);
		}
	}

	private  function recurse($out, $id) {

		$this->dumpDoc($out, $id, $this->context->config->WWW_ROOT,
			$this->context->config->UPLOAD_DIR);

		$sth = $this->context->connection->prepare("select page_id from
			page where instance_id = :instId and parent_id = :k");
		$this->context->connection->bindInstance($sth);
		$sth->bindValue(":k", $id, \PDO::PARAM_INT);
		$sth->execute();

		$c = array();
		while ($rd = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$c[] = $rd["page_id"];
		}

		foreach ($c as $id) {
			$this->recurse($out, $id);
		}
	}

}
?>
