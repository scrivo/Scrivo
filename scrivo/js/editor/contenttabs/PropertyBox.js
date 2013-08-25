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
 * $Id: PropertyBox.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.contenttabs.PropertyBox = SUI.defineClass({

	baseClass: SUI.AnchorLayout,

	initializer: function(arg) {

		SUI.editor.contenttabs.PropertyBox.initializeBase(this, arg);

		if (arg.onLoad) {
			this.onLoad = arg.onLoad;
		}
		if (arg.onDataSaved) {
			this.onDataSaved = arg.onDataSaved;
		}

		this.anchor = { left: true, top: true, right: true };

		this.minWidth(400);

		this.properties = [];
		this.labels = [];
		this.controls = [];

		if (arg.pageId) {
			this.loadControls(arg.pageId, arg.pageDefinitionTabId);
		}

	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 120,

	toolbar: null,
	properties: null,
	labels: null,
	controls: null,

	pageId: 0,
	pageDefinitionTabId: 0,

	onLoad: function() {},
	onDataSaved: function(res) {},

	loadControls: function(pageId, pageDefinitionTabId) {
		var that = this;
		this.pageId = pageId;
		this.pageDefinitionTabId = pageDefinitionTabId;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL,
			{
				a: "contenttabs.getPropertyList",
				pageId: pageId,
				pageDefinitionTabId: pageDefinitionTabId
			},
			function(res) {
				that.buildPanel(res.data.properties);
			}
		);
	},

	positionControl: function(i, top, center, h, left) {
		this.labels[i].top(top);
		this.controls[i].top(top);

		if (h != 1) {
			this.controls[i].height(
				h * this.CTRL_HEIGHT + (h-1) * this.MARGIN);
		}

		if (this.controls[i].isFullWidth()) {
			if (this.properties[i].label != "") {
				this.controls[i].top(
					this.controls[i].top() + this.CTRL_HEIGHT);
				this.controls[i].height(this.controls[i].height() - this.CTRL_HEIGHT);
			}
			this.controls[i].left(this.MARGIN);
			this.controls[i].right(this.MARGIN);
		} else if (left) {
			this.controls[i].left(this.MARGIN + this.LABEL_WIDTH);
			this.controls[i].right(center + 2*this.MARGIN);
		} else {
			this.labels[i].left(center + 2*this.MARGIN);
			this.controls[i].left(center + 2*this.MARGIN + this.LABEL_WIDTH);
		}
	},

	onLayout: function(box) {

		var center = Math.round(box.width() * 1 / 2);
		var left = true; // left or right column
		var lOcc = 0; // no of "cells" occupied in left colum
		var rOcc = 0; // no of "cells" occupied in right colum
		var fw = false;
		var ctlTop = this.MARGIN;

		for (var i=0; i<this.properties.length; /* void */) {

			var p = this.properties[i];
			var newCtrl = this.controls[i];

			// Start with new control if lOcc and/or rOcc - which depends on
			// te case - is down to zero
			if ((newCtrl.isFullWidth() && !lOcc && !rOcc)
					|| (!newCtrl.isFullWidth() && ((left && !lOcc)
					|| (!left && !rOcc)))) {
				fw = newCtrl.isFullWidth();
				// fullwidth control: reset to left side
				if (!left && (fw)) {
					ctlTop += this.MARGIN + this.CTRL_HEIGHT;
					left = true;
				}
				var h = newCtrl.getUnitHeight();
				this.positionControl(i, ctlTop, center, h ,left);
				if (left) {
					if (!lOcc) lOcc = h;
				} else {
					if (!rOcc) rOcc = h;
				}
				i++;
			}

			if (left) {
				if (lOcc) lOcc--;
				if (fw) {
					ctlTop += this.MARGIN + this.CTRL_HEIGHT;
				}
			} else {
				if (rOcc) rOcc--;
				ctlTop += this.MARGIN + this.CTRL_HEIGHT;
			}

			left = fw ? true : !left;
		}

		// Keep adding until lOcc and rOcc are down to zero to
		// determine the height of the panel
		while (lOcc || rOcc) {
			if (lOcc) lOcc--;
			if (rOcc) rOcc--;
			ctlTop += this.MARGIN + this.CTRL_HEIGHT;
		}
		if(!left) ctlTop += this.MARGIN + this.CTRL_HEIGHT;

		this.height(ctlTop);

	},

	buildPanel: function(properties) {

		this.properties = properties;

		for (var i=0; i<properties.length; i++) {
			this.labels[i] = new SUI.form.Label({
				left: this.MARGIN,
				width: this.LABEL_WIDTH - this.MARGIN,
				title: properties[i].label
			});
			this.add(this.labels[i]);

			var defP = {
				right: this.MARGIN,
				anchor: { right: true, left: true },
				typeData: properties[i].typeData
			};
			if (properties[i].type == "input") {
				this.controls[i] = new SUI.editor.properties.Input(defP);
			} else if (properties[i].type == "url") {
				this.controls[i] = new SUI.editor.properties.URL(defP);
			} else if (properties[i].type == "img") {
				defP.labelWidth = this.LABEL_WIDTH - this.MARGIN;
				this.controls[i] = new SUI.editor.properties.Image(defP);
			} else if (properties[i].type == "imgaltttl") {
				defP.labelWidth = this.LABEL_WIDTH - this.MARGIN;
				this.controls[i] =
					new SUI.editor.properties.ImageAltTitle(defP);
			} else if (properties[i].type == "text") {
				this.controls[i] = new SUI.editor.properties.Text(defP);
			} else if (properties[i].type == "color") {
				this.controls[i] = new SUI.editor.properties.Color(defP);
			} else if (properties[i].type == "checkbox") {
				this.controls[i] = new SUI.editor.properties.CheckBox(defP);
			} else if (properties[i].type == "select") {
				this.controls[i] = new SUI.editor.properties.SelectList(defP);
			} else if (properties[i].type == "html_text") {
				this.controls[i] = new SUI.editor.properties.HTMLText(defP);
			} else if (properties[i].type == "colorlist") {
				this.controls[i] = new SUI.editor.properties.ColorList(defP);
			} else {
				this.controls[i] = new SUI.editor.properties.Input(defP);
			}

			if (this.controls[i].setValue) {
				this.controls[i].setValue(properties[i].value);
			}

			this.add(this.controls[i]);

		}

		// an extra layOut to determine the height of the panel
		this.onLayout(this);
		this.onLoad();

	},

	layOut: function() {
		this.onLayout(this);
		SUI.editor.contenttabs.PropertyBox.parentMethod(this, "layOut");
	},

	dataModified: function() {

		for (var i=0; i<this.properties.length; i++) {
			if (this.controls[i].isChanged()) {
				return true;
			}
		}
		return false;
	},

	saveData: function() {

		var resdata = {};
		for (var i=0; i<this.properties.length; i++) {
			var val = this.controls[i].getValue() || "";
			if (val !== "") {
				resdata["prop_" + this.properties[i].phpSelector] = val;
			}
		}

		resdata.a = "contenttabs.savePropertyList";
		resdata.pageId = this.pageId;
		resdata.pageDefinitionTabId = this.pageDefinitionTabId;

		var that = this;

		SUI.editor.xhr.doPost(
			SUI.editor.resource.ajaxURL,
			resdata,
			function(res) {
				for (var i=0; i<that.properties.length; i++) {
					that.controls[i].compare =
						that.controls[i].getValue();
				}
				that.onDataSaved(res);
			}
		);
	}

});
