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
 * $Id: TableEditor.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.tableeditor.TableEditor = SUI.defineClass({

	initializer: function(arg) {

		this.doc = null;
		this.table = null;
		this.cell = null;
		this.shdw = null;
		this.rc = null;
		this.r = null;
		this.c = null;
		this.styles = null;
		if (!this._seq) this._seq = 0;

	},

	set: function(elem) {
		var s = elem;
		while(s!=null && (s.tagName!="TD" && s.tagName!="TH")){
			s=s.parentNode;
		}
		if (!s)
			return;
		var t = s;
		while(t!=null && t.tagName!="TABLE"){
			t=t.parentNode;
		}
		if (!t)
			return;

		this.cell = s;
		this.table = t;
		this.shdw = new SUI.editor.htmleditor.tableeditor.TableMirror(
			this.doc, this.table);
		this.rc = this.shdw.findCell(this.cell);
		this.r = this.rc[0];
		this.c = this.rc[1];
	},

	getStyles: function() {
		this.styles = {};
		this.styles.scrivotable = [];
		this.styles.scrivocell = [];
		this.styles.scrivocellrow = [];
		this.styles.scrivocellcol = [];
		this.styles.scrivocellcross = [];

		var selectors =
			SUI.editor.htmleditor.tableeditor.getStyleSelectors(this.doc);
		for (var i=0; i<selectors.length; i++) {
			var st = selectors[i];
			var sa = st.split(" ");
			var idx = st.indexOf("_");
			if (idx != -1) {
				var p1 = st.substring(1, idx);
				if (this.styles[p1]) {
					this.styles[p1].push(st.substring(idx+1));
				}
			}
		}
	},

//    addStyleToList: function(sel, type, label) {
//        for (var i=0; i<this.styles[type].length; i++) {
//            sel.options[sel.options.length] =
//                new Option(label + this.styles[type][i], type + "_"
//                     + this.styles[type][i]);
//        }
//    },

	genDomId: function(elem) {
	// Generate an unique id within the scope of a DOM tree
		if (elem.id)
			return;
		var id;
		var chrs = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		var d = new Date();
		do {
			id = "scrv"
				+ SUI.editor.htmleditor.tableeditor._intToNbase(
					this._seq++, chrs)
				+ "_"
				+ SUI.editor.htmleditor.tableeditor._intToNbase(
					d.getTime()/1000, chrs);
		} while (this.doc.getElementById(id) != null);
		elem.id = id;
	},

	remDomId: function(elem) {
		if (!elem.id)
			return;

		for (var ri=0; ri<this.table.rows.length; ri++) {
			row = this.table.rows[ri];
			for (var ci=0; ci<row.cells.length; ci++) {
				var cll = row.cells[ci];
				if (cll.headers) {
					cll.headers = cll.headers.replace(
						new RegExp(elem.id, "g"), "");
					cll.headers = cll.headers.replace(/\s+/g, " ");
					cll.headers = cll.headers.replace(/^\s*/g, "");
					cll.headers = cll.headers.replace(/\s*$/g, "");
					if (cll.headers == " " || cll.headers == "") {
						cll.removeAttribute("headers");
					}
				}
			}
		}

		if (elem.id.substr(0,4) == "scrv")
			elem.removeAttribute("id");
	},

	insertColumn: function(before) {
		var tf = new Array();
		if (!before)
			this.c += this.shdw.cellWidth(this.r, this.c);
		for (var ri=0; ri<this.shdw.s.length; ri++) {
			if (!this.shdw.s[ri][this.c]) {
				tf[tf.length] = SUI.editor.htmleditor.tableeditor.insertCell(
					this.table.rows[ri], this.table.rows[ri].cells.length);
			} else {
				if (this.shdw.s[ri][this.c].left) {
					cnt = this.shdw.countCellsBefore(ri, this.c);
					tf[tf.length] =
						SUI.editor.htmleditor.tableeditor.insertCell(
							this.table.rows[ri], cnt);
				} else  {
					this.shdw.s[ri][this.c].cell.colSpan++;
					tf[tf.length] = "";
				}
			}
		}

		var c = before ? this.c : this.c + 1;
		if (c >= this.shdw.s[0].length-1)
			c = this.shdw.s[0].length-1;

		var ri=0;
		var id = "";
		if (this.shdw.s[ri][c].cell.tagName == "TH") {
			if (tf[ri] != "") {
				tf[ri] = this._splittedMerge(
					this.shdw.s[ri][c].cell, tf[ri], true);
				id = tf[ri].id;
			}
			ri++;
		}

		for (; ri<this.shdw.s.length; ri++) {
			if (tf[ri] != "") {
				var hdrs = " " + id;
				if (this.shdw.s[ri][0].cell.id)
					hdrs += " " + this.shdw.s[ri][0].cell.id;
				if (hdrs != " ")
					tf[ri].headers = hdrs.substring(1);
				if (this.shdw.s[ri][c].cell.className)
					tf[ri].className = this.shdw.s[ri][c].cell.className;
			}
		}
	},

	deleteColumn: function() {
		for (var ri=0; ri<this.shdw.s.length; ri++) {
			if (this.shdw.s[ri][this.c].top) {
				if (this.shdw.s[ri][this.c].left
						&& this.shdw.s[ri][this.c].right) {
					if (this.shdw.s[ri][this.c].cell.id)
						this.remDomId(this.shdw.s[ri][this.c].cell.id);
					cnt = this.shdw.countCellsBefore(ri, this.c);
					this.table.rows[ri].deleteCell(cnt);
				} else  {
					this.shdw.s[ri][this.c].cell.colSpan--;
				}
			}
		}
	},

	insertRow: function(above) {
		var tf = new Array();
		if (!above)
			this.r += this.shdw.cellHeight(this.r, this.c);
		this.table.insertRow(this.r);
		var cnnt = 0;
		for (var ci=0; ci<this.shdw.s[0].length; ci++) {
			if (!this.shdw.s[this.r] || this.r==0) {
				tf[tf.length] = SUI.editor.htmleditor.tableeditor.insertCell(
					this.table.rows[this.r],
					this.table.rows[this.r].cells.length);
			} else {
				if (this.shdw.s[this.r][ci].top) {
					tf[tf.length] =
						SUI.editor.htmleditor.tableeditor.insertCell(
							this.table.rows[this.r], cnnt);
						cnnt++;
				} else  {
					this.shdw.s[this.r][ci].cell.rowSpan++;
					tf[tf.length] = "";
				}
			}
		}

		var r = above ? this.r : this.r + 1;
		if (r >= this.shdw.s.length-1)
			r = this.shdw.s.length-1;

		var ci=0;
		var id = "";

		if (this.shdw.s[r][ci].cell.id) {
			if (tf[ci] != "") {
				tf[ci] = this._splittedMerge(
					this.shdw.s[r][ci].cell, tf[ci], true);
				id = tf[ci].id;
			}
			ci++;
		}
		for (; ci<this.shdw.s[0].length; ci++) {
			if (tf[ci] != "") {
				var hdrs = " " + id;
				if (this.shdw.s[0][ci].cell.id)
					hdrs += " " + this.shdw.s[0][ci].cell.id;
				if (hdrs != " ") {
					tf[ci].headers = hdrs.substring(1);
				}
				if (this.shdw.s[r][ci].cell.className) {
					tf[ci].className = this.shdw.s[r][ci].cell.className;
				}
			}
		}
	},

	deleteRow: function() {
		for (var ci=0; ci<this.shdw.s[this.r].length; ci++) {
			if (this.shdw.s[this.r][ci].left) {
				if (this.shdw.s[this.r][ci].top
						&& this.shdw.s[this.r][ci].bottom) {
					this.remDomId(this.shdw.s[this.r][ci].cell);
				} else {
					this.shdw.s[this.r][ci].cell.rowSpan--;
				}
			}
		}
		this.table.deleteRow(this.r);
	},

	_splittedMerge: function(s, t, sp) {
		if (t.tagName != s.tagName) {
			var nd = this.doc.createElement(s.tagName);
			t.parentNode.replaceChild(nd, t);
			t = nd;
		}
		SUI.browser.mergeAttributes(t, s);
		if (sp) {
			t.colSpan = 1;
			t.rowSpan = 1;
		}
		if (s.id)  {
			t.id = null;
			this.genDomId(t);
		}
		return t;
	},

	splitCell: function(horizontal) {
		if (horizontal) {
			if (this.shdw.s[this.r][this.c].left
					&& this.shdw.s[this.r][this.c].right) {
				for (var ri=0; ri<this.shdw.s.length; ri++) {
					if (this.shdw.s[ri][this.c].top) {
						if (this.shdw.s[ri][this.c].cell == this.cell) {
							var cnt = this.shdw.countCellsBefore(this.r, this.c);
							var cll =
								SUI.editor.htmleditor.tableeditor.insertCell(
									this.table.rows[this.r], cnt+1);
							cll = this._splittedMerge(
								this.shdw.s[this.r][this.c].cell, cll);
						} else {
							this.shdw.s[ri][this.c].cell.colSpan++;
						}
					}
				}
			} else {

				var span = this.shdw.cellWidth(this.r, this.c);
				var halfspan = Math.floor(span/2);
				this.shdw.s[this.r][this.c].cell.colSpan = span-halfspan;
				var cnt = this.shdw.countCellsBefore(this.r, this.c);
				var cll = SUI.editor.htmleditor.tableeditor.insertCell(
					this.table.rows[this.r], cnt+1);
				cll = this._splittedMerge(
					this.shdw.s[this.r][this.c].cell, cll);
				cll.colSpan = halfspan;
			}
		} else {
			if (this.shdw.s[this.r][this.c].top
					&& this.shdw.s[this.r][this.c].bottom) {
				this.table.insertRow(this.r+1);
				for (var ci=0; ci<this.shdw.s[this.r].length; ci++) {
					if (this.shdw.s[this.r][ci].left) {
						if (this.shdw.s[this.r][ci].cell == this.cell) {
							var cll =
								SUI.editor.htmleditor.tableeditor.insertCell(
									this.table.rows[this.r+1],
									this.table.rows[this.r+1].cells.length);
							cll = this._splittedMerge(
								this.shdw.s[this.r][ci].cell, cll);
						} else {
							this.shdw.s[this.r][ci].cell.rowSpan++;
						}
					}
				}
			} else {
				var span = this.shdw.cellHeight(this.r, this.c);
				var halfspan = Math.ceil(span/2);
				this.shdw.s[this.r][this.c].cell.rowSpan = span-halfspan;
				var cnt = this.shdw.countCellsBefore(this.r+1, this.c);
				var cll=SUI.editor.htmleditor.tableeditor.insertCell(
					this.table.rows[this.r+span-halfspan], cnt);
				cll = this._splittedMerge(
					this.shdw.s[this.r][this.c].cell, cll);
				cll.rowSpan = halfspan;
			}
		}
	},

	canMergeVertical: function(up) {
		var row = this.r;
		var dir = up ? -1: 1;
		if (up && row==0) {
			return false;
		}
		if (!up) {
			row += this.shdw.cellHeight(row, this.c)-1;
			if (row == this.shdw.s.length-1) {
				return false;
			}
		}
		for (var ci=this.c; ci<this.shdw.s[row].length; ci++) {
			if (this.shdw.s[row+dir][ci].left != this.shdw.s[row][ci].left
					|| this.shdw.s[row+dir][ci].right
						!= this.shdw.s[row][ci].right)
				return false;
			if (this.shdw.s[row][ci].right)
				break;
		}
		return true;
	},

	canMergeHorizontal: function(left) {
		var col = this.c;
		var dir = left ? -1: 1;
		if (left && col==0) {
			return false;
		}
		if (!left) {
			col += this.shdw.cellWidth(this.r, col)-1;
			if (col == this.shdw.s[this.r].length-1) {
				return false;
			}
		}
		for (var ri=this.r; ri<this.shdw.s.length; ri++) {
			if (this.shdw.s[ri][col+dir].top != this.shdw.s[ri][col].top
					|| this.shdw.s[ri][col+dir].bottom
						!= this.shdw.s[ri][col].bottom)
				return false;
			if (this.shdw.s[ri][col].bottom)
				break;
		}
		return true;
	},

	mergeCells: function(horiz, leftup) {
		if (leftup) {
			var nd = this.shdw.s[this.r][this.c].cell.cloneNode(false);
			if (horiz) this.c--; else this.r--;
			rc = this.shdw.findCell(this.shdw.s[this.r][this.c].cell);
			this.r = rc[0];
			this.c = rc[1];
			nd.colSpan = this.shdw.s[this.r][this.c].cell.colSpan;
			nd.rowSpan = this.shdw.s[this.r][this.c].cell.rowSpan;
			nd.id = this.shdw.s[this.r][this.c].cell.id;
			nd.innerHTML = this.shdw.s[this.r][this.c].cell.innerHTML;
			this.shdw.s[this.r][this.c].cell.parentNode.replaceChild(
				nd, this.shdw.s[this.r][this.c].cell);
			this.shdw.s[this.r][this.c].cell = nd;
		}
		if (horiz) {
			this.c += this.shdw.cellWidth(this.r,this.c)-1;
			this.shdw.s[this.r][this.c].cell.colSpan +=
				this.shdw.s[this.r][this.c+1].cell.colSpan;
			this.shdw.s[this.r][this.c].cell.innerHTML +=
				this.shdw.s[this.r][this.c+1].cell.innerHTML;
			cnt = this.shdw.countCellsBefore(this.r, this.c+1);
			this.remDomId(this.shdw.s[this.r][this.c].cell);
			this.table.rows[this.r].deleteCell(cnt);
		} else {
			this.r += this.shdw.cellHeight(this.r,this.c)-1;
			this.shdw.s[this.r][this.c].cell.rowSpan +=
				this.shdw.s[this.r+1][this.c].cell.rowSpan;
			this.shdw.s[this.r][this.c].cell.innerHTML +=
				this.shdw.s[this.r+1][this.c].cell.innerHTML;
			cnt = this.shdw.countCellsBefore(this.r+1,this.c);
			this.remDomId(this.shdw.s[this.r][this.c].cell);
			this.table.rows[this.r+1].deleteCell(cnt);
		}
	}

});
