/*
 * jQuery WizardPro Plugin v1.1
 * Copyright (c) 2010 Philo Hermans
 * http://www.philohermans.com
 * http://codecanyon.net/user/Philo01
 */
 
 (function($){

	$.fn.wizardPro = function(options) {

		if (this.length > 1){
        	this.each(function() { $(this).wizardPro(options) });
        	return this;
    	}
    	
    	var wizard = $(this),
    		ul = $(this).find('ul.steps'),
			li = $(this).find('ul.steps li');
		    	
		var defaults = {
			fadeInWizard: 500,
			autoSpaceSteps: true,
			stepArrows: true,
			defaultAjaxRequest: true,
			useValidation: true,
			formClass: '.defaultRequest',
			enableHelpers: true,
			helperArrows: true,
			userHoverIntent: true,
			interactionBlock: true,
			fadeInBlock: 500
		}; 

	   	var options = $.extend(defaults, options);
	   	      
    	this.intialize = function() {
    	    return this;
    	};
    	
		var setHelperHeight = function(){
			var content_height = $(wizard).find('.step_content').height();
			$(wizard).find(".helper").css("height", content_height).animate({"width":'hide'},500);

			if (options.interactionBlock && $.browser.msie && $.browser.version.substr(0,1)<7) {
				$(wizard).find('.step_content .blockuser').css("height", content_height+'px');
			}
			
		}
		
		$(this).find('.no_javascript').hide();
		$(this).removeClass('js');	
		
		if(options.fadeInWizard > 0){
			$(this).hide();
			$(this).fadeIn(options.fadeInWizard);
		}
		
		if(options.interactionBlock){
			$(wizard).find('.step_content').prepend('<div class="blockuser"><span></span></div>');
		}
		
		/**
		* Wizard Helpers
		* 
		* This part will contains the part of the helpers
		*/	
		if(options.enableHelpers){
			
			if(options.helperArrows){
				$(this).find('.helper').prepend('<span class="arrow"></span>');
			}
		
		    if(jQuery().hoverIntent && options.userHoverIntent){
				$(this).find('a.show_helper').hoverIntent({
				    over: openHelper, 
				    timeout: 1000, 
				    out: closeHelper,
				    interval: 150
				});
			}else{
				$(this).find('a.show_helper').mouseover(function(){
					openHelperAlt($(this));
				}).mouseout(function(){
					closeHelperAlt($(this));
				});
			}
		}	

		function openHelper(){ 			
			var position = $(this).position();
			$($(this).attr('href')).animate({width:'show'},500, function(){
				$('span.arrow').css("margin-top", "0").hide();
				$('span.arrow').animate({marginTop: (position.top - 7), opacity:'show'}, {queue:true, duration:800});
			});
		}
		
		function openHelperAlt(helper_id){ 			
			var position = $(helper_id).position();
			$($(helper_id).attr('href')).animate({width:'show'},500, function(){
				$('span.arrow').css("margin-top", "0").hide();
				$('span.arrow').animate({marginTop: (position.top - 7), opacity:'show'}, {queue:true, duration:800});
			});
		}
		
		function closeHelper(){ 
			$($(this).attr('href')).animate({"width":'hide'},500);
		}
		
		function closeHelperAlt(helper_id){ 
			$($(helper_id).attr('href')).animate({"width":'hide'},500);
		}
		
		/**
		* Auto Space 
		* 
		* This part will make sure the nav steps are auto spaced
		*/	

		if(options.autoSpaceSteps){
			// Auto space the steps and add arrows
			var parent_width = $(ul).width();
			var childs = 0;
			var child_count = $(li).size();
			var count = 1;
			
			$(ul).find('li:first-child').addClass('current');
			
			$(li).each(function(count){
				childs = childs + $(this).width();
				
				$(this).contents().wrap('<p></p>');
			
				if(options.stepArrows){
					if(child_count != (count+1)){
						$(this).append('<span></span>');
					}
				}
			});
			
			var difference = parent_width - childs;
			var add_padding = Math.floor(difference / child_count / 2);
			
			$(li).each(function(){
				$(this).css({
					'padding-right': add_padding,
					'padding-left': add_padding
				});
			});
		}
		
		// Hide all steps except step 1
		var firststep = $(this).find(".step_content div.step:first").addClass('current');
		$(this).find(".step_content div.step:first ~ div.step").hide();
		
		// Set helper height to match wizard
		setHelperHeight();
		
		// Open next step
		$(this).find('.next').click( function(){
			openStep('next');
		});
		
		// Open previous step
		$(this).find('.prev').click( function(){
			openStep('prev');
		});
		
		// Enable validation
		if(options.useValidation){
			$(this).find(options.formClass).validate();
		}
		
		/**
		* Default Ajax Process 
		* 
		* This part will handle the default ajax posts
		*/	
		/*if(options.defaultAjaxRequest){
			
			$(this).find(options.formClass).submit( function(){
				var requestUrl = $(this).attr('action');
				var form = $(this);
				
				if(options.useValidation){
				    var valid = $(form).valid();
				}else{
				    valid = true;
				}					
				
				if(valid){
					
					$(document).ajaxStart(function(){
					    if(options.interactionBlock){ interactionBlock(1); }
					}).ajaxStop(function(){
					    if(options.interactionBlock){ interactionBlock(0); }
					    setHelperHeight();	
					});
					
					$.ajax({
				  		type: 'POST',
				  		url: requestUrl,
				  		data: $(this).serialize(),
				  		dataType: 'json',
				  		success: function(data) {
				  				
				  					if(data.response){
				  						$('div.errormsg').remove();
				  						
				  						if(data.step){
				  							openStep(data.step);
				  						}else{
				  								openStep('next');
				  						}
				  					}else{
				  						$('div.errormsg').remove();
				  						$('<div class="errormsg">'+data.message+"</div>").insertBefore(form);
				  					}
				  				
				  		}
					});
				}
                setHelperHeight();	
				return false;
			});
			
		}*/

		/**
		* Block user interaction
		* 
		*
		* This function will prevent the user from interacting with the wizard
		*/			
		var interactionBlock = this.interactionBlock = function(action) {
			if(action == 1){
				$(wizard).find('.step_content .blockuser').fadeIn(options.fadeInBlock);
			}else if(action == 0){
				$(wizard).find('.step_content .blockuser').fadeOut(options.fadeInBlock);
			}
		}
		
		/**
		* Open step
		* 
		*
		* @param action.
		* This function will open the next, previous or number defined step
		*/	
			
		var openStep = this.openstep = function(action) {
			// Find current step
			var step = $(wizard).find('.step_content div.step.current');
			var total_steps = $(wizard).find('.step_content div.step').size();

			// Fadeout current step
			$(step).fadeOut(function(){
				$(step).removeClass('current');
				// Update step list
				$(ul).find('li').removeClass('completed');
				
				if(action == 'next'){
					$(this).next('.step').fadeIn().addClass('current');
					$(ul).find('li.current').removeClass('current').addClass('completed').next('li').addClass('current');
				}else if(action == 'prev'){
					$(this).prev('.step').fadeIn().addClass('current');
					$(ul).find('li.current').removeClass('current').prev('li').addClass('current').prev('li').addClass('completed');
				}else{
					
					$(step).parent().find('div.step:nth-child('+(action + 1)+')').fadeIn().addClass('current');
					//$(this).next('.step').fadeIn().addClass('current');
					$(ul).find('li.current').removeClass('current');
					$(ul).find('li:nth-child('+action+')').addClass('current');
					$(ul).find('li.current').prev('li').addClass("completed");
				}
				
				// Fix small gab when viewing last step
				if($(ul).find('li:last-child').hasClass('current')){
					var color = $(ul).find('li.current').css("background-color");
					$(ul).css("background-color", color);
				}else{
					var color = $(ul).find('li').css("background-color");
					$(ul).css("background-color", color);
				}
				
				// Set helper height to match wizard
				setHelperHeight();
				
			});	
		}
				
		
		return this.intialize();
					
	};
})(jQuery);