<?php

class Escl_ajax{

    function __construct(){
        if( is_admin() ){
        	//add ajax hooks if logged in as admin
        	add_action('admin_print_footer_scripts',array(&$this,'print_livesearchjs'));
        	add_action('wp_ajax_nopriv_esclusersearch',array(&$this,'userlivesearch'));
        	add_action('wp_ajax_esclusersearch',array(&$this,'userlivesearch'));
        	add_action('wp_ajax_nopriv_escladdusertogroup',array(&$this,'addusertogroup'));
        	add_action('wp_ajax_escladdusertogroup',array(&$this,'addusertogroup'));
        	add_action('wp_ajax_nopriv_esclremoveuserfromgroup',array(&$this,'removeuserfromgroup'));
        	add_action('wp_ajax_esclremoveuserfromgroup',array(&$this,'removeuserfromgroup'));
        }
    }
    
    function print_livesearchjs(){
    	//nonces for ajax
    	$esclLiveSearch_nonce = wp_create_nonce('esclLS');
    	$esclu2gadd_nonce = wp_create_nonce('esclu2gadd');
    	$esclufgrm_nonce = wp_create_nonce('esclufgrm');
    	?>
    	<script type="text/javascript">
    		function escl_usersearch(criteria) {
    			jQuery.ajax({
    				type: "post",
    				url: "<?php echo get_admin_url(); ?>admin-ajax.php",
    				data: {
    					action: 'esclusersearch',
    					criteria: criteria,
    					_ajax_nonce: '<?php echo $esclLiveSearch_nonce; ?>'
    				},
    				beforeSend: function () {
    					jQuery('div#user-search-results-loading').show();
    				},
    				complete: function () {
    					jQuery('div#user-search-results-loading').hide();
    				},
    				success: function (html) { //so, if data is retrieved, store it in html
    					jQuery('div#user-search-results').show();
    					jQuery('div#user-search-results').html(html);
    				},
    				error: function () {
    					alert('There has been an error, Please try again');
    					return false;
    				}
    			});
    		}
    		
    		function escl_addusertogroup(userid) {
    			jQuery.ajax({
    				type: "post",
    				url: "<?php echo get_admin_url(); ?>admin-ajax.php",
    				data: {
    					action: 'escladdusertogroup',
    					userid: userid,
    					groupslug: '<?php echo $_GET['edit_group']; ?>',
    					_ajax_nonce: '<?php echo $esclu2gadd_nonce; ?>'
    				},
    				beforeSend: function () {
    					jQuery('div#user-search-results-loading').show();
    				},
    				complete: function () {
    					jQuery('div#user-search-results-loading').hide();
    				},
    				success: function (html) { //so, if data is retrieved, store it in html
    					jQuery('<li>').html(html).appendTo('ul#group-user-list').hide().fadeIn();
    				},
    				error: function () {
    					alert('There has been an error, Please try again');
    					return false;
    				}
    			});
    		}
		
    		function escl_removeuserfromgroup(userid) {
    			jQuery.ajax({
    				type: "post",
    				url: "<?php echo get_admin_url(); ?>admin-ajax.php",
    				data: {
    					action: 'esclremoveuserfromgroup',
    					userid: userid,
    					groupslug: '<?php echo $_GET['edit_group']; ?>',
    					_ajax_nonce: '<?php echo $esclufgrm_nonce; ?>'
    				},
    				beforeSend: function () {
    					//jQuery('div#user-search-results-loading').show();
    				},
    				complete: function () {
    					//jQuery('div#user-search-results-loading').hide();
    				},
    				success: function (html) { //so, if data is retrieved, store it in html
    					jQuery('ul#group-user-list li.userid-' + userid).fadeOut(function () {
    						jQuery(this).remove();
    					});
    					alert(html);
    				},
    				error: function () {
    					alert('There has been an error, Please try again');
    					return false;
    				}
    			});
    		}
    	</script>
    <?php } 
    
    function userlivesearch(){
    	wp_nonce_field('esclLS');
    	$clients = escl_get_users(array(
    		'role' => 'client',
    		'search' => '*'.$_POST['criteria'].'*'
    		)
    	);
    
    	$html .= '<ul>';
    	foreach($clients as $client){
    		$html.=	'<li class="'.$client->user_nicename.'-'.$client->ID.'"><a href="#" class="liveUserResult userID-'.$client->ID.'">'.$client->display_name.'</a></li>'."\n";
    	}
    	$html .= '</ul>';
    			
    	echo $html;
    	die;
    }
    
    function addusertogroup(){
    	wp_nonce_field('esclu2gadd');
    	$user = new Escl_user($_POST['userid']);
    	$groupinfo = Escl_groups::get_group_data($_POST['groupslug']);
    	$user->addToGroup($groupinfo->group_id);
    	echo $user->get('user_login');
    	die;
    }
    
    function removeuserfromgroup(){
    	wp_nonce_field('esclufgrm');
    	$user = new Escl_user($_POST['userid']);
    	$groupinfo = Escl_groups::get_group_data($_POST['groupslug']);
    	$user->RemoveFromGroup($groupinfo->group_id);
    	die;	
    }

}

$esclAjax = new Escl_ajax();