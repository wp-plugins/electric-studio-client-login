<?php

add_action('admin_menu', 'create_escl_options_page');
add_action('admin_init', 'register_and_build_escl_options');

function create_escl_options_page() {
  add_options_page('Clients Area', 'Clients Area', 'administrator', 'escl_options', 'escl_options_page');
}

function escl_options_page() {
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
    	<p><?php _e('Change the settings of this plugin here.'); ?></p>
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
		<p>N.B. This is a beta version, if you find any bugs, please leave a comment on our website or mail me at <a href="mailto:james@electricstudio.co.uk">james@electricstudio.co.uk</a></p>
    	<p>Plugin Created By <a href="http://www.electricstudio.co.uk/">Electric Studio</a> | Get great hosting from <a href="http://www.electrichosting.co.uk/">Electric Hosting</a></p>
  	</div>
	<?php
}

function register_and_build_escl_options(){
	register_setting('escl_add_group','escl_add_group', 'validate_escl_group_name');
	register_setting('escl_edit_group','escl_edit_group', 'validate_escl_group_name');
	register_setting('escl_delete_group','escl_delete_group', 'validate_escl_group_name');

	add_settings_section('add_group', 'Add A Group','escl_add_group_section_text','escl_add_group');
	add_settings_section('delete_group', 'Delete Group','escl_delete_group_section_text','escl_delete_group');
	add_settings_section('manage_group', 'Manage A Group','escl_manage_group_section_text','escl_add_group');
	add_settings_section('edit_group', 'Edit A Group','escl_edit_group_section_text','escl_edit_group');
	add_settings_section('edit_group_users', 'Edit Group Users', 'escl_add_group_user_section_text','escl_edit_group');

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
	<table id="group-list">
		<tr>
			<th>Group Name</th>
			<th>Group Slug</th>
			<th>Group Status</th>
			<th></th>
		</tr>
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