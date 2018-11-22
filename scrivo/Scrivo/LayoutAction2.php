<?php
/* Copyright (c)
 * - 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: LayoutAction.php 841 2013-08-19 22:19:47Z geert $
 */

/**
 * Implementation of the \Scrivo\Layout class.
 */

namespace Scrivo;

class LayoutSection {
	
	private $data;
	
	public function __toString() {
		return $this->data;
	}
	
	public function start() {
		\ob_start();
	}
	
	public function end() {
		$this->data = \ob_get_clean();
	}
	
}

class LayoutSections{
		
	private $section = array();

	public function __get($name) {
		if (!isset($this->section[$name])) {
			$this->section[$name] = new LayoutSection();
		}
		return $this->section[$name];
	}

	//public function __set($name, $value) {
	//	$this->section[$name] = $value;
	//}
	
	

} 

/**
 * The layout class provides a template system.
 *
 * By unsing this layout class you can break down a larger template
 * into smaller and manageble sections. These sections are named an can
 * be used in other sections.
 *
 * Consider the following data:
 *
 * <pre class="sh_php">
 * $data = new \stdClass;
 * $data->content = "Scrivo hello";
 * $data->menu = array("News", "Events", "Contact");
 * </pre>
 *
 * To display this data we first create a master template "master.tpl.php":
 *
 * <pre class="sh_php">
 * &lt;html lang="en_EN"&gt;
 *   &lt;body&gt;
 *     &lt;div class="menu"&gt;
 *       &lt;?php echo $this->getSection("menu"); ?&gt;
 *     &lt;/div&gt;
 *     &lt;div class="content"&gt;
 *       &lt;?php echo $this->getSection("content"); ?&gt;
 *     &lt;/div&gt;
 *   &lt;/body&gt;
 * &lt;/html&gt;
 * </pre>
 *
 * And another template file "sections.tpl.php" in which we define the sections:
 *
 * <pre class="sh_php">
 * &lt;?php
 * $this->beginSection("menu", true);
 * ?&gt;
 *     &lt;ul&gt;
 *     &lt;?php foreach ($data->menu as $m) { ?&gt;
 *         &lt;li&gt;&lt;?php echo $m?&gt;&lt;/li&gt;
 *     &lt;?php } ?&gt;
 *     &lt;/ul&gt;
 * ?&gt;
 * $this->endSection();
 *
 * $this->beginSection("content");
 * ?&gt;
 *     &lt;p&gt;&lt;?php echo $data->content?&gt;&lt;/p&gt;
 * &lt;?php
 * $this->endSection("content", true);
 * ?&gt;
 * </pre>
 *
 * Now we can apply the templates on the data using the following:
 *
 * <pre class="sh_php">
 * class MyLayout extends Layout {
 *     function apply($data) {
 *         include "sections.tpl.php";
 *         $this->useLayout("master.tpl.php");
 *         include $this->getLayout();
 *     }
 * }
 * $l = new MyLayout();
 * $l->apply($data);
 * </pre>
 */
class LayoutAction extends Action {

	private $layoutManager = null;

	public function __construct($context, $action, $userStatus, $session=null) {
		parent::__construct($context, $action, $userStatus, $session);
		$this->layoutManager = new LayoutSections();
	}

	public function __get($name) {
		if ("sections" === $name) {
			return $this->layoutManager;
		}
		return parent::__get($name);
	}
	
	function getView() {
		ob_start();
		include $this->resultData;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}

?>