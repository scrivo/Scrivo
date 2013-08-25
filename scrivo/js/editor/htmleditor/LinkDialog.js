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
 * $Id: LinkDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.LinkDialog = SUI.defineClass(
	/** @lends SUI.editor.htmleditor.LinkDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	initializer: function(arg) {

		SUI.editor.htmleditor.LinkDialog.initializeBase(this, arg);
		var that = this;

		this.setClientHeight(155);
		this.setClientWidth(500);

		this.caption(arg.link
			? SUI.editor.i18n.htmleditor.linkDialog.cptEditLink
			: SUI.editor.i18n.htmleditor.linkDialog.cptNewLink);

		// add the event listeners
		this.addListener("onRemove", arg.onRemove || function(e) {});

		this.clientPanel.inner.border(new SUI.Border());

		this.selTab = 0;
		this.data = null;
		this.content = arg.content?anchors:null;

		this.prepareData = function(urldata) {

			this.data = [{
				newlink: true,
				protocol: "http://",
				domPrtPthQry: "",
				pageId: 0,
				assetId: 0,
				anchor: "",
				title: "",
				target: "_blank",
				type: "external"
			}, {
				newlink: true,
				protocol: "http://",
				domPrtPthQry: "",
				pageId: arg.pageId,
				assetId: 0,
				anchor: "",
				title: "",
				target: "",
				type: "internal"
			}, {
				newlink: true,
				protocol: "http://",
				domPrtPthQry: "",
				pageId: 0,
				assetId: 0,
				anchor: "",
				title: "",
				target: "_blank",
				type: "file"
			}];

			var internalRef = SUI.editor.resource.internalLink;
			var fileRef = SUI.editor.resource.assetLink;

			if (urldata) {
				if (urldata.href) {

					this.selTab = 0;
					if (urldata.href.substr(0, internalRef.length)
							== internalRef) {
						this.selTab = 1;
						this.data[this.selTab].pageId =
							parseInt(urldata.href.substr(internalRef.length),10);
					} else if (urldata.href.substr(0, fileRef.length) == fileRef) {
						this.selTab = 2;
						this.data[this.selTab].assetId =
							parseInt(urldata.href.substr(fileRef.length),10);
					}

					//anchor
					var tmp = urldata.href.split("#");
					if (tmp.length > 1) {
						this.data[this.selTab].anchor = tmp[1];
						urldata.href = tmp[0];
					}

					//protocol
					var tmp = this.protocolSplit(urldata.href);
					if (tmp.protocol !== "") {
						this.data[this.selTab].protocol = tmp.protocol;
						this.data[this.selTab].domPrtPthQry = tmp.domPrtPthQry;
					} else {
						this.data[this.selTab].protocol = "";
						this.data[this.selTab].domPrtPthQry = urldata.href;
					}

					this.data[this.selTab].target =
						(!urldata.target || urldata.target == "") ?
						"" : urldata.target;
				}

				if (urldata.title) {
					this.data[this.selTab].title = urldata.title;
				}

				if (urldata.anchor) {
					this.data[this.selTab].anchor = urldata.anchor;
				}

				this.data[0].newlink = this.data[1].newlink =
					this.data[2].newlink = false;
			}

		};

		this.prepareData(arg.link);

		this.tabPanel = new SUI.TabPanel({
			tabs: [
				{ title: SUI.editor.i18n.htmleditor.linkDialog.tabExternalLink },
				{ title: SUI.editor.i18n.htmleditor.linkDialog.tabInternalLink },
				{ title: SUI.editor.i18n.htmleditor.linkDialog.tabFileLink }
			],
			selected: (this.selTab)
		});

		var ctlTop = this.MARGIN;

		this.inpExtURL = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH + 75 + this.MARGIN,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.inpExtURL.el().value = this.data[0].domPrtPthQry;

		this.selExtURL = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: 75,
			options: [{
				 value: "",
					text: SUI.editor.i18n.htmleditor.linkDialog.txtElse
				}].concat(this.protocols)
		});
		this.selExtURL.el().value = this.data[0].protocol;
		this.lblExtURL = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtLink,
			forBox: this.selExtURL
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpExtTitle = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.inpExtTitle.el().value = this.data[0].title || "";
		this.lblExtTitle = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtTitle,
			forBox: this.inpExtTitle
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selExtAnchor = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: this.SEL_ANCHOR_WIDTH,
			options: [{
				value:"",
				text:"["+SUI.editor.i18n.htmleditor.linkDialog.txtNA+"]"
			}]
		});
		this.lblExtAnchor = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtAnchor,
			forBox: this.selExtAnchor
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.chkExtWindow = new SUI.form.CheckBox({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH
		});
		this.chkExtWindow.el().checked = this.data[0].target != "";
		this.lblExtWindow = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtNewWin,
			forBox: this.chkExtWindow
		});

		this.initAnchors2(0);

		this.tabPanel.add(this.lblExtURL, 0);
		this.tabPanel.add(this.selExtURL, 0);
		this.tabPanel.add(this.inpExtURL, 0);

		this.tabPanel.add(this.lblExtTitle, 0);
		this.tabPanel.add(this.inpExtTitle, 0);

		this.tabPanel.add(this.lblExtAnchor, 0);
		this.tabPanel.add(this.selExtAnchor, 0);

		this.tabPanel.add(this.lblExtWindow, 0);
		this.tabPanel.add(this.chkExtWindow, 0);

		ctlTop = this.MARGIN;

		this.inpIntURL = new SUI.Box({
			top: ctlTop + 2,
			left: this.MARGIN + this.LABEL_WIDTH + 75 + this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});

		this.butIntURL = new SUI.form.Button({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: 75,
			height: 20,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtBrowse
		});

		this.butIntURL.span.style.top = "-2px";
		this.lblIntURL = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtLink,
			forBox: this.butIntURL
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpIntTitle = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.inpIntTitle.el().value = this.data[1].title || "";
		this.lblIntTitle = new SUI.form.Label({
			anchor: { top: true, left: true },
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtTitle,
			forBox: this.inpIntTitle
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.selIntAnchor = new SUI.form.SelectList({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: this.SEL_ANCHOR_WIDTH,
			options: [{
				value:"",
				text:"["+SUI.editor.i18n.htmleditor.linkDialog.txtNA+"]"
			}]
		});
		this.lblIntAnchor = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtAnchor,
			forBox: this.selIntAnchor
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.chkIntWindow = new SUI.form.CheckBox({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH
		});
		this.chkIntWindow.el().checked = this.data[1].target != "";
		this.lblIntWindow = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtNewWin,
			forBox: this.chkIntWindow
		});


		this.initAnchors2(1);
		this.displayUrl(1, this.inpIntURL);

		this.tabPanel.add(this.lblIntURL, 1);
		this.tabPanel.add(this.butIntURL, 1);
		this.tabPanel.add(this.inpIntURL, 1);

		this.tabPanel.add(this.lblIntTitle, 1);
		this.tabPanel.add(this.inpIntTitle, 1);

		this.tabPanel.add(this.lblIntAnchor, 1);
		this.tabPanel.add(this.selIntAnchor, 1);

		this.tabPanel.add(this.lblIntWindow, 1);
		this.tabPanel.add(this.chkIntWindow, 1);

		var ctlTop = this.MARGIN;

		this.inpFileURL = new SUI.Box({
			top: ctlTop + 2,
			left: this.MARGIN + this.LABEL_WIDTH + 75 + this.MARGIN,
			right: this.MARGIN,
			height: this.CTRL_HEIGHT,
			anchor: { right: true, left: true }
		});
		this.inpFileURL.el().disabled = true;

		this.butFileURL = new SUI.form.Button({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			width: 75,
			height: 20,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtBrowse
		});

		this.butFileURL.span.style.top = "-2px";
		this.lblFileURL = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtLink,
			forBox: this.butFileURL
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.inpFileTitle = new SUI.form.Input({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH,
			right: this.MARGIN,
			anchor: { right: true, left: true }
		});
		this.inpFileTitle.el().value = this.data[2].title || "";
		this.lblFileTitle = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtTitle,
			forBox: this.inpFileTitle
		});

		ctlTop += this.CTRL_HEIGHT + this.MARGIN;

		this.chkFileWindow = new SUI.form.CheckBox({
			top: ctlTop,
			left: this.MARGIN + this.LABEL_WIDTH
		});
		this.chkFileWindow.el().checked = this.data[2].target != "";
		this.lblFileWindow = new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH - this.MARGIN,
			title: SUI.editor.i18n.htmleditor.linkDialog.txtNewWin,
			forBox: this.chkFileWindow
		});

		this.displayUrl(2, this.inpFileURL);

		this.tabPanel.add(this.lblFileURL, 2);
		this.tabPanel.add(this.butFileURL, 2);
		this.tabPanel.add(this.inpFileURL, 2);

		this.tabPanel.add(this.lblFileTitle, 2);
		this.tabPanel.add(this.inpFileTitle, 2);

		this.tabPanel.add(this.lblFileWindow, 2);
		this.tabPanel.add(this.chkFileWindow, 2);

		this.clientPanel.add(this.tabPanel, 2);

		this.addEventListeners();

		if (arg.link && arg.link.href != "") {
			this.addExtraButton(SUI.editor.i18n.htmleditor.butRemove,
				function(e) {
					that.close();
					that.callListener("onRemove");
				}
			);
		}


	},

	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 125,
	SEL_ANCHOR_WIDTH: 200,

	addEventListeners: function() {
		var that = this;
		SUI.browser.addEventListener(this.inpExtURL.el(), "blur",
			function(e) {
				if (!that.setExternalLink()) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		SUI.browser.addEventListener(this.butIntURL.el(), "click",
			function(e) {
				if (!that.ehBrowseInternal(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);

		SUI.browser.addEventListener(this.butFileURL.el(), "click",
			function(e) {
				if (!that.ehBrowseFile(new SUI.Event(this, e))) {
					SUI.browser.noPropagation(e);
				}
			}
		);


	},

	initAnchors: function(res) {

		if (this.tabPanel.selectedTabIndex() == 0 || this.tabPanel.selectedTabIndex() == 1) {

			if (res.result != "OK") {
				new SUI.dialog.Alert({icon: "error", width: 500,
					caption: res.result, text: res.data}).show();
				return;
			}

			var div = SUI.browser.createElement();
			SUI.style.setRect(div, 10, -400, 100, 100);
			div.style.overflow = "hidden";
			div.style.backgroundColor = "#FFFFBB";
			div.innerHTML = res.data.html;
			var anchors = [{
				value:"",
				text:"["+SUI.editor.i18n.htmleditor.linkDialog.txtNA+"]"
			}];
			var alist = div.getElementsByTagName("A");
			for (var i=0; i<alist.length; i++) {
				if (alist[i].name) {
					anchors.push({value: alist[i].name, text: alist[i].name,
					checked: this.data[this.tabPanel.selectedTabIndex()].name == alist[i].name});
				}
			}
			//delete div;

			if (this.content && this.tabPanel.selectedTabIndex() == 1) {
				alist = this.content.getElementsByTagName("A");
				for (var i=0; i<alist.length; i++) {
					if (alist[i].name) {
						anchors.push({
							value: alist[i].name,
							text: alist[i].name,
							checked: this.data[this.tabPanel.selectedTabIndex()].name
								== alist[i].name
						});
					}
				}
			}

			if (this.data[this.tabPanel.selectedTabIndex()].anchor != "") {
				if (!anchors[this.data[this.tabPanel.selectedTabIndex()].anchor]) {
					anchors.push({value: this.data[this.tabPanel.selectedTabIndex()].anchor, text:
					this.data[this.tabPanel.selectedTabIndex()].anchor, checked: true});
				}
			}

			if (this.tabPanel.selectedTabIndex() == 0) {
				this.selExtAnchor.options(anchors);
			} else if (this.tabPanel.selectedTabIndex() == 1) {
				this.selIntAnchor.options(anchors);
			}
		}
	},

	initAnchors2: function(tab) {
		var that = this;
		if (this.data[tab].domPrtPthQry && (this.data[tab].protocol==="http://"
		   || this.data[tab].protocol==="https://")) {
			SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
				a: "htmleditor.anchors",
				url: this.data[tab].protocol+this.data[tab].domPrtPthQry
			}, function(e) {that.initAnchors(e);} );
		}
	},

	displayUrl: function(tab, field) {
		var that = this;
		if (this.data[tab].domPrtPthQry) {
			SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
				a: "displayURL",
				url: this.data[tab].protocol+this.data[tab].domPrtPthQry
			}, function(e) { field.el().innerHTML = e.data.url;} );
		}
	},

	setExternalLink: function() {
		var url = this.inpExtURL.el().value;
		var tmp = this.protocolSplit(url);
		if (tmp.protocol !== "") {
		 this.selExtURL.el().value = tmp.protocol;
		 this.inpExtURL.el().value = tmp.domPrtPthQry;
		} else {
			this.inpExtURL.el().value = url;
		}
		this.data[0].protocol = this.selExtURL.el().value;
		this.data[0].domPrtPthQry = this.inpExtURL.el().value;
		this.initAnchors2(0);
	},

	setInternalLink: function(pageId) {
		this.data[1].pageId = pageId;
		var url = SUI.editor.resource.internalLink+pageId;
		var tmp = this.protocolSplit(url);
		if (tmp.protocol !== "") {
			this.data[1].protocol = tmp.protocol;
			this.data[1].domPrtPthQry = tmp.domPrtPthQry;
		} else if (tmp.domPrtPthQry !== "") {
// relative is OK: TODO
			return null;
		} else {
			return null;
		}
		this.initAnchors2(1);
		this.displayUrl(1, this.inpIntURL);
	},

	setFileLink: function(assetId) {
		var url = "";
		if (assetId.type == "assetId") {
			this.data[2].assetId = assetId.value;
			url = SUI.editor.resource.assetLink+assetId.value;
		} else {
			this.data[2].assetId = 0;
			url = assetId.value;
		}
		var tmp = this.protocolSplit(url);
		if (tmp.protocol !== "") {
			this.data[2].protocol = tmp.protocol;
			this.data[2].domPrtPthQry = tmp.domPrtPthQry;
		} else if (tmp.domPrtPthQry !== "") {
// relative is OK: TODO
			return null;
		} else {
			return null;
		}
		this.displayUrl(2, this.inpFileURL);
	},


	ehBrowseInternal: function(e) {
		var that = this;
		var d = new SUI.editor.htmleditor.PageDialog({
			pageId: this.data[1].pageId,
			onOK: function(p) {
				that.setInternalLink(p.id);
			}
		});
		d.center();
		d.show();
	},

	ehBrowseFile: function(e) {
		var that = this;
		var fdlg = new SUI.editor.filedialog.FileDialog({
			assetId: this.data[2].assetId,
			onOK: function(assetId) {
				that.setFileLink(assetId);
			}
		});
		fdlg.show();
	},

	formToData: function() {
		var tabno = this.tabPanel.selectedTabIndex();
		if (tabno == 0) {
			this.data[tabno].protocol = this.selExtURL.el().value;
			this.data[tabno].domPrtPthQry = this.inpExtURL.el().value;
			this.data[tabno].title = this.inpExtTitle.el().value;
			this.data[tabno].anchor = this.selExtAnchor.el().value;
			this.data[tabno].target = !this.chkExtWindow.el().checked ? ""
				: this.data[tabno].target == ""
					? "_blank" : this.data[tabno].target;
		} else if (tabno == 1) {
			this.data[tabno].title = this.inpIntTitle.el().value;
			this.data[tabno].anchor = this.selIntAnchor.el().value;
			this.data[tabno].target = !this.chkIntWindow.el().checked ? ""
				: this.data[tabno].target == ""
					? "_blank" : this.data[tabno].target;
		} else if (tabno == 2) {
			this.data[tabno].title = this.inpFileTitle.el().value;
			this.data[tabno].target = !this.chkFileWindow.el().checked ? ""
				: this.data[tabno].target == ""
					? "_blank" : this.data[tabno].target;
		}

		this.close();
		return this.data[tabno];
	},

	protocolSplit: function(url) {
		for (var i=0; i<this.protocols.length; i++) {
		var val = this.protocols[i].value;
			if (url.substring(0, val.length).toLowerCase()    === val) {
				return {
					protocol: val,
					domPrtPthQry: url.substring(val.length)
				};
		}
		}
		return { protocol: "", domPrtPthQry: "" };
	},

	protocols: [
		{ value:"file://", text:"file" },
		{ value:"ftp://", text:"ftp" },
		{ value:"gopher://", text:"gopher" },
		{ value:"http://", text:"http" },
		{ value:"https://", text:"https" },
		{ value:"mailto:", text:"mailto" },
		{ value:"news:", text:"news" },
		{ value:"telnet:", text:"telnet" },
		{ value:"wais:", text:"wais" }
	],

	end: null

});
