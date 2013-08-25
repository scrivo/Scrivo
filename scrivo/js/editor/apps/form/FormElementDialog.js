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
 * $Id: FormElementDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.FormElementDialog = SUI.defineClass(
	/** @lends SUI.editor.apps.form.FormElementDialog.prototype */ {

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	/**
	 * @classdesc
	 * <p>The SUI.editor.apps.form.FormElementDialog is the base class for form
	 * element dialog. It provided a basic dialog with the following input
	 * elements set.</p>
	 * <ul>
	 * <li>A properties tab with one input field for the form element
	 *     label.</li>
	 * <li>A tab with a large entry field for addition information about
	 *     this form element.</li>
	 * <li>An 'advanced' tab with an input field for the form element id and
	 *     one for the form element name.</li>
	 * </ul>
	 * <p>Derived dialog sould implement the more form element type specific
	 * themselves.</p>
	 *
	 * @augments SUI.dialog.OKCancelDialog
	 *
	 * @constructs
	 *
	 * @param {int} arg.pageId
	 *    The id of the page that contains this element's form.
	 * @param {int} arg.pageDefinitionTabId
	 *    The id of the tab that contains the user interface of the form
	 *    application in the editor.
	 * @param {String} arg.elementType
	 *    The elements type: 'input', 'textarea', 'email', 'file', 'select',
	 *    'radiogroup', 'checkgroup' or 'checkbox'.
	 * @param {boolen} arg.copy
	 *    Setting to indicate that the form element should be copied instead
	 *    of modified.
	 * @param {int} arg.formElementId
	 *    The id of the form element to edit or copy.
	 *
	 * @protected
	 */
	initializer: function(arg) {
		var that = this;

		SUI.editor.apps.form.FormElementDialog.initializeBase(this, arg);

		// Set dialog client area size.
		this.setClientWidth(this.WIDTH);

		// Turn off the inner border generated by OKCancelDialog.
		this.clientPanel.inner.border(new SUI.Border());

		// Create a shortcut to the i18 keys of the form aplication.
		this.i18n = SUI.editor.i18n.apps.form;

		// Store the passed parameters in the dialog object.
		this.pageId = arg.pageId;
		this.pageDefinitionTabId = arg.pageDefinitionTabId;
		this.elementType = arg.elementType;
		this.copy = arg.copy || false;
		this.formElementId = arg.formElementId || 0;

		// Create a set this dialog's caption.
		var capt = "";
		switch (this.elementType) {
			case "input": capt = this.i18n.cptInput; break;
			case "textarea": capt = this.i18n.cptTextArea; break;
			case "email": capt = this.i18n.cptEmail; break;
			case "file": capt = this.i18n.cptFile; break;
			case "select": capt = this.i18n.cptSelect; break;
			case "radiogroup": capt = this.i18n.cptRadioGroup; break;
			case "checkgroup": capt = this.i18n.cptCheckGroup; break;
			case "checkbox": capt = this.i18n.cptCheckBox; break;
		}
		if (this.copy) {
			capt = this.i18n.cptCopy + " " + capt;
		} else if (this.elementId) {
			capt = this.i18n.cptEdit + " " + capt;
		} else {
			capt = this.i18n.cptNew + " " + capt;
		}
		this.caption(capt);

		// Create three tab panels and add it to the dialog client panel.
		this.tabPanel = new SUI.TabPanel({
			tabs: [
				{ title: this.i18n.tabProperties },
				{ title: this.i18n.tabExtraInfo },
				{ title: this.i18n.tabAdvanced }
			],
			selected: 0
		});
		this.clientPanel.add(this.tabPanel);

		// Now populate the tabs with controls.
		this.populatePropertiesTab();
		this.populateAdvancedTab();
		this.extraInfoTab();

		// Start listening to the onOK event to save the form element.
		this.addListener("onOK",
			function(res) {
				// Add the action to the result and post the data.
				res.a = "apps.form.saveFormElement";
				SUI.editor.xhr.doPost(
					SUI.editor.resource.ajaxURL,
					res,
					function(res) {
						// Close the dialog and notify the interface.
						that.dataSaved();
					}
				);
			}
		);

	},

	/**
	 * The client width of the dialog.
	 * @constant
	 * @type int
	 * @private
	 */
	WIDTH: 400,

	/**
	 * The margin width that is generally used.
	 * @constant
	 * @type int
	 * @private
	 */
	MARGIN: 8,

	/**
	 * A smaller margin.
	 * @constant
	 * @type int
	 * @private
	 */
	SMALL_MARGIN: 2,

	/**
	 * The width of a check box to repostion the check box label.
	 * @constant
	 * @type int
	 * @private
	 */
	CHK_WIDTH: 20,

	/**
	 * Suggested height for form elements.
	 * @constant
	 * @type int
	 * @private
	 */
	CTRL_HEIGHT: 20,

	/**
	 * The width of the labels before the form elements.
	 * @constant
	 * @type int
	 * @private
	 */
	LABEL_WIDTH: 120,

	/**
	 * Get the data for the form element to display in the dialog and show
	 * the dialog.
	 */
	show: function() {
		var that = this;

		// Get the form element data...
		SUI.editor.xhr.doGet(
			// ... using the following action and parameters...
			SUI.editor.resource.ajaxURL, {
				a: "apps.form.getFormElement",
				formElementId: this.formElementId,
				elementType: this.elementType
			},
			// ... and used the retrieved data to show the form.
			function(res) {
				// Transfer the data tot the dialog controls ...
				that.dataToForm(res.data);
				// ... and show the form.
				SUI.editor.apps.form.FormElementDialog.parentMethod(
					that, "show");
				// Selet the fist element of the dialog.
				try {
					that.inpLabelAttribute.el().select();
				} catch (e) {}
			}
		);
	},

	/**
	 * Transfer the data stored in the dialog to an data object.
	 *
	 * @return {Object} An object cotaining the form data.
	 */
	formToData: function() {
		return {
			pageId: this.pageId,
			pageDefinitionTabId: this.pageDefinitionTabId,
			copy: this.copy,
			elementType: this.elementType,
			formElementId: this.formElementId,
			itemInfo_LABEL: this.inpLabelAttribute.el().value,
			itemInfo_ID: this.inpIdAttribute.el().value,
			itemInfo_NAME: this.inpNameAttribute.el().value,
			itemInfo_INFO: this.inpExtraInfo.el().value
		};
	},

	/**
	 * Transfer the data from data object to the form controls.
	 *
	 * @param {Object} a An object cotaining the form data.
	 */
	dataToForm: function(a) {
		this.inpLabelAttribute.el().value = a.itemInfo.LABEL || "";
		this.inpIdAttribute.el().value = a.itemInfo.ID || "";
		this.inpNameAttribute.el().value = a.itemInfo.NAME || "";
		this.inpExtraInfo.el().value = a.itemInfo.ITEMINFO || "";
	},

	/**
	 * Add the form controls to the properties tab of the dialog. This
	 * method only adds an input field for a form element label. Override
	 * and extend this method to add type specific input fields.
	 *
	 * @protected
	 */
	populatePropertiesTab: function() {

		var ctlTop = this.MARGIN;

		this.inpLabelAttribute = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblLabelAttribute = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: this.i18n.dlgLabel,
			forBox: this.inpLabelAttribute
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.tabPanel.add(this.lblLabelAttribute, 0);
		this.tabPanel.add(this.inpLabelAttribute, 0);

		return ctlTop;
	},

	/**
	 * Add the form controls to the advanced tab of the dialog. This
	 * method adds input fields for a form element label id and label.
	 * Override and extend this method to add type specific input fields.
	 *
	 * @protected
	 */
	populateAdvancedTab: function() {

		var ctlTop = this.MARGIN;

		this.inpIdAttribute = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblIdAttribute = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: this.i18n.dlgIdAttribute,
			forBox: this.inpIdAttribute
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpNameAttribute = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblNameAttribute = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: this.i18n.dlgNameAttribute,
			forBox: this.inpNameAttribute
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.tabPanel.add(this.lblIdAttribute, 2);
		this.tabPanel.add(this.inpIdAttribute, 2);
		this.tabPanel.add(this.lblNameAttribute, 2);
		this.tabPanel.add(this.inpNameAttribute, 2);

		return ctlTop;
	},

	/**
	 * Add a large input box for additional information (help text) on the
	 * information tab of the dialog.
	 *
	 * @private
	 */
	extraInfoTab: function() {

		this.inpExtraInfo = new SUI.form.TextArea({
			top: this.MARGIN,
			left: this.MARGIN,
			right: this.MARGIN,
			bottom: this.MARGIN,
			anchor: { right: true, left: true, top: true, bottom: true }
		});
		this.tabPanel.add(this.inpExtraInfo, 1);
	}

});

