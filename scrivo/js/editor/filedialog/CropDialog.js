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
 * $Id: CropDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.CropDialog = SUI.defineClass({

	initializer: function(arg) {

		var that = this;

		this.image = arg.image;
		this.imageHeight = parseInt(arg.imageHeight, 10);
		this.imageWidth = parseInt(arg.imageWidth, 10);

		if (arg.onOK) this.onOK = arg.onOK;
		this.targetWidth = arg.targetWidth || "";
		this.targetHeight = arg.targetHeight || "";

		this.window = new SUI.Window({
			height: 400,
			width: 600,
			resizable: false
		});

		this.window.caption(SUI.editor.i18n.filedialog.cropDialog.caption);

		this.splitLayoutv = new SUI.SplitLayout({
			east: {
				width: this.PROP_WIDTH
			}
		});

		this.splitLayout = new SUI.SplitLayout({
			north: {
				height: this.PROP_HEIGHT
			}
		});

		this.imgPanel = new SUI.Panel({
			padding: new SUI.Padding(this.PANEL_PADDING),
			innerBorder: new SUI.Border(this.PANEL_BORDER),
			anchor: { right: true, left: true, top: true }
		});

		this.clientPanel = new SUI.Panel({
			padding: new SUI.Padding(this.PANEL_PADDING),
			innerBorder: new SUI.Border(this.PANEL_BORDER)
		});

		this.buttonPanel = new SUI.Panel({
			color: "transparent"
		});

		this.cancelButton = new SUI.form.Button({
			bottom: 4,
			right: 4,
			width: 100,
			anchor: { right: true, bottom: true },
			title: SUI.i18n.cancel
		});

		this.okButton = new SUI.form.Button({
			bottom: 4 + this.BUTTON_HEIGHT + 8,
			right: 4,
			width: 100,
			anchor: { right: true, bottom: true },
			title: SUI.i18n.ok
		});

		this.window.add(this.splitLayoutv);
		this.splitLayoutv.add(this.splitLayout, "east");

		this.splitLayout.add(this.clientPanel, "north");
		this.splitLayout.add(this.buttonPanel, "center");

		this.buttonPanel.add(this.okButton);
		this.buttonPanel.add(this.cancelButton);

		this.splitLayoutv.add(this.imgPanel, "center");

		var t = this.PROP_LINE_SPACING;

		var lblWidth = new SUI.form.Label({
			top: t,
			left: this.PROP_MARGIN,
			width: 100,
			title: SUI.editor.i18n.filedialog.cropDialog.width
		});

		t += this.PROP_LINE_HEIGHT + this.PROP_LINE_SPACING;

		this.inpWidth = new SUI.form.Input({
			top: t,
			right: this.STR_PIXELS_WIDTH+this.PROP_MARGIN+this.PROP_MARGIN,
			left: this.PROP_MARGIN,
			anchor: { right: true, left: true, top: true }
		});
		this.inpWidth.el().value = this.targetWidth;

		var lblPx1 = new SUI.form.Label({
			top: t,
			right: this.PROP_MARGIN,
			width: this.STR_PIXELS_WIDTH,
			title: SUI.editor.i18n.filedialog.cropDialog.pixels,
			anchor: { right: true, top: true }
		});

		lblWidth.forBox(this.inpWidth);

		this.clientPanel.add(lblWidth);
		this.clientPanel.add(this.inpWidth);
		this.clientPanel.add(lblPx1);

		t += this.PROP_LINE_HEIGHT + this.PROP_LINE_SPACING;

		var lblHeight = new SUI.form.Label({
			top: t,
			left: this.PROP_MARGIN,
			width: 100,
			title: SUI.editor.i18n.filedialog.cropDialog.height
		});

		t += this.PROP_LINE_HEIGHT + this.PROP_LINE_SPACING;

		this.inpHeight = new SUI.form.Input({
			top: t,
			right: this.STR_PIXELS_WIDTH+this.PROP_MARGIN+this.PROP_MARGIN,
			left: this.PROP_MARGIN,
			anchor: { right: true, left: true, top: true }
		});
		this.inpHeight.el().value = this.targetHeight;

		var lblPx2 = new SUI.form.Label({
			top: t,
			right: this.PROP_MARGIN,
			width: this.STR_PIXELS_WIDTH,
			title: SUI.editor.i18n.filedialog.cropDialog.pixels,
			anchor: { right: true, top: true }
		});

		lblHeight.forBox(this.inpHeight);

		this.clientPanel.add(lblHeight);
		this.clientPanel.add(this.inpHeight);
		this.clientPanel.add(lblPx2);

		t += this.PROP_LINE_HEIGHT + this.PROP_LINE_SPACING +
			this.PROP_LINE_SPACING;

		this.setButton = new SUI.form.Button({
			right: this.PROP_MARGIN,
			left: this.PROP_MARGIN,
			top: t,
			title: SUI.editor.i18n.filedialog.cropDialog.set,
			anchor: { right: true, top: true, left: true  }
		});

		this.clientPanel.add(this.setButton);

		var ws = this.window.clientAreaPosition();
		var innerH = this.PROP_HEIGHT + 2*this.BUTTON_HEIGHT + 4 + 8 + 8;
		var h1 = ws.top + ws.bottom + innerH;
		var h2 = this.imageHeight + ws.top + ws.bottom +
			2*(this.PANEL_PADDING + this.PANEL_BORDER);
		var w = this.imageWidth + ws.left + ws.right + this.PROP_WIDTH +
			2*(this.PANEL_PADDING + this.PANEL_BORDER);

		this.window.height(h1 > h2 ? h1 : h2);
		this.window.width(w);
		this.imgPanel.height(this.imageHeight +
			2*(this.PANEL_PADDING + this.PANEL_BORDER));

		this.window.center();

		this.cropper = new SUI.control.ImageCropper({
			image: this.image,
			width: this.imageWidth,
			height: this.imageHeight
		});

		this.cropper.targetSize(
		 this.inpWidth.el().value, this.inpHeight.el().value);

		this.imgPanel.add(this.cropper);
		this.imgPanel.el().style.overflow = "visible";
		this.imgPanel.inner.el().style.overflow = "visible";

		this.window.onEsc = function() {
			that.window.close();
			that.onCancel();
		};
		this.window.onEnter = function() {
			that.onOK();
		};

		SUI.browser.addEventListener(this.setButton.el(), "click", function(e) {
			if (!that.ehSet(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

		SUI.browser.addEventListener(this.okButton.el(), "click", function(e) {
			if (!that.ehOK(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

		SUI.browser.addEventListener(this.cancelButton.el(), "click", function(e) {
			if (!that.ehCancel(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});
	},

	PROP_WIDTH: 128,
	PROP_HEIGHT: 148,
	PANEL_PADDING: 4,
	PANEL_BORDER: 1,
	PROP_MARGIN: 8,
	STR_PIXELS_WIDTH: 40,
	PROP_LINE_HEIGHT: 20,
	PROP_LINE_SPACING: 4,
	BUTTON_HEIGHT: 24,

	ehSet: function(e) {
		this.cropper.targetSize(
		 this.inpWidth.el().value, this.inpHeight.el().value);
	},

	onOK: function(d) {},
	ehOK: function(e) {
		var r = this.cropper.data();
		if(r) {
			this.window.close();
			this.onOK(r);
		} else {
			new SUI.dialog.Alert({
				text: SUI.editor.i18n.filedialog.cropDialog.error
			}).show();
		}
	},

	onCancel: function() {},
	ehCancel: function(e) {
		this.window.close();
		this.onCancel();
	},

	show: function() {
		this.window.show();
	}

});
