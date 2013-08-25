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
 * $Id: TableSplitMergeDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.TableSplitMergeDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.TableSplitMergeDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.TableSplitMergeDialog.initializeBase(this, arg);

		this.data = arg.args;

		this.caption(
		 SUI.editor.i18n.htmleditor.tableSplitMergeDialog.cptInsDelSplitMerge);

		this.populateForm();
	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	BUTTON_SIZE: 26,
	DIALOG_WIDTH: 400,
	TXT_OFF: 4,

	addButton: function(top, but, txt, icn, text, fn) {

		but = new SUI.ToolbarButton({
			left: this.MARGIN,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			top: top,
			title: "",
			icon: icn,
			handler: fn
		});
		this.clientPanel.add(but);

		txt = new SUI.Box({
			top: top + this.TXT_OFF,
			height: this.CTRL_HEIGHT,
			left: this.BUTTON_SIZE + this.MARGIN*2,
			right: this.MARGIN,
			anchor: {left: true, right: true }
		});
		txt.el().style.whiteSpace = "nowrap";
		txt.el().innerHTML = text;

		this.clientPanel.add(txt);
	},

	populateForm: function() {
		var ctlTop = this.MARGIN - this.TXT_OFF;
		var that = this;
		var i18n = SUI.editor.i18n.htmleditor.tableSplitMergeDialog;

		this.txtSplit = new SUI.Box({
			top: ctlTop + this.TXT_OFF,
			height: this.CTRL_HEIGHT,
			left: this.MARGIN,
			right: this.MARGIN,
			anchor: {left: true, right: true }
		});
		this.txtSplit.el().style.whiteSpace = "nowrap";
		this.txtSplit.el().innerHTML = "<b>"
			+ i18n.txtSplit
			+ "</b>";

		this.clientPanel.add(this.txtSplit);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.addButton(ctlTop, this.buttIns, this.txtIns,
			SUI.editor.resource.htmleditor.icnSplitHorizontal,
			i18n.txtSplitHorizontal,
			function() { that.onButton("splitcellhoriz"); }
		);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.addButton(ctlTop, this.buttIns, this.txtIns,
			SUI.editor.resource.htmleditor.icnSplitVertical,
			i18n.txtSplitVertical,
			function() { that.onButton("splitcellvert"); }
		);

		if (this.data.left || this.data.right || this.data.up
				|| this.data.down) {

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;

			this.txtCol = new SUI.Box({
				top: ctlTop + this.TXT_OFF,
				height: this.CTRL_HEIGHT,
				left: this.MARGIN,
				right: this.MARGIN,
				anchor: {left: true, right: true }
			});
			this.txtCol.el().style.whiteSpace = "nowrap";
			this.txtCol.el().innerHTML = "<b>"
				+ i18n.txtMerge
				+ "</b>";

			this.clientPanel.add(this.txtCol);

			if (this.data.right) {

				ctlTop += this.CTRL_HEIGHT + this.MARGIN;

				this.addButton(ctlTop, this.buttInsRight, this.txtInsRight,
					SUI.editor.resource.htmleditor.icnMergeRight,
					i18n.txtMergeRight,
					function() { that.onButton("mergecellright"); }
				);

			}

			if (this.data.left) {

				ctlTop += this.CTRL_HEIGHT + this.MARGIN;

				this.addButton(ctlTop, this.buttInsLeft, this.txtInsLeft,
					SUI.editor.resource.htmleditor.icnMergeLeft,
					i18n.txtMergeLeft,
					function() { that.onButton("mergecellleft"); }
				);

			}

			if (this.data.down) {

				ctlTop += this.CTRL_HEIGHT + this.MARGIN;

				this.addButton(ctlTop, this.buttInsBelow, this.txtInsBelow,
					SUI.editor.resource.htmleditor.icnMergeBelow,
					i18n.txtMergeBelow,
					function() { that.onButton("mergecelldown"); }
				);

			}

			if (this.data.up) {

				ctlTop += this.CTRL_HEIGHT + this.MARGIN;

				this.addButton(ctlTop, this.buttInsAbove, this.txtInsAbove,
					SUI.editor.resource.htmleditor.icnMergeAbove,
					i18n.txtMergeAbove,
					function() { that.onButton("mergecellup"); }
				);

			}

		}

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.setClientHeight(ctlTop + this.TXT_OFF);
		this.setClientWidth(this.DIALOG_WIDTH);

		this.okButton.el().style.display = "none";

	},

	onButton: function(res) {
		this.close();
		this.callListener("onOK", res);
	}

});
