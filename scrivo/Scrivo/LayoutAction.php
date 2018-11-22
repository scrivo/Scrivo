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

	/**
	 * Stack of section options.
	 * @var array
	 */
	private $stored = array();

	/**
	 * An array containing the all the sections defined.
	 * @var array
	 */
	private $sections = array();

	/**
	 * The layout used for rendering.
	 * @var array
	 */
	private $layout = "";

	/**
	 * Define the layout to use for rendering.
	 *
	 * If no layout is provided the previously defined layout will be returned.
	 *
	 * @param string $layout
	 *	The layout to use.
	 *
	 * @return string
	 */
	function useLayout($layout = null) {
		if (is_null($layout)) {
			return $this->layout;
		}
		else {
			$this->layout = $layout;
		}
	}

	/**
	 * Gets the currently defined layout to use for rendering.
	 *
	 * @return string
	 */
	function getLayout() {
		return $this->useLayout();
	}

	/**
	 * Gets or sets options for defining a section.
	 *
	 * @param null|string $name
	 * @param null|bool $overwrite
	 * @return array
	 */
	private function sectionOptions($name = null, $overwrite = null) {
		if (is_null($name)) {
			return array_pop($this->stored);
		} else {
			array_push($this->stored, array($name, $overwrite));
		}
	}

	/**
	 * Mark the beginning of a layout section.
	 *
	 * Buffers all output until the end of the section is marked.
	 *
	 * @param string $name
	 *	The name of the section (currently only present for readability of
	 *  the code where used).
	 * @param boolean $overwrite
	 *	Overwrite previous content if existing? Default will append it to
	 *  existing.
	 */
	function beginSection($name, $overwrite = false) {
		ob_start();
		$this->sectionOptions($name, $overwrite);

	}

	/**
	 * Mark the end of a layout section.
	 *
	 * Assigns all buffered output to the section with the provided name.
	 */
	function endSection() {
		list($name, $overwrite) = $this->sectionOptions();
		$this->setSection($name, ob_get_clean(), $overwrite);
	}

	/**
	 * Assigns provided content to a specific section.
	 *
	 * If no content is provided the stored content of the section will be
	 * returned.
	 *
	 * @param string $name
	 *	The section to set the content of.
	 * @param string $content
	 *	The content to assign to the section.
	 * @param boolean $overwrite
	 *	Overwrite previous content if existing? Default will append it to
	 *  existing.
	 *
	 * @return string
	 */
	function setSection($name, $content = null, $overwrite = false) {
		if (isset($this->sections[$name]) && !$overwrite) {
			$this->sections[$name] .= $content;
		} else {
			$this->sections[$name] = $content;
		}
	}

	/**
	 * Retrieve the content of section with the provided name.
	 *
	 * @param string $name
	 *	The name of the section to get the content of.
	 *
	 * @return string
	 */
	function getSection($name) {
		return isset($this->sections[$name]) ? $this->sections[$name] : null;

	}

	/**
	 * Retrieves all sections.
	 *
	 * @return array
	 */
	function getAllSections() {
		return $this->sections;
	}

	/**
	 * Uses a function to render the content of a section.
	 *
	 * @param string $name
	 *	The name of the section to render the content of.
	 * @param callable $function
	 * 	The function which renders the content.
	 * @param array $parameters
	 * 	Optional array of parameters which will be passed to the function.
	 */
	function renderSection($name, $function, $parameters = array()) {
		$this->beginSection($name);
		call_user_func_array($function, $parameters);
		$this->endSection($name);
	}

}

?>