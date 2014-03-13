<?php
	/*
		Plugin Name: MoreSharesForYou
		Plugin URI: http://www.MoreSharesForYou.com/
		Description: Social exchange plugin for WordPress
		Version: 2.6
		Author: Social Exchange Plugin team
		Author URI: http://www.MoreSharesForYou.com/
		License: GPL
	*/

	$sxpath =  plugin_dir_path(__FILE__ );
	global $sxpath;


	if (function_exists('sx')) {
		wp_die('More Shares For You - Basic Version cannot be activated. Please deactivate the PRO version first. <br /><br />Back to the WordPress <a href="'.get_admin_url(null, 'plugins.php').'">Plugins page</a>.');
	}
	add_option('sx_version','2.6');
	if (get_option('sx_active_campaigns') === FALSE)
		add_option('sx_active_campaigns',array());
	require_once "inc/config.php";
	require_once "inc/common.php";
	require_once "inc/render.php";

?>
