$(function()
{

	$('#choose-preview .switch-box').on('click', function(e){
		e.preventDefault();
		var p = $(this).parent('.box');
		$('#choose-preview .box').not(p).removeClass('active');
		p.addClass('active');
	});
	$('#template-options .close2').on('click', function(){
		$('#template-options').collapse('hide');
	});

	var url_default = [];
		url_default['admin'] = $('#choose-preview .options[data-for="admin"] .actions a').attr('href');
		url_default['front'] = $('#choose-preview .options[data-for="front"] .actions a').attr('href');
	
	$('#choose-preview')
		.find('.options select')
		.on('change', function()
		{
			var box = $(this).parents('.box:first');
			var $for = $(this).attr('data-for');
			var select_layout = box.find('select[data-type="layout"]');
			var select_sidebar = box.find('select[data-type="sidebar"]');
			var select_sidebar_position = box.find('select[data-type="menu"]');
			var select_sidebar_sticky = box.find('select[data-type="sidebar-sticky"]');
			var select_top_sticky = box.find('select[data-type="top-sticky"]');
			var select_rtl = box.find('select[data-type="rtl"]');
			var select_style = box.find('select[data-type="style"]');
			var select_top_style = box.find('select[data-type="top-style"]');
			var select_sidebar_style = box.find('select[data-type="sidebar-style"]');
			var select_skin = box.find('select[data-type="skin"]');
			var select_ajax = box.find('select[data-type="ajax"]');
			
			var url = url_default[$for];
			
			if (select_layout.length)
				url += '&layout_type=' + select_layout.val();
			if (select_sidebar.length)
				url += '&sidebar=' + select_sidebar.val();
			if (select_sidebar_position.length)
				url += '&menu_position=' + select_sidebar_position.val();
			if (select_sidebar_sticky.length) 
				url += '&sidebar-sticky=' + select_sidebar_sticky.val();
			if (select_top_sticky.length) 
				url += '&top-sticky=' + select_top_sticky.val();
			if (select_rtl.length) 
				url += '&rtl=' + select_rtl.val();
			if (select_style.length) 
				url += '&style=' + select_style.val();
			if (select_top_style.length) 
				url += '&top_style=' + select_top_style.val();
			if (select_sidebar_style.length) 
				url += '&sidebar_style=' + select_sidebar_style.val();
			if (select_skin.length) 
				url += '&skin_custom=' + select_skin.val();
			if (select_ajax.length) 
				url += '&ajaxify=' + select_ajax.val();
			
			$('#choose-preview .options[data-for="'+$for+'"] .actions a').attr('href', url);
			location = url;
		});
});