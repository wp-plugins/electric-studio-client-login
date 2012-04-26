<?php

class Escl_options{

    function __construct(){
        if(is_admin()){
            add_action('admin_menu', array(&$this,'create_options_page'));
            add_action('admin_init', array(&$this,'register_and_build_options'));
            add_action('show_user_profile', array(&$this,'extra_user_profile_fields'));
            add_action('edit_user_profile', array(&$this,'extra_user_profile_fields'));
            add_action('show_user_profile', array(&$this,'user_group_list'));
            add_action('edit_user_profile', array(&$this,'user_group_list'));
            add_action('personal_options_update', array(&$this,'save_extra_user_profile_fields'));
            add_action('edit_user_profile_update', array(&$this,'save_extra_user_profile_fields'));
        }
    }
    
    /**
     * 
     * Building the menus to be added to the wp-admin sidebar
     * @since 0.7
     */
    function create_options_page() {
        add_menu_page('Clients Area', 'Clients Area', 'add_users', 'escl_options');
        add_submenu_page('escl_options', 'General Settings', 'General Settings', 'add_users', 'escl_options', array(&$this,'home'));
        add_submenu_page('escl_options', 'Group Management', 'Group Management', 'add_users', 'escl_group_options', array(&$this,'group_options_page'));
        add_submenu_page('escl_options', 'Client Setup', 'Client Setup', 'add_users', 'escl_client_setup', array(&$this,'client_setup_page'));
    }
    
    /**
     * 
     * This is where the bulk of the settings are arranged. It requires an understanding of the Wordpress settings API
     * @since 0.7
     */
    function register_and_build_options(){
        //Welcome to the Wordpress Settings API!!!
        register_setting('escl_general_settings','escl_general_settings');
    	register_setting('escl_add_group','escl_add_group', 'validate_escl_group_name');
    	register_setting('escl_edit_group','escl_edit_group', 'validate_escl_group_name');
    	register_setting('escl_delete_group','escl_delete_group', 'validate_escl_group_name');    
        
        
        //General settings sections
        add_settings_section('escl_general_settings', 'General Settings',array(&$this,'general_setting_text'),'escl_general_settings');
    	
        //Add Group Settings sections
        add_settings_section('add_group', 'Add A Group',array(&$this,'add_group_section_text'),'escl_add_group');
        
        //Edit group settings section
        add_settings_section('edit_group', 'Edit A Group',array(&$this,'edit_group_section_text'),'escl_edit_group');
    	add_settings_section('edit_group_users', 'Edit Group Users', array(&$this,'add_group_user_section_text'),'escl_edit_group');
    	
    	
    	//General settings fields
        add_settings_field('logout_redirect','Log Out Redirect (Redirect to Homepage on Logout):',array(&$this,'logout_redirect'),'escl_general_settings','escl_general_settings');
        add_settings_field('general_submit','',array(&$this,'submit'),'escl_general_settings','escl_general_settings');    
    	
        //add group settings fields
        add_settings_field('group_name','Group Name: ',array(&$this,'group_name'),'escl_add_group','add_group');
    	add_settings_field('group_slug','Group Slug (optional): ',array(&$this,'group_slug'),'escl_add_group','add_group');
    	add_settings_field('group_status','Group Status: ',array(&$this,'group_status'),'escl_add_group','add_group');
    	add_settings_field('', '', array(&$this,'add_action_hidden'),'escl_add_group','add_group');
    	add_settings_field('group_submit','',array(&$this,'submit'),'escl_add_group','add_group');
    	
    	//edit group settings fields
    	add_settings_field('group_name','Group Name: ',array(&$this,'group_name'),'escl_edit_group','edit_group');
    	add_settings_field('group_slug','Group Slug (optional): ',array(&$this,'group_slug'),'escl_edit_group','edit_group');
    	add_settings_field('group_status','Group Status: ',array(&$this,'group_status'),'escl_edit_group','edit_group');
    	add_settings_field('', '', array(&$this,'edit_action_hidden'),'escl_edit_group','edit_group');
    	add_settings_field('group_submit','',array(&$this,'submit'),'escl_edit_group','edit_group');
    	add_settings_field('group_add_user', 'Add User: ',array(&$this,'add_user'),'escl_edit_group','edit_group_users');
    	add_settings_field('group_current_users', 'Current Users: ',array(&$this,'list_current_members'),'escl_edit_group','edit_group_users');
    }
    
