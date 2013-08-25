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
 * $Id: ApplicationPanel.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.contenttabs.ApplicationPanel = SUI.defineClass({

	baseClass: SUI.Panel,

	initializer: function(arg) {

		SUI.editor.contenttabs.ApplicationPanel.initializeBase(
			this, arg);
		var that = this;

		this.pageId = arg.pageId;
		this.pageDefinitionTabId = arg.pageDefinitionTabId;

		if (arg.onLoad) {
			this.cbOnLoad = arg.onLoad;
		}

		SUI.editor.xhr.doGet(
			SUI.editor.resource.ajaxURL, {
				a: "contenttabs.getApplication",
				pageId: this.pageId,
				pageDefinitionTabId: this.pageDefinitionTabId
			},
			function(res) {
				if (res.data.url.substr(0, 3) == "js:") {

					var cls = eval(res.data.url.substr(3));
					that.app = new cls({
						pageId: that.pageId,
						pageDefinitionTabId: that.pageDefinitionTabId,
						pagePropertyDefinitionId:
							res.data.pagePropertyDefinitionId,
						applicationDefinitionId:
							res.data.applicationDefinitionId,
						onLoad: function(){that.cbOnLoad();},
						scrollTop: 100
					});
					that.add(that.app);
					that.draw();

				} else {
					that.iframe = new SUI.Box({
						tag: "IFRAME",
						anchor:
							{ left: true, right: true, top: true, bottom: true }
					});
					that.iframe.el().src = res.data.url;
/* try to fix resize issues with iframes
					that.iframe.el().onload = function() {
						that.layOut();
					};
*/
					that.add(that.iframe);
					that.cbOnLoad();
				}
			}
		);
	},

	cbOnLoad: function() {},

	dataModified: function() {
		return false;
	},

/* try to fix resize issues with iframes
	layOut: function() {
		if (this.iframe && this.iframe.el().contentWindow.layOut) {
			this.iframe.el().contentWindow.layOut(
				this.iframe.width(), this.iframe.height());
		}
		SUI.editor.contenttabs.ApplicationPanel.parentMethod(this, "layOut");
	},
*/

	saveData: function() {
	}

});
