jQuery(document).ready(function($){
    var menu = $('.nav > li');
	// 1뎁스 메뉴
	var wrap = $('#gnb')
	// 1뎁스와 2뎁스를 감싸고 있는 엘리먼트
	var menuHeight = menu.children('a').height()+20;
	// 1뎁스 메뉴의 높이를 변수에 할당
	var pageURL = location.href;
	// 현재 페이지 주소를 변수에 할당
	var activeMenu;
	// 현재 페이지 주소와 일치하는 메뉴를 저장시킬 변수선언
	
	menu.on({
		mouseover:function(){
			var tg = $(this);
			menu.removeClass('on');
			menu.removeClass('onTxt');
			tg.addClass('on');
			tg.addClass('onTxt');
			var th = menuHeight + tg.find('> .depth2').height()+30;
			// 1뎁스의 높이 + 2뎁스의 높이
			wrap.stop().animate({height:th});
			// #gnbWrap의 높이 값을 애니메이션 시켜 변경(2뎁스의 메뉴 높이 만큼 늘어남)
		},
		mouseout:function(){
			var tg = $(this);
			tg.removeClass('onTxt');
			wrap.stop().animate({height:menuHeight});
			// #gnbWrap의 높이 값을 애니메이션 시켜 변경(1뎁스의 메뉴 높이 만큼 회귀)
		}
	});
});