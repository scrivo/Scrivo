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
 * $Id: ImageAltTitle.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.properties.ImageAltTitle = SUI.defineClass({

	baseClass: SUI.editor.properties.Image,

	initializer: function(arg) {

		SUI.editor.properties.ImageAltTitle.initializeBase(this, arg);
		var that = this;

		this.bottommargin = (this.CTRL_HEIGHT + this.MARGIN) * 2;

		var l = 0;
		var w = arg.labelWidth || this.LABEL_WIDTH;

		this.alt = new SUI.form.Input({
			bottom: this.CTRL_HEIGHT + this.MARGIN,
			left: l,
			anchor: {left: true, right: true, bottom: true}
		});

		this.lblAlt = new SUI.form.Label({
			bottom: this.CTRL_HEIGHT + this.MARGIN,
			left: -w,
			width: w,
			anchor: {left: true, bottom: true},
			title: "<small>" + SUI.editor.i18n.properties.imageAtlTitle.alt
				+ "</small>",
			forBox: this.alt
		});

		this.add(this.alt);
		this.add(this.lblAlt);

		this.title = new SUI.form.Input({
			bottom: 0,
			left: l,
			anchor: {left: true, right: true, bottom: true}
		});

		this.lblTitle = new SUI.form.Label({
			bottom: 0,
			left: -w,
			width: w,
			anchor: {left: true, bottom: true},
			title: "<small>"+SUI.editor.i18n.properties.imageAtlTitle.title
				+ "</small>",
			forBox: this.title
		});

		this.add(this.title);
		this.add(this.lblTitle);

	},

	MARGIN: 4,
	CTRL_HEIGHT: 20,

	getValue: function(e) {
		if (this.value) {
			return this.value + "\t" + this.alt.el().value + "\t" +
				this.title.el().value;
		}
		return "";
	},

	setValue: function(val) {
		if (!val) {
			val = "";
		}
		var tmp = val.split("\t");
		if (tmp.length == 0 && SUI.trim(tmp[0]) == "") {
			this.value = null;
		} else {
			tmp2 = {
				src: tmp.length > 0 ? tmp[0] : "",
				alt: tmp.length > 1 ? tmp[1] : "",
				title: tmp.length > 2 ? tmp[2] : ""
			};
			SUI.editor.properties.ImageAltTitle.parentMethod(this, "setValue", tmp2.src)
			this.alt.el().value = tmp2.alt;
			this.title.el().value = tmp2.title;
		}
		this.compare = this.getValue();
	},

	getUnitHeight: function() {
		return 4;
	}

});
