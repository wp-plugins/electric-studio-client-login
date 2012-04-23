<?php

class Escl_user{
    
    function __construct(){
        add_shortcode('escl_logged_in', array(&$this,'is_logged_in'));
        add_action('template_redirect',array(&$this,'redirect_to_login'));
    }
    
    // [escl_logged_in]foo[/escl_logged_in]
    function is_logged_in($atts, $content=null){
    	if ( escl_login_check($atts) )
    		return do_shortcode($content);
    	else
    		return "";
    	
    }
    
    
    function login_check($atts){
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
    
    function redirect_to_login(){
    	global $user_ID, $user_identity, $user_level, $wp_query;
    	$post_id = $wp_query->post->ID;
    	$groupsAllowed = get_post_meta($post_id, '_escl_groups');
    	if($user_level < 10) //check that this is not the admin logged in
    		if((is_array($groupsAllowed[0]) && escl_user_in_group($user_identity,$groupsAllowed[0])==false))
                //if user is not allowed to view this page, redirect to login
    			wp_redirect(wp_login_url(get_permalink($post_id)));
    }

}

$esclUser = new Escl_user();

/* Start Compatability */
/* these are functions which wouldn't work anymore due to the 
 * functions now being methods within a class,
 * so these functions allow them to work as before
 * 
 * N.B. Do not use these functions as we plan on deprecating them
 */

function escl_is_logged_in($atts,$content=null){
    global $esclUser;
    return $esclUser->escl_is_logged_in($atts,$content);
}

function escl_login_check($atts){
    global $esclUser;
    return $esclUser->login_check($atts);
}

function escl_redirect_to_login(){
    global $esclUser;
    $esclUser->redirect_to_login();
}

/* End Compatability */