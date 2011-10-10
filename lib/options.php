<?php

add_action('admin_menu', 'create_escl_options_page');
add_action('admin_init', 'register_and_build_escl_options');
add_action('show_user_profile', 'extra_user_profile_fields' );
add_action('edit_user_profile', 'extra_user_profile_fields' );
add_action('personal_options_update', 'save_extra_user_profile_fields' );
add_action('edit_user_profile_update', 'save_extra_user_profile_fields' );

function create_escl_options_page() {
  add_menu_page('Clients Area', 'Clients Area', 'administrator', 'escl_options');
  add_submenu_page('escl_options', 'Clients Area', 'Clients Area', 'administrator', 'escl_options', 'escl_home');
  add_submenu_page('escl_options', 'Group Management', 'Group Management', 'administrator', 'escl_group_options', 'escl_group_options_page');
  add_submenu_page('escl_options', 'Client Management', 'Client Management', 'administrator', 'escl_client_management', 'escl_client_management_page');
  add_submenu_page('escl_options', 'Client Setup', 'Client Setup', 'administrator', 'escl_client_setup', 'escl_client_setup_page');
}

function escl_home(){ ?>
	<div id="theme-options-wrap">
        <div class="icon32" id="icon-tools"> <br /> </div>
        <h2>Client Login Plugin</h2>
        <p><?php _e('Change the settings of this plugin here.'); ?></p>
        <form action='options.php' method='POST'>
            <?php settings_fields('escl_general_settings'); ?>
            <?php do_settings_sections('escl_general_settings'); ?>
        </form>
        <p>N.B. This is a beta version, if you find any bugs, please leave a comment on our website or mail me at <a href="mailto:james@electricstudio.co.uk">james@electricstudio.co.uk</a></p>
        <p>Plugin Created By <a href="http://www.electricstudio.co.uk/">Electric Studio</a> | Get great hosting from <a href="http://www.electrichosting.co.uk/">Electric Hosting</a></p>
    </div>
    <?php
}

function escl_client_management_page(){ ?>
    <div id="theme-options-wrap">
        <div class="icon32" id="icon-tools"> <br /> </div>
        <h2>Client Management</h2>
        <p><?php _e('Manage Users here.'); ?></p>
        <form action="#" method="post">
            <label for="clientsearch">Find Client: </label><input name="clientsearch" type="text" value="<?if(isset($_POST['clientsearch'])){echo $_POST['clientsearch'];}?>"/>
            <input name="search" type="submit"/>
        </form>
        <?php
        if(isset($_POST['clientsearch'])){
            //What to do if the user has searched for a user
            $clients = get_users(array(
                    'role' => 'client',
                    'search' => '*'.$_POST['clientsearch'].'*'
                )
            );
            
            escl_create_client_table($clients);
                
        }else {
            $clients = get_users(array(
                    'role' => 'client',
                    'orderby' => 'registered',
                    'number' => 20
                )
            );
            
            escl_create_client_table($clients);
        }
        /* echo "<p>Export to CSV</p>"; */?>
    </div>
    <?php
}

function escl_create_client_table($clients){
    echo "<table class=\"escl_table wp-list-table widefat\">";
    
    echo "<thead>" .
          " <tr>" .
          "    <th>ID</th>" .
          "    <th>Display Name</th>" .
          "    <th>Login</th>" .
          "    <th>Nice Name</th>" .
          "    <th>Email</th>" .
          "    <th>Groups</th>" .
          "    <th></th>" .
          " </tr>" .
          "</thead>";
    
    foreach($clients as $client){
        echo "<tr>" .
                "<td>" . $client->ID . "</td>" .
                "<td><a href=\"".get_bloginfo('url')."/wp-admin/user-edit.php?user_id=".$client->ID."\">" . $client->display_name . "</a></td>" .
                "<td>" . $client->user_login . "</td>" .
                "<td>" . $client->user_nicename . "</td>" .
                "<td>" . $client->user_email . "</td>" .
                "<td>";
                $tmpGrps = escl_list_users_group($client->ID);
                $c = 0;
                foreach($tmpGrps as $g){
                    $c++;
                    echo $g->group_name;
                    if($c < count($tmpGrps))
                        echo ", ";
                }
                echo "</td>".
                "<td><a href=\"".get_bloginfo('url')."/wp-admin/users.php?s=".$client->ID."&action=-1&new_role&paged=1&action2=-1\">Delete</a></td>" .
              "</tr>";
    }
    
    echo "</table>";
}

