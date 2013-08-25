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
 * $Id: BlockList.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.apps.list.BlockList = SUI.defineClass({

	baseClass: SUI.AnchorLayout,

	initializer: function(arg) {

		SUI.editor.apps.list.BlockList.initializeBase(this, arg);
		var that = this;

		this.anchor = { left: true, right: true};

		this.frame = arg.frame;

		var height = 0;
		var top = this.MARGIN;

		for (var i=0; i<arg.data.items.length; i++) {

			top += height;

			var panel = new SUI.Panel({
				innerBorder: new SUI.Border(1),
				padding: new SUI.Padding(0, this.MARGIN, this.MARGIN),
				top: top,
				anchor: { left: true, right: true },
				height: 200
			});

			var deleteButton = new SUI.ToolbarButton({
				anchor: { right: true },
				right: this.MARGIN,
				width: this.BUTTON_SIZE,
				height: this.BUTTON_SIZE,
				top: this.MARGIN,
				title: SUI.editor.i18n.apps.list.deleteItem,
				icon: SUI.editor.resource.apps.list.icnDelete,
				handler: function() {
					that.frame.actDelete(this.extraInfo.listItemId);
				}
			});
			deleteButton.extraInfo = { listItemId: arg.data.items[i].listItemId };

			var downButton = new SUI.ToolbarButton({
				anchor: { right: true },
				right: this.BUTTON_SIZE + this.MARGIN,
				width: this.BUTTON_SIZE,
				height: this.BUTTON_SIZE,
				top: this.MARGIN,
				title: SUI.editor.i18n.apps.list.blockList.moveItemDown,
				icon: SUI.editor.resource.apps.list.icnDown,
				handler: function() {
					that.frame.actMoveDown(this.extraInfo.listItemId);
				}
			});
			downButton.extraInfo = { listItemId: arg.data.items[i].listItemId };

			var moveButton = new SUI.ToolbarButton({
				anchor: { right: true },
				right: 2 * this.BUTTON_SIZE + this.MARGIN,
				width: this.BUTTON_SIZE,
				height: this.BUTTON_SIZE,
				top: this.MARGIN,
				title: SUI.editor.i18n.apps.list.blockList.moveItem,
				icon: SUI.editor.resource.apps.list.icnMove,
				handler: function() {
					that.frame.actMoveItem(this.extraInfo.listItemId);
				}
			});
			moveButton.extraInfo = { listItemId: arg.data.items[i].listItemId };

			var upButton = new SUI.ToolbarButton({
				anchor: { right: true },
				right: 3 * this.BUTTON_SIZE + this.MARGIN,
				width: this.BUTTON_SIZE,
				height: this.BUTTON_SIZE,
				top: this.MARGIN,
				title: SUI.editor.i18n.apps.list.blockList.moveItemUp,
				icon: SUI.editor.resource.apps.list.icnUp,
				handler: function() {
					that.frame.actMoveUp(this.extraInfo.listItemId);
				}
			});
			upButton.extraInfo = { listItemId: arg.data.items[i].listItemId };

			var copyButton = new SUI.ToolbarButton({
				anchor: { right: true },
				right: 4 * this.BUTTON_SIZE + this.MARGIN,
				width: this.BUTTON_SIZE,
				height: this.BUTTON_SIZE,
				top: this.MARGIN,
				title: SUI.editor.i18n.apps.list.copyItem,
				icon: SUI.editor.resource.apps.list.icnCopyItem,
				handler: function() {
					that.frame.actCopyItem(this.extraInfo.listItemId);
				}
			});
			copyButton.extraInfo = { listItemId: arg.data.items[i].listItemId };

			var editButton = new SUI.ToolbarButton({
				anchor: { right: true },
				right: 5 * this.BUTTON_SIZE + this.MARGIN,
				width: this.BUTTON_SIZE,
				height: this.BUTTON_SIZE,
				top: this.MARGIN,
				title: SUI.editor.i18n.apps.list.editItem,
				icon: SUI.editor.resource.apps.list.icnEditItem,
				handler: function() {
					that.frame.actEditItem(this.extraInfo.listItemId);
				}
			});
			editButton.extraInfo = { listItemId: arg.data.items[i].listItemId };

			var p = 6;

			var goSubButton = null;
			if (arg.data.items[i].subItems) {
				goSubButton = new SUI.ToolbarButton({
					anchor: { right: true },
					right: p * this.BUTTON_SIZE + this.MARGIN,
					width: this.BUTTON_SIZE,
					height: this.BUTTON_SIZE,
					top: this.MARGIN,
					title: SUI.editor.i18n.apps.list.goSubList,
					icon: SUI.editor.resource.apps.list.icnGoSubList,
					handler: function() {
						that.frame.actGoSubList(this.extraInfo);
					}
				});
				goSubButton.extraInfo = {
					parentItemId: arg.data.items[i].listItemId,
					parentListItemDefinitionId:
						arg.data.items[i].listItemDefinitionId
				};
				p++;
			}

			var linkedButton = null;
			if (parseInt(arg.data.items[i].linkedPageId,10)) {
				linkedButton = new SUI.ToolbarButton({
					anchor: { right: true },
					right: p * this.BUTTON_SIZE + this.MARGIN,
					width: this.BUTTON_SIZE,
					height: this.BUTTON_SIZE,
					top: this.MARGIN,
					title: SUI.editor.i18n.apps.list.goLinkedPage,
					icon: SUI.editor.resource.apps.list.icnGoLinkedPage,
					handler: function() {
						that.frame.actGoLinkedPage(this.extraInfo.linkedPageId);
					}
				});
				linkedButton.extraInfo =
					{ linkedPageId: arg.data.items[i].linkedPageId };
			}

			var ctlTop = 2;

			var txtTitle = new SUI.Box({
				top: ctlTop + 2,
				left: this.MARGIN + this.LABEL_WIDTH,
				height: this.CTRL_HEIGHT,
				anchor: { right: true, left: true }
			});
			txtTitle.el().innerHTML = "<b>"+arg.data.items[i].row[0].data+"</b>";
			var lblTitle = this._getLabel(ctlTop, arg.data.items[i].row[0].label);

			ctlTop += this.CTRL_HEIGHT;

			var txtType = new SUI.Box({
				top: ctlTop + 2,
				left: this.MARGIN + this.LABEL_WIDTH,
				height: this.CTRL_HEIGHT,
				anchor: { right: true, left: true }
			});
			txtType.el().innerHTML = "<b>"+arg.data.items[i].row[3].data+"</b>";
			var lblType = this._getLabel(ctlTop, arg.data.items[i].row[3].label);

			ctlTop += this.CTRL_HEIGHT;

			var txtOnOffline = new SUI.Box({
				top: ctlTop + 2,
				left: this.MARGIN + this.LABEL_WIDTH,
				height: this.CTRL_HEIGHT,
				anchor: { right: true, left: true }
			});
			txtOnOffline.el().innerHTML =
				that.formatDate(arg.data.items[i].row[1].data, ">") + "/" +
				that.formatDate(arg.data.items[i].row[2].data, "<=");
			var lblOnOffline = this._getLabel(ctlTop, arg.data.items[i].row[1].label);


			panel.add(lblTitle);
			panel.add(txtTitle);

			panel.add(lblType);
			panel.add(txtType);

			panel.add(lblOnOffline);
			panel.add(txtOnOffline);

			panel.add(copyButton);
			panel.add(upButton);
			panel.add(moveButton);
			panel.add(downButton);
			panel.add(deleteButton);
			if (goSubButton) {
				panel.add(goSubButton);
			}
			if (linkedButton) {
				panel.add(linkedButton);
			}
			panel.add(editButton);

			ctlTop += this.CTRL_HEIGHT + 4;

			for (var j=4; j < arg.data.items[i].row.length; j++) {
				switch (arg.data.items[i].row[j].type) {
				case "input":
				case "select":
				case "checkbox":
				case "date":
					var title = new SUI.Box({
						top: ctlTop + 2,
						left: this.MARGIN + this.LABEL_WIDTH,
						height: this.CTRL_HEIGHT,
						anchor: { right: true, left: true }
					});
					var dat = "";
					var tmp = arg.data.items[i].row[j].data;
					if (tmp) {
						dat = tmp;
					}
					title.el().innerHTML = "<b>" + dat + "</b>";
					panel.add(this._getLabel(ctlTop, arg.data.items[i].row[j].label));
					panel.add(title);
					ctlTop += this.CTRL_HEIGHT;
					break;
				case "url":
					var title = new SUI.Box({
						top: ctlTop + 2,
						left: this.MARGIN + this.LABEL_WIDTH,
						height: this.CTRL_HEIGHT,
						anchor: { right: true, left: true }
					});
					var tmp = arg.data.items[i].row[j].data;
					if (tmp) {
						this._urlTitle(tmp, title);
					}
					panel.add(this._getLabel(ctlTop, arg.data.items[i].row[j].label));
					panel.add(title);
					ctlTop += this.CTRL_HEIGHT;
					break;
				case "color":
					var title = new SUI.Box({
						top: ctlTop,
						left: this.MARGIN + this.LABEL_WIDTH,
						height: this.CTRL_HEIGHT-2,
						anchor: { left: true },
						width: 50
					});
					title.border(new SUI.Border(1));
					var dat = arg.data.items[i].row[j].data;
					if (dat !== "") {
						title.el().style.backgroundColor = dat;
					}
					title.el().style.borderColor = "black";

					panel.add(this._getLabel(ctlTop, arg.data.items[i].row[j].label));
					panel.add(title);
					ctlTop += this.CTRL_HEIGHT;
					break;
				case "img":
					var title = new SUI.Box({
						top: ctlTop,
						left: this.MARGIN + this.LABEL_WIDTH,
						height: 2*this.CTRL_HEIGHT,
						anchor: { left: true },
						width: 50
					});
					//title.border(new SUI.Border(1));
					var dat = arg.data.items[i].row[j].data;
					if (dat !== "") {
						var image = new Image();
						image.height = title.height();
						image.src = dat.src;
						title.el().appendChild(image);
					}
					//title.el().style.borderColor = "black";

					panel.add(this._getLabel(ctlTop, arg.data.items[i].row[j].label));
					panel.add(title);
					ctlTop += 2*this.CTRL_HEIGHT;
					break;
				case "text":
				case "html_text":
					var txtText = new SUI.Box({
						top: ctlTop,
						left: this.MARGIN,
						right: this.MARGIN,
						height: 100,
						anchor: { right: true, left: true }

					});
					txtText.el().innerHTML = arg.data.items[i].row[j].data;
					txtText.el().style.overflow = "auto";
					txtText.el().style.backgroundColor = "white";
					txtText.el().style.borderStyle = "solid";
					txtText.el().style.borderColor = "black";
					txtText.border(new SUI.Border(1));
					panel.add(txtText);
					ctlTop += 100 + this.MARGIN;
					break;
				}
				ctlTop += 4;
			}

			ctlTop += this.MARGIN;

			panel.height(ctlTop);
			height = ctlTop;

			this.add(panel);
		}

		this.height(top + height);

	},

	BUTTON_SIZE: 26,
	MARGIN: 8,
	CTRL_HEIGHT: 20,
	LABEL_WIDTH: 100,

	_urlTitle: function(tmp, title) {
		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "displayURL",
				url: tmp.href
			},
			function(res) {
				title.el().innerHTML = "<a href=\""+tmp.href+
				"\" href=\""+tmp.href+"\" target=\"_blank\">"+
				(res.data.url || "")+"</a>";
			}
		);
	},

	_getLabel: function(ctlTop, txt) {
		return new SUI.form.Label({
			top: ctlTop,
			left: this.MARGIN,
			width: this.LABEL_WIDTH,
			title: txt
		});
	},

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
	}

});
