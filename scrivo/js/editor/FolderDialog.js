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
 * $Id: FolderDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.FolderDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.FolderDialog.initializeBase(this, arg);
		var that = this;

		this.setClientHeight(300);
		this.setClientWidth(300);

		this.caption(SUI.editor.i18n.folderDialog.cptSelectFolder);

		this.assetId = arg.assetId ? arg.assetId : 1;

		this.treeView = new SUI.TreeView({
			dataUrl: SUI.editor.resource.ajaxURL+"?a=folderTree",
			xhr: function(url, callback) {
				SUI.editor.xhr.doGet(url, null, callback);
			}
		});

		this.treeView.iconFunction(this.iconFunc);

		this.clientPanel.add(this.treeView, 0);

		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
				a: "folderPath",
				assetId: this.assetId
			}, function(res) { that.initTree(res); }
		);

	},

	iconFunc: function(i) {
		return SUI.editor.resource.icnSubFolder;
	},

	initTree: function(res) {
		if (res.result != "OK") {
			new SUI.dialog.Alert({icon: "error", width: 500,
				caption: res.result, text: res.data}).show();
			return;
		}
		var open = res.data.path;
		open.push(0);
		open.push(1);
		this.treeView.loadData({
			openNodes: open,
			selected: this.assetId
		});
	},

	formToData: function() {
		this.close();
		return this.treeView.selectedData();
	}

});
