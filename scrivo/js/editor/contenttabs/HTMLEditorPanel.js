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
 * $Id: HTMLEditorPanel.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.contenttabs.HTMLEditorPanel = SUI.defineClass({

	baseClass: SUI.Panel,

	initializer: function(arg) {

		SUI.editor.contenttabs.HTMLEditorPanel.initializeBase(
			this, arg);
		var that = this;

		var loaded = false;

		if (arg.onLoad) {
			this.cbOnLoad = arg.onLoad;
		}

		this.pageId = arg.pageId;
		this.pageDefinitionTabId = arg.pageDefinitionTabId;

		this.he = new SUI.editor.htmleditor.HTMLEditor({
			pageId: this.pageId,
			saveButton: true,
			onSave: function() {
				that.save();
			},
			onLoad: function() {
				that.cbOnLoad();
				that.setCompare();
			}
		});

		this.add(this.he);

		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "contenttabs.getPageContent",
				pageId: this.pageId,
				pageDefinitionTabId: this.pageDefinitionTabId
			},
			function(res) {
				that.he.editCtrl.resetIframe();
				that.he.editCtrl.setCSS(res.data.templateCSS,
					res.data.cssClass, res.data.stylesheets);
				that.he.editCtrl.setEditableElementIds(res.data.cssId);
				that.he.setValue(res.data.html);

				that.loaded = true;

			}
		);
	},

	compare: "",
	cbOnLoad: function() {},

	save: function() {
		var that = this;
		this.setCompare();
		SUI.editor.xhr.doPost(
			SUI.editor.resource.ajaxURL, {
				a: "contenttabs.savePageContent",
				pageId: this.pageId,
				pageDefinitionTabId: this.pageDefinitionTabId,
				content: this.he.getValue()
			},
			function(res) {
				new SUI.dialog.Alert({icon: "ok", width: 170,
					text: SUI.editor.i18n.contenttabs.dataSaved }).show();
			}
		);
	},

	/**
	* Compare value is set onLoad
	*/
	setCompare: function() {
		this.compare = this.he.getValue();
	},

	dataModified: function() {
		if (this.loaded) {
			return this.compare != this.he.getValue();
		}
		return false;
	},

	saveData: function() {
		if (this.loaded) {
			this.save();
		}
	}

});