    /**
     * 
     * This is the page that is labelled general settings in the menu
     */
    function home(){ ?>
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
    
    /**
     * 
     * This is the page for clients setup
     */
    function client_setup_page(){ ?>
    	<div class="wrap">
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
                            <th>Options</th>
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
            </div>
            <?php
    }
    
    /**
     * 
     * This controls all the group actions and displays the correct settings for the group management section
     * @since 0.7
     */
    function group_options_page() {
    	if (!current_user_can('manage_options'))  {
    		wp_die( __('You do not have sufficient permissions to access this page.') );
    	}
        
    	//Check if the add action is present
    	if( $_POST[ 'escl_action' ] == 'add' ) {
    	    //if so, start a Escl_group class
    	    $g = new Escl_groups();
    	    //run the add_group method
    	    $g->add_group($_POST['escl_group_name'], $_POST['escl_group_status'], $_POST['escl_group_slug']);
    	    //destroy the class (we are done with it, no need to keep it)
    	    unset($g);
	    //Check if the edit action is present
    	}elseif( $_POST[ 'escl_action'] == 'edit' ){
    	    //if so, start a Escl_group class
    	    $g = new Escl_groups();
    	    //run the update_group method
    	    $g->update_group($_POST['escl_group_slug'], $_POST['escl_group_name'], $_POST['escl_group_status']);
    	    //destroy the class (we are done with it, no need to keep it)
    	    unset($g);
	    //check if a group delete was requested
    	}elseif(isset($_GET['delete_group'])){
    	    //if so, start a Escl_group class
    	    $g = new Escl_groups();
    	    //run the remove_group method
    	    $g->remove_group($_GET['delete_group']);
    	    //destroy the class (we are done with it, no need to keep it)
    	    unset($g);
    	}
    	?>
    	
	    	<div class='wrap'>
                <div id="theme-options-wrap">
            	<div class="icon32" id="icon-tools">
            		<br /> 
            	</div>
            	<?php if($_GET['esclg']=="add"){?>
            		<h2>Add Group</h2>
            		<p><?php _e('Create a group.'); ?></p>
            		<form method="post" action="admin.php?page=escl_group_options">
            			<?php 
            			settings_fields('escl_add_group');
            			do_settings_sections('escl_add_group')
            			?>
            		</form>
            	<?php }elseif($_GET['esclg']=="edit"){?>
            		<form method="post" action="admin.php?page=escl_group_options">
            			<?php settings_fields('escl_edit_group'); ?>
            			<?php do_settings_sections('escl_edit_group'); ?>
            		</form>
            	<?php }else{ ?>
                	<h2>
                		Group Management
                		<a href="admin.php?page=escl_group_options&esclg=add" class="add-new-h2">Add New</a>
                	</h2>
                	<p><?php _e('Change the settings of groups here.'); ?></p>
            	    		<div class=
        	    		    <?php $this->create_groups_table()?>
              		</div>
          		<?php } ?>
	    	</div>
    	<?php
    }
    
    
    /**
     * 
     * Validation for the group name
     * @param string $optionname
     * @since 0.7
     */
    function validate_escl_group_name($optionname){
    	//put any validation on the option here.
    
    	return $optionname;
    }
    
    /**
     * 
     * Validation for the username
     * @param string $optionname
     * @since 0.7
     */
    function validate_escl_user_name($optionname){
    	return $optionname;
    }
    
    /**
     * 
     * Text to go under the add group title
     * @since 0.7
     */
    function add_group_section_text(){
    	echo "Fill in form to add a group";
    }
    
    /**
     * 
     * Text to go under the manage group title
     * @since 0.7
     */
    function manage_group_section_text(){
    	echo "See all your groups. Click edit, to edit group and add or remove users";
    }
    
    /**
     * 
     * Text to go under the add group user title
     */
    function add_group_user_section_text(){
    	echo "Manage the group's users here";
    }
    
    /**
     * 
     * Text to go under the general settings title
     */
    function general_setting_text(){
    	echo "Manage the plugin's General Settings";
    }
    
    /**
     * 
     * Text to go under the edit group title
     */
    function edit_group_section_text(){
    	echo "Amend form to edit group";
    }
    
    /**
     * 
     * Form element for group name
     * @since 0.7
     */
    function group_name(){
    	$option = "";
    	$option .= "<input type=\"text\" name=\"escl_group_name\" ";
    	if($_GET['escl_slug']){
    		$groupdata = Escl_groups::get_group_data($_GET['escl_slug']);
    		$option .= "value=\"".$groupdata->group_name."\"";
    	}
    	$option .= "/>";
    	echo $option;
    }
    
    /**
     * 
     * Form element for group slug
     * @since 0.7
     */
    function group_slug(){
      $option = "";
      $option .= "<input type=\"text\" name=\"escl_group_slug\" ";
      if($_GET['escl_slug']){
      	$option .= "value=\"".$_GET['escl_slug']."\"";
      }
      $option .= "/>";
      echo $option;
    }
    
    /**
     * 
     * Form element for group status
     * @since 0.7
     */
    function group_status(){
    	$option = "";
    	$option .= "<select name=\"escl_group_status\">";
    	if($_GET['escl_slug']){
    		$groupdata = Escl_groups::get_group_data($_GET['escl_slug']);
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
    
    /**
     * 
     * Hidden form element to specify the add action
     * @since 0.8
     */
    function add_action_hidden(){
         $option = "";
         $option .="<input type=\"hidden\" name=\"escl_action\" ";
         $option .= "value=\"add\"";
         $option .= "/>";
         echo $option;   
    }

    /**
     * 
     * Hidden form element to specify the add action
     * @since 0.8
     */
    function edit_action_hidden(){
         $option = "";
         $option .="<input type=\"hidden\" name=\"escl_action\" ";
         $option .= "value=\"edit\"";
         $option .= "/>";
         echo $option;   
    }    
    
    function add_user(){
    	?>
    	<input type="text" name="escl-search-user" id="escl-search-user" />
    	<div id="user-search-results-loading" style="display:none">LOADING...</div>
    	<div id="user-search-results" style="display:none"></div>
    	<div id="current-group-users">
    	<?php
    }
    
    /**
     * 
     * Form Element for the redirect setting
     * @since 0.7.7
     */
    function logout_redirect(){
      $vals = get_option('escl_general_settings');
      $option = "<input name=\"escl_general_settings[escl_logout_redirect]\" " .
            " type=\"checkbox\" value=\"1\" " . checked( 1, $vals['escl_logout_redirect'], false ) . " />";
      echo $option;	
    }
    
    /**
     * 
     * Displays list of current members of a group
     * @since 0.7
     */
    function list_current_members(){ ?>
    	<ul id="group-user-list">
        	<?php
        	$groupusers = Escl_groups::list_users_in_group($_GET['escl_slug']);
        	foreach($groupusers as $groupuser){
        		echo "<li class=\"group-user userid-".$groupuser->ID."\">".$groupuser->user_nicename." <a href=\"#\" class=\"rmFromGroup\">x</a></li>";
        	} ?>
    	</ul>
    	<p>N.B. After add/remove users, they may still appear until you have refreshed this page.</p>
    	<?php
    }
    
    /**
     * 
     * Creates a 'Wordpress Style' table for the options page that displays all the groups
     * @since 0.7
     */
    function create_groups_table(){ ?>
    	<div id='select-group'><?php $groups = Escl_groups::list_groups();?>
        	<table class="group-list wp-list-table widefat">
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Group Slug</th>
                        <th>Group Status</th>
                        <th>Options</th>
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
        		    				<td><a href='admin.php?page=escl_group_options&esclg=edit&escl_slug=".$group->group_slug."'>Edit</a> | <a href='admin.php?page=escl_group_options&delete_group=".$group->group_slug."' onclick='return confirm(\"Are you sure you want to delete group $group->group_name?\")'>Delete</a></td>";
        			echo "</tr>";
        		} ?>
        	</table>
    	</div>
        <?php
    }
    
    /**
     * 
     * Form Element for a confirm button
     * @since 0.7
     */
    function confirm(){?>
    	<p class="submit"><input name="Submit" type="submit"
    		class="button-primary" value="<?php esc_attr_e('Confirm'); ?>" />
    	</p>
        <?php
    }
    
    /** 
     * 
     * Form Element for a Submit button
     * @since 0.7
     */
    function submit(){?>
    	<p class="submit"><input name="Submit" type="submit"
    		class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
    	</p>
        <?php
    }
    
     /**
      * 
      * This method add extra fields to Wordpress' edit user page
      * @param unknown_type $user
      * @since 0.7.5
      */
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
     
    /**
     * 
     * This saves the extra fields from Wordpress' edit user page
     * @param unknown_type $user_id
     * @since 0.7.5
     */ 
    function save_extra_user_profile_fields( $user_id ) {
     
    if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
        $escl_fields= get_option('escl_fields',array());
    
        foreach($escl_fields as $efield){
            $tmp = $_POST["escl-{$efield['name']}"];
            $re = update_user_meta($user_id,'escl-'.$efield['name'],$tmp);
        }
    
    }

    /**
     * 
     * Displays list of groups user is a member of to be used in Wordpress' edit user page
     * @param unknown_type $user
     * @since 0.8
     */
    function user_group_list( $user ){?>
    	<h3><?php _e("Groups", "blank"); ?></h3>
    	<table class="form-table">
            <tr>
            	<th>Member of:</th>
            	<td>
            		<ul>
                	    <?php
                	    $thisUser = new Escl_user($user->get('ID'));
                	    $groups = $thisUser->listGroups();
                	    foreach($groups as $g){
                	        $html = "<li>";
                	        $html .= "<span class=\"";
                	        
                	        if($g->group_status == "active")
                	            $html .= "active";
            	            else 
                	            $html .= "inactive";
            	            
                	            $html .= "\">".$g->group_name."</span>";
            	            
                	        if($g->group_status != "active")
            	                $html .= "(inactive)";
                	        
            	            $html .= "</li>";
                	        echo $html;
                	    }
                	    ?>
            	    </ul>
        	    </td>
            </tr>
        </table>
        <?php
    }
}

$esclOptions = new Escl_options();