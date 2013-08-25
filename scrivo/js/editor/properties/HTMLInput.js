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
 * $Id: HTMLInput.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.properties.HTMLInput = SUI.defineClass({

	baseClass: SUI.editor.properties.BaseProperty,

	initializer: function(arg) {

		SUI.editor.properties.HTMLInput.initializeBase(this, arg);
		var that = this;

		var p = new SUI.Panel({
		 border: new SUI.Border(1)
		});

		this.editCtrl = new SUI.control.HTMLEditControl({
			ieStrictMode: false,
			top: 0,
			right: 0,
			anchor: {left: true, right: true, bottom: true, top: true},
					onLoad: function() { that.setCompare(); }
		});
		this.add(p);
		p.add(this.editCtrl);
		this.init();

		this.editCtrl.onContextMenu = function(x, y) {
			var p = this.absPos();
			that.contextMenu.showMenu(y + p.t, x + p.l);
		};

		this.actionList = new SUI.ActionList([{
				actionId: "comp.htmlinp.cut",
				title: SUI.editor.i18n.htmleditor.htmlEditor.actCut,
				icon: SUI.editor.resource.htmleditor.icnCut,
				handler: function(c) { that.actCut(c); }
			},{
				actionId: "comp.htmlinp.copy",
				title: SUI.editor.i18n.htmleditor.htmlEditor.actCopy,
				icon: SUI.editor.resource.htmleditor.icnCopy,
				handler: function(c) { that.actCopy(c); }
			},{
				actionId: "comp.htmlinp.paste",
				title: SUI.editor.i18n.htmleditor.htmlEditor.actPaste,
				icon: SUI.editor.resource.htmleditor.icnPaste,
				handler: function(c) { that.actPaste(c); }
			},{
				actionId: "comp.htmlinp.language",
				title: SUI.editor.i18n.htmleditor.htmlEditor.actLanguage,
				icon: SUI.editor.resource.htmleditor.icnLanguage,
				handler: function(c) { that.actLanguage(c); }
			},{
				actionId: "comp.htmlinp.symbols",
				title: SUI.editor.i18n.htmleditor.htmlEditor.actInsertSymbol,
				icon: SUI.editor.resource.htmleditor.icnSymbol,
				handler: function(c) { that.actInsertSymbol(c); }
			},{
				actionId: "comp.htmlinp.abbreviation",
				title: SUI.editor.i18n.htmleditor.htmlEditor.actAbbreviation,
				icon: SUI.editor.resource.htmleditor.icnAbbreviation,
				handler: function(c) { that.actAbbreviation(c); }
			}]
		);

		this.contextMenu = new SUI.PopupMenu({
			actionlist: this.actionList,
			items: [
				{ actionId: "comp.htmlinp.cut" },
				{ actionId: "comp.htmlinp.copy" },
				{ actionId: "comp.htmlinp.paste" },
				{ actionId: "comp.htmlinp.language" },
				{ actionId: "comp.htmlinp.symbols" },
				{ actionId: "comp.htmlinp.abbreviation" }
			]
		});

		this.editCtrl.onKeyDown = function(e) {
			if (e.event.keyCode==13) {
				if (SUI.browser.isIE) {
					window.event.returnValue = false;
				} else {
					e.event.preventDefault();
				}
			}
		};
	},

	_rmNodesRec: function(s){
		var r = 0;
		var a = { BODY:1, SPAN:1, A:1, B:1, U:1, I:1 };
		if(s.nodeType != 1) {
			return 0;
		}
		if (!a[s.tagName]) {
			SUI.browser.removeNode(s, false);
			r = 1;
		}
		for(var i=0;i<s.childNodes.length;i++)  {
			r += this._rmNodesRec(s.childNodes[i]);
		}
		return r;
	},

	getValue: function(e) {
		while (this._rmNodesRec(this.editCtrl.getDocument().body)) {
			/* void */;
		}
		var val = this.editCtrl.getValue();
		return val;

	},

	focus: function() {
		this.editCtrl.focus();
	},

	init: function() {
		this.editCtrl.resetIframe();
		this.editCtrl.setCSS(
			"html {"
			+ "  border: none;"
			+ "}"
			+ "body {"
			+ "  font-size: 80%;"
			+ "  font-family: Verdana, Arial, sans-serif;"
			+ "  background-color : white;"
			+ "  margin: 1px;"
			+ "  padding: 1px;"
			+ "  overflow: hidden;"
			+ "  white-space: nowrap;"
			+ "}"
			+ "body p {"
			+ "  white-space: nowrap;"
			+ "  padding: 0em;"
			+ "  margin: 0em"
			+ "}");
	},

	setValue: function(val) {
		this.init();
		this.editCtrl.setValue(val);
	},

		/**
	* Compare value is set onLoad
	*/
		setCompare: function() {
				this.compare = this.getValue();
		},

	actCut: function(c) {
		var that = this;
		var t = SUI.editor.i18n.htmleditor.htmlEditor;
		new SUI.dialog.Alert({
			width: 300,
			caption: t.browserUnsupportedCmd,
			text: "<p>" + t.browserSecurityWarningCut[0].replace(
				"{BR}",
				"</p><p>").replace(
					"{SC}",
					"<strong>" + t.browserSecurityWarningCut[1] + "</strong>"
				) + "</p>",
			onOK: function() { that.focus(); }
		}).show();
	},

	actCopy: function(c) {
		var that = this;
		var t = SUI.editor.i18n.htmleditor.htmlEditor;
		new SUI.dialog.Alert({
			width: 300,
			caption: t.browserUnsupportedCmd,
			text: "<p>" + t.browserSecurityWarningCopy[0].replace(
				"{BR}",
				"</p><p>").replace(
					"{SC}",
					"<strong>" + t.browserSecurityWarningCopy[1] + "</strong>"
				) + "</p>",
			onOK: function() { that.focus(); }
		}).show();
	},

	actPaste: function(c) {
		var that = this;
		var t = SUI.editor.i18n.htmleditor.htmlEditor;
		new SUI.dialog.Alert({
			width: 300,
			caption: t.browserUnsupportedCmd,
			text: "<p>" + t.browserSecurityWarningPaste[0].replace(
				"{BR}", "</p><p>").replace(
					"{SC}",
					"<strong>" + t.browserSecurityWarningPaste[1] + "</strong>"
				) + "</p>",
			onOK: function() { that.focus(); }
		}).show();
	},

	actLanguage: function(c) {
		var that = this;
		var spn = this.editCtrl.doGetLang();
		new SUI.editor.htmleditor.LanguageDialog({
			lang: spn ? spn.lang : "",
			onOK: function(lang) {
				if (!spn) {
					that.editCtrl.doInsertLang(lang);
				} else {
					that.editCtrl.doUpdateLang(spn, lang);
				}
				that.focus();
			},
			onCancel: function() {
				that.focus();
			},
			onRemove: function() {
				if (spn) {
					that.editCtrl.removeNode(spn);
				}
				that.focus();
			}
		}).show();
	},

	actAbbreviation: function(c) {
		var that = this;
		var spn = this.editCtrl.doGetAbbr();
		new SUI.editor.htmleditor.AbbreviationDialog({
			abbr: spn ? spn.title : "",
			onOK: function(abbr) {
				if (!spn) {
					that.editCtrl.doInsertAbbr(abbr);
				} else {
					that.editCtrl.doUpdateAbbr(spn, abbr);
				}
				that.focus();
			},
			onCancel: function() {
				that.focus();
			},
			onRemove: function() {
				if (spn) {
					that.editCtrl.removeNode(spn);
				}
				that.focus();
			}
		}).show();
	},

	actInsertSymbol: function(c) {
		var that = this;
		new SUI.editor.htmleditor.SymbolDialog({
			onOK: function(html) {
				that.editCtrl.doInsertHTML(html);
				that.focus();
			},
			onCancel: function() {
				that.focus();
			}
		}).show();
	},

	/**
	 * Force an onload event to happen to on Gecko based browsers (Gecko has
	 * issues cause sytem exeception in the onload handler when an onload
	 * event happens on an initially hidden contenteditable iframe).
	 */
	geckoForceOnloadEvent: function() {
		this.property.geckoForceOnloadEvent();
	}

});
