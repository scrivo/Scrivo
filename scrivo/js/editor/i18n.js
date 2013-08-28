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
 * $Id: i18n.js 842 2013-08-19 22:54:50Z geert $
 */

//"use strict";

/** @namespace */
SUI.editor = { scrivo: null };
/** @namespace */
SUI.editor.menutree = {};
/** @namespace */
SUI.editor.htmleditor = {};
/** @namespace */
SUI.editor.htmleditor.tableeditor = {};
/** @namespace */
SUI.editor.filedialog = {};
/** @namespace */
SUI.editor.properties = {};
/** @namespace */
SUI.editor.contenttabs = {};
/** @namespace */
SUI.editor.apps = {};
/** @namespace */
SUI.editor.apps.list = {};
/** @namespace */
SUI.editor.apps.form = {};

/* The language and resource keys and their default values are defined in this
 * file. The file is organized as follows: language keys are defined up the
 * sub-unit (e.g. library of dialog) and resource keys only at section level.
 * In principle each key is defined where it used, but in cases where a
 * really common resource is used in different sections, it is possible to
 * define the key at the highest common parent (but only really save cases
 * please).
 * The section names are closely linked with the namespaces/code files in
 * which these keys are used: please look for the pattern.
 * Note: The sections are in hierarchical-alphabetical order, keep it this
 * way!
 */

/*=[SUI.editor.i18n]=========================================================*/

/** @namespace */
SUI.editor.i18n = {};

/** @namespace */
SUI.editor.i18n.accessDialog = {
	captionFolder: "Change folder access",
	captionPage: "Change page access",
	accessPublic: "Public access",
	accessEditor: "Editor access",
	accessOneEditorRole: "At least one editor role should keep access",
	errTextNoId: "AccessDialog: no id set"
};

/** @namespace */
SUI.editor.i18n.colorDialog = {
	caption: "Color dialog",
	palette: "Palette",
	rgb: "RGB",
	hsv: "HSV"
};

/** @namespace */
SUI.editor.i18n.folderDialog = {
	cptSelectFolder: "Select Folder"
};

/** @namespace */
SUI.editor.i18n.loginDialog = {
	caption: "Please login",
	text: "Your session has expired, please login again.",
	usercode: "Usercode",
	password: "Password"
};

/** @namespace */
SUI.editor.i18n.pageDialog = {
	cptSelectPage: "Select Page"
};

/** @namespace */
SUI.editor.i18n.scrivo = {
	treeHeader: "Site structure",
	openHelpWindow:    "Open help window",
	helpLink: "http://www.scrivo.nl/help/scrivo2/index.html",
	dataModified: "Data modified!",
	saveChanges: "Do you want to save your changes?"
};

/*=[SUI.editor.i18n.contenttabs]=============================================*/

/** @namespace */
SUI.editor.i18n.contenttabs = {
	dataSaved: "Data saved",
	save: "Save"
};

/** @namespace */
SUI.editor.i18n.contenttabs.defaultPanel = {
	style: "Page specific CSS",
	script: "Page specific javascript",
	access: "Change page access",
	title: "Title",
	template: "Template",
	language: "Language",
	created: "Created",
	modified: "Modified",
	online: "Online on",
	offline: "Offline on",
	description: "Description",
	keywords: "Keywords"
};

/*=[SUI.editor.i18n.filedialog]==============================================*/

/** @namespace */
SUI.editor.i18n.filedialog = {};

/** @namespace */
SUI.editor.i18n.filedialog.cacheDialog = {
	captionCache: "Edit cache properties for files in this folder",
	cacheNormal:
		"Files in this folder will only be downloaded if they are  modified",
	cacheNone: "Prevent caching by the browser of the files in this folder " +
		"(the files will always be downloaded)",
	cacheExpires: "Keep the file in this folder in the browser's cache for " +
		" [], then download if modified",
	cacheTimes: ["minutes","hours","days","weeks","months","years"]
};

/** @namespace */
SUI.editor.i18n.filedialog.cropDialog = {
	caption: "Crop and/or resize image",
	width: "Width",
	height: "Height",
	set: "Set",
	pixels: "pixels",
	error: "Enter the desired width and/or height first!"
};

