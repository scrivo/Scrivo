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
 * $Id: MenuTree.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.menutree.MenuTree = SUI.defineClass({

	baseClass: SUI.Panel,

	initializer: function(arg) {

		SUI.editor.contenttabs.PropertyPanel.initializeBase(this, arg);
		var that = this;

		this.frame = arg.frame;

		if (arg.onLoad) {
			this.cbOnLoad = arg.onLoad;
		}

		var actionListData = [{
				actionId: "nav.tree.select",
				title: SUI.editor.i18n.menutree.menuTree.editPage,
				icon: SUI.editor.resource.menutree.icnEditPage,
				handler: function(c) {  that.actPageEdit(c);  }
			},{
				actionId: "nav.tree.new",
				title: SUI.editor.i18n.menutree.menuTree.newPage,
				icon: SUI.editor.resource.menutree.icnNewPage,
				handler: function(c) { that.actNewPage(c); }
			},{
				actionId: "nav.tree.up",
				title: SUI.editor.i18n.menutree.menuTree.pageUp,
				icon: SUI.editor.resource.menutree.icnUp,
				handler: function(c) { that.actPageUpDown(c, true); }
			},{
				actionId: "nav.tree.down",
				title: SUI.editor.i18n.menutree.menuTree.pageDown,
				icon: SUI.editor.resource.menutree.icnDown,
				handler: function(c) { that.actPageUpDown(c, false); }
			},{
				actionId: "nav.tree.move",
				title: SUI.editor.i18n.menutree.menuTree.pageMove,
				icon: SUI.editor.resource.menutree.icnTreeMove,
				handler: function(c) { that.actMovePage(c); }
			},{
				actionId: "nav.tree.properties",
				title: SUI.editor.i18n.menutree.menuTree.pageProperties,
				icon: SUI.editor.resource.menutree.icnProperties,
				handler: function(c) { that.actPageProperties(c); }
			},{
				actionId: "nav.tree.preview",
				title: SUI.editor.i18n.menutree.menuTree.preview,
				icon: SUI.editor.resource.menutree.icnPreview,
				handler: function(c) { that.actPreview(c); }
			},{
				actionId: "nav.tree.delete",
				title: SUI.editor.i18n.menutree.menuTree.deletePage,
				icon: SUI.editor.resource.menutree.icnDelete,
				handler: function(c) { that.actDeletePage(c); }
			}
		];

		this.actionList = new SUI.ActionList(actionListData);

		this.actionListCtx = new SUI.ActionList(actionListData);

		this.toolBar = new SUI.Toolbar({
			actionlist: this.actionList,
			tools: [
				new SUI.ToolbarButton({actionId: "nav.tree.new"}),
				new SUI.ToolbarButton({actionId: "nav.tree.preview"}),
				new SUI.ToolbarButton({actionId: "nav.tree.up"}),
				new SUI.ToolbarButton({actionId: "nav.tree.down"}),
				new SUI.ToolbarButton({actionId: "nav.tree.move"}),
				new SUI.ToolbarButton({actionId: "nav.tree.properties"}),
				new SUI.ToolbarButton({actionId: "nav.tree.delete"})
			]
		});

		this.treeContextMenu = new SUI.PopupMenu({
			actionlist: this.actionListCtx,
			items: [
				{ actionId: "nav.tree.select" },
				{ actionId: "nav.tree.new" },
				{ actionId: "nav.tree.preview" },
				{ actionId: "nav.tree.up" },
				{ actionId: "nav.tree.down" },
				{ actionId: "nav.tree.move" },
				{ actionId: "nav.tree.properties" },
				{ actionId: "nav.tree.delete" }
			]
		});

		this.treeView = new SUI.TreeView({
			top: this.toolBar.height(),
			dataUrl: SUI.editor.resource.ajaxURL+"?a=menutree.getMenu",
			xhr: function(url, callback) {
				SUI.editor.xhr.doGet(url, null, callback);
			}
		});

		this.treeView.addListener("onSelect",
		 function() {
			 that.frame.loadPage(that.treeView.selectedData().id);
		 }
		);

		this.treeView.addListener("onSelectionChange",
		 function() {
		   that.enableButtons(false, that.treeView);
		 }
		);

		this.treeView.addListener("onContextMenu",
		 function(x, y) {
			 that.enableButtonsCtx(true, this.treeView);
			 that.treeContextMenu.showMenu(y,x);
		 }
		);

		this.treeView.iconFunction(
		 function(i) {
				if (i==0) return SUI.editor.resource.menutree.icnNavigate;
				if (i==1) return SUI.editor.resource.menutree.icnPageNavigate;
				if (i==2) return SUI.editor.resource.menutree.icnPage;
				if (i==5) return SUI.editor.resource.menutree.icnTools;
				return SUI.editor.resource.menutree.icnFolder;
		 }
		);

		this.add(this.toolBar);
		this.add(this.treeView);

		this.treeView.loadData({
			pid: 0,
			openNodes: [0,1],
			selected: arg.selected
		});

	},

	enableButtonsCtx: function(ctx, tv) {
		var node = this.treeView.contextMenuData();
		this.actionListCtx.enable(this.enabledButtons(node));
	},

	enableButtons: function(ctx, tv) {
		var node = this.treeView.selectedData();
		this.actionList.enable(this.enabledButtons(node));
	},

	enabledButtons: function(node) {
		if (node) {
			return {
				"nav.tree.select": node,
				"nav.tree.new": node,
				"nav.tree.preview": node.type==1 || node.type==2,
				"nav.tree.up": node,
				"nav.tree.down": node,
				"nav.tree.move": node,
				"nav.tree.properties": node,
				"nav.tree.delete": node
			};
		}
	},

	actPageEdit: function() {
		this.treeView.selectContextMenuNode();
	},

	actPageUpDown: function(what, up) {
		var node = null;
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		if (node) {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: up?"menutree.moveUp":"menutree.moveDown",
					pageId: node.id
				},
				function(res){
					that.treeView.loadData({
						pid: 0,
						selected: that.treeView.selectedData()
							? that.treeView.selectedData().id : 0
					});
				}
			);
		}
	},

	actDeletePage: function(what) {
		var node = null;
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		if (node) {
			new SUI.dialog.Confirm({
				caption: SUI.editor.i18n.menutree.menuTree.captionDeletePage,
				text: SUI.editor.i18n.menutree.menuTree.textDeletePage,
				onOK: function() {
			SUI.editor.xhr.doGet(
				SUI.editor.resource.ajaxURL, {
					a: "menutree.deletePage",
					pageId: node.id
				},
				function(res){
					that.treeView.loadData({
						pid: 0
					});
				}
			);
		}
			}).show();
		}
	},

	actMovePage: function(what) {
		var node = null;
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		if (node) {
			var d = new SUI.editor.PageDialog({
				pageId: node.id,
				onOK: function(parent) {
					SUI.editor.xhr.doGet(
						SUI.editor.resource.ajaxURL, {
							a: "menutree.movePage",
							pageId: node.id,
							parentId: parent.id
						},
						function(res) {
							that.treeView.loadData({
								pid: 0
							});
						}
					);
				}
			});
			d.show();
		}
	},

	actPageProperties: function(what) {
		var node = null;
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		if (node) {

			new SUI.editor.menutree.PagePropertyDialog({
				pageId: node.id,
				onOK: function() {
					that.treeView.loadData({
						pid: 0,
						selected: that.treeView.selectedData()
							? that.treeView.selectedData().id : 0
					});
				}
			}).show();
		}

	},

	actNewPage: function(what) {
		var node = null;
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		if (node) {

			new SUI.editor.menutree.PagePropertyDialog({
				pagePid: node.id,
				onOK: function() {
					that.treeView.loadData({
						pid: 0,
						selected: that.treeView.selectedData()
							? that.treeView.selectedData().id : 0
					});
				}
			}).show();
		}

	},

	actPreview: function(what) {
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		if (node) {
			window.open(SUI.editor.resource.internalLink + node.id,
				"scrivo_preview");
		}

	}

});
