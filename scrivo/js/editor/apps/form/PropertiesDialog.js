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
 * $Id: PropertiesDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.PropertiesDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		var that = this;

		SUI.editor.apps.form.PropertiesDialog.initializeBase(this, arg);

		this.pageId = arg.pageId;
		this.pageDefinitionTabId = arg.pageDefinitionTabId;

		this.caption(SUI.editor.i18n.apps.form.cptFormProperties);

		this.populateForm();

		this.addListener("onOK",
			function(res) {
				var res = that.formToData();
				res.a = "apps.form.updateFormProperties";
				SUI.editor.xhr.doPost(
					SUI.editor.resource.ajaxURL,
					res,
					function(res) {
						that.close();
					}
				);
			}
		);


	},

	WIDTH: 450,
	MARGIN: 8,
	SMALL_MARGIN: 2,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 120,
	CHK_WIDTH: 20,

	show: function() {
		var that = this;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "apps.form.getFormProperties",
				pageId: this.pageId,
				pageDefinitionTabId: this.pageDefinitionTabId
			},
			function(res) {
				that.dataToForm(res.data);
				SUI.editor.apps.form.FormElementDialog.parentMethod(
					that, "show");
				try {
					that.inpLabelAttribute.el().focus();
				} catch (e) {}
			}
		);
	},

	formToData: function() {
		return {
			pageId: this.pageId,
			pageDefinitionTabId: this.pageDefinitionTabId,
			emailSubject: this.inpEmailSubject.el().value,
			mailTo: this.inpMailtTo.el().value,
			captcha: this.chkCaptcha.el().checked ? 1 : 0,
			captchaText: this.txtCaptchaText.el().value
		};
	},

	dataToForm: function(a) {
		this.inpEmailSubject.el().value = a.emailSubject;
		this.inpMailtTo.el().value = a.mailTo;
		this.chkCaptcha.el().checked = parseInt(a.captcha, 10) == 1;
		this.txtCaptchaText.el().value = a.captchaText;
	},

	populateForm: function() {

		var ctlTop = this.MARGIN;

		this.inpEmailSubject = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblEmailSubject = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.apps.form.dlgEmailSubject,
			forBox: this.inpEmailSubject
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpMailtTo = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblMailtTo = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.apps.form.dlgMailTo,
			forBox: this.inpMailtTo
		});

		ctlTop += this.CTRL_HEIGHT + this.SMALL_MARGIN;

		this.lblMailToExtra = new SUI.form.Label({
			top: ctlTop,
			anchor: { left: true, right: true },
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.apps.form.dlgSeperateAddresses,
			forBox: this.inpMailtTo
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.chkCaptcha = new SUI.form.CheckBox({
			top: ctlTop,
			left: this.MARGIN,
			anchor: { left: true }
		});
		this.lblCaptcha = new SUI.form.Label({
			top: ctlTop,
			anchor: { left: true, right: true },
			left: this.MARGIN + this.CHK_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.apps.form.dlgCaptcha,
			forBox: this.chkCaptcha
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.lblCaptchaText = new SUI.form.Label({
			top: ctlTop,
			anchor: { left: true, right: true },
			left: this.MARGIN,
			width: this.MARGIN,
			height: this.CTRL_HEIGHT*2,
			title: SUI.editor.i18n.apps.form.dlgCaptchaText,
			forBox: this.txtCaptchaText
		});
		ctlTop += 2*this.CTRL_HEIGHT + this.SMALL_MARGIN;

		this.txtCaptchaText = new SUI.form.TextArea({
			top: ctlTop,
			left: this.MARGIN,
			right: this.MARGIN,
			height: 100,
			anchor: { right: true, left: true }
		});
		ctlTop += 100 + this.MARGIN;

		this.setClientHeight(ctlTop);
		this.setClientWidth(this.WIDTH);

		this.clientPanel.add(this.lblEmailSubject);
		this.clientPanel.add(this.inpEmailSubject);
		this.clientPanel.add(this.lblMailtTo);
		this.clientPanel.add(this.inpMailtTo);
		this.clientPanel.add(this.lblMailToExtra);
		this.clientPanel.add(this.lblCaptcha);
		this.clientPanel.add(this.chkCaptcha);
		this.clientPanel.add(this.lblCaptchaText);
		this.clientPanel.add(this.txtCaptchaText);
	}


});

