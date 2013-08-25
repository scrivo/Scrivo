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
 * $Id: Image.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.properties.Image = SUI.defineClass({

	baseClass: SUI.editor.properties.BaseProperty,

	initializer: function(arg) {

		SUI.editor.properties.Image.initializeBase(this, arg);
		var that = this;

		this.el().style.overflow = "visible";

		var pageDefintionWidth = arg.typeData.pageDefintionWidth;
		var pageDefinitionHeight = arg.typeData.pageDefinitionHeight;

		this.linkButton = new SUI.ToolbarButton({
			left: 0,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			top: Math.round((this.HEIGHT-this.BUTTON_SIZE)/2),
			title: "",
			icon: SUI.editor.resource.properties.icnImage,
			handler: function() {
				var assetId = parseInt(that.getValue().substr(
					SUI.editor.resource.assetLink.length),10);
				new SUI.editor.filedialog.FileDialog({
					assetId: assetId,
					pageDefintionWidth: pageDefintionWidth,
					pageDefinitionHeight: pageDefinitionHeight,
					onOK: function(assetId) {
						if (assetId.type == "assetId") {
							that.setValue2(
								SUI.editor.resource.assetLink+assetId.value, true);
						} else {
							that.setValue2(assetId.value, true);
						}
					}
				}).show();
			}
		});
		this.add(this.linkButton);

		this.removeButton = new SUI.ToolbarButton({
			left: 0,
			width: this.BUTTON_SIZE,
			height: this.BUTTON_SIZE,
			top: Math.round((this.HEIGHT-this.BUTTON_SIZE)/2) + this.BUTTON_SIZE,
			title: "",
			icon: SUI.editor.resource.properties.icnRemove,
			handler: function() {
				that.value = null;
				that.displayValue();
			}
		});
		this.add(this.removeButton);

		this.imgBox = new SUI.Box({
			top: 0,
			height: this.HEIGHT*2,
			left: this.BUTTON_SIZE + this.WIDTH_SEP,
			width: 50
		});
		this.imgBox.border(new SUI.Border(this.BORDER));
		this.imgBox.el().style.borderImage = "black";
		this.add(this.imgBox);

		this.cbLayout = function(a) {
			this.displayValue();
		};

		var sizeLabel = "";
		   if (arg.typeData.pageDefintionWidth) {
			   sizeLabel = SUI.editor.i18n.properties.image.width + ": "
				   + arg.typeData.pageDefintionWidth + "<br>";
		   }
		   if (arg.typeData.pageDefinitionHeight) {
			   sizeLabel += SUI.editor.i18n.properties.image.height + ": "
				   + arg.typeData.pageDefinitionHeight;
		   }
		   if (sizeLabel !== "") {
			   var lw = arg.labelWidth || this.LABELWIDTH;
			   this.add(new SUI.form.Label({
				top: 18,
				height: 50,
				left: -lw,
				width: lw,
				anchor: {left: true, top: true},
				title: "<small>"+sizeLabel+"</small>"
			}));
		   }


	},

	BUTTON_SIZE: 22,
	WIDTH_SEP: 2,
	BORDER: 1,
	LABELWIDTH: 100,

	image: null,
	origh: 0,
	origw: 0,
	bottommargin: 0,

	getValue: function(e) {
		if (this.value) {
			return this.value;
		}
		return "";
	},

	setValue: function(val) {
		this.setValue2(val, false);
		this.compare = this.getValue();
	},

	setValue2: function(val, intern) {
		if (!val || SUI.trim(val) == "") {
			this.value = null;
		} else {
			if (this.value != val) {
				this.clearImage();
				this.value = val;
			}
		}
		this.displayValue();
	},

	loadImage: function() {
		var that = this;
		this.image = new Image();
		this.image.style.position = "absolute";
		this.image.onload = function() {
			that.origh = that.image.height;
			that.origw = that.image.width;
			that.imgBox.el().appendChild(that.image);
			that.displayValue();
		};
		// Set the src after you set the onload handler
		this.image.src = this.value;
	},

	clearImage: function() {
		if (this.image && this.imgBox.el().firstChild) {
			this.imgBox.el().removeChild(this.imgBox.el().firstChild);
		}
		this.image = null;
	},

	display: function() {
		SUI.editor.properties.Image.parentMethod(this, "display");
		this.displayValue();
	},

	displayValue: function() {

		if (!this.value || SUI.trim(this.value) == "") {
			this.clearImage();
			return;
		}

		if (!this.image) {
			this.loadImage();
			return;
		}

		if (!this.origh) {
			return;
		}

		var    rat = this.origw/this.origh;
		var w, h;
		if ((this.width() - this.imgBox.left())/
				(this.height()-this.bottommargin) < rat) {
			w = Math.round(this.width() - this.imgBox.left());
			h = Math.round(w/rat);
		} else {
			h = Math.round(this.height()-this.bottommargin);
			w = Math.round(h*rat);
		}

		this.image.height = h-2*this.BORDER;
		this.image.width = w-2*this.BORDER;
		this.imgBox.height(h);
		this.imgBox.width(w);

		this.imgBox.display();
	},

	getUnitHeight: function() {
		return 2;
	}

});
