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
 * $Id: TableCellDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.TableCellDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.TableCellDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.TableCellDialog.initializeBase(
			this, arg);
		var that = this;

		this.data = arg.args;

		this.isHeaderCell = this.data.ctype == "header";

		this.caption(
			SUI.editor.i18n.htmleditor.tableCellDialog.cptEditTableCell);

		this.ttOnChangeCellType = function(event) {
			var e = new SUI.Event(this, event);

			that.changeCellType(e);
			SUI.browser.noPropagation(e.event);
		};

		this.ttOnFocusFreeHeaderCells = function(event) {
			var e = new SUI.Event(this, event);
			that.selHeaderCells.el().selectedIndex = -1;
			that.buttHeaderCells.el().innerHTML =
				SUI.editor.i18n.htmleditor.tableCellDialog.txtSelect;
			SUI.browser.noPropagation(e.event);
		};

		this.ttOnFocusHeaderCells = function(event) {
			var e = new SUI.Event(this, event);
			that.selFreeHeaderCells.el().selectedIndex = -1;
			that.buttHeaderCells.el().innerHTML =
				SUI.editor.i18n.htmleditor.tableCellDialog.txtDeselect;
			SUI.browser.noPropagation(e.event);
		};

		this.ttTransferHeaderCells = function(event) {
			var e = new SUI.Event(this, event);
			that.transferHeaderCells();
			SUI.browser.noPropagation(e.event);
		};

		this.ttAddAxis = function(event) {
			var e = new SUI.Event(this, event);
			if (SUI.trim(that.inpAxis.el().value) != "") {
				that.selAxis.el().options[that.selAxis.el().options.length] =
					new Option(that.inpAxis.el().value);
				that.inpAxis.el().value = "";
			}
			SUI.browser.noPropagation(e.event);
		};

		this.populateForm();

		this.dataToForm();

	},

	STYLES_LEFT: 240,
	VERTICAL_SPLIT_WIDTH: 40,
	MARGIN: 8,
	H_MARGIN: 16,
	CTRL_HEIGHT: 20,
	BUTTON_HEIGHT: 24,
	TEXT_WIDTH: 160,
	LABEL_WIDTH_STYLES: 120,
	LABEL_WIDTH_ABBR: 80,
	RADIO_WIDTH: 26,
	RADIO_OFFSET: 3,
	ROW_SPACING_SMALL: 2,
	ROW_SPACING_LARGE: 12,
	NUM_INPUT_WIDTH: 30,
	TEXT_OFFSET: 2,
	SEL_UNIT_WIDTH: 50,
	SEL_BORDER_STYLE: 70,
	COL_CTRL_WIDTH: 100,
	ADVANCED_PANEL_HEIGHT: 175,

	changeCellType: function(e) {
		this.isHeaderCell = (e.elListener == this.inpHeaderCell.el());
		this.showAdvanced();
	},

	showAdvanced: function() {

		if (this.isHeaderCell) {

			this.headerCellPanel.el().style.display = "block";
			this.dataCellPanel.el().style.display = "none";

		} else {

			this.headerCellPanel.el().style.display = "none";
			this.dataCellPanel.el().style.display = "block";

		}

	},

	transfer: function(a, b) {
		for (var i=a.options.length-1; i>=0; i--) {
			if (a.options[i].selected) {
				var o = a.options[i];
				b.options[b.options.length] = new Option(o.text, o.value);
				a.options[i] = null;
			}
		}
	},

	transferHeaderCells: function() {
		this.transfer(this.selFreeHeaderCells.el(), this.selHeaderCells.el());
		this.transfer(this.selHeaderCells.el(), this.selFreeHeaderCells.el());
	},

	/**
	* Copy of the one in TableDialog
	* TODO: don't copy code
	*/
	borderOptions: function(t, lbl, style, col, inp, unit, txt) {

		var bw = [];
		bw.push({
			value: "",
			text: ""
		});
		for (var i=0; i<SUI.editor.resource.cssBorderStyles.length; i++) {
			bw.push({
				value: SUI.editor.resource.cssBorderStyles[i],
				text: SUI.editor.resource.cssBorderStyles[i]
			});
		}

		var cu = [];
		cu.push({
			value: "",
			text: ""
		});
		for (var i=0; i<SUI.editor.resource.cssUnits.length; i++) {
			cu.push({
				value: SUI.editor.resource.cssUnits[i],
				text: SUI.editor.resource.cssUnits[i]
			});
		}

		this[style] = new SUI.form.SelectList({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT,
			width: this.SEL_BORDER_STYLE,
			options: bw
		});
		this[lbl] = new SUI.form.Label({
			top: t,
			left: this.MARGIN + this.STYLES_LEFT,
			width: this.LABEL_WIDTH_STYLES - this.MARGIN,
			title: txt,
			forBox: this[style],
			anchor: { left: true, top: true}
		});
		this[col] = new SUI.editor.properties.Color({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT
				+ this.H_MARGIN + this.SEL_BORDER_STYLE,
			anchor: { right: true, left: true, top: true}
		});
		this[inp] = new SUI.form.Input({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT
				+ this.H_MARGIN + this.SEL_BORDER_STYLE + this.H_MARGIN
				+ this.COL_CTRL_WIDTH,
			width: this.NUM_INPUT_WIDTH,
			anchor: { left: true }
		});
		this[unit] = new SUI.form.SelectList({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT
				+ this.H_MARGIN + this.SEL_BORDER_STYLE + this.H_MARGIN
				+ this.COL_CTRL_WIDTH + this.NUM_INPUT_WIDTH,
			width: this.SEL_UNIT_WIDTH,
			options: cu
		});

		this.clientPanel.add(this[lbl]);
		this.clientPanel.add(this[style]);
		this.clientPanel.add(this[col]);
		this.clientPanel.add(this[inp]);
		this.clientPanel.add(this[unit]);
	},

	populateForm: function() {

		var ctlTop = this.MARGIN;
		var that = this;

		this.txtCellStyle = new SUI.Box({
			top: ctlTop + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT
		});
		that.txtCellStyle.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableCellDialog.txtStandardStyles
			+ "</b>";
		this.clientPanel.add(this.txtCellStyle);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selStyles = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN,
			width: this.STYLES_LEFT - this.VERTICAL_SPLIT_WIDTH
		});
		this.clientPanel.add(this.selStyles);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		ctlTop += this.CTRL_HEIGHT;

		this.txtHeaders = new SUI.Box({
			top: ctlTop + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT
		});
		that.txtHeaders.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableCellDialog.txtCellType
			+ "</b>";
		this.clientPanel.add(this.txtHeaders);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpDataCell = new SUI.form.RadioButton({
			top: ctlTop + this.RADIO_OFFSET,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "headeroptions",
			anchor: { left: true, top: true}
		});
		if (!this.isHeaderCell) {
			this.inpDataCell.el().checked = true;
		}
		SUI.browser.addEventListener(this.inpDataCell.el(), "change",
			this.ttOnChangeCellType);

		this.lblDataCell = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN  + this.RADIO_WIDTH,
			width: this.STYLES_LEFT - this.MARGIN - this.RADIO_WIDTH,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtDataCell,
			forBox: this.inpDataCell,
			anchor: { left: true, top: true}
		});

		this.clientPanel.add(this.lblDataCell);
		this.clientPanel.add(this.inpDataCell);

		ctlTop += this.CTRL_HEIGHT + this.ROW_SPACING_SMALL;

		this.inpHeaderCell = new SUI.form.RadioButton({
			top: ctlTop + this.RADIO_OFFSET,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "headeroptions",
			anchor: { left: true, top: true}
		});
		if (this.isHeaderCell) {
			this.inpHeaderCell.el().checked = true;
		}
		SUI.browser.addEventListener(this.inpHeaderCell.el(), "change",
			this.ttOnChangeCellType);

		this.lblHeaderCell = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN  + this.RADIO_WIDTH,
			width: this.STYLES_LEFT - this.MARGIN - this.RADIO_WIDTH,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtHeaderCell,
			forBox: this.inpHeaderCell,
			anchor: { left: true, top: true}
		});

		this.clientPanel.add(this.lblHeaderCell);
		this.clientPanel.add(this.inpHeaderCell);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		/**[ Styles ]***************************************/

		var ctlTop2 = this.MARGIN;

		this.txtCellStyle = new SUI.Box({
			top: ctlTop2 + this.TEXT_OFFSET,
			left: this.MARGIN + this.STYLES_LEFT,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT
		});
		that.txtCellStyle.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableCellDialog.txtCellStyle
			+ "</b>";
		this.clientPanel.add(this.txtCellStyle);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;
		this.borderOptions(ctlTop2, "lblLeftBorder", "selLeftBorderStyle",
			"colLeftBorder", "inpLeftBorder", "selLeftBorderUnit",
			SUI.editor.i18n.htmleditor.tableDialog.txtLeftBorder);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;
		this.borderOptions(ctlTop2, "lblRightBorder", "selRightBorderStyle",
			"colRightBorder", "inpRightBorder", "selRightBorderUnit",
			SUI.editor.i18n.htmleditor.tableDialog.txtRightBorder);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;
		this.borderOptions(ctlTop2, "lblTopBorder", "selTopBorderStyle",
			"colTopBorder", "inpTopBorder", "selTopBorderUnit",
			SUI.editor.i18n.htmleditor.tableDialog.txtTopBorder);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;
		this.borderOptions(ctlTop2, "lblBottomBorder", "selBottomBorderStyle",
			"colBottomBorder", "inpBottomBorder", "selBottomBorderUnit",
			SUI.editor.i18n.htmleditor.tableDialog.txtBottomBorder);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;

		this.colColor = new SUI.editor.properties.Color({
			top: ctlTop2,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT,
			anchor: { right: true, left: true, top: true}
		});
		this.lblColor = new SUI.form.Label({
			top: ctlTop2,
			left: this.MARGIN + this.STYLES_LEFT,
			width: this.LABEL_WIDTH_STYLES - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtColor
		});

		this.clientPanel.add(this.lblColor);
		this.clientPanel.add(this.colColor);

		var w = this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT
			+ this.H_MARGIN + this.SEL_BORDER_STYLE + this.H_MARGIN
			+ this.COL_CTRL_WIDTH + this.NUM_INPUT_WIDTH + this.SEL_UNIT_WIDTH
			+ this.MARGIN;

		/**[ Advanced panels ]***************************************/

		ctlTop2 += this.CTRL_HEIGHT + this.MARGIN;
		var hw = Math.round(w/2);
		ctlTop = ctlTop2;

		this.dataCellPanel = new SUI.AnchorLayout({
			top: ctlTop,
			left: 0,
			height: this.ADVANCED_PANEL_HEIGHT,
			anchor: { left: true }
		});

		this.clientPanel.add(this.dataCellPanel);

		this.headerCellPanel = new SUI.AnchorLayout({
			top: ctlTop,
			left: 0,
			height: this.ADVANCED_PANEL_HEIGHT,
			anchor: { left: true }
		});

		this.clientPanel.add(this.headerCellPanel);

		/**[ Data cell ]***************************************/

		this.txtDataCell = new SUI.Box({
			top: 0 + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { left: true, right: true }
		});
		that.txtDataCell.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableCellDialog.txtDataCell + "</b>";
		this.dataCellPanel.add(this.txtDataCell);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selFreeHeaderCells = new SUI.form.SelectList({
			top: this.CTRL_HEIGHT + this.MARGIN + this.CTRL_HEIGHT
				+ this.MARGIN,
			left: this.MARGIN,
			right: hw + this.MARGIN,
			bottom: this.MARGIN,
			anchor: { top: true, left: true, right: true, bottom: true }
		});
		this.selFreeHeaderCells.el().multiple = "multiple";
		SUI.browser.addEventListener(this.selFreeHeaderCells.el(), "focus",
			this.ttOnFocusFreeHeaderCells);
		SUI.browser.addEventListener(this.selFreeHeaderCells.el(), "dblclick",
			this.ttTransferHeaderCells);

		this.lblFreeHeaderCells = new SUI.form.Label({
			top: this.CTRL_HEIGHT + this.MARGIN,
			left: this.MARGIN,
			right: hw + this.MARGIN,
			title:
				SUI.editor.i18n.htmleditor.tableCellDialog.txtFreeHeaderCells,
			anchor: { top: true, left: true, right: true },
			forBox: this.selFreeHeaderCells
		});

		this.selHeaderCells = new SUI.form.SelectList({
			top: this.CTRL_HEIGHT + this.MARGIN + this.CTRL_HEIGHT
				+ this.MARGIN,
			left: hw + this.MARGIN,
			right: this.MARGIN,
			bottom: this.MARGIN + this.MARGIN + this.BUTTON_HEIGHT,
			anchor: { top: true, left: true, right: true, bottom: true }
		});
		this.selHeaderCells.el().multiple = "multiple";
		SUI.browser.addEventListener(this.selHeaderCells.el(), "focus",
			this.ttOnFocusHeaderCells);
		SUI.browser.addEventListener(this.selHeaderCells.el(), "dblclick",
			this.ttTransferHeaderCells);

		this.lblHeaderCells = new SUI.form.Label({
			top: this.CTRL_HEIGHT + this.MARGIN,
			left: hw + this.MARGIN,
			right: this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtHeaderCells,
			anchor: { top: true, left: true, right: true },
			forBox: this.selHeaderCells
		});
		this.buttHeaderCells = new SUI.form.Button({
			left: hw + this.MARGIN,
			bottom: this.MARGIN,
			width: 100,
			height: this.BUTTON_HEIGHT,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtSelect,
			anchor: { left: true, bottom: true}
		});
		SUI.browser.addEventListener(this.buttHeaderCells.el(), "click",
			this.ttTransferHeaderCells);

		this.dataCellPanel.add(this.lblFreeHeaderCells);
		this.dataCellPanel.add(this.selFreeHeaderCells);

		this.dataCellPanel.add(this.lblHeaderCells);
		this.dataCellPanel.add(this.selHeaderCells);
		this.dataCellPanel.add(this.buttHeaderCells);

		this.setClientHeight(ctlTop2 + this.ADVANCED_PANEL_HEIGHT);
		this.setClientWidth(w);

		this.dataCellPanel.width(w);

		/**[ Header cell ]***************************************/

		this.txtHeaderCell = new SUI.Box({
			top: 0 + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { left: true, right: true }
		});
		that.txtHeaderCell.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableCellDialog.txtHeaderCell
			+ "</b>";
		this.headerCellPanel.add(this.txtHeaderCell);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selAxis = new SUI.form.SelectList({
			top: this.CTRL_HEIGHT + this.MARGIN + this.CTRL_HEIGHT
				+ this.MARGIN,
			left: this.MARGIN,
			right: hw + this.MARGIN,
			bottom: this.MARGIN + this.MARGIN + this.BUTTON_HEIGHT,
			anchor: { top: true, left: true, right: true, bottom: true }
		});
		this.selAxis.el().multiple = "multiple";
		this.lblAxis = new SUI.form.Label({
			top: this.CTRL_HEIGHT + this.MARGIN,
			left: this.MARGIN,
			width: this.LABEL_WIDTH_STYLES,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtAxis,
			forBox: this.selAxis
		});
		this.buttAxis = new SUI.form.Button({
			right: hw + this.MARGIN,
			bottom: this.MARGIN,
			width: 100,
			height: this.BUTTON_HEIGHT,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtAddAxis,
			anchor: { right: true, bottom: true}
		});
		SUI.browser.addEventListener(this.buttAxis.el(), "click",
			this.ttAddAxis);
		this.inpAxis = new SUI.form.Input({
			left: this.MARGIN,
			bottom: this.MARGIN + this.TEXT_OFFSET,
			right: hw + this.MARGIN + 100 + this.MARGIN,
			anchor: { left: true, right: true, bottom: true }
		});

		this.headerCellPanel.add(this.lblAxis);
		this.headerCellPanel.add(this.selAxis);
		this.headerCellPanel.add(this.inpAxis);
		this.headerCellPanel.add(this.buttAxis);

		this.inpAbbr = new SUI.form.Input({
			top: this.CTRL_HEIGHT + this.MARGIN,
			left: hw + this.MARGIN + this.LABEL_WIDTH_ABBR,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblAbbr = new SUI.form.Label({
			top: this.CTRL_HEIGHT + this.MARGIN,
			left: hw + this.MARGIN,
			width: this.LABEL_WIDTH_ABBR,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtAbbr,
			anchor: { left: true },
			forBox: this.inpAbbr
		});

		this.inpForceTd = new SUI.form.CheckBox({
			top: this.CTRL_HEIGHT + this.MARGIN + this.CTRL_HEIGHT
				+ this.MARGIN + this.RADIO_OFFSET,
			left: hw + this.MARGIN + this.RADIO_OFFSET,
			anchor: { left: true, top: true}
		});

		this.lblForceTd = new SUI.form.Label({
			top: this.CTRL_HEIGHT + this.MARGIN + this.CTRL_HEIGHT
				+ this.MARGIN,
			left: hw + this.MARGIN + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableCellDialog.txtForceTD,
			forBox: this.inpForceTd,
			anchor: { right: true, left: true, top: true}
		});

		this.headerCellPanel.add(this.lblAbbr);
		this.headerCellPanel.add(this.inpAbbr);

		this.headerCellPanel.add(this.lblForceTd);
		this.headerCellPanel.add(this.inpForceTd);

		this.setClientHeight(ctlTop2 + this.ADVANCED_PANEL_HEIGHT);
		this.setClientWidth(w);

		this.headerCellPanel.width(w);

		this.showAdvanced();

	},

	dataToForm: function() {

		this.data.ctype = this.isHeaderCell ? "header" : "data";

		var stylesOptions = [];
		for (var i=0; i<this.data.styles.scrivocell.length; i++) {
			stylesOptions.push({
				value: "scrivocell_" + this.data.styles.scrivocell[i],
				text:
					SUI.editor.i18n.htmleditor.tableCellDialog.txtStyleDataCell
					+ ": " + this.data.styles.scrivocell[i]
			});
		}
		for (var i=0; i<this.data.styles.scrivocellrow.length; i++) {
			stylesOptions.push({
				value: "scrivocellrow_" + this.data.styles.scrivocellrow[i],
				text:
					SUI.editor.i18n.htmleditor.tableCellDialog.txtStyleRowCell
					+ ": " + this.data.styles.scrivocellrow[i]
			});
		}
		for (var i=0; i<this.data.styles.scrivocellcol.length; i++) {
			stylesOptions.push({
				value: "scrivocellcol_" + this.data.styles.scrivocellcol[i],
				text:
					SUI.editor.i18n.htmleditor.tableCellDialog.txtStyleColCell
					+ ": " + this.data.styles.scrivocellcol[i]
			});
		}
		for (var i=0; i<this.data.styles.scrivocellcross.length; i++) {
			stylesOptions.push({
				value: "scrivocellcross_"
					+ this.data.styles.scrivocellcross[i],
				text:
				  SUI.editor.i18n.htmleditor.tableCellDialog.txtStyleCellCross
					+ ": " + this.data.styles.scrivocellcross[i]
			});
		}
		this.selStyles.options(stylesOptions);
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selStyles.el(), this.data.className);

		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selLeftBorderStyle.el(), this.data.leftborderstyle);
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selRightBorderStyle.el(), this.data.rightborderstyle);
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selTopBorderStyle.el(), this.data.topborderstyle);
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selBottomBorderStyle.el(), this.data.bottomborderstyle);

		this.colLeftBorder.setValue(this.data.leftbordercolor);
		this.colRightBorder.setValue(this.data.rightbordercolor);
		this.colTopBorder.setValue(this.data.topbordercolor);
		this.colBottomBorder.setValue(this.data.bottombordercolor);

		var t = SUI.editor.htmleditor.tableeditor.parseCssDim(
			this.data.leftborderwidth);
		this.inpLeftBorder.el().value = t.value;
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selLeftBorderUnit.el(), t.unit);
		t = SUI.editor.htmleditor.tableeditor.parseCssDim(
			this.data.rightborderwidth);
		this.inpRightBorder.el().value = t.value;
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selRightBorderUnit.el(), t.unit);
		t = SUI.editor.htmleditor.tableeditor.parseCssDim(
			this.data.topborderwidth);
		this.inpTopBorder.el().value = t.value;
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selTopBorderUnit.el(), t.unit);
		t = SUI.editor.htmleditor.tableeditor.parseCssDim(
			this.data.bottomborderwidth);
		this.inpBottomBorder.el().value = t.value;
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selBottomBorderUnit.el(), t.unit);

		this.colColor.setValue(this.data.bgcolor);

		this.selFreeHeaderCells.options(this.data.freeHeaders);
		this.selHeaderCells.options(this.data.selHeaders);

		var axs = [{
			value: "",
			text: SUI.editor.i18n.htmleditor.tableCellDialog.txtAxisNone
		}];
		for (var i in this.data.axes) {
			axs.push({ value: i, text: i });
		}
		this.selAxis.options(axs);
		this.selAxis.el().value = this.data.axis;

		this.inpAbbr.el().value = this.data.abbr;
		this.inpForceTd.el().checked = this.data.forcetd;
	},

	formToData: function() {

		this.data.className = this.selStyles.el().value;

		this.data.leftborderstyle = this.selLeftBorderStyle.el().value;
		this.data.rightborderstyle = this.selRightBorderStyle.el().value;
		this.data.topborderstyle = this.selTopBorderStyle.el().value;
		this.data.bottomborderstyle = this.selBottomBorderStyle.el().value;

		this.data.leftbordercolor = this.colLeftBorder.getValue();
		this.data.rightbordercolor = this.colRightBorder.getValue();
		this.data.topbordercolor = this.colTopBorder.getValue();
		this.data.bottombordercolor = this.colBottomBorder.getValue();

		this.data.leftborderwidth =
			this.inpLeftBorder.el().value + this.selLeftBorderUnit.el().value;
		this.data.rightborderwidth =
			this.inpRightBorder.el().value + this.selRightBorderUnit.el().value;
		this.data.topborderwidth =
			this.inpTopBorder.el().value + this.selTopBorderUnit.el().value;
		this.data.bottomborderwidth =
			this.inpBottomBorder.el().value + this.selBottomBorderUnit.el().value;

		this.data.bgcolor = this.colColor.getValue();

		this.data.selHeaders = [];
		for (var i=0; i<this.selHeaderCells.el().options.length; i++) {
			this.data.selHeaders.push(this.selHeaderCells.el().options[i].value);
		}
		this.data.axis = this.selAxis.el().value;

		this.data.abbr = this.inpAbbr.el().value;
		this.data.forcetd = this.inpForceTd.el().checked;

		this.close();
		return this.data;

	}

});
