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
 * $Id: PropertyPanel.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * Simple panel implementation: just a toolbar with a save button and
 * and a set of properties that is encapsulated by the PropertyBox
 * component.
 * Note PropertyBox requires time for the property values to load. All
 * save actions should be disabled in the meantime.
 */
SUI.editor.contenttabs.PropertyPanel = SUI.defineClass({

	baseClass: SUI.Panel,

	initializer: function(arg) {

		SUI.editor.contenttabs.PropertyPanel.initializeBase(this, arg);
		var that = this;

		if (arg.onLoad) {
			this.cbOnLoad = arg.onLoad;
		}

		this.saveButton = new SUI.ToolbarButton({
			title: SUI.editor.i18n.contenttabs.save,
			icon: SUI.editor.resource.contenttabs.icnSave,
			handler: function() {
				that.pb.saveData();
			}
		});
		this.saveButton.enable(false);

		this.toolbar = new SUI.Toolbar({
			tools: [this.saveButton]
		});

		this.add(this.toolbar);

		this.scrollbox = new SUI.editor.VerticalScrollBox({
			top: this.toolbar.height()
		});

		this.pb = new SUI.editor.contenttabs.PropertyBox({
			right: 0,
			pageId: arg.pageId,
			pageDefinitionTabId: arg.pageDefinitionTabId,
			onLoad: function() {
				that.cbOnLoad();
				that.saveButton.enable(true);
			},
			onDataSaved: function(res) {
				new SUI.dialog.Alert({icon: "ok", width: 170,
					text: SUI.editor.i18n.contenttabs.dataSaved }).show();
			}
		});
		this.add(this.scrollbox);
		this.scrollbox.add(this.pb);
	},

	cbOnLoad: function() {},

	dataModified: function() {
		if (this.saveButton.enabled) {
			return this.pb.dataModified();
		}
		return false;
	},

	saveData: function() {
		if (this.saveButton.enabled) {
			this.pb.saveData();
		}
	}

});
