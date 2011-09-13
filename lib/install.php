<?php

function electric_studio_client_login_install() {
	global $plugindir;
	global $wpdb;
	
	/* Creates new database field */
	add_option("escl_verison", '0.1', '', 'yes');
	
	
	$sqlCreateGroupsTable = "CREATE TABLE ".$wpdb->prefix."escl_user_group (
	  group_id mediumint(9) NOT NULL AUTO_INCREMENT,
	  group_timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  group_name text NOT NULL,
	  group_slug text NOT NULL,
	  group_status text NOT NULL,
	  PRIMARY KEY  group_id (group_id));";
	
	$sqlCreateNewRelTable = "CREATE TABLE ".$wpdb->prefix."escl_user_group_rel (
	  group_rel_id mediumint(9) NOT NULL AUTO_INCREMENT,
	  group_id mediumint(9) NOT NULL,
	  user_login VARCHAR(60) NOT NULL,
	  PRIMARY KEY  group_rel_id (group_rel_id));";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	dbDelta($sqlCreateGroupsTable);
   	dbDelta($sqlCreateNewRelTable);
	
	add_role('client', 'Client', array(
	    'read' => true, // True allows that capability
	    'edit_posts' => false,
	    'delete_posts' => false, // Use false to explicitly deny
	));	
	
	
	$new_page_title = 'Client Area';
	$new_page_content = '';
	$new_page_template = 'page-clientarea.php'; //ex. template-custom.php. Leave blank if you don't want a custom page template.
	$new_page = array(
		'post_type' => 'page',
		'post_title' => $new_page_title,
		'post_content' => $new_page_content,
		'post_status' => 'publish',
		'post_author' => 1,
	);
	$new_page_id = wp_insert_post($new_page);
	if(!empty($new_page_template)){
		update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
	}
	
}

function electric_studio_client_login_remove() {
	global $templateArray;
	global $themeDir;
	
	//remove template files added by plugin
	foreach($templateArray as $tempFile){
		if(file_exists($themeDir.$tempFile)){
			unlink($themeDir.$tempFile);
		}
	}
	
	//@todo drop used tables
	
	/* Deletes the database field */
	remove_role('client');
	
	$mypage = get_page_by_title( 'Client Area' );
	  $mypageid = $mypage->ID;
	  wp_delete_post($mypageid, $force_delete);
	
	delete_option('escl_verison');
}

