<?php

require_once('../../../../wp-load.php');
require_once('../../../../wp-admin/includes/admin.php'); 
do_action('admin_init');

if (!is_user_logged_in()){
        die('You must be logged in to access this script.');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#mpforms_dlg.title}</title>
	<script type="text/javascript" src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
</head>
<body style="height: 100%">
	<form>
	{#mpforms_dlg.selectform}:<br><br>
	<select id='mpforms_select' >
		<option value=""></option>
		<?php
			$api = new mailplus_forms_api();
			$forms = $api->get_forms();

			foreach ($forms as $form) {
				echo "<option value='" . $form->id . "'>" . $form->name . "</option>";
			}			
		?>

	</select>


	<div class="mceActionPanel" style="padding-top: 16px">
			<input type="button" id="insert" name="insert" value="{#mpforms_dlg.insert}" onclick="mpformsDialog.insert();" />
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
	</div>

	</form>
</body>
</html>
