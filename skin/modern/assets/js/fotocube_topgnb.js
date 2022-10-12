$(function($){
	/* gnb */
	var depth2Wrap = $(".depth2Wrap");
	$("#gnb > li > span > a").mouseenter(function(){
		$(this).parents("li").addClass("on").siblings().removeClass("on");
		depth2Wrap.hide();
		$(this).parent().next(".depth2Wrap").show();
	});

	$("#gnb > li").mouseleave(function(){
		$(this).removeClass("on");
		depth2Wrap.hide();
	});

	var max = $(".depth2Wrap").length + 1;
	for(var i = 0; i < max; i++){
		var depth2sub = $(".depth2Wrap"+".sub0"+i+" .depth2 > li:lt(4)");
		depth2sub.addClass("mgt0");
	}

	/* language */
	$(".lang").click(function(){
		$(this).toggleClass("on").next().toggle();
		return false;
	});

	$(".language").mouseleave(function(){
		$(this).children(".lang").removeClass("on");
		$(this).children("ul").hide();
	});

	/* login menu */
	$(".loginInfo .name").click(function(){
		$(this).next(".loginMenu").toggle();
		return false;
	});

	$(".loginInfo .btnClose").click(function(){
		$(".loginMenu").hide();
		return false;
	});

	$(".loginInfo").mouseleave(function(){
		$(this).children(".loginMenu").hide();
	});

	/* 전체메뉴 */
	$(".btnAllMenu").click(function(){
		$(".allMenu").slideToggle(800, 'easeOutExpo');
		return false;
	});

	$(".allMenu .btnClose").click(function(){
		$(".allMenu").slideUp(800, 'easeOutExpo');
		return false;
	});

	/* network */
	$("#networkWrap .btnNetwork").toggle(function(){
		$("#networkWrap .btnNetwork span").text("열기");
		$(this).addClass("open");
		$("#network").slideUp(1000, 'easeOutExpo');
		return false;
	}, function(){
		$("#networkWrap .btnNetwork span").text("닫기");
		$(this).removeClass("open");
		$("#network").slideDown(1000, 'easeOutExpo');
		var scrollHeight = $(document).height();
		$('html, body').animate({scrollTop:scrollHeight}, 1000, 'easeOutExpo');
		return false;
	});

	/* top 버튼 */
	$("#footer .btnTop").on('click', function(){
		$('html, body').animate({scrollTop:0}, 300);
		return false;
	});

	/* footer mark */
	$('.markList').jCarouselLite({
		btnNext: ".btnBnUp",
		btnPrev: ".btnBnDown",
		visible: 1,
		speed:400,
		vertical: true
	});

	/* lnb */
	$(".lnb > li:last-child").addClass("last");

	/* tab */
	$(".tab li a").on('click', function(e){
		e.preventDefault();
		var href= $(this).attr("href");
		if ($(this).parents("li").hasClass("on") == false) {
			$(href).show().siblings(".tabCont").hide();
			$(this).parents("li").addClass("on").siblings("li").removeClass("on");
			$(this).find("img").each(on).parents().siblings("li").find("img").each(off);
		}
		return false;
	});
});

