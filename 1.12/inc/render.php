<?php
	add_action( 'admin_menu', 'sx_add_menu_pages' );
	function sx_add_menu_pages(){
		add_menu_page(	"Social exchange ", "Social exchange", 'edit_theme_options', "social_exchange", "sx_main_page" );
	}
	function  sx_options_init(){
		register_setting( 'sxdboptions', 'sxdboptions',   'sx_save_options'  );
	}
	add_action( 'admin_init', 'sx_options_init' );
function sx_save_options($input){
		$options = get_option( 'sxdboptions' );
		if(isset($input['sx-fb-page'])){
			$input['sx-fb-page'] = trim($input['sx-fb-page']);
			if(!empty($input['sx-fb-page'])){
				$pagefb = sanitize_text_field($input['sx-fb-page']);
				$settings = array("fbpage"=>$pagefb,"site"=>get_site_url());
				$response = sx_get_response("updateUser",$settings);
				$input['sx-fb-page'] = intval($response['status']) == 0 ? $pagefb : @$options['sx-fb-page'] ;
			}
		}
		$newlikes = $input['sx-fb-likes'];
		$settings = array("likes"=>$newlikes,"site"=>get_site_url());
		$response = sx_get_response("addPageLikes",$settings);
		$input['sx-fb-likes'] = '' ;
		return $input;
}
	function sx_main_page(){
			 global $sxpath;
			 global $sxoptions;
			 $datas = get_sxdata();
			 $pagesl = array() ;
			 $artl = array();
			 if(!empty($datas)){
				$pagesl  = $datas['pages'];
				$artl  = $datas['articles'];
			 }
			 require_once $sxpath."/layouts/main_page.php";
	}
	function get_sxdata(){
			$data = sx_get_response("getPagesArticles",array("site"=>get_site_url()));
			if($data['status'] == 0)
				return $data['data'];
			return false;
	}
	add_action( 'admin_init', 'sx_metabox', 1 );
	function sx_metabox(){
		add_meta_box( "socia-exchange-widget", "Social Exchange", "sx_post_widget", "post", "side", "high" );
		add_meta_box( "socia-exchange-widget", "Social Exchange", "sx_post_widget", "page", "side", "high" );
	} 
	function sx_post_widget($post){
			 global $sxoptions;
				wp_nonce_field( plugin_basename( __FILE__ ), 'sx_noncename' );
				$likes = sx_get_post_likes($post->ID);
			 ?>
				<p>Points available: <b> <?php echo $sxoptions['points']; ?></b></p>
				<p> <b><u>Facebook shares </u></b></p>
				<p>Points pending to be spent: <b><?php echo intval($likes['created']) * 5; ?></b><br/>
				Points already spent:  <b><?php echo intval($likes['done']) * 5 ; ?></b><br/>
				Alocate new points: <input type="text" name="sx-post-likes" value="0"  style="
    width: 50px;" /> </p>
				 <input name="save" type="submit" class="button button-submit" id="publish" accesskey="p" value="Update">
				<p  style="text-align: right;"><a href="http://www.socialexchangeplugin.com/">How to get more points?</p>
			 <?php
	}
	function sx_get_post_likes($id){
		$settings = array("post"=>get_permalink($id),"site"=>get_site_url());
		$response = sx_get_response("getPostLikes",$settings);
		if(intval($response['status']) == 0)
			return array(
				"created"=>$response['data']['created'],
				"done"=>$response['data']['done']
			);
		  return array(
				"created"=>0,
				"done"=>0
			);
	}
	$sxdone = 0;
	add_action( 'save_post', 'sx_save_likes',1,2 );
	function sx_save_likes( $id ) {
    if ( 'post' == @$_POST['post_type'] || 'page' == @$_POST['post_type']) {
		if ( ! current_user_can( 'edit_page', $id ) )
        return;
	} else {
		if ( ! current_user_can( 'edit_post', $id ) )
			return;
	}
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return;
    if ( @$_POST['post_type'] == 'revision' )
        return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;
	if ( ! current_user_can( 'edit_post', $id ) )
			return;
	if ( false !== wp_is_post_revision( $id ) )
			return;
    if ( ! isset( $_POST['sx_noncename'] ) || ! wp_verify_nonce( $_POST['sx_noncename'], plugin_basename( __FILE__ ) ) )
      return;
	  $likes = intval($_POST['sx-post-likes']);
	  if($likes > 0 &&  $sxdone == 0){
			$settings = array("post"=>get_permalink($id),"site"=>get_site_url(),"likes"=>$likes,"link"=>get_permalink($id),"title"=>get_the_title($id));
			$response = sx_get_response("addPostLikes",$settings);
			$sxdone = 1;
	  }
	}
