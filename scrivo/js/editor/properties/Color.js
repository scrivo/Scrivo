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
 * $Id: Color.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.properties.Color = SUI.defineClass({

	baseClass: SUI.editor.properties.BaseProperty,

	initializer: function(arg) {

	SUI.editor.properties.Color.initializeBase(this, arg);
		var that = this;

		this.el().style.overflow = "visible";

		this.linkButton = new SUI.ToolbarButton({
			left: 0,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			top: Math.round((this.HEIGHT-this.BUTTON_SIZE)/2),
			title: "",
			icon: SUI.editor.resource.properties.icnColor,
			handler: function() {
				new SUI.editor.ColorDialog({
					color: that.value,
					onOK: function(col) {
						that.value =  col;
						that.displayValue();
					}
				}).show();
			}
		});
		this.add(this.linkButton);

		this.removeButton = new SUI.ToolbarButton({
			left: this.BUTTON_SIZE,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			top: Math.round((this.HEIGHT-this.BUTTON_SIZE)/2),
			title: "",
			icon: SUI.editor.resource.properties.icnRemove,
			handler: function() {
				that.value = null;
				that.displayValue();
			}
		});
		this.add(this.removeButton);

		this.txtSrc = new SUI.Box({
			top: 0,
			height: this.HEIGHT,
			left: this.BUTTON_SIZE + this.BUTTON_SIZE + this.WIDTH_SEP,
			width: 50
		});
		this.txtSrc.border(new SUI.Border(1));
		this.txtSrc.el().style.borderColor = "black";
		this.add(this.txtSrc);
	},

	BUTTON_SIZE: 22,
	WIDTH_SEP: 2,

	getValue: function(e) {
		if (this.value) {
			return this.value;
		}
		return "";
	},

	setValue: function(val) {
		if (!val || SUI.trim(val) == "") {
			this.value = null;
		} else {
			this.value = val;
		}
		this.displayValue();
		this.compare = this.getValue();
	},

	displayValue: function() {
		var that = this;
		this.txtSrc.el().style.backgroundColor = "transparent";
		if (this.value && this.value != "") {
			this.txtSrc.el().style.backgroundColor = this.value;
		}
	}

});
