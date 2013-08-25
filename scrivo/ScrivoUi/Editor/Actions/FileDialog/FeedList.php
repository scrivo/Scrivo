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
 * $Id: FeedList.php 866 2013-08-25 16:22:35Z geert $
 */

namespace ScrivoUi\Editor\Actions\FileDialog;

use \Scrivo\Action;
use \Scrivo\Request;

/**
 * The FeedList class implements an action for reading an external feed to
 * show it in the Scrivo file dialog. A template for the XML for the feed
 * is given below.
 *
 *	<urllist pid="" id="id44963">
 * 		<urllistheaders>
 *			<col type="string" width="250" align="left">Name</col>
 *			<col type="string" width="50" align="left">LST</col>
 *			<col type="filesize" width="75" align="right">Size</col>
 *			<col type="string" width="100" align="left">Type</col>
 *			<col type="date" width="100" align="left">Modified</col>
 *		</urllistheaders>
 *		<folder id="id44965">
 *			<col>Archive</col>
 *			<col/>
 *			<col/>
 *			<col/>
 *			<col>2007-11-13 01:38:22</col>
 *		</folder>
 *		...
 *		<url mimetype="application/pdf" url="http://www.v6.org/file.pdf">
 *			<col>Summary Impactanalysis 2010.pdf</col>
 *			<col>No</col>
 *			<col>143610</col>
 *			<col>application/pdf</col>
 *			<col>2010-10-04 09:52:35</col>
 *		</url>
 *		...
 *	</urllist>
 */

class FeedList extends Action {

	/**
	 * In this action an external XML feed is read so its contents can be used
	 * it the Scrivo file dialog.
	 */
	function doAction() {

		$itemId = Request::get("itemId", Request::TYPE_INTEGER);
		$feedId = Request::get("feedId", Request::TYPE_INTEGER);

		if ($feedId == $itemId) {
			$itemId = 0;
		}
		$url = Request::get("url", Request::TYPE_STRING);

		$res = array();
		$res["result"] = "OK";

		$ul = "{$url}?id={$itemId}";
		$ufl = new XMLUrlListReader($ul);

		$res = array(
			"headers" => $ufl->headers[0],
			"folders" => $ufl->folders,
			"urls" => $ufl->urls
		);

		$this->setResult(self::SUCCESS, $res);
	}
}

/**
 * A simple on the fly parser for the feed XML.
 * TODO: why not SimpleXML?, and if not move it to somewhere else.
 */
class XMLUrlListReader  {
	var $parser;

	var $level = 0;
	var $currentNode;
	var $currentNodeData;
	var $currentData;
	var $currentCol;

	var $headers = Array();
	var $folders = Array();
	var $urls = Array();

	function XMLUrlListReader($data) {
		$this->parser = xml_parser_create("UTF-8");

		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, "tag_open", "tag_close");
		xml_set_character_data_handler($this->parser, "data");

		xml_parse($this->parser, file_get_contents($data));
	}

	function tag_open($parser, $tag, $attributes) {
		$this->level++;
		if ($this->level == 2) {
			$this->currentCol = 0;
			$this->currentNode = $tag;
			$this->currentNodeData = Array();
			if ($this->currentNode != "URLLISTHEADERS") {
				$this->currentNodeData["attr"] = $attributes;
				$this->currentNodeData["data"] = Array();
			}
		}
		if ($this->level == 3) {
			if ($this->currentNode == "URLLISTHEADERS") {
				$this->currentNodeData[]["attr"] = $attributes;
			}
			$this->currentCol++;
		}
	}

	function tag_close($parser, $tag) {
		if ($this->level == 2) {
			if ($this->currentNode == "FOLDER") {
				$this->folders[] = $this->currentNodeData;
			} else if ($this->currentNode == "url") {
				$this->urls[] = $this->currentNodeData;
			} else if ($this->currentNode == "URLLISTHEADERS") {
				$this->headers[] = $this->currentNodeData;
			}
		}
		if ($this->level == 3) {
			if ($this->currentNode == "URLLISTHEADERS") {
				$this->currentNodeData[count($this->currentNodeData)-1]["data"]
				= $this->currentData;
			} else {
				$dat = trim($this->currentData);
				$c = $this->headers[0][$this->currentCol-1]["attr"];
				if (isset($c["type"]) && $c["type"] == "date") {
					$dat = sqltimestamp_to_timestamp($dat);
				}
				$this->currentNodeData["data"][] = $dat;
			}
		}
		$this->currentData = "";
		$this->level--;
	}

	function data($parser, $data) {
		$this->currentData .= $data;
	}
}

?>