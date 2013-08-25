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
 * $Id: UploadDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.filedialog.UploadDialog = SUI.defineClass(
	/** @lends SUI.editor.filedialog.UploadDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	/**
	 * @class <p>SUI.editor.filedialog.UploadDialog is an upload dialog window.
	 * It can be used to send one or more files simultaniously,
	 * or to send and overwrite a single file on the server. The UI can be
	 * notified that the upload is completed by the dialog's onUpload event.
	 * </p>
	 *
	 * <p>The dialog window creates and upload form and because uploading can't
	 * done with xhr an iframe element is created to serve as target for the
	 * upload action. The upload form supports to modi: as an upload for 1 to
	 * 10 new files or to overwrite one single file. In the first case the
	 * following data (multipart/form-data encoded) is send to the server:</p>
	 *
	 * <dl>
	 * <dt>dirId</dt>
	 * <dd>The asset id of the folder to upload the files to.</dd>
	 * <dt>userfile[]</dt>
	 * <dd>The data send by an array of file upload fields.</dd>
	 * </dl>
	 *
	 * <p>And In case of overwriting a file:</p>
	 *
	 * <dl>
	 * <dt>dirId</dt>
	 * <dd>The asset id of the folder to upload the files to.</dd>
	 * <dt>assetId</dt>
	 * <dd>The asset id of the file to overwrite.</dd>
	 * <dt>userfile[]</dt>
	 * <dd>The data send by a single upload field.</dd>
	 * </dl>
	 *
	 * <p>Note: userfile is still an array, but only one array element is
	 * allowed.</p>
	 *
	 * <p>The servers response is send to the iframe, and this response code
	 * will contain the following code:</p>
	 *
	 * <pre class="sh_javascript">
	 * window.onload = function() {
	 *     if (parent.uploaded) {
	 *           parent.uploaded(err);
	 *     }
	 * }
	 * </pre>
	 *
	 * <p>So the script in the page will check if there's a function named
	 * "uploaded" defined in the iframe's parent window, and if so, executes
	 * it. This function is implemented by the upload dialog and closes
	 * the dialog and calls the "onUpload" event listener.</p>
	 *
	 * @augments SUI.dialog.OKCancelDialog
	 *
	 * @description Create an upload dialog window. The files are uploaded
	 * to a virtual folder indicated by the uploadDir parameter. If an
	 * asset id is given as a parameter then the file with that asset id
	 * will be overwritten on the server. Also the posiblilty of creating more
	 * input fields will then be removed because we can't overwrite a single
	 * file by a group of files.
	 *
	 * @constructs
	 *
	 * @param {object} arg A parameter object in which the following members
	 *     can be set:
	 * @param {int} [arg.assetId] Only if we want to overwrite a
	 *     file on the server. Then this is the asset id of the file overwrite.
	 * @param {function} [arg.onUpload] An event listener function that is
	 *     triggered when the browser is finished with uploading the file.
	 *     See {@link #event:onUpload}
	 * @param {int} arg.uploadDir The (asset) id of the directory on the
	 *     server to upload the file to.
	 */
	initializer: function(arg) {

		var that = this;

		arg.caption =
			arg.caption || SUI.editor.i18n.filedialog.uploadDialog.uploadFiles;
		arg.clientWidth = arg.clientWidth || 350;

		this.uploadDir = arg.uploadDir;
		this.assetId = arg.assetId || null;

		SUI.dialog.Confirm.initializeBase(this, arg);

		// Create an hidden iframe.
		if (SUI.browser.isIE && SUI.browser.version < 8) {
			// patch for IE 7
			var ifr = document.createElement("<IFRAME name='sink'>");
		} else {
		var ifr = document.createElement("IFRAME");
		ifr.name = "sink";
		}
		ifr.style.display = "none";
		this.el().appendChild(ifr);

		// Create an upload form that sends it data to the hidden iframe.
		this.form = new SUI.form.Form({
			upload: true,
			action: SUI.editor.resource.filedialog.uploadURL,
			target: "sink"
		});

		// Create a file upload field for the file to upload.
		this.file = new SUI.form.Input({
			top: 10,
			left: 10,
			right: 10,
			bottom: 10,
			anchor: { right: true, left: true, top: true, bottom: true }
		});
		this.file.el().type  = "file";
		this.file.el().name = "userfile[]";

		// And an hidden input to send the folder id along with the file.
		this.dirId = new SUI.form.Input({});
		this.dirId.el().type = "hidden";
		this.dirId.el().name = "dirId";
		this.dirId.el().value = this.uploadDir;

		// Add it all to the dialog.
		this.clientPanel.add(this.form);
		this.form.add(this.file);
		this.form.add(this.dirId);

		// If we want to upload and overwrite the file ...
		if (this.assetId) {
		 // ... then we need a field for the assetId of the asset to
		 // overwrite.
			this.assetId = new SUI.form.Input({});
			this.assetId.el().type  = "hidden";
			this.assetId.el().name = "assetId";
			this.assetId.el().value = this.assetId;
			this.form.add(this.assetId);
		} else {
		 // ... else we add an extra button for more file upload fields.
			this.addExtraButton(
				SUI.editor.i18n.filedialog.uploadDialog.moreFiles,
				function(e) {
					that.moreFields();
				}
			);
		}

		// Register the onUpload event listener.
		if (arg.onUpload) {
		 this.addListener("onUpload", arg.onUpload);
		}

		// When the user clicks on OK, upload the file(s).
		this.addListener("onOK", this.upload);

		// Call the close function of the parent class when the user presses
		// on the cancel button: we disabled the close method of this class.
		this.addListener("onCancel",
		 function() {
		   SUI.editor.filedialog.UploadDialog.parentMethod(this, "close");
		 }
		);

		// Set function that can be accessed by the onload handler of the
		// upload result page.
		window.uploaded = function() {
	   SUI.editor.filedialog.UploadDialog.parentMethod(that, "close");
			that.callListener("onUpload");
		};

	},

	/**
	 * Trick to disable closing of the dialog when the user clicks on the
	 * OK button.
	 * @private
	 */
	close: function() {
	},

	/**
	 * Add 9 more upload fields to the client area of the control.
	 * @private
	 */
	moreFields: function() {

	 // Add 9 new upload fields to the dialog box.
		for (var i = 1; i < 10; i++) {
			var file = new SUI.form.Input({
				top: 10 + i*28,
				left: 10,
				right: 10,
				anchor: { right: true, left: true, top: true }
			});
			file.el().type  = "file";
			file.el().name = "userfile[]";
			this.form.add(file);
		}
		this.setClientHeight(10 + i*28);

		// Hide the button
		this.extraButton.el().style.display = "none";

		// Center and redisplay the window.
		this.center();
		this.draw();
	},

	/**
	 * @event
	 * onUpload event handler. This event handler is called when the
	 * browser is finished with uploading the file.
	 */
	onUpload: function() {
	},

	/**
	 * Add an 'is waiting' image to the client area of the dialog.
	 * @private
	 */
	setIsUploadingImage: function() {

	 // Remove the OK, more fields buttons add the form.
		this.okButton.el().style.display = "none";
		this.extraButton.el().style.display = "none";
		this.form.el().style.display = "none";

		// Create an image box and add it to the client panel.
		var img = new SUI.Box({
		 tag:"IMG",
		 width: 224,
		 height: 48
		});
		this.setClientWidth(img.width());
		this.setClientHeight(img.height());
		img.el().src = SUI.imgDir + "/" +
		 SUI.editor.resource.filedialog.aniWaitUpload;
		this.clientPanel.add(img);

		// Center and redisplay the window.
		this.center();
		this.draw();
	},

	/**
	 * Submit the form (upload the file) and show an 'is uploading' image.
	 * @private
	 */
	upload: function() {
		// Submit the form.
		this.form.el().submit();
		// Add an wait image to the client panel of the dialog.
		this.setIsUploadingImage();
	}

});
