<?php

require_once('../../../../wp-load.php');
require_once('../../../../wp-admin/includes/admin.php');
do_action('admin_init');

if (!is_user_logged_in()){
	die('You must be logged in to access this script.');
}

header('Content-Type: text/javascript')

?>
(function() {

	tinymce.PluginManager.requireLangPack('mpforms');

	tinymce.create('tinymce.plugins.mpforms', {

		init: function(ed, url) {
			ed.addCommand('mpforms', function() {
				ed.windowManager.open({
					file: url + '/dialog.php',
					inline: 1,
					height: 120,
					width: 400
				}, {
					plugin_url: url
				});
		
			});
			

			ed.addButton('mpforms', {
				title : 'mpforms.desc',
				cmd : 'mpforms',
				image : url + '/img/mpforms.png'
			});
		},

		getInfo : function() {
	                return {
				longname : 'MailPlus Forms plugin',
				author : 'Paul Bosselaar / MailPlus',
				authorurl : 'http://www.mailplus.nl',
				infourl : 'http://www.mailplus.nl',
				version : "0.1"
			};
		}
	});
	
	tinymce.PluginManager.add('mpforms', tinymce.plugins.mpforms);	

})();

