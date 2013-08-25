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
 * $Id: TabPanel.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * Tabpanel that check if content was modified on the current tab
 * when the user switches to another tab.
 * It assumes that the component on the tab has a "dataModified" and
 * a "saveData" method. And if so, these methods will be delegated.
 */
SUI.editor.contenttabs.TabPanel = SUI.defineClass({

	baseClass: SUI.TabPanel,

	initializer: function(arg) {

		SUI.editor.contenttabs.TabPanel.initializeBase(this, arg);

	},

	selectTab: function(event) {
		var that = this;
		if (this.dataModified()) {
			new SUI.dialog.Confirm({
				caption: SUI.editor.i18n.scrivo.dataModified,
				text: SUI.editor.i18n.scrivo.saveChanges,
				onOK: function() {
					that.saveData();
					SUI.editor.contenttabs.TabPanel.parentMethod(that, "selectTab", event);
				},
				onCancel: function() {
					SUI.editor.contenttabs.TabPanel.parentMethod(that, "selectTab", event);
				}
			}).show();

		} else {
			SUI.editor.contenttabs.TabPanel.parentMethod(this, "selectTab", event);
		}
	},

	dataModified: function() {
		var st = this.selectedTab();
		if (st.content.children.length
			&& st.content.children[0].dataModified) {
			return st.content.children[0].dataModified();
		}
		return false;
	},

	saveData: function() {
		var st = this.selectedTab();
		if (st.content.children.length
			&& st.content.children[0].saveData) {
			return st.content.children[0].saveData();
		}
	}

});
