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
 * $Id: DirList.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.DirList = SUI.defineClass({

	baseClass: SUI.Box,

	initializer: function(arg) {

		SUI.editor.filedialog.DirList.initializeBase(this, arg);
		var that = this;

		this.sel = document.createElement("SELECT");
		this.el().appendChild(this.sel);
		this.sel.style.position = "absolute";
		this.anchor = { right: true, left: true };

		this.type = "dirlist";

		this.width(200);
		this.height(30);
	},

	layOut: function() {
		this.setRect(this);
		this.sel.style.top = 2 + "px";
		this.sel.style.left = 2 + "px";
		this.sel.style.width = this.width()-2 + "px";
	},

	loadData: function(path, feeds) {
		this.sel.options.length = 0;
		var ind = "";
		for (var i=0; i<path.length; i++) {
			this.sel.options[this.sel.options.length] =
				new Option(ind + path[i].title, path[i].assetId);
			ind += "\u00A0.\u00A0";
		}
		this.sel.options[this.sel.options.length-1].selected = true;

		// add the feeds
		for (var i=0; i<feeds.length; i++) {
			this.sel.options[this.sel.options.length] =
				new Option(feeds[i].title, "feed_" + feeds[i].feedId);
		}
	}

});

