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
 * $Id: ColorDialog.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

SUI.editor.ColorDialog = SUI.defineClass(
	/** @lends SUI.editor.ColorDialog.prototype */{

	/** @ignore */ baseClass: SUI.dialog.OKCancelDialog,

	/**
	 * @class
	 *
	 * @augments SUI.dialog.OKCancelDialog
	 *
	 * @description
	 * Create a color selection dialog. The dialog has three tabs that provide
	 * three different ways of selecting a color.
	 *
	 * @constructs
	 */
	initializer: function(arg) {

		SUI.editor.ColorDialog.initializeBase(this, arg);
		var that = this;

		if (arg.color) {
			this.color = arg.color;
		}

		this.caption(SUI.editor.i18n.colorDialog.caption);

		this.populateForm();

		this.colorTablePicker.colorCode(this.color);
		this.hSVColorPicker.colorCode(this.color);
		this.rGBColorPicker.colorCode(this.color);
	},

	color: "#CCCCCC",

	/**
	 * A table of the so called web save palette (a blast from the past)
	 * reorganized on their hues to create a color wheel.
	 * @type array()
	 * @private
	 */
	webPalette: [
		"#FFF","#CCC","#999","#666","#333","#000","#FC0","#F90",
		"#F60","#F30","#000","#333","#666","#999","#CCC","#FFF",
		"#9C0","#000","#000","#000","#000","#C90","#FC3","#FC6",
		"#F96","#F63","#C30","#000","#000","#000","#000","#C03",
		"#CF0","#CF3","#330","#660","#990","#CC0","#FF0","#C93",
		"#C63","#300","#600","#900","#C00","#F00","#F36","#F03",
		"#9F0","#CF6","#9C3","#663","#993","#CC3","#FF3","#960",
		"#930","#633","#933","#C33","#F33","#C36","#F69","#F06",
		"#6F0","#9F6","#6C3","#690","#996","#CC6","#FF6","#963",
		"#630","#966","#C66","#F66","#903","#C39","#F6C","#F09",
		"#3F0","#6F3","#390","#6C0","#9F3","#CC9","#FF9","#C96",
		"#C60","#C99","#F99","#F39","#C06","#906","#F3C","#F0C",
		"#0C0","#3C0","#360","#693","#9C6","#CF9","#FFC","#FC9",
		"#F93","#FCC","#F9C","#C69","#936","#603","#C09","#303",
		"#3C3","#6C6","#0F0","#3F3","#6F6","#9F9","#CFC","#000",
		"#000","#000","#C9C","#969","#939","#909","#636","#606",
		"#060","#363","#090","#393","#696","#9C9","#000","#000",
		"#000","#FCF","#F9F","#F6F","#F3F","#F0F","#C6C","#C3C",
		"#030","#0C3","#063","#396","#6C9","#9FC","#CFF","#39F",
		"#9CF","#CCF","#C9F","#96C","#639","#306","#90C","#C0C",
		"#0F3","#3F6","#093","#0C6","#3F9","#9FF","#9CC","#06C",
		"#69C","#99F","#99C","#93F","#60C","#609","#C3F","#C0F",
		"#0F6","#6F9","#3C6","#096","#6FF","#6CC","#699","#036",
		"#369","#66F","#66C","#669","#309","#93C","#C6F","#90F",
		"#0F9","#6FC","#3C9","#3FF","#3CC","#399","#366","#069",
		"#039","#33F","#33C","#339","#336","#63C","#96F","#60F",
		"#0FC","#3FC","#0FF","#0CC","#099","#066","#033","#39C",
		"#36C","#00F","#00C","#009","#006","#003","#63F","#30F",
		"#0C9","#000","#000","#000","#000","#09C","#3CF","#6CF",
		"#69F","#36F","#03C","#000","#000","#000","#000","#30C",
		"#FFF","#CCC","#999","#666","#333","#000","#0CF","#09F",
		"#06F","#03F","#000","#333","#666","#999","#CCC","#FFF"
	],

	populateForm: function() {

		var that = this;

		// Set the client size of the dialog.
		this.setClientHeight(195);
		this.setClientWidth(343);

		// Remove the inner border that SUI.dialog.OKCancelDialog by default
		// provides.
		this.clientPanel.inner.border(new SUI.Border());

		// Create a tab panel with three tabs.
		this.tabPanel = new SUI.TabPanel({
			tabs: [
				{ title: SUI.editor.i18n.colorDialog.palette },
				{ title: SUI.editor.i18n.colorDialog.rgb },
				{ title: SUI.editor.i18n.colorDialog.hsv }
			],
			selected: 0
		});

		// Create a color picker table control.
		this.colorTablePicker = new SUI.control.ColorTablePicker({
			table: this.webPalette,
			rows: 16,
			columns: 16,
			onChange: function(e) {
				that.color = e;
				that.hSVColorPicker.colorCode(that.color);
				that.rGBColorPicker.colorCode(that.color);
			}
		});

		// Create an RGB color picker control.
		this.rGBColorPicker = new SUI.control.RGBColorPicker({
			onChange: function(e) {
				that.color = e;
				that.colorTablePicker.colorCode(that.color);
				that.hSVColorPicker.colorCode(that.color);
			}
		});

		// Create an HSV color picker table control.
		this.hSVColorPicker = new SUI.control.HSVColorPicker({
			onChange: function(e) {
				that.color = e;
				that.colorTablePicker.colorCode(that.color);
				that.rGBColorPicker.colorCode(that.color);
			}
		});

		// Add the tab panel to the client panel.
		this.clientPanel.add(this.tabPanel);
		// Add the three color picker controls to the tab panels.
		this.tabPanel.add(this.colorTablePicker, 0);
		this.tabPanel.add(this.rGBColorPicker, 1);
		this.tabPanel.add(this.hSVColorPicker, 2);
	},

	formToData: function() {
		this.close();
		return this.color;
	}

});
