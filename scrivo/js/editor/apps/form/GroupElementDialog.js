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
 * $Id: GroupElementDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.GroupElementDialog = SUI.defineClass({

	baseClass: SUI.editor.apps.form.FormElementDialog,

	initializer: function(arg) {

		this.type = arg.type || "select";

		SUI.editor.apps.form.GroupElementDialog.initializeBase(this, arg);

	},

	TOOL_BUTTON_SIZE: 20,
	NEW_OPTION_SIZE: 200,

	formToData: function() {

		var res = SUI.editor.apps.form.GroupElementDialog.parentMethod(
			this, "formToData");

		if (this.type == "radiogroup") {
			res.itemInfo_UNCHECKED =
				this.chkUncheckedByDefault.el().checked;
		}

		var txt = [];
		for (var i=0; i<this.list.children.length; i++) {
			var t = SUI.trim(this.list.children[i].data.el().value);
			if (t != "") {
				txt.push(t);
			}
		}
		res.itemInfo_ITEMS = txt.join("\n");

		return res;
	},

	dataToForm: function(a) {
		SUI.editor.apps.form.InputElementDialog.parentMethod(
			this, "dataToForm", a);
		var ii = a.typeData.items || [];
		if (!ii.length) {
			this.doAddButton();
		} else {
			for (var i=0; i<ii.length; i++) {
				this.doAddButton(ii[i]);
			}
		}
		if (this.type == "radiogroup") {
			this.chkUncheckedByDefault.el().checked = a.typeData.unchecked;
		}
		this.doDrawButtons();
	},

	populatePropertiesTab: function() {
		var that = this;

		var ctlTop = SUI.editor.apps.form.GroupElementDialog.parentMethod(this,
			"populatePropertiesTab");

		this.addButton = new SUI.form.Button({
			bottom: this.MARGIN,
			right: this.MARGIN,
			anchor: { bottom: true, right: true },
			width: this.NEW_OPTION_SIZE,
			title: this.i18n.btnNewOption
		});
		SUI.browser.addEventListener(this.addButton.el(), "click", function() {
			that.doAddButton();
			that.doDrawButtons();
		});

		if (this.type == "radiogroup") {

			this.chkUncheckedByDefault = new SUI.form.CheckBox({
				top: ctlTop,
				left: this.MARGIN,
				anchor: { left: true }
			});
			this.lblUncheckedByDefault = new SUI.form.Label({
				top: ctlTop,
				anchor: { left: true, right: true },
				left: this.MARGIN + this.CHK_WIDTH,
				width: this.MARGIN,
				title: this.i18n.dlgDefaultUnchecked,
				forBox: this.chkUncheckedByDefault
			});

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		}

		this.lblOptionList = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			right: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			anchor: { left: true, right: true },
			title: this.i18n.dlgOptionList
		});
		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.list = new SUI.AnchorLayout({
			bottom: this.MARGIN + this.CTRL_HEIGHT,
			right: this.MARGIN,
			top: ctlTop,
			left: this.MARGIN,
			anchor: { top: true, left: true, bottom: true, right: true }
		});

		this.tabPanel.add(this.addButton, 0);
		if (this.type == "radiogroup") {
			this.tabPanel.add(this.lblUncheckedByDefault, 0);
			this.tabPanel.add(this.chkUncheckedByDefault, 0);
		}
		this.tabPanel.add(this.lblOptionList, 0);
		this.tabPanel.add(this.list, 0);

		this.listTop = ctlTop;

		this.doDrawButtons();

	},

	moveButton: function (butt, up) {
		var t, i, n, l = this.list.children.length;
		// Find button index ...
		for (i=0; i<l; i++) {
			if (this.list.children[i] == butt) {
				break;
			}
		}
		// ... get the new index ...
		if (up) {
			n = i-1; {
				if (n<0) {
					n = l-1;
				}
			}
		} else {
			n = i+1; {
				if (n>=l) {
					n = 0;
				}
			}
		}
		// ... and swap elements at index i and n.
		var t=this.list.children[i];
		this.list.children[i] = this.list.children[n];
		this.list.children[n] = t;
	},


	doAddButton: function(txt) {

		var that = this;

		var box = new SUI.AnchorLayout({
			height: this.CTRL_HEIGHT,
			anchor: { left: true, right: true },
			top: 0
		});

		var input = new SUI.form.Input({
			right: this.TOOL_BUTTON_SIZE*3,
			anchor: { left: true, right: true }
		});
		input.el().value = txt || "";

		var deleteButton = new SUI.ToolbarButton({
			anchor: { right: true },
			width: this.TOOL_BUTTON_SIZE,
			height: this.TOOL_BUTTON_SIZE,
			title: this.i18n.btnOptionDelete,
			icon: SUI.editor.resource.apps.form.icnDelete,
			handler: function() {
				that.list.remove(box);
				that.list.parent().el().style.overflow = "hidden";
				that.doDrawButtons();
			}
		});

		var downButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: this.TOOL_BUTTON_SIZE,
			width: this.TOOL_BUTTON_SIZE,
			height: this.TOOL_BUTTON_SIZE,
			title: this.i18n.btnOptionDown,
			icon: SUI.editor.resource.apps.form.icnDown,
			handler: function() {
				that.moveButton(box, false);
				that.doDrawButtons();
			}
		});

		var upButton = new SUI.ToolbarButton({
			anchor: { right: true },
			right: 2 * this.TOOL_BUTTON_SIZE,
			width: this.TOOL_BUTTON_SIZE,
			height: this.TOOL_BUTTON_SIZE,
			title: this.i18n.btnOptionUp,
			icon: SUI.editor.resource.apps.form.icnUp,
			handler: function() {
				that.moveButton(box, true);
				that.doDrawButtons();
			}
		});

		box.data = input;

		box.add(input);
		box.add(upButton);
		box.add(downButton);
		box.add(deleteButton);

		this.list.add(box);
	},

	doDrawButtons: function() {

		var t = 0;
		for (var i=0; i<this.list.children.length; i++) {
			this.list.children[i].top(t);
			t += this.CTRL_HEIGHT + this.MARGIN;
		}

		this.list.height(t);

		var ts = this.tabPanel.clientAreaPosition();
		this.setClientHeight(ts.top + ts.bottom + this.listTop +
				this.list.height() + 2*this.MARGIN + this.CTRL_HEIGHT);

		this.draw();


	}

});