/** @namespace */
SUI.editor.i18n.filedialog.fileDialog = {

	caption: "File manager",

	actListSelect: "Select",
	actListUp: "Open the parent folder",
	actListRefresh: "Refresh list",
	actListUpload: "Upload file(s) to the server",
	actListOverwrite: "Upload and overwrite a file",
	actListNewfolder: "Create a new folder",
	actTreeNewfolder: "Create a new folder",
	actListCut: "Cut file(s) and/or folder(s)",
	actTreeCut: "Cut folder",
	actListPaste: "Paste",
	actTreePaste: "Paste into folder",
	actListCrop: "Resize and/or crop picture",
	actListRename: "Rename file or folder",
	actTreeRename: "Rename folder",
	actListDelete: "Delete file(s) and/or folder(s)",
	actTreeDelete: "Delete folder",
	actListAccess: "Change access to this folder",
	actTreeAccess: "Change access to this folder",
	actListCache: "Change cache settings",
	actTreeCache: "Change cache settings",

	name: "Name",
	size: "Size",
	type: "Type",
	modified: "Modified",

	fileInformation: "File information",
	folders: "Folders",
	butSelect: "Select",

	captionDeleteFiles: "Confirm file or folder deletion",
	textDeleteFiles:
		"Are you sure you want to delete the selected file(s) and/or folder(s)?"

};

/** @namespace */
SUI.editor.i18n.filedialog.filenameDialog = {
	captionFile: "Edit file properties",
	fileName: "File name",
	onlineOn: "Online on",
	offlineOn: "Offline on",
	invalidOnlineDate: "Invalid online date",
	invalidOfflineDate: "Invalid offline date"
};

/** @namespace */
SUI.editor.i18n.filedialog.foldernameDialog = {
	captionNewFolder: "Create new folder",
	captionFolder: "Edit folder properties",
	folderName: "Folder name"
};

/** @namespace */
SUI.editor.i18n.filedialog.uploadDialog = {
	moreFiles: "More files",
	uploadFiles: "Upload file(s)"
};

/*=[SUI.editor.i18n.htmleditor]==============================================*/

/** @namespace */
SUI.editor.i18n.htmleditor = {
	butRemove: "Remove"
};

/** @namespace */
SUI.editor.i18n.htmleditor.abbreviationDialog = {
	cptEditAbbreviation: "Edit abbreviation",
	cptNewAbbreviation: "Insert abbreviation",
	txtAbbreviation: "Abbreviation"
};

/** @namespace */
SUI.editor.i18n.htmleditor.anchorDialog = {
	cptEditAnchor: "Edit anchor",
	cptNewAnchor: "Insert anchor",
	txtAnchor: "Anchor"
};

/** @namespace */
SUI.editor.i18n.htmleditor.findDialog = {
	cptFind: "Find/Replace",
	txtFind: "Find",
	txtReplace: "Replace",
	txtOptions: "Options",
	txtWholeWords: "Match whole words only",
	txtMatchCase: "Match case",
	txtDirection: "Direction",
	txtUp: "Up",
	txtDown: "Down",
	butFind: "Find",
	butReplace: "Replace",
	butReplaceAll: "Replace all"
};

