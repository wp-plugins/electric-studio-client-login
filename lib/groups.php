<?php

function escl_list_groups($searchTerm = ""){
	global $wpdb;
	
	$sql = "SELECT group_name, group_slug, group_status FROM ".$wpdb->prefix."escl_user_group";
	
	if(strlen($searchTerm)>0){
		$sql .= " WHERE group_name like '%".$searchTerm."%'";
	}
	
	$sql .= " LIMIT 0,20";
	
	$sql = $wpdb->prepare($sql);
	
	return $wpdb->get_results($sql);
}

//lists all the users in a group
function escl_list_users_in_group($groupslug){
	global $wpdb;
	
	$groupinfo = escl_get_group_data($groupslug);
	
	$sql = "SELECT users.ID, users.user_login, users.user_nicename FROM $wpdb->users as users
	INNER JOIN ".$wpdb->prefix."escl_user_group_rel as rel ON users.user_login = rel.user_login WHERE rel.group_id = $groupinfo->group_id";
	
	$sql = $wpdb->prepare($sql);
	
	$userlist = $wpdb->get_results($sql);
	
	return $userlist;
}

//lists all groups user is a member of
function escl_list_users_group($userid){
    global $wpdb;
    
    $sql = "SELECT group_name FROM $wpdb->users as users
    INNER JOIN ".$wpdb->prefix."escl_user_group_rel as rel ON users.user_login = rel.user_login
    INNER JOIN ".$wpdb->prefix."escl_user_group as grp ON rel.group_id = grp.group_id
        WHERE users.ID = $userid";
    
    $sql = $wpdb->prepare($sql);
    
    $userlist = $wpdb->get_results($sql);
    
    return $userlist;
}

function escl_add_group($groupName, $groupStatus, $groupSlug = ""){
	global $wpdb;
	
	if($groupSlug == "")
		$groupSlug = $groupName;
	
	$groupSlug = strtolower($groupSlug);
	$groupSlug = preg_replace('/ /','-',$groupSlug);
	$groupSlug = preg_replace('/[\/\\\\]/','',$groupSlug);
	
	$results = 1;
	$i;
	while($results>0){ //this while loop is for checking if the slug already exists, and changes the slug until it doesn't already exist.
		$sql = "SELECT group_id FROM ".$wpdb->prefix."escl_user_group WHERE group_slug = '$groupSlug'";
		$sql = $wpdb->prepare($sql);

		$results = count($wpdb->get_results($sql));
		$i++;
		
		if($results>0){
			if($i>1)
				$groupSlug = substr($groupSlug,0,strrpos($groupSlug,'-'));	
			$groupSlug = $groupSlug.'-'.$i;
		}
		
	}
	
	$rows_affected = $wpdb->insert($wpdb->prefix."escl_user_group", array(
			'group_timestamp' => current_time('mysql'),
			'group_name' => $groupName,
			'group_slug' => $groupSlug,
			'group_status' => $groupStatus,
		)
	);

}

function escl_get_group_data($groupSlug){
	global $wpdb;
	
	$sql = "Select group_id, group_timestamp, group_name, group_status FROM ".$wpdb->prefix."escl_user_group WHERE group_slug = '$groupSlug'";
	
	$sql = $wpdb->prepare($sql);
	
	$result = $wpdb->get_results($sql);
	
	return $result[0];
}

function escl_get_group_data_from_id($groupid){
	global $wpdb;
	
	$sql = "Select group_slug, group_timestamp, group_name, group_status FROM ".$wpdb->prefix."escl_user_group WHERE group_id = '$groupid'";
	
	$sql = $wpdb->prepare($sql);
	
	$result = $wpdb->get_results($sql);
	
	return $result[0];
}

function escl_add_user_to_group($groupid,$userlogin){
	global $wpdb;
	
	$groupinfo = escl_get_group_data_from_id($groupid);
	
	$sql = "INSERT INTO ".$wpdb->prefix."escl_user_group_rel (group_id, user_login)
			VALUES ($groupid,'$userlogin')";
	
	$sql = $wpdb->prepare($sql);
	
	if(!escl_user_in_group($userlogin,$groupinfo->group_slug)){ //check that user does not already exist in group
		$wpdb->query($sql);
	}
}

function escl_remove_user_from_group($groupid,$userlogin){
	global $wpdb;
	
	$sql = "DELETE FROM ".$wpdb->prefix."escl_user_group_rel WHERE group_id = '$groupid' and user_login = '$userlogin'";
	
	$sql = $wpdb->prepare($sql);
	
	$wpdb->query($sql);
}

function escl_user_in_group($user, $groups){
	global $wpdb;
	
	if(!is_array($groups)){
		$groups = array($groups);
	}
	
	foreach($groups as $group){
		$groupinfo = escl_get_group_data($group);
		
		$sql = "SELECT group_rel_id FROM ".$wpdb->prefix."escl_user_group_rel WHERE group_id='$groupinfo->group_id' AND user_login='$user'";
		$sql = $wpdb->prepare($sql);
		$resultCount = $wpdb->get_results($sql);
		
		if(count($resultCount)>0){
			return true;
		}
	}
	return false;
}


function escl_remove_group($groupSlug){
	global $wpdb;
	
	$groupinfo = escl_get_group_data($groupSlug);
	
	//first we need to delete all the relationships involving that group
	$sqlRmRels = "DELETE FROM ".$wpdb->prefix."escl_user_group_rel WHERE group_id='$groupinfo->group_id'";
	$sqlRmRels = $wpdb->prepare($sqlRmRels);
	
	//now we delete the group
	$sqlRmGroup = "DELETE FROM ".$wpdb->prefix."escl_user_group WHERE group_id='$groupinfo->group_id'";
	$sqlRmGroup = $wpdb->prepare($sqlRmGroup);
	
	//perform both queries
	$wpdb->query($sqlRmRels);	
	$wpdb->query($sqlRmGroup);
}