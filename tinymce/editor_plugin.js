tinymce.PluginManager.add('mpforms', function(editor, url) {
	if (!editor) {
		return;
	}

    editor.addCommand('mpforms', function() {
        showDialog();
    });

    editor.addButton('mpforms', {
        title : 'Add MailPlus form',
        cmd : 'mpforms',
        image : url + '/img/mpforms.png'
    });

    function showDialog() {
        var win = editor.windowManager.open({
			title: 'Add a MailPlus form',
			body:[{
				type: 'form',
                //width: '400px',
				layout : 'flex',
                direction : 'column',

                name: 'mpform_form',
                defaults : {
                    type : 'formItem',
                    autoResize : "overflow",
                    flex : 1,
                    minWidth: 350
                },
				items: [
					{
						name: 'mpforms_select',
						label: 'Choose a MailPlus form to add',
						type: 'listbox',
						width: 250,
						values : []
					}
				]
			}],
            onSubmit : onSubmitForm
		});

        var listbox = win.find('#mpforms_select')[0];
        jQuery.getJSON('/?mpforms_get_forms=1', function(data) {
            jQuery.each(data, function(index, item){
            	listbox.settings.values.push({text:  item.name, value: item.id});
			});

		});

        function onSubmitForm() {
            var data = win.toJSON();

        	var displayValue = '[mailplusform formid=' + data.mpforms_select + ' /]';
            editor.selection.setContent(displayValue);
        }
    }

})();

