tinyMCEPopup.requireLangPack();

var mpformsDialog = {
	init : function() {
	},

	insert : function() {
		var ed = tinyMCEPopup.editor;
		var formId = document.getElementById('mpforms_select').value;
		if (formId != null && formId != '') {
			ed.execCommand('mceInsertContent', false, '[mailplusform formid=' + formId + ' /]');
			tinyMCEPopup.close();
		}
	}
}

tinyMCEPopup.onInit.add(mpformsDialog.init, mpformsDialog);
