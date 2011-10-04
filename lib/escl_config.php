<?php

//directory of the current theme
$themeDir = get_theme_root() . '/' . get_template()."/";

//array of template filenames that need importing on install
$templateArray = array('page-clientarea.php');

//directory of the templates (within plugin)
$pluginTemplateDir = $plugindir."templates/";

//array of other config
$escl_config = array(
	'groupsTableName' => "escl_user_group",
	'groupsRelTableName' => "escl_user_group_rel"
);