/*메인페이지 인기상품, 신상품*/
function main_bottom_img(button) {
	$(".pro_px_img_list ").hide();
	$("#pro_id_" + button).show();
};

//상품상세슬라이더
$(function () {
	$.fn.smartSlider = function () {
		var width = 106; // 넘어가는 속도
		var count = 2;   //공간을 얼마만큼 보여 줄건가
		var slides = $('#slides > li');
		var position = 0;

		if ($(":input[name=slides_margin_left]").val() != undefined) {
			position = Number($(":input[name=slides_margin_left]").val());
		}

		$("#right").click(function(){
			if (position >= 0) return false;
		    position = position + width * count;
			$("#slides").animate({
				marginLeft: position
		    }, 300);

		    if ($(":input[name=slides_margin_left]").val() != undefined) {
				$(":input[name=slides_margin_left]").val(position);
			}

			return false;
		});

		$("#left").click(function () {
			if (position <= (-width * count * (slides.length - 5))) return false;
			position = position - width * count;
		 	$("#slides").animate({
		 		marginLeft: position
			}, 300);

			if ($(":input[name=slides_margin_left]").val() != undefined) {
				$(":input[name=slides_margin_left]").val(position);
			}

		 	return false;
		});
 	};

	$('#slider').smartSlider();
});

/*무통장 클릭시 아래 서류 창 나옴*/
$(document).ready(function() {
	if ($(".basong_span_px_m input[type=radio][name=paymethod]:checked").val() == "b") {
		$("#depo").attr("disabled", false);
		$("#depo1").attr("disabled", false);
		$("#depo2").attr("disabled", false);
		$("#depo3").attr("disabled", false);
		$(":input[name=payer_name]").attr("required", true);
	} else {
		$("#depo").attr("disabled", true);
		$("#depo1").attr("disabled", true);
		$("#depo2").attr("disabled", true);
		$("#depo3").attr("disabled", true);
		$(":input[name=payer_name]").attr("required", false);
	}

	$("#last_view").hide();

	$(".card_px").click(function() {
		$("#depo").prop("checked", false);
		$("#depo1").prop("disabled", false);
		$("#depo2").prop("disabled", false);
		$("#depo3").attr("disabled", false);
		$(":input[name=payer_name]").attr("required", false);
		$("#depo").attr("disabled", true);
		$("#depo1").attr("disabled", true);
		$("#depo2").attr("disabled", true);
		$("#depo3").attr("disabled", true);
		$("#last_view").hide();
	});	//click

	$(".card_px_mm").click(function() {
		$("#depo").attr("disabled", false);
		$("#depo1").attr("disabled", false);
		$("#depo2").attr("disabled", false);
		$("#depo3").attr("disabled", false);
		$(":input[name=payer_name]").attr("required", true);

		if ($("#depo1").is(":checked")){
			$("#last_view").show();
			$("#last_view2").hide();
		} else{
			$("#last_view").hide();
		}

		if ($("#depo2").is(":checked")){
			$("#last_view2").show();
			$("#last_view").hide();
		} else{
			$("#last_view2").hide();
		}
	});

	$("#depo1").click(function() {
		if ($(".basong_span_px_m input[type=radio][name=paymethod]:checked").val() == "b" && $("#depo1").is(":checked")) {
			$("#last_view").show();
			$("#last_view2").hide();
		} else {
			$("#last_view").hide();
		}
	});

	$("#depo2").click(function() {
		if ($(".basong_span_px_m input[type=radio][name=paymethod]:checked").val() == "b" && $("#depo2").is(":checked")) {
			$("#last_view2").show();
			$("#last_view").hide();
		} else {
			$("#last_view2").hide();
		}
	});
});//제이쿼리

/*상품리스트*/
function panel_box(panel) {
	$(".container").hide();
	$("#panel_cont_" + panel).show();
};

// 후기 페이지 글 후기
$(document).ready(function() {
	$(".down_btn_re").click(function() {
		$("#0807_re").toggle();
	});
});

// 후기 페이지 사진 후기
$(document).ready(function() {
	$(".down_btn_poto").click(function() {
		$("#0808_re").toggle();
	});
});

//갤러리 레이어 팝업
function open_gallery_layer(){
	// 화면의 높이와 너비를 변수로 만듭니다.
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();

	// 마스크의 높이와 너비를 화면의 높이와 너비 변수로 설정합니다.
	$('.mask_gell').css({'width':2500,'height':2000});

	// fade 애니메이션 : 1초 동안 검게 됐다가 80%의 불투명으로 변합니다.
	$('.mask_gell').fadeIn(1000);
	$('.mask_gell').fadeTo("slow",0.8);

	// 레이어 팝업을 가운데로 띄우기 위해 화면의 높이와 너비의 가운데 값과 스크롤 값을 더하여 변수로 만듭니다.
	var left = ( $(window).scrollLeft() + ( $(window).width() - $('.window_edit').width()) / 2 );
	var top = ( $(window).scrollTop() + ( $(window).height() - $('.window_edit').height()) / 2 );

	// css 스타일을 변경합니다.
	$('.window_edit').css({'left':left,'top':top, 'position':'absolute'});

	// 레이어 팝업을 띄웁니다.
	$('.window_edit').show();
}

