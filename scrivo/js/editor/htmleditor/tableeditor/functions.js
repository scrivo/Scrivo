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
 * $Id: functions.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * Simple wrapper for insterCell. Makes sure each cell created contains
 * at least a &nbsp;
 */
SUI.editor.htmleditor.tableeditor.insertCell = function (row, idx, data) {
	var r = row.insertCell(idx);
	r.innerHTML = data ? data : "&nbsp;";
	return r;
};

/**
 * Convert a TD to a TH or vs.
 */
SUI.editor.htmleditor.tableeditor.setCellType = function(doc, cell, th) {
	if (th) {
	if (cell.tagName == "TH") return cell;
		elem = doc.createElement("TH");
	} else {
		if (cell.tagName == "TD") return cell;
		elem = doc.createElement("TD");
	}
	elem.innerHTML = cell.innerHTML;
	SUI.browser.mergeAttributes(elem, cell);
	// why does cell.colSpan <= 1 ? null not work in FF
	elem.colSpan = cell.colSpan <= 1 ? 1 : cell.colSpan;
	elem.rowSpan = cell.rowSpan <= 1 ? 1 : cell.rowSpan;
	if (cell.id) {
		elem.id = cell.id;
	}
	cell.parentNode.replaceChild(elem, cell);
	cell = elem;
	return cell;
};

/**
 * Convert integer value to an any number base
 * for example to hex: intToNbase(23, "0123456789ABCDEF");
 */
SUI.editor.htmleditor.tableeditor._intToNbase = function (val, baseStr) {
	var base = baseStr.length;
	var r = "";
	var t=val;
	do {
		r = baseStr.charAt(t % base) + r;
		t = Math.floor(t/base);
	} while (t>0);

	return r;
};

/**
 * Get the style selectors from a document
 */
SUI.editor.htmleditor.tableeditor.getStyleSelectors = function (doc) {
	var r = new Array();
	for (var i=0; i<doc.styleSheets.length; i++) {
		if (SUI.browser.isIE) {
			var imp = doc.styleSheets[i].imports;
			for (var k=0; k<imp.length; k++) {
				var rules2 = imp[k].rules;
				for (var j=0; j<rules2.length; j++) {
					if (rules2[j].selectorText) {
						r[r.length] = rules2[j].selectorText;
					}
				}
			}
			var rules = doc.styleSheets[i].rules;
			for (var j=0; j<rules.length; j++) {
				if (rules[j].selectorText) {
					r[r.length] = rules[j].selectorText;
				}
			}
		} else {
			var rules = doc.styleSheets[i].cssRules;
			for (var k=0; k<rules.length; k++) {
				if (rules[k].styleSheet) {
					var rules2 = rules[k].styleSheet.cssRules;
					for (var j=0; j<rules2.length; j++) {
						if (rules2[j].selectorText) {
							r[r.length] = rules2[j].selectorText;
						}
					}
				}
			}
			for (var j=0; j<rules.length; j++) {
				if (rules[j].selectorText) {
					r[r.length] = rules[j].selectorText;
				}
			}
		}
	}
	return r;
};

/**
 * Replace table ids with newly generated ones. Usefull when copying
 * a table: the copy can't use the ids of the original
 */
SUI.editor.htmleditor.tableeditor.replaceTableIds = function (doc, table) {
	var et = new EditTable();
	et.shdw = new SUI.editor.htmleditor.tableeditor.TableMirror(doc, table);
	et.doc = table.document;
	var nw = new Array();
	for (var i=0; i<et.shdw.hdr_arr.length; i++) {
		nw[nw.length] = et.shdw.hdr_arr[i].id;
		et.shdw.hdr_arr[i].removeAttribute("id");
		et.genDomId(et.shdw.hdr_arr[i]);
	}
	for (var i=0; i<et.shdw.s.length; i++) {
		for (var j=0; j<et.shdw.s[i].length; j++) {
			for (var k=0; k<et.shdw.s[i][j].headers.length; k++) {
				for (var l=0; l<nw.length; l++) {
					if (nw[l] == et.shdw.s[i][j].headers[k]) {
					et.shdw.s[i][j].headers[k] = et.shdw.hdr_arr[l].id;
					}
				}
			}
			et.shdw.s[i][j].cell.headers = et.shdw.s[i][j].headers.join(" ");
		}
	}
};

/**
 * Strip header-highlight-markup from the table
 */
SUI.editor.htmleditor.tableeditor._cleanTable = function (table, borders) {
	for (var ri=0; ri<table.rows.length; ri++) {
		var row = table.rows[ri];
		for (var ci=0; ci<row.cells.length; ci++) {
			var cll = row.cells[ci];
			SUI.style.removeClass(cll, "sys_table_head");
			SUI.style.removeClass(cll, "sys_table_axis");
			if(borders) SUI.style.removeClass(cll, "sys_show_cell");
		}
	}
};

/**
 * When the cursor is in a table, the header cells associated to a
 * data cell should be hilighted. The same for all header cells that
 * belong to one axis.
 * NB time-critical
 */