add_action('wp_ajax_update_page_likes', 'update_page_likes_callback');
function update_page_likes_callback() {
	 $page = $_GET['page'];
	 $result = sx_get_response("addPageLike",
		array(
			"site"=>get_site_url(),
			"page"=>$page
		)
	 );
	 die();
}
add_action('wp_ajax_skip_like', 'skip_like_callback');
function skip_like_callback() {
	 $page = $_GET['id'];
	 $result = sx_get_response("skipPage",
		array(
			"site"=>get_site_url(),
			"page"=>$page
		)
	 );
		if($result['status'] == 0 && !empty($result['data']['pages'])){
			?>
						<tr title="<?php echo $result['data']['pages'][0]['name']; ?>" >
								<td width="80%"><?php echo $result['data']['pages'][0]['name']; ?></td>
								<td width="10%"><?php echo $result['data']['pages'][0]['html']; ?></td>
								<td width="10%">
									<a href="#" class="skip-like">Skip</a>
								</td>
					</tr>
			<?php
		}else{
			?>	<tr>
					<td width="100%">No page available!</td>
				</tr>
			<?php
		}
	 die();
}
add_action('wp_ajax_get_like', 'get_like_callback');
function get_like_callback() {
	 $result = sx_get_response("getPagesArticles",
		array(
			"site"=>get_site_url()
		)
	 );
		if($result['status'] == 0 && !empty($result['data']['pages'])){
			?>
						<tr title="<?php echo $result['data']['pages'][0]['name']; ?>" >
								<td width="80%"><?php echo $result['data']['pages'][0]['name']; ?></td>
								<td width="10%"><?php echo $result['data']['pages'][0]['html']; ?></td>
								<td width="10%">
									<a href="#" class="skip-like">Skip</a>
								</td>
					</tr>
			<?php
		}else{
			?>	<tr>
					<td width="100%">No page available!</td>
				</tr>
			<?php
		}
	 die();
}
add_action('wp_ajax_get_share', 'get_share_callback');
function get_share_callback() {
	 $result = sx_get_response("getPagesArticles",
		array(
			"site"=>get_site_url()
		)
	 );
		if($result['status'] == 0 && !empty($result['data']['articles'])){
			?>
						<tr   title="<?php echo $result['data']['articles'][0]['link']; ?>">
							<td><b><?php echo $result['data']['articles'][0]['name']; ?></b></td> </tr>
							<tr>
							<td width="80%"><?php echo $result['data']['articles'][0]['link']; ?></td>
							<td width="10%"> <?php echo $result['data']['articles'][0]['html']; ?>
							</td>
							<td width="10%">
								<a href="#" class="skip-share">Skip</a>
							</td>
						</tr>
			<?php
		}else{
			?>	<tr>
					<td width="100%">No page available!</td>
				</tr>
			<?php
		}
	 die();
}
add_action('wp_ajax_skip_share', 'skip_share_callback');
function skip_share_callback() {
	 $page = $_GET['id'];
	 $result = sx_get_response("skipArticle",
		array(
			"site"=>get_site_url(),
			"page"=>$page
		)
	 );
		if($result['status'] == 0 && !empty($result['data']['articles'])){
			?>
						<tr   title="<?php echo $result['data']['articles'][0]['link']; ?>">
							<td><b><?php echo $result['data']['articles'][0]['name']; ?></b></td> </tr>
						<tr>
							<td width="80%"><?php echo $result['data']['articles'][0]['link']; ?></td>
							<td width="10%"> <?php echo $result['data']['articles'][0]['html']; ?>
							</td>
							<td width="10%">
								<a href="#" class="skip-share">Skip</a>
							</td>
						</tr>
			<?php
		}else{
			?>
				<tr><td width="100%">No article available!</td></tr>
			<?php
		}
	 die();
}
add_action('wp_ajax_get_page_likes', 'get_page_likes_callback');
function get_page_likes_callback() {
	 $result = sx_get_response("FBpageLikes",
		array(
			"site"=>get_site_url(),
			"page"=>trim($_GET['id'])
		)
	 );
	 echo json_encode($result);
	 die();
}
add_action('wp_ajax_get_post_shares', 'get_post_shares_callback');
function get_post_shares_callback() {
	 $result = sx_get_response("FBpostShares",
		array(
			"site"=>get_site_url(),
			"page"=>trim($_GET['id'])
		)
	 );
	 echo json_encode($result);
	 die();
}
add_action('wp_ajax_confirm_page_like', 'confirm_page_like_callback');
function confirm_page_like_callback() {
	 $result = sx_get_response("confirmPageLike",
		array(
			"site"=>get_site_url(),
			"likeid"=>trim($_GET['id'])
		)
	 );
	 echo json_encode($result);
	 die();
}
add_action('wp_ajax_confirm_page_share', 'confirm_page_share_callback');
function confirm_page_share_callback() {
	 $result = sx_get_response("confirmPageShare",
		array(
			"site"=>get_site_url(),
			"shareid"=>trim($_GET['id'])
		)
	 );
	 echo json_encode($result);
	 die();
}
?>