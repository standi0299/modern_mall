$(function(){

	// nav scroll
	var sct = $(window).scrollTop();
	var nav = $('.btn_magazine').offset().top;

	$(window).scroll(function(){
		sct = $(window).scrollTop();
		if(sct >= nav){
			$('#pupa-nav').addClass('fixed');
		}else{
			$('#pupa-nav').removeClass('fixed');
		}
	});

	// nav hover
	$('#pupa-nav .pupa-nav-inner .pupa-nav-depth1 > li').mouseover(function(){
		$('#pupa-nav .pupa-nav-inner .pupa-nav-depth1 > li').removeClass('depth1_on');
		$(this).addClass('depth1_on');

		if($(this).find('.pupa-nav-depth2').length > 0){
			$('#pupa-nav .pupa-nav-bottom').show();
			$('.pupa-nav-depth2').hide();
			$(this).find('.pupa-nav-depth2').show();
		}else{
			$('#pupa-nav .pupa-nav-bottom').hide();
			$('.pupa-nav-depth2').hide();
		}
	});

	$('#pupa-nav .pupa-nav-bottom').mouseout(function(){
		if(!$('#pupa-nav').is(':hover')){
			$('#pupa-nav .pupa-nav-bottom').hide();
			$('.pupa-nav-depth2').hide();
			$('#pupa-nav .pupa-nav-inner .pupa-nav-depth1 > li').removeClass('depth1_on');
		}
	});

	$('#pupa-nav').mouseout(function(){
		if(!$('#pupa-nav').is(':hover') && !$('#pupa-nav .pupa-nav-bottom').is(':hover')){
			$('#pupa-nav .pupa-nav-bottom').hide();
			$('.pupa-nav-depth2').hide();
			$('#pupa-nav .pupa-nav-inner .pupa-nav-depth1 > li').removeClass('depth1_on');
		}
	});

	// mypage popup
	$('.header-top-menu-mypage').hover(function(){
		$(this).addClass('active');
		$('.header-top-menu-mypage-pop').show();
	},function(){
		$(this).removeClass('active');
		$('.header-top-menu-mypage-pop').hide();
	})

	// main product tab
	$('.main-product-list-btn li').each(function(idx){
		$(this).click(function(e){
			e.preventDefault();
			$('.main-product-list-btn li').removeClass('on');
			$(this).addClass('on');
			$('.main-product-list-wrap .product-list ul').hide();
			$('.main-product-list-wrap .product-list ul').eq(idx).fadeIn();
		});
	});

	$('.cs-slide ul').slick({
		vertical:true,
		slidesToShow: 1,
		slidesToScroll: 1
	});	

	$('.main-img').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrow:true,
		autoplay:true,
		speed:1000
	});	

	$('.options-wrap.color ul').slick({
		slidesToShow: 5,
		slidesToScroll:1,
		arrow:true,
		infinite:false
	});

	$('.product-list.recommend ul').slick({
		slidesToShow: 4,
		slidesToScroll:1,
		arrow:true
	});

	$('.close').click(function(e){
		e.preventDefault();
		$('.pupa-bg').fadeOut();
		$(this).parent().hide();
		$("html").css({
			"overflow-y":"scroll"
		});
	});

	//상품 하트 toggle
	$('.btn_wish').click(function(e){
		e.stopPropagation();
		$(this).toggleClass('on');
		return false;
	});

	//첨부파일 사진 삭제
	$('.attach_close').click(function(){
		$(this).parent().remove();
	});
});

function popOpen(elem){
	$('.pupa-bg').fadeIn();
	$(elem).fadeIn();
	var tx = $(elem).outerWidth()/2;
	var ty = $(elem).outerHeight()/2;
	var h = $(window).height();
	sct = $(window).scrollTop();
	
	// 팝업 높이가 해상도보다 크면 position:fixed 해제
	if($(elem).height() > h ){
		$(elem).css({position:'absolute',left:'50%',top:sct+'px',marginLeft:-tx+"px",marginTop:0});
		$("html").css({
			"overflow-y":"scroll"
		});
	}else{
		$(elem).css({position:'fixed',left:'50%',top:'50%',marginLeft:-tx+"px",marginTop:-ty+"px"});
		$("html").css({
			"overflow-x":"hidden",
			'overflow-y':"hidden"
		});
	}
}