SUI.editor.htmleditor.tableeditor.highlightCells = function (he, doc, c) {

	while(c!=null && (c.tagName!="TD" && c.tagName!="TH")) {
		c=c.parentNode;
	}

	if (c != he.tableEditor.cell && c) {
		he.tableEditor.cell = c;

		var t = he.tableEditor.cell;
		while(t!=null && t.tagName!="TABLE"){
			t=t.parentNode;
		}

		if (he.tableEditor.table) {
			SUI.editor.htmleditor.tableeditor._cleanTable(
				he.tableEditor.table, t != he.tableEditor.table);
		}

		if (!t) {
			he.tableEditor = new EditTable();
			return;
		}

		//he.tableEditor.doc = he.tableEditor.cell.document;
		he.tableEditor.doc = doc;
		if (t != he.tableEditor.table) {
			he.tableEditor.table = t;
			he.tableEditor.shdw = new SUI.editor.htmleditor.tableeditor.TableMirror(
				doc, he.tableEditor.table);
		}
		he.tableEditor.rc = he.tableEditor.shdw.findCell(he.tableEditor.cell);
		he.tableEditor.r = he.tableEditor.rc[0];
		he.tableEditor.c = he.tableEditor.rc[1];

		var r = he.tableEditor.r;
		var c = he.tableEditor.c;

		if (he.tableEditor.shdw.s[r][c].headers.length>0) {
			for (var i=0; i<he.tableEditor.shdw.s[r][c].headers.length; i++) {
				try {
					SUI.style.addClass(he.tableEditor.doc.getElementById(
						he.tableEditor.shdw.s[r][c].headers[i]), "sys_table_head");
				} catch (e) {
					he.tableEditor.shdw.s[r][c].headers[i] = null;
					he.tableEditor.shdw.s[r][c].cell.headers =
						he.tableEditor.shdw.s[r][c].headers.join(" ");
				}
			}
		}
		if (he.tableEditor.cell.axis) {
			var t = he.tableEditor.cell.axis;
			for (var i=0; i<he.tableEditor.shdw.axis[t].length; i++) {
				SUI.style.addClass(
					he.tableEditor.shdw.axis[t][i], "sys_table_axis");
			}
		}
	} else if (!c) {
		if (he.tableEditor.table) {
			SUI.editor.htmleditor.tableeditor._cleanTable(
				he.tableEditor.table, true);
		}
		he.tableEditor = new SUI.editor.htmleditor.tableeditor.TableEditor({});
	}
};

/**
 * Create DOM nodes for table with a given no of rows and columns
 */
SUI.editor.htmleditor.tableeditor._createTable = function(doc, rows, cols) {
	var table = doc.createElement("TABLE");
	for (var i=0; i<rows; i++) {
		var row = table.insertRow(i);
		for (var j=0; j<cols; j++) {
			var cel = SUI.editor.htmleditor.tableeditor.insertCell(row, j);
		}
	}
	return table;
};

/**
 * Set one of the standard paterns of header cells (row, colum, none or both)
 * in a table
 */
SUI.editor.htmleditor.tableeditor._setHeads = function(t, col, row) {

	var val = 0;
	if (col) val += 1;
	if (row) val += 2;

	// remove all headers, axis and ids (starting with "scrv")
	for (i=0;i<t.table.rows.length; i++) {
		for (j=0;j<t.table.rows[i].cells.length; j++) {
			var cl = t.table.rows[i].cells[j];
			cl=SUI.editor.htmleditor.tableeditor.setCellType(t.doc, cl, false);
			if (cl.axis)
				cl.removeAttribute("axis");
			if (cl.headers)
				cl.removeAttribute("headers");
			if (cl.id)
				if (cl.id.substr(0,4) == "scrv")
					cl.removeAttribute("id");
		}
	}

	var i = t.table.rows.length;
	var j = t.table.rows[0].cells.length;
	switch (val) {
		case 1: j = 0; break;
		case 2: i = 0; break;
		case 3: i = 1; j = 1; break;
	}

	for (;i<t.table.rows.length; i++) {
		SUI.editor.htmleditor.tableeditor.setCellType(
			t.doc, t.table.rows[i].cells[0], true);
		t.genDomId(t.table.rows[i].cells[0]);
		t.table.rows[i].cells[0].axis="row";
	}
	for (;j<t.table.rows[0].cells.length; j++) {
		SUI.editor.htmleditor.tableeditor.setCellType(
			t.doc, t.table.rows[0].cells[j], true);
		t.genDomId(t.table.rows[0].cells[j]);
		t.table.rows[0].cells[j].axis="column";
	}

	/* Shadowtable alternative beter, but trickier to make header
		associations :
		for (;i<t.shdw.s.length; i++) {
			if (t.shdw.s[0][j].top) {
				SUI.editor.htmleditor.tableeditor.setCellType(
					t.doc, t.shdw.s[i][0].cell, true);
				t.genDomId(t.shdw.s[i][0].cell);
				t.shdw.s[i][0].cell.axis="rij";
			}
		}
		for (;j<t.shdw.s[0].length; j++) {
			if (t.shdw.s[0][j].left) {
				SUI.editor.htmleditor.tableeditor.setCellType(
					t.doc, t.shdw.s[0][j].cell, true);
				t.genDomId(t.shdw.s[0][j].cell);
				t.shdw.s[0][j].cell.axis="kolom";
			}
		}
	*/

	var r = 0;
	var c = 0;
	switch (val) {
		case 1: r = 1; break;
		case 2: c = 1; break;
		case 3: c = 1; r = 1; break;
	}

	if (c) {
		for (i=r;i<t.table.rows.length; i++) {
			for (j=c;j<t.table.rows[0].cells.length; j++) {
				var cl = t.table.rows[i].cells[j];
				var hdr = cl.headers ? cl.headers + " " : "";
				cl.headers = hdr + t.table.rows[i].cells[0].id;
			}
		}
	}
	if (r) {
		for (i=r;i<t.table.rows.length; i++) {
			for (j=c;j<t.table.rows[0].cells.length; j++) {
				var cl = t.table.rows[i].cells[j];
				var hdr = cl.headers ? cl.headers + " " : "";
				cl.headers = hdr + t.table.rows[0].cells[j].id;
			}
		}
	}
};

