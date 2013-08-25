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
 * $Id: ListView.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.list.ListView = SUI.defineClass({

	baseClass: SUI.AnchorLayout,

	initializer: function(arg) {

		SUI.editor.apps.list.ListView.initializeBase(this, arg);
		var that = this;

		if (arg.onLoad) {
			this.cbOnLoad = arg.onLoad;
		}

		this.pageId = arg.pageId;
		this.parentIds = [];
		this.parentId = 0;
		this.parentListItemDefinitionId = 0;
		this.pagePropertyDefinitionId = arg.pagePropertyDefinitionId;
		this.applicationDefinitionId = arg.applicationDefinitionId;
		this.subItems = false;

		this.listId = 0;
		this.listItemDefinitionId = null;

		this.anchor = { left: true, right: true, top: true, bottom: true };

		this.actionList = new SUI.ActionList([{
				actionId: "list.listview.new",
				title: SUI.editor.i18n.apps.list.listView.newItem,
				icon: SUI.editor.resource.apps.list.icnNewItem,
				handler: function(c) { that.actNewItem(c); }
			},{
				actionId: "list.listview.edit",
				title: SUI.editor.i18n.apps.list.editItem,
				icon: SUI.editor.resource.apps.list.icnEditItem,
				handler: function(c) { that.actEditItem(c); }
			},{
				actionId: "list.listview.goUp",
				title: SUI.editor.i18n.apps.list.goUp,
				icon: SUI.editor.resource.apps.list.icnGoUp,
				handler: function(c) { that.actGoUp(c); }
			},{
				actionId: "list.listview.goSubList",
				title: SUI.editor.i18n.apps.list.goSubList,
				icon: SUI.editor.resource.apps.list.icnGoSubList,
				handler: function(c) { that.actGoSubList(c); }
			},{
				actionId: "list.listview.goLinkedPage",
				title: SUI.editor.i18n.apps.list.goLinkedPage,
				icon: SUI.editor.resource.apps.list.icnGoLinkedPage,
				handler: function(c) { that.actGoLinkedPage(c); }
			},{
				actionId: "list.listview.copy",
				title: SUI.editor.i18n.apps.list.copyItem,
				icon: SUI.editor.resource.apps.list.icnCopyItem,
				handler: function(c) { that.actCopyItem(c); }
			},{
				actionId: "list.listview.delete",
				title: SUI.editor.i18n.apps.list.deleteItem,
				icon: SUI.editor.resource.apps.list.icnDelete,
				handler: function(c) { that.actDelete(c); }
			}
		]);

		this.toolBar = new SUI.Toolbar({
			actionlist: this.actionList,
			tools: [
				new SUI.ToolbarButton({actionId: "list.listview.new"}),
				new SUI.ToolbarButton({actionId: "list.listview.edit"}),
				new SUI.ToolbarButton({actionId: "list.listview.goUp"}),
				new SUI.ToolbarButton({actionId: "list.listview.goSubList"}),
				new SUI.ToolbarButton({actionId: "list.listview.goLinkedPage"}),
				new SUI.ToolbarButton({actionId: "list.listview.copy"}),
				new SUI.ToolbarButton({actionId: "list.listview.delete"})
			]
		});

		this.splitSetWork = new SUI.SplitLayout({
			north: {
				height: this.toolBar.height()
			}
		});

		this.listView = null;
/*
		this.listView = new SUI.ListView({
			cols: [],
			minHeight: 100,
			minWidth: 100,
			selected: null
		});

		this.listView.onSelectionChanged = function() {
			that.enableButtons(that.listView);
		};
*/

		this.listContextMenu = new SUI.PopupMenu({
			actionlist: this.actionList,
			items: [
				{ actionId: "list.listview.edit" },
				{ actionId: "list.listview.goUp" },
				{ actionId: "list.listview.goSubList" },
				{ actionId: "list.listview.goLinkedPage" },
				{ actionId: "list.listview.copy" },
				{ actionId: "list.listview.delete" }
			]
		});

		this.splitSetWork.add(this.toolBar, "north");

		this.add(this.splitSetWork);

		this.loadData();
	},

	cbOnLoad: function() {},

	loadData: function() {
		var that = this;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "apps.list.getListView",
				pageId: this.pageId,
				applicationDefinitionId: this.applicationDefinitionId,
				parentId: this.parentId,
				parentListItemDefinitionId: this.parentListItemDefinitionId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId
			},
			function(res) {

				that.listId = res.data.listId;
				that.listItemDefinitionId = res.data.type;
				that.subItems = res.data.subItems;

				var cols = [];
				var hdrs = res.data.headers;
				for (var i=0; i<hdrs.length; i++) {
					var hdr = {
						title: hdrs[i]["LABEL"],
						key: hdrs[i]["COL_TYPE"],
						width: hdrs[i]["COL_WIDTH"],
						align: hdrs[i]["COL_ALIGN"]
					};
					if (hdrs[i]["TYPE"] === "datetime") {
						hdr.format_func = function(rw,k) {
							var dt = SUI.date.parseSqlDate(rw[k]);
							return dt ? SUI.date.format(dt, "datetime") : "";
						};
					} else if (hdrs[i]["TYPE"] === "date") {
						hdr.format_func = function(rw,k) {
							var dt = SUI.date.parseSqlDate(rw[k]);
							return dt ? SUI.date.format(dt, "date") : "";
						};
					} else if (hdrs[i]["TYPE"] === "time") {
						hdr.format_func = function(rw,k) {
							var dt = SUI.date.parseSqlDate(rw[k]);
							return dt ? SUI.date.format(dt, "time") : "";
						};
					}
					cols.push(hdr);
				}

				that.listView = new SUI.ListView({
					multiselect: true,
					cols: cols,
					sort: "_SCRIVO_TITLE",
					data: res.data.list,
					selected: null
				});
				that.enableButtons(that.listView);
				that.listView.onDblClick = function(row) {
					that.actionList.doAction("list.listview.edit");
				};
				that.listView.onSelectionChange = function() {
					that.enableButtons(that.listView);
				};
				that.listView.onContextMenu = function(x, y) {
					that.listContextMenu.showMenu(y,x);
				};

				that.splitSetWork.add(that.listView, "center");

				that.cbOnLoad();
			}
		);
	},

	reloadData: function() {

		var that = this;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "apps.list.getListView",
				pageId: this.pageId,
				applicationDefinitionId: this.applicationDefinitionId,
				parentId: this.parentId,
				parentListItemDefinitionId: this.parentListItemDefinitionId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId
			},
			function(res) {
				that.listView.loadData(res.data.list);
				that.listView.draw();
				that.subItems = res.data.subItems;
				that.enableButtons(that.listView);
			}
		);

	},

	enableButtons: function(lv) {
		this.actionList.enable({
			"list.listview.new": true,
			"list.listview.edit": lv.selectedRows.length == 1,
			"list.listview.goUp": this.parentIds.length,
			"list.listview.goSubList": lv.selectedRows.length && this.subItems,
			"list.listview.goLinkedPage":
				lv.selectedRows.length == 1 && lv.selectedRows[0]._SCRIVO_DOCUMENT_ID,
			"list.listview.copy": lv.selectedRows.length == 1,
			"list.listview.delete": lv.selectedRows.length > 0
		});
	},

	actNewItem: function() {
		var rw = this.listView.selectedRows[0];
		var that = this;
		new SUI.editor.apps.list.ListItemDialog({
			pageId: this.pageId,
			pagePropertyDefinitionId: this.pagePropertyDefinitionId,
			parentId: this.parentId,
			listItemDefinitionId: this.listItemDefinitionId,
			onOK: function() {
				that.reloadData();
			}
		}).show();
	},

	actCopyItem: function() {
		if (this.listView.selectedRows.length == 1) {
			var rw = this.listView.selectedRows[0];
			var that = this;
			new SUI.editor.apps.list.ListItemDialog({
				pageId: this.pageId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId,
				parentId: this.parentId,
				listItemId: rw._SCRIVO_ID,
				copyItem: true,
				onOK: function() {
					that.reloadData();
				}
			}).show();
		}
	},

	actEditItem: function() {
		if (this.listView.selectedRows.length == 1) {
			var rw = this.listView.selectedRows[0];
			var that = this;
			new SUI.editor.apps.list.ListItemDialog({
				pageId: this.pageId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId,
				parentId: this.parentId,
				listItemId: rw._SCRIVO_ID,
				onOK: function() {
					that.reloadData();
				}
			}).show();
		}
	},

	actGoLinkedPage: function() {
		if (this.listView.selectedRows.length == 1) {
			var rw = this.listView.selectedRows[0];
			if (parseInt(rw._SCRIVO_DOCUMENT_ID, 10)) {
				SUI.editor.scrivo.loadPage(rw._SCRIVO_DOCUMENT_ID);
			}
		}
	},

	actDelete: function() {
		if (this.listView.selectedRows.length >= 1) {
			var rws = this.listView.selectedRows;
			var ids = [];
			for (var i=0; i<this.listView.selectedRows.length; i++) {
				ids.push(this.listView.selectedRows[i]._SCRIVO_ID);
			}
			var that = this;

			new SUI.dialog.Confirm({
				caption: SUI.editor.i18n.apps.list.listView.captionDeleteItems,
				text: SUI.editor.i18n.apps.list.listView.textDeleteItems,
				onOK: function() {
					SUI.editor.xhr.doGet(
						SUI.editor.resource.ajaxURL, {
							a: "apps.list.deleteListItems",
							pageId: this.pageId,
							pagePropertyDefinitionId:
								this.pagePropertyDefinitionId,
							listItemIds: ids
						},
						function(res) {
							that.reloadData();
						}
					);
				}
			}).show();

		}
	},

	actGoUp: function(c) {
		if (this.parentIds.length) {
			var d = this.parentIds.pop();
			this.parentId = d.parentItemId;
			this.parentListItemDefinitionId = d.parentListItemDefinitionId;
			this.loadData();
		}
	},

	actGoSubList: function(c) {
		var rw = this.listView.selectedRows[0];
		if (rw._SCRIVO_ID) {
			if (this.parentIds.indexOf(rw._SCRIVO_ID) == -1) {
				this.parentIds.push({
					parentItemId: this.parentId,
					parentListItemDefinitionId: this.parentListItemDefinitionId
				});
				this.parentId = rw._SCRIVO_ID;
				this.parentListItemDefinitionId = rw._SCRIVO_DEF_ID;
			}
			this.loadData();
		}
	}

});
