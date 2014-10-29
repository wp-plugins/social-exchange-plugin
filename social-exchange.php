<?php
	/*
		Plugin Name: MoreSharesForYou
		Plugin URI: http://www.MoreSharesForYou.com/
		Description: MoreSharesForYou is a content discovery and promotion tool that gets your content viewed and shared by other people.
		Version: 2.9.5
		Author: MoreSharesForYou team
		Author URI: http://www.MoreSharesForYou.com/
		License: GPL
	*/

	$sxpath =  plugin_dir_path(__FILE__ );
	global $sxpath;


	if (function_exists('sx')) {
		wp_die('More Shares For You - Basic Version cannot be activated. Please deactivate the PRO version first. <br /><br />Back to the WordPress <a href="'.get_admin_url(null, 'plugins.php').'">Plugins page</a>.');
	}
	update_option('sx_version','2.9.5');
	if (get_option('sx_active_campaigns') === FALSE)
		add_option('sx_active_campaigns',array());
	require_once "inc/config.php";
	require_once "inc/common.php";
	require_once "inc/render.php";

?>
