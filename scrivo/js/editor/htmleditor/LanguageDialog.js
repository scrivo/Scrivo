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
 * $Id: LanguageDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.LanguageDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.LanguageDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.AnchorDialog.initializeBase(this, arg);

		// add the event listeners
		this.addListener("onRemove", arg.onRemove || function(e) {});

		this.data = { lang: "" };
		if (arg.lang) {
			this.data.lang = arg.lang.toLowerCase();
		}

		this.caption(this.data.assetId
			? SUI.editor.i18n.htmleditor.languageDialog.cptEditLanguage
			: SUI.editor.i18n.htmleditor.languageDialog.cptNewLanguage);

		this.populateForm();
		this.dataToForm();

		var that = this;
		if (this.data.lang != "") {
			this.addExtraButton(SUI.editor.i18n.htmleditor.butRemove,
				function(e) {
					that.close();
					that.callListener("onRemove");
				}
			);
		}

	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 100,

	populateForm: function() {
		var ctlTop = this.MARGIN;
		var that = this;

		this.selLanguage = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblLanguage = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.languageDialog.txtLanguage,
			forBox: this.selLanguage
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.setClientHeight(ctlTop);
		this.setClientWidth(320);

		this.clientPanel.add(this.lblLanguage);
		this.clientPanel.add(this.selLanguage);

	},

	setSrc: function(assetId) {
	},

	dataToForm: function() {
		var that = this;
		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
			a: "htmleditor.languageList"
		}, function(e) {
			that.selLanguage.options(e.data.languages);
			that.selLanguage.el().value = that.data.lang;
			if (that.data.lang != ""
					&& that.selLanguage.el().value != that.data.lang) {
				that.selLanguage.el().options.push(
					new Option(that.data.lang, that.data.lang, false, true)
				);
			}
		});


	},

	formToData: function() {
		this.data.lang = this.selLanguage.el().value;
		this.close();
		return this.data.lang;
	}

});