/** @namespace */
SUI.editor.i18n.htmleditor.htmlEditor = {

	actSave: "Save",
	actCut: "Cut",
	actCopy: "Copy",
	actPaste: "Paste",
	actUndo: "Undo",
	actRedo: "Redo",
	actFind: "Find/replace",
	actTable: "Insert/edit table",
	actRowColumn: "Insert/remove tablerow or -colum",
	actSplitMergeCell: "Split or merge table cell",
	actCellProperties: "Cell properties",
	actLink: "Insert/edit link",
	actImage: "Insert/edit Image",
	actLanguage: "Insert/edit language mark",
	actAbbreviation: "Insert/edit full form for abbreviation",
	actAnchor: "Insert/edit anchor",
	actPageBreak: "Insert page break",
	actInsertSymbol: "Insert symbol",
	actHighlightColor: "Mark text",
	actTextColor: "Text color",
	actRemoveFormatting: "Remove formatting",
	actSpellCheck: "Spell check",
	actHTML: "Edit HTML codes",
	actBold: "Bold",
	actItalic: "Italic",
	actUnderline: "Underline",
	actAlignLeft: "Left alignment",
	actAlignRight: "Right alignment",
	actAlignCenter: "Center alignment",
	actOrderedList: "Ordered list",
	actUnorderedList: "Unordered list",
	actIndent: "Increase indent",
	actDeIndent: "Decrease indent",

	browserUnsupportedCmd: "Unsupported command",
	browserSecurityWarningCut: [
		"Your browser's security settings do no allow you to cut text this"
		+ " way.{BR}Please use the keyboard shortcut ({SC}) instead.",
		"Ctrl+X"],
	browserSecurityWarningCopy: [
		"Your browser's security settings do no allow you to copy text this"
		+ " way.{BR}Please use the keyboard shortcut ({SC}) instead.",
		"Ctrl+C"],
	browserSecurityWarningPaste: [
		"Your browser's security settings do no allow you to paste text this"
		+ " way.{BR}Please use the keyboard shortcut ({SC}) instead.{BR}",
		"Ctrl+V"],
	browserPasteMethod:
		"{BR}Use the setting below to select how the data should be pasted",

	errNoCellSelected: "No cell selected",

	txtOccurencesReplaced: "Replace complete. Replaced [] occurrence(s)",
	txtNotFound: "Not found",

	selFormat: "Format...",
	selParagraph: "Paragraph",
	selHeading1: "Heading 1",
	selHeading2: "Heading 2",
	selHeading3: "Heading 3",
	selHeading4: "Heading 4",
	selHeading5: "Heading 5",
	selHeading6: "Heading 6",

	selFont: "Font...",
	selArial: "Arial",
	selArialBlack: "Arial Black",
	selArialNarrow: "Arial Narrow",
	selComic: "Comic Sans MS",
	selCourier: "Courier New",
	selSystem: "System",
	selTahoma: "Tahoma",
	selTimes: "Times New Roman",
	selVerdana: "Verdana",
	selWingdings: "Wingdings",
	selWebdings: "webdings",

	selFontSize: "Size..."

};

/** @namespace */
SUI.editor.i18n.htmleditor.imageDialog = {
	cptEditImage: "Edit image",
	cptNewImage: "Insert image",
	txtSrc: "Image file",
	txtSelect: "Select",
	txtAlt: "Text equivalent (alt)",
	txtTitle: "Image title",
	txtLongdesc: "Link to additional information (longdesc)",
	txtClear: "Clear",
	txtLayout: "Layout",
	txtAlign: "Alignment",
	txtAlignOptions: ["none", "left", "right"],
	txtBorder: "Border width",
	txtDimensions: "Dimensions",
	txtWidth: "Width",
	txtHeight: "Height",
	txtMargins: "Margins",
	txtLeftRight: "Left, Right",
	txtTopBottom: "Top, Bottom"
};

/** @namespace */
SUI.editor.i18n.htmleditor.languageDialog = {
	cptEditLanguage: "Edit language",
	cptNewLanguage: "Insert language",
	txtLanguage: "Language"
};

/** @namespace */
SUI.editor.i18n.htmleditor.linkDialog = {
	cptEditLink: "Edit link",
	cptNewLink: "Insert link",
	tabExternalLink: "Internet link",
	tabInternalLink: "Internal link",
	tabFileLink: "Asset link",
	txtLink: "Link",
	txtElse: "else",
	txtTitle: "Title",
	txtNA: "not applicable",
	txtAnchor: "Anchor",
	txtNewWin: "New window",
	txtBrowse: "browse"
};

/** @namespace */
SUI.editor.i18n.htmleditor.pasteDialog = {
	cptPasteSpecial: "Paste special",
	pastePlainText: "Paste as plain text",
	pasteFilteredText: "Paste without formatting",
	pasteMarkupText: "Paste and keep all formatting"
};

/** @namespace */
SUI.editor.i18n.htmleditor.suggestionsDialog = {
	cptSpellCheck: "Spell check",
	txtSuggestions: "Suggestions"
};

/** @namespace */
SUI.editor.i18n.htmleditor.symbolDialog = {
	cptInsertSymbol: "Select Page"
};

