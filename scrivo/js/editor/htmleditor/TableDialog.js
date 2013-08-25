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
 * $Id: TableDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.TableDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.TableDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.TableDialog.initializeBase(this, arg);

		this.data = arg.args;

		this.caption(this.data.newTable
			? SUI.editor.i18n.htmleditor.tableDialog.cptNewTable
			: SUI.editor.i18n.htmleditor.tableDialog.cptEditTable);

		this.populateForm();

		this.dataToForm();
	},

	STYLES_LEFT: 240,
	VERTICAL_SPLIT_WIDTH: 40,
	MARGIN: 8,
	H_MARGIN: 16,
	CTRL_HEIGHT: 20,
	TEXT_WIDTH: 160,
	LABEL_WIDTH: 90,
	LABEL_WIDTH_STYLES: 120,
	LABEL_WIDTH_SIZE: 120,
	RADIO_WIDTH: 26,
	RADIO_OFFSET: 3,
	ROW_SPACING_SMALL: 2,
	ROW_SPACING_LARGE: 12,
	NUM_INPUT_WIDTH: 30,
	TEXT_OFFSET: 2,
	SEL_UNIT_WIDTH: 50,
	SEL_BORDER_STYLE: 70,
	COL_CTRL_WIDTH: 100,
	SUMMARY_HEIGHT: 50,

	headerOptions: function(t, lbl, inp, icon, txt, val) {
		this[inp] = new SUI.form.RadioButton({
			top: t + this.RADIO_OFFSET,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "headeroptions",
			anchor: { left: true, top: true}
		});
		this[inp].el().value = val;

		this[lbl] = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			width: this.STYLES_LEFT - this.MARGIN - this.RADIO_WIDTH,
			title: "<img style=\"vertical-align: bottom\" src=\""
				+ (SUI.imgDir + "/" + icon) + "\"> " + txt,
			forBox: this[inp],
			anchor: { left: true, top: true}
		});

		this.clientPanel.add(this[lbl]);
		this.clientPanel.add(this[inp]);
	},

	/**
	* Copy of the one in TableCellDialog
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

		this.txtStyles = new SUI.Box({
			top: ctlTop + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT
		});
		that.txtStyles.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableDialog.txtStandardStyles
			+ "</b>";
		this.clientPanel.add(this.txtStyles);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selStyles = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN,
			width: this.STYLES_LEFT - this.VERTICAL_SPLIT_WIDTH
		});
		this.clientPanel.add(this.selStyles);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.txtHeaders = new SUI.Box({
			top: ctlTop + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT
		});
		that.txtHeaders.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableDialog.txtTableHeaders
			+ "</b>";
		this.clientPanel.add(this.txtHeaders);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		this.headerOptions(ctlTop, "lblHeadersColumn", "inpHeadersColumn",
			SUI.editor.resource.htmleditor.icnHeadersColumn,
			SUI.editor.i18n.htmleditor.tableDialog.txtHeadersColumn, "col");

		ctlTop += this.CTRL_HEIGHT + this.ROW_SPACING_SMALL;
		this.headerOptions(
			ctlTop, "lblHeadersRowColumn", "inpHeadersRowColumn",
			SUI.editor.resource.htmleditor.icnHeadersRowColumn,
			SUI.editor.i18n.htmleditor.tableDialog.txtHeadersRowColumn,
			"rowcol");

		ctlTop += this.CTRL_HEIGHT + this.ROW_SPACING_SMALL;
		this.headerOptions(ctlTop, "lblHeadersRow", "inpHeadersRow",
			SUI.editor.resource.htmleditor.icnHeadersRow,
			SUI.editor.i18n.htmleditor.tableDialog.txtHeadersRow, "row");

		ctlTop += this.CTRL_HEIGHT + this.ROW_SPACING_SMALL;
		this.headerOptions(ctlTop, "lblHeadersNone", "inpHeadersNone",
			SUI.editor.resource.htmleditor.icnHeadersNone,
			SUI.editor.i18n.htmleditor.tableDialog.txtHeadersNone, "none");

		if (!this.data.newTable) {

			ctlTop += this.CTRL_HEIGHT + this.ROW_SPACING_SMALL;

			this.inpHeadersKeep = new SUI.form.RadioButton({
				top: ctlTop + this.RADIO_OFFSET,
				left: this.MARGIN + this.RADIO_OFFSET,
				name: "headeroptions",
				anchor: { left: true, top: true}
			});
			this.inpHeadersKeep.el().value = "keep";

			this.lblHeadersKeep = new SUI.form.Label({
				top: ctlTop,
				left: this.MARGIN  + this.RADIO_WIDTH,
				width: this.STYLES_LEFT - this.MARGIN - this.RADIO_WIDTH,
				title: SUI.editor.i18n.htmleditor.tableDialog.txtHeadersKeep,
				forBox: this.inpHeadersKeep,
				anchor: { left: true, top: true}
			});

			this.clientPanel.add(this.lblHeadersKeep);
			this.clientPanel.add(this.inpHeadersKeep);

		} else {

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;

			this.txtSize = new SUI.Box({
				top: ctlTop + this.TEXT_OFFSET,
				left: this.MARGIN,
				width: this.TEXT_WIDTH,
				height: this.CTRL_HEIGHT
			});
			that.txtSize.el().innerHTML = "<b>"
				+ SUI.editor.i18n.htmleditor.tableDialog.txtTableSize
				+ "</b>";
			this.clientPanel.add(this.txtSize);

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;

			this.inpRows = new SUI.form.Input({
				top: ctlTop,
				left: this.MARGIN + this.LABEL_WIDTH_SIZE,
				width: this.NUM_INPUT_WIDTH
			});
			this.lblRows = new SUI.form.Label({
				top: ctlTop,
				left: this.MARGIN,
				width: this.LABEL_WIDTH_SIZE- this.MARGIN,
				title: SUI.editor.i18n.htmleditor.tableDialog.txtRows,
				forBox: this.inpRows
			});

			this.clientPanel.add(this.lblRows);
			this.clientPanel.add(this.inpRows);

			ctlTop += this.CTRL_HEIGHT + this.MARGIN;

			this.inpColumns = new SUI.form.Input({
				top: ctlTop,
				left: this.MARGIN + this.LABEL_WIDTH_SIZE,
				width: this.NUM_INPUT_WIDTH
			});
			this.lblColumns = new SUI.form.Label({
				top: ctlTop,
				left: this.MARGIN,
				width: this.LABEL_WIDTH_SIZE - this.MARGIN,
				title: SUI.editor.i18n.htmleditor.tableDialog.txtColums,
				forBox: this.inpColumns
			});

			this.clientPanel.add(this.lblColumns);
			this.clientPanel.add(this.inpColumns);
		}

		/**[ Styles ]***************************************/

		var ctlTop2 = this.MARGIN;

		this.txtStyles = new SUI.Box({
			top: ctlTop2 + this.TEXT_OFFSET,
			left: this.MARGIN + this.STYLES_LEFT,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT
		});
		that.txtStyles.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableDialog.txtStyles
			+ "</b>";
		this.clientPanel.add(this.txtStyles);

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
			title: SUI.editor.i18n.htmleditor.tableDialog.txtColor
		});

		this.clientPanel.add(this.lblColor);
		this.clientPanel.add(this.colColor);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;

		this.inpSpacing = new SUI.form.Input({
			top: ctlTop2,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT,
			width: this.NUM_INPUT_WIDTH
		});
		this.lblSpacing = new SUI.form.Label({
			top: ctlTop2,
			left: this.MARGIN + this.STYLES_LEFT,
			width: this.LABEL_WIDTH_STYLES - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableDialog.txtCelSpacing,
			forBox: this.inpSpacing
		});

		this.clientPanel.add(this.lblSpacing);
		this.clientPanel.add(this.inpSpacing);

		ctlTop2 += this.CTRL_HEIGHT + this.ROW_SPACING_LARGE;

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
		this.inpWidth = new SUI.form.Input({
			top: ctlTop2,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT,
			width: this.NUM_INPUT_WIDTH
		});
		this.selWidthUnit = new SUI.form.SelectList({
			top: ctlTop2,
			left: this.MARGIN + this.LABEL_WIDTH_STYLES + this.STYLES_LEFT
				+ this.NUM_INPUT_WIDTH,
			width: this.SEL_UNIT_WIDTH,
			options: cu
		});
		this.lblWidth = new SUI.form.Label({
			top: ctlTop2,
			left: this.MARGIN + this.STYLES_LEFT,
			width: this.LABEL_WIDTH_STYLES - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableDialog.txtWidth,
			forBox: this.inpWidth
		});

		this.clientPanel.add(this.lblWidth);
		this.clientPanel.add(this.inpWidth);
		this.clientPanel.add(this.selWidthUnit);

		/**[ Information ]***************************************/

		if (ctlTop2 > ctlTop) {
			ctlTop = ctlTop2;
		}

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.txtInformation = new SUI.Box({
			top: ctlTop + this.TEXT_OFFSET,
			left: this.MARGIN,
			width: this.TEXT_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { left: true, right: true }
		});
		that.txtInformation.el().innerHTML = "<b>"
			+ SUI.editor.i18n.htmleditor.tableDialog.txtInformation
			+ "</b>";
		this.clientPanel.add(this.txtInformation);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpCaption = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblCaption = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableDialog.txtCaption,
			forBox: this.inpCaption
		});

		this.clientPanel.add(this.lblCaption);
		this.clientPanel.add(this.inpCaption);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpSummary = new SUI.form.TextArea({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			height: this.SUMMARY_HEIGHT,
			anchor: { left: true, right: true }
		});
		this.lblSummary = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.tableDialog.txtSummary,
			forBox: this.inpSummary
		});

		this.clientPanel.add(this.lblSummary);
		this.clientPanel.add(this.inpSummary);

		ctlTop += this.SUMMARY_HEIGHT + this.MARGIN;

		this.setClientHeight(ctlTop);
		this.setClientWidth(this.MARGIN + this.LABEL_WIDTH_STYLES
			+ this.STYLES_LEFT + this.H_MARGIN + this.SEL_BORDER_STYLE
			+ this.H_MARGIN + this.COL_CTRL_WIDTH + this.NUM_INPUT_WIDTH
			+ this.SEL_UNIT_WIDTH + this.MARGIN);

	},

	dataToForm: function() {

		var stylesOptions = [{value: "keep_styles",
			text: SUI.editor.i18n.htmleditor.tableDialog.txtKeepStyles}];
		for (var i=0; i<this.data.styles.scrivotable.length; i++) {
			stylesOptions.push({
				value: "scrivotable_" + this.data.styles.scrivotable[i],
				text: /*label ??*/ "" + this.data.styles.scrivotable[i]
			});
		}
		this.selStyles.options(stylesOptions);
		SUI.editor.htmleditor.tableeditor.setSelOption(
			this.selStyles.el(), this.data.className);

		if (this.data.newTable) {
			this.inpHeadersColumn.el().checked = true;
			this.inpRows.el().value = this.data.numrows;
			this.inpColumns.el().value = this.data.numcols;
		} else {
			this.inpHeadersKeep.el().checked = true;
		}

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
		this.inpSpacing.el().value = this.data.spacing;
		t = SUI.editor.htmleditor.tableeditor.parseCssDim(
			this.data.tablewidth);
		this.inpWidth.el().value = t.value;
		this.selWidthUnit.el().value = t.unit;

		this.inpSummary.el().value = this.data.summary;
		this.inpCaption.el().value = this.data.caption;
	},

	formToData: function() {

		this.data.className = this.selStyles.el().value;

		if (this.inpHeadersColumn.el().checked) {
			this.data.headers = "col";
		} else if (this.inpHeadersRowColumn.el().checked) {
			this.data.headers = "rowcol";
		} else if (this.inpHeadersRow.el().checked) {
			this.data.headers = "row";
		} else if (this.inpHeadersNone.el().checked) {
			this.data.headers = "none";
		} else {
			this.data.headers = "keep";
		}

		if (this.data.newTable) {
			this.data.numrows = this.inpRows.el().value;
			this.data.numcols = this.inpColumns.el().value;
		}

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
		this.data.spacing = this.inpSpacing.el().value;
		this.data.tablewidth =
			this.inpWidth.el().value + this.selWidthUnit.el().value;

		this.data.summary = this.inpSummary.el().value;
		this.data.caption = this.inpCaption.el().value;

		this.close();

		return this.data;
	}

});