$(document).ready(function() {
	// show_edit를 클릭시 작동하며 검은 마스크 배경과 레이어 팝업을 띄웁니다.
	/*
	$('.show_edit').click(function(e) {
		// preventDefault는 href의 링크 기본 행동을 막는 기능입니다.
		//e.preventDefault();
		//wrapWindowByMask_2();
	});
	*/

	// 닫기(close)를 눌렀을 때 작동합니다.
	$('.window_edit .closedd').click(function(e) {
		e.preventDefault();
		$('.mask_gell, .window_edit').hide();
	});

	// 뒤 검은 마스크를 클릭시에도 모두 제거하도록 처리합니다.
	$('.mask_gell').click(function() {
		$(this).hide();
		$('.window_edit').hide();
	});
});

/*이벤트*/
function clickst(radioIndex) {

	$(".st_event_map").hide();
	$("#st_stve_" + radioIndex).show();
};

/*스크롤 타겟?
 $(document).ready(function(){
 $("#moveBtn").on("click",function(event){
 // 이동 버튼을 클릭시 pre 태그로 스크롤의 위치가 이동되도록 한다.

 // 1. pre태그의 위치를 가지고 있는 객체를 얻어온다. => offset 객체
 var offset = $("#maa_text").offset();

 // offset은 절대 위치를 가져온다. offset.top을 통해 상단의 좌표를 가져온다.
 // position은 부모를 기준으로한 상대위치를 가져온다.
 $("html body").animate({scrollTop:offset.top},2000);

 });*/


function fnMove(seq) {
	var offset = $("#option" + seq).offset();
	$('html, body').animate({
		scrollTop : offset.top
	}, 400);
}

//장바구니 수량 값
$(function() {
	$('.vase_btn2').click(function() {
		var n = $('.vase_btn2').index(this);
		var num = $(".vase_btn3:eq(" + n + ")").val();
		num = $(".vase_btn3:eq(" + n + ")").val(num * 1 + 1);
	});
	$('.vase_btn4').click(function() {
		var n = $('.vase_btn4').index(this);
		var num = $(".vase_btn3:eq(" + n + ")").val();
		if(num < 2 ) alert("최소 수량은 1 입니다.");
		else num = $(".vase_btn3:eq(" + n + ")").val(num * 1 - 1);

	});
});

function auto_set_pay(){
var state = jQuery('#selectBox option:selected').val();
	if(state == '1'){
		$(".number_1").show();
		$(".number_2").hide();
		$(".upload").hide();
		$("#filename").hide();
		$("#number").attr();
	}else if(state == '2'){
		$(".number_2").show();
		$(".number_1").hide();
		$(".upload").hide();
		$("#filename").hide();
		$("#number2").attr();
	}else if(state == '3'){
		$(".number_1").show();
		$(".number_2").hide();
		$(".upload").hide();
		$("#filename").hide();
		$("#number2").attr();
	}else if(state == '4'){
		$(".number_2").show();
		$(".number_1").hide();
		$(".upload").hide();
		$("#filename").hide();
		$("#number2").attr();
	}else if(state == '5'){
		$(".upload").show();
		$(".number_1").hide();
		$(".number_2").hide();
		$("#filename").show();
		//$(".upload").attr("placeholder","사업자등록번호");
	}else{
		$(".number_1").hide();
		$(".upload").show();
		$(".upload").show();
	}
}

/*상세페이지 다른 상품 미리보기*/
function current(n) {
  showDivs(slideIndex = n);
}

function showDivs(n) {
  var i;
  var x = document.getElementsByClassName("myclass");
  var dots = document.getElementsByClassName("demo");
  if (n > x.length) {slideIndex = 1;}
  if (n < 1) {slideIndex = x.length;}
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
     dots[i].className = dots[i].className.replace(" w3-opacity-off", "");
  }
  x[slideIndex-1].style.display = "block","border:1px solid #000";
  dots[slideIndex-1].className += " w3-opacity-off";
}


/*[pixstory] 다른이벤트페이지 아래 슬라이드*/
$(function ($) {
    $.fn.smartSlider = function () {
        var width = 1105;
        var count = 1;
        var slides = $('#slides_event > li');
        var position = 0;

		 $("#right2").click(function () {
            if (position >= 0) return false;
            position = position + width * count;
             $("#slides_event").animate({
                marginLeft: position
            }, 300);
             return false;
        });


         $("#left2").click(function () {
            if (position <= -width * (slides.length - 1)) return false;
            position = position - width * count;
             $("#slides_event").animate({
                marginLeft: position
            }, 300);
             return false;
        });
    };


	$('#slider_event').smartSlider();
});
