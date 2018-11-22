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
 * $Id: FormElements.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.FormElements = SUI.defineClass(
	/** @lends SUI.editor.apps.form.FormElements.prototype */{

	/** @ignore */ baseClass: SUI.AnchorLayout,

	/**
	 * @classdesc
	 * The SUI.editor.apps.form.FormElements class renders a list of
	 * form elements. All form element have a litte toolbar to perform
	 * editing actions on them like edit, delete and reposition.
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

		SUI.editor.apps.form.FormElements.initializeBase(this, arg);
		var that = this;

		this.anchor = { left: true, right: true};

		this.frame = arg.frame;

		this.padding(new SUI.Padding(this.MARGIN));

		var top = 0;

		for (var i=0; i<arg.data.elements.length; i++) {

			// Create a panel to host the label and form element and form
			// element toolbar.
			var panel = new SUI.AnchorLayout({
				top: top,
				anchor: { left: true, right: true }
			});

			// Add the form element toolbar to the panel.
			this.addButtons(panel, arg.data.elements[i]);

			// Create a panel for the label and form element.
			var elemBox = new SUI.Panel({
				border: i==arg.data.elements.length-1 ? new SUI.Border(1) :
					new SUI.Border(1, 1, 0),
				padding: new SUI.Padding(0),
				right: 6 * this.BUTTON_SIZE + this.MARGIN,
				anchor: { left: true, right: true },
				height: this.CTRL_HEIGHT
			});

			// Create the form element label and add it to the form element
			// panel ...
			var lbl = new SUI.form.Label({
				top: this.TOP_MARGIN,
				left: this.MARGIN,
				height: this.CTRL_HEIGHT,
				width: this.LABEL_WIDTH,
				anchor: { left: true },
				title: arg.data.elements[i].label
			});
			// ... and add it to the panel (not for info boxes and check boxes).
			if (arg.data.elements[i].type != "checkbox" &&
					arg.data.elements[i].type != "infotext") {
				elemBox.add(lbl);
			}

			// Get the correct form element to display ...
			var el = this.getFormElement(arg.data.elements[i]);
			if (el) {
				// ... and add it to the form.
				elemBox.add(el);
				elemBox.height(el.height() + 2*this.TOP_MARGIN +
					elemBox.border().top + elemBox.border().bottom);
			}

			// Add the element box to the panel ...
			panel.add(elemBox);
			// ... set the panel to the element box height.
			panel.height(elemBox.height());

			// Add the element panel to the list..
			this.add(panel);
			// ... and get the top position for the next form element in
			// the list.
			top += panel.height();
		}

		this.height(top + 2*this.MARGIN);

	},

	/**
	 * The size of the buttons in the form element toolbars.
	 * @constant
	 * @type int
	 * @private
	 */
	BUTTON_SIZE: 26,

	/**
	 * The margin width that is generally used.
	 * @constant
	 * @type int
	 * @private
	 */
	MARGIN: 8,

	/**
	 * Top and bottom margin to be used in form element boxes.
	 * @constant
	 * @type int
	 * @private
	 */
	TOP_MARGIN: 6,

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
	LABEL_WIDTH: 150,

	/**
	 * Convert form element type ids to more readable names.
	 *
	 * @param {String} id The form element type id number.
	 * @return {String} The form element type name.
	 * @private
	 */
	typeConvert: function(id) {
		switch(parseInt(id, 10)) {
		}
	},

	/**
	 * Add a litte toolbar with edit, copy, postion and delete buttons to
	 * the given panel in the upper right corner.
	 *
	 * @param {SUI.AnchorLayout} panel A box to add the buttons to.
	 * @param {Object} element The form element for which to add the toolbar.
	 * @private
	 */
	addButtons: function(panel, element) {
		var that = this;

		// Create a button to delete the form element.
		var deleteButton = new SUI.ToolbarButton({
			anchor: { right: true },
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			title: SUI.editor.i18n.apps.form.deleteItem,
			icon: SUI.editor.resource.apps.form.icnDelete,
			handler: function() {
				that.frame.actDelete(this.extraInfo.listItemId);
			}
		});
		deleteButton.extraInfo = { listItemId: element.listItemId };

		// Create a button to move the form element one postion down.
		var downButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: this.BUTTON_SIZE,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			title: SUI.editor.i18n.apps.form.moveElementDown,
			icon: SUI.editor.resource.apps.form.icnDown,
			handler: function() {
				that.frame.actMoveDown(this.extraInfo.listItemId);
			}
		});
		downButton.extraInfo = { listItemId: element.listItemId };

		// Create a button to move the form element to an arbitrary postion.
		var moveButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: 2 * this.BUTTON_SIZE,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			title: SUI.editor.i18n.apps.form.moveElement,
			icon: SUI.editor.resource.apps.form.icnMove,
			handler: function() {
				that.frame.actMoveItem(this.extraInfo.listItemId);
			}
		});
		moveButton.extraInfo = { listItemId: element.listItemId };

		// Create a button to move the form element one postion up.
		var upButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: 3 * this.BUTTON_SIZE,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			title: SUI.editor.i18n.apps.form.moveElementUp,
			icon: SUI.editor.resource.apps.form.icnUp,
			handler: function() {
				that.frame.actMoveUp(this.extraInfo.listItemId);
			}
		});
		upButton.extraInfo = { listItemId: element.listItemId };

		// Create a button to copy the form element.
		var copyButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: 4 * this.BUTTON_SIZE,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			title: SUI.editor.i18n.apps.form.copyElement,
			icon: SUI.editor.resource.apps.form.icnCopyItem,
			handler: function() {
				that.frame.actFormElement(this.extraInfo.type,
					this.extraInfo.listItemId, true);
			}
		});
		copyButton.extraInfo = {
			listItemId: element.listItemId,
			type: element.type
		};

		// Create a button to modify the form element.
		var editButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: 5 * this.BUTTON_SIZE,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			title: SUI.editor.i18n.apps.form.editElement,
			icon: SUI.editor.resource.apps.form.icnEditItem,
			handler: function() {
				that.frame.actFormElement(this.extraInfo.type,
					this.extraInfo.listItemId);
			}
		});
		editButton.extraInfo = {
			listItemId: element.listItemId,
			type: element.type
		};

		// Add the buttons to the panel.
		panel.add(copyButton);
		panel.add(upButton);
		panel.add(moveButton);
		panel.add(downButton);
		panel.add(deleteButton);
		panel.add(editButton);

	},

	/**
	 * Create a sample form element based on form definition data. This
	 * function only dispatches to the given type of form element.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.Box} A SUI form element object.
	 * @private
	 */
	getFormElement: function(data) {
		switch (data.type) {
			case "input": return this.buildInputElement(data);
			case "textarea": return this.buildTextAreaElement(data);
			case "radiogroup": return this.buildRadioGroup(data);
			case "select": return this.buildSelectList(data);
			case "checkbox": return this.buildCheckBox(data);
			case "checkgroup": return this.buildCheckBoxGroup(data);
			case "infotext": return this.buildInfoText(data);
			case "file": return this.buildFileUploadField(data);
			case "email": return this.buildInputElement(data);
		}
		return null;
	},

	/**
	 * Create a sample input field based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.form.Input} An input form element.
	 * @private
	 */
	buildInputElement: function(data) {
		var e = new SUI.form.Input({
			top: this.TOP_MARGIN,
			left: 2*this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		if (data.maxLength) {
			e.el().maxLength = data.maxLength;
		}
		if (data.width != 0) {
			e.el().size = data.width;
			e.anchor = { left: true };
		}
		e.el().value = data.defaultValue;
		return e;
	},

	/**
	 * Create a sample text area based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.form.TextArea} A textarea form element.
	 * @private
	 */
	buildTextAreaElement: function(data) {
		var e = new SUI.form.TextArea({
			top: this.TOP_MARGIN,
			left: 2*this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT * data.ROWS,
			anchor: { right: true, left: true }
		});
		if (data.width != 0) {
			e.el().rows = data.width;
			e.anchor = { left: true };
		}
		e.el().value = data.defaultValue;
		return e;
	},

	/**
	 * Create a sample radio group based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.AnchorLayout} A box with the radio buttons.
	 * @private
	 */
	buildRadioGroup: function(data) {
		// Get the number of items in the group.
		var n = data.items.length;
		// Create an anchor layout to host the radio buttons.
		var e = new SUI.AnchorLayout({
			top: this.TOP_MARGIN,
			left: 2*this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT * n,
			anchor: { right: true, left: true }
		});
		// Is the group unchecked or checked by default.
		var chk = data.unchecked;
		// Loop to create the controls.
		for (var i=0; i<n; i++) {
			// Create radio button.
			var rb = new SUI.form.RadioButton({
				top: i*this.CTRL_HEIGHT,
				height: this.CTRL_HEIGHT,
				anchor: { right: true, left: true },
				checked: chk,
				name: "name_"+data.listItemId
			});
			// Create the label.
			var label = new SUI.form.Label({
				top: i*this.CTRL_HEIGHT,
				height: this.CTRL_HEIGHT,
				left: this.CTRL_HEIGHT,
				anchor: { right: true, left: true },
				forBox: rb,
				title: data.items[i]
			});
			// Add them to the anchor layout.
			e.add(rb);
			e.add(label);
			// Only the first item can be checked.
			chk = false;
		}
		// Return the anchor layout with the radio buttons.
		return e;
	},

	/**
	 * Create a sample check box group based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.AnchorLayout} A box with the check boxes.
	 * @private
	 */
	buildCheckBoxGroup: function(data) {
		// Get the number of items in the group.
		var n = data.items.length;
		// Create an anchor layout to host the check boxes.
		var e = new SUI.AnchorLayout({
			top: this.TOP_MARGIN,
			left: 2*this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT * n,
			anchor: { right: true, left: true }
		});
		// Loop to create the controls.
		for (var i=0; i<n; i++) {
			// Create a check box.
			var cb = new SUI.form.CheckBox({
				top: i*this.CTRL_HEIGHT,
				height: this.CTRL_HEIGHT,
				anchor: { right: true, left: true }
			});
			// Create the label.
			var label = new SUI.form.Label({
				top: i*this.CTRL_HEIGHT,
				height: this.CTRL_HEIGHT,
				left: this.CTRL_HEIGHT,
				anchor: { right: true, left: true },
				forBox: cb,
				title: data.items[i]
			});
			// Add them to the anchor layout.
			e.add(cb);
			e.add(label);
		}
		// Return the anchor layout with the radio buttons.
		return e;
	},

	/**
	 * Create a sample select list based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.form.SelectList} A select list form element.
	 * @private
	 */
	buildSelectList: function(data) {
		// Get the number of items in the list.
		var n = data.items.length;
		// And create data for a select list.
		var opts = [];
		for (var i=0; i<n; i++) {
			opts.push({text: data.items[i]});
		}
		// Create an anchor layout to host the check boxes.
		var e = new SUI.form.SelectList({
			top: this.TOP_MARGIN,
			left: 2*this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			options: opts
		});
		return e;
	},

	/**
	 * Create a sample check box based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.AnchorLayout} A box with a check box and label.
	 * @private
	 */
	buildCheckBox: function(data) {
		// Create an anchor layout to host the check box and label.
		var e = new SUI.AnchorLayout({
			top: this.TOP_MARGIN,
			left: this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});
		// Is check box checked by default.
		var chk = data.checked;
		// Create a check box.
		var cb = new SUI.form.CheckBox({
			top: 0,
			height: this.CTRL_HEIGHT,
			checked: chk,
			anchor: { right: true, left: true }
		});
		// Create the label.
		var label = new SUI.form.Label({
			top: 0,
			height: this.CTRL_HEIGHT,
			left: this.CTRL_HEIGHT,
			anchor: { right: true, left: true },
			forBox: cb,
			title: data.label
		});
		// Add them to the anchor layout.
		e.add(cb);
		e.add(label);
		// Return the anchor layout with the radio buttons.
		return e;
	},

	/**
	 * Create a sample file upload field based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.form.Input} A file input form element.
	 * @private
	 */
	buildFileUploadField: function(data) {
		var e = new SUI.form.Input({
			top: this.TOP_MARGIN,
			left: 2*this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		if (data.width != 0) {
			e.el().size = data.width;
			e.anchor = { left: true };
		}
		e.el().value = data.defaultValue;
		e.el().type = "file";
		e.el().name = "userfile"+data.listItemId+"[]";
		return e;
	},

	/**
	 * Create a sample information text based on form definition data.
	 *
	 * @param {Object} data The form element for which to add the toolbar.
	 * @return {SUI.Box} A box with the info text.
	 * @private
	 */
	buildInfoText: function(data) {
		var e = new SUI.Box({
			top: this.TOP_MARGIN,
			left: this.MARGIN,
			right: this.MARGIN,
			height: 60,
			anchor: { right: true, left: true }
		});
		e.el().innerHTML = data.infoText;
		return e;
	}

});
