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
 * $Id: List.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.list.List = SUI.defineClass({

	baseClass: SUI.AnchorLayout,

	initializer: function(arg) {

		SUI.editor.apps.list.List.initializeBase(this, arg);
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
		this.pageNo = 1;
		this.firstPage = true;
		this.lastPage = false;

		//this.listId = 0;
		this.listItemDefinitionId = null;

		this.anchor = { left: true, right: true, top: true, bottom: true };

		this.actionList = new SUI.ActionList([{
				actionId: "list.list.goUp",
				title: SUI.editor.i18n.apps.list.goUp,
				icon: SUI.editor.resource.apps.list.icnGoUp,
				handler: function(c) {
					if (that.parentIds.length) {
						var d = that.parentIds.pop();
						that.parentId = d.parentItemId;
						that.parentListItemDefinitionId =
							d.parentListItemDefinitionId;
						that.actGoSubList(d, true);
					}
				}
			},{
				actionId: "list.list.search",
				title: SUI.editor.i18n.apps.list.list.searchItem,
				icon: SUI.editor.resource.apps.list.icnFind,
				handler: function(c) {
					var pr = new SUI.dialog.Prompt({
						width: 240,
						caption:
							SUI.editor.i18n.apps.list.list.promptSearchCaption,
						text: SUI.editor.i18n.apps.list.list.promptSearch,
						onOK: function(val) {
							that.loadData({ search: val });
						}
					}).show();
				}
			},{
				actionId: "list.list.prev",
				title: SUI.editor.i18n.apps.list.list.prevPage,
				icon: SUI.editor.resource.apps.list.icnPrev,
				handler: function(c) {
					if (!that.first) {
						that.pageNo--;
						that.loadData();
					}
				}
			},{
				actionId: "list.list.next",
				title: SUI.editor.i18n.apps.list.list.nextPage,
				icon: SUI.editor.resource.apps.list.icnNext,
				handler: function(c) {
					if (!that.last) {
						that.pageNo++;
						that.loadData();
					}
				}
			}
		]);

		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "apps.list.getListItemDefinitions",
				pageId: this.pageId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId,
				applicationDefinitionId: this.applicationDefinitionId
			},
			function(res) {
				that.listItemDefinitions = res.data.types;
				that.parentListItemDefinitionId =
					res.data.parentListItemDefinitionId;
				that.initForm();
			}
		);

		this.initForm = function() {

			var that = this;
			var tools = [];

			for (var i=0; i<this.listItemDefinitions.length; i++) {
				var tb = new SUI.ToolbarButton({
					title: this.listItemDefinitions[i].listItemDefinitionTitle,
					icon: "../../"+this.listItemDefinitions[i].icon,
					handler: function() {
						that.actNewItem(this.extraInfo.listItemDefinitionId);
					}
				});
				tb.extraInfo =
					{ listItemDefinitionId: this.listItemDefinitions[i].listItemDefinitionId };
				tools.push(tb);
			}

			tools.push(new SUI.ToolbarSeparator({}));
			tools.push(new SUI.ToolbarButton({actionId: "list.list.goUp"}));
			tools.push(new SUI.ToolbarSeparator({}));
			tools.push(new SUI.ToolbarButton({actionId: "list.list.search"}));
			tools.push(new SUI.ToolbarSeparator({}));
			tools.push(new SUI.ToolbarButton({actionId: "list.list.prev"}));
			tools.push(new SUI.ToolbarButton({actionId: "list.list.next"}));

			this.toolBar = new SUI.Toolbar({
				actionlist: this.actionList,
				tools: tools
			});

			this.splitSetWork = new SUI.SplitLayout({
				north: {
					height: this.toolBar.height()
				}
			});

			this.splitSetWork.add(this.toolBar, "north");

			this.add(this.splitSetWork);

			this.loadData();
		};
	},

	cbOnLoad: function() {},

	loadData: function() {

		var arg = arguments[0] ? arguments[0] : {};
		var that = this;
		var val = arg.search ? arg.search : "";
		var scrollTop = arg.scrollTop ? arg.scrollTop : 0;
//        this.parentId = arg.parentId ? arg.parentId : 0;

		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "apps.list.getBlockList",
				pageId: this.pageId,
				pageNo: this.pageNo,
				parentId: this.parentId,
				search: SUI.trim(val?val:""),
				pagePropertyDefinitionId: this.pagePropertyDefinitionId
			},
			function(res) {
				var sb = new SUI.editor.VerticalScrollBox({
					minHeight: 100,
					minWidth: 100
				});
				that.scrollBox = sb;
				var bl = new SUI.editor.apps.list.BlockList({
					data : res.data,
					frame: that
				});
				sb.add(bl);
				that.splitSetWork.add(sb, "center");

				if (scrollTop) {
					that.scrollBox.setScrollTop(scrollTop);
				}

				that.first = res.data.first;
				that.last = res.data.last;

				that.cbOnLoad();


				that.enableButtons();
			}
		);

	},

	enableButtons: function(lv) {
		this.actionList.enable({
			"list.list.goUp": this.parentIds.length,
			"list.list.prev": !this.first,
			"list.list.next": !this.last
		});
	},

	actNewItem: function(listItemDefinitionId) {
		var that = this;
		new SUI.editor.apps.list.ListItemDialog({
			pageId: this.pageId,
			pagePropertyDefinitionId: this.pagePropertyDefinitionId,
			parentId: this.parentId,
			listItemDefinitionId: listItemDefinitionId,
			onOK: function() {
				that.loadData();
			}
		}).show();
	},

	actCopyItem: function(id) {
		var that = this;
		var st = this.scrollBox.getScrollTop();
		new SUI.editor.apps.list.ListItemDialog({
			pageId: this.pageId,
			pagePropertyDefinitionId: this.pagePropertyDefinitionId,
			parentId: this.parentId,
			listItemId: id,
			copyItem: true,
			onOK: function() {
				that.loadData({scrollTop: st});
			}
		}).show();
	},

	actEditItem: function(id) {
		var that = this;
		var st = this.scrollBox.getScrollTop();
		new SUI.editor.apps.list.ListItemDialog({
			pageId: this.pageId,
			pagePropertyDefinitionId: this.pagePropertyDefinitionId,
			parentId: this.parentId,
			listItemId: id,
			onOK: function() {
				that.loadData({scrollTop: st});
			}
		}).show();
	},

	actMoveUp: function(id) {
		var that = this;
		var st = this.scrollBox.getScrollTop();
		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
				a: "apps.list.moveUp",
				pageId: this.pageId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId,
				parentId: this.parentId,
				listItemId: id
			},
			function(res) {
				that.loadData({scrollTop: st});
			}
		);
	},

	actMoveDown: function(id) {
		var that = this;
		var st = this.scrollBox.getScrollTop();
		SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL, {
				a: "apps.list.moveDown",
				pageId: this.pageId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId,
				parentId: this.parentId,
				listItemId: id
			},
			function(res) {
				that.loadData({scrollTop: st});
			}
		);
	},

	actMoveItem: function(id) {
		var that = this;
		var st = this.scrollBox.getScrollTop();
		new SUI.editor.apps.list.PositionDialog({
			pageId: this.pageId,
			pagePropertyDefinitionId: this.pagePropertyDefinitionId,
			parentId: this.parentId,
			listItemId: id,
			onOK: function(data) {
				data.a = "apps.list.moveToPosition";
				SUI.editor.xhr.doGet(SUI.editor.resource.ajaxURL,
					data,
					function(res) {
						that.loadData({scrollTop: st});
					}
				);
			}
		}).show();
	},

	actDelete: function(id) {
		var ids = [];
		var st = this.scrollBox.getScrollTop();
		ids.push(id);
		var that = this;

		new SUI.dialog.Confirm({
			caption: SUI.editor.i18n.apps.list.list.captionDeleteItem,
			text: SUI.editor.i18n.apps.list.list.textDeleteItem,
			onOK: function() {
				SUI.editor.xhr.doGet(
					SUI.editor.resource.ajaxURL, {
						a: "apps.list.deleteListItems",
						pageId: this.pageId,
						pagePropertyDefinitionId: this.pagePropertyDefinitionId,
						listItemIds: ids
					},
					function(res) {
						that.loadData({scrollTop: st});
					}
				);
			}
		}).show();

	},

	actGoLinkedPage: function(pageId) {
		SUI.editor.scrivo.loadPage(pageId);
	},

	actGoSubList: function(data, moveUp) {
		var that = this;
		this.pageNo = 1;
		this.firstPage = true;
		this.lastPage = false;
		if (moveUp == undefined && this.parentIds.indexOf(data) == -1) {
			this.parentIds.push({
				parentItemId: this.parentId,
				parentListItemDefinitionId: this.parentListItemDefinitionId
			});
			this.parentId = data.parentItemId;
			this.parentListItemDefinitionId = data.parentListItemDefinitionId;
		}
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "apps.list.getListItemDefinitions",
				pageId: this.pageId,
				applicationDefinitionId: this.applicationDefinitionId,
				parentId: this.parentId,
				parentListItemDefinitionId: this.parentListItemDefinitionId,
				pagePropertyDefinitionId: this.pagePropertyDefinitionId
			},
			function(res) {
				// TODO: work out removal/replacement of sections
				SUI.browser.removeNode(that.splitSetWork.el(), true);
				that.children = [];
				that.splitSetWork = null;

				that.listItemDefinitions = res.data.types;
				that.parentListItemDefinitionId =
					res.data.parentListItemDefinitionId;
				that.initForm();
			}
		);
	}

});
