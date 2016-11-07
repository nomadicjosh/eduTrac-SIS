/* Remove Envato Frame */
if (window.location != window.parent.location)
	top.location.href = document.location.href;

(function($, window)
{
	// fix for safari back button issue
	window.onunload = function(){};

	window.beautify = function (source)
	{
		var output,
			opts = {};

	 	opts.preserve_newlines = false;
	 	// opts.jslint_happy = true;
		output = html_beautify(source, opts);
	    return output;
	}

	// generate a random number within a range (PHP's mt_rand JavaScript implementation)
	window.mt_rand = function (min, max) 
	{
		var argc = arguments.length;
		if (argc === 0) {
			min = 0;
			max = 2147483647;
		}
		else if (argc === 1) {
			throw new Error('Warning: mt_rand() expects exactly 2 parameters, 1 given');
		}
		else {
			min = parseInt(min, 10);
			max = parseInt(max, 10);
		}
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	// scroll to element animation
	function scrollTo(id)
	{
		if ($(id).length)
			$('html,body').animate({scrollTop: $(id).offset().top},'slow');
	}

	// handle menu toggle button action
	function toggleMenuHidden()
	{
		var c = $('.container-fluid');
		c.toggleClass('menu-hidden');

		if (c.is('.menu-hidden'))
			$('#menu').addClass('hidden-xs');
		else
			$('#menu').removeClass('hidden-xs');
	}

	// main menu visibility toggle
	$('.navbar.main .btn-navbar').click(function(){
		toggleMenuHidden();
	});

	$(window).setBreakpoints({
		distinct: false,
		breakpoints: [ 768 ]
	});

	$(window).bind('exitBreakpoint768',function() {		
		$('.container-fluid').addClass('menu-hidden');
	});

	$(window).bind('enterBreakpoint768',function() {
		$('.container-fluid').removeClass('menu-hidden');
	});

	if (typeof Holder != 'undefined')
	{
		Holder.add_theme("dark", {background:"#45484d", foreground:"#aaa", size:9}).run();
		Holder.add_theme("white", {background:"#fff", foreground:"#c9c9c9", size:9}).run();
		if (typeof primaryColor != 'undefined') Holder.add_theme("primary", {background:primaryColor, foreground:"#c9c9c9", size:9}).run();
	}
	
	// multi-level top menu
	$('body').on('mouseover', '.submenu', function()
	{
        $(this).children('ul').removeClass('submenu-hide').addClass('submenu-show');
    }).on('mouseout', '.submenu', function()
    {
    	$(this).children('ul').removeClass('.submenu-show').addClass('submenu-hide');
    });
	
	// tooltips
	$('body').tooltip({ selector: '[data-toggle="tooltip"]' });
	
	// popovers
	$('[data-toggle="popover"]').popover();
	
	// loading state for buttons
	$('[data-toggle*="btn-loading"]').click(function () {
        var btn = $(this);
        btn.button('loading');
        setTimeout(function () {
        	btn.button('reset')
        }, 3000);
    });
	$('[data-toggle*="button-loading"]').click(function () {
        var btn = $(this);
        btn.button('loading');
    });
	
	// print
	$('[data-toggle="print"]').click(function(e)
	{
		e.preventDefault();
		window.print();
	});
	
	// typeahead
	if ($('[data-toggle="screen"]').length)
		$('[data-toggle="screen"]').typeahead({
			source: ["SYSS - System Settings","MPRM - Manage Perms","APRM - Add Permission","MRLE - Manage Roles",
			"AUDT - Audit Trail","SQL - SQL Terminal","ARLE - Add Role","SCH - School Form",
			"SEM - Semester Form","TERM - Term Form","AYR - Acad Year Form","CRSE - Course",
			"DEPT - Department Form","CRL - Credit Load Form","DEG - Degree Form","MAJR - Major Form",
			"MINR - Minor Form","PROG - Program","CCD - CCD Form","CIP - CIP Form","LOC - Location Form",
			"BLDG - Building Form","ROOM - Room Form","SPEC - Specialization Form","SUBJ - Subject Form",
			"CLYR - Class Year Form","APRG - Add Acad Program","ACRS - Add Course","SECT - Course Section",
			"RGN - Course Registration","NSCP - NSLC Purge","NSCS - NSLC Setup","NSCX - NSLC Extraction",
			"NSCE - NSLC Verification","NSCC - NSLC Correction","NSCT - NSLC File","NAE - Name & Address",
			"APER - Add Person","SPRO - Student Profile","FAC - Faculty Profile",
			"TRAN - Generate Transcript","SLR - Student Load Rules","RSTR - Restriction Codes",
			"GRSC - Grade Scale","BRGN - Batch Registration","SROS - Student Roster","STAF - Staff",
			"EXTR - External Course","ATCEQ - New Transfer Course Equivalency",
			"TCEQ - Transfer Course Equivalency","TCRE - Transfer Credit", "RLDE - Rule Definition",
                        "ACLV - Academic Level"],
			items: 6,
			minLength: 2		
		});
	
	// carousels
	$('.carousel').carousel();
	
	// Google Code Prettify
	if ($('.prettyprint').length && typeof prettyPrint != 'undefined')
		prettyPrint();

	if ($(window).width() <= 768)
		$('.container-fluid').addClass('menu-hidden');
	
	// menu slim scroll max height
	setTimeout(function()
	{
		var menu_max_height = parseInt($('#menu .slim-scroll').attr('data-scroll-height'));
		var menu_real_max_height = parseInt($('#wrapper').height());
		
		$('#menu .slim-scroll').slimScroll({
			height: (menu_max_height < menu_real_max_height ? (menu_real_max_height - 40) : menu_max_height) + "px",
			allowPageScroll : true,
			railDraggable: ($.fn.draggable ? true : false)
	    });
		
		if (Modernizr.touch)
			return; 
		
		// fixes weird bug when page loads and mouse over the sidebar (can't scroll)
		$('#menu .slim-scroll').trigger('mouseenter').trigger('mouseleave');
	}, 200);
})(jQuery, window);