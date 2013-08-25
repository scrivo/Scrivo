/* Copyright (c) 2013, Geert Bergman (geert@scrivo.nl)
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
 * $Id: PositionDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.form.PositionDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.apps.form.PositionDialog.initializeBase(this, arg);
		var that = this;

		this.pageId = arg.pageId;
		this.pageDefinitionTabId = arg.pageDefinitionTabId;
		this.formElementId = arg.formElementId;

		this.caption(SUI.editor.i18n.apps.form.cptRepostion);

		this.populateForm();

		this.dataToForm();
	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 80,

	populateForm: function() {
		var ctlTop = this.MARGIN;
		var that = this;

		this.selPosition = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblPosition = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.apps.form.dlgPosition,
			forBox: this.selPosition
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.setClientHeight(ctlTop);
		this.setClientWidth(320);

		this.clientPanel.add(this.lblPosition);
		this.clientPanel.add(this.selPosition);

	},

	setSrc: function(assetId) {
	},

	dataToForm: function() {
		var that = this;
		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
			a: "apps.form.getFormElements",
			pageId: this.pageId,
			pageDefinitionTabId: this.pageDefinitionTabId
		}, function(res) {
			var opts = [];
			for (var i=0; i<res.data.elements.length; i++) {
				opts.push({
					value: res.data.elements[i].form_element_id,
					text: (i+1)+": "+ res.data.elements[i].itemdata.LABEL
				});
			}
			opts.push({
				data: 0,
				text: (i+1)+": "+ SUI.editor.i18n.apps.form.txtBelow
			});

			that.selPosition.options(opts);
			that.selPosition.el().value = that.formElementId;
		});
	},

	formToData: function() {
		this.close();
		return {
			pageId: this.pageId,
			pageDefinitionTabId: this.pageDefinitionTabId,
			formElementId: this.formElementId,
			newPos: this.selPosition.el().value
		};
	}

});