SUI.editor.htmleditor.tableeditor._cellStyleFromSet =
	function(tableStyles, typename, subset) {
		if (tableStyles[typename]) {
			var st = tableStyles[typename];
			for (var i=0; i<st.length; i++) {
				if (st[i] == subset) {
					return typename + "_" + subset;
				}
			}
		}
		return null;
	};

SUI.editor.htmleditor.tableeditor._setCellStyles =
	function (t, col, row, subset) {

		var val = 0;
		if (col) val += 1;
		if (row) val += 2;

		var idx = subset.indexOf("_");
		if (idx != -1) {
			subset = subset.substring(idx+1);
		} else {
			subset = "";
		}

		var cell = SUI.editor.htmleditor.tableeditor._cellStyleFromSet(
						t.styles, "scrivocell", subset);
		var cross = SUI.editor.htmleditor.tableeditor._cellStyleFromSet(
						t.styles, "scrivocellcross", subset);
		var col = SUI.editor.htmleditor.tableeditor._cellStyleFromSet(
						t.styles, "scrivocellcol", subset);
		var row = SUI.editor.htmleditor.tableeditor._cellStyleFromSet(
						t.styles, "scrivocellrow", subset);

		for (i=0;i<t.table.rows.length; i++) {
			for (j=0;j<t.table.rows[i].cells.length; j++) {
				SUI.style.setClass(t.table.rows[i].cells[j], cell);
			}
		}

		var i = t.table.rows.length;
		var j = t.table.rows[0].cells.length;
		switch (val) {
			case 1: j = 0; break;
			case 2: i = 0; break;
			case 3: i = 1; j = 1;
				SUI.style.setClass(t.table.rows[0].cells[0], cross);
			break;
		}

		for (;i<t.table.rows.length; i++) {
			SUI.style.setClass(t.table.rows[i].cells[0], row);
		}
		for (;j<t.table.rows[0].cells.length; j++) {
				SUI.style.setClass(t.table.rows[0].cells[j], col);
		}
	};

/**
 * Set an option in a selectlist. If no value is
 * specified then the select list is reset.
 * If a value is set, but it is not in the list,
 * then the value is added to the list
 */
SUI.editor.htmleditor.tableeditor.setSelOption = function(sel, val) {
	//if (val == "scrivocellcross_modern")
	if (!val || val == "") {
		sel.selectedIndex = -1;
		return;
	}

	var v = val.split(" ");
	var v2 = Array();
	for (var i=0; i<v.length; i++) {
		if (v[i].substr(0,4) != "sys_") {
			v2[v2.length] = v[i];
		}
	}

	for (var i=0; i<v2.length; i++) {
		for (var j=0; j<sel.options.length; j++) {
			if (sel.options[j].value == v2[i]) {
				sel.options[j].selected = true;
				return;
			}
		}
	}
	val = v2.join(" ");
	sel.options[sel.options.length] = new Option(val, val);
	sel.selectedIndex = sel.options.length-1;
};

/**
 * Strip value and unit from a css length value (34px or 50%).
 * Valid units defined in SUI.editor.resource.cssUnits
 */
SUI.editor.htmleditor.tableeditor.parseCssDim = function(cssDim) {

	var res = { value: cssDim, unit: "" };

	for (var i=0; i<SUI.editor.resource.cssUnits.length; i++) {
		if (-1 != cssDim.indexOf(SUI.editor.resource.cssUnits[i])) {
			res.unit = SUI.editor.resource.cssUnits[i];
			var t = cssDim.replace(res.unit, "");
			res.value = parseInt(t, 10);
			if (isNaN(res.value)) {
				res.value = 0;
			}
			break;
		}
	}
	return res;
};
