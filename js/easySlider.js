/*
 * 	Easy Slider 1.7 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4004/easy-slider-15-the-easiest-jquery-plugin-for-sliding
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
/*
 *	markup example for $("#slider").easySlider();
 *	
 * 	<div id="slider">
 *		<ul>
 *			<li><img src="images/01.jpg" alt="" /></li>
 *			<li><img src="images/02.jpg" alt="" /></li>
 *			<li><img src="images/03.jpg" alt="" /></li>
 *			<li><img src="images/04.jpg" alt="" /></li>
 *			<li><img src="images/05.jpg" alt="" /></li>
 *		</ul>
 *	</div>
 *
 */

(function($) {

	$.fn.easySlider = function(options){
	  
		// default configuration properties
		var defaults = {			
			prevId: 		'prevBtn',
			prevText: 		'<',
			nextId: 		'nextBtn',	
			nextText: 		'>',
			controlsShow:	true,
			controlsBefore:	'',
			controlsAfter:	'',	
			controlsFade:	true,
			firstId: 		'firstBtn',
			firstText: 		'First',
			firstShow:		false,
			lastId: 		'lastBtn',	
			lastText: 		'Last',
			lastShow:		false,				
			vertical:		false,
			speed: 			800,
			auto:			false,
			mouseleave:		false,
			pause:			5000,
			continuous:		false, 
			numeric: 		false,
			numericId: 		'controls'
		}; 
		
		var options = $.extend(defaults, options);  
				
		this.each(function() {  
			var obj = $(this); 				
			var s = $("li", obj).length;
			var w = $("li", obj).width(); 
			var h = $("li", obj).height(); 
			var clickable = true;
			obj.width(w); 
			obj.height(h); 
			obj.css("overflow","hidden");
			var ts = s-1;
			var t = 0;
			$("ul", obj).css('width',s*w);			
			if(options.continuous){
				$("ul", obj).prepend($("ul li:last-child", obj).clone().css("margin-left","-"+ w +"px"));
				$("ul", obj).append($("ul li:nth-child(2)", obj).clone());
				$("ul", obj).css('width',(s+1)*w);
			};				
			
			if(!options.vertical) $("li", obj).css('float','left');
								
			if(options.controlsShow){
				var html = options.controlsBefore;				
				if(options.numeric && $(obj).attr("id")!="event"){
					html += '<div id="'+ options.numericId +'"></div>';
				} else {
					if ($(obj).attr("id")!="event"){
						if(options.firstShow) html += '<span id="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
						$("#prevbtn").html('<span id="'+ options.prevId +'"><a href=\"javascript:void(0);\">'+ options.prevText +'</a></span>');
						$("#nextbtn").html('<span id="'+ options.nextId +'"><a href=\"javascript:void(0);\">'+ options.nextText +'</a></span>');
						if(options.lastShow) html += ' <span id="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';				
					}
				};
				
				html += options.controlsAfter;
				$(obj).after(html);										
			};
 			
			if(options.numeric && $(obj).attr("id")=="event"){									
				$j("#event01").click(function(){	
					animate(0,true);
				}); 	
				$j("#event02").click(function(){	
					animate(1,true);
				}); 
				$j("#event03").click(function(){	
					animate(2,true);
				});						
			} else {
				$("a","#"+options.nextId).click(function(){		
					animate("next",true);
				});
				$("a","#"+options.prevId).click(function(){		
					animate("prev",true);				
				});	
				$("a","#"+options.firstId).click(function(){		
					animate("first",true);
				});				
				$("a","#"+options.lastId).click(function(){		
					animate("last",true);				
				});				
			};
			
			function setCurrent(i){
				i = parseInt(i)+1;
				$("li", "#" + options.numericId).removeClass("current");
				$("li#" + options.numericId + i).addClass("current");
			};
			
			function adjust(){
				if(t>ts) t=0;		
				if(t<0) t=ts;	
				if(!options.vertical) {
					$("ul",obj).css("margin-left",(t*w*-1));
				} else {
					$("ul",obj).css("margin-left",(t*h*-1));
				}
				clickable = true;
				if(options.numeric) setCurrent(t);
			};

			if ($(obj).attr("id")=="box") $("#page").html((t+1) + "/" + Math.floor(s));
			if ($(obj).attr("id")=="event") $j("img","#event01").attr("src",$j("img","#event01").attr("src").replace("_gray","_red"));
			function animate(dir,clicked){
				if (clickable){
					var ot = t;				
					switch(dir){
						case "next":
							t = (ot>=ts) ? (options.continuous ? t+1 : ts) : t+1;	
							if ($(obj).attr("id")=="box") $("#page").html((t+1) + "/" + Math.floor(s));	
							if ($(obj).attr("id")=="event"){
								tt = t + 1;
								if(tt>3) tt = 1;
								for(var i=1; i<4; i++) $j("img","#event0"+i).attr("src",$j("img","#event0"+i).attr("src").replace("_red","_gray"));
								$j("img","#event0"+tt).attr("src",$j("img","#event0"+tt).attr("src").replace("_gray","_red"));
							}
							break; 
						case "prev":
							t = (t<=0) ? (options.continuous ? t-1 : 0) : t-1;
							if ($(obj).attr("id")=="box") $("#page").html((t+1) + "/" + Math.floor(s));
							break; 
						case "first":
							t = 0;
							if ($(obj).attr("id")=="box") $("#page").html((t+1) + "/" + Math.floor(s));
							break; 
						case "last":
							t = ts;
							break; 
						default:
							t = dir;
							tt = t + 1;
							if ($(obj).attr("id")=="event"){
								for(var i=1; i<4; i++) $j("img","#event0"+i).attr("src",$j("img","#event0"+i).attr("src").replace("_red","_gray"));
								$j("img","#event0"+tt).attr("src",$j("img","#event0"+tt).attr("src").replace("_gray","_red"));
								
							}
							break; 
					};	
					var diff = Math.abs(ot-t);
					var speed = diff*options.speed;						
					if(!options.vertical) {
						p = (t*w*-1);
						$("ul",obj).animate(
							{ marginLeft: p }, 
							{ queue:false, duration:speed, complete:adjust }
						);				
					} else {
						p = (t*h*-1);
						$("ul",obj).animate(
							{ marginTop: p }, 
							{ queue:false, duration:speed, complete:adjust }
						);					
					};
					
					if(!options.continuous && options.controlsFade){
						if(t==ts){
							$("a","#"+options.nextId).hide();
							$("a","#"+options.lastId).hide();
						} else {
							$("a","#"+options.nextId).show();
							$("a","#"+options.lastId).show();					
						};
						if(t==0){
							$("a","#"+options.prevId).hide();
							$("a","#"+options.firstId).hide();
						} else {
							$("a","#"+options.prevId).show();
							$("a","#"+options.firstId).show();
						};					
					};				
					
					if(clicked) clearTimeout(timeout);
					if(options.auto){
						timeout = setTimeout(function(){
							animate("next",false);
						},diff*options.speed+options.pause);
					};
			
				};
				
			};
			// init
			var timeout;
			if(options.auto){;
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);

				/* by TARAKA ------------------------------------*/
				if (options.leavestop){

					$(this).mouseenter(function(){
						clearTimeout(timeout);
						timeout = '';
					});

					$(this).mouseleave(function(){
						timeout = setTimeout(function(){
							animate("next",false);
						},options.pause);
					});

				}
				/* by TARAKA ------------------------------------*/

			};		
			
			if(options.numeric) setCurrent(0);
		
			if(!options.continuous && options.controlsFade){					
				$("a","#"+options.prevId).hide();
				$("a","#"+options.firstId).hide();		
				if (s==1)
				{
					$("a","#"+options.nextId).hide();
					$("a","#"+options.lastId).hide();		
				}else{
					$("a","#"+options.nextId).show();
					$("a","#"+options.nextId).show();	
				}
			};
			
		});
	  
	};

})(jQuery);



