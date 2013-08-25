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
 * $Id: xhr.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * <p>The module SUI.editor.xhr contains wrapper function for the SUI.xhr.doGet
 * and SUI.xhr.doPost functions. These function can't (and shouldn't) display
 * any information if xhr request goes wrong. These wrapper functions do this
 * for you. Three different types of errors are dealt with:</p>
 * <ul>
 * <li>Should not happen error: UI.xhr.doGet/doPost throw an exception,
 *     probably due to an unparsable server result. An message box with the
 *     caption "XHR:Error" will be displayed, probably not with a very useful
 *     error description.</li>
 * <li>An handled error in the response from the server: probably the input
 *     parameters we're invalid and the server returns an error with
 *     description. This error is shown in a message box.</li>
 * <li>Authentication error: the user was inactive for a while, has thrown
 *     away his cookies or whatever, but the session had expired. Then the
 *     user is prompted to login again.</li>
 * </ul>
 * <p>Note: dealing with the authentication error is complicating the issue. It
 * is possible that the UI fires multiple xhr request at the same time as a
 * result of one UI event. This shouldn't result in multiple login boxes when
 * your not authenticated. Thus when dealing with this case only one login box
 * is shown and the data of the unauthenticated xhr request and the subsequent
 * unauthenticated xhr requests are stored to execute them later on.</p>
 * @namespace
 */
SUI.editor.xhr = {

	/**
	 * Execute a get request to an http server and supply a callback function
	 * to deal with the response.
	 * @param {String} url The url of an xhr script on a web server.
	 * @param {Object} data An object of which the members and their values
	 *     will be converted to an url query string and appended to the url.
	 *     Note: it is also possible to add a query string directly to the url
	 *     parameter, but then you should pass null for this parameter.
	 * @param {Function} callback A function with signature ({Object}) to
	 *     handle the server's response.
	 */
	doGet: function(url, data, callback) {
		try {
			// 'url', 'data', 'callback' and ...
			SUI.xhr.doGet(url, data, function(res) {
				// ... 'res' are all closure variables
				SUI.editor.xhr.handleRequest(url, data, callback, res, true);
			});
		} catch (e) {
			this.xhrErrorDlg("XHR:Error", e.message);
							}
	},

	/**
	 * Execute a post request to an http server and supply a callback function
	 * to deal with the response.
	 * @param {String} url The url of an xhr script on a web server.
	 * @param {Object} data An object of which the members and their values
	 *     will be converted to form encoded data and send along with the
	 *     request.
	 * @param {Function} callback A function with signature ({Object}) to
	 *     handle the server's response.
	 */
	doPost: function(url, data, callback) {
		try {
			// 'url', 'data', 'callback' and ...
			SUI.xhr.doPost(url, data, function(res) {
				// ... 'res' are all closure variables
				SUI.editor.xhr.handleRequest(url, data, callback, res, false);
			});
		} catch (e) {
			this.xhrErrorDlg("XHR:Error", e.message);
		}
	},

	/**
	 * An array of all pending xhr requests when not authenticated.
	 * @type Object[]
	 * @private
	 */
	requests: [],

	/**
	 * Show an error dialog.
	 * @private
	 */
	xhrErrorDlg: function (caption, message) {
		new SUI.dialog.Alert({
			icon: "error", width: 500, caption: caption, text: message
						}).show();
	},

	/**
	 * Execute all pending xhr requests and clear the pending requests
	 * array.
	 * @private
	 */
	doPostponed: function() {
		for (var i=0; i<this.requests.length; i++) {
			try {
				var a = this.requests[i];
				if (this.requests[i].get) {
					this.doGet(a.url, a.data, a.callback);
					} else {
					this.doPost(a.url, a.data, a.callback);
					}
			} catch (e) {
				this.xhrErrorDlg("XHR:Error", e.message);
				}
			}
		this.requests = [];
	},

	/**
	 * Process the result of an SUI.xhr.doGet/doPost request and deal with
	 * the (handled) error conditions of not being authenticated or an
	 * server error response. When an unauthenticated request is handled
	 * the request is parked to execute it later and the user is prompted
	 * to log in again. After a successful login the pending requests will
	 * be executed.
	 * @param {String} url The url of an xhr script on a web server.
	 * @param {Object} data An object of which the members and their values
	 *     will be converted to form encoded data and send along with the
	 *     request.
	 * @param {Function} callback A function with signature ({Object}) to
	 *     handle the server's response.
	 * @param {Object} res The result data of the request.
	 * @param {boolean} get Flag to indicate a get or post request.
	 * @private
	 */
	handleRequest: function(url, data, callback, res, get) {
		// if we've received a valid result ...
		if (res.result == "OK") {
			// ... process it
			callback(res);
		} else {
			// ... else check if it was an authentication error: ...
					if (res.result == "NO_AUTH") {
				// ... yes: is it the first unauthenticated request? ...
				if (this.requests.length == 0) {
					// ... then show a login dialog
						new SUI.editor.LoginDialog({
						width: 380,
							onOK: function() {
							SUI.editor.xhr.doPostponed();
							}
						}).show();
				}
				// ... and push all unauthenticated requests onto the requests
				// array
				this.requests.push({
					get: get,
					url: url,
					data: data,
					callback: callback
				});
					} else {
				// ... no: show the error message that the server send in
				// the response
				this.xhrErrorDlg(res.result, res.data);
					}
				}
			}

};
