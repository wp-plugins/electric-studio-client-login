<?php

class Escl_functions{

    function __construct(){
        add_shortcode('escl_logged_in', array(&$this,'is_logged_in'));
        add_shortcode('escl_login_panel',array(&$this,'custom_login'));
        add_shortcode('escl-login', array(&$this,'custom_login'));
        add_action("plugins_loaded",array(&$this,"widget_init"));  
        add_action('wp_head',array(&$this,'redirect_to_login'));
    }

    function widget_init(){
    	wp_register_sidebar_widget(
    		'escl_login_form',
    		__('Login'),
    		array(&$this,'widget_login'),
    		array('description' => 'Places a login form in the sidebar')
    	);
    }
    
    // [escl_logged_in]foo[/escl_logged_in]
    function is_logged_in($atts, $content=null){
    	if ( self::login_check($atts) )
    		return do_shortcode($content);
    	else
    		return "";
    	
    }
    
    //this is the callback function  for the login widget
    function widget_login($args) {
    	extract($args);
    	echo $before_widget;
    	echo $before_title;?>Login<?php echo $after_title;
    	self::custom_login();
    	echo $after_widget;
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
    	    if(is_singular()) //check that this is a single post
        		if((is_array($groupsAllowed[0]) && Escl_groups::user_in_group($user_identity,$groupsAllowed[0])==false))
                    //if user is not allowed to view this page, redirect to login
        			wp_redirect(wp_login_url(get_permalink($post_id)));
    }

    /**
     * @method custom_login()
     * @since 0.8
     * Displays the login form if not logged in, else shows the 'logged in' menu
     */
    function custom_login(){ ?>
    			<?php global $user_ID, $user_identity, $user_level ?>
    			<?php if ( $user_ID ) : ?>
    			Logged in as <strong><?php echo $user_identity ?></strong>.
    			<ul id="escl-control-panel">
    				<li><a href="<?php bloginfo('url') ?>/wp-admin/"><?php echo __('Dashboard')?></a></li>
    				<?php if ( $user_level >= 1 ) : ?>
    				<li><a href="<?php bloginfo('url') ?>/wp-admin/post-new.php"><?php echo __('Add a Post')?></a></li>
    				<?php endif // $user_level >= 1 ?>
    				<li><a href="<?php echo self::get_client_area_perm(); ?>"><?php echo __('My Area')?></a></li>
    				<li><a href="<?php bloginfo('url') ?>/wp-admin/profile.php"><?php echo __('Edit Profile')?></a></li>
    				<li><?php self::logout_link(); ?></li>
    			</ul>
    	
    			<?php else : ?>
    	
    			<form action="<?php bloginfo('url') ?>/wp-login.php" id="escl-login-form" method="post">
    				<p>
    					<label for="log">User</label>
    					<input type="text" name="log" id="log" value="<?php echo esc_html(stripslashes($user_login), 1) ?>" size="22" />
					</p>
					<p>
    					<label for="pwd">Password</label>
    					<input type="password" name="pwd" id="pwd" size="22" />
    				</p>
    				<input type="submit" name="submit" value="Send" class="button" />
    				<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
    				<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
    			</form>
    			<ul>
    				<?php if ( get_option('users_can_register') ){ ?>
    					<li><a href="<?php bloginfo('url') ?>/wp-register.php">Register</a></li>
    				<?php } ?>
    				<li><a href="<?php bloginfo('url') ?>/wp-login.php?action=lostpassword">Recover password</a></li>
    			<?php endif ?>
    			</ul>
    <?php }    
    
    /**
     * @method lout_link()
     * @return echos html
     * @since 0.8
     * Displays logout link
     */
    function logout_link(){
        $vals = get_option('escl_general_settings');
        if($vals['escl_logout_redirect'] == 1){?>
            <a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>" title="Logout">Logout</a>	
        <?php }else{ ?>
        	<a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Logout">Logout</a>	
        <?php }
    }

    /**
     * @method get_client_area_perm
     * @since 0.8
     * @return string(url)
     * this method returns the permlinks of the page with the title of 'Client Area'
     */
    function get_client_area_perm(){
    	global $wpdb;
    	$pageTitle = 'Client Area';
    	
    	$sql = "SELECT ID FROM $wpdb->posts
    		WHERE ( post_name = '".$pageTitle."' OR post_title = '".$pageTitle."' ) 
    		AND post_status = 'publish'
    		AND post_type='page'";
    	
    	$sql = $wpdb->prepare($sql);
    	
    	$pageID = $wpdb->get_var($sql);
    	
    	return get_permalink($pageID);
    }
    
}

$esclf = new Escl_functions();


/* Start Compatability */
/* these are functions which wouldn't work anymore due to the 
 * functions now being methods within a class,
 * so these functions allow them to work as before
 * 
 * N.B. Do not use these functions as we plan on deprecating them
 */

/**
 * 
 * For shortcode of text that should only be displayed to users that are logged in
 * @param unknown_type $atts
 * @param unknown_type $content
 * @since 0.7.5
 * @deprecated
 * @see Escl_functions::is_logged_in()
 */
function escl_is_logged_in($atts,$content=null){
    global $esclf;
    return $esclf->escl_is_logged_in($atts,$content);
}

/**
 * 
 * Checks if a user is logged in
 * @param unknown_type $atts
 * @since 0.7
 * @deprecated
 * @see Escl_functions::login_check()
 */
function escl_login_check($atts){
    global $esclf;
    return $esclf->login_check($atts);
}

/**
 * 
 * Dependant on settings, redirects after login
 * @since 0.7.1
 * @deprecated
 * @see Escl_functions::redirect_to_login()
 */
function escl_redirect_to_login(){
    global $esclf;
    $esclf->redirect_to_login();
}

/**
 * 
 * Displays a link to logout
 * @since 0.7
 * @deprecated
 * @see Escl_functions::logout_link()
 */
function escl_logout_link(){
    global $esclf;
    $esclf->logout_link();
}

/**
 * 
 * Gets permalink of the clients area
 * @since 0.7
 * @deprecated
 * @see Escl_functions::get_client_area_perm()
 */
function escl_get_client_area_perm(){
    global $esclf;
    $esclf->get_client_area_perm();
}

/**
 * 
 * Displays the loginform / logged in menu
 * @since 0.7.1
 * @deprecated
 * @see Escl_functions::custom_login()
 */
function escl_custom_login(){
    global $esclf;
    $esclf->custom_login();
}

/**
 * 
 * this is the callback function for the login widget
 * @param unknown_type $args
 * @since 0.7.1
 * @deprecated
 * @see Escl_functions::widget_login()
 */
function escl_widget_login($args){
    global $esclf;
    $esclf->widget_login($args);
}
/* End Compatability */