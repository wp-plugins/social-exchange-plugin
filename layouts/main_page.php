<link rel="stylesheet" href="<?php echo sx("plugin_url"); ?>css/style.css" />
<?php
		wp_enqueue_script('jquery' );
?>
<script type="text/javascript">
var likeid = 0;
var shareid = 0;
function showLoader(){
	jQuery(".likes-result").html('');
	jQuery(".like-loader").fadeIn(200,function(){
	});
}
function showSoader(){
	jQuery(".shares-result").html('');
	jQuery(".share-loader").fadeIn(200,function(){
	});
}

function sxRefreshData(){
	var parent = jQuery("#sx-share-row");
			parent.fadeOut(200,function(){
				var id = parent.attr("title");
				jQuery(".share-loader").fadeIn(300,function(){
					jQuery.ajax({
						url:ajaxurl,
						data:{action:"reload_data"},
						dataType:"json",
						success:function(r){
							jQuery(".share-loader").fadeOut(100,function(){
								jQuery("#sxpoints").html(r.points);
								jQuery(".shares-result").html(r.next);
								jQuery(".skip-share").on("click",SkipShare);
							});

						}
					});
				});
			 }) ;
}
function SkipShare() {
	var parent = jQuery("#sx-share-row");
	parent.fadeOut(200,function(){
		var id = parent.attr("title");
		jQuery(".share-loader").fadeIn(300,function(){
				jQuery.ajax({
				url:ajaxurl,
				data:{action:"skip_share",id:id},
				success:function(r){
						jQuery(".share-loader").fadeOut(100,function(){
							jQuery(".shares-result").html(r);
							jQuery(".skip-share").on("click",SkipShare);
						});
				}
		});
		})
	});
	return false;
}
jQuery(document).ready(function(){

	jQuery(".skip-share").on("click",SkipShare);


})


</script>
<div id="sx-container">
<?php
	if(@$_GET['settings-updated'] == 'true') {
?>
<div id="message" class="updated"><p>Settings updated</p></div>
<?php } ?>
<div class="sx-title">MoreSharesForYou Panel</div>
	<form method="post" action="options.php">
	<?php settings_fields( 'sxdboptions'); ?>
	<?php
		$sxdb = get_option( 'sxdboptions' );
	?>
<fieldset class="sx_box"><legend>Plugin Info</legend>
		<div class="sx-left">
				MoreSharesForYou <strong>v<?php echo get_option('sx_version'); ?></strong><br/>
				Total shares made: <?php
					$res = sx_get_response('getTotalShares',array('site'=>get_site_url()));
					echo $res['data']['done']; ?><br/>
				Website: <a href="http://moresharesforyou.com">MoreSharesForYou</a><br/>
				email: <strong>hello@moresharesforyou.com</strong><br/>
		</div>
		<div class="sx-right">
				<?php
					if (sx_img_exists("http://moresharesforyou.com/panelbanner.jpg")) {
					    ?><a href="http://www.moresharesforyou.com/info"><img src="http://www.moresharesforyou.com/panelbanner.jpg" alt="" /></a><br/><?php
					}
				?>
		</div>
</fieldset>
		<div class="sx-clear"> </div>

<fieldset class="sx_box"><legend>Your Points</legend>
	<div class="sx-row">
		<div class="sx-left">
				 <span id="sxpoints" style="font-size: 28px;font-weight: bold;"><?php echo $sxoptions['points'] ;?></span>
		</div>
	</div>
	<div class="sx-row">
		<div class="sx-left">
		<p>Gain More Points by Sharing posts and pages in the box below. More on How to get more points on <a href ="http://moresharesforyou.com/get-more-points/">www.MoreSharesForYou.com</a> </p>
		</div>
	</div>
</fieldset>
		<div class="sx-clear"> </div>
