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
 * $Id: editor.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/* Create script tags for a number of JavaScript files.
 */
function _editorlib(s) {
	var scripts= document.getElementsByTagName("script");
	var path = scripts[scripts.length-1].src.split("?")[0];
	path = path.replace("editor.js", "");
	for (var i=0; i<s.length; i++) {
	document.write("<script type=\"text/javascript\" src=\""
			+ path + s[i] + "\"></script>");
	}
}

/* Include the files of the core-library.
 */
_editorlib([
	"i18n.js",
	"xhr.js",
	"VerticalScrollBox.js",
	"AccessDialog.js",
	"CodeDialog.js",
	"LoginDialog.js",
	"PageDialog.js",
	"FolderDialog.js",
	"ColorDialog.js",
	"Scrivo.js",

	"menutree/MenuTree.js",
	"menutree/PagePropertyDialog.js",

	"htmleditor/tableeditor/functions.js",
	"htmleditor/tableeditor/TableCellMirror.js",
	"htmleditor/tableeditor/TableMirror.js",
	"htmleditor/tableeditor/TableEditor.js",
	"htmleditor/PageDialog.js",
	"htmleditor/PasteDialog.js",
	"htmleditor/LinkDialog.js",
	"htmleditor/ImageDialog.js",
	"htmleditor/LanguageDialog.js",
	"htmleditor/AbbreviationDialog.js",
	"htmleditor/AnchorDialog.js",
	"htmleditor/SymbolDialog.js",
	"htmleditor/FindDialog.js",
	"htmleditor/SuggestionsDialog.js",
	"htmleditor/TableDialog.js",
	"htmleditor/TableCellDialog.js",
	"htmleditor/TableSplitMergeDialog.js",
	"htmleditor/TableRowColDialog.js",
	"htmleditor/HTMLEditor.js",

	"filedialog/CacheDialog.js",
	"filedialog/CropDialog.js",
	"filedialog/DirList.js",
	"filedialog/FileDialog.js",
	"filedialog/FilenameDialog.js",
	"filedialog/FoldernameDialog.js",
	"filedialog/UploadDialog.js",

	"properties/BaseProperty.js",
	"properties/URL.js",
	"properties/Color.js",
	"properties/CheckBox.js",
	"properties/Input.js",
	"properties/SelectList.js",
	"properties/ColorList.js",
	"properties/Image.js",
	"properties/ImageAltTitle.js",
	"properties/Text.js",
	"properties/HTMLText.js",
	"properties/HTMLInput.js",
	"properties/Date.js",
	"properties/Info.js",

	"contenttabs/TabPanel.js",
	"contenttabs/PropertyBox.js",
	"contenttabs/PropertyPanel.js",
	"contenttabs/DefaultPanel.js",
	"contenttabs/HTMLEditorPanel.js",
	"contenttabs/ApplicationPanel.js",

	"apps/form/Form.js",
	"apps/form/FormElements.js",
	"apps/form/FormElementDialog.js",
	"apps/form/InputElementDialog.js",
	"apps/form/GroupElementDialog.js",
	"apps/form/CheckBoxDialog.js",
	"apps/form/InfoDialog.js",
	"apps/form/PositionDialog.js",
	"apps/form/PropertiesDialog.js",

	"apps/list/List.js",
	"apps/list/BlockList.js",
	"apps/list/DistributedList.js",
	"apps/list/ListView.js",
	"apps/list/ListItemDialog.js",
	"apps/list/PositionDialog.js"

]);