/** @namespace */
SUI.editor.i18n.htmleditor.tableCellDialog = {

	cptEditTableCell: "Edit table cell",

	txtStandardStyles: "Cell styles",

	txtStyleDataCell: "Data cell",
	txtStyleRowCell: "Row header",
	txtStyleColCell: "Column header",
	txtStyleCellCross: "Cell cross",

	txtCellType: "Cell type",
	txtDataCell: "Data cell",
	txtHeaderCell: "Header cell",

	txtCellStyle: "Cell style",
	txtTopBorder: "Top border",
	txtLeftBorder: "Left border",
	txtRightBorder: "Right border",
	txtBottomBorder: "Bottom border",
	txtColor: "Background color",

	txtAxis: "Cell's axis",
	txtAxisNone: "none",
	txtAddAxis: "Add Axis",
	txtAbbr: "Abbreviation",
	txtForceTD: "Force TD for header cell",

	txtFreeHeaderCells: "Free header cells",
	txtHeaderCells: "Associated header cells",
	txtSelect: "Select",
	txtDeselect: "Deselect",
	txtRow: "row",
	txtColumn: "column"

};

/** @namespace */
SUI.editor.i18n.htmleditor.tableDialog = {

	cptEditTable: "Edit table",
	cptNewTable: "Insert table",

	txtStandardStyles: "Table styles",
	txtKeepStyles: "Keep current style",

	txtTableHeaders: "Table headers",
	txtHeadersNone: "No headers",
	txtHeadersRow: "Row headers",
	txtHeadersColumn: "Column headers",
	txtHeadersRowColumn: "Row and colum headers",
	txtHeadersKeep: "Keep current situation",

	txtTableSize: "Table size",
	txtColums: "Number of columns",
	txtRows: "Number of rows",

	txtStyles: "Table style",
	txtTopBorder: "Top border",
	txtLeftBorder: "Left border",
	txtRightBorder: "Right border",
	txtBottomBorder: "Bottom border",
	txtColor: "Background color",
	txtCelSpacing: "Cell spacing",
	txtWidth: "Table width",

	txtInformation: "Table information",
	txtCaption: "Caption",
	txtSummary: "Summary"
};

/** @namespace */
SUI.editor.i18n.htmleditor.tableRowColDialog = {

	cptInsDelRowCol: "Insert/delete a row or column",

	txtRow: "Row",
	txtColumn: "Column",

	txtInsertRowAbove: "Insert new row above selected cell",
	txtInsertRowBelow: "Insert new row below selected cell",
	txtDeleteRow: "Delete row",
	txtInsertColLeft: "Insert new column on the left of seleceted cell",
	txtInsertColRight: "Insert new column on the right of seleceted cell",
	txtDeleteCol: "Delete column"
};

/** @namespace */
SUI.editor.i18n.htmleditor.tableSplitMergeDialog = {

	cptInsDelSplitMerge: "Split cell or merge cells",

	txtSplit: "Split cell",
	txtMerge: "Merge cells",

	txtSplitVertical: "Split selected cell in vertical direction",
	txtSplitHorizontal: "Split selected cell in horizontal direction",

	txtMergeAbove: "Merge selected cell with the cell above",
	txtMergeBelow: "Merge selected cell with the cell below",
	txtMergeLeft: "Merge selected cell with the cell on the left",
	txtMergeRight: "Merge selected cell with the cell on the right"

};

/*=[SUI.editor.i18n.menutree]================================================*/

/** @namespace */
SUI.editor.i18n.menutree = {};

/** @namespace */
SUI.editor.i18n.menutree.menuTree = {
	editPage: "Edit page",
	newPage: "New page",
	pageUp: "Move up",
	pageDown: " Move down",
	pageProperties: "Properties",
	deletePage: "Delete",
	pageMove: "Move elsewhere",
	preview: "Preview",
	captionDeletePage: "Confirm page deletion",
	textDeletePage: "Are you sure you want to delete this page?"
};

