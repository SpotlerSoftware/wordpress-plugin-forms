<?php
/*
Plugin Name: MailPlus Forms
Plugin URI: https://www.spotler.com
Description: With the <strong>MailPlus Forms Plugin</strong> web masters can easily integrate web forms or online surveys created in <a href="https://spotler.com/software/" target="_blank">MailPlus</a> on pages and posts without any technical knowledge. MailPlus is an online marketing platform which contains a user-friendly form editor with a lot of features. For example, matrix questions, conditional questions, skip logic/branching, multi-paging, extensive features for validating answers from respondents, great e-mail confirmation possibilities and much more. <strong>To get started:</strong> 1) Click the “Activate” link to the left of this description, 2) Go to your <a href="http://login.mailplus.nl" target="_blank">MailPlus</a> account to get your authorization codes, 3) Go to your <a href='options-general.php?page=mailplusforms'>plugin settings</a> and enter your API key and secret.
Version: 1.1.0
Author: Spotler Software
Author URI: https://www.spotler.com
License: Modified BSD license
*/

/*  

Copyright (c) Spotler (email : info@spotler.com)
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

require_once('mailplus_integration.php');

define ('MPPLUGINNAME', 'MailPlus Forms');
define ('MPPLUGINID', 'mailplusforms');

add_action('admin_menu', 'mpforms_plugin_menu');
add_action('init', 'mpforms_addbuttons');
add_action('admin_init', 'mpforms_admin_init');
add_action('send_headers', 'mpforms_handle_headers');
add_action('template_redirect', 'mpforms_get_forms');

add_shortcode('mailplusform', 'mpforms_shortcode');

add_filter( 'query_vars', 'add_query_vars_filter' );

function add_query_vars_filter( $vars ){
    $vars[] = "mpforms_get_forms";
    return $vars;
}


function mpforms_get_forms()
{
    if (get_query_var('mpforms_get_forms') != 1) {
        return;
    }

    if (!is_user_logged_in()){
        die('You must be logged in to access this script.');
    }
    header('Content-type: application/json');
    $api = new mailplus_forms_api();
    $forms = $api->get_forms();

    $result = array();

    foreach ($forms as $form) {
        $row = array('id' => (int) $form->id, 'name' => (string) $form->name);
        $result[] = $row;
    }

    echo json_encode($result);
    exit;
}

function mpforms_plugin_menu() {
	add_options_page(MPPLUGINNAME, MPPLUGINNAME, 'manage_options', MPPLUGINID, 'mpforms_options_page');
}

function  mpforms_options_page() {
?>
	<div class="wrap">
	<div id="icon-options-mpforms" class="icon32" style="background: url('<?php echo plugins_url('mailplus-forms/settings.png'); ?>') no-repeat scroll 0 0 transparent;"><br></div>
	<h2>MailPlus Forms Plugin Settings</h2><br>
	<form action="options.php" method="post">
		<?php settings_fields('mpforms_plugin_options'); ?>
		<?php do_settings_sections('mpforms_plugin_sections'); ?>
		<br><br>
		<input name="Submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />	
		<br><br>
		                <div>
<strong>About MailPlus</strong><br>
<a href="http://www.mailplus.nl"  target="_blank">MailPlus</a> is an online marketing platform which contains a user-friendly form editor with a lot of features. For example, matrix questions, conditional questions, skip logic/branching, multi-paging, extensive features for validating answers from respondents, great e-mail confirmation possibilities and much more.

                </div>

	</form>
	</div>

<?php
}

/* ADMIN SETTINGS */
function mpforms_admin_init() {
	register_setting( 'mpforms_plugin_options', 'mpforms_plugin_options', 'mp_forms_plugin_options_validate' );
	add_settings_section('plugin_main', null, 'mpforms_plugin_section_text', 'mpforms_plugin_sections');
	add_settings_section('plugin_format', null, 'mpforms_plugin_format_section_text', 'mpforms_plugin_sections');

	add_settings_field('mpforms_api_url', 'MailPlus API URL', 'mpforms_apiurl_setting_string', 'mpforms_plugin_sections', 'plugin_main');
	add_settings_field('mpforms_consumer_key', 'Consumer key', 'mpforms_key_settings_string', 'mpforms_plugin_sections', 'plugin_main');
	add_settings_field('mpforms_consumer_secret', 'Consumer secret', 'mpforms_secret_settings_string', 'mpforms_plugin_sections', 'plugin_main');
	add_settings_field('mpforms_htmlxhtml', 'Publish forms in html or xhtml?', 'mpforms_htmlxhtml_settings', 'mpforms_plugin_sections', 'plugin_format');
	add_settings_field('mpforms_tablesdivs', 'Publish forms in tables or divs?', 'mpforms_tablesdivs_settings', 'mpforms_plugin_sections', 'plugin_format');
}