<fieldset class="sx_box"><legend>Share my Homepage</legend>
	<div class="sx-box">
			<?php $likes = sx_get_home_likes(); ?>
			<table class="sx-table">
				<tbody>
					<tr>
					<td width ="25%"><?php echo get_site_url();?></td>
					<td width ="25%"><p>Shares made:  <span id="sxsd_0"><b><?php echo intval($likes['done'])  ; ?></b></span><br/>
						Maximum shares: <?php ?>
							<?php
								if($likes['active'] == 'no') {
									$display1="inline";
									$display2="none";
									$campaign="paused";
								}
								else {
									$display1="none";
									$display2="inline";
									$campaign="running";
								}

							?>
							<span id="sxlnw_0" style="display:<?php echo $display1; ?>">
								<input type="text" class="sx-share-need-list" name="sx-post-likes" value="<?php echo intval($likes['created'])  ; ?>"  style="  width: 50px;" />
							</span>
							<span id="sxlndw_0" style="display:<?php echo $display2; ?>">
								<?php echo intval($likes['created'])  ; ?>
							</span>
						 </p>
					</td>
					<td width ="25%"><div class="main-campaign-wrapper aligncenter" id="sxen_0">
							<input type="hidden" class="sx-activate-campaign-list" value="0" />
							<a    <?php if($likes['active'] == 'no') : ?> title="Start the campaign" <?php endif; ?> class="main-page campaign-btn <?php if($likes['active'] == 'yes') : ?>sx-play-active<?php else: ?> sx-play-inactive<?php endif; ?>"  data-value="yes" value=""></a>
						 <a  <?php if($likes['active'] == 'yes') : ?> title="Pause the campaign" <?php endif; ?> class="main-page <?php if($likes['active'] == 'yes') : ?>sx-pause-inactive<?php else: ?> sx-pause-active<?php endif; ?> campaign-btn"  data-value="no" value="" ></a>

						</div><div class="clear"></div>
						<span id="sxcmp">Campaign is <?php echo $campaign;?></span>
					<p><div   id="sx-error-label"> </div></p>
					 <p><div   id="sx-notice-label"> </div></p>
					</td>
					<td width ="25%"><p>
						 <?php if($sxoptions['premium'] == 'yes') { ?>
							<div class="sx-network"><a class="sx-fb-btn"></a><a  class="sx-tw-btn-active" ></a><a  class="sx-gp-btn-active"></a><a  class="sx-ln-btn-active"></a><div class="clear"></div></div>

						 <?php } else { ?>
							<div class="sx-network"><a class="sx-fb-btn"></a><a  class="sx-tw-btn" ></a><a  class="sx-gp-btn"></a><a  class="sx-ln-btn"></a><div class="clear"></div> <a class="sx-unlock" href=" http://moresharesforyou.com/pro/">Unlock all networks</a></div>

						<?php } ?>
					<br/><hr/>
					<p>
						<a href='<?php echo 'http://www.moresharesforyou.com/URL/'.get_bloginfo('url'); ?>' target="_blank"><div class="sx-stats-btn"></div>Check my Social stats</a>
					</p>
					</td>
				</tr>
			</tbody>
			</table>
	</div>
</fieldset>
<fieldset class="sx_box"><legend>Gain points by sharing other posts</legend>
	<div class="sx-box">
			<div class="sx-row">
				<table class="sx-table">
					<tbody class="shares-result">
					<?php foreach($artl as $al){ ?>
							<tr id="sx-share-row" title=" <?php echo $al['link']; ?>"><td><b> <?php echo $al['name']; ?></b></td></tr>
							<tr>
							<td width ="80%"> <?php echo $al['link']; ?></td>
							<td width ="10%"> <?php echo $al['html']; ?></td>
							<td width ="10%"> <a href="#" class="skip-share">Skip</td>
							</tr>
						<?php } ?>
			<?php if(empty($artl)) { ?>
				<tr>
					<td width="100%">No articles available!</td>
				</tr>
			<?php } ?>
					</tbody>
				</table>
				<div class="share-loader"></div>
			</div>
	</div>
</fieldset>