/** @namespace */
SUI.editor.i18n.menutree.pagePropertyDialog = {
	cptEditPage: "Edit page properties",
	cptNewPage: "Create new page",
	title: "Title",
	template: "Template",
	pageType: "Page type",
	position: "Position",
	onlineOn: "Online on",
	offlineOn: "Offline on",
	invalidOnlineDate: "Invalid online date",
	invalidOfflineDate: "Invalid offline date",
	errCaptionCreatePage: "Can't create page",
	errTextCreatePage:
		"It's not possible to create a new page at this location."
};

/*=[SUI.editor.i18n.properties]==============================================*/

/** @namespace */
SUI.editor.i18n.properties = {};

/** @namespace */
SUI.editor.i18n.properties.imageAtlTitle = {
	title: "Image title",
	alt: "Alternative text"
};

/** @namespace */
SUI.editor.i18n.properties.image = {
	width: "Width",
	height: "Height"
};

/*=[SUI.editor.i18n.apps]====================================================*/

/** @namespace */
SUI.editor.i18n.apps = {};

/** @namespace */
SUI.editor.i18n.apps.list = {
	deleteItem: "Delete list item",
	copyItem: "Copy list item",
	editItem: "Edit list item",
	goUp: "Go to the parent list",
	goLinkedPage: "Edit linked page",
	goSubList: "Go to the sub-list"
};

/** @namespace */
SUI.editor.i18n.apps.list.blockList = {
	moveItemUp: "Move item one position up",
	moveItemDown: "Move item one position down",
	moveItem: "Move item to another position"
};

/** @namespace */
SUI.editor.i18n.apps.list.list = {
	searchItem: "Search for list items",
	nextPage: "Go to next page",
	prevPage: "Go to previous page",
	promptSearchCaption: "Zoek in de lijst",
	promptSearch: "Zoekterm",
	captionDeleteItem: "Confirm item deletion",
	textDeleteItem: "Are you sure you want to delete the selected item"
};

/** @namespace */
SUI.editor.i18n.apps.list.listItemDialog = {
	dlgCaptionNewItem: "Create new list item",
	dlgCaptionEditItem: "Edit list item"
};

/** @namespace */
SUI.editor.i18n.apps.list.listView = {
	newItem: "Create new list item",
	captionDeleteItems: "Confirm item deletion",
	textDeleteItems: "Are you sure you want to delete the selected item(s)"
};

/** @namespace */
SUI.editor.i18n.apps.list.positionDialog = {
	dlgCaption: "Move item",
	lblPosition: "Position"
};

/** @namespace */
SUI.editor.i18n.apps.form = {
	edtProperties: "Form properties",
	captionDeleteItem: "Confirm form element deletion",
	textDeleteItem:
		"Are you sure you want to delete the selected form element?",
	deleteItem: "Delete this form element",
	moveElementDown: "Move this form element one position down",
	moveElement: "Move this form element to another position",
	moveElementUp: "Move this form element one position up",
	copyElement: "Copy this form element",
	editElement: "Edit this form element",
	txtBelow: "Below",
	newElemAttachment: "New file upload field",
	newElemCheckBox: "New check box",
	newElemCheckGroup: "New check box group",
	newElemInfo: "New information text",
	newElemInput: "New text input field",
	newElemMail: "New mail address input field",
	newElemRadioGroup: "New radio button group",
	newElemSelectList: "New select list group",
	newElemTextArea: "New text input box",
	expExcel: "Export to Excel",
	expHtml: "Export to HTML",
	tabProperties: "Properties",
	tabExtraInfo: "Extra information",
	tabAdvanced: "Advanced",
	dlgLabel: "Label",
	dlgIdAttribute: "Id attribute",
	dlgNameAttribute: "Name attribute",
	dlgRows: "Rows",
	dlgWidth: "Width",
	dlgFullWidth: "Full width",
	dlgFixedWidth: "Size in characters ",
	dlgDefaultValue: "Default value",
	dlgEmail: "Use as reply to",
	dlgRequired: "Required",
	dlgMaxLength: "Character limit",
	dlgDefaultChecked: "Checked by default",
	dlgDefaultUnchecked: "Unchecked by default",
	dlgOptionList: "Answer option list",
	dlgPosition: "Postition",
	dlgEmailSubject: "E-mail subject",
	dlgMailTo: "Mail to",
	dlgSeperateAddresses: "Seperate multipe addresses with a comma (,)",
	dlgCaptcha: "Captcha",
	dlgCaptchaText: "Captcha text, add the link to refresh the captcha " +
		"between square<br>brackets and the link for the adio captcha " +
		"between curly brackets",
	btnNewOption: "New answer option",
	btnOptionDelete: "Delete answer option",
	btnOptionUp: "Move answer option one position up",
	btnOptionDown: "Move answer option one position down",
	cptInput: "text input field",
	cptTextArea: "text input box",
	cptEmail: "email input field",
	cptFile: "file input field",
	cptSelect: "select list",
	cptRadioGroup: "radio button group",
	cptCheckGroup: "check box group",
	cptCheckBox: "check box",
	cptInfoText: "information text",
	cptCopy: "Copy",
	cptEdit: "Edit",
	cptNew: "New",
	cptRepostion: "Repostion form element",
	cptFormProperties: "Edit form properties"
};

