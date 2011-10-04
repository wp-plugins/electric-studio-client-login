<?php

add_shortcode('escl_logged_in', 'escl_is_logged_in');
add_action('template_redirect','escl_redirect_to_login');

// [escl_logged_in]foo[/escl_logged_in]
function escl_is_logged_in($atts, $content=null){
	if ( escl_login_check($atts) )
		return do_shortcode($content);
	else
		return "";
	
}


function escl_login_check($atts){
    global $user_ID, $user_identity, $user_level;
     
    extract( shortcode_atts( array(
        'group' => array('escl_any'),
        'user' => 'escl_any',
        'userlevel' => 'escl_any'
    ), $atts));
    
    if(!is_array($group)){
        $group = explode("|",$group);
    }
    
    if ( is_user_logged_in() ) {
        //check the user's identity is ok
        if($user_level == 10){
            return true;
        }
        else if($user == 'escl_any' || $user == $user_identity){
            //check that the user level is high enough
            if($userlevel == 'escl_any' || $userlevel > $user_level){
                //check that user group is allowed
                if(in_array('escl_any',$group) || escl_user_in_group($user_identity,$group) == true){
                    return true;
                }
            }
        }
    }else{
        return false;
    }
}

function escl_redirect_to_login(){
	global $user_ID, $user_identity, $user_level, $wp_query;
	$post_id = $wp_query->post->ID;
	$groupsAllowed = get_post_meta($post_id, '_escl_groups');
	if($user_level < 10) //check that this is not the admin logged in
		if((!strlen($groupsAllowed[0])>0 && escl_user_in_group($user_identity,$groupsAllowed[0])==false))
            //if user is not allowed to view this page, redirect to login
			wp_redirect(wp_login_url(get_permalink($post_id)));
}



