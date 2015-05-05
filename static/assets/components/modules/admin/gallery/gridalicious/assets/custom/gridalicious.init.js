(function($)
{
	$('[data-toggle*="gridalicious"]').each(function()
	{
		var $that = $(this);
		$(this).removeClass('hide2').gridalicious(
		{
			gutter: $that.attr('data-gridalicious-gutter') || 13, 
			width: $that.attr('data-gridalicious-width') ? parseInt($that.attr('data-gridalicious-width')) : 200,
			animate: true,
			selector: '.widget'
		});
	});
})(jQuery);