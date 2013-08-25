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
 * $Id: AccessDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.AccessDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.AccessDialog.initializeBase(this, arg);
		var that = this;

		if (arg.assetId) {
			this.itemId = arg.assetId;
			this.caption(SUI.editor.i18n.accessDialog.captionFolder);
		} else if (arg.pageId) {
			this.itemId = arg.pageId;
			this.caption(SUI.editor.i18n.accessDialog.captionPage);
		} else {
			new SUI.dialog.Alert({icon: "error", width: 500,
				text: SUI.editor.i18n.accessDialog.errTextNoId}).show();
			return;
		}

		this.width(this.WIDTH);

		this.cbOK = arg.onOK ? arg.onOK : function() {};
		this.addListener("onOK",
			function(res) {
			var count = 0;
				for (var i in that.data.editorRoles) {
					if (that.data.editorRoles[i].inp.el().checked) {
					count++;
				}
			}
			if (!count) {
				new SUI.dialog.Alert({
					text: SUI.editor.i18n.accessDialog.accessOneEditorRole
				}).show();
			} else {
					res.a = "setAccess";
					SUI.editor.xhr.doPost(this.XHR_URL, res, this.save);
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

		this.show = function() {
			SUI.editor.xhr.doGet(
				this.XHR_URL, {a: "getAccess", itemId: this.itemId },
				function(res) {
					that.dataToForm(res.data);
					that.center();
					SUI.editor.AccessDialog.parentMethod(that, "show");
					for (var i in that.data.publicRoles) {
						that.data.publicRoles[i].inp.el().focus();
						break;
					}
				}
			);
		};
	},

	MARGIN: 8,
	HEADER_HEIGHT: 20,
	LINE_HEIGHT: 18,
	LINE_SPACING: 0,
	RADIO_WIDTH: 26,
	RADIO_OFFSET: 3,
	WIDTH: 340,
	XHR_URL: SUI.editor.resource.ajaxURL,

	itemId: 0,
	data: [],

	formToData: function() {

		var r = [];

		for (var i in this.data.publicRoles) {
			if (this.data.publicRoles[i].inp.el().checked) {
				r.push(i);
			}
		}
		for (var i in this.data.editorRoles) {
			if (this.data.editorRoles[i].inp.el().checked) {
				r.push(i);
			}
		}

		return {itemId: this.itemId, roles: r};
	},

	buildSection: function(d, title, t) {

		var txt = new SUI.Box({
			top: t,
			height: this.LINE_HEIGHT + this.MARGIN,
			left: this.MARGIN,
			right: this.MARGIN,
			anchor: { right: true, left: true, top: true}
		});

		txt.el().innerHTML = title;
		txt.addClass("sui-form-header");
		this.clientPanel.add(txt);

		t+= this.HEADER_HEIGHT + this.LINE_SPACING;

		for (var i in d) {

			var inp = new SUI.form.CheckBox({
				top: t + this.RADIO_OFFSET,
				left: this.MARGIN + this.RADIO_OFFSET,
				name: "cache",
				checked: d[i].checked,
				anchor: { left: true, top: true}
			});
			var lbl = new SUI.form.Label({
				top: t,
				left: this.MARGIN  + this.RADIO_WIDTH,
				right: this.MARGIN,
				title: d[i].label,
				forBox: inp,
				anchor: { right: true, left: true, top: true}
			});
			this.clientPanel.add(inp);
			this.clientPanel.add(lbl);

			d[i].inp = inp;

			t += this.LINE_HEIGHT + this.LINE_SPACING;
		}

		t += this.MARGIN;

		return t;
	},

	dataToForm: function(a) {

		var t = this.MARGIN;

		this.data = a;

		t = this.buildSection(
				a.publicRoles, SUI.editor.i18n.accessDialog.accessPublic, t);
		t = this.buildSection(
				a.editorRoles, SUI.editor.i18n.accessDialog.accessEditor, t);

		this.setClientHeight(t);
	}

});
