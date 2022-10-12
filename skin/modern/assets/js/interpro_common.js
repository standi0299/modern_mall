jQuery(document).ready(function($){
    $('.main_banner_sec > div').each(function(){
        var tab = $(this).children('ul'),
            tabBtn = tab.children('li');
        var container = $(this);
        
        tabBtn.hover(function(){        
           var i = $(this).index(this);
            
           if($(this).hasClass('on')) return;
           
           tabBtn.removeClass('on');
           $(this).addClass('on');
            
            var interval = 4000;
            var timer;

            /*function changeBtn(){
                var anchors = container.find('li');
                var first = tabBtn.eq(i);
                var second = tabBtn.eq(i);        

                first.removeClass('on');
                second.addClass('on');
                
            }
            function startTimer(){
                timer = setInterval(changeBtn, interval);
            }
            function stopTimer(){
                clearInterval(timer);
            }
            container.find('li').hover(stopTimer, startTimer);
            startTimer();*/ /*시험중*/
        });
        
    });
});

jQuery(document).ready(function($){
	$('.depth2').each(function(){
		var topDiv = $(this);
		var anchors = topDiv.find('ul.snbSubNav a');
		var panelDivs = topDiv.find('div.tap0');
		var lastAnchor;
		var lastPanel;
		anchors.show();
		
		lastAnchor = anchors.filter('.on');
		lastPanel = $(lastAnchor.attr('href'));
		
		panelDivs.hide();
		lastPanel.show();
		
		anchors.hover(function(event){
			event.preventDefault();
			var currentAnchor = $(this);
			var currentPanel = $(currentAnchor.attr('href')); 
			
			if(currentAnchor.get(0)===lastAnchor.get(0)){
				return;
			}
			
			lastPanel.fadeOut(200,function(){
				lastAnchor.removeClass('on');
				currentAnchor.addClass('on');
				
				/* lastPanel.hide(); */
				currentPanel.fadeIn(200);
				/* currentPanel.show(); */
				lastAnchor = currentAnchor;
				lastPanel = currentPanel;
			});
		});
	});
}); /*상단메뉴 2depth tab 스크립트*/

jQuery(document).ready(function($){
	$('.main_banner_sec > div').each(function(){
		var topDiv = $(this);
		var anchors = topDiv.find('ul.main_banner_btn li a');
		var panelDivs = topDiv.find('.main_banner .banner');
		var lastAnchor;
		var lastPanel;
		anchors.show();
		
		lastAnchor = anchors.filter('.on');
		lastPanel = $(lastAnchor.attr('href'));
		
		panelDivs.hide();
		lastPanel.show();
		
		anchors.hover(function(event){
			event.preventDefault();
			var currentAnchor = $(this);
			var currentPanel = $(currentAnchor.attr('href')); 
			
			if(currentAnchor.get(0)===lastAnchor.get(0)){
				return;
			}
			
			lastPanel.fadeOut(400,function(){
				lastAnchor.removeClass('on');
				currentAnchor.addClass('on');
				
				/* lastPanel.hide(); */
				currentPanel.fadeIn(400);
				/* currentPanel.show(); */
				lastAnchor = currentAnchor;
				lastPanel = currentPanel;
			});
		});
	});
}); /*메인배너 tab 스크립트*/

jQuery(document).ready(function($){
	$.fn.extend({		
		categoryCarousel:function(){
			$(".business_cat_case").carousel({
				create:function(){
					categoryCarouselImg();
				},
				viewitem:3,
				moveitem:1,
				interval:false,
				moveEnd:function(){
					categoryCarouselImg();
				},
				prevBtn:$("#arrowLeft"),
				nextBtn:$("#arrowRight"),
			});
			
			var $wrap    = $(".business_cat_case"),
				$auto    = $("#bannerPlay"),
				$stop    = $("#bannerStop"),
				$nextBnt = $("#arrowRight"),
				auto = false,
				timer;
			
			/*$auto.on("click", function(e){ auto = true; });
			$stop.on("click", function(e){ auto = false; });*/
			
			timer = setInterval(function(){
				auto && $nextBnt.trigger('click');
			}, 1000);

			function categoryCarouselImg(){
				$(".business_cat img").each(function(){ $(this).attr("offsrc",this.src); });
				$(".business_cat a").on("mouseenter focus",function(){
					var img = $(this).find("img").get(0);
					/*img.src = $(img).attr("oversrc");*/
				});
				$(".business_cat a").on("mouseleave blur",function(){
					var img = $(this).find("img").get(0);
					/*img.src = $(img).attr("offsrc");*/
				});
			}
		},
	});
});

jQuery(function($){
	$.fn.categoryCarousel();
});

jQuery(document).ready(function($){
	$.fn.extend({		
		popupCarousel:function(){
			$(".pop_cont_case").carousel({
				create:function(){
					popupCarouselImg();
				},
				viewitem:3,
				moveitem:1,
				interval:false,
				moveEnd:function(){
					popupCarouselImg();
				},
				prevBtn:$("#popArrowLeft"),
				nextBtn:$("#popArrowRight"),
			});
			
			var $wrap    = $(".pop_cont_case"),
				$auto    = $("#bannerPlay"),
				$stop    = $("#bannerStop"),
				$nextBnt = $("#popArrowRight"),
				auto = false,
				timer;
			
			/*$auto.on("click", function(e){ auto = true; });
			$stop.on("click", function(e){ auto = false; });*/
			
			timer = setInterval(function(){
				auto && $nextBnt.trigger('click');
			}, 1000);

			function popupCarouselImg(){
				$(".pop_cat img").each(function(){ $(this).attr("offsrc",this.src); });
				$(".pop_cat a").on("mouseenter focus",function(){
					var img = $(this).find("img").get(0);
					/*img.src = $(img).attr("oversrc");*/
				});
				$(".pop_cat a").on("mouseleave blur",function(){
					var img = $(this).find("img").get(0);
					/*img.src = $(img).attr("offsrc");*/
				});
			}
		},
	});
});

jQuery(function($){
	$.fn.popupCarousel();
});