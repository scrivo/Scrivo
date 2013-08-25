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
 * Template select feed :
 *
 * $Id: SelectList.js 580 2013-03-27 12:16:37Z geert <?
 *	$res = array();
 *
 *	try {
 *	        $res["result"] = "OK";
 *	        $res["data"] = array(
 *	                array("text"=>"one", "value"=>1),
 *	                array("text"=>"two", "value"=>2),
 *	                array("text"=>"three", "value"=>3),
 *	                array("text"=>"four", "value"=>4),
 *	                array("text"=>"five", "value"=>5),
 *	        );
 *	//      throw new Exception("Whoops");
 *	} catch (Exception $e) {
 *	        $res["result"] = "ERROR";
 *	        $res["data"] = $e->getMessage();
 *	}
 *
 *	echo json_encode($res);
 *
 *
 */

//"use strict";

SUI.editor.properties.SelectList = SUI.defineClass({

	baseClass: SUI.editor.properties.BaseProperty,

	initializer: function(arg) {

		SUI.editor.properties.SelectList.initializeBase(this, arg);
		var that = this;

		this.unitHeight = 1;
		this.multiple = false;

	this.feed = false;
	this.tmpValue = null;

		var options = [];
		if (this.typeData.data) {
			var a = this.typeData.data.split(";");
			for (var i=0; i<a.length; i++) {
				var c = SUI.trim(a[i]);
				if (c != "") {
					var b = c.split(":",2);
					if (b.length == 2) {
						options.push({value: b[0], text: b[1]});
					} else {
						options.push({value: c, text: c});
					}
				}
			}
		}

	if (this.typeData.feed) {
		this.feed = true;
		SUI.editor.xhr.doGet(SCRIVO_BASE_DIR+"/"+this.typeData.feed, null, function(r) {
			that.feed = false;
			that.property.options(r.data);
			if (that.tmpValue!==null) {
				that.setValue(that.tmpValue);
				that.tmpValue=null;
			}
		});
	}

		if (this.typeData.type && this.typeData.type.toLowerCase() == "multiple") {
			this.multiple = true;
			if (this.typeData.size) {
				var s = parseInt(this.typeData.size, 10);
				this.unitHeight = Math.ceil(s/2);
			} else {
				this.unitHeight = 3;
			}
		}

		this.property = new SUI.form.SelectList({
			top: 0,
			right: 0,
			options: options,
			anchor: this.multiple ? { left: true, bottom:true, top: true }
				: { left: true }
		});

		if (this.multiple) {
			this.property.el().multiple = "multiple";
		}

		this.height(this.HEIGHT);

		this.add(this.property);
	},

	getValue: function() {
		if (this.property.el().multiple) {
			var r = [];
			for (i=0; i<this.property.el().options.length; i++) {
				if (this.property.el().options[i].selected) {
					r.push(this.property.el().options[i].value);
				}
			}
			return r;
		}
		return this.property.el().value;
	},

	setValue: function(val) {
	if (this.feed == true) {
		this.tmpValue = val;
		return;
	}
		if (this.property.el().multiple) {
			if (!val) val = "";
			for (i=0; i<this.property.el().options.length; i++) {
				this.property.el().options[i].selected =
					val.indexOf(this.property.el().options[i].value) != -1;
			}
		} else {
			this.property.el().value = val;
		}
		this.compare = this.getValue();
	},

	getUnitHeight: function() {
		return this.unitHeight;
	}

});
