$(function(){

    //메인비주얼
    (function mainVisualSlider(){
      var $wrapSliderBn = $('#mainVisual');
      if( $wrapSliderBn.length < 1 ) return;
      var $sliderBn = $wrapSliderBn.find('ul');
      var $prevBtn = $("<button />").attr("type", "button").addClass("btn_slide_prev").append("Prev");
      var $nextBtn = $("<button />").attr("type", "button").addClass("btn_slide_next").append("Next");
      var opts = {
         mode:'fade',
         speed: 1700,
         pause: 4000,
         infiniteLoop: true,
         auto: true,
         autoHover: true,
         controls: false,
         autoControl : true,
         useCSS: false,
         pager: true,
         easing: 'easeInOutQuint'
      };
      $wrapSliderBn.find('.box_btn_move_control').append($prevBtn, "\r\n", $nextBtn);
      var bnnrSlider = $sliderBn.bxSlider(opts);
      $prevBtn.on("click", function(evt){ bnnrSlider.goToPrevSlide(); });
      $nextBtn.on("click", function(evt){ bnnrSlider.goToNextSlide(); });
    })();
    
    //메인 베스트 아이템
    (function mainGallerySlider(){
      var $wrapSliderBn = $('#bestSlide');
      if( $wrapSliderBn.length < 1 ) return;
      var $sliderBn = $wrapSliderBn.find('ul');
      var $prevBtn = $("<button />").attr("type", "button").addClass("btn_slide_prev").append("Prev");
      var $nextBtn = $("<button />").attr("type", "button").addClass("btn_slide_next").append("Next");
      var opts = {
		  speed: 500,
          controls: false,       
          auto:true,
		  autoHover: true,
		  slideWidth: 340,
		  minSlides: 1,
          maxSlides: 5,
		  moveSlides: 1,
          slideMargin: 30,
          autoControls: true,
		  easing: 'easeInOutQuint'
	  };
      $wrapSliderBn.find('.box_btn_move_control').append($prevBtn, "\r\n", $nextBtn);
      var bnnrSlider = $sliderBn.bxSlider(opts);
      $prevBtn.on("click", function(evt){ bnnrSlider.goToPrevSlide(); });
      $nextBtn.on("click", function(evt){ bnnrSlider.goToNextSlide(); });
    })();

	/*// 후가공 선택
	(function addFinishingOption () {
		var NewContent = '<div class="finishing_option_list">테스트</div>'
		$('.option_thumb').click(function () {
			var $this = $(this);
			var $fhOption = $this.closest('.pd_detail_finishing_wrap').next('.finishing_option_list');
			var $test = $this.closest('.pd_detail_finishing_wrap');

			if ($this.length) {
				$fhOption.toggle();
			} else {
				$(NewContent).insertAfter($test);
			}
		});
	})();
	*/
	// 후가공 선택
	(function addFinishingOption () {
		$('.option_thumb').click(function () {
			$(this).toggleClass('active');
			var id = $(this).data('id');
			$('.finishing_option_list[data-id="'+id+'"]').slideToggle('1000', "swing", function() {
				//console.log($(this).is(':visible'));
				
				//선택 해제면 값을 초기화.
				if (!$(this).is(':visible')) {
					afterOption(id, $(this)); 
				}
			});
			
			//"선택된 후가공 없음" 숨기기.
			$("#after_opt").addClass('quick_option_list');
			
			// 우측
			$('.quick_option_list[data-id="'+id+'"]').slideToggle('1000', "swing", function() {								
			});
		});
	})();

	// 템플렛 좌측 메뉴
	(function lnbSide(){
		$(".tm_2dpul").hide();
		
		$('.temp_menu li a').click(function(){
			$(this).toggleClass('active').next('ul').slideToggle();
			$(this).parent().siblings().children('ul').slideUp();
			$(this).parent().siblings().children('.active').removeClass('active');
			return false;
			/*
			
			var checkElement = $(this).next();
			//checkElement.slideToggle('normal');
			
			if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
				return false;
			}
			if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
				$('.temp_menu ul:visible').not(checkElement.parent().parent()).slideUp('normal');
				checkElement.slideDown('normal');
				return false;
			}
			*/
		});
	})();

	
	//상단 메뉴 스크롤 발생시 고정
    $(document).ready(function() {
        var stickyNav = function(){
			var scrollTop = $(window).scrollTop();
			if (scrollTop > 0) { 
				$('body').addClass('sticky');
				$('#header_idg').addClass('fixed');
				$('#container_idg').css('padding-top', '92px');
			} else {
				$('body').removeClass('sticky');
				$('#header_idg').removeClass('fixed');
				$('#container_idg').css('padding-top', '0');
			}
		};
		stickyNav();

		$(window).scroll(function() {
			stickyNav();
		});
    });

});


/* 파일 업로드 */
function overLayer_on(id) {
	document.getElementById(id).style.display = "block";
	//$('body').css('overflow','hidden');
	var version = GetVersionOfIE();
	if (version == "N/A") {
		$('body').css('overflow','hidden');
	}
	else {
		if (parseFloat(version) > 11) {
			$('body').css('overflow','hidden');
		}
	}
}

function overLayer_off(id) {
	document.getElementById(id).style.display = "none";
	//$('body').css('overflow','visible');
	var version = GetVersionOfIE();
	if (version == "N/A") {
		$('body').css('overflow','visible');
	}
	else {
		if (parseFloat(version) > 11) {
			$('body').css('overflow','visible');
		}
	}
}