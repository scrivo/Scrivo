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
 * $Id: CodeDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * SUI.editor.CodeDialog is an editor dialog for code-like content (CSS,
 * JavaScript, HTML, etc.). It's a very trivial dialog that only shows
 * a large textarea for modifing the code.
 */
SUI.editor.CodeDialog = SUI.defineClass({

	/**
	 * We need a dialog with an OK and Cancel button so extend from
	 * SUI.dialog.OKCancelDialog
	 */
	baseClass: SUI.dialog.OKCancelDialog,

	/**
	 * Constructor for the code dialog.
	 * @param see base class
	 * @param {obj.string} code The code to edit
	 */
	initializer: function(arg) {

		// set default window size for the dialog
		arg.height = arg.height || 400;
		arg.width = arg.width || 500;

		SUI.editor.CodeDialog.initializeBase(this, arg);

		// Create a new text area an anchor it to all sides of the client area
		this.textArea = new SUI.Box({
			tag: "TEXTAREA",
			anchor: { left: true, right: true, top: true, bottom: true }
		});

		// We like to edit code in a momo spaced font
		this.textArea.el().style.fontFamily = "monospace";

		// Add the textarea to the client panel
		this.clientPanel.add(this.textArea);

		// And set the value of the text area
		this.textArea.el().value = arg.code ? arg.code : "";
	},

	/**
	 * Transfer the contens of the textarea as the return value
	 */
	formToData: function() {
		this.close();
		return this.textArea.el().value;
	}

});