function escl_client_setup_page(){ ?>
	<div id="theme-options-wrap">
        <div class="icon32" id="icon-tools"> <br /> </div>
        <h2>Client Management</h2>
        <p><?php _e('Setup Client Structure here'); ?></p>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <label for="field-name">Add A Field: </label><input type="text" name="field-name" value="<?php if(isset($_POST['field-name'])) echo $_POST['field-name'];?>"/>
            <label for="field-type">Type: </label>
                <select name="field-type">
                    <option value="text">Text Input</option>
                    <option value="checkbox">Checkbox</option>
                </select>
            <input type="submit" value=">"/>
        </form>
        <?php if(isset($_POST['field-name'])){
        	$fields = get_option('escl_fields', array());
            if(!isset($fields[$_POST['field-name']])){
                $field = strtolower($_POST['field-name']);
                $field = preg_replace('/ /','-',$field);
                $field = preg_replace('/[\/\\\\]/','',$field);
                
                $fields[$field]['title'] = $_POST['field-name']; 
                $fields[$field]['name'] = $field;
                $fields[$field]['type'] = $_POST['field-type']; 
                /*if($fields[$field]['type']== 'text'){ //@TODO setup other options for fields
                	$fields[$field]['options'] = array('option1','option2');
                }*/
                update_option('escl_fields',$fields);
                echo '<p class="success">Field Added</p>';
            }else{
                echo "<p class=\"error\">A field with that name already exists</p>";
            }
        }else if(isset($_GET['rmfield'])){
            $f = get_option('escl_fields', array());
            unset($f[$_GET['rmfield']]);
            update_option('escl_fields',$f);
        	echo "<p>Field '{$_GET['rmfield']}' has been removed</p>";
        }?>
        
        <h3>Current Fields</h3>
        <?php
        $fields = get_option('escl_fields', array());
        ?>
        
        <table class="escl_table wp-list-table widefat">
            <thead>
                <tr>
                    <th>Field Name</th>
                    <th>Field Type</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
        <?php
        foreach($fields as $field){
        	echo '<tr>';
            echo '  <td>'.$field['title'].'</td>';
            echo '  <td>'.$field['type'].'</td>';
            echo '  <td><a href="'.$_SERVER['REQUEST_URI'].'&rmfield='.$field['name'].'">Remove</a>';
            echo '</tr>';
        }?>
        </table>
        <?php
}

function escl_group_options_page() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if( $_POST[ 'option_page' ] == 'add_group' ) {
		escl_add_group($_POST['escl_group_name'],$_POST['escl_group_status'],$_POST['escl_group_slug']);
	}
	?>
	
    <div id="theme-options-wrap">
    	<div class="icon32" id="icon-tools"> <br /> </div>
    	<h2>Client Login Settings</h2>
    	<p><?php _e('Change the settings of groups here.'); ?></p>
	    <?php if(strlen($_GET['edit_group'])>0){ ?>
	    	<div id='edit-group'>
			<?php $thisGroup = escl_get_group_data($_GET['edit_group']); ?>
				<form action='' method='POST'>
					<?php settings_fields('edit_group'); ?>
		    		<?php do_settings_sections('escl_edit_group'); ?>
				</form>
			</div>
		<?php }else if(strlen($_GET['delete_group'])>0){
			if($_POST['option_page'] == 'delete_group'){
				escl_remove_group($_GET['delete_group']);
				echo $_GET['delete_group']." has been deleted";
			}else{ ?>
				<form action='' method='POST'>
					<?php settings_fields('delete_group'); ?>
			    	<?php do_settings_sections('escl_delete_group'); ?>
				</form>
			<?php } ?>
		<?php }else{?>
	    	<div id='add-group'>
	    	<form method="post" action="">
	    		<?php settings_fields('add_group'); ?>
	    		<?php do_settings_sections('escl_add_group'); ?>
	    	</form>
	    	</div>
		    	
		<?php } ?>
  	</div>
	<?php
}

