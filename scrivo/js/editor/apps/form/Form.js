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
 * $Id: Form.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.Form = SUI.defineClass(
	/** @lends SUI.editor.apps.form.Form.prototype */{

	/** @ignore */ baseClass: SUI.AnchorLayout,

	/**
	 * @classdesc
	 * SUI.editor.apps.form.Form is the main class of the form application.
	 * The class creates a layout that hosts the application toolbar and
	 * form elements list. Besides setting up the main interface the form
	 * application also host most action handlers for most actions that
	 * can be triggered from the user interface.
	 *
	 * @augments SUI.AnchorLayout
	 *
	 * @constructs
	 *
	 * @param {int} arg.pageId The id of the page that contains the form.
	 * @param {int} arg.pageDefinitionTabId The id of the tab that contains the
	 *    user interface of the form application in the editor.
	 */
	initializer: function(arg) {
		var that = this;

		// Call base class constructor.
		SUI.editor.apps.form.Form.initializeBase(this, arg);

		// Get the standard application parameters.
		this.pageId = arg.pageId;
		this.pageDefinitionTabId = arg.pageDefinitionTabId;

		// An application anchors to all four sides.
		this.anchor = { left: true, right: true, top: true, bottom: true };

		// Construct the toolbar
		this.toolBar = this.createToolBar(this.createActionList());

		// Split the work area in two parts: north for the tool bar and the
		// center will be the work area.
		this.splitSetWork = new SUI.SplitLayout({
			north: {
				height: this.toolBar.height()
			}
		});

		// Add the toolbar to the split layout.
		this.splitSetWork.add(this.toolBar, "north");

		// Add the split layout to the application box.
		this.add(this.splitSetWork);

		// Load the form element list into the main work area.
		this.loadFormElements();

	},

	/**
	 * (Re)load and draw the form element list.
	 *
	 * @param {int} [arg.scrollTop=0] An optional offset to scroll the top of
	 *   the list to.
	 * @private
	 */
	loadFormElements: function(arg) {
		var that = this;

		// arg defaults to an object.
		var arg = arg || {};
		// Get the passed scrollTop (if any).
		var scrollTop = arg.scrollTop || 0;

		// Get the form element list...
		SUI.editor.xhr.doGet(
			// ... using the following action and parameters...
			SUI.editor.resource.ajaxURL, {
				a: "apps.form.getFormElements",
				pageId: this.pageId,
				pageDefinitionTabId: this.pageDefinitionTabId
			},
			// ... and execute the following upon retrieval.
			function(res) {
				// Create a new scroll box ...
				var sb = new SUI.editor.VerticalScrollBox({
					minHeight: 100,
					minWidth: 100
				});
				// ... and keep a reference.

				// Create a new form elements list ...
				that.scrollBox = sb;
				var bl = new SUI.editor.apps.form.FormElements({
					data : res.data,
					frame: that
				});
				// ... and add it to the scroll box.
				sb.add(bl);

				// Add the scroll box to the center of the work area.
				that.splitSetWork.add(sb, "center");

				// Scroll to the given offset (closure var).
				if (scrollTop) {
					that.scrollBox.setScrollTop(scrollTop);
				}

				// Redraw the application area.
				that.splitSetWork.draw();
			}
		);

	},

	/**
	 * Action handler to open form element dialogs. The dialogs can be open
	 * in different modes: new, update and copy.
	 *
	 * @param {String} type A string to identify the typ of dialog that needs
	 *    to be openend.
	 * @param {int} [elementId=0] The id of a form element in case of updating
	 *    and copying.
	 * @param {boolean} [copy=false] A boolean value to indicate that we want
	 *    to copy the item.
	 * @private
	 */
	actFormElement: function(type, elementId, copy) {
		var that = this;

		// Get the scroll top to restore the scroll offset after saving.
		var st = this.scrollBox.getScrollTop();

		// Construct the argument object that we'll use when calling the
		// proper dialog.
		var args = {
			elementType: type,
			pageId: that.pageId,
			pageDefinitionTabId: that.pageDefinitionTabId,
			formElementId: elementId || 0,
			copy: copy ? true : false,
			onDataSaved: function() {
				// If the data was saved reload the list and set the scrollTop.
				that.loadFormElements({scrollTop: st});
			}
		}

		// Open the correct form element dailog.
		if (type == "input" || type == "textarea" || type == "email" ||
				type == "file") {
			new SUI.editor.apps.form.InputElementDialog(args).show();
		} else if (type == "select" || type == "radiogroup" ||
				type == "checkgroup") {
			new SUI.editor.apps.form.GroupElementDialog(args).show();
		} else if (type == "checkbox") {
			new SUI.editor.apps.form.CheckBoxDialog(args).show();
		} else if (type == "infotext") {
			new SUI.editor.apps.form.InfoDialog(args).show();
		}
	},

	/**
	 * Action handler to move a form element one position up.
	 *
	 * @param {int} id The id of the form element to move up.
	 * @private
	 */
	actMoveUp: function(id) {
		var that = this;

		// Get the scroll top to be able to restore it after saving.
		var st = this.scrollBox.getScrollTop();

		// Update the form element position ...
		SUI.editor.xhr.doGet(
			// ... by using the following action and parameters ...
			SUI.editor.resource.ajaxURL, {
				a: "apps.form.moveUp",
				formElementId: id
			},
			// ... and reload the list after updating.
			function(res) {
				that.loadFormElements({scrollTop: st});
			}
		);
	},

	/**
	 * Action handler to move a form element one position down.
	 *
	 * @param {int} id The id of the form element to move down.
	 * @private
	 */
	actMoveDown: function(id) {
		var that = this;

		// Get the scroll top to be able to restore it after saving.
		var st = this.scrollBox.getScrollTop();

		// Update the form element position ...
		SUI.editor.xhr.doGet(
			// ... by using the following action and parameters ...
			SUI.editor.resource.ajaxURL, {
				a: "apps.form.moveDown",
				formElementId: id
			},
			// ... and reload the list after updating.
			function(res) {
				that.loadFormElements({scrollTop: st});
			}
		);
	},

	/**
	 * Action handler to move a form element to an arbitrary position. This
	 * action opens the postion dialog.
	 *
	 * @param {int} id The id of the form element to move to a new position.
	 * @private
	 */
	actMoveItem: function(id) {
		var that = this;

		// Get the scroll top to be able to restore it after saving.
		var st = this.scrollBox.getScrollTop();

		// Open the postion dialog.
		new SUI.editor.apps.form.PositionDialog({
			pageId: this.pageId,
			pageDefinitionTabId: this.pageDefinitionTabId,
			formElementId: id,
			onOK: function(data) {
				// On OK save the data using the passed data and following
				// action ...
				data.a = "apps.form.moveToPosition";
				SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL,
					data,
					function(res) {
						// ... and reload the list after updating.
						that.loadFormElements({scrollTop: st});
					}
				);
			}
		}).show();
	},

	/**
	 * Action handler to open the form properties dialog.
	 *
	 * @private
	 */
	actFormProperties: function() {
		new SUI.editor.apps.form.PropertiesDialog({
			pageId: this.pageId,
			pageDefinitionTabId: this.pageDefinitionTabId
		}).show();
	},

	/**
	 * Action handler to delete a form element.
	 *
	 * @param {int} id The id of the form element to delete.
	 * @private
	 */
	actDelete: function(id) {
		var that = this;

		// Get the scroll top to be able to restore it after saving.
		var st = this.scrollBox.getScrollTop();

		// Open the confirm dialog.
		new SUI.dialog.Confirm({
			caption: SUI.editor.i18n.apps.form.captionDeleteItem,
			text: SUI.editor.i18n.apps.form.textDeleteItem,
			onOK: function() {
				// On OK delete the form element ...
				SUI.editor.xhr.doGet(
					// ... by using the following action and parameters ...
					SUI.editor.resource.ajaxURL, {
						a: "apps.form.deleteFormElement",
						formElementId: id
					},
					function(res) {
						// ... and reload the list after updating.
						that.loadFormElements({scrollTop: st});
					}
				);
			}
		}).show();

	},

	/**
	 * Action handler to open a new window with an HTML report of all filled
	 * in forms.
	 *
	 * @private
	 */
	actExportHtml: function() {
		var loc = "ajax/apps/form/exp_html.php?pageId="+ this.pageId;
		loc += "&pageDefinitionTabId="+ this.pageDefinitionTabId;
		var w = window.open(loc, "report");
	},

	/**
	 * Action handler to download an (HTML)excel report of all filled
	 * in forms.
	 *
	 * @private
	 */
	actExportExcel: function() {
		var loc = "ajax/apps/form/exp_excel.php?pageId="+ this.pageId;
		loc += "&pageDefinitionTabId="+ this.pageDefinitionTabId;
		var w = window.open(loc, "report");
	},

	/**
	 * Create the action list for this application.
	 *
	 * @private
	 */
	createActionList: function() {
		var that = this;

		// Set all the action for the form application in an action list.
		return new SUI.ActionList([{
				actionId: "form.newElemInput",
				title: SUI.editor.i18n.apps.form.newElemInput,
				icon: SUI.editor.resource.apps.form.icnElemInput,
				handler: function(c) { that.actFormElement("input"); }
			},{
				actionId: "form.newElemTextArea",
				title: SUI.editor.i18n.apps.form.newElemTextArea,
				icon: SUI.editor.resource.apps.form.icnElemTextArea,
				handler: function(c) { that.actFormElement("textarea"); }
			},{
				actionId: "form.newElemRadioGroup",
				title: SUI.editor.i18n.apps.form.newElemRadioGroup,
				icon: SUI.editor.resource.apps.form.icnElemRadioGroup,
				handler: function(c) { that.actFormElement("radiogroup"); }
			},{
				actionId: "form.newElemSelectList",
				title: SUI.editor.i18n.apps.form.newElemSelectList,
				icon: SUI.editor.resource.apps.form.icnElemSelectList,
				handler: function(c) { that.actFormElement("select"); }
			},{
				actionId: "form.newElemCheckBox",
				title: SUI.editor.i18n.apps.form.newElemCheckBox,
				icon: SUI.editor.resource.apps.form.icnElemCheckBox,
				handler: function(c) { that.actFormElement("checkbox"); }
			},{
				actionId: "form.newElemCheckGroup",
				title: SUI.editor.i18n.apps.form.newElemCheckGroup,
				icon: SUI.editor.resource.apps.form.icnElemCheckGroup,
				handler: function(c) { that.actFormElement("checkgroup"); }
			},{
				actionId: "form.newElemAttachment",
				title: SUI.editor.i18n.apps.form.newElemAttachment,
				icon: SUI.editor.resource.apps.form.icnElemAttachment,
				handler: function(c) { that.actFormElement("file"); }
			},{
				actionId: "form.newElemMail",
				title: SUI.editor.i18n.apps.form.newElemMail,
				icon: SUI.editor.resource.apps.form.icnElemMail,
				handler: function(c) { that.actFormElement("email"); }
			},{
				actionId: "form.newElemInfo",
				title: SUI.editor.i18n.apps.form.newElemInfo,
				icon: SUI.editor.resource.apps.form.icnElemInfo,
				handler: function(c) { that.actFormElement("infotext"); }
			},{
				actionId: "form.edtProperties",
				title: SUI.editor.i18n.apps.form.edtProperties,
				icon: SUI.editor.resource.apps.form.icnProperties,
				handler: function(c) { that.actFormProperties(); }
			},{
				actionId: "form.expHtml",
				title: SUI.editor.i18n.apps.form.expHtml,
				icon: SUI.editor.resource.apps.form.icnExportHtml,
					handler: function(c) { that.actExportHtml(); }
			},{
				actionId: "form.expExcel",
				title: SUI.editor.i18n.apps.form.expExcel,
				icon: SUI.editor.resource.apps.form.icnExportExcel,
				handler: function(c) { that.actExportExcel(); }
			}
		]);
	},

	/**
	 * Create the toolbar for this application.
	 *
	 * @private
	 */
	createToolBar: function(actionList) {
		// Create a toolbar, use the action list for its actions.
		return new SUI.Toolbar({
			actionlist: actionList,
			tools: [
				new SUI.ToolbarButton({actionId: "form.newElemInput"}),
				new SUI.ToolbarButton({actionId: "form.newElemTextArea"}),
				new SUI.ToolbarButton({actionId: "form.newElemRadioGroup"}),
				new SUI.ToolbarButton({actionId: "form.newElemSelectList"}),
				new SUI.ToolbarButton({actionId: "form.newElemCheckBox"}),
				new SUI.ToolbarButton({actionId: "form.newElemCheckGroup"}),
				new SUI.ToolbarButton({actionId: "form.newElemAttachment"}),
				new SUI.ToolbarButton({actionId: "form.newElemMail"}),
				new SUI.ToolbarSeparator({}),
				new SUI.ToolbarButton({actionId: "form.newElemInfo"}),
				new SUI.ToolbarSeparator({}),
				new SUI.ToolbarButton({actionId: "form.edtProperties"}),
				new SUI.ToolbarSeparator({}),
				new SUI.ToolbarButton({actionId: "form.expHtml"}),
				new SUI.ToolbarButton({actionId: "form.expExcel"})
			]
		});
	}

});
