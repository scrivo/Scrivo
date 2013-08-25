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
 * $Id: HTMLEditor.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.HTMLEditor = SUI.defineClass({

	baseClass: SUI.Box,

	initializer: function(arg) {

		SUI.editor.htmleditor.HTMLEditor.initializeBase(this, arg);
		var that = this;

		this.tableEditor =
			new SUI.editor.htmleditor.tableeditor.TableEditor({});

		this.anchor = { left: true, right: true, top: true, bottom: true };

		if (arg.onSave) {
			this.onSave = arg.onSave;
		}
		if (arg.onLoad) {
			this.onLoad = arg.onLoad;
		}

		this._pageId = arg.pageId || 0;

		this.actionList = new SUI.ActionList([{
				actionId: "htmleditor.save",
				title: this.i18n.actSave,
				icon: this.resource.icnSave,
				handler: function(c) { that.actSave(c); }
			},{
				actionId: "htmleditor.cut",
				title: this.i18n.actCut,
				icon: this.resource.icnCut,
				handler: function(c) { that.actCut(c); }
			},{
				actionId: "htmleditor.copy",
				title: this.i18n.actCopy,
				icon: this.resource.icnCopy,
				handler: function(c) { that.actCopy(c); }
			},{
				actionId: "htmleditor.paste",
				title: this.i18n.actPaste,
				icon: this.resource.icnPaste,
				handler: function(c) { that.actPaste(c); }
			},{
				actionId: "htmleditor.undo",
				title: this.i18n.actUndo,
				icon: this.resource.icnUndo,
				handler: function(c) { that.actUndo(c); }
			},{
				actionId: "htmleditor.redo",
				title: this.i18n.actRedo,
				icon: this.resource.icnRedo,
				handler: function(c) { that.actRedo(c); }
			},{
				actionId: "htmleditor.table",
				title: this.i18n.actTable,
				icon: this.resource.icnTable,
				handler: function(c) { that.actTable(c); }
			},{
				actionId: "htmleditor.find",
				title: this.i18n.actFind,
				icon: this.resource.icnFind,
				handler: function(c) { that.actFind(c); }
			},{
				actionId: "htmleditor.rowcolum",
				title: this.i18n.actRowColumn,
				icon: this.resource.icnRowColumn,
				handler: function(c) { that.actRowColumn(c); }
			},{
				actionId: "htmleditor.splitmergecell",
				title: this.i18n.actSplitMergeCell,
				icon: this.resource.icnSplitMergeCell,
				handler: function(c) { that.actSplitMergeCell(c); }
			},{
				actionId: "htmleditor.cellproperties",
				title: this.i18n.actCellProperties,
				icon: this.resource.icnCellProperties,
				handler: function(c) { that.actCellProperties(c); }
			},{
				actionId: "htmleditor.link",
				title: this.i18n.actLink,
				icon: this.resource.icnLink,
				handler: function(c) { that.actLink(c); }
			},{
				actionId: "htmleditor.image",
				title: this.i18n.actImage,
				icon: this.resource.icnImage,
				handler: function(c) { that.actImage(c); }
			},{
				actionId: "htmleditor.language",
				title: this.i18n.actLanguage,
				icon: this.resource.icnLanguage,
				handler: function(c) { that.actLanguage(c); }
			},{
				actionId: "htmleditor.abbreviation",
				title: this.i18n.actAbbreviation,
				icon: this.resource.icnAbbreviation,
				handler: function(c) { that.actAbbreviation(c); }
			},{
				actionId: "htmleditor.anchor",
				title: this.i18n.actAnchor,
				icon: this.resource.icnAnchor,
				handler: function(c) { that.actAnchor(c); }
			},{
				actionId: "htmleditor.pagebreak",
				title: this.i18n.actPageBreak,
				icon: this.resource.icnPageBreak,
				handler: function(c) { that.actPageBreak(c); }
			},{
				actionId: "htmleditor.insertsymbol",
				title: this.i18n.actInsertSymbol,
				icon: this.resource.icnSymbol,
				handler: function(c) { that.actInsertSymbol(c); }
			},{
				actionId: "htmleditor.hilightcolor",
				title: this.i18n.actHighlightColor,
				icon: this.resource.icnHighlightColor,
				handler: function(c) { that.actHighlightColor(c); }
			},{
				actionId: "htmleditor.textcolor",
				title: this.i18n.actTextColor,
				icon: this.resource.icnTextColor,
				handler: function(c) { that.actTextColor(c); }
			},{
				actionId: "htmleditor.removeformatting",
				title: this.i18n.actRemoveFormatting,
				icon: this.resource.icnRemoveFormatting,
				handler: function(c) { that.actRemoveFormatting(c); }
			},{
				actionId: "htmleditor.spellcheck",
				title: this.i18n.actSpellCheck,
				icon: this.resource.icnSpellCheck,
				handler: function(c) { that.actSpellCheck(c); }
			},{
				actionId: "htmleditor.html",
				title: this.i18n.actHTML,
				icon: this.resource.icnHTML,
				handler: function(c) { that.actHTML(c); }
			},{
				actionId: "htmleditor.bold",
				title: this.i18n.actBold,
				icon: this.resource.icnBold,
				handler: function(c) { that.actBold(c); }
			},{
				actionId: "htmleditor.italic",
				title: this.i18n.actItalic,
				icon: this.resource.icnItalic,
				handler: function(c) { that.actItalic(c); }
			},{
				actionId: "htmleditor.underline",
				title: this.i18n.actUnderline,
				icon: this.resource.icnUnderLine,
				handler: function(c) { that.actUnderline(c); }
			},{
				actionId: "htmleditor.alignleft",
				title: this.i18n.actAlignLeft,
				icon: this.resource.icnAlignLeft,
				handler: function(c) { that.actAlignLeft(c); }
			},{
				actionId: "htmleditor.alignright",
				title: this.i18n.actAlignRight,
				icon: this.resource.icnAlignRight,
				handler: function(c) { that.actAlignRight(c); }
			},{
				actionId: "htmleditor.aligncenter",
				title: this.i18n.actAlignCenter,
				icon: this.resource.icnAlignCenter,
				handler: function(c) { that.actAlignCenter(c); }
			},{
				actionId: "htmleditor.orderedlist",
				title: this.i18n.actOrderedList,
				icon: this.resource.icnOrderedList,
				handler: function(c) { that.actOrderedList(c); }
			},{
				actionId: "htmleditor.unorderedlist",
				title: this.i18n.actUnorderedList,
				icon: this.resource.icnUnorderedList,
				handler: function(c) { that.actUnorderedList(c); }
			},{
				actionId: "htmleditor.indent",
				title: this.i18n.actIndent,
				icon: this.resource.icnIndent,
				handler: function(c) { that.actIndent(c); }
			},{
				actionId: "htmleditor.deindent",
				title: this.i18n.actDeIndent,
				icon: this.resource.icnDeIndent,
				handler: function(c) { that.actDeIndent(c); }
			}]
		);

		this.el().style.backgroundColor = "white";

		var tools = [
			new SUI.ToolbarButton({actionId: "htmleditor.save"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.cut"}),
			new SUI.ToolbarButton({actionId: "htmleditor.copy"}),
			new SUI.ToolbarButton({actionId: "htmleditor.paste"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.undo"}),
			new SUI.ToolbarButton({actionId: "htmleditor.redo"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.find"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.table"}),
			new SUI.ToolbarButton({actionId: "htmleditor.rowcolum"}),
			new SUI.ToolbarButton({actionId: "htmleditor.splitmergecell"}),
			new SUI.ToolbarButton({actionId: "htmleditor.cellproperties"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.link"}),
			new SUI.ToolbarButton({actionId: "htmleditor.image"}),
			new SUI.ToolbarButton({actionId: "htmleditor.language"}),
			new SUI.ToolbarButton({actionId: "htmleditor.abbreviation"}),
			new SUI.ToolbarButton({actionId: "htmleditor.anchor"}),
			new SUI.ToolbarButton({actionId: "htmleditor.pagebreak"}),
			new SUI.ToolbarButton({actionId: "htmleditor.insertsymbol"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.hilightcolor"}),
			new SUI.ToolbarButton({actionId: "htmleditor.textcolor"}),
			new SUI.ToolbarButton({actionId: "htmleditor.removeformatting"}),
			new SUI.ToolbarSeparator({}),
			new SUI.ToolbarButton({actionId: "htmleditor.spellcheck"}),
			new SUI.ToolbarButton({actionId: "htmleditor.html"})
		];

		if (!arg.saveButton) {
			tools.splice(0,2);

		}
		this.toolbar1 = new SUI.Toolbar({
			parent: this,
			actionlist: this.actionList,
			tools: tools
		});

		this.blockFormats = new SUI.form.SelectList({
			width: 90,
			options: [
				{ value: "", text: this.i18n.selFormat },
				{ value: "P", text: this.i18n.selParagraph },
				{ value: "H1", text: this.i18n.selHeading1 },
				{ value: "H2", text: this.i18n.selHeading2 },
				{ value: "H3", text: this.i18n.selHeading3 },
				{ value: "H4", text: this.i18n.selHeading4 },
				{ value: "H5", text: this.i18n.selHeading5 },
				{ value: "H6", text: this.i18n.selHeading6 }
			]
		});
		SUI.browser.addEventListener(this.blockFormats.el(), "change", function(e) {
			if (!that.ehSetBlockFormat(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

		this.fonts = new SUI.form.SelectList({
			width: 130,
			options: [
				{ value: "", text: this.i18n.selFont
				},{
					value: "Arial, Verdana, sans-serif",
					text: this.i18n.selArial
				},{
					value: "Arial Black, Arial, sans-serif",
					text: this.i18n.selArialBlack
				},{
					value: "Arial Narrow, Arial, sans-serif",
					text: this.i18n.selArialNarrow
				},{
					value: "Comic Sans MS, fantasy",
					text: this.i18n.selComic
				},{
					value: "Courier New, monospace",
					text: this.i18n.selCourier
				},{
					value: "System, sans-serif",
					text: this.i18n.selSystem
				},{
					value: "Tahoma, sans-serif",
					text: this.i18n.selTahoma
				},{
					value: "Times New Roman, serif",
					text: this.i18n.selTimes
				},{
					value: "Verdana, Arial, sans-serif",
					text: this.i18n.selVerdana
				},{
					value: "Wingdings, fantasy",
					text: this.i18n.selWingdings
				},{
					value: "Webdings, fantasy",
					text: this.i18n.selWebdings
				}
			]
		});
		SUI.browser.addEventListener(this.fonts.el(), "change", function(e) {
			if (!that.ehSetFont(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

		this.fontSizes = new SUI.form.SelectList({
			width: 80,
			options: [
				{ value: "", text: this.i18n.selFontSize },
				{ value: "50%", text: "50%" },
				{ value: "70%", text: "70%" },
				{ value: "80%", text: "80%" },
				{ value: "90%", text: "90%" },
				{ value: "100%", text: "100%" },
				{ value: "110%", text: "110%" },
				{ value: "120%", text: "120%" },
				{ value: "150%", text: "150%" },
				{ value: "170%", text: "170%" },
				{ value: "200%", text: "200%" },
				{ value: "240%", text: "240%" },
				{ value: "280%", text: "280%" },
				{ value: "320%", text: "320%" }
			]
		});
		SUI.browser.addEventListener(this.fontSizes.el(), "change", function(e) {
			if (!that.ehSetFontSize(new SUI.Event(this, e))) {
				SUI.browser.noPropagation(e);
			}
		});

		this.toolbar2 = new SUI.Toolbar({
			parent: this,
			actionlist: this.actionList,
			tools: [
				this.blockFormats,
				this.fonts,
				this.fontSizes,
				new SUI.ToolbarButton({actionId: "htmleditor.bold"}),
				new SUI.ToolbarButton({actionId: "htmleditor.italic"}),
				new SUI.ToolbarButton({actionId: "htmleditor.underline"}),
				new SUI.ToolbarSeparator({}),
				new SUI.ToolbarButton({actionId: "htmleditor.alignleft"}),
				new SUI.ToolbarButton({actionId: "htmleditor.alignright"}),
				new SUI.ToolbarButton({actionId: "htmleditor.aligncenter"}),
				new SUI.ToolbarSeparator({}),
				new SUI.ToolbarButton({actionId: "htmleditor.orderedlist"}),
				new SUI.ToolbarButton({actionId: "htmleditor.unorderedlist"}),
				new SUI.ToolbarSeparator({}),
				new SUI.ToolbarButton({actionId: "htmleditor.indent"}),
				new SUI.ToolbarButton({actionId: "htmleditor.deindent"})
			]
		});

		//console.log(this.blockFormats.height());
		var t =
			(this.toolbar2.clientHeight() - this.blockFormats.height()) / 2 | 0;
		this.blockFormats.top(t);
		this.fonts.top(t);
		this.fontSizes.top(t);

		this.editCtrl = new SUI.control.HTMLEditControl({
			ieStrictMode: false,
			parent: this,
			onCommandExecuted: function() {
//                if (that.editCtrl.getDocument().hasFocus) {
					that.enableButtons();
//                }
			},
			onSelectionChange: function() {
//                if (that.editCtrl.getDocument().hasFocus) {
					that.enableButtons();
//                }
			},
			onLoad: function() {
				that.onLoad();
				that.enableButtons();

				if (SUI.browser.isIE && that.editCtrl.ieStrictMode()) {
					// IE Strict mode patch. The editor content is rendered
					// in strict mode making the body tag size to its contents.
					// If the user clicks outside of the body the html element
					// will be selected and no cursor will appear. When the
					// user clicks on the HTML element set the cursor to the
					// end of the document and focus the body.
					var doc = that.editCtrl.getDocument();
					doc.onclick = function() {
						var e = that.editCtrl._editWin.event;
						if (e && e.srcElement.tagName == "HTML") {
							var range = doc.selection.createRange();
							range.moveToElementText(doc.body);
							range.collapse(false);
							range.select();
							doc.body.focus();
						}
					};
					// IE8 font-face patch. Sometimes (hard to reproduce) a CSS
					// @font-faces set somewhere int the styles of the content
					// editor (what tag: html, body and/or others?) will cause
					// the parent frame to use that font too. Hopefully
					// resetting the stylesheets will correct this situation.
					var sheets = document.styleSheets;
					for(var s = 0; s < sheets.length; s++) {
						sheets[s].disabled = true;
						sheets[s].disabled = false;
					}
				}

			}
		});


		this.editCtrl.initBlockFormats(this.blockFormats.el().options);

		this.setValue = function(val) {
	//        this.editCtrl.resetIframe();
			this.editCtrl.setValue(val);
		};

		this.getValue = function() {
			var v = "";
			try {
				v = this.editCtrl.getValue();
			} catch (e) {
				// It is reasonably save to ignore the "getValue failed
				// because the editor was not fully loaded yet" error.
			}
			return v;
		};

		this.layOut = function() {

			this.toolbar1.width(this.width());
			this.toolbar2.width(this.width());
			this.toolbar2.top(this.toolbar1.height());

			var ctrlTop = this.toolbar1.height() + this.toolbar2.height();

			this.editCtrl.top(ctrlTop);
			this.editCtrl.width(this.width());
			this.editCtrl.height(this.height() - ctrlTop);

			SUI.editor.htmleditor.HTMLEditor.parentMethod(this, "layOut");
			this.toolbar1.layOut();
			this.toolbar2.layOut();
			this.editCtrl.layOut();
		};

		this.display = function() {
			SUI.editor.htmleditor.HTMLEditor.parentMethod(this, "display");
			this.toolbar1.display();
			this.toolbar2.display();
			this.editCtrl.display();
		};

	},

	i18n: SUI.editor.i18n.htmleditor.htmlEditor,

	resource: SUI.editor.resource.htmleditor,

	pasteMethod: "plain",

	onLoad: function() {},

	enableButtons: function() {

		var elem = null;
		try {
			var elem = this.editCtrl.getSelectedElement();
		} catch (err) {
			//console.log("SUI.editor.htmleditor.HTMLEditor.enableButtons");
			return;
		}

		SUI.editor.htmleditor.tableeditor.highlightCells(this,
			this.editCtrl.getDocument(), elem);

		var enabled = this.editCtrl.commandsEnabled();
		this.actionList.enable({
			"htmleditor.cut": enabled.cut,
			"htmleditor.copy": enabled.copy,
			"htmleditor.paste": enabled.paste,
			"htmleditor.undo": enabled.undo,
			"htmleditor.redo": enabled.redo,
			"htmleditor.table": true,
			"htmleditor.rowcolum": this.tableEditor.table,
			"htmleditor.splitmergecell": this.tableEditor.table,
			"htmleditor.cellproperties": this.tableEditor.table,
			"htmleditor.link": enabled.link,
			"htmleditor.image": enabled.image,
			"htmleditor.anchor": enabled.anchor,
			"htmleditor.pagebreak": enabled.pageBreak,
			"htmleditor.orderedlist": enabled.orderedList,
			"htmleditor.unorderedlist": enabled.unorderedList,
			"htmleditor.indent": enabled.indent,
			"htmleditor.deindent": enabled.deIndent
		});

		var states = this.editCtrl.commandStates();
		this.actionList.select({
			"htmleditor.bold": states.bold,
			"htmleditor.underline": states.underline,
			"htmleditor.italic": states.italic,
			"htmleditor.alignleft": states.alignLeft,
			"htmleditor.aligncenter": states.alignCenter,
			"htmleditor.alignright": states.alignRight,
			"htmleditor.textcolor": states.textColor,
			"htmleditor.unorderedlist": states.insertUnorderedList,
			"htmleditor.orderedlist": states.insertOrderedList
		});

		this.blockFormats.el().value = this.editCtrl.getCurrentBlockFormat();

	},

	focus: function() {
		this.enableButtons();
		this.editCtrl.focus();
	},

	ehSetBlockFormat: function(e) {
		this.editCtrl.doFormatBlock(e.elListener.value);
	},

	ehSetFont: function(e) {
		this.editCtrl.doFontName(this.fonts.el().value);
		this.fonts.el().selectedIndex = 0;
	},

	ehSetFontSize: function(e) {
		this.editCtrl.doFontSize(this.fontSizes.el().value);
		this.fontSizes.el().selectedIndex = 0;
	},

	filterSpell: function() {
		do {
			var l = this.editCtrl.getDocument().getElementsByTagName("SPAN");
			var counter = 0;
			for (var i=0; i<l.length; i++) {
				if (l[i].className.substr(0, 9) == "sys_spell") {
					SUI.browser.removeNode(l[i], false);
					counter++;
				}
			}
		} while (counter > 0);
	},

	onSave: function() {},

	actSave: function(c) {
		this.filterSpell();
		this.onSave();
	},

	actCut: function(c) {
		var that = this;
		new SUI.dialog.Alert({
			width: 300,
			caption: this.i18n.browserUnsupportedCmd,
			text: "<p>" + this.i18n.browserSecurityWarningCut[0].replace(
				"{BR}",
				"</p><p>").replace(
					"{SC}",
					"<strong>" + this.i18n.browserSecurityWarningCut[1]
						+ "</strong>"
				) + "</p>",
			onOK: function() {
				that.focus();
			}
		}).show();
	},

	actCopy: function(c) {
		var that = this;
		new SUI.dialog.Alert({
			width: 300,
			caption: this.i18n.browserUnsupportedCmd,
			text: "<p>" + this.i18n.browserSecurityWarningCopy[0].replace(
				"{BR}",
				"</p><p>").replace(
					"{SC}",
					"<strong>" + this.i18n.browserSecurityWarningCopy[1]
						+ "</strong>"
				) + "</p>",
			onOK: function() {
				that.focus();
			}
		}).show();
	},

	actPaste: function(c) {
		var that = this;
		new SUI.editor.htmleditor.PasteDialog({
			pasteMethod: this.editCtrl.pasteMethod(),
			caption: this.i18n.browserUnsupportedCmd,
			text: "<p>" + (this.i18n.browserSecurityWarningPaste[0]
					+ this.i18n.browserPasteMethod).replace(
					/{BR}/g,
					"</p><p>").replace(
						"{SC}",
						"<strong>" + this.i18n.browserSecurityWarningPaste[1]
						+ "</strong>"
				) + "</p>",
			onOK: function(data) {
				that.editCtrl.pasteMethod(data);
				that.focus();
			}
		}).show();
/*
		new SUI.dialog.Alert({
			width: 300,
			caption: this.i18n.browserUnsupportedCmd,
			text: "<p>" + this.i18n.browserSecurityWarningPaste[0].replace(
					"{BR}",
					"</p><p>").replace(
						"{SC}",
						"<strong>" + this.i18n.browserSecurityWarningPaste[1]
						+ "</strong>"
				) + "</p>",
			onOK: function() {
				that.focus();
			}
		}).show();
*/
	},

	actUndo: function(c) {
		this.editCtrl.doUndo();
	},

	actRedo: function(c) {
		this.editCtrl.doRedo();
	},

	actFind: function(c) {
		var that = this;

		new SUI.editor.htmleditor.FindDialog({

			onFind: function(data) {
				var res = that.editCtrl.doFind(data.find, data.matchCase,
					!data.directionDown, data.wholeWords);
				if (!res) {
					new SUI.dialog.Alert({width: 300,
						text: that.i18n.txtNotFound}).show();
					return;
				}
			},

			onReplace: function(data) {
				that.editCtrl.doReplace(data.replace);
			},

			onReplaceAll: function(data) {
				var cnt = that.editCtrl.doReplaceAll(data.find, data.replace,
					data.matchCase, !data.directionDown, data.wholeWords);
				var str = that.i18n.txtNotFound;
				if (cnt) {
					var str =
						that.i18n.txtOccurencesReplaced.replace("[]", cnt);
				}
				new SUI.dialog.Alert({width: 300, text: str,
					onOK: function() {
						that.focus();
					}
				}).show();
			},

			onCancel: function() {
				that.focus();
			}

		}).show();

	},

	actTable: function(c) {


		var elem = this.editCtrl.getSelectedElement();

		var te = SUI.editor.htmleditor.tableeditor;

		this.tableEditor.doc = this.editCtrl.getDocument();
		this.tableEditor.set(elem);

		var args = {};

		args.newTable = true;
		if (this.tableEditor.table) {
			args.newTable = false;
		} else {
			// why? just did this
			this.tableEditor.doc = this.editCtrl.getDocument();
		}

		this.tableEditor.getStyles();
		args.styles = this.tableEditor.styles;

		if (!args.newTable) {
			args.className = this.tableEditor.table.className;
		}

		args.numrows = args.newTable ? 3 : null;
		args.numcols = args.newTable ? 3 : null;

		args.leftborderstyle = args.newTable
			? null : this.tableEditor.table.style.borderLeftStyle;
		args.rightborderstyle = args.newTable
			? null : this.tableEditor.table.style.borderRightStyle;
		args.topborderstyle = args.newTable
			? null : this.tableEditor.table.style.borderTopStyle;
		args.bottomborderstyle = args.newTable
			? null : this.tableEditor.table.style.borderBottomStyle;

		args.leftbordercolor = args.newTable
			? null : this.tableEditor.table.style.borderLeftColor;
		args.rightbordercolor = args.newTable
			? null : this.tableEditor.table.style.borderRightColor;
		args.topbordercolor = args.newTable
			? null : this.tableEditor.table.style.borderTopColor;
		args.bottombordercolor = args.newTable
			? null : this.tableEditor.table.style.borderBottomColor;

		args.leftborderwidth = args.newTable
			? "" : this.tableEditor.table.style.borderLeftWidth;
		args.rightborderwidth =    args.newTable
			? "" : this.tableEditor.table.style.borderRightWidth;
		args.topborderwidth =  args.newTable
			? "" : this.tableEditor.table.style.borderTopWidth;
		args.bottomborderwidth = args.newTable
			? "" : this.tableEditor.table.style.borderBottomWidth;

		args.bgcolor = args.newTable
			? null : this.tableEditor.table.style.backgroundColor;
		args.tablewidth = args.newTable
			? "50%" : this.tableEditor.table.style.width;
		args.spacing = args.newTable
			? 0 : this.tableEditor.table.cellSpacing;

		args.caption = "";
		if (!args.newTable && this.tableEditor.table.caption) {
			args.caption = this.tableEditor.table.caption.innerText;
		}
		args.summary = args.newTable ? "" : this.tableEditor.table.summary;

		var that = this;
		new SUI.editor.htmleditor.TableDialog({
			args: args,
			onOK: function(data) {

				var te = SUI.editor.htmleditor.tableeditor;

				if (data.newTable) {
					that.tableEditor.table =
						SUI.editor.htmleditor.tableeditor._createTable(
							that.tableEditor.doc, data.numrows, data.numcols);
				}

				that.tableEditor.table.style.borderLeftStyle =
					args.leftborderstyle;
				that.tableEditor.table.style.borderRightStyle =
					args.rightborderstyle;
				that.tableEditor.table.style.borderTopStyle =
					args.topborderstyle;
				that.tableEditor.table.style.borderBottomStyle =
					args.bottomborderstyle;

				that.tableEditor.table.style.borderLeftColor =
					args.leftbordercolor;
				that.tableEditor.table.style.borderRightColor =
					args.rightbordercolor;
				that.tableEditor.table.style.borderTopColor =
					args.topbordercolor;
				that.tableEditor.table.style.borderBottomColor =
					args.bottombordercolor;

				that.tableEditor.table.style.borderLeftWidth =
					args.leftborderwidth;
				that.tableEditor.table.style.borderRightWidth =
					args.rightborderwidth;
				that.tableEditor.table.style.borderTopWidth =
					args.topborderwidth;
				that.tableEditor.table.style.borderBottomWidth =
					args.bottomborderwidth;

				that.tableEditor.table.style.backgroundColor = args.bgcolor;
				that.tableEditor.table.cellSpacing = data.spacing;
				that.tableEditor.table.style.width = args.tablewidth;

				that.tableEditor.table.deleteCaption();
				if (data.caption != "") {
					capt = that.tableEditor.table.createCaption();
					capt.innerText = data.caption;
				}
				that.tableEditor.table.summary = data.summary;

				if (data.headers == "rowcol" || data.headers == "col"
						|| data.headers == "row" || data.headers == "none") {
					var row =
						(data.headers == "row" || data.headers == "rowcol");
					var col =
						(data.headers == "col" || data.headers == "rowcol");
					SUI.editor.htmleditor.tableeditor._setHeads(
						that.tableEditor, col, row);

					if (data.className != "keep_styles") {
						SUI.style.setClass(
							that.tableEditor.table, data.className);
						SUI.editor.htmleditor.tableeditor._setCellStyles(
							that.tableEditor, col, row, data.className);
					}
				}

				if (data.newTable) {
					that.editCtrl.doInsertNode(that.tableEditor.table);
				}

				SUI.editor.htmleditor.tableeditor.highlightCells(that,
					that.tableEditor.doc, that.tableEditor.table);

			},
			onCancel: function() {
				that.focus();
			}
		}).show();
	},

	actRowColumn: function(c) {
		var that = this;
		new SUI.editor.htmleditor.TableRowColDialog({
			onOK: function(action) {
				switch (action) {
				case "insrowbelow":
					that.tableEditor.insertRow(false);
					break;
				case "insrowabove":
					that.tableEditor.insertRow(true);
					break;
				case "delrow":
					that.tableEditor.deleteRow();
					break;
				case "inscolleft":
					that.tableEditor.insertColumn(true);
					break;
				case "inscolright":
					that.tableEditor.insertColumn(false);
					break;
				case "delcolumn":
					that.tableEditor.deleteColumn();
					break;
				}

				SUI.editor.htmleditor.tableeditor.highlightCells(that,
					that.tableEditor.doc, that.tableEditor.table);
			}
		}).show();
	},

	actSplitMergeCell: function(c) {

		var args = {};
		var that = this;

		args.right = this.tableEditor.canMergeHorizontal(false);
		args.left = this.tableEditor.canMergeHorizontal(true);
		args.down = this.tableEditor.canMergeVertical(false);
		args.up = this.tableEditor.canMergeVertical(true);

		new SUI.editor.htmleditor.TableSplitMergeDialog({
			args: args,
			onOK: function(action) {
				switch (action) {
				case "splitcellhoriz":
					that.tableEditor.splitCell(true);
					break;
				case "splitcellvert":
					that.tableEditor.splitCell(false);
					break;
				case "mergecellright":
					that.tableEditor.mergeCells(true, false);
					break;
				case "mergecellleft":
					that.tableEditor.mergeCells(true, true);
					break;
				case "mergecelldown":
					that.tableEditor.mergeCells(false, false);
					break;
				case "mergecellup":
					that.tableEditor.mergeCells(false, true);
					break;
				}

				SUI.editor.htmleditor.tableeditor.highlightCells(that,
					that.tableEditor.doc, that.tableEditor.table);
			}
		}).show();
	},

	actCellProperties: function(c) {

		var elem = this.editCtrl.getSelectedElement();

		var te = SUI.editor.htmleditor.tableeditor;

		this.tableEditor.doc = this.editCtrl.getDocument();
		this.tableEditor.set(elem);

		var args = {};

		if (!this.tableEditor.cell) {
			var that = this;
			new SUI.dialog.Alert({icon: "error", width: 300,
				text: that.i18n.errNoCellSelected}
			).show();
			return;
		}

		args.ctype = "data";
		for (var i=0; i<this.tableEditor.shdw.hdr_arr.length; i++) {
			if (this.tableEditor.shdw.hdr_arr[i] == this.tableEditor.cell) {
				args.ctype = "header";
				break;
			}
		}

		this.tableEditor.getStyles();
		args.styles = this.tableEditor.styles;
		args.className = this.tableEditor.cell.className;

		args.leftborderstyle = this.tableEditor.cell.style.borderLeftStyle;
		args.rightborderstyle = this.tableEditor.cell.style.borderRightStyle;
		args.topborderstyle = this.tableEditor.cell.style.borderTopStyle;
		args.bottomborderstyle = this.tableEditor.cell.style.borderBottomStyle;

		args.leftbordercolor = this.tableEditor.cell.style.borderLeftColor;
		args.rightbordercolor = this.tableEditor.cell.style.borderRightColor;
		args.topbordercolor = this.tableEditor.cell.style.borderTopColor;
		args.bottombordercolor = this.tableEditor.cell.style.borderBottomColor;

		args.leftborderwidth = this.tableEditor.cell.style.borderLeftWidth;
		args.rightborderwidth = this.tableEditor.cell.style.borderRightWidth;
		args.topborderwidth = this.tableEditor.cell.style.borderTopWidth;
		args.bottomborderwidth = this.tableEditor.cell.style.borderBottomWidth;

		args.bgcolor = this.tableEditor.cell.style.backgroundColor;

		args.axes = this.tableEditor.shdw.axis;
		args.axis = this.tableEditor.cell.axis;

		args.selHeaders = [];
		args.freeHeaders = [];

		for (var i=0; i<this.tableEditor.shdw.hdr_arr.length; i++) {

			var txt= this.tableEditor.shdw.hdr_arr[i].axis ? "[" +
				this.tableEditor.shdw.hdr_arr[i].axis + "] " : "";
			txt += this.tableEditor.shdw.hdr_arr[i].innerHTML.replace(
					/(<([^>]+)>)/ig, "");

			var refs = (this.tableEditor.cell.headers
				&& this.tableEditor.shdw.hdr_arr[i].id
				&& this.tableEditor.cell.headers.search(new RegExp(
					this.tableEditor.shdw.hdr_arr[i].id,"g")) != -1);

			if (refs) {
				args.selHeaders.push({
					value: this.tableEditor.shdw.hdr_arr[i].id, text: txt
				});
			}
			else   {
				if (this.tableEditor.shdw.hdr_arr[i].id)
					args.freeHeaders.push({
						value: this.tableEditor.shdw.hdr_arr[i].id, text: txt
					});
			}
		}

		args.forcetd = false;
		args.abbr = "";
		if (args.ctype == "header") {
			args.forcetd = this.tableEditor.cell.tagName == "TD";

			if (this.tableEditor.cell.abbr)
				args.abbr = this.tableEditor.cell.abbr;
		}

		var that = this;
		new SUI.editor.htmleditor.TableCellDialog({
			args: args,
			onOK: function(data) {

				SUI.style.setClass(that.tableEditor.cell, data.className);

				that.tableEditor.cell.style.borderLeftStyle =
					data.leftborderstyle;
				that.tableEditor.cell.style.borderRightStyle =
					data.rightborderstyle;
				that.tableEditor.cell.style.borderTopStyle =
					data.topborderstyle;
				that.tableEditor.cell.style.borderBottomStyle =
					data.bottomborderstyle;

				that.tableEditor.cell.style.borderLeftColor =
					data.leftbordercolor;
				that.tableEditor.cell.style.borderRightColor =
					data.rightbordercolor;
				that.tableEditor.cell.style.borderTopColor =
					data.topbordercolor;
				that.tableEditor.cell.style.borderBottomColor =
					data.bottombordercolor;

				that.tableEditor.cell.style.borderLeftWidth =
					data.leftborderwidth;
				that.tableEditor.cell.style.borderRightWidth =
					data.rightborderwidth;
				that.tableEditor.cell.style.borderTopWidth =
					data.topborderwidth;
				that.tableEditor.cell.style.borderBottomWidth =
					data.bottomborderwidth;

				that.tableEditor.cell.style.backgroundColor =
					data.bgcolor;

				if (data.ctype == "header") {

					// header cel
					if (data.axis != "") {
						that.tableEditor.cell.axis = data.axis;
					} else {
						if (that.tableEditor.cell.axis) {
							that.tableEditor.cell.removeAttribute("axis");
						}
					}
					if (that.tableEditor.cell.headers) {
						that.tableEditor.cell.removeAttribute("headers");
					}
					if (data.abbr != "") {
						that.tableEditor.cell.abbr = data.abbr;
					}

					that.tableEditor.genDomId(that.tableEditor.cell);
					that.tableEditor.cell =
						SUI.editor.htmleditor.tableeditor.setCellType(
							that.tableEditor.doc, that.tableEditor.cell,
							!data.forcetd);

				} else {

					// data cel
					var hdrs = [];
					for (var i=0; i<data.selHeaders.length; i++) {
						hdrs.push(data.selHeaders[i]);
					}
					if (hdrs.length > 0) {
						that.tableEditor.cell.headers = hdrs.join(" ");
					} else {
						if (that.tableEditor.cell.headers) {
							that.tableEditor.cell.removeAttribute("headers");
						}
					}
					if (that.tableEditor.cell.axis) {
						that.tableEditor.cell.removeAttribute("axis");
					}
					if (that.tableEditor.cell.abbr) {
						that.tableEditor.cell.removeAttribute("abbr");
					}

					that.tableEditor.remDomId(that.tableEditor.cell);
					that.tableEditor.cell =
						SUI.editor.htmleditor.tableeditor.setCellType(
							that.tableEditor.doc, that.tableEditor.cell,
							false);

				}

				SUI.editor.htmleditor.tableeditor.highlightCells(that,
					that.tableEditor.doc, that.tableEditor.table);

			}
		}).show();

	},

	actLink: function(c) {
		var that = this;
		var link = this.editCtrl.doGetLink();
		new SUI.editor.htmleditor.LinkDialog({
			link: link,
			pageId: this._pageId,
			onOK: function(d) {
				var href = d.protocol + d.domPrtPthQry;
				if (d.anchor != "") {
					href += "#" + d.anchor;
				}
				var attr = {
					href: href,
					target: d.target != "" ? d.target : null,
					title: d.title != "" ? d.title : null
				};
				if (d.newlink) {
					that.editCtrl.doInsertLink(attr);
				} else {
					that.editCtrl.doUpdateLink(link, attr);
				}
			},
			onCancel: function() {
				that.focus();
			},
			onRemove: function() {
				if (link) {
					that.editCtrl.doRemoveLink(link);
				}
			}
		}).show();
	},

	actImage: function(c) {
		var that = this;
		var img = this.editCtrl.doGetImg();
		var    newimg = img ? false : true;
		if (newimg) {
			img = new Image();
		}
		var width = "";
		var height = "";
		if (!newimg) {
			width = parseInt(img.width, 10);
			height = parseInt(img.height, 10);
		}
		var data = {
			src: img.src == null ? "" : img.src,
			longdesc: img.getAttribute("longdesc") == null
				? "" : img.getAttribute("longdesc"),
			alt: img.alt == null ? "" : img.alt,
			title: img.title == null ? "" : img.title,
			align: img.align == null ? "" : img.align,
			width: !img ? "" : (isNaN(width) ? "" : width),
			height: !img ? "" : (isNaN(height) ? "" : height),
			borderWidth: img.border,
			marginLeft: img.style.marginLeft.replace(/[a-z]/g, ""),
			marginRight: img.style.marginRight.replace(/[a-z]/g, ""),
			marginTop: img.style.marginTop.replace(/[a-z]/g, ""),
			marginBottom: img.style.marginBottom.replace(/[a-z]/g, "")
		};
		new SUI.editor.htmleditor.ImageDialog({
			attr: data,
			onOK: function(attr) {
				SUI.browser.setAttributes(img, {
					src: attr.src != "" ? attr.src : "",
					alt: attr.alt != "" ? attr.alt : "",
					title: attr.title != "" ? attr.title : null,
					width: attr.width != "" ? attr.width : null,
					height: attr.height != "" ? attr.height : null,
					align: attr.align != "" ? attr.align : null,
					longdesc: attr.longdesc != "" ? attr.longdesc : null,
					border: attr.borderWidth != "" ? attr.borderWidth : null
				});
				img.style.marginLeft =
					attr.marginLeft != "" ? attr.marginLeft + "px" : null;
				img.style.marginRight =
					attr.marginRight != "" ? attr.marginRight + "px" : null;
				img.style.marginTop =
					attr.marginTop != "" ? attr.marginTop + "px" : null;
				img.style.marginBottom =
					attr.marginBottom != "" ? attr.marginBottom + "px" : null;
				if (newimg) {
					that.editCtrl.doInsertImg(img);
				} else {
					that.editCtrl.doUpdateImg(img);
				}
			},
			onCancel: function() {
				that.focus();
			}
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
			},
			onCancel: function() {
				that.focus();
			},
			onRemove: function() {
				if (spn) {
					that.editCtrl.doRemoveNode(spn);
				}
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
			},
			onCancel: function() {
				that.focus();
			},
			onRemove: function() {
				if (spn) {
					that.editCtrl.doRemoveNode(spn);
				}
			}
		}).show();
	},

	actAnchor: function(c) {
		var that = this;
		var a = this.editCtrl.doGetLink();
		new SUI.editor.htmleditor.AnchorDialog({
			name: (a && a.name) ? a.name : "",
			onOK: function(name) {
				if (!a) {
					that.editCtrl.doInsertAnchor(name);
				} else {
					that.editCtrl.doUpdateAnchor(a, name);
				}
			},
			onCancel: function() {
				that.focus();
			},
			onRemove: function() {
				if (a) {
					that.editCtrl.doRemoveAnchor(a);
				}
			}
		}).show();
	},

	actPageBreak: function(c) {
		this.editCtrl.doInsertHorizontalRule();
	},

	actInsertSymbol: function(c) {
		var that = this;
		new SUI.editor.htmleditor.SymbolDialog({
			onOK: function(html) {
				that.editCtrl.doInsertHTML(html);
			},
			onCancel: function() {
				that.focus();
			}
		}).show();
	},

	actHighlightColor: function(c) {
		var that = this;
		new SUI.editor.ColorDialog({
			onOK: function(col) {
				that.editCtrl.doBackColor(col);
			},
			onCancel: function() {
				that.focus();
			}
		}).show();
	},

	actTextColor: function(c) {
		var that = this;
		new SUI.editor.ColorDialog({
			onOK: function(col) {
				that.editCtrl.doForeColor(col);
			},
			onCancel: function() {
				that.focus();
			}
		}).show();
	},

	actRemoveFormatting: function(c) {
		this.editCtrl.doRemoveFormat();
	},

	actSpellCheck: function(c) {
		var that = this;
		SUI.editor.xhr.doPost(
			SUI.editor.resource.ajaxURL, {
				a: "htmleditor.spell",
				content: this.getValue(),
				lang: "NL"
			}, function(res){
				that.editCtrl.setValue(res.data);
				that.focus();
			}
		);
	},

	actHTML: function(c) {
		var that = this;
		new SUI.editor.CodeDialog({
			code: this.getValue(),
			onOK: function(html) {
				that.setValue(html);
				that.focus();
			},
			onCancel: function() {
				that.focus();
			}
		}).show();
	},

	actBold: function(c) {
		this.editCtrl.doBold();
	},

	actItalic: function(c) {
		this.editCtrl.doItalic();
	},

	actUnderline: function(c) {
		this.editCtrl.doUnderline();
	},

	actAlignLeft: function(c) {
		this.editCtrl.doJustifyLeft();
	},

	actAlignRight: function(c) {
		this.editCtrl.doJustifyRight();
	},

	actAlignCenter: function(c) {
		this.editCtrl.doJustifyCenter();
	},

	actOrderedList: function(c) {
		this.editCtrl.doInsertOrderedList();
	},

	actUnorderedList: function(c) {
		this.editCtrl.doInsertUnorderedList();
	},

	actIndent: function(c) {
		this.editCtrl.doIndent();
	},

	actDeIndent: function(c) {
		this.editCtrl.doOutdent();
	},

	/**
	 * Force an onload event to happen to on Geck based browsers (Gecko refuses
	 * to do an onload if the iframe is initially hidden, so you can use this).
	 */
	geckoForceOnloadEvent: function() {
		this.editCtrl.geckoForceOnloadEvent();
	},

	_pageId: 0

});