function register_and_build_escl_options(){
	register_setting('escl_add_group','escl_add_group', 'validate_escl_group_name');
	register_setting('escl_edit_group','escl_edit_group', 'validate_escl_group_name');
	register_setting('escl_delete_group','escl_delete_group', 'validate_escl_group_name');
    register_setting('escl_general_settings','escl_general_settings');

    add_settings_section('escl_general_settings', 'General Settings','escl_general_setting_text','escl_general_settings');
	add_settings_section('add_group', 'Add A Group','escl_add_group_section_text','escl_add_group');
	add_settings_section('delete_group', 'Delete Group','escl_delete_group_section_text','escl_delete_group');
	add_settings_section('manage_group', 'Manage A Group','escl_manage_group_section_text','escl_add_group');
	add_settings_section('edit_group', 'Edit A Group','escl_edit_group_section_text','escl_edit_group');
	add_settings_section('edit_group_users', 'Edit Group Users', 'escl_add_group_user_section_text','escl_edit_group');

    add_settings_field('logout_redirect','Log Out Redirect (Redirect to Homepage on Logout):','escl_logout_redirect','escl_general_settings','escl_general_settings');
    add_settings_field('general_submit','','escl_submit','escl_general_settings','escl_general_settings');    
	add_settings_field('group_name','Group Name: ','escl_group_name','escl_add_group','add_group');
	add_settings_field('group_name','Group Name: ','escl_group_name','escl_edit_group','edit_group');
	add_settings_field('group_slug','Group Slug (optional): ','escl_group_slug','escl_add_group','add_group');
	add_settings_field('group_slug','Group Slug (optional): ','escl_group_slug','escl_edit_group','edit_group');
	add_settings_field('group_status','Group Status: ','escl_group_status','escl_add_group','add_group');
	add_settings_field('group_status','Group Status: ','escl_group_status','escl_edit_group','edit_group');
	add_settings_field('group_submit','','escl_submit','escl_add_group','add_group');
	add_settings_field('group_submit','','escl_submit','escl_edit_group','edit_group');
	add_settings_field('group_add_user', 'Add User: ','escl_add_user','escl_edit_group','edit_group_users');
	add_settings_field('group_current_users', 'Current Users: ','escl_list_current_members','escl_edit_group','edit_group_users');
	add_settings_field('group_manage', 'Manage Groups: ','escl_create_groups_table','escl_add_group','manage_group');
	add_settings_field('','','escl_group_slug_hidden','escl_delete_group','delete_group');
	add_settings_field('group_submit','','escl_confirm','escl_delete_group','delete_group');
}

function validate_escl_group_name($optionname){
	//put any validation on the option here.

	return $optionname;
}

function validate_escl_user_name($optionname){
	//put any validation on the option here.
	return $optionname;
}

function escl_add_group_section_text(){
	echo "Fill in form to add a group";
}

function escl_manage_group_section_text(){
	echo "See all your groups. Click edit, to edit group and add or remove users";
}

function escl_add_group_user_section_text(){
	echo "Manage the group's users here";
}

function escl_general_setting_text(){
	echo "Manage the plugin's General Settings";
}

function escl_delete_group_section_text(){
	echo "Confirm you want to delete this group";
}

function escl_edit_group_section_text(){
	echo "Amend form to edit group";
}

function escl_group_name(){
	$option = "";
	$option .= "<input type=\"text\" name=\"escl_group_name\" ";
	if($_GET['edit_group']){
		$groupdata = escl_get_group_data($_GET['edit_group']);
		$option .= "value=\"".$groupdata->group_name."\"";
	}
	$option .= "/>";
	echo $option;
}

function escl_group_slug(){
  $option = "";
  $option .= "<input type=\"text\" name=\"escl_group_slug\" ";
  if($_GET['edit_group']){
  	$option .= "value=\"".$_GET['edit_group']."\"";
  }
  $option .= "/>";
  echo $option;
}

function escl_group_slug_hidden(){
  $option = "";
  $option .= "<input type=\"hidden\" name=\"escl_group_slug\" ";
  $option .= "value=\"".$_GET['remove_group']."\"";
  $option .= "/>";
  echo $option;
}

