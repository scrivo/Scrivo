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
 * $Id: AbbreviationDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.AbbreviationDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.AbbreviationDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	/**
	 * @class <p>SUI.editor.htmleditor.AbbreviationDialog is a simple dialog
	 * box to enter, edit or remove an abbreviation: i.e. an ABBR tag in an
	 * HTML document. For the actual editing of the HTML node you can add
	 * handlers to the "onOK" (creation/modifcation) and "onRemove" (removal)
	 * event of the dailog window.</p>
	 * <p>The dialog can be openend displaying the currently defined full text
	 * of the abbreviation. In that case an extra button to remove the
	 * abbreviation will be added to the dialog buttons.</p>
	 *
	 * @example
	 *   var that = this;
	 *   var spn = this.editCtrl.doGetAbbr();
	 *   new SUI.editor.htmleditor.AbbreviationDialog({
	 *       abbr: spn ? spn.title : "",
	 *       onOK: function(abbr) {
	 *           if (!spn) {
	 *               that.editCtrl.doInsertAbbr(abbr);
	 *           } else {
	 *               that.editCtrl.doUpdateAbbr(spn, abbr);
	 *           }
	 *       },
	 *       onCancel: function() {
	 *           that.focus();
	 *       },
	 *       onRemove: function() {
	 *           if (spn) {
	 *               that.editCtrl.doRemoveNode(spn);
	 *           }
	 *       }
	 *   }).show();
	 *
	 * @augments SUI.dialog.OKCancelDialog
	 *
	 * @description Create a dialog to enter, edit or remove the full
	 * text of an abbreviation.
	 *
	 * @constructs
	 *
	 * @param {object} arg A parameter object in which the following members
	 *     can be set:
	 * @param {string} [arg.abbr] The full text of the abbreviation.
	 * @param {int} [arg.onRemove] An event listener function that is
	 *     called whe the user clicks on the remove button of the dialog.
	 *     See {@link #event:onRemove}
	 */
	initializer: function(arg) {

		SUI.editor.htmleditor.AbbreviationDialog.initializeBase(this, arg);

		// add the event listeners
		this.addListener("onRemove", arg.onRemove || function(e) {});

		// set the initial data of the dialog
		this.data = { abbr: arg.abbr || "" };

		// set the dialog caption
		this.caption(this.data.assetId
			? SUI.editor.i18n.htmleditor.abbreviationDialog.cptEditAbbreviation
			: SUI.editor.i18n.htmleditor.abbreviationDialog.cptNewAbbreviation);

		// create the form controls and fill them with the initial data
		this.populateForm();
		this.dataToForm();

		// if an initial value was given create an extra button to remove
		// abbreviation
		var that = this;
		if (this.data.abbr != "") {
			this.addExtraButton(SUI.editor.i18n.htmleditor.butRemove,
				function(e) {
					that.close();
					that.callListener("onRemove");
				}
			);
		}
	},

	/**
	 * The height of a control (line).
	 * @type int
	 * @constant
	 */
	CTRL_HEIGHT: 20,

	/**
	 * The (inner) width of the dialog.
	 * @constant
	 */
	DIALOG_WIDTH: 320,

	/**
	 * the width of the label(s).
	 * @constant
	 */
	LABEL_WIDTH: 90,

	/**
	 * The margin between the lines.
	 * @constant
	 */
	MARGIN: 8,

	/**
	 * Fill the dialog control(s) with data.
	 * @private
	 */
	dataToForm: function() {
		this.inpAbbreviation.el().value = this.data.abbr;
	},

	/**
	 * Close the dialog and return the dialog's data.
	 * @private
	 */
	formToData: function() {
	 this.close();
		return this.inpAbbreviation.el().value;
	},

	/**
	 * onRemove event handler. This event handler is called when the
	 * user clicks on the 'remove' button.
	 * @event
	 */
	onRemove: function() {
	},

	/**
	 * Create the dialog controls.
	 * @private
	 */
	populateForm: function() {

		// only one label and input field on this control.
		this.inpAbbreviation = new SUI.form.Input({
			top: this.MARGIN,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblAbbreviation = new SUI.form.Label({
			top: this.MARGIN,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title:
				SUI.editor.i18n.htmleditor.abbreviationDialog.txtAbbreviation,
			forBox: this.inpAbbreviation
		});

		// add them to the client panel
		this.clientPanel.add(this.lblAbbreviation);
		this.clientPanel.add(this.inpAbbreviation);

		// And set the dialog size
		this.setClientHeight(this.CTRL_HEIGHT + 2 * this.MARGIN);
		this.setClientWidth(this.DIALOG_WIDTH);
	}

});