function mpforms_plugin_section_text() {
?>

With the MailPlus Forms Plugin web masters can easily integrate web forms or online surveys created in MailPlus on pages and posts without any technical knowledge.<br><br>
<strong>Authorization</strong><br>
To get started, go to your <a href='http://login.mailplus.nl' target='_blank'>MailPlus account</a> to get your authorization codes and enter these below:
<?php

}

function mpforms_plugin_format_section_text() {
?>
<br><br>
<strong>Form Integration Preferences</strong><br>
Choose wether you want to integrate your forms in ‘xhtml or html’ and choose wether you want them to consist of ‘tables or divs’. Enter your personal preferences below:



<?php

}


function mpforms_apiurl_setting_string() {
	$options = get_option('mpforms_plugin_options');

	if (isset($options['mpforms_api_url'])) {
		 $url = $options['mpforms_api_url'];
	} else {
		$url = 'https://restapi.mailplus.nl/';
	}

	echo "<input id='mpforms_api_url' name='mpforms_plugin_options[mpforms_api_url]' size='50' type='text' value='$url' />";
} 

function mpforms_key_settings_string() {
	$options = get_option('mpforms_plugin_options');
	if (!isset($options['mpforms_consumer_key'])) { $options['mpforms_consumer_key'] = ''; };
	echo "<input id='mpforms_consumer_key' name='mpforms_plugin_options[mpforms_consumer_key]' size='32' type='text' value='{$options['mpforms_consumer_key']}'";
}

function mpforms_secret_settings_string() {
        $options = get_option('mpforms_plugin_options');
	if (!isset($options['mpforms_consumer_secret'])) { $options['mpforms_consumer_secret'] = ''; };

        echo "<input id='mpforms_consumer_secret' name='mpforms_plugin_options[mpforms_consumer_secret]' size='32' type='text' value='{$options['mpforms_consumer_secret']}'";
}

function mpforms_htmlxhtml_settings() {
        $options = get_option('mpforms_plugin_options');
        
	if (isset($options['mpforms_htmlxhtml'])) {
		$val = $options['mpforms_htmlxhtml'];
	} else {
		$val = 'xhtml';
	}
?>
        <input name='mpforms_plugin_options[mpforms_htmlxhtml]' type='radio' id='mpforms_xhtml' value='xhtml' <?php if ($val === 'xhtml') echo "checked='checked'"; ?> ><label for='mpforms_xhtml'>xhtml</label><br />
	<input name='mpforms_plugin_options[mpforms_htmlxhtml]' type='radio' id='mpforms_html' value='html' <?php if ($val === 'html') echo "checked='checked'"; ?> ><label for='mpforms_html'>html</label>
        
<?php
}

function mpforms_tablesdivs_settings() {
	$options = get_option('mpforms_plugin_options');

	if (isset($options['mpforms_tablesdivs'])) {
        	$val = $options['mpforms_tablesdivs'];
	} else {
		$val = 'tables';
        }       
?>
        <input name='mpforms_plugin_options[mpforms_tablesdivs]' type='radio' id='mpforms_tables' value='tables' <?php if ($val == 'tables') echo "checked='checked'"; ?> ><label for='mpforms_html'>tables</label><br>
        <input name='mpforms_plugin_options[mpforms_tablesdivs]' type='radio' id='mpforms_divs' value='divs' <?php if ($val == 'divs') echo "checked='checked'"; ?> ><label for='mpforms_xhtml'>divs</label>
        
<?php

}


