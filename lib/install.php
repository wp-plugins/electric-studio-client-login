<?php
function electric_studio_client_login_install() {
	global $wpdb;

	//On the original version there was a typo on the word version,
	//this corrects that.
	if(get_option('escl_verison',0) > 0){
	    $oldVersion = get_option('escl_verison',0);
	    delete_option('escl_verison');
	}else{
	    $oldVersion = get_option('escl_version',0);
	}

	//syslog(LOG_WARNING, date("H:m:s")." ".$oldVersion);

	/* Creates new database field */
	add_option("escl_general_settings", array());
    add_option("escl_fields", array());

    //check if there is a previously installed version

    	$sqlCreateGroupsTable = "CREATE TABLE ".$wpdb->prefix."escl_user_group (
    	  group_id mediumint(9) NOT NULL AUTO_INCREMENT,
    	  group_timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    	  group_name text NOT NULL,
    	  group_slug text NOT NULL,
    	  group_status text NOT NULL,
    	  group_lock text NOT NULL,
    	  UNIQUE KEY  group_id (group_id));";

    	$sqlCreateNewRelTable = "CREATE TABLE ".$wpdb->prefix."escl_user_group_rel (
    	  group_rel_id mediumint(9) NOT NULL AUTO_INCREMENT,
    	  group_id mediumint(9) NOT NULL,
    	  user_login VARCHAR(60) NOT NULL,
    	  UNIQUE KEY  group_rel_id (group_rel_id));";

    	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
       	dbDelta($sqlCreateGroupsTable);
       	dbDelta($sqlCreateNewRelTable);

    //if no prior installed versions
    if($oldVersion< "0.5"){

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

    //if prior version is pre 0.8.1
    if($oldVersion < "0.8.1"){

        //create the default group
    	$g = new Escl_groups();
    	$g->add_group('Default', 'active','default',true);
        unset($g);

    }

	//set the version nubmer as an option
	add_option("escl_version", '0.8.1', '', 'yes');

}

function electric_studio_client_login_remove() {
    global $wpdb;
	global $templateArray;
	global $themeDir;

	//remove template files added by plugin
	foreach($templateArray as $tempFile){
		if(file_exists($themeDir.$tempFile)){
			unlink($themeDir.$tempFile);
		}
	}

	//drop added tables
    $table1 = $wpdb->prefix."escl_user_group";
    $table2 = $wpdb->prefix."escl_user_group_rel";
	$wpdb->query("DROP TABLE IF EXISTS $table1");
	$wpdb->query("DROP TABLE IF EXISTS $table2");

	/* Deletes the database field */
	remove_role('client');

	$mypage = get_page_by_title( 'Client Area' );
	  $mypageid = $mypage->ID;
	  wp_delete_post($mypageid);

	delete_option('escl_verison');

    delete_option("escl_fields");
}

