<?php 

function escl_add_meta_box(){
	$postTypes = get_post_types();
	foreach($postTypes as $pt){
		add_meta_box(
			'escl_users',
			'User Restrictions',
			'escl_meta_box_html',
			$pt,
			'normal',
			'high');
	}
	unset($pt);
}

function escl_meta_box_html(){
	$escl_groups = escl_list_groups();
	$selectedGroups = get_post_meta($_GET['post'],'_escl_groups');
	$selectedGroups = $selectedGroups[0];
		
	$html = "";
	
	$html .= "<table style=\"width: 100%; align: center\">";
	$html .= "<tr>";
	
	$i = 0;
	foreach($escl_groups as $g){
		$html .= "<td style=\"width: 19%\">";
		$html .= "<label for=\"escl_groups[$i]\">$g->group_name</label><input type=\"checkbox\" name=\"escl_groups[$i]\" value=\"$g->group_slug\"";
		if(is_array($selectedGroups))
			if(in_array($g->group_slug,$selectedGroups))
				$html .= " checked=\"checked\"";
		$html .= ">";
		$html .= "</td>";
		$i++;
		if($i % 5 == 0) //check if this row is divisable by 5 (i.e. last column in row)
			$html .= "</tr><tr>";
	}
	
	while($i % 5 != 0){ //loop to create extra cells at the end of the table so the cells end up as a multiple of 5
		$i++;
		$html .= "<td style=\"width: 19%\">&nbsp;</td>";
	}
	
	$html .= "</tr>";
	$html .= "</table>";

	echo $html;
}

function escl_save_group_restr($post_id){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    	return;
    
    $groups = $_POST['escl_groups'];	

    if(!update_post_meta($post_id, '_escl_groups', $groups))
    	add_post_meta($post_id, '_escl_groups' , $groups, true);
    
    return $groups; 
}

add_action( 'add_meta_boxes', 'escl_add_meta_box' );
add_action( 'save_post', 'escl_save_group_restr' );