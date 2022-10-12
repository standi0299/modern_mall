/* ------------------------------------------------------------
 * Name      : common.js
 * Desc      : 추억을 만지다. 미오디오(mobile) 스크립트
 * Created   : 2015-08-11 by tttboram@naver.com
 ------------------------------------------------------------ */

/* 이미지 on, off */
function on(){this.src = this.src.replace("_off","_on");}
function off(){this.src = this.src.replace("_on","_off");}

/* 주소창가리기 */
window.addEventListener('load', function() {
	setTimeout(scrollTo, 0, 0, 1);
}, false);

/* 가로탭 슬라이드 제어*/
function tabHozizonSlideCtrl(){
	var tab_iScroll = [],
		tabOverallWidth = [],
		tabUnitWidth = [];

	$(".tabSlideArea").each(function(idx){

		var $overallBox = $(this),
			$slideArea = $overallBox.find(".slideArea"),
			$slideInnerWrap = $overallBox.find(".slideArea .inner-wrap"),
			$slideUL = $slideArea.find("ul"),
			$slideLI = $slideArea.find("ul li"),
			$btnPrev = $overallBox.find(".btnPrev"),
			$btnNext = $overallBox.find(".btnNext");

		init();

		function chkScrollPosX( posX ){
			$btnPrev.removeClass("on");
			$btnNext.removeClass("on");

			if( $overallBox.width() - $slideInnerWrap.width() > 0 )	return false;

			if( posX != 0)												$btnPrev.addClass("on");
			if( posX != $overallBox.width() - $slideInnerWrap.width() )	$btnNext.addClass("on");
		}

		function init(){
			var totalWidth = 0;

			$slideArea[0].id = "iScroll_" + idx;
			tabUnitWidth[$slideArea[0].id] = new Array();

			for(var i=0; i < $slideLI.length; i++){
				tabUnitWidth[$slideArea[0].id][i] = $slideLI.eq(i).outerWidth(false) + 18;
				$slideLI.eq(i).width( tabUnitWidth[$slideArea[0].id][i] );
				totalWidth += $slideLI.eq(i).outerWidth(true);
			}

			$slideInnerWrap.width( totalWidth );

			$slideArea.css({
				"width" : $overallBox.width(),
				"position" : "relative",
				"overflow" : "hidden"
			});

			var iscroll = new IScroll("#"+$slideArea[0].id, {
				scrollX: true,
				scrollY: false,
				mouseWheel: true,
				preventDefaultException : {tagName : /^(INPUT|TEXTAREA|BUTTON|SELECT|A)$/}
			});

			tabOverallWidth[$slideArea[0].id] = totalWidth;
			tab_iScroll[$slideArea[0].id] = iscroll;
			chkScrollPosX( tab_iScroll[$slideArea[0].id].x );

			tab_iScroll[$slideArea[0].id].on("scrollEnd", function(){
				chkScrollPosX( this.x );
			});

			var selectedElem = ( $slideArea.find("ul li.on").prev().length > 0 ) ? $slideArea.find("ul li.on").prev()[0] : $slideArea.find("ul li:first-child")[0];
			tab_iScroll[$slideArea[0].id].scrollToElement( selectedElem );

			$btnPrev.on({
				click : function(e){
					e.preventDefault();
					tab_iScroll[$slideArea[0].id].scrollToElement( $slideArea.find("ul li:first-child")[0] );
				}
			});

			$btnNext.on({
				click : function(e){
					e.preventDefault();
					tab_iScroll[$slideArea[0].id].scrollToElement( $slideArea.find("ul li:last-child")[0] );
				}
			});

			resizeChk();
		}

		function resizeChk(){
			var boxWidth = $overallBox.width(),
				extraX = $overallBox.width() - tabOverallWidth[$slideArea[0].id],
				extraXUnit = extraX / $slideLI.length,
				totalWidth = 0;

			$slideArea.width( $overallBox.width() );

			if( extraX <= 0 )	extraXUnit = 0;

			for(var i=0; i < $slideLI.length; i++){
				$slideLI.eq(i).width( tabUnitWidth[$slideArea[0].id][i] + extraXUnit );
				totalWidth += $slideLI.eq(i).outerWidth(true);
			}

			$slideInnerWrap.width( totalWidth );

			tab_iScroll[ $slideArea[0].id ].refresh();
			chkScrollPosX( tab_iScroll[$slideArea[0].id].x );
		}

		$(window).resize(function(){
			resizeChk();
		});
	});
}

// 레이어팝업 열기
function openLayerPop( targetLayerName ){
	var $targetLayerPop = $(targetLayerName),
		$btnCloseLayer = $targetLayerPop.find(".btnClose");

	// 레이어 닫기 버튼 이벤트 등록
	$btnCloseLayer.off("click");
	$btnCloseLayer.on({
		click : function(e){
			e.preventDefault();

			closeLayerPop( targetLayerName );
		}
	});

	$targetLayerPop.addClass("on");
}

// 레이어팝업 닫기기
function closeLayerPop( targetLayerName ){
	var $targetLayerPop = $(targetLayerName);

	$targetLayerPop.removeClass("on");
}

function minHeightCtrl(){
	$(window).resize(function(){
		var totalHeight = $("#container").outerHeight() + $("#wrap > footer").outerHeight(),
			extraHeight = $(window).height() - totalHeight;

		$("#container").css("min-height", $("#container").height() + extraHeight );
	});

	$(window).trigger("resize");
}

/* 기능 초기화 */
$(window).load(function(){
	/* Main Vertial IScroll */
	//mainIScrollInit();

	// Tab 가로 슬라이드 제어
	if( $(".tabSlideArea").length > 0 )		tabHozizonSlideCtrl();

	// 최소높이 제어
	minHeightCtrl();

	$(".layerPop").css({"marginTop": "'-'+(window.innerHeight / 2)"});
});

$(window).resize(function() {
	var $winWidth = window.innerWidth;
	var $winHeight = window.innerHeight;
	var $popHeight = $(".layerPop").height();
	//alert($popHeight);
	if($winWidth > $winHeight){
		//alert("가로");
		$(".layerPop").css({"marginTop": "'-'+($winHeight / 2)"});
		//alert($(window).height());
	}else{
		//alert("세로");
		$(".layerPop").css({"margin": "'-'+($winHeight / 2) 0 0 0"});
		//alert($(window).height());
	}
});

//sideNav
$(document).ready(function(){
	$("#sideNav").hide();
	$(".navBtn").on({
		click : function(e){
			e.preventDefault();
			$("#sideNav").show();
			$("#wrap").addClass("dimed_on");
		}
	});
	$("#sideNav h1 span").on({
		click : function(e){
			e.preventDefault();
			$("#sideNav").hide();
			$("#wrap").removeClass("dimed_on");
		}
	});

	//엘라스틱게시판(공지사항,자주묻는질문)
	$(".infoArea .infoList dl dd").slideUp();
	$(".infoArea .infoList dl dt").on({
		click : function(e){
			e.preventDefault();
			if($(this).hasClass("open")){
				$(".infoArea .infoList dl dt").removeClass("open");
				$(".infoArea .infoList dl dd").slideUp();
			}else{
				$(".infoArea .infoList dl dt").removeClass("open");
				$(".infoArea .infoList dl dd").slideUp();
				$(this).next().slideDown();
				$(this).addClass("open");
			}
		}
	});
});