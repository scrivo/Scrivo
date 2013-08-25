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
 * $Id: FindDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.FindDialog = SUI.defineClass({

	initializer: function(arg) {

		var that = this;

		this.cbFind = arg.onFind ? arg.onFind : function(e) {};
		this.cbCancel = arg.onCancel ? arg.onCancel : function(e) {};
		this.cbReplace = arg.onReplace ? arg.onReplace : function(e) {};
		this.cbReplaceAll = arg.onReplaceAll ? arg.onReplaceAll : function(e) {};

		this.window = new SUI.Window({
			height: this.DIALOG_HEIGHT,
			width: this.DIALOG_WIDTH,
			resizable: false
		});

		this.data = {
			doFind: true,
			find: "",
			replace: "",
			wholeWords: false,
			matchCase: false,
			directionDown: true
		};

		this.window.caption(SUI.editor.i18n.htmleditor.findDialog.cptFind);

		this.selTab = 0;
		this.tabPanel = new SUI.TabPanel({
			tabs: [
				{ title: SUI.editor.i18n.htmleditor.findDialog.txtFind },
				{ title: SUI.editor.i18n.htmleditor.findDialog.txtReplace }
			],
			onSelectTab: function(tab) {
				that.doSelectTab(tab);
			},
			selected: (this.selTab)
		});

		this.populateFindTab();
		this.populateReplaceTab();

		this.setFindData();

		this.window.add(this.tabPanel);
	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 75,
	BUTTON_HEIGHT: 24,
	V_MARGIN: 2,

	BUTTON_WIDTH: 120,
	BUTTON_MARGIN: 4,

	SECT_HORIZ_MARGIN: 20,

	OPTIONS_LABEL_WIDTH: 170,
	DIR_LABEL_WIDTH: 75,
	LAYOUT_CTRL_WIDTH: 24,

	OPTIONS_TOP_FIND: 36,
	OPTIONS_TOP_REPLACE: 70,

	RADIO_WIDTH: 26,
	RADIO_OFFSET: 3,

	DIALOG_WIDTH: 450,
	DIALOG_HEIGHT: 220,

	show: function() {
		this.window.show();
	},

	doSelectTab: function(tab) {

		if (this.selTab == 0) {
			this.getFindData();
			this.setReplaceData();
		} else {
			this.getReplaceData();
			this.setFindData();
		}
		this.selTab = this.tabPanel.selectedTabIndex();

	},

	addFindOptions: function(tabNo) {

		var ctlTop = (tabNo?this.OPTIONS_TOP_REPLACE:this.OPTIONS_TOP_FIND);
		var left = this.MARGIN;

		this["txtOpt"+tabNo] = new SUI.Box({
			top: ctlTop + 2,
			left: left,
			width: this.OPTIONS_LABEL_WIDTH,
			height: this.CTRL_HEIGHT
		});
		this["txtOpt"+tabNo].el().innerHTML =
			"<b>"+SUI.editor.i18n.htmleditor.findDialog.txtOptions+"</b>";

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this["inpWholeWords"+tabNo] = new SUI.form.CheckBox({
			top: ctlTop + this.RADIO_OFFSET,
			left: left + this.RADIO_OFFSET,
			anchor: { left: true, top: true}
		});

		this["lblWholeWords"+tabNo] = new SUI.form.Label({
			top: ctlTop,
			left: left + this.RADIO_WIDTH,
			width: this.OPTIONS_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.findDialog.txtWholeWords,
			forBox: this["inpWholeWords"+tabNo]
		});

		ctlTop += this.CTRL_HEIGHT + this.V_MARGIN;

		this["inpMatchCase"+tabNo] = new SUI.form.CheckBox({
			top: ctlTop + this.RADIO_OFFSET,
			left: left + this.RADIO_OFFSET,
			anchor: { left: true, top: true}
		});

		this["lblMatchCase"+tabNo] = new SUI.form.Label({
			top: ctlTop,
			left: left + this.RADIO_WIDTH,
			width: this.OPTIONS_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.findDialog.txtMatchCase,
			forBox: this["inpMatchCase"+tabNo]
		});

		ctlTop = (tabNo?this.OPTIONS_TOP_REPLACE:this.OPTIONS_TOP_FIND);
		left = left + this.OPTIONS_LABEL_WIDTH + this.SECT_HORIZ_MARGIN;

		this["txtDir"+tabNo] = new SUI.Box({
			top: ctlTop + 2,
			left: left,
			width: this.DIR_LABEL_WIDTH,
			height: this.CTRL_HEIGHT
		});
		this["txtDir"+tabNo].el().innerHTML =
			"<b>"+SUI.editor.i18n.htmleditor.findDialog.txtDirection+"</b>";

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this["inpUp"+tabNo] = new SUI.form.RadioButton({
			top: ctlTop + this.RADIO_OFFSET,
			left: left + this.RADIO_OFFSET,
			name: "options",
			anchor: { left: true, top: true}
		});

		this["lblUp"+tabNo] = new SUI.form.Label({
			top: ctlTop,
			left: left + this.RADIO_WIDTH,
			width: this.DIR_LABEL_WIDTH,
			title: SUI.editor.i18n.htmleditor.findDialog.txtUp,
			forBox: this["inpUp"+tabNo]
		});

		ctlTop += this.CTRL_HEIGHT + this.V_MARGIN;

		this["inpDown"+tabNo] = new SUI.form.RadioButton({
			top: ctlTop + this.RADIO_OFFSET,
			left: left + this.RADIO_OFFSET,
			name: "options",
			anchor: { left: true, top: true}
		});

		this["lblDown"+tabNo] = new SUI.form.Label({
			top: ctlTop,
			left: left + this.RADIO_WIDTH,
			width: this.DIR_LABEL_WIDTH,
			title: SUI.editor.i18n.htmleditor.findDialog.txtDown,
			forBox: this["inpDown"+tabNo]
		});

		this.tabPanel.add(this["txtOpt"+tabNo], tabNo);
		this.tabPanel.add(this["lblWholeWords"+tabNo], tabNo);
		this.tabPanel.add(this["inpWholeWords"+tabNo], tabNo);
		this.tabPanel.add(this["lblMatchCase"+tabNo], tabNo);
		this.tabPanel.add(this["inpMatchCase"+tabNo], tabNo);

		this.tabPanel.add(this["txtDir"+tabNo], tabNo);
		this.tabPanel.add(this["lblUp"+tabNo], tabNo);
		this.tabPanel.add(this["inpUp"+tabNo], tabNo);
		this.tabPanel.add(this["lblDown"+tabNo], tabNo);
		this.tabPanel.add(this["inpDown"+tabNo], tabNo);
	},

	populateFindTab: function() {

		var ctlTop = this.MARGIN;
		var left = this.MARGIN;
		var that = this;

		this.inpFind0 = new SUI.form.Input({
			top: ctlTop,
			left: left + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblFind0 = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.findDialog.txtFind,
			forBox: this.inpFind0
		});

		this.cancelButton0 = new SUI.form.Button({
			bottom: this.BUTTON_MARGIN,
			right: this.BUTTON_MARGIN,
			width: this.BUTTON_WIDTH,
			anchor: { right: true, bottom: true },
			title: SUI.i18n.cancel
		});
		SUI.browser.addEventListener(this.cancelButton0.el(), "click",
			function(e) {
				if (!that.onCancel(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		this.findButton0 = new SUI.form.Button({
			bottom: this.BUTTON_MARGIN,
			right: this.BUTTON_MARGIN + this.BUTTON_WIDTH + this.MARGIN,
			width: this.BUTTON_WIDTH,
			anchor: { right: true, bottom: true },
			title: SUI.editor.i18n.htmleditor.findDialog.butFind
		});
		SUI.browser.addEventListener(this.findButton0.el(), "click",
			function(e) {
				if (!that.onFind(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		this.tabPanel.add(this.lblFind0, 0);
		this.tabPanel.add(this.inpFind0, 0);

		this.addFindOptions(0);

		this.tabPanel.add(this.findButton0, 0);
		this.tabPanel.add(this.cancelButton0, 0);

	},

	getFindData: function() {

		this.data.doFind = true;
		this.data.find = this.inpFind0.el().value;
		this.data.wholeWords = this.inpWholeWords0.el().checked;
		this.data.matchCase = this.inpMatchCase0.el().checked;
		this.data.directionDown = this.inpDown0.el().checked;

	},

	setFindData: function() {

		this.inpFind0.el().value = this.data.find;
		this.inpWholeWords0.el().checked = this.data.wholeWords;
		this.inpMatchCase0.el().checked = this.data.matchCase;
		this.inpDown0.el().checked = this.data.directionDown;
		this.inpUp0.el().checked = !this.data.directionDown;

	},

	populateReplaceTab: function() {

		var ctlTop = this.MARGIN;
		var left = this.MARGIN;
		var that = this;

		this.inpFind1 = new SUI.form.Input({
			top: ctlTop,
			left: left + this.LABEL_WIDTH,
			right: this.BUTTON_WIDTH + this.MARGIN * 3,
			anchor: { left: true, right: true }
		});
		this.lblFind1 = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.findDialog.txtFind,
			forBox: this.inpFind1
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpReplace1 = new SUI.form.Input({
			top: ctlTop,
			left: left + this.LABEL_WIDTH,
			right: this.BUTTON_WIDTH + this.MARGIN * 3,
			anchor: { left: true, right: true }
		});
		this.lblReplace1 = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.findDialog.txtReplace,
			forBox: this.inpReplace1
		});

		this.cancelButton1 = new SUI.form.Button({
			bottom: this.BUTTON_MARGIN,
			right: this.BUTTON_MARGIN,
			width: this.BUTTON_WIDTH,
			anchor: { right: true, bottom: true },
			title: SUI.i18n.cancel
		});
		SUI.browser.addEventListener(this.cancelButton1.el(), "click",
			function(e) {
				if (!that.onCancel(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		this.replaceAllButton1 = new SUI.form.Button({
			bottom: this.BUTTON_MARGIN + this.BUTTON_HEIGHT + this.MARGIN,
			right: this.BUTTON_MARGIN,
			width: this.BUTTON_WIDTH,
			anchor: { right: true, bottom: true },
			title: SUI.editor.i18n.htmleditor.findDialog.butReplaceAll
		});
		SUI.browser.addEventListener(this.replaceAllButton1.el(), "click",
			function(e) {
				if (!that.onReplaceAll(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		this.replaceButton1 = new SUI.form.Button({
			bottom: this.BUTTON_MARGIN + 2*this.BUTTON_HEIGHT + 2*this.MARGIN,
			right: this.BUTTON_MARGIN,
			width: this.BUTTON_WIDTH,
			anchor: { right: true, bottom: true },
			title: SUI.editor.i18n.htmleditor.findDialog.butReplace
		});
		SUI.browser.addEventListener(this.replaceButton1.el(), "click",
			function(e) {
				if (!that.onReplace(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		this.findButton1 = new SUI.form.Button({
			bottom: this.BUTTON_MARGIN + 3*this.BUTTON_HEIGHT + 3*this.MARGIN,
			right: this.BUTTON_MARGIN,
			width: this.BUTTON_WIDTH,
			anchor: { right: true, bottom: true },
			title: SUI.editor.i18n.htmleditor.findDialog.butFind
		});
		SUI.browser.addEventListener(this.findButton1.el(), "click",
			function(e) {
				if (!that.onFind(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		this.tabPanel.add(this.lblFind1, 1);
		this.tabPanel.add(this.inpFind1, 1);

		this.tabPanel.add(this.lblReplace1, 1);
		this.tabPanel.add(this.inpReplace1, 1);

		this.addFindOptions(1);

		this.tabPanel.add(this.findButton1, 1);
		this.tabPanel.add(this.replaceButton1, 1);
		this.tabPanel.add(this.replaceAllButton1, 1);
		this.tabPanel.add(this.cancelButton1, 1);

	},

	getReplaceData: function() {

		this.data.doFind = false;
		this.data.find = this.inpFind1.el().value;
		this.data.replace = this.inpReplace1.el().value;
		this.data.wholeWords = this.inpWholeWords1.el().checked;
		this.data.matchCase = this.inpMatchCase1.el().checked;
		this.data.directionDown = this.inpDown1.el().checked;

	},

	setReplaceData: function() {

		this.inpFind1.el().value = this.data.find;
		this.inpWholeWords1.el().checked = this.data.wholeWords;
		this.inpMatchCase1.el().checked = this.data.matchCase;
		this.inpDown1.el().checked = this.data.directionDown;
		this.inpUp1.el().checked = !this.data.directionDown;

	},

	onFind: function() {
		if (this.selTab == 0) {
			this.getFindData();
		} else {
			this.getReplaceData();
		}
		this.cbFind(this.data);
	},

	onReplace: function() {
		this.getReplaceData();
		this.cbReplace(this.data);
	},

	onReplaceAll: function() {
		this.getReplaceData();
		this.cbReplaceAll(this.data);
		this.window.close();
	},

	onCancel: function() {
		this.window.close();
	}

});