/*=[Resources]===============================================================*/

/** @namespace */
SUI.editor.resource = {
	getLoginKeyURL: SCRIVO_BASE_DIR + "/scrivo/secure/ajax_login.php",
	loginWithKeyURL: SCRIVO_BASE_DIR + SUI_DIR +"/login.php",
	ajaxURL: SCRIVO_BASE_DIR + "/scrivo/index.php",

	internalLink: SCRIVO_BASE_DIR + "/index.php?p=",
	assetLink: SCRIVO_BASE_DIR + "/scrivo/asset.php?id=",

	cssUnits: ["%","in","cm","mm","em","ex","pt","pc","px"],
	cssBorderStyles: ["none", "dotted", "dashed", "solid", "double", "groove",
		"ridge","inset","outset"],
	icnNavigationItem: "navigate.png",
	icnNavigationableDocument: "page_navigate.png",
	icnApplication: "tools.png",
	icnNotNavigationableDoc: "page.png",
	icnSubFolder: "folder.png",
	icnHelp: "help.png"
};

/** @namespace */
SUI.editor.resource.contenttabs = {
	icnSave: "save.png",
	icnScript: "script.png",
	icnCss: "css.png",
	icnAccess: "lock.png"
};

/** @namespace */
SUI.editor.resource.filedialog = {
	uploadURL: SCRIVO_BASE_DIR + 
		"/scrivo/ScrivoUi/Editor/Misc/filedialog_upload.php",
	cropperBackgroundImg: SCRIVO_BASE_DIR + 
		"/scrivo/ScrivoUi/Editor/Misc/cropper_backgroundimage.php",

	icnCrop: "crop.png",
	icnCut: "cut.png",
	icnDelete: "delete.png",
	icnUpload: "drive_upload.png",
	icnUploadOverwrite: "drive_upload2.png",
	icnNewFolder: "folder_new.png",
	icnFolderUp: "folder_up.png",
	icnFolder: "folder.png",
	icnAccess: "lock.png",
	icnPaste: "paste.png",
	icnProperties: "properties.png",
	icnReload: "reload.png",
	icnRename: "rename.png",

	icnMimeArchive: "mime_archive.png",
	icnMimeDefault: "mime_default.png",
	icnMimeExcel: "mime_excel.png",
	icnMimeImage: "mime_image.png",
	icnMimeMedia: "mime_media.png",
	icnMimePdf: "mime_pdf.png",
	icnMimePpt: "mime_ppt.png",
	icnMimeText: "mime_text.png",
	icnMimeWord: "mime_word.png",

	aniWaitUpload: "wait_upload.gif"
};

