jQuery(document).ready(function(){
	var ids = [];
	jQuery(".sx-activate-campaign-list ").each(function(){
		ids.push(jQuery(this).val());
		
	});
 
	jQuery.ajax({
		url:ajaxurl,
		data:{action:"sx_get_posts_data",ids:ids},
		type:"POST",
		dataType:"json",
		success:function(r){
				jQuery.each(r,function(k,v){
					jQuery("#sxsd_"+v.id).text(v.done);
					jQuery("#sxsn_"+v.id).val(v.likes);
					jQuery("#sxlndw_"+v.id).text(v.likes);
					if(v.active == 'yes'){
						var fclass = 'sx-pause-inactive';
						var sclass = 'sx-play-active';
						jQuery("#sxlnw_"+v.id).hide();
						jQuery("#sxlndw_"+v.id).show();
					} else{
						
						var fclass = 'sx-pause-active';
						var sclass = 'sx-play-inactive';
						jQuery("#sxlndw_"+v.id).hide();
						jQuery("#sxlnw_"+v.id).show();
					}
					jQuery("#sxen_"+v.id).html('<a    class="campaign-btn '+fclass+'" '+ftitle+'  data-value="yes" value=""></a> <a   class="campaign-btn '+sclass+'"   '+stitle+' data-value="no" value="" ></a>');
					
					
					
				});
				jQuery(".sx-list-hide").fadeIn();
		}
	});
	jQuery(".campaign-btn").live("click",function(){
		if(jQuery(this).hasClass("sx-play-active") || jQuery(this).hasClass("sx-pause-active") ) return false;
		var parent = jQuery(this).parent().parent().parent();
		 
		var sn = parent.find('.sx-share-need-list').val();
		var id = parent.find('.sx-activate-campaign-list').val();
		var activate ; 
		if(jQuery(this).hasClass('sx-play-inactive')){
		
			var activate = 'yes';
		}
		if(jQuery(this).hasClass('sx-pause-inactive')){
		
			var activate = 'no';
		}
		if(activate == 'no'){
			jQuery("#sxen_"+id).find(".sx-play-active").removeClass("sx-play-active").addClass("sx-play-inactive");
			jQuery("#sxen_"+id).find(".sx-pause-inactive").removeClass("sx-pause-inactive").addClass("sx-pause-active");
						jQuery("#sxlndw_"+id).hide();
		}else{
			
						jQuery("#sxlnw_"+id).hide();
						jQuery("#sxlndw_"+id).show();
			jQuery("#sxen_"+id).find(".sx-play-inactive").removeClass("sx-play-inactive").addClass("sx-play-active");
			jQuery("#sxen_"+id).find(".sx-pause-active").removeClass("sx-pause-active").addClass("sx-pause-inactive");
		}
		jQuery("#sxlndw_"+id).text(sn);
		parent.find(".sx-save-need-list").addClass("button-primary-disabled");
		var dn = parseInt(jQuery("#sxsd_"+id).text());
		if(dn>sn) return false
		jQuery.ajax({
				url:ajaxurl,
				data:{action:"sx_add_post_data",id:id,activate:activate,sn:sn},
				type:"POST",
				dataType:"json",
				success:function(r){
						 
				 setTimeout(function() { parent.find(".sx-save-need-list").removeClass("button-primary-disabled") },
				 100) ;
				}
			});	
		return false;
	})
})