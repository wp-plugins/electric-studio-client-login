<?php

add_shortcode('escl_login_panel','escl_custom_login');

//this function is outputs a login form.
function escl_custom_login(){ ?>
			<?php global $user_ID, $user_identity, $user_level ?>
			<?php if ( $user_ID ) : ?>
			Logged in as <strong><?php echo $user_identity ?></strong>.
			<ul id="escl-control-panel">
				<li><a href="<?php bloginfo('url') ?>/wp-admin/"><?php echo __('Dashboard')?></a></li>
				<?php if ( $user_level >= 1 ) : ?>
				<li><a href="<?php bloginfo('url') ?>/wp-admin/post-new.php"><?php echo __('Add a Post')?></a></li>
				<?php endif // $user_level >= 1 ?>
				<li><a href="<?php echo escl_get_client_area_perm(); ?>"><?php echo __('My Area')?></a></li>
				<li><a href="<?php bloginfo('url') ?>/wp-admin/profile.php"><?php echo __('Edit Profile')?></a></li>
				<li><?php escl_logout_link(); ?></li>
			</ul>
	
			<?php else : ?>
	
			<form action="<?php bloginfo('url') ?>/wp-login.php" method="post">
				<p>
				<label for="log"><input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="22" /> User</label><br />
				<label for="pwd"><input type="password" name="pwd" id="pwd" size="22" /> Password</label><br />
				<input type="submit" name="submit" value="Send" class="button" />
				<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label><br />
				</p>
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

//this is the callback function  for the login widget
function escl_widget_login($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Login<?php echo $after_title;
	escl_custom_login();
	echo $after_widget;
}


//this function returns the permlinks of the page with the title of 'Client Area'
function escl_get_client_area_perm(){
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

function escl_logout_link(){
    $vals = get_option('escl_general_settings');
    if($vals['escl_logout_redirect'] == 1){?>
        <a href="<?php echo wp_logout_url( get_bloginfo('url') ); ?>" title="Logout">Logout</a>	
    <?php }else{ ?>
    	<a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Logout">Logout</a>	
    <?php }
}