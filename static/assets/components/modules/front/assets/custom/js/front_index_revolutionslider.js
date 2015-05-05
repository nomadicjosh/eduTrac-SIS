$(function()
{	
	var api;
	jQuery(document).ready(function() {
		api = jQuery('.fullwidthbanner').revolution(
			{
				delay:9000,
				startheight:500,
				startwidth:1170,

				hideThumbs:10,

				thumbWidth:100,							// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
				thumbHeight:50,
				thumbAmount:5,

				navigationType:"none",					//bullet, thumb, none, both		(No Thumbs In FullWidth Version !)
				navigationArrows:"verticalcentered",		//nexttobullets, verticalcentered, none
				navigationStyle:"square",				//round,square,navbar

				touchenabled:"on",						// Enable Swipe Function : on/off
				onHoverStop:"off",						// Stop Banner Timet at Hover on Slide on/off

				navOffsetHorizontal:0,
				navOffsetVertical:20,

				stopAtSlide:-1,
				stopAfterLoops:-1,

				shadow:0,								//0 = no Shadow, 1,2,3 = 3 Different Art of Shadows  (No Shadow in Fullwidth Version !)
				fullWidth:"on"							// Turns On or Off the Fullwidth Image Centering in FullWidth Modus
			});
	});

	function loadVideo(){
		jQuery("#video_link").html('<iframe id="video_frame" width="960" height="540" src="http://www.youtube.com/embed/t9N36YbFS4c?autoplay=1&fmt=22" frameborder="0" allowfullscreen style="max-width:100%;"></iframe>');
	}
});