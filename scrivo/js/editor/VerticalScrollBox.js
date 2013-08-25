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
 * $Id: VerticalScrollBox.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/**
 * Class SUI.editor.VerticalScrollBox helps you to prevent horizontal scroll
 * bars. At times a horizontal scroll bar pop up in reaction to the
 * occurance of a vertical scroll bar. The horizontal scroll bar then lets
 * you scroll just the amount of width of the vertical scroll bar. Wrapping
 * this SUI.editor.VerticalScrollBox around an other Sui.Box component will
 * prevent this. SUI.editor.VerticalScrollBox also gives support for setting
 * the scroll top during the display phase.
 */
SUI.editor.VerticalScrollBox = SUI.defineClass({

	/**
	 * SUI.editor.VerticalScrollBox is a SUI.Box component.
	 */
	baseClass: SUI.AnchorLayout,

	/**
	 * A SUI.editor.VerticalScrollBox is just a SUI.Box that has an overflow
	 * style of 'auto' and by default anchors to all sides. Despite the 'auto'
	 * setting a horizontal scroll bar will be prevented.
	 */
	initializer: function(arg) {

		SUI.editor.VerticalScrollBox.initializeBase(this, arg);

		this.anchor = { left: true, top: true, right: true, bottom: true };
		this.el().style.overflow = "auto";
	},

	/**
	 * Override SUI framework diplay method.
	 */
	display: function() {

		// Do the standard display method
		SUI.editor.apps.list.List.parentMethod(this, "display");

		// Now we can set the elements scroll top
		if (this._scrollTop) {
			this.el().scrollTop = this._scrollTop;
		}
	},

	/**
	 * Get the scroll top of the VerticalScrollBox.
	 */
	getScrollTop: function() {
		// Return the elements current scroll top
		return this.el().scrollTop;
	},

	/**
	 * Override SUI framework layOut method.
	 */
	layOut: function() {

		// A VerticalScrollBox is not allowed to have more than one child
		if (this.children.length != 1) {
			throw "VerticalScrollBox has "+this.children.length+" children";
		}

		// Do the standard layOut
		SUI.editor.VerticalScrollBox.parentMethod(this, "layOut");

		// Now if we detect a vertical scroll bar limit the width of the
		// child so there will be no horizontal scroll bar
		if (this.children[0].height() > this.height()) {
			this.children[0].width(
				this.children[0].width() - SUI.style.scrollbarWidth());
			this.children[0].layOut();
		}

	},

	/**
	 * Set the scroll top of the VerticalScrollBox.
	 */
	setScrollTop: function(st) {
		// Try to set it directly on the element ...
		this.el().scrollTop = st;
		// .. but also store the value because we need it for the diplay method
		this._scrollTop = st;
	},

	// Store the scrollTop because it can only be set after the div is rendered
	_scrollTop: 0

});
