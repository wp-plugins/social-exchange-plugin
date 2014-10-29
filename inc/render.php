<?php
	add_action( 'admin_menu', 'sx_add_menu_pages' );
	function sx_add_menu_pages(){
		add_menu_page(	"MoreSharesForYou ", "MoreSharesForYou", 'edit_theme_options', "social_exchange", "sx_main_page", plugins_url('social-exchange-plugin/images/MSFY_Favicon20x20.png') );
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
			$artl  = $datas['articles'];
		}

		// checkEmail API CALL
		$email = sx_get_response("checkEmail",array("site"=>get_site_url()));
		$subscribed = $email['data']['email'];
		if (!empty($subscribed)) {
			require_once $sxpath."/layouts/main_page.php";
			//Still show form
			if ($subscribed == 'no')
				require_once $sxpath."/layouts/optin_form.php";
		}
		else {
			require_once $sxpath."/layouts/optin_form.php";
		}


	}
	function get_sxdata(){
		$data = sx_get_response("getPagesArticles",array("site"=>get_site_url()));
		if($data['status'] == 0)
			return $data['data'];
		return false;
	}
	add_action( 'admin_init', 'sx_metabox', 1 );
	function sx_metabox(){
		add_meta_box( "socia-exchange-widget", "MoreSharesForYou", "sx_post_widget", "post", "side", "high" );
		add_meta_box( "socia-exchange-widget", "MoreSharesForYou", "sx_post_widget", "page", "side", "high" );
	}
	function sx_post_widget($post){
		global $sxpath;
		global $sxoptions;
		wp_nonce_field( plugin_basename( __FILE__ ), 'sx_noncename' );
		$likes = sx_get_post_likes($post->ID);
		wp_enqueue_style( 'sx-stylesheet', sx("plugin_url")."css/style.css" );

		//checkEmail API CALL
		$email = sx_get_response("checkEmail",array("site"=>get_site_url()));
		$subscribed = $email['data']['email'];
		if (empty($subscribed)) {
			include $sxpath."/layouts/optin_form_content.php";
			return;
		}
		?>
		<?php	if (($email['data']['email_confirmation']=='0') && ($email['data']['email']<>'no')) { ?>
		<span style="font-size:11px; font-style:italic; color:#FF0000">Confirm your email address to get the free points! </span>
		<?php ;} ?> <br />
		
		<p>Points available: <b> <?php echo $sxoptions['points']; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://moresharesforyou.com/get-more-points/" class="sx-get-more">How to get more points?</a></p>
		<input type="hidden" name="sx-post-active" id="sx-post-active" value="<?php echo $likes['active']; ?>"/> <input type="hidden"  name="sx-post-done" value="<?php echo intval($likes['done'])  ; ?>"  style="  width: 50px;" />
		Shares made:  <span id="sxsd_<?php echo $post->ID; ?>"><b><?php echo intval($likes['done'])  ; ?></b></span><br/>
		Maximum shares: <?php ?>
		<?php if($likes['active'] == 'no') : ?>
		<input type="text" name="sx-post-likes" value="<?php echo intval($likes['created'])  ; ?>"  style="  width: 50px;" />
		<?php else : ?>
		<b><?php echo intval($likes['created'])  ; ?></b>
		<input type="hidden"  name="sx-post-likes" value="<?php echo intval($likes['created'])  ; ?>"  style="  width: 50px;" />
		<?php endif; ?>
		</p>
		<p><div   id="sx-error-label"> </div></p>
		<p><div   id="sx-notice-label"> </div></p>
		<p>
		<?php if($sxoptions['premium'] == 'yes') { ?>
			<div class="sx-networks"><a class="sx-fb-btn"></a><a  class="sx-tw-btn-active" ></a><a  class="sx-gp-btn-active"></a><a  class="sx-ln-btn-active"></a><div class="clear"></div></div>
		<?php } else { ?>
			<div class="sx-networks"><a class="sx-fb-btn"></a><a  class="sx-tw-btn" ></a><a  class="sx-gp-btn"></a><a  class="sx-ln-btn"></a><div class="clear"></div> <a class="sx-unlock" href=" http://moresharesforyou.com/pro/">Unlock all networks</a></div>
		<?php } ?>

		<div class="campaign-wrapper">
		<a    <?php if($likes['active'] == 'no') : ?> title="Start the campaign" <?php endif; ?> class="campaign-btn post-page <?php if($likes['active'] == 'yes') : ?>sx-play-active<?php else: ?> sx-play-inactive<?php endif; ?>"  data-value="yes" value=""></a>
		<a  <?php if($likes['active'] == 'yes') : ?> title="Pause the campaign" <?php endif; ?> class=" post-page <?php if($likes['active'] == 'yes') : ?>sx-pause-inactive<?php else: ?> sx-pause-active<?php endif; ?> campaign-btn"  data-value="no" value="" ></a>

		<div class="sx-clear"></div>
		<?php if($likes['active'] == 'yes') : ?>
			Campaign is running
		<?php else: ?>
			Campaign is paused
		<?php endif; ?>
		</div><div class="sx-clear"></div></p>
		<p>
			<a href='<?php echo 'http://www.moresharesforyou.com/URL/'.get_permalink($post->ID); ?>' target="_blank"><div class="sx-stats-btn"></div>Check my Social stats</a>
		</p>
		<?php
	}
	function sx_get_post_likes($id){
		$settings = array("post"=>get_permalink($id),"site"=>get_site_url());
		$response = sx_get_response("getPostLikes",$settings);
		if(intval($response['status']) == 0)
			return array(
				"created"=>$response['data']['created'],
				"done"=>$response['data']['done'],
				"active"=>$response['data']['active']
			);
		  return array(
				"created"=>0,
				"done"=>0,
				"active"=>false
			);
	}
	function sx_get_home_likes(){
		$settings = array("post"=>get_site_url(),"site"=>get_site_url());

		$response = sx_get_response("getPostLikes",$settings);
		if(intval($response['status']) == 0)
			return array(
				"created"=>$response['data']['created'],
				"done"=>$response['data']['done'],
				"active"=>$response['data']['active']
			);
		  return array(
				"created"=>0,
				"done"=>0,
				"active"=>false
			);
	}

	$sxdone = 0;

	add_action( 'save_post', 'sx_save_likes',1,2 );
	function sx_save_likes( $id ) {
	global $sxdone;
    if ( 'post' == @$_POST['post_type'] || 'page' == @$_POST['post_type']) {
		if ( ! current_user_can( 'edit_page', $id ) )
        return;
	} else {
		if ( ! current_user_can( 'edit_post', $id ) )
			return;
	}
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return;
    if ( @$_POST['post_status'] != 'publish' )
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
    if ( strpos("http://localhost",get_site_url()) ===  0 )
		return;

	  $likes = intval($_POST['sx-post-likes']);
		if($sxdone ==0 && $_POST['sx-post-done']<=$likes) {
			$response = save_post_data($id,$likes,isset($_POST['sx-post-active']) ? $_POST['sx-post-active'] : 'no');;
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
add_action('wp_ajax_reload_data', 'reload_data_callback');
function reload_data_callback(){
			 $datas = get_sxdata();

			 $artl = array();
		if(!empty($datas)){
				$artl  = $datas['articles'];
		 }

		if(!empty($artl)){

					$str = '	<tr  id="sx-share-row"  title="'.$artl[0]['link'].'">
							<td><b>'.$artl[0]['name'].'</b></td> </tr>
						<tr>
							<td width="80%">'. $artl[0]['link'].'</td>
							<td width="10%">'.$artl[0]['html'].'
							</td>
							<td width="10%">
								<a href="#" class="skip-share">Skip</a>
							</td>
						</tr>';

		}else{

			$str = '		<tr><td width="100%">No article available!</td></tr>';

		}
		global $sxoptions;
		echo json_encode(array("next"=>$str,"points"=>$sxoptions['points']));
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
						<tr  id="sx-share-row" title="<?php echo $result['data']['articles'][0]['link']; ?>">
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
						<tr  id="sx-share-row"  title="<?php echo $result['data']['articles'][0]['link']; ?>">
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
add_filter('manage_posts_columns', 'sx_columns');
add_filter('manage_pages_columns', 'sx_columns');
function sx_columns($columns) {
   	$first = array_slice($columns,0,2,true);
	$last = array_slice($columns, 2,count($columns) - 2,true);

    return array_merge($first,array("sx_enable"=>"Enable  <br/>Campaign","sx_need"=>"Maximum <br/> shares","sx_done"=>"Done <br/> shares"),$last);
}
add_action('manage_posts_custom_column',  'sx_show_columns');
add_action('manage_pages_custom_column',  'sx_show_columns');

function sx_show_columns($name) {
    global $post;
    switch ($name) {
        case 'sx_enable':
            echo '<input type="hidden" class="sx-activate-campaign-list" value="'.$post->ID.'"/><div class="sx-cmp-btns" id="sxen_'.$post->ID.'" style="display:none"><a class="campaign-btn sx-pause-active" data-value="yes" value=""></a> <a class="campaign-btn sx-play-inactive" data-value="no" value=""></a></div> ';
		break;
        case 'sx_need':
            echo '<div id="sxlnw_'.$post->ID.'"><input type="text" class="sx-share-need-list sx-list-hide" id="sxsn_'.$post->ID.'"/></div><div id="sxlndw_'.$post->ID.'"></div>';
		break;
        case 'sx_done':
            echo '<span class="sx-list-hide" id="sxsd_'.$post->ID.'">0</span>';
		break;
    }
}
function sx_enqueue($hook) {
	global $post;

	wp_register_style( 'sx_wp_admin_css', sx("plugin_url") . 'css/admin.css', false, '1.0.0' );
	wp_enqueue_style( 'sx_wp_admin_css' );

	if($hook == 'edit.php'){
		if($post->post_type == 'post' || $post->post_type == 'page' ) {
			wp_enqueue_script( 'sx_custom_script_list', sx("plugin_url") . 'js/sxlist.js', array(), time() );
		}
	}
	if($hook == 'post.php'){
		if($post->post_type == 'post' || $post->post_type == 'page' ) {
			wp_enqueue_script( 'sx_optin', sx("plugin_url") . 'js/sxoptin.js' );
		}
	}
	if($hook == 'toplevel_page_social_exchange'){
		wp_enqueue_script( 'sx_custom_script_list', sx("plugin_url") . 'js/sxlist.js', array(), time() );
		wp_enqueue_script( 'sx_optin', sx("plugin_url") . 'js/sxoptin.js' );
	}
	wp_enqueue_script( 'sx_custom_script', sx("plugin_url") . 'js/sxscripts.js', array(), time() );
	wp_enqueue_script( 'sx_bind-first', sx("plugin_url") . 'js/bind-first.js' );
}
add_action( 'admin_enqueue_scripts', 'sx_enqueue' );
function sx_get_posts_data(){
	$urls = array();
	foreach($_POST['ids'] as $id){
			$urls[] = get_permalink($id);
	}
	$settings = array("posts"=>$urls,"site"=>get_site_url());
	$response = sx_get_response("getPostsLikes",$settings);

	$result = array();
	if(intval($response['status']) == 0)
			$result = $response['data'];

	foreach($result as $k=>$r){
			$result[$k]['id'] = url_to_postid($result[$k]['id']);
 	}
	echo json_encode($result);
	die();
}
add_action( 'wp_ajax_sx_get_posts_data', 'sx_get_posts_data' );

function sx_add_post_data(){

	if(intval($_POST['sn']) > 0)
		save_post_data($_POST['id'],$_POST['sn'],$_POST['activate'],$_POST['main']);
	die();

}
add_action( 'wp_ajax_sx_add_post_data', 'sx_add_post_data' );
function save_post_data($id,$sn,$activate,$main = "false"){
	$campaigns = get_option('sx_active_campaigns');
	if ($activate == 'yes') {
		$campaigns[] = $id;
	}
	else {
		$pos = array_search($id,$campaigns);
		unset($campaigns[$pos]);
	}
	update_option('sx_active_campaigns',$campaigns);
	if ($main == "false") {
		$settings = array("post"=>get_permalink($id),"site"=>get_site_url(),"likes"=>$sn,"link"=>get_permalink($id),"title"=>get_the_title($id),"active"=>$activate );
	}
	else {
		$settings = array("post"=>get_site_url(),"site"=>get_site_url(),"likes"=>$sn,"link"=>get_site_url(),"title"=>get_bloginfo('name'),"active"=>$activate );
	}
	return ( sx_get_response("addPostLikes",$settings));

}
add_action( 'admin_notices', 'sx_admin_notices' );
function sx_admin_notices() {
	global $sxoptions;
	$points = $sxoptions['points'];
	if ($points < 5) {
		$res = sx_get_response('getTotalShares',array('site'=>get_site_url()));
		$done = $res['data']['done'];
	?>
	<div class="update-nag" style="text-align:center">
	    <p><strong>MoreSharesForYou</strong>:Your pages have been shared <?php echo $done; ?> Times. <br/>You don't have enough points for MoreShares. Please <a href="<?php admin_url('admin.php?page=social_exchange'); ?>">share some pages</a> or <a href="http://moresharesforyou.com/get-more-points/">get some more points</a>.</p>
	</div>
	<?php
	}
	$campaigns = get_option('sx_active_campaigns');
	if (empty($campaigns)) {
	?>
	<div class="clear">
	<div class="update-nag">
	    <p><strong>MoreSharesForYou</strong>: Please start the campaign to have other people share your posts or pages</p>
	</div>
	<?php
	}
}

add_action( 'wp_ajax_sx_optin','sx_optin');
function sx_optin() {
	$email = $_POST['sx_ajax_email'];

	//Store Email value to remote server
	$res = sx_get_response('storeEmail',array('site'=>get_site_url(),'email'=>$email));

	die();
}

function sx_img_exists($url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if($code == 200){
	$status = true;
	}else{
	$status = false;
	}
	curl_close($ch);
	return $status;
}


?>
