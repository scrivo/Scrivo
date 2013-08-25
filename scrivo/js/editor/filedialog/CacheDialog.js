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
 * $Id: CacheDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.CacheDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.filedialog.CacheDialog.initializeBase(
			this, arg);
		var that = this;

		if (arg.assetId) {
			this.assetId = arg.assetId;
		}

		this.width(this.WIDTH);
		this.caption(
			SUI.editor.i18n.filedialog.cacheDialog.captionCache);

		var t = this.MARGIN;

		this.inpNormal = new SUI.form.RadioButton({
			top: t + this.RADIO_OFFSET,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "cache",
			anchor: { left: true, top: true}
		});
		this.lblNormal = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.filedialog.cacheDialog.cacheNormal,
			height: this.LINE_HEIGHT,
			forBox: this.inpNormal,
			anchor: { right: true, left: true, top: true}
		});
		this.clientPanel.add(this.inpNormal);
		this.clientPanel.add(this.lblNormal);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.inpExpires = new SUI.form.RadioButton({
			top: t + this.RADIO_OFFSET,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "cache",
			anchor: { left: true, top: true}
		});
		var lb = SUI.editor.i18n.filedialog.cacheDialog.cacheExpires.split("[]");
		this.lblExpires = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: lb[0],
			height: this.LINE_HEIGHT,
			forBox: this.inpExpires,
			anchor: { right: true, left: true, top: true}
		});

		this.inpTime = new SUI.form.Input({
			maxLength: 4
		});
		this.inpTime.el().style.position = "relative";
		this.inpTime.el().size= 2;
		var opts = [];
		var vals = ["MINUTE", "HOUR", "DAY", "WEEK", "MONTH", "YEAR"];
		var mx = SUI.editor.i18n.filedialog.cacheDialog.cacheTimes.length;
		for (var i=0; i<mx; i++) {
			opts.push({
				value: vals[i],
				text: SUI.editor.i18n.filedialog.cacheDialog.cacheTimes[i]
			});
		}
		this.selTimeUnit = new SUI.form.SelectList({
			anchor: { left: true, top: true},
			options: opts
		});
		this.selTimeUnit.el().style.position = "relative";

		this.lblExpires.el().appendChild(this.inpTime.el());
		this.lblExpires.el().appendChild(this.selTimeUnit.el());
		this.lblExpires.el().appendChild(document.createTextNode(lb[1]));

		this.clientPanel.add(this.inpExpires);
		this.clientPanel.add(this.lblExpires);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.inpNone = new SUI.form.RadioButton({
			top: t + this.RADIO_OFFSET,
			left: this.MARGIN + this.RADIO_OFFSET,
			name: "cache",
			anchor: { left: true, top: true}
		});
		this.lblNone = new SUI.form.Label({
			top: t,
			left: this.MARGIN  + this.RADIO_WIDTH,
			right: this.MARGIN,
			title: SUI.editor.i18n.filedialog.cacheDialog.cacheNone,
			height: this.LINE_HEIGHT,
			forBox: this.inpNone,
			anchor: { right: true, left: true, top: true}
		});
		this.clientPanel.add(this.inpNone);
		this.clientPanel.add(this.lblNone);

		this.lblNormal.el().style.whiteSpace = "";
		this.lblExpires.el().style.whiteSpace = "";
		this.lblNone.el().style.whiteSpace = "";

		t += this.LINE_HEIGHT + this.MARGIN;

		this.setClientHeight(t);

		this.cbOK = arg.onOK ? arg.onOK : function() {};
		this.addListener("onOK",
			function(res) {
				res.a = "filedialog.cacheSettings";
				SUI.editor.xhr.doPost(
					SUI.editor.resource.ajaxURL, res, this.save);
			}
		);

		this.save = function(res) {
			that.close();
			that.cbOK();
		};

		this.show = function() {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: "filedialog.folderProperties",
					assetId: this.assetId
				},
				function(res) {
					var sel = that.dataToForm(res.data);
					that.center();
					SUI.editor.filedialog.CacheDialog.parentMethod(
						that, "show");
					sel.el().focus();
				}
			);
		};
	},

	MARGIN: 8,
	LINE_HEIGHT: 40,
	LINE_SPACING: 6,
	RADIO_WIDTH: 26,
	RADIO_OFFSET: 3,
	WIDTH: 450,

	assetId: 0,

	formToData: function() {
		var o = "lastmod";
		if (this.inpNone.el().checked) {
			o = "nocache";
		} else if (this.inpExpires.el().checked) {
			o = "expire";
		}
		return {
			assetId: this.assetId,
			period: this.inpTime.el().value,
			timeunit: this.selTimeUnit.el().value,
			opt: o
		};
	},

	dataToForm: function(a) {
		var sel;
		if (a.opt == "nocache") {
			this.inpNone.el().checked = true;
			sel = this.inpNone;
		} else if (a.opt == "expire") {
			this.inpExpires.el().checked = true;
			sel = this.inpExpires;
		} else {
			this.inpNormal.el().checked = true;
			sel = this.inpNormal;
		}
		this.inpTime.el().value = a.period;
		this.selTimeUnit.el().value = a.timeunit;
		return sel;
	}

});
