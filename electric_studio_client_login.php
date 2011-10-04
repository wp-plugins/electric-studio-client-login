<?php
/*
Plugin Name: Electric Studio Client Login
Plugin URI: http://www.electricstudio.co.uk
Description: Give clients a login area and allow administrators to control what content is viewable by which users
Version: 0.7
Author: James Irving-Swift
Author URI: http://www.irving-swift.com
License: GPL2
*/

//directory of the plugin (this directory)
$plugindir = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

include 'lib/escl_config.php';

$missingFiles = false; //to stop a php warning for line 27 if there are no missing files

//check for missing files (obviously, the first time this plugin is run, all the files will be missing.)
foreach($templateArray as $tempFile){
	if(!file_exists($themeDir.$tempFile)){
		$missingFiles = true;
	}
}

//copy all template files to accomodate for the missing files
if($missingFiles){
	foreach($templateArray as $templateFile){
		$data = file_get_contents($pluginTemplateDir.$templateFile);
		$handle = fopen($themeDir.$templateFile, "w");
		//echo $themeDir.$templateFile;
		fwrite($handle, $data);
		fclose($handle);
	}
}

include 'lib/install.php';
include 'lib/options.php';
include 'lib/functions.php';
include 'lib/groups.php';
include 'lib/user.php';
include 'lib/ajax.php';
include 'lib/metabox.php';


/* Runs when plugin is activated */
register_activation_hook(__FILE__,'electric_studio_client_login_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'electric_studio_client_login_remove' );

function escl_init(){
	if(is_admin()){
		wp_register_style( 'escl-livesearch', get_bloginfo('wpurl').'/wp-content/plugins/electric-studio-client-login/css/escl_livesearch.css');
		wp_enqueue_style('escl-livesearch');
		wp_register_script('escl-livesearch-js',get_bloginfo('wpurl').'/wp-content/plugins/electric-studio-client-login/js/escl_livesearch.js',array('jquery'));
		wp_enqueue_script('escl-livesearch-js');
	}
	
}

add_action('init','escl_init');


//register plugin's template directory
//register_theme_directory("plugins/electric_studio_client_login/templates/");

//array_push($wp_theme_directories, "/home/clients/public_html/development/wpcms/wp-content/plugins/electric-studio-client-login/templates/");


function escl_widget_init(){
	wp_register_sidebar_widget(
		'escl_login_form',
		__('Login'),
		'escl_widget_login',
		array(
			'description' => 'Places a login form in the sidebar'
		)
	);
}

add_shortcode('escl-login', 'escl_custom_login');

add_action("plugins_loaded", "escl_widget_init");

function electric_studio_client_login()
{
  echo get_option('OPTION_NAME');
}



