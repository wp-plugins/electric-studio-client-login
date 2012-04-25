<?php
class Escl_user extends WP_User{

    /**
     * 
     * Method to add user to a group
     * @method addToGroup
     * @param ID $groupid
     * @return N/A
     * @since 0.8
     */
    function addToGroup($groupid){
        global $wpdb;
    	
        $user = get_userdata($this->get('ID'));
        
    	$groupinfo = Escl_groups::get_group_data_from_id($groupid);
    	
    	$sql = "INSERT INTO ".$wpdb->prefix."escl_user_group_rel (group_id, user_login)
    			VALUES ($groupid,'".$user->user_login."')";
    	
    	$sql = $wpdb->prepare($sql);
    	
    	if(!Escl_groups::user_in_group($userlogin,$groupinfo->group_slug)){ //check that user does not already exist in group
    		$wpdb->query($sql);
    	}
    }
    
    /**
     * 
     * To remove user from a group
     * @method RemoveFromGroup
     * @param ID $groupid
     * @return N/A
     * @since 0.8
     */
    
    function RemoveFromGroup($groupid){
        global $wpdb;

        $user = get_userdata($this->get('ID'));

    	$sql = "DELETE FROM ".$wpdb->prefix."escl_user_group_rel WHERE group_id = '$groupid' and user_login = '".$user->user_login."'";
    	
    	$sql = $wpdb->prepare($sql);
    	
    	$wpdb->query($sql);
    }
    
    /**
     * 
     * To get an array of objects each object being a group in which the user is a member of
     * @method listGroups
     * @since 0.8
     * @return array of objects
     */
    function listGroups(){
        global $wpdb;

        $user = get_userdata($this->get('ID'));

    	$sql = "SELECT gro.group_name, gro.group_slug, gro.group_status FROM ".$wpdb->prefix."escl_user_group_rel as rel 
    				JOIN ".$wpdb->prefix."escl_user_group as gro
    				ON gro.group_id = rel.group_id
    				WHERE rel.user_login = '".$user->user_login."'";
    	
    	$sql = $wpdb->prepare($sql);
    	
    	return $wpdb->get_results($sql);
    	
    }
    
}


class Escl_User_Query extends WP_User_Query{
    
}

/* User Functions */

function escl_get_users( $args = array() ) {
    $args = wp_parse_args( $args );
    $args['count_total'] = false;
    
    $user_search = new Escl_User_Query($args);
    
    return (array) $user_search->get_results();
}
