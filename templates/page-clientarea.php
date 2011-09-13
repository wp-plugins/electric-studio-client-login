<?php
/*
	@package     WordPress
	@subpackage  Electric_Studio
	Template Name: Client Area
*/
get_header();
?>

  <div id="main_heading">
      <h1><?php echo __('My Area'); ?></h1>
  </div>
      
<div id="content_wrapper">
<div id="content" class="contentnarrow" role="main">

	<?php echo "hello $user_identity"; ?>
    
</div><!-- #content | Ends -->

<?php get_sidebar(); ?>

</div><!-- #content_wrapper | Ends -->

<?php get_footer(); ?>