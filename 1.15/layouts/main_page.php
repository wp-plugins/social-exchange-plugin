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

function getPageLikes(){

			jQuery.ajax({

						url:ajaxurl,

						dataType:"json",

						data:{action:"get_page_likes",id:jQuery('.likes-result tr:first').attr('title')},

						success:function(r){

							if(r.status == 0){

								likeid = r.data;

							}

						}

				});

}

function getPostShares(){

			jQuery.ajax({

						url:ajaxurl,

						dataType:"json",

						data:{action:"get_post_shares",id:jQuery('.shares-result tr:first').attr('title')},

						success:function(r){

							if(r.status == 0){

								shareid = r.data;

							}

						}

				});

}

function confirmSingleFBL(){

	if(likeid == 0){

		showNextLike();

	}else{

					jQuery.ajax({

						url:ajaxurl,

						dataType:"json",

						data:{action:"confirm_page_like",id:likeid},

						success:function(r){

							if(r.status == 0){

								jQuery("#sxpoints").html(r.data);

							}

							showNextLike();

						}

				});

	}

}

function confirmSingleFBS(){

	if(shareid == 0){

		showNextShare();

	}else{

					jQuery.ajax({

						url:ajaxurl,

						dataType:"json",

						data:{action:"confirm_page_share",id:shareid},

						success:function(r){

							if(r.status == 0){

								jQuery("#sxpoints").html(r.data);

							}

							showNextShare();

						}

				});

	}

}

function showNextLike(){

			likeid = 0;

			var parent = jQuery(".likes-result");

			parent.fadeOut(200,function(){

				jQuery(this).html('');

				jQuery(".like-loader").fadeIn(300,function(){

						jQuery.ajax({

						url:ajaxurl,

						data:{action:"get_like"},

						success:function(r){

								jQuery(".like-loader").fadeOut(100,function(){

									jQuery(".likes-result").html(r).fadeIn(100);

								});

						}

				});

				})

			});

}

function showNextShare(){

			likeid = 0;

			var parent = jQuery(".shares-result");

			parent.fadeOut(200,function(){

				jQuery(this).html('');

				jQuery(".share-loader").fadeIn(300,function(){

						jQuery.ajax({

						url:ajaxurl,

						data:{action:"get_share"},

						success:function(r){

								jQuery(".share-loader").fadeOut(100,function(){

									jQuery(".shares-result").html(r).fadeIn(100);

								});

						}

				});

				})

			});

}

jQuery(document).ready(function(){

	jQuery(".skip-like").live("click",function(){

			var parent = jQuery(this).parent().parent();

			parent.fadeOut(200,function(){

				jQuery(this).remove();

				var id = parent.attr("title");

				jQuery(".like-loader").fadeIn(300,function(){

						jQuery.ajax({

						url:ajaxurl,

						data:{action:"skip_like",id:id},

						success:function(r){

								jQuery(".like-loader").fadeOut(100,function(){

									jQuery(".likes-result").html(r);

								});

						}

				});

				})

			});

		return false;

	})

	jQuery(".skip-share").live("click",function(){

			var parent = jQuery(this).parent().parent();

			parent.fadeOut(200,function(){

				var id = parent.attr("title");

				jQuery(".share-loader").fadeIn(300,function(){

						jQuery.ajax({

						url:ajaxurl,

						data:{action:"skip_share",id:id},

						success:function(r){

								jQuery(".share-loader").fadeOut(100,function(){

									jQuery(".shares-result").html(r);

								});

						}

				});

				})

			});

		return false;

	});

})

</script>

<div id="sx-container">

<?php

	if(@$_GET['settings-updated'] == 'true') {

?>

<div id="message" class="updated"><p>Settings updated</p></div>

<?php } ?>

<div class="sx-title">Social Exchange Panel</div>

	<form method="post" action="options.php">

	<?php settings_fields( 'sxdboptions'); ?>

	<?php

		$sxdb = get_option( 'sxdboptions' );

	?>

<fieldset class="sx_box"><legend>How does it work?</legend>

	<div class="sx-row">

		<div class="sx-left">

				Promote your content (Wordpress Posts) by assigning Points. <br/>

		</div>

	</div>

	<div class="sx-row">

		<div class="sx-left">

		You can assign points in Edit Post / Edit Page for each URL on your website.

		</div>

	</div>

</fieldset>

		<div class="sx-clear"> </div>

<fieldset class="sx_box"><legend>Points</legend>

	<div class="sx-row">

		<div class="sx-left">

				 <span id="sxpoints" style="font-size: 28px;font-weight: bold;"><?php echo $sxoptions['points'] ;?></span>

		</div>

	</div>

	<div class="sx-row">

		<div class="sx-left">

		<p>Gain More Points by Sharing posts and pages in the box below. <a href ="http://www.socialexchangeplugin.com/">More on How to get more points on www.socialexchangeplugin.com </a> </p>

		</div>

	</div>

</fieldset>

		<div class="sx-clear"> </div>


	<fieldset class="sx_box"><legend>Gain points by sharing other posts</legend>

	<div class="sx-box">

			<div class="sx-row">

				<table class="sx-table">

					<tbody class="shares-result">

					<?php foreach($artl as $al){ ?>

							<tr title=" <?php echo $al['link']; ?>"><td><b> <?php echo $al['name']; ?></b></td></tr>

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

	</div> </fieldset>

