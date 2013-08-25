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
 * $Id: TableCellMirror.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

// STC
SUI.editor.htmleditor.tableeditor.TableCellMirror = SUI.defineClass({

	initializer: function(cell, rowSpan, colSpan) {

		this.rowSpan = rowSpan;
		this.colSpan = colSpan;
		this.cell = cell;
		this.headers = [];

		this.id = this.cell.id;
		this.axis = this.cell.axis ? this.cell.axis : "";

		var hdrs = this.cell.headers ? this.cell.headers.split(" ") : "";
		for (var i=0; i<hdrs.length; i++) {
			if (hdrs[i] != "") this.headers[this.headers.length] = hdrs[i];
		}
		//  if (this.headers.length > 0) alert (this.headers[0]);
	},

	rowSpan: null,
	colSpan: null,
	top: true,
	bottom: true,
	left: true,
	right: true,
	cell: null,
	id: null,
	axis: null,
	headers: null

});

