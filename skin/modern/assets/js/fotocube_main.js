$(document).ready(function() {
	var main_page = 0;
	$('.mainBtn_next').click(function(){
	
		//alert($('.main_visual ul li').length);

		//$('.main_visual ul li').css('display','none');
		
		if(main_page < $('.main_visual ul li').length-1){


			$('.main_visual').css('background-color',$('.main_visual ul li').eq(main_page+1).css('background-color'));

			$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"0"},1600);
			$('.main_visual ul li').eq(main_page).animate({opacity:"0"},1400,function(){

				main_page += 1;
				$('.main_visual ul li').eq(main_page).css('display','');
				$(this).css('display','none');
				$('.main_visual ul li').eq(main_page).animate({opacity:"1"},1400);
				$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"1"},1600);

			});

		}else{
			//alert("main_page = "+main_page);
			$('.main_visual').css('background-color',$('.main_visual ul li').eq(0).css('background-color'));
			$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"0"},1600);
			$('.main_visual ul li').eq(main_page).animate({opacity:"0"},1400,function(){

				main_page = 0;
				$('.main_visual ul li').eq(main_page).css('display','');
				$(this).css('display','none');
				$('.main_visual ul li').eq(main_page).animate({opacity:"1"},1400);
				$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"1"},1600);

			});
		}
	
	});

	$('.mainBtn_prev').click(function(){
	
		//alert($('.main_visual ul li').length);

		//$('.main_visual ul li').css('display','none');
		if(main_page == 0){
			//alert("main_page = "+main_page);
			$('.main_visual').css('background-color',$('.main_visual ul li').eq($('.main_visual ul li').length-1).css('background-color'));
			$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"0"},1600);
			$('.main_visual ul li').eq(main_page).animate({opacity:"0"},1400,function(){

				main_page = $('.main_visual ul li').length-1;
				$('.main_visual ul li').eq(main_page).css('display','');
				$(this).css('display','none');
				$('.main_visual ul li').eq(main_page).animate({opacity:"1"},1400);
				$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"1"},1600);

			});
		}else{
			$('.main_visual').css('background-color',$('.main_visual ul li').eq(main_page-1).css('background-color'));

			$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"0"},1600);
			$('.main_visual ul li').eq(main_page).animate({opacity:"0"},1400,function(){

				main_page -= 1;
				$('.main_visual ul li').eq(main_page).css('display','');
				$(this).css('display','none');
				$('.main_visual ul li').eq(main_page).animate({opacity:"1"},1400);
				$('.main_visual ul li').eq(main_page).find('.txt').animate({opacity:"1"},1600);

			});
		}
		
	});

	
	$('.coordi .tab li a').click(function(){
		$('.coordi .tab li a').removeClass('on');
		$(this).addClass('on');
		
		idx = $('.coordi .tab li a').index($(this));

		$('.list').css('display','none');
		$('.list').eq(idx).css('display','block');	
	});

	//이 부분이 어느 계절 먼저 보이는지 바꾸는 부분
	//$('.list').eq(0).css('display','none');
	$('.list').eq(0).css('display','none');

	$('.coordi_prev').click(function(){
		winWidth = $(window).width();

		//alert("winWidth = " + winWidth);
		ul_left = $('.list > ul').css('left');
		ul_right = $('.list > ul').css('right');
		//alert("ul_left = "+ul_left+"\nil_right = "+ul_right);

		now_left = ul_left.replace('px','');
		//alert("ul_left = " + now_left);

		move_num = now_left - 360;
		if(move_num >= 240){
		$('.list > ul').animate({'left':move_num+'px'});
			auto_flag = 1;
		}else{
			auto_flag = 0;
		}
	});

	$('.coordi_next').click(function(){
		ul_left = $('.list > ul').css('left');
		ul_right = $('.list > ul').css('right');
		//alert("ul_left = "+ul_left+"\nil_right = "+ul_right);
		now_left = ul_left.replace('px','') * 1;


		move_num = now_left + 360;
		if(move_num <= 960){
		$('.list > ul').animate({'left':move_num+'px'});
			auto_flag = 0;
		}else{
			auto_flag = 1;
		}
	});

	winWidth = $(window).width();
	if(winWidth < 1900){
		$('.coordi_prev').css('display','block');
		$('.coordi_next').css('display','block');
	}else{
		$('.coordi_prev').css('display','none');
		$('.coordi_next').css('display','none');
	}
	
	
	$(window).resize(function(){	
		winWidth = $(window).width();
		if(winWidth < 1900){
			$('.coordi_prev').css('display','block');
			$('.coordi_next').css('display','block');
			
		}else{
			$('.coordi_prev').css('display','none');
			$('.coordi_next').css('display','none');
			$('.list > ul').animate({'left':600+'px'});
		}
	});
	
	
	$('#main_codi1').jCarouselLite({
		btnNext : '.mainCode1_next',
		btnPrev : '.mainCode1_prev',
			circular : "true",
			auto : 5000,
			visible : 1,
			scroll: 1
	});

	$('#main_codi2').jCarouselLite({
		btnNext : '.mainCode2_next',
		btnPrev : '.mainCode2_prev',
			circular : "true",
			auto : 5000,
			visible : 1,
			scroll: 1
	});

	$('#main_codi3').jCarouselLite({
		btnNext : '.mainCode3_next',
		btnPrev : '.mainCode3_prev',
			circular : "true",
			visible : 1,
			auto : 5000,
			scroll: 1
	});

	$('#main_codi4').jCarouselLite({
		btnNext : '.mainCode4_next',
		btnPrev : '.mainCode4_prev',
			circular : "true",
			visible : 1,
			auto : 5000,
			scroll: 1
	});
	
	
	$('.kr_view').jCarouselLite({
		btnGo: [".kr_viewDot1", ".kr_viewDot2", ".kr_viewDot3"],
		circular : "true",
		visible : 1,
		scroll: 1,
		auto : 5000,
		speed: 1600,
		afterEnd:function(a){

			//alert("$(this).index() = " + $(this).index());
			btnIndex = a.index() -1;
			//alert("btnIndex = " + btnIndex);
			//alert("btn_dot.length = " + $('.btn_dot').length);
			//$('.btn_dot').eq(btnIndex).addClass(on);
			if(btnIndex == 3){btnIndex = 0;}
			$('.krviewBtn').each(function(i){

				if(i == btnIndex){
					$(this).addClass('on');
				}else{
					$(this).removeClass('on');
				}

			});

		}
	});

	$('.fn_view').jCarouselLite({
		btnGo: [".fn_viewDot1", ".fn_viewDot2", ".fn_viewDot3"],
		circular : "true",
		visible : 1,
		scroll: 1,
		auto : 5000,
		speed: 1600,
		afterEnd:function(a){

			//alert("$(this).index() = " + $(this).index());
			btnIndex = a.index() -1;
			//alert("btnIndex = " + btnIndex);
			//alert("btn_dot.length = " + $('.btn_dot').length);
			//$('.btn_dot').eq(btnIndex).addClass(on);
			if(btnIndex == 3){btnIndex = 0;}
			$('.fnviewBtn').each(function(i){

				if(i == btnIndex){
					$(this).addClass('on');
				}else{
					$(this).removeClass('on');
				}

			});

		}
	});
});	


function auto_main(){
	$('.mainBtn_next').click();
}
var auto_flag = 0;
function auto_coodi(){
	winWidth = $(window).width();
	if(winWidth < 1900){
		ul_left = $('.list > ul').css('left');
		ul_right = $('.list > ul').css('right');
		now_left = ul_left.replace('px','') * 1;
		if(now_left <= 240){
			auto_flag = 1;
		}else if(now_left >= 960){
			auto_flag = 0;
		}
		//alert("now_left = "+now_left+"\nauto_flag = "+auto_flag);
		if(auto_flag == 0){
			move_num = now_left - 360;
		}else if(auto_flag == 1){
			move_num = now_left + 360;
		}
		$('.list > ul').animate({'left':move_num+'px'});
	}
}
setInterval("auto_main()",4000);
setInterval("auto_coodi()",5400);