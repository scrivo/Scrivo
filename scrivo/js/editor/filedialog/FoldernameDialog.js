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
 * $Id: FoldernameDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.FoldernameDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.filedialog.FoldernameDialog.initializeBase(
			this, arg);
		var that = this;

		var capt = SUI.editor.i18n.filedialog.foldernameDialog.captionNewFolder;
		if (arg.dirId) {
			this.dirId = arg.dirId;
		}
		if (arg.assetId) {
			this.assetId = arg.assetId;
			var capt = SUI.editor.i18n.filedialog.foldernameDialog.captionFolder;
		}

		this.width(this.WIDTH);

		this.caption(capt);

		var t = this.MARGIN;

		this.inpFile = new SUI.form.Input({
			top: t,
			right: this.MARGIN,
			left: this.LABEL_WIDTH+this.MARGIN,
			anchor: { right: true, left: true, top: true}
		});
		this.lblFile = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.filedialog.foldernameDialog.folderName,
			forBox: this.inpFile
		});
		this.clientPanel.add(this.lblFile);
		this.clientPanel.add(this.inpFile);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.setClientHeight(t);

		this.cbOK = arg.onOK ? arg.onOK : function() {};
		this.addListener("onOK",
			function(res) {
				res.a = "filedialog.folderUpdate";
				SUI.editor.xhr.doPost(SUI.editor.resource.ajaxURL, res, this.save);
			}
		);

		this.save = function(res) {
			if (res.result != "OK") {
				new SUI.dialog.Alert({icon: "error", width: 500,
					caption: res.result, text: res.data}).show();
				return;
			}
			that.close();
			that.cbOK();
		};

		this.show = function() {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: "filedialog.folderProperties",
					assetId: this.assetId
				},
				function(res) {
					that.dataToForm(res.data);
					that.center();
					SUI.editor.filedialog.FoldernameDialog.parentMethod(
						that, "show");
					that.inpFile.el().select();
				}
			);
		};
	},

	MARGIN: 8,
	LINE_HEIGHT: 20,
	LINE_SPACING: 6,
	LABEL_WIDTH: 100,
	WIDTH: 400,

	assetId: 0,
	dirId: 0,

	formToData: function() {
		return {
			assetId: this.assetId,
			dirId: this.dirId,
			title: this.inpFile.el().value
		};
	},

	dataToForm: function(a) {
		this.inpFile.el().value = a.title;
	}

});