function escl_group_status(){
	$option = "";
	$option .= "<select name=\"escl_group_status\">";
	if($_GET['edit_group']){
		$groupdata = escl_get_group_data($_GET['edit_group']);
	}else{
		$groupdata = "";
	}
	$option .= "<option value=\"active\" ";
	$option .= ($groupdata->group_status == 'active') ? "selected=\"selected\"" : "";
	$option .= ">Active</option>";
	$option .= "<option value=\"inactive\" ";
	$option .= ($groupdata->group_status == 'inactive') ? "selected=\"selected\"" : "";
	$option .= ">Inactive</option>";
	$option .= "</select>";
	echo $option;
}

function escl_add_user(){
	?>
	<input type="text" name="escl-search-user" id="escl-search-user" />
	<div id="user-search-results-loading" style="display:none">LOADING...</div>
	<div id="user-search-results" style="display:none"></div>
	<div id="current-group-users">
	<?php
}

function escl_logout_redirect(){
  $vals = get_option('escl_general_settings');
  $option = "<input name=\"escl_general_settings[escl_logout_redirect]\" " .
        " type=\"checkbox\" value=\"1\" " . checked( 1, $vals['escl_logout_redirect'], false ) . " />";
  echo $option;	
}

function escl_list_current_members(){ ?>
	<ul id="group-user-list">
	<?php
	$groupusers = escl_list_users_in_group($_GET['edit_group']);
	foreach($groupusers as $groupuser){
		echo "<li class=\"group-user userid-".$groupuser->ID."\">".$groupuser->user_nicename." <a href=\"#\" class=\"rmFromGroup\">x</a></li>";
	}
	?>
	</ul>
	<p>N.B. After add/remove users, they may still appear until you have refreshed this page.</p>
	<?php
}

function escl_create_groups_table(){ ?>
	<div id='select-group'><?php $groups = escl_list_groups();?>
	<table class="group-list wp-list-table widefat">
        <thead>
		  <tr>
			 <th>Group Name</th>
			 <th>Group Slug</th>
			 <th>Group Status</th>
			 <th></th>
             <th></th>
		  </tr>
        </thead>
		<?php
		$isEven = false;
		foreach($groups as $group){
			$isEven = ($isEven == true) ? false : true;
			$class = ($isEven == true) ? "even" : "odd";
			echo "<tr class=\"";
			echo $class;
			echo "\">";
			echo "<td>".$group->group_name."</td>
		    				<td>".$group->group_slug."</td>
		    				<td>".$group->group_status."</td>
		    				<td><a href='".$_SERVER['REQUEST_URI']."&edit_group=".$group->group_slug."'>Edit</a></td>
		    				<td><a href='".$_SERVER['REQUEST_URI']."&delete_group=".$group->group_slug."'>Delete</a></td>";
			echo "</tr>";
		}
	
		?>
	</table>
	</div>
<?php }

function escl_confirm(){?>
	<p class="submit"><input name="Submit" type="submit"
		class="button-primary" value="<?php esc_attr_e('Confirm'); ?>" />
	</p>
<?php }

function escl_submit(){?>
	<p class="submit"><input name="Submit" type="submit"
		class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
<?php }

 
function extra_user_profile_fields( $user ) { ?>
<h3><?php _e("Extra profile information", "blank"); ?></h3>

 
<table class="form-table">
<?php

$escl_fields= get_option('escl_fields',array());


foreach($escl_fields as $field){ //echo out all the custom fields ?>
    <tr>
    <?php    
        echo '<th><label for="'.$field['name'].'">'.$field['title'].': </label></th>';
        $val = get_user_meta($user->ID,'escl-'.$field['name'],true);
        echo '<td>';
        if($field['type']=='text'){
            echo "<input type=\"text\" value=\"$val\" name=\"escl-{$field['name']}\"><br/>";
        }else if($field['type']=='checkbox'){
            $html = "<input type=\"checkbox\" name=\"escl-{$field['name']}\" value=\"true\"";
            if($val == 'true')
                $html .= ' checked="checked" ';
            $html .= "><br/>";
            echo $html;
        }
        echo '</td>';
        ?>
    </tr>
    <?php
}
?>
</table>
<?php }
 
 
function save_extra_user_profile_fields( $user_id ) {
 
if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
    $escl_fields= get_option('escl_fields',array());

    foreach($escl_fields as $efield){
        $tmp = $_POST["escl-{$efield['name']}"];
        $re = update_user_meta($user_id,'escl-'.$efield['name'],$tmp);
    }

}