/** @namespace */
SUI.editor.resource.htmleditor = {
	icnSave: "save.png",
	icnCut: "cut.png",
	icnCopy: "copy.png",
	icnPaste: "paste.png",
	icnUndo: "undo.png",
	icnRedo: "redo.png",
	icnFind: "search.png",
	icnTable: "table.png",
	icnRowColumn: "tableinsdel.png",
	icnSplitMergeCell: "tablemerge.png",
	icnCellProperties: "tablecell.png",
	icnLink: "link.png",
	icnImage: "image.png",
	icnLanguage: "language.png",
	icnAbbreviation: "abbreviation.png",
	icnAnchor: "anchor.png",
	icnPageBreak: "pagebreak.png",
	icnSymbol: "symbol.png",
	icnHighlightColor: "highlightcolor.png",
	icnTextColor: "textcolor.png",
	icnRemoveFormatting: "eraser.png",
	icnSpellCheck: "spell.png",
	icnHTML: "tools.png",
	icnBold: "bold.png",
	icnItalic: "italic.png",
	icnUnderLine: "underline.png",
	icnAlignLeft: "alignleft.png",
	icnAlignRight: "alignright.png",
	icnAlignCenter: "aligncenter.png",
	icnOrderedList: "orderedlist.png",
	icnUnorderedList: "unorderedlist.png",
	icnIndent: "indent.png",
	icnDeIndent: "deindent.png",

	icnHeadersNone: "table_noheaders.png",
	icnHeadersRow: "table_rowheaders.png",
	icnHeadersColumn: "table_colheaders.png",
	icnHeadersRowColumn: "table_rowcolheaders.png",

	icnInsertRowAbove: "table_insert_row_above.png",
	icnInsertRowBelow: "table_insert_row_below.png",
	icnDeleteRow: "table_delete_row.png",
	icnInsertColLeft: "table_insert_col_left.png",
	icnInsertColRight: "table_insert_col_right.png",
	icnDeleteCol: "table_delete_column.png",

	icnSplitHorizontal: "tablecell_split_horizontal.png",
	icnSplitVertical: "tablecell_split_vertical.png",
	icnMergeAbove: "tablecell_merge_above.png",
	icnMergeBelow: "tablecell_merge_below.png",
	icnMergeLeft: "tablecell_merge_left.png",
	icnMergeRight: "tablecell_merge_right.png"
};

/** @namespace */
SUI.editor.resource.menutree = {
	icnNavigate: "navigate.png",
	icnPageNavigate: "page_navigate.png",
	icnPage: "page.png",
	icnTools: "tools.png",
	icnFolder: "folder.png",
	icnEditPage: "page_pencil.png",
	icnNewPage: "page.png",
	icnUp: "arrow_up_yellow.png",
	icnDown: "arrow_down_yellow.png",
	icnProperties: "properties.png",
	icnDelete: "delete.png",
	icnTreeMove: "treemove.png",
	icnPreview: "preview.png"
};

/** @namespace */
SUI.editor.resource.properties = {
	icnLink: "link.png",
	icnColor: "colors.png",
	icnImage: "image.png",
	icnRemove: "delete.png"
};

/** @namespace */
SUI.editor.resource.apps = {};

/** @namespace */
SUI.editor.resource.apps.list = {
	icnNewItem: "page.png",
	icnEditItem: "page_pencil.png",
	icnGoUp: "folder_up.png",
	icnGoLinkedPage: "page_link.png",
	icnGoSubList: "go_subfolder.png",
	icnCopyItem: "copy.png",
	icnDelete: "delete.png",
	icnNext: "arrow_right_blue.png",
	icnPrev: "arrow_left_blue.png",
	icnFind: "search.png",
	icnUp: "arrow_up_yellow.png",
	icnDown: "arrow_down_yellow.png",
	icnMove: "arrow_up_down_yellow.png"
};

/** @namespace */
SUI.editor.resource.apps.form = {
	icnElemInput: "form_input.png",
	icnElemTextArea: "form_textarea.png",
	icnElemRadioGroup: "form_radiogroup.png",
	icnElemSelectList: "form_selectlist.png",
	icnElemCheckBox: "form_checkbox.png",
	icnElemCheckGroup: "form_checkgroup.png",
	icnElemAttachment: "form_attachment.png",
	icnElemMail: "mail.png",
	icnElemInfo: "info.png",
	icnProperties: "properties.png",
	icnExportHtml: "mime_default.png",
	icnExportExcel: "mime_excel.png",
	icnEditItem: "page_pencil.png",
	icnCopyItem: "copy.png",
	icnDelete: "delete.png",
	icnUp: "arrow_up_yellow.png",
	icnDown: "arrow_down_yellow.png",
	icnMove: "arrow_up_down_yellow.png"
};
