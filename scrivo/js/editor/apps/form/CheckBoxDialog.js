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
 * $Id: CheckBoxDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.CheckBoxDialog = SUI.defineClass({

	baseClass: SUI.editor.apps.form.FormElementDialog,

	initializer: function(arg) {

		SUI.editor.apps.form.CheckBoxDialog.initializeBase(this, arg);

	},

	formToData: function() {
		var res = SUI.editor.apps.form.CheckBoxDialog.parentMethod(
				this, "formToData");

		res.itemInfo_CHECKED = this.chkCheckedByDefault.el().checked;

		return res;
	},

	dataToForm: function(a) {
		SUI.editor.apps.form.InputElementDialog.parentMethod(
				this, "dataToForm", a);

		this.chkCheckedByDefault.el().checked = a.typeData.checked;
	},

	populatePropertiesTab: function() {

		var that = this;

		var ctlTop = SUI.editor.apps.form.CheckBoxDialog.parentMethod(this,
			"populatePropertiesTab");

		this.chkCheckedByDefault = new SUI.form.CheckBox({
			top: ctlTop,
			left: this.MARGIN,
			anchor: { left: true }
		});
		this.lblCheckedByDefault = new SUI.form.Label({
			top: ctlTop,
			anchor: { left: true, right: true },
			left: this.MARGIN + this.CHK_WIDTH,
			width: this.MARGIN,
			title: this.i18n.dlgDefaultChecked,
			forBox: this.chkCheckedByDefault
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.tabPanel.add(this.lblCheckedByDefault, 0);
		this.tabPanel.add(this.chkCheckedByDefault, 0);

		var ts = this.tabPanel.clientAreaPosition();
		this.setClientHeight(ts.top + ts.bottom + ctlTop);

	}

});

