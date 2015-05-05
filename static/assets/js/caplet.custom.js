    $(function() {
			   
		//////////     MOBILE CHECK    //////////   
		var iOS = /(iPad|iPhone|iPod)/g.test( navigator.userAgent );
		var android = /mobile|android/i.test (navigator.userAgent);
		
		if(iOS || android){
			$("html").addClass("isMobile");
			if(iOS) { $(".form-control").css("-webkit-appearance","caret"); }
			$("select.input-sm,select.input-lg ").css("line-height","1.3");
		 }
		 
		 
		//////////     CAPLET COLOR    //////////
		var cepletColor=({ 
			"primary":"#0090d9",
			"info":"#B5D1D8",
			"success":"#2ECC71",
			"Approved":"#2ECC71",
			"warning":"#FFCC33",
			"Pending":"#FFCC33",
			"danger":"#E15258",
			"Rejected":"#E15258",
			"inverse":"#62707D",
			"theme":"#f35958",
			"theme-inverse":"#0aa699",
			"palevioletred":"#372b32" ,
			"green":"#99CC00",
			"lightseagreen":"#1ABC9B",
			"purple":"#736086",
			"darkorange":"#f9ba46",
			"pink":"#d13a7a"
		});
		$.inColor= function(value, obj) {
			var foundVal;
				$.each(obj, function(key, val) {
					if (value === key) {
						foundVal =  val;
						return;
					}
				});
			return foundVal;
		};
		$.fillColor= function(obj) {
			var inColor=$.inColor(obj.data("color") || obj.data("toolscolor") || obj.data("counter-color") , cepletColor);
			var codeColor= inColor || obj.data("color") || obj.data("toolscolor") || obj.data("counter-color") ;
			return codeColor;
		};
		$.rgbaColor=function( hex, opacity) {
		    var bigint = parseInt(hex.replace("#",""), 16),
				r = (bigint >> 16) & 255,
				g = (bigint >> 8) & 255,
				b = bigint & 255;
				if(opacity || opacity<=1){
		    			return "rgba("+r + "," + g + "," + b+","+ ( opacity || 1 )+")";
				}else{
					return "rgb("+r + "," + g + "," + b+")";
				}
		}
		
		var capletOverlay=$('<div id="caplet-overlay">');
		$.overlay=function(opacity){
			$("#wrapper").append(capletOverlay);
			capletOverlay.fadeTo( "slow" , opacity || 0.2 );
		}
		$.clearOverlay=function(){
			capletOverlay.fadeTo( "slow" , 0 ,function(){
				capletOverlay.remove();
			});
		}
		
		
		//////////     TEXTAREA  AUTO SIZE    //////////
		$('textarea[data-height="auto"]').autosize();
		 $(".widget-write-post textarea").limit({  limit: 20 });
		 
		//////////     SELETE PICKER    //////////
		$('.selectpicker').selectpicker();
		
		//////////     INPUT MAXLENGTH    //////////
		$("input[maxlength] , textarea[maxlength] ").each(function() {
			$(this).maxlength({ bornIn:"#main" }); 
		});
		
		//////////     PRETTY PRINT CODE    //////////
		addEventListener('load', function (event) { prettyPrint() }, false);
		
		
		//////////     TAB DROP    //////////
		$('[data-provide="tabdrop"]').tabdrop();
		
		//////////     TAB NEXT    //////////
		var $tabs = $('.tabbable li');
		$('#prevtab').on('click', function() {
			$tabs.filter('.active').prev('li').find('a[data-toggle="tab"]').tab('show');
		});
		$('#nexttab').on('click', function() {
			$tabs.filter('.active').next('li').find('a[data-toggle="tab"]').tab('show');
		});
		
		
		$('#flot_view a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		})


		//////////     TOP SEARCH     //////////
		$(".btn-header-search , .close-header-search").on('click',function(){
				var navSearch=$(".widget-top-search");
				navSearch.toggleClass("nav-top-search");
				if($(this).hasClass("close-header-search")){
					$.clearOverlay();
					return true;
				}
				navSearch.find("input").focus();
				$.overlay(0.4);
		});
		 
		 
		//////////     NAV AND MAIN  COLLAPSE     //////////
		 $("a.nav-collapse").on('click',function(){
				var main=$("#nav,#main");
					main.toggleClass( "nav-collapse-out" );
					if(!main.hasClass("nav-collapse-out")){
						main.addClass("none");
					}
					setTimeout(function () { main.removeClass("none") }, 600);
		});
		 
		 
		 
		//////////     MAIL  COLLAPSE     //////////
		 $(".mail-collapse").on('click',function(){
				$("#main").toggleClass( "in" );  							
		});
		//////////     MAIL FAVOURITE      //////////
		$("a.mail-favourite").click(function(){
			$(this).toggleClass( "active" );
			var star=$(this).find(".fa");
		});
		 
		 
		 
		//////////     CHAT  COLLAPSE     //////////
		$('.widget-chat .chat-collapse').on('click', function () {
            var btn=$(this), chatBody=btn.closest(".widget-chat").find(".chat-body");
			btn.find("i").toggleClass( "fa-minus" );
			btn.find("i").toggleClass( "fa-plus" );
			chatBody.slideToggle(500);
        });	



		//////////     FULLSCREEN  TOGGLE     //////////
		$('.fullscreen').click(function() {
			toggleFullScreen();
			$(this).find("i").toggleClass( "fa-expand" );
			$(this).find("i").toggleClass( "fa-compress" );
		});
		function toggleFullScreen() {
			if ((document.fullScreenElement && document.fullScreenElement !== null) || (!document.mozFullScreen && !document.webkitIsFullScreen)) {
				if (document.documentElement.requestFullScreen) {
					document.documentElement.requestFullScreen();
				} else if (document.documentElement.mozRequestFullScreen) {
					document.documentElement.mozRequestFullScreen();
				} else if (document.documentElement.webkitRequestFullScreen) {
					document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
				}
			} else {
				if (document.cancelFullScreen) {
					document.cancelFullScreen();
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else if (document.webkitCancelFullScreen) {
					document.webkitCancelFullScreen();
				}
			}
		}
		
		
			
		//////////     DATA  COLOR  CHANGE    //////////
		$('label[data-color]').each(function(i) {
			var label=$(this);
			label.css("border-top-color", $.fillColor(label) );
		});
		$('.label[data-color] ,  .badge[data-color] ').each(function(i) {
			var label=$(this);
			label.css("background-color", $.fillColor(label) );
		});
		$('.alert[data-color]').each(function(i) {
			var alerts=$(this);
			alerts.css({ "background-color":$.fillColor(alerts),"color":"#FFF" });
		});
		$('.panel-heading[data-color]').each(function(){
			var heading=$(this);
			headingColor=$.fillColor(heading) ;
			heading.css("background-color", headingColor );	
			heading.find("h2").css("color","#FFF");
			heading.find("label").css({ "background-color":$.xcolor.darken( headingColor , 1 , 13),"color":"#FFF"});
		});
		$('.progress-bar[data-color]').each(function(i) {
			var progress=$(this);
			progress.css("background-color", $.fillColor(progress) );
		});
		$('.well[data-color]').each(function(i) {
			var well=$(this);
			well.css({"background-color":$.fillColor(well),"color": well.data("fcolor") || "#FFF" });
		});
		$('.btn[data-color]').each(function(i) {
			var btn=$(this);
			btn.css({"background-color":$.fillColor(btn),"border-color": $.fillColor(btn) ,"color": btn.data("fcolor") || "#FFF" });
		});
		$('.avatar-link[data-color]').each(function(i) {
			var elLink=$(this), btn=elLink.find(".btn"),
			elLinkColor=$.fillColor(elLink);
				btn.each(function(i) {
					$(this).css({ "background-color": elLinkColor , "border-left-color": $.xcolor.darken( elLinkColor , 1 , 13) });
				});		
		});
		$('.widget-im .im-thumbnail[data-color]').each(function(i) {
			var thumbnail=$(this),
			thumbnailColor=$.fillColor(thumbnail);
			thumbnail.css({ "background-color": thumbnailColor , "border-color": thumbnailColor });
			thumbnail.find("i").css({"color": thumbnail.data("btn-fcolor") || $.xcolor.darken( thumbnailColor , 3 , 13) });
		});
		$('[data-btn-group="monochromatic"]').each(function(i) {
			var group=$(this),fontColor, btn=$(this).find(".btn"),
			color=$.inColor($(this).data("btn-color"), cepletColor);
			btn.each(function(i) {
				if(group.data("btn-step")==="lighten"){
					btncolor=$.xcolor.lighten( color || group.data("btn-color") || "#CCC", i == 0 ? i :  i+1 , group.data("btn-color-step") || 9);
				}else{
					btncolor= $.xcolor.darken( color || group.data("btn-color") || "#AAA",  i == 0 ? i :  i+1  ,group.data("btn-color-step") || 13) ;
				}
				$(this).css({"background-color": btncolor,"border-color":btncolor ,"color": group.data("btn-fcolor") || "#FFF" });
	
			});							   
		});
		$('.modal[data-header-color]').each(function(i) {
			var modal=$(this), header=$(this).find(".modal-header"),
			color=$.inColor($(this).data("header-color"), cepletColor);
				headerColor= color || modal.data("header-color") || "#FFF";
				borderColor= $.xcolor.darken( headerColor , 1 , 13) ;
				header.css({"background-color": headerColor ,"border-bottom-width": "5px" ,"border-color":borderColor ,"color": modal.data("header-fcolor") || "#FFF" });
		});
		
		
		
		
		//////////     WIDGET IM      //////////
		$("a.im-delete , a.im-action").click(function(){
				imClose($(this),$(this).closest('section'));
				$(this).closest('section').toggleClass( "push-in" );
		});
		$("a.im-confirm").click(function(){
				var aClick=$(this), confirm=aClick.data("confirm"), parents=aClick.closest('li');		
				var imWrapper=aClick.closest("ul");
				var confirmClose=function(){
						parents.fadeOut(400,function(){
							$(this).remove();
							setTimeout(function () { imClose(aClick,imWrapper) }, 200); 
						});
					}
				if(confirm=="yes" || confirm=="accept"){
					confirmClose();
				}
				if(confirm=="actionNow"){
					alert("Your Action Complete !!")
					confirmClose();
				}
				if(confirm=="no" ){
					parents.find("section").toggleClass( "push-in" );
				}
		});
		var imClose=(function (el , wrapper) {
				if(el.hasClass("im-delete") || el.hasClass("im-action") ){
					el.parents('ul').find('li section').not(wrapper).removeClass("push-in");
				}
				if(el.hasClass("im-confirm")){
					var  nLi=wrapper.find('li').length;
					if(!nLi){
						wrapper.append('<li class="empty"><i class="fa fa-inbox fa-4x"></i><h5>No Result.</h5></li>');
					}
				}
		});

		
		
		
		//////////     MAP GOOGLE      //////////
		$(".mapTools").click(function(){
			$("#mapSetting").toggleClass( "active" );
		});
		$(".map-control-min").click(function(){
			$("#mapControl").toggleClass( "active" );
			$(this).find("i").toggleClass( "fa-chevron-down" );
			$(this).find("i").toggleClass( "fa-chevron-up" );
		});
		//Map Collapse
		$('a[data-toggle="collapse"]').click(function(){
				var caret=$(this).find(".collapse-caret");
				caret.toggleClass( "fa-angle-down" );
				caret.toggleClass( "fa-angle-up" );
		}); 
		
		$(".slide-trash").click(function(){
			$("#slide-trash").toggleClass( "active" );
		});
		
		

		//////////     PROGRESS BAR      //////////
		$('.progress-tooltip .progress-bar').progressbar({ display_text: 'tooltip' });
		$('.progress-bar').progressbar();
		
		
		//////////     TOOLTIP AND POPOVER     //////////
		$('.tooltip-area .btn').tooltip();
		$('.tooltip-area a[data-toggle="tooltip"]').tooltip();
		$('.popover-area [data-toggle="popover"]').popover();
		$('.popover-area-hover [data-toggle="popover"]').popover({ trigger:"hover" });
		
		

		//////////     OFF CANVAS MENU      //////////
		// Menu right
		$('nav#menu-right').mmenu({
			position	: 'right',
			counters	: true,
			searchfield	: true,
			header		: {
				//add			: true,
				//update		: true,
				//title		: 'Contacts' 
			}
		});
		// Menu Left
		$('nav#menu').mmenu({
			searchfield   :  true,	
			slidingSubmenus	: true
		}).on( "closing.mm", function(){
			var highest=$(this).find("ul.mm-highest");
			highest.find(".mm-subclose").trigger('click');
			setTimeout(function () { closeSub() }, 200);
			function closeSub(){
				var nav=$('nav#menu');
				if(nav.hasClass("mm-vertical")){
					nav.find("li").each(function(i) {
						$(this).removeClass("mm-opened");	
					});
				}else{
					nav.find("ul").each(function(i) {
						if(i==0){
							$(this).removeClass("mm-subopened , mm-hidden").addClass("mm-current");	
						}else{
							$(this).removeClass("mm-opened , mm-subopened , mm-current  , mm-highest").addClass("mm-hidden");						
						}	
					});
				}
			}
		  });
		
		//////////     TOGGLE  OPEN LEFT CANVAS MENU      //////////
		$("body").append('<div class="toggle-menu"/>');
		$('body').on("click",".toggle-menu",function( e ) {
				e.stopImmediatePropagation();
				e.preventDefault();
				$('nav#menu').trigger( 'open.mm' );
		});
		
		
		//////////     TOUCH TO OPEN CANVAS MENU      //////////
		var nav=document.getElementById("nav");
		if(nav){
			var wrapper= Hammer( nav );
			 wrapper.on("dragright", function(event) {	// hold , tap, doubletap ,dragright ,swipe, swipeup, swipedown, swipeleft, swiperight
				if((event.gesture.deltaY<=7 && event.gesture.deltaY>=-7) && event.gesture.deltaX >100){
					$('nav#menu').trigger( 'open.mm' );
				}
			 });
			 wrapper.on("dragleft", function(event) {
				if((event.gesture.deltaY<=5 && event.gesture.deltaY>=-5) && event.gesture.deltaX <-100){
					$('nav#contact-right').trigger( 'open.mm' );
				}
			 });
		}

		$('li[data-counter-color]').each(function(i) {
			var counter=$(this).find("em.mm-counter");
			counter.css({"background-color":$.fillColor($(this)),"color":"#FFF" });
		});

		//////////     TIME LINE  MARK RE SIZE    //////////
		$(".widget-timeline li").each(function(i) {
			var data = $(this).data();
			if(data.color){
				$(this).css({"border-color": $.fillColor($(this)) ,"color": data.fcolor || "#FFF" });
				$(this).find("section").css({"background-color": $.fillColor($(this)) });
			}
		}); 
		$("div.mark").not(".bgimg").each(function() {
			var mark = $(this);
			mark.css({"line-height": parseInt(mark.height()+20)+"px" });
		 }); 

	
	
		//////////     CORNER FLIP    //////////
		$(".corner-flip").each(function(i) {
			$(this).append('<div class="flip" />');
		}); 
	


		//////////     HORIZONTAL FORM  AUTO GENERENT COLUMN       //////////
		$(".form-horizontal").each(function() {
			var form=$(this) ,data=$(this).data(), colClass= "col-"+( data.col || "md" ), coldiv="" ,
			colOffset=colClass+"-offset-", labelClass=colClass +"-"+( data.collabel || "2" );
			 if(data.collabel){
				 coldiv=12-parseInt(data.collabel);
			}
			var divClass=colClass +"-"+( coldiv || "10" );
			$(this).find(".form-group").each(function(i) {
				$div=$(this).find("div").eq(0);
				$label=$(this).find("label.control-label");
				if($(this).hasClass("offset")){
					$div.addClass(colOffset+(data.collabel || "2"));
				}
				 if(data.alignlabel){
					 $label.css("text-align",data.alignlabel);
				}
				 if(data.label){
					 form.addClass('labelcustomize');
					 $text=$label.html();
					 $label.html("").append('<span class="'+data.label+'">'+$text+'</span>');
					 $label.find("span.icon").css({"border-color": data.colorlabel || "#EEE" });
				}
				if(!$(this).hasClass("none")){
					$label.addClass(labelClass);
					$div.addClass(divClass);
				}
			});
			if(data.label=="icon"){
				form.append('<div class="icon '+labelClass+' '+data.alignlabel+'" />')
				form.find("div.icon").css({"border-color": data.colorlabel || "#EEE" });
			}
		});



		//////////     ICHECK     //////////
	  var createiCheck = (function() {
	  $('.iCheck').each(function(i) {
			var  data=$(this).data() , 
			 input=$(this).find("input") , 
			 li=$(this).find("li") ,
			 index="cp"+i , 
			 insert_text,
			 iCheckColor = [ "black", "red","green","blue","aero","grey","orange","yellow","pink","purple"],
			 callCheck=data.style || "flat";
		 if(data.color && data.style !=="polaris" && data.style !=="futurico" ){
				hasColor= jQuery.inArray(data.color, iCheckColor);
				if(hasColor !=-1 && hasColor < iCheckColor.length){
					callCheck=callCheck+"-"+data.color;
				}
		}
		input.each(function(i) {
			var self = $(this), label=$(this).next(), label_text=label.html();
			self.attr("id","iCheck-"+index+"-"+i);
			if(data.style=="line"){
				insert_text='<div class="icheck_line-icon"></div><span>'+label_text+'</span>';
				label.remove();
				self.iCheck({ checkboxClass: 'icheckbox_'+callCheck, radioClass: 'iradio_'+callCheck, insert:insert_text  });
			}else{
				label.attr("for","iCheck-"+index+"-"+i);
			}
		});
		if(data.style!=="line"){
			input.iCheck({ checkboxClass: 'icheckbox_'+callCheck, radioClass: 'iradio_'+callCheck });
		}else{
			li.addClass("line");
		}
	  });
	});
	createiCheck();
	
	  $('.iCheckColor li').click(function() {
	    var self = $(this);
	    if (!self.hasClass('active')) {
		 self.siblings().removeClass('active');
		 var color = self.attr('class');
 		$('.iCheck').each(function(i) {
			$(this).data("color",color)		  
		});
		$('.iCheck input').iCheck('destroy');
			createiCheck();
		 	self.addClass('active');
	    };
	  });

	 $('.ios-switch .switch').each(function(i) {
			$(this).addClass("ios");
	});
	$('.ios').bootstrapSwitch('setOnLabel', '');
	$('.ios').bootstrapSwitch('setOffLabel', '');
	
	
	//////////     ICHECK     //////////
	$(".ios-switch input:checkbox").change(function(){
		var targetLabel=$(this).parents('li').find("label span")		;	
		if( $(this).is(':checked')){
			targetLabel.text("ON");
		}else{
			targetLabel.text("OFF");
		}
	});	  
	
	
	//////////     COLOR PICKER     //////////
	$('[data-provide="colorpicker"]').each(function(i) {
		var id="color_"+i, $this=$(this).attr("id",id), data=$(this).data(),
		submit_btn=data.inline ? 0:1;
		if(data.addon && $this.is("input")){
			$('#'+id).next().css("width",$(this).outerHeight());
		}
		$this.colpick({
			bornIn:"#main",
			flat: data.inline || false,
			submit: submit_btn,
			layout: data.layout || 'hex',
			color: $this.val() || $.xcolor.random(),
			colorScheme: data.theme || "gray",
			onChange:function(hsb,hex,rgb) {
				$('#'+id).val('#'+hex);
				if(data.addon){
					$('#'+id).css({'border-color':'#'+hex });
					$('#'+id).next().css({'background-color':'#'+hex , 'border-color':'#'+hex });
				}
			},
			onSubmit:function(hsb,hex,rgb,el) {
				$(el).val('#'+hex); 
				$(el).colpickHide();
			}
		});
	});
	
	
		//////////     COLOR PALET     //////////
		$('#colorpalette1').colorPalette().on('selectColor', function(e) {
			$('#selected-color1').val(e.color);
			$('#selected-color1').parent().find(".ico").css("color", e.color );
		});
	
		//////////     COLOR PALET  IN  ADD EVENT CALENDAR    //////////
		var cc_color= new Array();
		$.each(cepletColor, function(key,val) {
				cc_color.push(val) //put color for ceplet color
		}); 		
		$('#colorpalette_events').colorPalette({ colors:[cc_color] }).on('selectColor', function(e) {
				var data=$(this).data();
				$(data.returnColor).val(e.color);
				$(this).parents(".input-group").find(".ico").css("color", e.color );
		});
			   
		
		
		//////////     PANEL  TOOLS     //////////
		$(".panel-tools[data-toolscolor]").each(function(i) {
			var tools=$(this), 
				data=$(this).data(), 
				btn=$(this).find(".btn"),
				step="";
				data.colorStep  = data.colorStep || true;
				if(tools.prev().hasClass("panel-heading") && !tools.hasClass("panel-tools-mini") ){
					tools.prev().css("border","none");
				}
				if(data.toolscolor){ 
					tools.css({"background-color":$.fillColor(tools) }) ;
					tools.parent().find(".panel-body .table thead > tr > th").css("border-bottom-color", $.fillColor(tools) );
				}
				btn.each(function(i) {
					$(this).css("background-color",$.xcolor.darken( $.fillColor(tools) , data.colorStep==false? 1:i+1 ,15))	;
				});
		});
		$('.panel-tools .btn-collapse').on('click', function () {
            var btn=$(this), panelBody=btn.closest(".panel").find(".panel-body");
			btn.toggleClass("in");
			btn.find("i").toggleClass( "fa-sort-amount-desc" );
			btn.find("i").toggleClass( "fa-sort-amount-asc" );
			if ($(this).hasClass("in")) {
				panelBody.slideUp();
			} else {
				panelBody.slideDown();
			}
        });	
		$('.panel-tools .btn-reload').click(function (e) {
			var  btn=$(this), panelBody=btn.closest(".panel").find(".panel-body"),
			overlay=$('<div class="load-overlay"><div><div class="c1"></div><div class="c2"></div><div class="c3"></div><div class="c4"></div></div><span>Loading...</span></div>');
			btn.removeClass("btn-panel-reload").addClass("disabled")
			panelBody.append(overlay);
			overlay.css('opacity',1).fadeIn();
			setTimeout(function () {  
				btn.removeClass("disabled").addClass("btn-panel-reload") ;
				panelBody.find(overlay).fadeOut("slow",function(){ $(this).remove() });
			}, 5000);
		});
		$(".panel-tools").on('click',".btn-close",function(){
				var panel=$(this).closest('.panel'), tools=$(this).closest('.panel-tools');
				console.log(tools)
				var confirmGroup=$('<div class="pt-confirm-group">'+'<div class=" btn-group btn-group-justified">'+'<a class="btn btn-inverse close-confirm" href="javascript:void(0)" data-confirm="accept">Yes</a>'+'<a class="btn btn-theme btn-close" href="javascript:void(0)">No.</a>'+'</div>'+'</div>');
				var blockClose=$('<div class="blockerClose">');
				tools.toggleClass( "push-in");
				if(tools.hasClass("push-in")){
					tools.append(confirmGroup);
					panel.toggleClass( "push-in");
					blockClose.appendTo("#content");
					blockClose.css({ "height":$("#content").outerHeight() }).fadeTo(400,0.5);
					console.log($("#content").outerHeight())
				}else{
					$(".blockerClose").fadeOut(200,function(){ $(this).remove() }); 
					setTimeout(function () {
						 tools.find(".pt-confirm-group").remove(); 
						 panel.toggleClass( "push-in") ;
					}, 500);  
				}	  
		});
	$(".panel-tools").on('click','.close-confirm',function(){
		$(this).closest('.panel').fadeOut(500,function(){
				$(this).remove();
				$(".blockerClose").fadeOut(200,function(){ $(this).remove() }); 
		});
	});
	 $('#content').on('click' ,'.blockerClose', function() {
		var el=$(this); 
		el.fadeOut(200,function(){ $(this).remove() }); 
		$(".panel-tools.push-in").toggleClass("push-in",function(){
			var tools=$(this);
			setTimeout(function () { 
				tools.find(".pt-confirm-group").remove();
				tools.closest('.panel').removeClass("push-in");
			}, 500);  
		});	
	 });
		
		
		
		
    });
    
    