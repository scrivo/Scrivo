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
 * $Id: LoginDialog.js 842 2013-08-19 22:54:50Z geert $
 */

"use strict";

SUI.editor.LoginDialog = SUI.defineClass({

	baseClass: SUI.dialog.Alert,

	initializer: function(arg) {

		arg.caption = SUI.editor.i18n.loginDialog.caption;
		arg.text = SUI.editor.i18n.loginDialog.text;

		this.cbOK = arg.onOK || function() {};
		delete arg.onOK;

		SUI.editor.LoginDialog.initializeBase(this, arg);
		var that = this;

		this.addListener("onOK",
			function(res) {
				SUI.editor.xhr.doPost(
					SUI.editor.resource.getLoginKeyURL, res,
					function(res) {
						SUI.editor.xhr.doGet(
							SUI.editor.resource.ajaxURL,{
								a: "loginXhr",
								key: res.data.key
							},
							function(res) {
								that.close();
								that.cbOK();
								that = null;
							}
						);
					}
				);
			}
		);

		this.addListener("onCancel",
			function() {
				document.location = "..";
			}
		);

		var ctlTop = 40 + this.MARGIN;

		this.inpUsercode = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH_SIZE,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblUsercode = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH_SIZE,
			title: SUI.editor.i18n.loginDialog.usercode,
			forBox: this.inpUsercode
		});

		this.clientPanel.add(this.lblUsercode);
		this.clientPanel.add(this.inpUsercode);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpPassword = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH_SIZE,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblPassword = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH_SIZE,
			title: SUI.editor.i18n.loginDialog.password,
			forBox: this.inpPassword
		});

		this.inpPassword.el().type = "password";

		this.clientPanel.add(this.lblPassword);
		this.clientPanel.add(this.inpPassword);

		this.ehInput(null);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.setClientHeight(ctlTop);

		SUI.browser.addEventListener(that.inpUsercode.el(), "input", function(e) {
			if (!that.ehInput(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

		SUI.browser.addEventListener(that.inpPassword.el(), "input", function(e) {
			if (!that.ehInput(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	TEXT_WIDTH: 160,
	LABEL_WIDTH: 80,
	LABEL_WIDTH_SIZE: 120,

	ehInput: function(e) {
		this.okButton.el().disabled = (SUI.trim(this.inpUsercode.el().value) == "") ||
			(SUI.trim(this.inpPassword.el().value) == "");
	},

	show: function() {
		// This try catch was needed for IE < 8. These IE's raised a (focus)
		// error that caused the request data not to be saved.
		try {
			SUI.editor.LoginDialog.parentMethod(this, "show");
			this.inpUsercode.el().focus();
		} catch(e) {}
	},

	formToData: function() {
		return {
			usercode: this.inpUsercode.el().value,
			password: this.inpPassword.el().value
		};
	}

});
