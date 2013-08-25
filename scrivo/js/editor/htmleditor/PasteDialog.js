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
 * $Id: PasteDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.PasteDialog = SUI.defineClass({

	baseClass: SUI.dialog.Confirm,

	initializer: function(arg) {

		SUI.editor.htmleditor.PasteDialog.initializeBase(this, arg);
		var that = this;

		this.image.src = SUI.imgDir+"/"+SUI.resource.mbAlert;

		this.width(this.WIDTH);
		this.caption(
			SUI.editor.i18n.htmleditor.pasteDialog.cptPasteSpecial);

		var t = this.CTRL_TOP;

		this.inpPlain = new SUI.form.RadioButton({
			top: t,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "pasteMethod",
			anchor: { left: true, top: true}
		});
		this.lblPlain = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.htmleditor.pasteDialog.pastePlainText,
			forBox: this.inpPlain,
			anchor: { right: true, left: true, top: true}
		});
		this.clientPanel.add(this.inpPlain);
		this.clientPanel.add(this.lblPlain);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.inpFiltered = new SUI.form.RadioButton({
			top: t,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "pasteMethod",
			anchor: { left: true, top: true}
		});
		this.lblFiltered = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.htmleditor.pasteDialog.pasteFilteredText,
			forBox: this.inpFiltered,
			anchor: { right: true, left: true, top: true}
		});

		this.clientPanel.add(this.inpFiltered);
		this.clientPanel.add(this.lblFiltered);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.inpMarkup = new SUI.form.RadioButton({
			top: t,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "pasteMethod",
			anchor: { left: true, top: true}
		});
		this.lblMarkup = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.htmleditor.pasteDialog.pasteMarkupText,
			forBox: this.inpMarkup,
			anchor: { right: true, left: true, top: true}
		});
		this.clientPanel.add(this.inpMarkup);
		this.clientPanel.add(this.lblMarkup);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.dataToForm(arg.pasteMethod);

		this.setClientHeight(t);
	},

	CTRL_TOP: 150,
	MARGIN: 8,
	LINE_HEIGHT: 20,
	LINE_SPACING: 6,
	RADIO_WIDTH: 26,
	RADIO_OFFSET: 3,
	WIDTH: 400,

	formToData: function() {
		var o = "text";
		if (this.inpMarkup.el().checked) {
			o = "html";
		} else if (this.inpFiltered.el().checked) {
			o = "filtered";
		}
		this.close();
		return o;
	},

	dataToForm: function(a) {
		if (a == "html") {
			this.inpMarkup.el().checked = true;
		} else if (a == "filtered") {
			this.inpFiltered.el().checked = true;
		} else {
			this.inpPlain.el().checked = true;
		}
	}

});
