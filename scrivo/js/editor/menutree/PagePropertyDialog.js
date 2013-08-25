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
 * $Id: PagePropertyDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.menutree.PagePropertyDialog = SUI.defineClass({

	baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.menutree.PagePropertyDialog.initializeBase(this, arg);
		var that = this;

		this.pageId = 0;
		if (arg.pageId) {
			this.pageId = arg.pageId;
		}
		this.pagePid = 0;
		if (arg.pagePid) {
			this.pagePid = arg.pagePid;
		}

		this.width(this.WIDTH);
		this.caption(this.pageId == 0
			? SUI.editor.i18n.menutree.pagePropertyDialog.cptNewPage
			: SUI.editor.i18n.menutree.pagePropertyDialog.cptEditPage);

		var t = this.MARGIN;

		this.inpTitle = new SUI.editor.properties.HTMLInput({
			top: t,
			right: this.MARGIN,
			left: this.LABEL_WIDTH+this.MARGIN,
			anchor: { right: true, left: true, top: true}
		});
		this.lblTitle = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.menutree.pagePropertyDialog.title,
			forBox: this.inpTitle
		});
		this.clientPanel.add(this.lblTitle);
		this.clientPanel.add(this.inpTitle);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.selTemplate = new SUI.form.SelectList({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.txtTemplate = new SUI.Box({
			top: t+2,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			height: this.LINE_HEIGHT,
			anchor: { top: true, left: true, right: true }
		});
		this.selTemplate.el().style.display = "none";
		this.txtTemplate.el().style.display = "none";
		this.lblTemplate = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.menutree.pagePropertyDialog.template,
			forBox: this.selTemplate
		});
		this.clientPanel.add(this.lblTemplate);
		this.clientPanel.add(this.txtTemplate);
		this.clientPanel.add(this.selTemplate);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.selType = new SUI.form.SelectList({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblType = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.menutree.pagePropertyDialog.pageType,
			forBox: this.selType
		});
		this.clientPanel.add(this.lblType);
		this.clientPanel.add(this.selType);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.selPosition = new SUI.form.SelectList({
			top: t,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { left: true, right: true }
		});
		this.lblPosition = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.menutree.pagePropertyDialog.position,
			forBox: this.selPosition
		});
		this.clientPanel.add(this.lblPosition);
		this.clientPanel.add(this.selPosition);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.ctlOnline = new SUI.control.Date({
			top: t,
			left: this.LABEL_WIDTH+this.MARGIN,
			type: "datetime",
			anchor: { left: true, top: true}
		});
		this.lblOnline = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.menutree.pagePropertyDialog.onlineOn,
			forBox: this.ctlOnline.firstBox()
		});
		this.clientPanel.add(this.ctlOnline);
		this.clientPanel.add(this.lblOnline);

		t += this.LINE_HEIGHT + this.LINE_SPACING;

		this.ctlOffline = new SUI.control.Date({
			top: t,
			left: this.LABEL_WIDTH+this.MARGIN,
			type: "datetime",
			anchor: { left: true, top: true}
		});
		this.lblOffline = new SUI.form.Label({
			top: t,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.menutree.pagePropertyDialog.offlineOn,
			forBox: this.ctlOffline.firstBox()
		});
		this.clientPanel.add(this.ctlOffline);
		this.clientPanel.add(this.lblOffline);

		t += this.LINE_HEIGHT + this.MARGIN;

		this.setClientHeight(t);

		this.cbOK = arg.onOK ? arg.onOK : function() {};
		this.addListener("onOK",
			function(res) {
				if (this.ctlOnline.value().type == "error") {
					new SUI.dialog.Alert({
						text:
						SUI.editor.i18n.menutree.pagePropertyDialog.invalidOnlineDate
					}).show();
				} else if (this.ctlOffline.value().type == "error") {
					new SUI.dialog.Alert({
						text:
						SUI.editor.i18n.menutree.pagePropertyDialog.invalidOfflineDate
					}).show();
				} else {
					res.a = "menutree.savePageProperties";
					SUI.editor.xhr.doPost(SUI.editor.resource.ajaxURL, res, this.save);
				}
			}
		);

		this.save = function(res) {
			if (res.result != "OK") {
				new SUI.dialog.Alert({icon: "error", width: 500,
					caption: res.result, text: res.data}).show();
				return;
			}
			that.close();
		};

		this.show = function() {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: "menutree.getPageProperties",
					pageId: this.pageId,
					pagePid: this.pagePid
				},
				function(res) {
					if (res.data.pageDefinitions) {
						if (!res.data.pageDefinitions.length) {
							var t = SUI.editor.i18n.menutree.pagePropertyDialog;
							new SUI.dialog.Alert({
								width: 300,
								caption: t.errCaptionCreatePage,
								text: t.errTextCreatePage
							}).show();
							return;
						}
					}
					that.dataToForm(res.data);
					that.center();
					SUI.editor.menutree.PagePropertyDialog.parentMethod(
						that, "show");
					//that.inpTitle.el().select();
				}
			);
		};
	},

	MARGIN: 8,
	LINE_HEIGHT: 20,
	LINE_SPACING: 6,
	LABEL_WIDTH: 100,
	WIDTH: 400,


	formToData: function() {
		return {
			pageId: this.pageId,
			pagePid: this.pagePid,
			title: this.inpTitle.getValue(),
			pageDefinitionId: this.selTemplate.el().value,
			pageType: this.selType.el().value,
			position: this.selPosition.el().value,
			onlineOn: this.ctlOnline.value().strDate,
			offlineOn: this.ctlOffline.value().strDate
		};
	},

	setTypes: function(types) {
		var st = [];
		for (var i=0; i<types.length; i++) {
			st.push({
				value: types[i],
				text: this.pageTypes[types[i]]
			});
		}
		this.selType.options(st);
	},

	dataToForm: function(a) {

		var that = this;
		this.inpTitle.setValue(a.page.title);
		this.pageTypes = a.pageTypes;

		if (a.pageDefinitions) {
			this.selTemplate.options(a.pageDefinitions);
			this.pageDefinitions = a.pageDefinitions;
			this.selTemplate.el().style.display = "block";
			SUI.browser.addEventListener(this.selTemplate.el(), "change",
				function() {
					that.setTypes(
						that.pageDefinitions[that.selTemplate.el().selectedIndex].types
					);
				}
			);

			if (this.pageDefinitions.length) {
				this.setTypes(this.pageDefinitions[0].types);
			}

		} else {
			this.setTypes(a.types);
			this.selType.el().value = a.page.type;
			this.txtTemplate.el().innerHTML = a.pageDefinition;
			this.txtTemplate.el().style.display = "block";
		}

		this.selPosition.options(a.positions);
		if (a.selPos != 0) {
			this.selPosition.el().value = a.page.selPos;
		}

		this.ctlOnline.value(a.page.online);
		this.ctlOffline.value(a.page.offline);
	}

});
