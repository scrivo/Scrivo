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
 * $Id: DefaultPanel.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * Simple panel implementation, a sort of extion of PropertyPanel (study
 * that one first). Next to a placeholder of the properties on the
 * PropertyBox there are also a number of controls for the default
 * properties of a page (language, title, keywords, javascript, etc).
 * Flow is a little bit ackward because keywords and javascript controls
 * are located after the PropertyBox, but the height of the PropertyBox
 * is ony known after it has loaded its properties.
 */
SUI.editor.contenttabs.DefaultPanel = SUI.defineClass({

	baseClass: SUI.Panel,

	initializer: function(arg) {

		SUI.editor.contenttabs.DefaultPanel.initializeBase(this, arg);
		var that = this;

		if (arg.onLoad) {
			this.cbOnLoad = arg.onLoad;
		}

	//    this.minWidth(400);

		this.saveButton = new SUI.ToolbarButton({
			title: SUI.editor.i18n.contenttabs.save,
			icon: SUI.editor.resource.contenttabs.icnSave,
			handler: function() {
				that.pb.saveData();
			}
		});
		this.saveButton.enable(false);

		this.stylesheet = "";
		this.cssButton = new SUI.ToolbarButton({
			title: SUI.editor.i18n.contenttabs.defaultPanel.style,
			icon: SUI.editor.resource.contenttabs.icnCss,
			handler: function() {
				new SUI.editor.CodeDialog({
					code: that.stylesheet,
					onOK: function(css) {
						that.stylesheet = css;
					}
				}).show();
			}
		});
		this.cssButton.enable(false);

		this.javascript = "";
		this.scriptButton = new SUI.ToolbarButton({
			title: SUI.editor.i18n.contenttabs.defaultPanel.script,
			icon: SUI.editor.resource.contenttabs.icnScript,
			handler: function() {
				new SUI.editor.CodeDialog({
					code: that.javascript,
					onOK: function(script) {
						that.javascript = script;
					}
				}).show();
			}
		});
		this.scriptButton.enable(false);

		this.accessButton = new SUI.ToolbarButton({
			title: SUI.editor.i18n.contenttabs.defaultPanel.access,
			icon: SUI.editor.resource.contenttabs.icnAccess,
			handler: function() {
				var p = new SUI.editor.AccessDialog({
					pageId: arg.pageId,
					onOK: function() {

					}
				}).show();
			}
		});

		this.toolbar = new SUI.Toolbar({
			tools: [
				this.saveButton,
				new SUI.ToolbarSeparator({}),
				this.cssButton,
				this.scriptButton,
				new SUI.ToolbarSeparator({}),
				this.accessButton
			]
		});

		this.add(this.toolbar);

		this.scrollbox = new SUI.editor.VerticalScrollBox({
			top: this.toolbar.height()
		});

		this.box = new SUI.AnchorLayout({
			anchor: { left: true, top: true, right: true }
		});

		this.add(this.scrollbox);
		this.scrollbox.add(this.box);

		var ctlTop = this.MARGIN;

		this.txtTitle = new SUI.Box({
			top: ctlTop + 2,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: 3*this.MARGIN + 2*this.DATE_WIDTH + 2*this.DATE_LABEL_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});
		this.txtTemplate = new SUI.Box({
			top: ctlTop + this.CTRL_HEIGHT + this.MARGIN + 2,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: 3*this.MARGIN + 2*this.DATE_WIDTH + 2*this.DATE_LABEL_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});
		this.txtCreated = new SUI.Box({
			top: ctlTop + 2,
			width: this.DATE_WIDTH,
			right: 2*this.MARGIN + this.DATE_WIDTH + this.DATE_LABEL_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { right: true }
		});
		this.txtModified = new SUI.Box({
			top: ctlTop + this.CTRL_HEIGHT + this.MARGIN + 2,
			width: this.DATE_WIDTH,
			right: 2*this.MARGIN + this.DATE_WIDTH + this.DATE_LABEL_WIDTH,
			height: this.CTRL_HEIGHT,
			anchor: { right: true }
		});
		this.txtOnline = new SUI.Box({
			top: ctlTop + 2,
			width: this.DATE_WIDTH,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true }
		});
		this.txtOffline = new SUI.Box({
			top: ctlTop + this.CTRL_HEIGHT + this.MARGIN + 2,
			width: this.DATE_WIDTH,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true }
		});


		this.lblTitle = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.contenttabs.defaultPanel.title
		});
		this.lblTemplate = new SUI.form.Label({
			top: ctlTop + this.CTRL_HEIGHT + this.MARGIN,
			left: this.MARGIN,
			width: this.LABEL_WIDTH- this.MARGIN,
			title: SUI.editor.i18n.contenttabs.defaultPanel.template
		});
		this.lblCreated = new SUI.form.Label({
			top: ctlTop,
			right: 3*this.MARGIN + 2*this.DATE_WIDTH + this.DATE_LABEL_WIDTH,
			width: this.DATE_LABEL_WIDTH - this.MARGIN,
			anchor: { right: true },
			title: SUI.editor.i18n.contenttabs.defaultPanel.created
		});
		this.lblModified = new SUI.form.Label({
			top: ctlTop + this.CTRL_HEIGHT + this.MARGIN,
			right: 3*this.MARGIN + 2*this.DATE_WIDTH + this.DATE_LABEL_WIDTH,
			width: this.DATE_LABEL_WIDTH - this.MARGIN,
			anchor: { right: true },
			title: SUI.editor.i18n.contenttabs.defaultPanel.modified
		});
		this.lblOnline = new SUI.form.Label({
			top: ctlTop,
			right: 2*this.MARGIN+this.DATE_WIDTH,
			width: this.DATE_LABEL_WIDTH - this.MARGIN,
			anchor: { right: true },
			title: SUI.editor.i18n.contenttabs.defaultPanel.online
		});
		this.lblOffline = new SUI.form.Label({
			top: ctlTop + this.CTRL_HEIGHT + this.MARGIN,
			right: 2*this.MARGIN+this.DATE_WIDTH,
			width: this.DATE_LABEL_WIDTH - this.MARGIN,
			anchor: { right: true },
			title: SUI.editor.i18n.contenttabs.defaultPanel.offline
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.box.add(this.lblTitle);
		this.box.add(this.txtTitle);
		this.box.add(this.lblTemplate);
		this.box.add(this.txtTemplate);
		this.box.add(this.lblCreated);
		this.box.add(this.txtCreated);
		this.box.add(this.lblModified);
		this.box.add(this.txtModified);
		this.box.add(this.lblOnline);
		this.box.add(this.txtOnline);
		this.box.add(this.lblOffline);
		this.box.add(this.txtOffline);

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selLanguage = new SUI.form.SelectList({
			top: ctlTop + 2,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: 200,
			height: this.CTRL_HEIGHT,
			anchor: { left: true }
		});
		this.lblLanguage = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.contenttabs.defaultPanel.language,
			forBox: this.selLanguage
		});

		this.box.add(this.lblLanguage);
		this.box.add(this.selLanguage);

		ctlTop += this.CTRL_HEIGHT;

		this.txaDescription = new SUI.form.TextArea({
			top: ctlTop + this.CTRL_HEIGHT,
			left: this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT * 3,
			anchor: { right: true, left: true }
		});
		this.lblDescription = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.contenttabs.defaultPanel.description,
			forBox: this.txaDescription
		});

		this.txaKeywords = new SUI.form.TextArea({
			top: ctlTop + this.CTRL_HEIGHT,
			left: this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT * 3,
			anchor: { right: true, left: true }
		});
		this.lblKeywords = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: SUI.editor.i18n.contenttabs.defaultPanel.keywords,
			forBox: this.txaKeywords
		});

		this.pbLoaded = false;
		this.defLoaded = false;
		this.origdata = null;

		this.pb = new SUI.editor.contenttabs.PropertyBox({
			top: ctlTop,
			pageId: arg.pageId,
			pageDefinitionTabId: 0,
			onLoad: function() {
				that.addKeywordsAndDescription();
				that.cbOnLoad();

				that.pbLoaded = true;
				if (that.pbLoaded && that.defLoaded) {
					that.saveButton.enable(true);
				}
			},
			onDataSaved: function(res) {

				var data = {
					a: "contenttabs.saveDefaultProperties",
					pageId: arg.pageId,
					language: that.selLanguage.el().value,
					keywords: that.txaKeywords.el().value,
					description: that.txaDescription.el().value,
					stylesheet: that.stylesheet,
					javascript: that.javascript
				};

				SUI.editor.xhr.doPost(
					SUI.editor.resource.ajaxURL,
					data,
					function(res) {
						that.origdata = data;

						new SUI.dialog.Alert({icon: "ok", width: 170,
							text: SUI.editor.i18n.contenttabs.dataSaved}).show();
					}
				);
			}
		});

		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL,
			{
				a: "contenttabs.getDefaultProperties",
				pageId: arg.pageId
			},
			function(res) {
				that.selLanguage.options(res.data.properties.languages);
				that.selLanguage.el().value = res.data.properties.language;
				that.txaDescription.el().value = res.data.properties.description;
				that.txaKeywords.el().value = res.data.properties.keywords;
				that.stylesheet = res.data.properties.stylesheet;
				that.javascript = res.data.properties.javascript;
				that.txtTitle.el().innerHTML =
					"<b>" + res.data.properties.title + "</b>";
				that.txtTemplate.el().innerHTML =
					"<b>" + res.data.properties.template + "</b>";
				that.txtCreated.el().innerHTML =
					that.formatDate(res.data.properties.dateCreated);
				that.txtModified.el().innerHTML =
					that.formatDate(res.data.properties.dateModified);
				that.txtOnline.el().innerHTML =
					that.formatDate(res.data.properties.dateOnline, ">");
				that.txtOffline.el().innerHTML =
					that.formatDate(res.data.properties.dateOffline, "<=");

				that.origdata = res.data.properties;

				that.defLoaded = true;
				if (that.pbLoaded && that.defLoaded) {
					that.saveButton.enable(true);
				}

				that.scriptButton.enable(true);
				that.cssButton.enable(true);
			}
		);

		this.box.add(this.pb);
	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 120,
	DATE_WIDTH: 120,
	DATE_LABEL_WIDTH: 100,

	cbOnLoad: function() {},

	formatDate: function(dt, ref) {
		if (!dt) {
			return "<span style=\"font-weight:bold;white-space:nowrap\">" +
				"-</span>";
		}
		dt = SUI.date.parseSqlDate(dt);
		var s = SUI.date.format(dt, "datetime");
		var st = "";
		if (ref) {
			var rf = new Date();
			var res = false;
			switch (ref) {
			case ">": res = dt > rf; break;
			case ">=": res = dt >= rf; break;
			case "<": res = dt < rf; break;
			case "<=": res = dt <= rf; break;
			}
			if (res) {
				st = ";color:#bb0000";
			}
		}
		return "<span style=\"font-weight:bold;white-space:nowrap"
			+ st + "\">" + s + "</span>";
	},

	addKeywordsAndDescription: function() {

		var ctlTop = this.pb.top() + this.pb.height();

		this.lblDescription.top(ctlTop);
		this.txaDescription.top(ctlTop + this.CTRL_HEIGHT);

		this.box.add(this.lblDescription);
		this.box.add(this.txaDescription);

		ctlTop += 4 * this.CTRL_HEIGHT + this.MARGIN;

		this.lblKeywords.top(ctlTop);
		this.txaKeywords.top(ctlTop + this.CTRL_HEIGHT);

		this.box.add(this.lblKeywords);
		this.box.add(this.txaKeywords);

		this.box.height(ctlTop + 4 * this.CTRL_HEIGHT + this.MARGIN);

	},

	dataModified: function() {

		if (this.saveButton.enabled) {
			if (this.selLanguage.el().value != this.origdata.language
				|| this.txaDescription.el().value != this.origdata.description
				|| this.txaKeywords.el().value != this.origdata.keywords
				|| this.stylesheet != this.origdata.stylesheet
				|| this.javascript != this.origdata.javascript) {
				return true;
			}
			return this.pb.dataModified();
		}
		return false;
	},

	saveData: function() {
		if (this.saveButton.enabled) {
			this.pb.saveData();
		}
	}

});
