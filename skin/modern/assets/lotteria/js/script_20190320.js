// JavaScript Document


$('nav.jh_navi li ul').hide().removeClass('fallback');
$('nav.jh_navi li').hover(
  function () {
    $('ul', this).stop().slideDown(100);
  },
  function () {
    $('ul', this).stop().slideUp(100);
  }
);

$('#jh_lnb .frit ul li ul').hide().removeClass('fritSubBlit');
$('#jh_lnb .frit ul li').hover(
  function () {
    $('ul', this).stop().slideDown(100);
  },
  function () {
    $('ul', this).stop().slideUp(100);
  }
);


$(document).ready(function(){
	$('#jsamBtn1').click(function(){
		  $(this).css('display','none');
		  $(".jh_allMenu").css('display','block');
		  $("#jsamBtn2").css('display','block');
	})
	$('#jsamBtn2').click(function(){
		  $(this).css('display','none');
		  $(".jh_allMenu").css('display','none');
		  $("#jsamBtn1").css('display','block');
	}) 
	$('li.mypage').click(function(){
		  $("#myPageSide").css('display','block');
	}) 
	$('.mpsXbtn').click(function(){
		  $("#myPageSide").css('display','none');
	}) 
	
	
	$(window).scroll(function(){  
		if ($(this).scrollTop() < 240) {   
			$('#wing').css('top','246px');
			$('#wing').css('position','absolute');
		}  
		if ($(this).scrollTop() > 241) {  
			$('#wing').css('top','10px');
			$('#wing').css('position','fixed');
		}
	});  
	$(window).scroll(function(){  
		if ($(this).scrollTop() < 80) {   
			$('#myPageSide').css('top','85px');
			$('#myPageSide').css('position','absolute');
		}  
		if ($(this).scrollTop() > 81) {  
			$('#myPageSide').css('top','0px');
			$('#myPageSide').css('position','fixed');
		}
	});  
})


jssor_1_slider_init = function() {

	var jssor_1_options = {
	  $AutoPlay: 1,
	  $Idle: 3000,
	  $ArrowNavigatorOptions: {
		$Class: $JssorArrowNavigator$
	  },
	  $BulletNavigatorOptions: {
		$Class: $JssorBulletNavigator$
	  }
	};

	var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

	/*#region responsive code begin*/

	var MAX_WIDTH = 1184;

	function ScaleSlider() {
		var containerElement = jssor_1_slider.$Elmt.parentNode;
		var containerWidth = containerElement.clientWidth;

		if (containerWidth) {

			var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

			jssor_1_slider.$ScaleWidth(expectedWidth);
		}
		else {
			window.setTimeout(ScaleSlider, 30);
		}
	}

	ScaleSlider();

	$Jssor$.$AddEvent(window, "load", ScaleSlider);
	$Jssor$.$AddEvent(window, "resize", ScaleSlider);
	$Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
	/*#endregion responsive code end*/
};


jssor_2_slider_init = function() {

	var jssor_2_options = {
	  $AutoPlay: 1,
	  $Idle: 3000,
	  $ArrowNavigatorOptions: {
		$Class: $JssorArrowNavigator$
	  },
	  $BulletNavigatorOptions: {
		$Class: $JssorBulletNavigator$
	  }
	};

	var jssor_2_slider = new $JssorSlider$("jssor_2", jssor_2_options);

	/*#region responsive code begin*/

	var MAX_WIDTH = 1184;

	function ScaleSlider() {
		var containerElement = jssor_2_slider.$Elmt.parentNode;
		var containerWidth = containerElement.clientWidth;

		if (containerWidth) {

			var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

			jssor_2_slider.$ScaleWidth(expectedWidth);
		}
		else {
			window.setTimeout(ScaleSlider, 30);
		}
	}

	ScaleSlider();

	$Jssor$.$AddEvent(window, "load", ScaleSlider);
	$Jssor$.$AddEvent(window, "resize", ScaleSlider);
	$Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
	/*#endregion responsive code end*/
};