// Created by ipbforumskins.com

var $i = jQuery.noConflict();

jQuery(document).ready(function($){

	$i('a[href=#top], a[href=#ipboard_body]').click(function(){
		$i('html, body').animate({scrollTop:0}, 400);
        return false;
	});
	
	$i(".forum_name").hover(function() {
		$i(this).next(".forum_desc_pos").children(".forum_desc_con").stop()
		.animate({left: "0", opacity:1}, "fast")
		.css("display","block")
	}, function() {
		$i(this).next(".forum_desc_pos").children(".forum_desc_con").stop()
		.animate({left: "10", opacity: 0}, "fast", function(){
			$i(this).hide();
		})
	});
	
	$i('#topicViewBasic').click(function(){
		$i(this).addClass("active");
		$i('#topicViewRegular').removeClass("active");
		$i("#customize_topic").addClass("basicTopicView");
		$.cookie('ctv','basic',{ expires: 365, path: '/'});
		return false;
	});
	
	$i('#topicViewRegular').click(function(){
		$i(this).addClass("active");
		$i('#topicViewBasic').removeClass("active");
		$i("#customize_topic").removeClass("basicTopicView");
		$.cookie('ctv',null,{ expires: -1, path: '/'});
		return false;
	});
	
	if ( ($.cookie('ctv') != null))	{
		$i("#customize_topic").addClass("basicTopicView");
		$i("#topicViewBasic").addClass("active");
	}
	else{
		$i("#topicViewRegular").addClass("active");
	}
	
	$i('.category_block .forumHover').each(function(){
		var forumURL = $(this).find(".col_c_forum h4 strong a").attr("href");
		$i(this).click(function(){
			window.location = forumURL;
			$(this).addClass("showLoading");
		})
	});
	
	$i("#nav_background").click(function(){
		$i("#toggle_background").slideToggle();
	})
	
});