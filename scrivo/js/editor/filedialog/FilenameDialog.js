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
 * $Id: FilenameDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.FilenameDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.filedialog.FilenameDialog.initializeBase(
			this, arg);
		var that = this;

		if (arg.assetId) {
			this.assetId = arg.assetId;
		}

		this.width(this.WIDTH);

		this.caption(
			SUI.editor.i18n.filedialog.filenameDialog.captionFile);

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
			title: SUI.editor.i18n.filedialog.filenameDialog.fileName,
			forBox: this.inpFile
		});
		this.clientPanel.add(this.lblFile);
		this.clientPanel.add(this.inpFile);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.ctlOnline = new SUI.control.Date({
			top: t,
			left: this.LABEL_WIDTH+this.MARGIN,
			type: "datetime",
			anchor: { left: true, top: true}
		});
		this.lblOnline = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.filedialog.filenameDialog.onlineOn,
			forBox: this.ctlOnline.firstBox()
		});
		this.clientPanel.add(this.ctlOnline);
		this.clientPanel.add(this.lblOnline);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.ctlOffline = new SUI.control.Date({
			top: t,
			left: this.LABEL_WIDTH+this.MARGIN,
			type: "datetime",
			anchor: { left: true, top: true}
		});
		this.lblOffline = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.filedialog.filenameDialog.offlineOn,
			forBox: this.ctlOffline.firstBox()
		});
		this.clientPanel.add(this.ctlOffline);
		this.clientPanel.add(this.lblOffline);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.setClientHeight(t);

		this.cbOK = arg.onOK ? arg.onOK : function() {};
		this.addListener("onOK",
			function(res) {
				if (this.ctlOnline.value().type == "error") {
					new SUI.dialog.Alert({
						text:
						SUI.editor.i18n.filedialog.filenameDialog.invalidOnlineDate
					}).show();
				} else if (this.ctlOffline.value().type == "error") {
					new SUI.dialog.Alert({
						text:
						SUI.editor.i18n.filedialog.filenameDialog.invalidOfflineDate
					}).show();
				} else {
					res.a = "filedialog.assetUpdate";
					SUI.editor.xhr.doPost(SUI.editor.resource.ajaxURL, res, this.save);
				}
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

		this.show = function(assetId) {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: "filedialog.assetProperties",
					assetId: this.assetId
				},
				function(res) {
					that.dataToForm(res.data);
					that.center();
					SUI.editor.filedialog.FilenameDialog.parentMethod(
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

	formToData: function() {
		return {
			assetId: this.assetId,
			title: this.inpFile.el().value,
			onlineOn: this.ctlOnline.value().strDate,
			offlineOn: this.ctlOffline.value().strDate
		};
	},

	dataToForm: function(a) {
		this.assetId = a.assetId;
		this.inpFile.el().value = a.title;
		this.ctlOnline.value(a.onlineOn);
		this.ctlOffline.value(a.offlineOn);
	}

});

