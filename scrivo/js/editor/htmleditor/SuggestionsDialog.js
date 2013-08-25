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
 * $Id: SuggestionsDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.SuggestionsDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.SuggestionsDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.SuggestionsDialog.initializeBase(this, arg);

		var sugg = arg.span.getAttribute("suggestions");

		this.data = sugg.split(",");

		this.caption(
			SUI.editor.i18n.htmleditor.suggestionsDialog.cptSpellCheck);

		this.populateForm();
		this.dataToForm();
	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 160,

	populateForm: function() {
		var that = this;

		this.selSuggestions = new SUI.form.SelectList({
			top: this.CTRL_HEIGHT + this.MARGIN,
			left: this.MARGIN,
			right: this.MARGIN,
			bottom: this.MARGIN,
			anchor: { top: true, left: true, right: true, bottom: true }
		});
		this.selSuggestions.el().multiple = "multiple";

		SUI.browser.addEventListener(this.selSuggestions.el(), "dblclick",
			function() {
				that.callListener("onOK", that.formToData());
			}
		);

		this.lblSuggestions = new SUI.form.Label({
			top:  this.MARGIN,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.htmleditor.suggestionsDialog.txtSuggestions,
			forBox: this.selSuggestions
		});

		this.setClientHeight(180);
		this.setClientWidth(240);

		this.clientPanel.add(this.lblSuggestions);
		this.clientPanel.add(this.selSuggestions);
	},

	dataToForm: function() {
		var opts = [];
		for (var i=0; i<this.data.length; i++) {
			opts.push({
				value: this.data[i],
				text: this.data[i]
			});
		}
		this.selSuggestions.options(opts);
	},

	formToData: function() {
		if (this.selSuggestions.el().selectedIndex != -1) {
			var res = this.selSuggestions.el().value;
			this.close();
			return res;
		}
	}

});
