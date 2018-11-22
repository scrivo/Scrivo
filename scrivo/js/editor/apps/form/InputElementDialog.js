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
 * $Id: InputElementDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.InputElementDialog = SUI.defineClass({

	baseClass: SUI.editor.apps.form.FormElementDialog,

	initializer: function(arg) {

		this.type = arg.type || "input";

		SUI.editor.apps.form.InputElementDialog.initializeBase(this, arg);

	},

	populatePropertiesTab: function() {

		var ctlTop = SUI.editor.apps.form.InputElementDialog.parentMethod(this,
			"populatePropertiesTab");

		if (this.type == "textarea") {

			this.inpRows = new SUI.form.Input({
				top: ctlTop,
				left: this.MARGIN + this.LABEL_WIDTH,
				width: 40,
				maxLength: 4,
				anchor: { left: true }
			});
			this.lblRows = new SUI.form.Label({
				top: ctlTop,
				left: this.MARGIN,
				width: this.LABEL_WIDTH - this.MARGIN,
				title: this.i18n.dlgRows,
				forBox: this.inpRows
			});

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		}

		this.lblWidth = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: this.i18n.dlgWidth
		});
		this.rdoFullWidth = new SUI.form.RadioButton({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			anchor: { left: true }
		});
		this.rdoFullWidth.el().name = "rdoWidth";
		this.lblFullWidth = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH + this.CHK_WIDTH,
			right: this.MARGIN,
			title: this.i18n.dlgFullWidth,
			forBox: this.rdoFullWidth,
			anchor: { left: true, right: true }
		});

		ctlTop += this.CTRL_HEIGHT + this.SMALL_MARGIN;

		this.rdoFixedWidth = new SUI.form.RadioButton({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			anchor: { left: true }
		});
		this.rdoFixedWidth.el().name = "rdoWidth";
		this.lblFixedWidth = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH + this.CHK_WIDTH,
			right: this.MARGIN,
			title: this.i18n.dlgFixedWidth,
			forBox: this.rdoFixedWidth,
			anchor: { left: true, right: true }
		});

		this.inpChars = new SUI.form.Input({
			maxLength: 4
		});
		this.inpChars.el().style.position = "relative";
		this.inpChars.el().size = 2;
		this.lblFixedWidth.el().appendChild(this.inpChars.el());

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		if (this.type != "file") {

			this.inpDefaultValue = new SUI.form.Input({
				top: ctlTop,
				left: this.MARGIN + this.LABEL_WIDTH,
				right: this.MARGIN,
				anchor: { right: true, left: true }
			});
			this.lblDefaultValue = new SUI.form.Label({
				top: ctlTop,
				left: this.MARGIN,
				width: this.LABEL_WIDTH - this.MARGIN,
				title: this.i18n.dlgDefaultValue,
				forBox: this.inpDefaultValue
			});

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		}

		if (this.type == "email") {

			this.chkReplyTo = new SUI.form.CheckBox({
				top: ctlTop,
				left: this.MARGIN,
				anchor: { left: true }
			});
			this.lblReplyTo = new SUI.form.Label({
				top: ctlTop,
				anchor: { left: true, right: true },
				left: this.MARGIN + this.CHK_WIDTH,
				width: this.MARGIN,
				title: this.i18n.dlgEmail,
				forBox: this.chkReplyTo
			});

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		}

		this.chkRequired = new SUI.form.CheckBox({
			top: ctlTop,
			left: this.MARGIN,
			anchor: { left: true }
		});
		this.lblRequired = new SUI.form.Label({
			top: ctlTop,
			anchor: { left: true, right: true },
			left: this.MARGIN + this.CHK_WIDTH,
			width: this.MARGIN,
			title: this.i18n.dlgRequired,
			forBox: this.chkRequired
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		if (this.type == "textarea") {
			this.tabPanel.add(this.lblRows, 0);
			this.tabPanel.add(this.inpRows, 0);
		}
		this.tabPanel.add(this.lblWidth, 0);
		this.tabPanel.add(this.rdoFullWidth, 0);
		this.tabPanel.add(this.lblFullWidth, 0);
		this.tabPanel.add(this.rdoFixedWidth, 0);
		this.tabPanel.add(this.lblFixedWidth, 0);
		if (this.type != "file") {
			this.tabPanel.add(this.lblDefaultValue, 0);
			this.tabPanel.add(this.inpDefaultValue, 0);
		}
		if (this.type == "email") {
			this.tabPanel.add(this.lblReplyTo, 0);
			this.tabPanel.add(this.chkReplyTo, 0);
		}
		this.tabPanel.add(this.lblRequired, 0);
		this.tabPanel.add(this.chkRequired, 0);

		var ts = this.tabPanel.clientAreaPosition();
		this.setClientHeight(ts.top + ts.bottom + ctlTop);

	},

	populateAdvancedTab: function() {

		var ctlTop = SUI.editor.apps.form.InputElementDialog.parentMethod(this,
			"populateAdvancedTab");

		if (this.type == "input" || this.type == "email") {

			this.inpMaxLength = new SUI.form.Input({
				top: ctlTop,
				left: this.MARGIN + this.LABEL_WIDTH,
				width: 40,
				maxLength: 4,
				anchor: { left: true }
			});
			this.lblMaxLength = new SUI.form.Label({
				top: ctlTop,
				left: this.MARGIN,
				width: this.LABEL_WIDTH - this.MARGIN,
				title: this.i18n.dlgMaxLength,
				forBox: this.inpMaxLength
			});

			this.tabPanel.add(this.lblMaxLength, 2);
			this.tabPanel.add(this.inpMaxLength, 2);

		}

	},

	formToData: function() {

		var res = SUI.editor.apps.form.InputElementDialog.parentMethod(
				this, "formToData");

		if (this.type == "textarea") {
			res.itemInfo_ROWS = this.inpRows.el().value;
		}

		res.itemInfo_WIDTH = this.rdoFullWidth.el().checked ?
			"" : this.inpChars.el().value;

		if (this.type != "file") {
			res.itemInfo_DEFAULT_VALUE = this.inpDefaultValue.el().value;
		}

		if (this.type == "email") {
			res.itemInfo_REPLYTO = this.chkReplyTo.el().checked;
		}

		res.itemInfo_REQUIRED = this.chkRequired.el().checked;

		if (this.type == "input" || this.type == "email") {
			res.itemInfo_MAXLENGTH = this.inpMaxLength.el().value;
		}

		return res;
	},

	dataToForm: function(a) {

		SUI.editor.apps.form.InputElementDialog.parentMethod(
			this, "dataToForm", a);

		if (this.type == "textarea") {
			this.inpRows.el().value = a.typeData.rows || "";
		}

		var w = (a.typeData.width || "");
		this.rdoFullWidth.el().checked = w == "";
		this.rdoFixedWidth.el().checked = w != "";
		this.inpChars.el().value = w;

		if (this.type != "file") {
			this.inpDefaultValue.el().value = a.typeData.defaultValue || "";
		}
		if (this.type == "email") {
			this.chkReplyTo.el().checked = a.typeData.replyTo;
		}
		this.chkRequired.el().checked = a.typeData.required;

		if (this.type == "input" || this.type == "email") {
			this.inpMaxLength.el().value = a.typeData.maxLength || "";
		}
	}

});

