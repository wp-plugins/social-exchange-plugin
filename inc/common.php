<?php
	
	register_activation_hook( __FILE__, 'activate_social_exchange' );
	$sxoptions = array(
		"active" =>false,
		"points" => 0 
	);
	function activate_social_exchange(){
		sxcheck();
	}
	function sxcheck(){
		$site = get_site_url();
		global $current_user;
		get_currentuserinfo();
		$email = $current_user->user_email;
		$method = "userCheck"; 
		$response = sx_get_response($method,array(
			"site"=>$site,
			"user"=>$email
		));
		add_option( "sxdboptions", array("sx-fb-page"=>""), '',false );
		global $sxoptions;
		 
		 
		if(is_array($response)){
			if(intval($response['status']) == 0){
					$sxoptions['active'] = true;
					$sxoptions['premium'] =  $response['data']['premium'];
					$sxoptions['points'] = $response['data']['points'];
			 		$sxoptions['likes'] = $response['data']['likes'];
					$sxoptions['likesdone'] = $response['data']['likesdone'];
			} 
		}  
	} 
	
	add_action( 'admin_init', 'sxcheck' );
	function sx_get_response($method,$args){
		$api_url = sx('api_url');
		$args = array_merge($args,array("test"=>"yes"));
		 
		$response = wp_remote_post($api_url.$method,array(
						'method' => 'POST',
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => array(),
						'body' => $args,
						'cookies' => array()
			));  
		if(is_wp_error($response))
			return array();
			
		return json_decode( $response['body'],true);
	}
	
?>
