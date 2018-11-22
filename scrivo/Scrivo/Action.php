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
 * $Id: Action.php 866 2013-08-25 16:22:35Z geert $
 */

/**
 * Implementation of the \Scrivo\Action class.
 */

namespace Scrivo;

class Action {

	const VIEW = 1;
	const FORWARD = 2;
	const TYPE = 3;
	const FILE = 4;
	const SUCCESS = 5;
	const FAIL = 6;
	const WARN = 7;
	const AUTH = 8;
	const PARAMS = 9;
	const ACTION = 10;
	const DOWNLOAD = 11;
	const XHR = 12;

	protected $context = null;
	protected $session = null;
	protected $parameters = null;

	private $auth = \Scrivo\User::STATUS_MEMBER;
	private $type = null;
	private $file = null;
	private $action = null;
	private $forward = array();
	private $result = self::FAIL;
	private $resultData = null;

	public static function create($context, $action, $userStatus, $session) {
		if (isset($action[self::ACTION])) {
			$act = $action[self::ACTION];
			return new $act($context, $action, $userStatus, $session);
		} else {
			return new Action($context, $action, $userStatus, $session);
		}
	}

	function __construct($context, $action, $userStatus, $session=null) {

		$this->auth = $action[self::AUTH];
		if ($userStatus) {
			if ($userStatus > $this->auth) {
				die("Authorization Error");
			}
		}

		$this->context = $context;
		$this->session = $session;
		$this->type = $action[self::TYPE];

		if (isset($action[self::FILE])) {
			$this->file = $action[self::FILE];
		}
		if (isset($action[self::ACTION])) {
			$this->action = $action[self::ACTION];
		}
		if (isset($action[self::FORWARD])) {
			$this->forward = $action[self::FORWARD];
		}
		if (isset($action[self::PARAMS])) {
			$this->parameters = $action[self::PARAMS];
		}

		if (isset($session->clearError)) {
			unset($session->clearError);

			unset($session->forwardResult);
			unset($session->errorActionId);
			unset($session->errorCode);
			unset($session->formData);
		}
		if (isset($session->errorActionId)) {
			$session->clearError = true;
		}
	}

	/**
	 * Implementation of the readable properties using the PHP magic
	 * method __get().
	 *
	 * @param string $name The name of the property to get.
	 *
	 * @return mixed The value of the requested property.
	 */
	public function __get($name) {
		switch($name) {
			case "type": return $this->type;
		}
		throw new \Scrivo\SystemException("No such property-get '$name'.");
	}

	function setParameters($arr) {
		$this->parameters = $arr;
	}

	function setResult($res, $result = null, $form_data = null) {
		if ($result instanceof \Exception) {
			$result = $result->getMessage();
		}
		$this->result = $res;
		$this->session->forwardResult = $res;
		if ($res == self::FAIL) {
			$this->session->errorCode = $result!==null ? $result : null;
		}
		$this->session->formData = $form_data ? serialize($form_data) : null;
		$this->resultData = $result;

		$this->session->errorActionId =
			($result && isset($this->forward[$res]))
			? $this->forward[$res] : null;
	}

	function forward() {
		$p = array("a" => $this->forward[$this->result]);
		if ($this->parameters) {
			$p = $p + $this->parameters;
		}
		array_walk_recursive($p, function(&$a){
			if ($a instanceof \Scrivo\Str) {
				$a = (string)$a;
			}
		});
		$loc = "Location: ?".http_build_query($p, "&amp;");
		header($loc);
	}

	function getView() {
		ob_start();
		include $this->getLayout();
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	function getXhr() {
		$res = array();
		if ($this->result == self::FAIL) {
			$res["result"] = "ERROR";
		} else {
			$res["result"] = "OK";
		}
		$res["data"] = $this->resultData ? $this->resultData : "";
		$res = $this->prepareXhr($res);
		return json_encode($res);
	}

	function prepareXhr($data) {
		if (is_array($data)) {
			foreach ($data as $k=>$v) {
				$data[$k] = $this->prepareXhr($v);
			}
		} else if ($data instanceof \stdClass) {
			$data = (array)$data;
			foreach ($data as $k=>$v) {
				$data[$k] = $this->prepareXhr($v);
			}
		} else if ($data instanceof \Scrivo\Str) {
			$data = (string)$data;
		}
		return $data;
	}

}

?>