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
 * $Id: Scrivo.js 842 2013-08-19 22:54:50Z geert $
 */

SUI.editor.htmleditor.suggestions = function(a) {
	window.focus();
	new SUI.editor.htmleditor.SuggestionsDialog({
		span: a,
		onOK: function(res) {
		  setTimeout(function() {
			try {
				var p = a;
				while (p && p.nodeType != 9){
					p=p.parentNode;
				}
				p.body.focus();
			} catch(e) {}
			if (res) {
			a.innerHTML = res;
			SUI.browser.removeNode(a, false);
			}
		  }, 10);
		},
		onCancel: function(res) {
			try {
				var p = a;
				while (p && p.nodeType != 9){
					p=p.parentNode;
				}
				p.body.focus();
			} catch(e) {}
		}
	}).show();
};

SUI.editor.Scrivo = SUI.defineClass({

	baseClass: SUI.AnchorLayout,

	pageId: ROOT_PAGE_ID,

	initializer: function(arg) {
		var that = this;

		SUI.editor.Scrivo.initializeBase(this, arg);

		this.splitLayout = new SUI.SplitLayout({
			north: {
				height: 88
			}
		});
		arg.parent.el().appendChild(this.splitLayout.el());

		this.splitHeader = new SUI.SplitLayout({
			south: {
				height: 36
			}
		});

		this.add(this.splitLayout);

		this.splitLayout.add(this.splitHeader, "north");

		this.headerPanelTop = new SUI.Panel({});
		this.headerPanelTop.clientBox().addClass("header");
		this.headerPanelTop.content(
			"<div><span class=\"headerlogo\">"
			+ "<span class=\"scrivologo\">scr<span class=\"it\">i</span>vo"
			+ "<sub>®</sub></span></span>"
			+ "<h1 style=\"font-style: italic\">" + arg.title + "</h1></div>"
		);
		this.splitHeader.add(this.headerPanelTop, "center");

		this.headerPanelBottom = new SUI.Panel({});
		this.headerPanelBottom.clientBox().addClass("menu");

		this.helpButton = new SUI.ToolbarButton({
			anchor: { top: true, right: true },
			right: 5,
			top: 5,
			title: SUI.editor.i18n.scrivo.openHelpWindow,
			icon: SUI.editor.resource.icnHelp,
			handler: function() {
				var w = window.open(SUI.editor.i18n.scrivo.helpLink,
					"_blank", "width=970,height=600");
				w.focus();
			}
		});
		this.headerPanelBottom.add(this.helpButton);

		this.notesButton = new SUI.ToolbarButton({
			anchor: { top: true, right: true },
			right: 32,
			top: 5,
			title: SUI.editor.i18n.scrivo.openHelpWindow,
			icon: SUI.editor.resource.contenttabs.icnScript,
			handler: function() {
				new SUI.editor.CodeDialog({
					code: that.pageId,
					onOK: function(notes) {
						//	that.stylesheet = css;
						alert(notes);
					}
				}).show();
			}
		});
		this.headerPanelBottom.add(this.notesButton);

		this.splitHeader.add(this.headerPanelBottom, "south");

		var panel = new SUI.Panel({});

		var panel2 = new SUI.Panel({ padding: new SUI.Padding(3) });

		this.frameset = new SUI.BorderLayout({
			parent: this.splitLayout,
			center: {
				minWidth: 200
			},
			west: {
				minWidth: 100,
				width: 200
			}
		});
		panel.add(panel2);
		this.splitLayout.add(panel, "center");
		panel2.add(this.frameset);

		this.accordion = new SUI.Accordion({
			items: [ { title: SUI.editor.i18n.scrivo.treeHeader } ],
			selected: 0
		});

		this.accordionPanel = new SUI.Panel({
			border: new SUI.Border(1)
		});

		this.frameset.add(this.accordionPanel, "west");
		this.accordionPanel.add(this.accordion);

		this.menu = new SUI.editor.menutree.MenuTree({
			frame: this, selected: ROOT_PAGE_ID});

		this.accordion.add(this.menu, 0);
	},

	loadPage: function(pageId) {
		var that = this;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "getTabs",
				pageId: pageId
			},
			function(res) {
				that.pageId = pageId;
				that.createTabs(pageId, res.data);
			}
		);
	},

	lastTab: null,
	createTabs: function(pageId, data) {
		var that = this;
		delete this.tabs;
		this.lastTab = null;
		var tabs = [];
		for (var i=0; i<data.tabs.length; i++) {
			tabs.push({
				title: data.tabs[i].title,
				extraInfo: {
					type: data.tabs[i].tab,
					pageId: pageId,
					pageDefinitionTabId: data.tabs[i].pageDefinitionTabId
				}
			});
		}

		this.tabpanel = new SUI.editor.contenttabs.TabPanel({
			tabs: tabs,
			onSelectTab: function(tab) {
				that.doSelectTab(tab);
			}
		});

		this.frameset.add(this.tabpanel, "center",
			/* onRemove */
			/* asynchronious */
			function(tp) {
				if (tp.dataModified()) {
					new SUI.dialog.Confirm({
						caption: SUI.editor.i18n.scrivo.dataModified,
						text: SUI.editor.i18n.scrivo.saveChanges,
						onOK: function() {
							tp.saveData();
							that.frameset.finishAdd();
							that.frameset.draw();
						},
						onCancel: function() {
							that.frameset.finishAdd();
							that.frameset.draw();
						}
					}).show();
				} else {
					that.frameset.finishAdd();
				}
			}
			/* blocking
			function(fset) {
				if (confirm()) {
					fset.removeAndAdd();
				}
			} */
		);
		this.tabpanel.selectedTabIndex(data.defaultTab);
		this.doSelectTab(this.tabpanel.selectedTab());
		this.frameset.draw();
	},

	doSelectTab: function(tab) {

		var that = this;

		if (this.lastTab && this.lastTab.extraInfo.panel) {
			this.lastTab.content.remove(this.lastTab.extraInfo.panel);
		}

		if (tab.extraInfo.type == "default") {

			tab.extraInfo.panel =
					new SUI.editor.contenttabs.DefaultPanel({
				pageId: tab.extraInfo.pageId,
				pageDefinitionTabId: 0,
				border: new SUI.Border(1),
				onLoad: function() {
					that.tabpanel.draw();
				}
			});

		} else if (tab.extraInfo.type == "content") {

			tab.extraInfo.panel =
					new SUI.editor.contenttabs.HTMLEditorPanel({
				pageId: tab.extraInfo.pageId,
				pageDefinitionTabId: tab.extraInfo.pageDefinitionTabId,
				border: new SUI.Border(1),
				onLoad: function() {
					that.tabpanel.draw();
				}
			});

		} else if (tab.extraInfo.type == "properties") {

			tab.extraInfo.panel =
					new SUI.editor.contenttabs.PropertyPanel({
				pageId: tab.extraInfo.pageId,
				pageDefinitionTabId: tab.extraInfo.pageDefinitionTabId,
				border: new SUI.Border(1),
				onLoad: function() {
					that.tabpanel.draw();
				}
			});

		} else if (tab.extraInfo.type == "application") {

			tab.extraInfo.panel =
					new SUI.editor.contenttabs.ApplicationPanel({
				pageId: tab.extraInfo.pageId,
				pageDefinitionTabId: tab.extraInfo.pageDefinitionTabId,
				border: new SUI.Border(1),
				onLoad: function() {
					that.tabpanel.draw();
				}
			});

		}

		tab.content.add(tab.extraInfo.panel);
		this.lastTab = tab;
	}

});