function mp_forms_plugin_options_validate($input) {
	$options = get_option('mpforms_plugin_options');
	$options['mpforms_api_url'] = trim($input['mpforms_api_url']);
	$options['mpforms_consumer_key'] = trim($input['mpforms_consumer_key']);
	$options['mpforms_consumer_secret'] = trim($input['mpforms_consumer_secret']);
	$options['mpforms_htmlxhtml'] = $input['mpforms_htmlxhtml'];
	$options['mpforms_tablesdivs'] = $input['mpforms_tablesdivs'];	

	return $options;
}


/* TINYMCE OPTIONS */
function mpforms_addbuttons() {
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;

	// Add only in Rich Editor mode
	//if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "mpforms_add_tinymce_plugin");
		add_filter('mce_buttons', 'mpforms_register_button');
	//}
}

function mpforms_register_button($buttons) {
	array_push($buttons, "separator", "mpforms");
	return $buttons;
}

function mpforms_add_tinymce_plugin($plugins) {
	$plugins['mpforms'] = plugins_url('/mailplus-forms/tinymce/editor_plugin.js');
	return $plugins;
}

function mp_forms_repair_post($data) {
	// combine rawpost and $_POST ($data) to rebuild broken arrays in $_POST
        $rawpost = "&".file_get_contents("php://input");
        while(list($key,$value)= each($data)) {
            $pos = preg_match_all("/&".$key."=([^&]*)/i",$rawpost, $regs, PREG_PATTERN_ORDER);       
            if((!is_array($value)) && ($pos > 1)) {
                $qform[$key] = array();
                for($i = 0; $i < $pos; $i++) {
                    $qform[$key][$i] = urldecode($regs[1][$i]);
                }
            } else {
                $qform[$key] = $value;
            }
        }
        return $qform; 
}

function mpforms_get_post_url($formid) {
    $siteUrl = get_option('siteurl');
    $siteUrlParts = parse_url($siteUrl);
    $posturl = $siteUrlParts['scheme'] . '://' . $siteUrlParts['host'];

    if (isset($siteUrlParts['port'])) {
        $posturl .= ':' . $siteUrlParts['port'];
    }

    $posturl .= $_SERVER['REQUEST_URI'];

    if (strpos($posturl, 'formid=' . $formid) === false) {
        if (strpos($posturl, '?') === false) {
            $posturl .= '?formid=' . $formid;
        } else {
            $posturl .= '&formid=' . $formid;
        }
    }
    return $posturl;
}
function mpforms_handle_headers()
{
	global $mpforms_data;
	if (isset($_POST['formEncId']) && isset($_GET['formid'])) {
		$formid = $_GET['formid'];
		$api = new mailplus_forms_api();
		$posturl = mpforms_get_post_url($formid);

		$result = '';
		try {
			$res = $api->post_form($formid, $posturl, mp_forms_repair_post($_POST));
			unset($_POST['formEncId']);
			if ($res->url && $res->url != '') {
				wp_redirect($res->url);
				exit;
			} else {
				wp_enqueue_script('mpforms-' . $formid, $res->script, array('jquery', 'jquery-validate', 'jquery-ui-datepicker'));
				$result = $res->html;
			}
		}
		catch (Exception $e) {
			$result .= "<pre> Error: " . $e->getMessage() . "</pre>";
		}

		$mpforms_data[$formid] = $result;
	}

}

/* SHORTCODE OPTIONS */
function mpforms_shortcode($atts) {
	global $mpforms_data;
	extract( shortcode_atts( array(
		'formid' => '',
		'ssl' => 'false',
	), $atts ) );

	$api = new mailplus_forms_api();

	$posturl = mpforms_get_post_url($formid);

	$result = '';
	try {
		nocache_headers();
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-validate', '//static.mailplus.nl/jq/jquery.validate.min.js', array('jquery'));
		wp_enqueue_script('jquery-ui-datepicker');
	
		if (isset($mpforms_data[$formid])) {
			$result = $mpforms_data[$formid];
		} else {
			$encId = null;
			if (isset($_GET['encId'])) {
				$encId = $_GET['encId'];
			}
			$res = $api->get_form($formid, $posturl, $encId);
			$result .= $res->html;
			wp_enqueue_script('mpforms-' . $formid, $res->script, array('jquery', 'jquery-validate', 'jquery-ui-datepicker'));
		}


	} catch (Exception $e) {
		$result .= "<pre> Error: " . $e->getMessage() . "</pre>";
	}
	

	return $result;

}
