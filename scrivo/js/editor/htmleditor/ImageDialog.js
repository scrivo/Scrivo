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
 * $Id: ImageDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.ImageDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.ImageDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.ImageDialog.initializeBase(this, arg);

		this.data = {
			assetId: "",
			src: "",
			alt: "",
			title: "",
			pageId: "",
			longdesc: "",
			align: "",
			borderWidth: "",
			width: "",
			height: "",
			marginLeft: "",
			marginTop: "",
			marginRight: "",
			marginBottom: ""
		};

		for (var i in this.data) {
			if (arg.attr && arg.attr[i]) {
				if (i == "assetId") {
					if (arg.attr.assetId != "") {
						this.data.assetId = arg.attr.assetId;
						this.data.src =
							SUI.editor.resource.assetLink +
							this.data.assetId;
					}
				} else if (i == "src") {
					if (this.data.assetId == "") {
						this.data.src = arg.attr.src;
						var tmp = this.data.src.substr(0,
							SUI.editor.resource.assetLink.length);
						if (SUI.editor.resource.assetLink === tmp) {
						this.data.assetId =
							parseInt(this.data.src.substr(
									SUI.editor.resource.assetLink.length), 10);
					}
					}
				} else if (i == "pageId") {
					if (arg.attr.pageId != "") {
						this.data.pageId = arg.attr.pageId;
						this.data.longdesc =
							SUI.editor.resource.internalLink +
							this.data.pageId;
					}
				} else if (i == "longdesc") {
					if (this.data.pageId == "") {
						this.data.longdesc = arg.attr.longdesc;
						this.data.pageId =
							parseInt(this.data.longdesc.substr(
							SUI.editor.resource.internalLink.length),10);
					}
				} else {
					this.data[i] = arg.attr[i];
				}
			}
		}

		this.caption(this.data.assetId
			? SUI.editor.i18n.htmleditor.imageDialog.cptEditImage
			: SUI.editor.i18n.htmleditor.imageDialog.cptNewImage);

		this.populateForm();

		this.dataToForm();
	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 150,

	SECT_HORIZ_MARGIN: 40,
	SECT_VERT_MARGIN: 26,

	LAYOUT_LABEL_WIDTH: 100,
	LAYOUT_SEL_WIDTH: 65,
	LAYOUT_CTRL_WIDTH: 24,

	DIM_LABEL_WIDTH: 65,
	DIM_CTRL_WIDTH: 36,

	MARGINS_LABEL_WIDTH: 100,
	MARGINS_CTRL_WIDTH: 24,

	SELECTBUTTON_WIDTH: 75,
	SELECTBUTTON_HEIGHT: 20,

	populateForm: function() {
		/*-[src]----------------------------------*/
		var ctlTop = this.MARGIN;
		var that = this;

		this.txtSrc = new SUI.Box({
			top: ctlTop + 2,
			left: this.MARGIN + this.LABEL_WIDTH + this.SELECTBUTTON_WIDTH
				+ this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});
		this.butSrc = new SUI.form.Button({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: this.SELECTBUTTON_WIDTH,
			top: ctlTop,
			height: this.SELECTBUTTON_HEIGHT,
				title: SUI.editor.i18n.htmleditor.imageDialog.txtSelect
		});
		this.butSrc.span.style.top = "-2px";
		SUI.browser.addEventListener(this.butSrc.el(), "click", function(e) {
			if (!that.ehSelectSrc(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});
		this.lblSrc = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtSrc,
			forBox: this.butSrc
		});

		/*-[alt]----------------------------------*/
		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpAlt = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblAlt = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtAlt,
			forBox: this.inpAlt
		});

		/*-[title]--------------------------------*/
		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpTitle = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.lblTitle = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtTitle,
			forBox: this.inpTitle
		});

		/*-[longdesc]-----------------------------*/
		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.txtLongdesc = new SUI.Box({
			top: ctlTop + 2,
			left: this.MARGIN + this.LABEL_WIDTH + this.SELECTBUTTON_WIDTH +
				this.MARGIN + this.SELECTBUTTON_WIDTH + this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});
		this.butLongdesc = new SUI.form.Button({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: this.SELECTBUTTON_WIDTH,
			top: ctlTop,
			height: this.SELECTBUTTON_HEIGHT,
				title: SUI.editor.i18n.htmleditor.imageDialog.txtSelect
		});
		this.butLongdesc.span.style.top = "-2px";
		SUI.browser.addEventListener(this.butLongdesc.el(), "click",
			function(e) {
				if (!that.ehSelectLongdesc(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);
		this.butClearLongdesc = new SUI.form.Button({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH + this.SELECTBUTTON_WIDTH +
				this.MARGIN,
			width: this.SELECTBUTTON_WIDTH,
			top: ctlTop,
			height: this.SELECTBUTTON_HEIGHT,
				title: SUI.editor.i18n.htmleditor.imageDialog.txtClear
		});
		this.butClearLongdesc.span.style.top = "-2px";
		SUI.browser.addEventListener(this.butClearLongdesc.el(), "click",
			function(e) {
				if (!that.ehClearLongdesc(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);
		this.lblLongdesc = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtLongdesc,
			forBox: this.butLongdesc
		});

		/*-[layOut]-------------------------------*/
		ctlTop += this.CTRL_HEIGHT + this.SECT_VERT_MARGIN;
		var saveCtlTop = ctlTop;
		var left = this.MARGIN;

		this.txtLayout = new SUI.Box({
			top: ctlTop + 2,
			left: left,
			width: this.LAYOUT_LABEL_WIDTH+this.LAYOUT_SEL_WIDTH,
			height: this.CTRL_HEIGHT
		});
		this.txtLayout.el().innerHTML =
			"<b>"+SUI.editor.i18n.htmleditor.imageDialog.txtLayout+"</b>";

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selAlign = new SUI.form.SelectList({
			top: ctlTop,
			left: left + this.LAYOUT_LABEL_WIDTH,
			width: this.LAYOUT_SEL_WIDTH,
			options: [{
				value: "",
				text: SUI.editor.i18n.htmleditor.imageDialog.txtAlignOptions[0]
			},{
				value: "left",
				text: SUI.editor.i18n.htmleditor.imageDialog.txtAlignOptions[1]
			},{
				value: "right",
				text: SUI.editor.i18n.htmleditor.imageDialog.txtAlignOptions[2]
			}]
		});
		this.lblAlign = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.LAYOUT_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtAlign,
			forBox: this.selAlign
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpBorder = new SUI.form.Input({
			top: ctlTop,
			left: left + this.LAYOUT_LABEL_WIDTH,
			width: this.LAYOUT_CTRL_WIDTH
		});
		this.lblBorder = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.LAYOUT_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtBorder,
			forBox: this.inpBorder
		});

		/*-[dimensions]---------------------------*/
		ctlTop = saveCtlTop;
		left = left + this.LAYOUT_LABEL_WIDTH + this.LAYOUT_SEL_WIDTH +
			this.SECT_HORIZ_MARGIN;

		this.txtDimensions = new SUI.Box({
			top: ctlTop + 2,
			left: left,
			width: left,
			height: this.CTRL_HEIGHT
		});
		this.txtDimensions.el().innerHTML =
			"<b>"+SUI.editor.i18n.htmleditor.imageDialog.txtDimensions+"</b>";

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpWidth = new SUI.form.Input({
			top: ctlTop,
			left: left + this.DIM_LABEL_WIDTH,
			width: this.DIM_CTRL_WIDTH
		});
		this.lblWidth = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.DIM_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtWidth,
			forBox: this.inpWidth
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpHeight = new SUI.form.Input({
			top: ctlTop,
			left: left + this.DIM_LABEL_WIDTH,
			width: this.DIM_CTRL_WIDTH
		});
		this.lblHeight = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.DIM_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtHeight,
			forBox: this.inpHeight
		});

		/*-[margins]------------------------------*/
		ctlTop = saveCtlTop;
		left = left + this.DIM_CTRL_WIDTH + this.DIM_LABEL_WIDTH +
			this.SECT_HORIZ_MARGIN;

		this.txtMargins = new SUI.Box({
			top: ctlTop + 2,
			left: left,
			width: this.MARGINS_LABEL_WIDTH + this.MARGINS_CTRL_WIDTH +
				this.MARGIN + this.MARGINS_CTRL_WIDTH,
			height: this.CTRL_HEIGHT
		});
		this.txtMargins.el().innerHTML =
			"<b>"+SUI.editor.i18n.htmleditor.imageDialog.txtMargins+"</b>";

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpLeft = new SUI.form.Input({
			top: ctlTop,
			left: left + this.MARGINS_LABEL_WIDTH,
			width: this.MARGINS_CTRL_WIDTH
		});
		this.inpRight = new SUI.form.Input({
			top: ctlTop,
			left: left + this.MARGINS_LABEL_WIDTH + this.MARGINS_CTRL_WIDTH +
				this.MARGIN,
			width: this.MARGINS_CTRL_WIDTH
		});
		this.lblLeftRight = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.MARGINS_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtLeftRight,
			forBox: this.inpLeft
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpTop = new SUI.form.Input({
			top: ctlTop,
			left: left + this.MARGINS_LABEL_WIDTH,
			width: this.MARGINS_CTRL_WIDTH
		});
		this.inpBottom = new SUI.form.Input({
			top: ctlTop,
			left: left + this.MARGINS_LABEL_WIDTH + this.MARGINS_CTRL_WIDTH +
				this.MARGIN,
			width: this.MARGINS_CTRL_WIDTH
		});
		this.lblTopBottom = new SUI.form.Label({
			top: ctlTop,
			left: left,
			width: this.MARGINS_LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.imageDialog.txtTopBottom,
			forBox: this.inpTop
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;
		left = left + this.MARGINS_LABEL_WIDTH + this.MARGINS_CTRL_WIDTH +
			this.MARGIN + this.MARGINS_CTRL_WIDTH + this.MARGIN;

		this.setClientHeight(ctlTop);
		this.setClientWidth(left);

		this.clientPanel.add(this.lblSrc);
		this.clientPanel.add(this.butSrc);
		this.clientPanel.add(this.txtSrc);

		this.clientPanel.add(this.lblAlt);
		this.clientPanel.add(this.inpAlt);

		this.clientPanel.add(this.lblTitle);
		this.clientPanel.add(this.inpTitle);

		this.clientPanel.add(this.lblLongdesc);
		this.clientPanel.add(this.butLongdesc);
		this.clientPanel.add(this.butClearLongdesc);
		this.clientPanel.add(this.txtLongdesc);

		this.clientPanel.add(this.txtLayout);
		this.clientPanel.add(this.lblAlign);
		this.clientPanel.add(this.selAlign);
		this.clientPanel.add(this.lblBorder);
		this.clientPanel.add(this.inpBorder);

		this.clientPanel.add(this.txtDimensions);
		this.clientPanel.add(this.lblWidth);
		this.clientPanel.add(this.inpWidth);
		this.clientPanel.add(this.lblHeight);
		this.clientPanel.add(this.inpHeight);

		this.clientPanel.add(this.txtMargins);
		this.clientPanel.add(this.lblLeftRight);
		this.clientPanel.add(this.inpLeft);
		this.clientPanel.add(this.inpRight);

		this.clientPanel.add(this.txtMargins);
		this.clientPanel.add(this.lblTopBottom);
		this.clientPanel.add(this.inpTop);
		this.clientPanel.add(this.inpBottom);
	},

	ehSelectSrc: function(e) {
		var that = this;
		var fdlg = new SUI.editor.filedialog.FileDialog({
			assetId: this.data.assetId,
			onOK: function(assetId) {
				that.setSrc(assetId);
			}
		});
		fdlg.show();
	},

	setSrc: function(assetId) {
		var that = this;
		if (!assetId.type) {
			this.data.assetId = assetId;
			this.data.src = SUI.editor.resource.assetLink+assetId;
		} else {
			if (assetId.type == "assetId") {
				this.data.assetId = assetId.value;
				this.data.src = SUI.editor.resource.assetLink+assetId.value;
			} else {
				this.data.assetId = 0;
				this.data.src = assetId.value;
			}
		}
		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
			a: "displayURL",
			url: this.data.src
		}, function(e) {
			if (e.result=="OK") {
				that.txtSrc.el().innerHTML = e.data.url;
			}
		});
	},

	ehSelectLongdesc: function(e) {
		var that = this;
		var d = new SUI.editor.htmleditor.PageDialog({
			pageId: this.data.pageId,
			onOK: function(p) {
				that.setLongdesc(p.id);
			}
		});
		d.center();
		d.show();
	},

	setLongdesc: function(pageId) {
		var that = this;
		this.data.pageId = pageId;
		this.data.longdesc = SUI.editor.resource.internalLink+pageId;
		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
			a: "displayURL",
			url: this.data.longdesc
		}, function(e) {
			if (e.result=="OK") {
				that.txtLongdesc.el().innerHTML = e.data.url;
			}
		});
	},

	ehClearLongdesc: function(e) {
		this.data.pageId = "";
		this.data.longdesc = "";
		this.txtLongdesc.el().innerHTML = "";
	},

	dataToForm: function() {

		this.inpAlt.el().value = this.data.alt;
		this.inpTitle.el().value = this.data.title;
		this.selAlign.el().value = this.data.align;
		if (this.selAlign.el().value != this.data.align) {
			this.selAlign.el().options[this.selAlign.el().options.length] =
				new Option(this.data.align, this.data.align, false, true);
		}
		this.inpBorder.el().value = this.data.borderWidth;
		this.inpWidth.el().value = this.data.width;
		this.inpHeight.el().value = this.data.height;
		this.inpLeft.el().value = this.data.marginLeft;
		this.inpRight.el().value = this.data.marginRight;
		this.inpTop.el().value = this.data.marginTop;
		this.inpBottom.el().value = this.data.marginBottom;

		if (this.data.pageId != "") {
			this.setLongdesc(this.data.pageId);
		}
		if (this.data.assetId != "") {
			this.setSrc(this.data.assetId);
		}
	},

	formToData: function() {

		this.data.alt = this.inpAlt.el().value;
		this.data.title = this.inpTitle.el().value;
		this.data.align = this.selAlign.el().value;
		this.data.align = this.selAlign.el().value;
		this.data.borderWidth = this.inpBorder.el().value;
		this.data.width = this.inpWidth.el().value;
		this.data.height = this.inpHeight.el().value;
		this.data.marginLeft = this.inpLeft.el().value;
		this.data.marginRight = this.inpRight.el().value;
		this.data.marginTop = this.inpTop.el().value;
		this.data.marginBottom = this.inpBottom.el().value;

		this.close();

		return this.data;
	}
});
