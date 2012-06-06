<?php
/*
Plugin Name: Electric Studio Client Login
Plugin URI: http://www.electricstudio.co.uk
Description: Give clients a login area and allow administrators to control what content is viewable by which users and groups.
Version: 0.8.1
Author: James Irving-Swift
Author URI: http://www.irving-swift.com
License: GPL2
*/

//directory of the plugin (this directory)
$plugindir = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

include 'lib/escl_config.php';

function create_template_files(){
    include 'lib/escl_config.php';

    $missingFiles = false; //to stop a php warning for line 27 if there are no missing files

    //check for missing files (obviously, the first time this plugin is run, all the files will be missing.)
    foreach($templateArray as $tempFile){
    	if(!file_exists($themeDir.$tempFile)){
    		$missingFiles = true;
    	}
    }


    //copy all template files to accommodate for the missing files
    if($missingFiles){
    	foreach($templateArray as $templateFile){
    	    copy(dirname(__FILE__)."/".$pluginTemplateDir.$templateFile, $themeDir.$templateFile) or die ("Unable to copy Client Area template into theme. Please Check you have sufficent Privilages");
    	}
    }
}

include_once 'lib/install.php';
include_once 'lib/options.php';
include_once 'lib/functions.php';
include_once 'lib/groups.php';
include_once 'lib/user.php';
include_once 'lib/ajax.php';
include_once 'lib/metabox.php';

/* Runs when plugin is activated */
register_activation_hook(ABSPATH.PLUGINDIR.'/electric-studio-client-login/electric_studio_client_login.php','escl_activate');

/* Runs on plugin deactivation*/
register_deactivation_hook(ABSPATH.PLUGINDIR.'/electric-studio-client-login/electric_studio_client_login.php', 'escl_deactivate' );

function escl_activate(){
    electric_studio_client_login_install();
    create_template_files();
}

function escl_deactivate(){
    electric_studio_client_login_remove();
}

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


function electric_studio_client_login()
{
  echo get_option('OPTION_NAME');
}




