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
 * $Id: HTMLText.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.properties.HTMLText = SUI.defineClass({

	baseClass: SUI.editor.properties.BaseProperty,

	initializer: function(arg) {

		SUI.editor.properties.HTMLText.initializeBase(this, arg);
		var that = this;

		var p = new SUI.Panel({
		 border: new SUI.Border(1)
		});

		if (this.typeData.unitHeight) {
			this.unitHeight = this.typeData.unitHeight;
		} else if (this.typeData.height) {
			var n = this.typeData.height - 8;
			this.unitHeight = Math.ceil(n / 28);
		} else {
			this.unitHeight = 7;
		}

		this.property = new SUI.editor.htmleditor.HTMLEditor({
			top: 0,
			right: 0,
			anchor: {left: true, right: true, bottom: true, top: true},
			onLoad: function() { that.setCompare(); }
		});
		this.add(p);
		p.add(this.property);
	},

	getValue: function(e) {
		return this.property.getValue();
	},

	setValue: function(val) {
		this.property.editCtrl.resetIframe();
		this.property.editCtrl.setCSS(
			"html {" +
			"  border: none;" +
			"}" +
			"body {" +
			"  font-size: 80%;" +
			"  font-family: Verdana, Arial, sans-serif;" +
			"  background-color : white;" +
			"  margin: 3px;" +
			"  padding: 3px;" +
			"}" +
			"h1 {font-size:150% }" +
			"h2 {font-size:140% }" +
			"h3 {font-size:130% }" +
			"h4 {font-size:120% }" +
			"h5 {font-size:110% }" +
			"h6 {font-size:100% }" +
			"body p {" +
			"  margin-top: 0.4em;" +
			"  margin-bottom: 0.4em" +
			"}");
		this.property.setValue(val);
	},

	/**
	* Compare value is set onLoad
	*/
	setCompare: function() {
		this.compare = this.getValue();
	},

	/**
	 * Force an onload event to happen to on Gecko based browsers (Gecko has
	 * issues cause sytem exeception in the onload handler when an onload
	 * event happens on an initially hidden contenteditable iframe).
	 */
	geckoForceOnloadEvent: function() {
		this.property.geckoForceOnloadEvent();
	},

	getUnitHeight: function() {
		return this.unitHeight;
	},

	isFullWidth: function() {
		return true;
	}


});
