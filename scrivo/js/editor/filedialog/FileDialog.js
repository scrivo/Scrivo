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
 * $Id: FileDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.FileDialog = SUI.defineClass({

	initializer: function(arg) {

		var that = this;

		this.selectedDir = arg.assetId ? arg.assetId : 0;
		this.cutted = { dirId: 0, rows: [] };
		this.path = [];

		this.feedId = 0;
		this.feeds = {};
		this.dirListRoot = [];

		// suggested image size for image cropper
		this.pageDefintionWidth = arg.pageDefintionWidth || null;
		this.pageDefinitionHeight = arg.pageDefinitionHeight || null;

		this.cbOK = arg.onOK ? arg.onOK : function() {};

		this.cols = [{
				title: SUI.editor.i18n.filedialog.fileDialog.name,
				key: "title",
				width: 300,
				minWidth: 100,
				maxWidth: 400,
				icon: this.get_icon,
				sort: this.sort_string
			},{
				title: SUI.editor.i18n.filedialog.fileDialog.size,
				key: "size",
				width: 75,
				align: "right",
				sort: this.sort_number,
				format_func: this.format_fsize
			},{
				title: SUI.editor.i18n.filedialog.fileDialog.type,
				key: "mimetype",
				width: 140,
				sort: this.sort_string
			},{
				title: SUI.editor.i18n.filedialog.fileDialog.modified,
				key: "modified",
				width: 130,
				sort: this.sort_number,
				format_func: this.format_time
			}
		];

		this.actionList = new SUI.ActionList([{
				actionId: "fdlg.list.select",
				title: SUI.editor.i18n.filedialog.fileDialog.actListSelect,
				handler: function(c) { that.actListSelect(c); }
			},{
				actionId: "fdlg.tree.select",
				title: SUI.editor.i18n.filedialog.fileDialog.actListSelect,
				handler: function(c) { that.actTreeSelect(c); }
			},{
				actionId: "fdlg.list.up",
				title: SUI.editor.i18n.filedialog.fileDialog.actListUp,
				icon: SUI.editor.resource.filedialog.icnFolderUp,
				handler: function(c) { that.actListUp(c); }
			},{
				actionId: "fdlg.list.refresh",
				title: SUI.editor.i18n.filedialog.fileDialog.actListRefresh,
				icon: SUI.editor.resource.filedialog.icnReload,
				handler: function(c) { that.actListRefresh(c); }
			},{
				actionId: "fdlg.list.upload",
				title: SUI.editor.i18n.filedialog.fileDialog.actListUpload,
				icon: SUI.editor.resource.filedialog.icnUpload,
				handler: function(c) { that.actListUpload(c); }
			},{
				actionId: "fdlg.list.overwrite",
				title: SUI.editor.i18n.filedialog.fileDialog.actListOverwrite,
				icon: SUI.editor.resource.filedialog.icnUploadOverwrite,
				handler: function(c) { that.actListOverwrite(c); }
			},{
				actionId: "fdlg.list.newfolder",
				title: SUI.editor.i18n.filedialog.fileDialog.actListNewfolder,
				icon: SUI.editor.resource.filedialog.icnNewFolder,
				handler: function(c) { that.actListNewfolder(c); }
			},{
				actionId: "fdlg.tree.newfolder",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreeNewfolder,
				icon: SUI.editor.resource.filedialog.icnNewFolder,
				handler: function(c) { that.actTreeNewfolder(c); }
			},{
				actionId: "fdlg.list.cut",
				title: SUI.editor.i18n.filedialog.fileDialog.actListCut,
				icon: SUI.editor.resource.filedialog.icnCut,
				handler: function(c) { that.actListCut(c); }
			},{
				actionId: "fdlg.tree.cut",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreeCut,
				icon: SUI.editor.resource.filedialog.icnCut,
				handler: function(c) { that.actTreeCut(c); }
			},{
				actionId: "fdlg.list.paste",
				title: SUI.editor.i18n.filedialog.fileDialog.actListPaste,
				icon: SUI.editor.resource.filedialog.icnPaste,
				handler: function(c) { that.actListPaste(c); }
			},{
				actionId: "fdlg.tree.paste",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreePaste,
				icon: SUI.editor.resource.filedialog.icnPaste,
				handler: function(c) { that.actTreePaste(c); }
			},{
				actionId: "fdlg.list.crop",
				title: SUI.editor.i18n.filedialog.fileDialog.actListCrop,
				icon: SUI.editor.resource.filedialog.icnCrop,
				handler: function(c) { that.actListCrop(c); }
			},{
				actionId: "fdlg.list.rename",
				title: SUI.editor.i18n.filedialog.fileDialog.actListRename,
				icon: SUI.editor.resource.filedialog.icnRename,
				handler: function(c) { that.actListRename(c); }
			},{
				actionId: "fdlg.tree.rename",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreeRename,
				icon: SUI.editor.resource.filedialog.icnRename,
				handler: function(c) { that.actTreeRename(c); }
			},{
				actionId: "fdlg.list.delete",
				title: SUI.editor.i18n.filedialog.fileDialog.actListDelete,
				icon: SUI.editor.resource.filedialog.icnDelete,
				handler: function(c) { that.actListDelete(c); }
			},{
				actionId: "fdlg.tree.delete",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreeDelete,
				icon: SUI.editor.resource.filedialog.icnDelete,
				handler: function(c) { that.actTreeDelete(c); }
			},{
				actionId: "fdlg.list.access",
				title: SUI.editor.i18n.filedialog.fileDialog.actListAccess,
				icon: SUI.editor.resource.filedialog.icnAccess,
				handler: function(c) { that.actListAccess(c); }
			},{
				actionId: "fdlg.tree.access",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreeAccess,
				icon: SUI.editor.resource.filedialog.icnAccess,
				handler: function(c) { that.actTreeAccess(c); }
			},{
				actionId: "fdlg.list.cache",
				title: SUI.editor.i18n.filedialog.fileDialog.actListCache,
				icon: SUI.editor.resource.filedialog.icnProperties,
				handler: function(c) { that.actListCache(c); }
			},{
				actionId: "fdlg.tree.cache",
				title: SUI.editor.i18n.filedialog.fileDialog.actTreeCache,
				icon: SUI.editor.resource.filedialog.icnProperties,
				handler: function(c) { that.actTreeCache(c); }
			}]
		);

		this.win = new SUI.Window({
			width: 800,
		 height: 500,
			resizable: true,
			title: SUI.editor.i18n.filedialog.fileDialog.caption,
			padding: new SUI.Padding()
		});

		this.frameset = new SUI.BorderLayout({
			west: {
				width: 200
			}
		});

		this.splitSetWork = new SUI.SplitLayout({
			north: {
				height: 31
			},
			south: {
				height: 34
			}
		});

		this.sidePanel = new SUI.Panel({
		 border: new SUI.Border(1)
		});
		this.contentPanel = new SUI.Panel({
			border: new SUI.Border(1),
			minHeight: 100,
			minWidth: 100
		});
		this.buttonPanel = new SUI.Panel({
			minHeight: 34,
			maxHeight: 34
		});

		this.accordion = new SUI.Accordion({
			items: [{
				title: SUI.editor.i18n.filedialog.fileDialog.fileInformation
			},{
				title: SUI.editor.i18n.filedialog.fileDialog.folders
			}],
			selected: 0
		});

		this.treeView = new SUI.TreeView({
			dataUrl: SUI.editor.resource.ajaxURL+"?a=filedialog.folderList"
		});

		this.treeView.addListener("onSelect",
			function() {
				that.loadData(that.treeView.selectedData().id);
			}
		);

		this.listView = new SUI.ListView({
			multiselect: true,
			minHeight: 100,
			minWidth: 100,
			cols: this.cols,
			sort: "title",
			selected: null,
			focussed: 0
		});

		this.dirList = new SUI.editor.filedialog.DirList({});

		this.toolBar = new SUI.Toolbar({
			actionlist: this.actionList,
			tools: [
				this.dirList,
				new SUI.ToolbarButton({actionId: "fdlg.list.up"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.refresh"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.upload"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.overwrite"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.newfolder"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.cut"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.paste"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.crop"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.rename"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.delete"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.access"}),
				new SUI.ToolbarButton({actionId: "fdlg.list.cache"})
			]
		});

		this.listContextMenu = new SUI.PopupMenu({
			actionlist: this.actionList,
			items: [
				{ actionId: "fdlg.list.select" },
				{ actionId: "fdlg.list.up" },
				{ actionId: "fdlg.list.refresh" },
				{ actionId: "fdlg.list.upload" },
				{ actionId: "fdlg.list.overwrite" },
				{ actionId: "fdlg.list.newfolder" },
				{ actionId: "fdlg.list.cut" },
				{ actionId: "fdlg.list.paste" },
				{ actionId: "fdlg.list.crop" },
				{ actionId: "fdlg.list.rename" },
				{ actionId: "fdlg.list.delete" },
				{ actionId: "fdlg.list.access" },
				{ actionId: "fdlg.list.cache" }
			]
		});

		this.treeContextMenu = new SUI.PopupMenu({
			actionlist: this.actionList,
			items: [
				{ actionId: "fdlg.tree.select" },
				{ actionId: "fdlg.tree.newfolder" },
				{ actionId: "fdlg.tree.cut" },
				{ actionId: "fdlg.tree.paste" },
				{ actionId: "fdlg.tree.rename" },
				{ actionId: "fdlg.tree.delete" },
				{ actionId: "fdlg.tree.access" },
				{ actionId: "fdlg.tree.cache" }
			]
		});

		this.okButton = new SUI.form.Button({
			top: 4,
			right: 112,
			width: 100,
			anchor: { right: true },
			title: SUI.editor.i18n.filedialog.fileDialog.butSelect
		});

		this.cancelButton = new SUI.form.Button({
			top: 4,
			right: 4,
			width: 100,
			anchor: { right: true },
			title: SUI.i18n.cancel
		});

		this.win.add(this.frameset);

		this.frameset.add(this.sidePanel, "west");
		//this.sidePanel.add(this.treeView);
		this.sidePanel.add(this.accordion);
		this.detailPanel = new SUI.Panel({color:"white"});
		this.detailPanel.padding (new SUI.Padding(5));
		this.detailPanel.clientBox().addClass("scrivo-fld-asset-detail");

		this.accordion.add(this.detailPanel, 0);
		this.accordion.add(this.treeView, 1);

		this.frameset.add(this.contentPanel, "center");
		this.contentPanel.add(this.splitSetWork);

		this.splitSetWork.add(this.toolBar, "north");
	//    this.splitSetWork.add(this.listView, "center");
		this.splitSetWork.add(this.buttonPanel, "south");

		this.buttonPanel.add(this.okButton);
		this.buttonPanel.add(this.cancelButton);

		SUI.browser.addEventListener(this.okButton.el(), "click", function() {
			that.actionList.doAction("fdlg.list.select");
		});

		SUI.browser.addEventListener(this.cancelButton.el(), "click", function() {
			that.win.close();
		});

		SUI.browser.addEventListener(this.dirList.sel, "change", function() {
			that.loadData(this.value, true);
		});

		this.win.onEnter = function() {
			this.actionList.doAction("fdlg.list.select");
		};

		this.listView.addListener("onContextMenu",
		 function(x, y) {
			 that.listContextMenu.showMenu(y,x);
			}
		);

		this.listView.addListener("onSelectionChange",
		 function() {
				that.enableButtons(that.listView);
				that.loadDetails(that.listView);
			}
		);

		this.listView.addListener("onDblClick",
		 function() {
		   that.actionList.doAction("fdlg.list.select");
			}
		);

		this.treeView.addListener("onContextMenu",
		 function(x, y) {
				that.enableButtons(that.listView);
				that.treeContextMenu.showMenu(y,x);
			}
		);

		this.loadImg = function(img, aw, ah) {
			var pop = document.createElement("IMG");
			pop.popw = aw;
			pop.poph = ah;
			pop.onload = function() {
				h= this.height;
				w= this.width;
				if (w > this.popw || h > this.poph) {
					if (w/h < this.popw/this.poph) {
						this.style.height=this.poph+"px";
						this.style.width= ((w/h)*this.poph)+"px";
					} else {
						this.style.width=this.popw+"px";
						this.style.height= ((h/w)*this.popw)+"px";
					}
				}
				this.onload=null;
				var tmp = document.getElementById("scrivo_filedialog_img");
				if (tmp && tmp.firstChild) {
				 tmp.replaceChild(pop, tmp.firstChild);
				}
			};
			  pop.src = img || "";

		};

		this.timeOut = 0;
		this.loadDetails = function(lv) {
			var argDetails = [];
			for (var i=0; i<lv.selectedRows.length; i++) {
				argDetails.push(lv.selectedRows[i].assetId);
			}
			if (this.timeOut != 0) {
				clearTimeout(this.timeOut);
			}
			this.timeOut = setTimeout(
				function() {
					SUI.editor.xhr.doGet(
						SUI.editor.resource.ajaxURL, {
							a: "filedialog.assetDetails",
							assetIds: argDetails
						},
						function(res){
							that.detailPanel.content(res.data.html);
							if (res.data.img != "") {
								that.loadImg(res.data.img, 150, 75);
							}
							//if (img!="") load_img(img, 150, 75);
						}
					);
				},
				1000
			);
		};

		this.loadDetailsFeed = function(lv) {
			this.detailPanel.content("");
		};
	},

	enableButtons: function(lv) {
		var oneFileSelected = lv.selectedRows.length == 1 &&
			lv.selectedRows[0].type == 1;
		var imgMatch = oneFileSelected ?
			lv.selectedRows[0].mimetype.match("^image/") : false;

		var listPaste = false;
		var contextPaste = false;
		if (this.cutted.rows.length > 0) {
			listPaste = (this.selectedDir != this.cutted.dirId)
				|| (this.listView.selectedRows.length === 1
					&& this.listView.selectedRows[0].type === 0);
			var node = this.treeView.contextMenuData();
			contextPaste = node && node.id != this.cutted.dirId;
		}

		this.actionList.enable({
			"fdlg.list.select": lv.selectedRows.length == 1,
			"fdlg.list.up": this.path.length > 1,
			"fdlg.list.refresh": true,
			"fdlg.list.upload": true,
			"fdlg.list.overwrite": oneFileSelected,
			"fdlg.list.newfolder": true,
			"fdlg.list.cut": lv.selectedRows.length > 0,
			"fdlg.list.paste": listPaste,
			"fdlg.tree.paste": contextPaste,
			"fdlg.list.crop":  oneFileSelected &&
				(imgMatch ? imgMatch[0] === "image/" : false),
			"fdlg.list.rename": lv.selectedRows.length == 1,
			"fdlg.list.delete": lv.selectedRows.length > 0,
			"fdlg.list.access": lv.selectedRows.length == 1 &&
				lv.selectedRows[0].type == 0,
			"fdlg.list.cache": lv.selectedRows.length == 1 &&
				lv.selectedRows[0].type == 0
		});
	},

	enableButtonsFeed: function(lv) {
		this.actionList.enable({
			"fdlg.list.select": lv.selectedRows.length == 1,
			"fdlg.list.up": this.feeds[this.feedId].path.length > 1,
			"fdlg.list.refresh": true,
			"fdlg.list.upload": false,
			"fdlg.list.overwrite": false,
			"fdlg.list.newfolder": false,
			"fdlg.list.cut": false,
			"fdlg.list.paste": false,
			"fdlg.tree.paste": false,
			"fdlg.list.crop":  false,
			"fdlg.list.rename": false,
			"fdlg.list.delete": false,
			"fdlg.list.access": false,
			"fdlg.list.cache": false
		});
	},

	loadAssetData: function(assetId, tree) {

		var that = this;

		this.feedId = 0;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "filedialog.assetList",
				assetId: assetId
			},
			function(res) {
				a = res.data;
				var sel = [];
				that.selectedDir = a.path[a.path.length-1].assetId;
				that.splitSetWork.add(that.listView, "center");

				that.listView.loadData(a.list, a.indexSelected);
				that.splitSetWork.draw();

				that.dirList.loadData(a.path, a.feeds);
				that.dirListRoot = { path: a.path, feeds: a.feeds };

				for (var i=0; i<a.feeds.length; i++) {
					var feedKey = "feed_" + a.feeds[i].feedId;
					if (!that.feeds[feedKey]) {
						that.initFeed(feedKey, a.feeds[i]);
					}
				}
				that.path = a.path;
				that.enableButtons(that.listView);

				if (tree) {
					var open = [];
					for(var i in that.path) {
						open.push(that.path[i].assetId);
					}
					that.treeView.loadData({
						parent: that.treeView.el(),
						pid: 0,
						open: open,
						selected: that.selectedDir
					});
				}
			}
		);
	},

	initFeedList: function(headers) {
		var cols = [];
		var that = this;
		for (var i=0; i<headers.length; i++) {
			cols.push({
				title: headers[i].data,
				icon: i==0 ? "icon" : null,
				key: "key_"+i,
				width: headers[i].attr.WIDTH
					? parseInt(headers[i].attr.WIDTH,10) : 250,
				align: headers[i].attr.ALIGN ? headers[i].attr.ALIGN : "left",
				sort: headers[i].attr.TYPE == "string"
					? this.sort_string : headers[i].attr.TYPE == "filesize"
						? this.sort_number : this.sort_string,
				format_func: headers[i].attr.TYPE == "date"
					? this.format_time : headers[i].attr.TYPE == "filesize"
						? this.format_fsize : null
			});
		}

		this.feeds[this.feedId].cols = cols;

		var lv = new SUI.ListView({
			cols: cols,
			sort: "key_0",
			selected: null,
			focussed: 0,
			width: 100,
			height: 100
		});

		lv.onDblClick = function(row) {
			that.actionList.doAction("fdlg.list.select");
		};

		lv.onSelectionChanged = function() {
			that.enableButtonsFeed(that.feeds[that.feedId].listView);
			that.loadDetailsFeed(that.feeds[that.feedId].listView);
		};

		lv.onContextMenu = function(x, y) {
			that.listContextMenu.showMenu(y,x);
		};

		this.splitSetWork.add(lv, "center");

		this.feeds[this.feedId].listView = lv;
	},

	initFeed: function(feedKey, feedData) {

		this.feeds[feedKey] = {
			feedData: feedData,
			path: [{
				assetId: feedKey,
				parentId: 0,
				title: feedData.title
			}]
		};
	},

	loadUrlFeed: function(feedItemId, title) {

		var that = this;
		var args = arguments;

		if (this.feeds[feedItemId]) {
			this.feedId = feedItemId;
		}
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "filedialog.feedList",
				url: this.feeds[this.feedId].feedData.url,
				feedId: this.feedId,
				itemId: feedItemId
			},
			function(res) {
				if (!that.feeds[that.feedId].listview) {
					that.initFeedList(res.data.headers);
				}

				if (args[0] == that.feedId) {
					that.dirList.loadData(that.dirListRoot.path,
						that.dirListRoot.feeds);
					that.dirList.sel.value = that.feedId;
					that.feeds[that.feedId].path.length = 1;
				} else {
					that.addToFeedPath(args[0], args[1]);
				}

				var data = [];
				for (var i=0; i<res.data.folders.length; i++) {
					var row = {
						id: res.data.folders[i].attr.ID,
						type: 0,
						icon: SUI.editor.resource.filedialog.icnFolder
					};
					var cols = that.feeds[that.feedId].cols;
					for (var j=0; j<cols.length; j++) {
						row[cols[j].key] = res.data.folders[i].data[j];
					}
					data.push(row);
				}
				for (var i=0; i<res.data.urls.length; i++) {
					var row = {
						id: res.data.urls[i].attr.ID,
						url: res.data.urls[i].attr.URL,
						type: 1,
						icon: SUI.editor.resource.filedialog.icnMimeDefault
					};
					var cols = that.feeds[that.feedId].cols;
					for (var j=0; j<cols.length; j++) {
						row[cols[j].key] = res.data.urls[i].data[j];
					}
					data.push(row);
				}
				that.feeds[that.feedId].listView.loadData(data);
				that.splitSetWork.draw();

				that.enableButtonsFeed(that.feeds[that.feedId].listView);
			}
		);
	},

	loadData: function(assetId, tree) {

		this.listView.setIsLoadingImage();

		if ((this.feeds[assetId] || this.feedId) && assetId != 2) {
			this.loadUrlFeed(assetId, tree);
		} else {
			this.loadAssetData(assetId, tree);
		}
	},

	addToFeedPath: function(feedItemId, name) {

		var tmpPath = [];
		var pid = 0;

		for (i=0; i<this.feeds[this.feedId].path.length; i++) {
			if (this.feeds[this.feedId].path[i].assetId == feedItemId) {
				break;
			}
			pid = this.feeds[this.feedId].path[i].assetId;
			tmpPath.push(this.feeds[this.feedId].path[i]);
		}
		tmpPath.push({
			assetId: feedItemId,
			parentId: pid,
			title: name
		});
		this.feeds[this.feedId].path = tmpPath;

		this.dirList.loadData(this.feeds[this.feedId].path,    []);
	},

	show: function() {
		this.win.show();
		this.loadData(this.selectedDir, true);
	},

	doDelete: function(t) {
		var that = this;
		SUI.editor.xhr.doPost(
			SUI.editor.resource.ajaxURL, {
				a: "filedialog.delete",
				assetIds: t
			}, function(res){
				that.loadData(that.selectedDir, true);
			}
		);
	},

	doPaste: function(dir) {
		var that = this;
		SUI.editor.xhr.doPost(
			SUI.editor.resource.ajaxURL, {
				a: "filedialog.paste",
				dirId: dir,
				assetIds: this.cutted.rows
			}, function(res){
				that.cutted.dirId = 0;
				that.cutted.rows = [];
				that.loadData(that.selectedDir, true);
			}
		);
	},

	actListSelect: function() {
		if (this.feedId == 0) {
			if (this.listView.selectedRows.length == 1) {
				var rw = this.listView.selectedRows[0];
				if (rw.type == 0) {
					this.loadData(rw.assetId, true);
				} else {
					if (!this.okButton.el().disabled) {
						this.win.close();
						this.cbOK({
							type: "assetId",
							value: rw.assetId
						});
					}
				}
			}
		} else {
			var lv = this.feeds[this.feedId].listView;
			if (lv.selectedRows.length == 1) {
				var rw = lv.selectedRows[0];
				if (rw.type == 0) {
					this.loadData(rw.id, rw.key_0);
				} else {
					if (!this.okButton.el().disabled) {
						this.win.close();
						this.cbOK({
							type: "url",
							value: rw.url
						});
					}
				}
			}
		}
	},

	actTreeSelect: function(what) {
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		this.loadData(node.id);
	},

	actListRename: function() {
		var that = this;
		if (this.listView.selectedRows[0].type == 0) {
			var p = new SUI.editor.filedialog.FoldernameDialog({
				assetId: this.listView.selectedRows[0].assetId,
				onOK: function() {
					that.loadData(that.selectedDir, true);
				}
			});
		} else {
			var p = new SUI.editor.filedialog.FilenameDialog({
				assetId: this.listView.selectedRows[0].assetId,
				onOK: function() {
					that.actionList.doAction("fdlg.list.refresh");
				}
			});
		}
		p.show();
	},

	actTreeRename: function(what) {
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		var p = new SUI.editor.filedialog.FoldernameDialog({
			assetId: node.id,
			onOK: function() {
				that.loadData(that.selectedDir, true);
			}
		});
		p.show();
	},

	actListUp: function() {
		if (this.feedId == 0) {
			this.loadData(this.path[this.path.length-1].parentId, true);
		} else {
			var p = this.feeds[this.feedId].path;
			this.loadData(p[p.length-1].parentId, true);
		}

	},

	actListRefresh: function() {
		this.loadData(this.selectedDir);
	},

	actListUpload: function() {
		var that = this;
		new SUI.editor.filedialog.UploadDialog({
			uploadDir: this.selectedDir,
			onUpload: function() {
				that.actionList.doAction("fdlg.list.refresh");
			}
		}).show();
	},

	actListOverwrite: function() {
		var that = this;
		new SUI.editor.filedialog.UploadDialog({
			uploadDir: this.selectedDir,
			assetId: this.listView.selectedRows[0].assetId,
			onUpload: function() {
				that.actionList.doAction("fdlg.list.refresh");
			}
		}).show();
	},

	actListNewfolder: function() {
		var that = this;
		var p = new SUI.editor.filedialog.FoldernameDialog({
			dirId: this.selectedDir,
			onOK: function() {
				that.loadData(that.selectedDir, true);
			}
		}).show();
	},

	actTreeNewfolder: function(what) {
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		var p = new SUI.editor.filedialog.FoldernameDialog({
			dirId: node.id,
			onOK: function() {
				that.loadData(that.selectedDir, true);
			}
		}).show();
	},

	actListCrop: function() {
		var that = this;
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "filedialog.cropCanvas",
				assetId: this.listView.selectedRows[0].assetId
			},
			function(res){
				var a = res.data;
				var p = new SUI.editor.filedialog.CropDialog({
					imageWidth: a.w,
					imageHeight: a.h,
					targetWidth: that.pageDefintionWidth,
					targetHeight: that.pageDefinitionHeight,
					image: SUI.editor.resource.filedialog.cropperBackgroundImg
						+ "?assetId=" + that.listView.selectedRows[0].assetId
						+ "&width=" + a.w,
					onOK: function(d) {
						d.assetId = that.listView.selectedRows[0].assetId;
						d.a = "filedialog.crop";
						SUI.editor.xhr.doPost(SUI.editor.resource.ajaxURL, d,
							function(res) {
								that.actionList.doAction("fdlg.list.refresh");
							}
						);
					}
				}).show();
			}
		);
	},

	actListDelete: function() {
		var that = this;
		var t = [];
		for (var i=0; i<this.listView.selectedRows.length; i++) {
			t.push(this.listView.selectedRows[i].assetId);
		}
		var p = new SUI.dialog.Confirm({
			title: SUI.editor.i18n.filedialog.fileDialog.captionDeleteFiles,
			text: SUI.editor.i18n.filedialog.fileDialog.textDeleteFiles,
			onOK: function() { that.doDelete(t); }
		});
		p.show();
	},

	actTreeDelete: function(what) {
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		var p = new SUI.dialog.Confirm({
			title: SUI.editor.i18n.filedialog.fileDialog.captionDeleteFiles,
			text: SUI.editor.i18n.filedialog.fileDialog.textDeleteFiles,
			onOK: function() { that.doDelete([node.id]); }
		});
		p.show();
	},

	actListAccess: function() {
		var that = this;
		var p = new SUI.editor.AccessDialog({
			assetId: this.listView.selectedRows[0].assetId,
			onOK: function() {
				that.actionList.doAction("fdlg.list.refresh");
			}
		}).show();
	},

	actTreeAccess: function(what) {
		var that = this;
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		var p = new SUI.editor.filedialog.AccessDialog({
			assetId: node.id,
			onOK: function() {
				that.actionList.doAction("fdlg.list.refresh");
			}
		}).show();
	},

	actListCache: function() {
		var that = this;
		var p = new SUI.editor.filedialog.CacheDialog({
			assetId: this.listView.selectedRows[0].assetId,
			onOK: function() {
				that.actionList.doAction("fdlg.list.refresh");
			}
		}).show();
	},

	actTreeCache: function() {
		var that = this;
		var p = new SUI.editor.filedialog.CacheDialog({
			assetId: this.listView.selectedRows[0].assetId,
			onOK: function() {
				that.actionList.doAction("fdlg.list.refresh");
			}
		}).show();
	},

	actListCut: function() {
		var t = [];
		for (var i=0; i<this.listView.selectedRows.length; i++) {
			t.push(this.listView.selectedRows[i].assetId);
		}
		this.cutted.dirId = this.selectedDir;
		this.cutted.rows = t;
		this.enableButtons(this.listView);
	},

	actTreeCut: function(what) {
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		this.cutted.dirId = node.pid;
		this.cutted.rows = [ node.id ];
		this.enableButtons(this.listView);
	},

	actListPaste: function() {
		if (this.listView.selectedRows.length === 1
				&& this.listView.selectedRows[0].type === 0) {
			this.doPaste(this.listView.selectedRows[0].assetId);
		} else {
		this.doPaste(this.selectedDir);
		}
	},

	actTreePaste: function(what) {
		var node = this.treeView.selectedData();
		if (what instanceof SUI.PopupMenu) {
			node = this.treeView.contextMenuData();
		}
		this.doPaste(node.id);
	},

	format_fsize: function(rw,k){
		if (rw["type"] == 0) return "";
		var size_names = ["Byte","KB","MB","GB","TB","PB","EB"];
		var name_id=0;
		var size = rw[k];
		while(size>=1024 && (name_id<size_names.length-1)){
			size/=1024;
			name_id++;
		}
		return (Math.round(size))+" "+size_names[name_id];
	},

	format_date: function(rw,k) {
		var d = new Date(rw[k]*1000);
		return ("00"+d.getDate()).substr(-2)
			+ "-" + ("00"+(d.getMonth()+1)).substr(-2)
			+ "-" +    (""+d.getFullYear()).substr(2);
	},

	format_time: function(rw,k) {
		var d = new Date(rw[k]*1000);
		return ("00"+d.getDate()).substr(-2)
			+ "-" + ("00"+(d.getMonth()+1)).substr(-2)
			+ "-" +    (""+d.getFullYear()).substr(2)
			+ " " + ("00"+(d.getDay())).substr(-2)
			+ ":" + ("00"+(d.getMinutes())).substr(-2)
			+ ":" + ("00"+(d.getSeconds())).substr(-2);
	},

	get_icon: function(rw) {
		var mt = rw.mimetype;
		var x = mt.substring(0,6);
		if (rw.type == 0)  {
			return SUI.editor.resource.filedialog.icnFolder;
		} else    if ("image/" == x) {
			return SUI.editor.resource.filedialog.icnMimeImage;
		} else    if ("audio/" == x || "video/" == x) {
			return SUI.editor.resource.filedialog.icnMimeMedia;
		} else     if ("text/plain" == mt) {
			return SUI.editor.resource.filedialog.icnMimeText;
		} else     if ("applic" == x) {
			if (mt.indexOf("msword") != -1) {
				return SUI.editor.resource.filedialog.icnMimeWord;
			} else    if (mt.indexOf("pdf") != -1) {
				return SUI.editor.resource.filedialog.icnMimePdf;
			} else     if (mt.indexOf("powerpoint") != -1) {
				return SUI.editor.resource.filedialog.icnMimePpt;
			} else     if (mt.indexOf("excel") != -1) {
				return SUI.editor.resource.filedialog.icnMimeExcel;
			} else     if (mt.indexOf("flash") != -1) {
				return SUI.editor.resource.filedialog.icnMimeMedia;
			} else     if (mt.indexOf("zip") != -1) {
				return SUI.editor.resource.filedialog.icnMimeArchive;
			} else if (mt.indexOf("tar") != -1) {
				return SUI.editor.resource.filedialog.icnMimeArchive;
			}
		}
		return SUI.editor.resource.filedialog.icnMimeDefault;
	},

	sort_string: function(data, key, dir) {
		data.sort(
			function(a,b){
				if (a.type == 0 && b.type == 1) return -1;
				if (a.type == 1 && b.type == 0) return 1;
				var aa = ""+a[key];
				var bb = ""+b[key];
				return     dir * (aa.toLowerCase() < bb.toLowerCase()
					? -1
					: aa.toLowerCase()>bb.toLowerCase()
						? 1
						: 0);
			}
		);
	},

	sort_number: function(data, key, dir) {
		data.sort(
			function(a,b){
				if (a.type == 0 && b.type == 1) return -1;
				if (a.type == 1 && b.type == 0) return 1;
				return dir*(a[key]-b[key]);
			}
		);
	}

});
