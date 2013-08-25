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
 * $Id: TableMirror.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.htmleditor.tableeditor.TableMirror = SUI.defineClass({

	initializer: function(doc, table) {

		this.axis = [];
		this.hdr_arr = [];
		this.datacells = [];

		this.doc = doc;
		this.t = table;

		this.s = this._borders(this._square(table, this._create(this, table)));
		this._classify(this);
	},

	_firstSpan: function(shdw, r, c) {
		var s = shdw[r][c].rowSpan;
		var res = r;
		for (var ri = r-1; ri>=0; ri--) {
			if (!shdw[ri][c] || shdw[ri][c].rowSpan < s)
				break;
			res = ri;
		}
		return res;
	},

	_classify: function(s) {
		var list = s.datacells;
		s.datacells = new Array();

		for (var i=0; i<list.length; i++) {
			var hdr = false;
			if (list[i].tagName=="TH" || list[i].axis) {
				hdr = true;
			} else if (list[i].id) {
				for (var j=0; j<list.length; j++) {
					if (list[j].tagName!="TH" && !list[j].axis
							&& list[j].id!=list[i].id) {
						if (list[j].headers) {
							if (list[j].headers.search(
									new RegExp(list[i].id, "g")) != -1) {
								hdr = true;
								break;
							}
						}
					}
				}
			}

			if (hdr) {
				if (list[i].headers) {
					list[i].headers = null;
				}
				s.hdr_arr[s.hdr_arr.length] = list[i];
			} else {
				if (list[i].axis) {
					list[i].axis = null;
				}
				s.datacells[s.datacells.length] = list[i];
			}
		}

		for (var i=0; i<s.hdr_arr.length; i++) {

			if (s.hdr_arr[i].axis) {
				if (!s.axis[s.hdr_arr[i].axis]) {
					s.axis[s.hdr_arr[i].axis] = new Array();
				}
				s.axis[s.hdr_arr[i].axis][s.axis[s.hdr_arr[i].axis].length]
					= s.hdr_arr[i];
			}
		}
		//alert(s.hdr_arr.length)
	},

	_handleHeader: function(shdwtbl, cell) {
		shdwtbl.datacells[shdwtbl.datacells.length] = cell;
		//var p = DOM.currentStyle(cell, "borderStyle");
		var p = SUI.browser.currentStyle(cell, "borderStyle");
		if (p == "none" || p == "") {
			SUI.style.addClass(cell, "sys_show_cell");
		}
	},

	_create: function (shdwtbl, table) {
		var shdw = new Array();
		var xuc=0;
		for (var ri=0; ri<table.rows.length; ri++) {
			shdw[ri] = new Array();
			var row = table.rows[ri];
			var uc=0;
			for (var ci=0; ci<row.cells.length; ) {
				if (ri>0 && shdw[ri-1][uc] && shdw[ri-1][uc].rowSpan > 1) {
					var fr = this._firstSpan(shdw, ri-1,uc);
					shdw[ri][uc] =
						new SUI.editor.htmleditor.tableeditor.TableCellMirror(
							shdw[fr][uc].cell, shdw[ri-1][uc].rowSpan-1,
							shdw[fr][uc].colSpan);
					uc++;
					continue;
				}
				var cell = row.cells[ci];
				this._handleHeader(shdwtbl, cell);
				for (var si=0; si<cell.colSpan; si++) {
					var sp = cell.rowSpan;
					if (ri>0 && shdw[ri-1][uc] && shdw[ri-1][uc].rowSpan > 1) {
						sp = shdw[ri-1][uc].rowSpan-1;
					}
					shdw[ri][uc] =
						new SUI.editor.htmleditor.tableeditor.TableCellMirror(
							cell, sp, cell.colSpan-si);
					uc ++;
				}
				ci++;
			}
			if (xuc < uc) {
				xuc = uc;
			}
			for (; uc < xuc; uc++) {
				if (shdw[ri-1][uc] && shdw[ri-1][uc].rowSpan != 1) {
					var fr = this._firstSpan(shdw, ri-1,uc);
					shdw[ri][uc] =
						new SUI.editor.htmleditor.tableeditor.TableCellMirror(
							shdw[fr][uc].cell, shdw[ri-1][uc].rowSpan-1,
							shdw[fr][uc].colSpan);
				}
			}
		}
		return shdw;
	},

	_square: function(table, shdw) {
		var mx = 0;
		for (var ri=0; ri<shdw.length; ri++)
			if (shdw[ri].length > mx)
				mx = shdw[ri].length;
		for (var ri=0; ri<shdw.length; ri++)
			for (var ci=shdw[ri].length; ci<mx; ci++) {
				var row = table.rows[ri];
				var cell = SUI.editor.htmleditor.tableeditor.insertCell(
					row, row.cells.length) ;
				shdw[ri][ci] =
					new SUI.editor.htmleditor.tableeditor.TableCellMirror(
						cell, 1, 1);
			}
		return shdw;
	},

	_borders: function(shdw) {
		for (var ri=0; ri<shdw.length; ri++)
			for (var ci=1; ci<shdw[ri].length; ci++)
				if (shdw[ri][ci-1].colSpan != 1) {
					shdw[ri][ci-1].right = false;
					shdw[ri][ci].left = false;
				}
		for (var ri=1; ri<shdw.length; ri++)
			for (var ci=0; ci<shdw[ri].length; ci++)
				if (shdw[ri-1][ci] && shdw[ri-1][ci].rowSpan > 1) {
					shdw[ri][ci].top = false;
					shdw[ri-1][ci].bottom = false;
				}
		return shdw;
	},

	countCellsBefore: function(r, c) {
//    function _cellsBefore(r, c) {
		if (c == 0) return 0;
		var cnt = 0;
		var cell = this.s[r][c].cell;
		for (var i=c-1; i>=0; i--) {
			if (this.s[r][i].top && this.s[r][i].left)
				cnt++;
		}
		return cnt;
	},

//    function _cellsRight(r, c) {
	cellWidth: function(r, c) {
		for (var i=0; i<this.s[r].length; i++)
			if (this.s[r][c+i].right)
				break;
		return i+1;
	},

//    function _cellsAbove(r, c) {
	cellHeight: function(r, c) {
		for (var i=0; i<this.s.length; i++)
			if (this.s[r+i][c].bottom)
				break;
		return i+1;
	},

	findCell: function(cell) {
//    function _findCell(cell) {
		for (var ri=0; ri<this.s.length; ri++)
			for (var ci=0; ci<this.s[ri].length; ci++)
				if (this.s[ri][ci].cell == cell)
					return new Array(ri, ci);
	},

	dump: function() {
		var str="";
		for (var ri=0; ri<this.s.length; ri++) {
			str += "\n";
			for (var ci=0; ci<this.s[ri].length; ci++) {
				str += ("[" +
	//     this.s[ri][ci].top + ", " + this.s[ri][ci].bottom  +
				" " +
	//     this.s[ri][ci].rowSpan + ", " + this.s[ri][ci].colSpan  +
				" " +
				this.s[ri][ci].left + ", " + this.s[ri][ci].right +
				" " +
	//     (this.s[ri][ci].cell ? this.s[ri][ci].cell.innerText : "") +
				"]");
			}
		}
		//console.log(str);
	}

});
