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
 * $Id: ListItemDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.list.ListItemDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		arg.resizable = true;
		SUI.editor.apps.list.ListItemDialog.initializeBase(
			this, arg);
		var that = this;

		this.pageId = arg.pageId;
		this.pagePropertyDefinitionId = arg.pagePropertyDefinitionId;
		this.listItemId = arg.listItemId ? arg.listItemId : 0;
		this.listItemDefinitionId = arg.listItemDefinitionId ? arg.listItemDefinitionId : 0;
		this.copyItem = arg.copyItem ? arg.copyItem : 0;
		this.parentId = arg.parentId ? arg.parentId : 0;

	//    this.clientPanel.inner.border(new SUI.Border(""));

		this.resizable = true;
		this.width(this.WIDTH);
		this.caption(this.listItemId
			? SUI.editor.i18n.apps.list.listItemDialog.dlgCaptionEditItem
			: SUI.editor.i18n.apps.list.listItemDialog.dlgCaptionNewItem);

		this.cbOK = arg.onOK ? arg.onOK : function() {};
		this.addListener("onOK",
			function(res) {
/*
				if (this.ctlOnline.getValue().type == "error") {
					new SUI.dialog.Alert(
						{text: SUI.editor.i18n.fdlgInvalidOnlineDate}).show();
				} else if (this.ctlOffline.getValue().type == "error") {
					new SUI.dialog.Alert(
						{text: SUI.editor.i18n.fdlgInvalidOfflineDate}).show();
				} else {
*/
				var res = this.formToData();
				res.a = "apps.list.saveListItem";
				res.pageId = this.pageId;
				res.pagePropertyDefinitionId = this.pagePropertyDefinitionId;
				res.listItemId = this.copyItem ? 0 : this.listItemId;
				res.listItemDefinitionId = this.listItemDefinitionId;
				res.parentId = this.parentId;

				SUI.editor.xhr.doPost(
					SUI.editor.resource.ajaxURL,
					res,
					function(res) {
						that.close();
						that.cbOK();
					}
				);
			}
		);

		this.show = function(assetId) {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: "apps.list.getListItem",
					pageId: this.pageId,
					parentId: this.parentId,
					pagePropertyDefinitionId: this.pagePropertyDefinitionId,
					listItemId: this.listItemId,
					listItemDefinitionId: this.listItemDefinitionId
				},
				function(res) {
					that.dataToForm(res.data);
					that.center();
					SUI.editor.apps.list.ListItemDialog.parentMethod(
						that, "show");
					try {
						that.controls[0].el().firstChild.select();
					} catch (e) {}
				}
			);
		};
	},

	WIDTH: 400,

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 120,

	assetId: 0,

	formToData: function() {
		var res = {};
		for (var j=0; j<this.phpSelectors.length; j++) {
			var k = SUI.trim(this.phpSelectors[j]);
			if (k != "" && this.controls[j].getValue) {
				var v = this.controls[j].getValue();
				if (v) {
					res[k] = v;
				}
			}
		}
		return res;
	},

	dataToForm: function(a) {

		var hasTabs = a.tabs && a.tabs.length > 0;
		var that = this;

		this.listItemDefinitionId = a.listItemDefinitionId;

		if (hasTabs) {
			this.clientPanel.inner.border(new SUI.Border());
			var tabs = [];
			for (var i=0; i<a.tabs.length; i++) {
				tabs.push({title: a.tabs[i]});
			}

			this.tabPanel = new SUI.TabPanel({
				tabs: tabs,
				panelMargin: 0,
				onSelectTab: function(tab) {
					// This is a patch for a bug in FF: when contenteditabe
					// iframes are initially hidden system exceptions occur
					// when execCommand is called in the onload handler,
					// causing the onload handler to end prematurely.
					for (var i=0; i<tab.content.children.length; i++) {
						var c = tab.content.children[i];
						if (c instanceof SUI.editor.properties.HTMLInput ||
								c instanceof SUI.editor.properties.HTMLText) {
							c.geckoForceOnloadEvent();
						}
					}
				}
			});
		} else {
			this.panel = new SUI.Panel({});
		}

		this.controls = [];
		this.labels = [];
		this.phpSelectors = [];

		var lMaxHeight = 0;

		for (var j=0, k=0; j<a.items.length; j++) {

			var ctlTop = this.MARGIN;

			for (var i=0; i<a.items[j].length; i++, k++) {

				this.phpSelectors[k] = "prop_"+a.items[j][i].phpSelector;

				this.labels[k] = new SUI.form.Label({
					top: ctlTop,
					left: this.MARGIN,
					width: this.LABEL_WIDTH,
					title: a.items[j][i].label
				});
				if (hasTabs) {
					this.tabPanel.add(this.labels[k], j-1);
				} else {
					this.panel.add(this.labels[k]);
				}

				var defP = {
					top: ctlTop,
					right: this.MARGIN,
					anchor: { right: true, left: true },
					typeData: a.items[j][i].typeData
				};

				if (a.items[j][i].type == "input") {
					this.controls[k] = new SUI.editor.properties.Input(defP);
				} else if (a.items[j][i].type == "url") {
					this.controls[k] = new SUI.editor.properties.URL(defP);
				} else if (a.items[j][i].type == "img") {
// Fix in next release -> db update
//                    this.controls[k] = new SUI.editor.properties.Image(defP);
//                } else if (a.items[j][i].type == "imgalttit") {
					defP.labelWidth = this.LABEL_WIDTH - this.MARGIN;
					this.controls[k] =
						new SUI.editor.properties.ImageAltTitle(defP);
				} else if (a.items[j][i].type == "text") {
					this.controls[k] = new SUI.editor.properties.Text(defP);
				} else if (a.items[j][i].type == "color") {
					this.controls[k] = new SUI.editor.properties.Color(defP);
				} else if (a.items[j][i].type == "checkbox") {
					this.controls[k] =
						new SUI.editor.properties.CheckBox(defP);
				} else if (a.items[j][i].type == "datetime") {
					this.controls[k] = new SUI.editor.properties.Date(defP);
				} else if (a.items[j][i].type == "select") {
					this.controls[k] =
						new SUI.editor.properties.SelectList(defP);
				} else if (a.items[j][i].type == "info") {
					this.controls[k] = new SUI.editor.properties.Info(defP);
					this.controls[k].top(this.controls[k].top() + 2);
					this.labels[k].el().style.display = "none";
				} else if (a.items[j][i].type == "html_text") {
					this.controls[k] =
						new SUI.editor.properties.HTMLText(defP);
				} else if (a.items[j][i].type == "colorlist") {
					this.controls[k] =
						new SUI.editor.properties.ColorList(defP);
				} else {
					this.controls[k] = new SUI.editor.properties.Input(defP);
				}

				var h = this.controls[k].getUnitHeight();
				if (h != 1) {
					this.controls[k].height(
						h * this.CTRL_HEIGHT + (h-1) * this.MARGIN);
				}

				if (this.controls[k].isFullWidth()) {
					if (a.items[j][i].label != "") {
						this.controls[k].top(
							this.controls[k].top() + this.CTRL_HEIGHT);
						this.controls[k].height(
							this.controls[k].height() - this.CTRL_HEIGHT);
					}
					this.controls[k].left(this.MARGIN);
					this.controls[k].right(this.MARGIN);
				} else {
					this.controls[k].left(this.MARGIN + this.LABEL_WIDTH);
					this.controls[k].right(this.MARGIN);
				}

				ctlTop += (this.MARGIN + this.CTRL_HEIGHT) * h;

				if (this.controls[k].setValue) {
					this.controls[k].setValue(a.items[j][i].data);
				}

				if (hasTabs) {
					this.tabPanel.add(this.controls[k], j-1);
				} else {
					this.panel.add(this.controls[k]);
				}
			}

			if (ctlTop > lMaxHeight) {
				lMaxHeight = ctlTop;
			}
		}

		if (hasTabs) {
			this.clientPanel.add(this.tabPanel);
			this.setClientHeight(lMaxHeight + this.tabPanel.clientAreaPosition().top
				+ this.tabPanel.clientAreaPosition().bottom);
		} else {
			this.clientPanel.add(this.panel);
			this.setClientHeight(lMaxHeight);
		}
		this.setClientWidth(550);

	}

});

