/* ------------------------------------------------------------
 * Name      : common.js
 * Desc      : KDBi.TV (Moblie) 스크립트
 * Created   : 2015-05-08 by tttboram
 ------------------------------------------------------------ */

/* 이미지 on, off */
function on(){this.src = this.src.replace("_off","_on");}
function off(){this.src = this.src.replace("_on","_off");}

/* 주소창가리기 */
window.addEventListener('load', function() {
	setTimeout(scrollTo, 0, 0, 1);
}, false);

/* 20141212 수정 : S */
/* Main Vertial IScroll */
function mainIScrollInit(){
	/* iScroll Object */
	var myScroll = [];

	$(".js-vScroll").each(function(idx){
		$(this).css("overflow", "hidden");

		var $overallBox = $(this),
			$slideInnerWrap = $overallBox.find(".innerWrap"),
			$btnArrow = $overallBox.parent().find(".btnNext");

		this.id = "iScroll_" + idx;

		var iscroll = new IScroll("#"+this.id, {
			scrollX: true,
			scrollY: false,
			mouseWheel: true,
			preventDefaultException : {tagName : /^(INPUT|TEXTAREA|BUTTON|SELECT|A)$/}
		});

		myScroll[this.id]= iscroll;

		myScroll[this.id].on("scrollEnd", function(){
			chkScrollPosX( this.x );
		});

		$btnArrow.on({
			click : function(e){
				e.preventDefault();

				if( $(this).hasClass("reverse") ){
					iscroll.scrollToElement( $slideInnerWrap.find("ul li:first-child")[0] );
				}else{
					iscroll.scrollToElement( $slideInnerWrap.find("ul li:last-child")[0] );
				}
			}
		});

		//2015-01-12 ttt
		$slideInnerWrap.find("ul li").on({
			click : function(e){
				e.preventDefault();
				var $showUl = $(this).index();
				var $friendDiv = $(this).parent().parent().parent().parent().parent().children().attr("class");

				$("."+$friendDiv).find(">ul").hide();
				$("."+$friendDiv).find(">ul").eq($showUl).show();
			}
		})

		function chkScrollPosX( posX ){
			if( $overallBox.width() - $slideInnerWrap.width() >= 0 ){
				$btnArrow.removeClass("reverse");
				return false;
			}

			if( posX == $overallBox.width() - $slideInnerWrap.width() )	$btnArrow.addClass("reverse");
			else 														$btnArrow.removeClass("reverse");
		}

		$(window).resize(function(){
			var $items = $slideInnerWrap.find("> ul > li"),
				t_width = 0,
				tgScroll = iscroll;

			if( $overallBox.hasClass("type_fix") ){
				var ratio = parseInt( $overallBox.data("ratio").split("%")[0] ) / 100 ;
				$items.width( $overallBox.width() * ratio );

				for( var i=0;i<$items.length;i++)
				t_width += $items.eq(i).outerWidth();

				$slideInnerWrap.css("width", t_width);
				tgScroll.refresh();
			}
		});
	});

	$(window).trigger("resize");
}
/* 20141212 수정 : E */

/* 전체 메뉴 제어 */
function allMenuCtrl(){
	var $body = $("body"),
		$leftMenu = $("#all_menu"),
		$contWrap = $("#wrap"),
		$searchWrap = $("#wrap .search_wrap"),
		$location = $(".location"),
		$header = $("#wrap header");

	$("body").width( $(window).width());
	$("#all_menu").height( $(window).height() );

	// 전체 메뉴 비활성화 이벤트
	$contWrap.on({
		click: function(e){
			if( $body.hasClass("menu_on") ){
				$body.removeClass("menu_on");
			}
		}
	});

	// 전체 메뉴 활성화
	$(".btnAllMenu").on({
		click : function(e){
			e.preventDefault();
			e.stopPropagation();
			$body.addClass("menu_on");
		}
	});

	// 검색 활성화 이벤트
	$("header .btnSearch").on({
		click : function(e){
			e.preventDefault();
			$contWrap.addClass("dimed_on");
			$searchWrap.addClass("on");
		}
	});

	// 검색 취소 이벤트
	$("header .cancel").on({
		click : function(e){
			e.preventDefault();
			$contWrap.removeClass("dimed_on");
			$searchWrap.removeClass("on");
		}
	});

	$(window).resize(function() {
		$("body").width( $(window).width() );
		$("#all_menu").height( $(window).height() );
		$(".left_menu_on").height($(window).height());
	});
}

/* 전체 메뉴 Nav 제어 */
function navCtrl( init ){
	var $nav = $("#all_menu .allMenuList"),
		$1dep = $nav.find("> article > ul > li"),
		$1depA = $1dep.find("> a"),
		$2dep = $nav.find("> article > ul > li > ul > li");

	$1dep.removeClass("on");

	if( init == true ){
		return false;
	}else{
		$1depA.each(function(){
			if( $(this).siblings("ul").length > 0 ){
				$(this).addClass("subTrue");
			}
		});

		$1depA.on({
			click : function(e){
				var $parent = $(this).parent();
				if( $parent.find("> ul").length > 0 ){
					e.preventDefault();
					if ( $parent.hasClass("on") )	$parent.removeClass("on");
					else							$parent.addClass("on");
				}
			}
		});
	}
}

/* FAQ 제어 */
function faqCtrl(){
	$(".faqList > li").on({
		click : function(e){
			e.preventDefault();
			if($(this).hasClass("on")){
				$(this).removeClass("on");
			}else{
				$(this).parent().find("> li").removeClass("on");
				$(this).addClass("on");
			}
		}
	});
}

/* 현재 위치(Location) 영역 제어 */
function locMenuCtrl(){
	var winHeight = $(window).height(),
		headHeight = 0,
		$subMenu = $(".location .subMenu");

	$(window).resize(function(){
		winHeight = $(window).height();
		headHeight = $("#wrap header").outerHeight(true) + $(".location").outerHeight(true);
		$subMenu.css("max-height", winHeight - headHeight);
	});

	$(window).trigger("resize");

	// 현재 위치 표시 부분 클릭 이벤트
	$(".location").on({
		click : function(e){
			e.preventDefault();
			if($(".location").find("> .subMenu").size() > 0){
				if($(this).hasClass("on"))	$(this).removeClass("on");
				else						$(this).addClass("on");
			}
		}
	});

	// 2depth 존재시 아이콘(+) 표시
	$(".location .subMenu > li > a").each(function(){
		if( $(this).siblings("ul").length > 0 ){
			$(this).addClass("subTrue");
		}
	});

	// 링크 클릭시 서브메뉴 제어
	$(".location .subMenu a").on({
		click : function(e){
			e.stopPropagation();

			if( $(this).hasClass("subTrue") ){
				e.preventDefault();

				var $parentLI = $(this).parent("li"),
					$2depUL = $(this).siblings("ul");

				if( $parentLI.hasClass("on") ){
					$parentLI.removeClass("on");
					$2depUL.removeClass("on");
				}
				else{
					$parentLI.siblings().removeClass("on");
					$parentLI.addClass("on");
					$2depUL.addClass("on");
				}
			}else{
				$(".location").removeClass("on");
			}
		}
	});
}

/* 메인 - 공지사항 롤링 제어 */
function mainNoticeRolling(options){
	var opts = {
		overallBox : null,
		btn_prev : null,
		btn_next : null,
		roll_speed : 500,
		autoMove : false,
		autoRollTerm : 5000
	}

	opts = $.extend({}, opts, options );

	var that = {
		$overallBox : $(opts.overallBox),
		$slideBox : $(opts.overallBox + " > ul"),
		$slideItem : $(opts.overallBox + " > ul > li"),

		$btnPrev : opts.btn_prev,
		$btnNext : opts.btn_next,

		$obj : null,
		itemNum : $(opts.overallBox + " > ul > li").length,

		itemHeight : $(opts.overallBox + " > ul").outerHeight(),

		presentIdx : 0,

		rollSpeed : opts.roll_speed,

		aniState : false,
		autoState : null,
		autoRollTerm : opts.autoRollTerm,

		moveSlide : function( targetIdx ){
			var direction = ( this.presentIdx < targetIdx ) ? "up" : "down",
				targetIdx = ( targetIdx + this.itemNum ) % this.itemNum;

			if( targetIdx == this.presentIdx || this.aniState == true )	return false;

			this.aniState = true;

			if( direction == "up" ){
				this.$obj[this.presentIdx].animate({"top" : -this.itemHeight}, this.rollSpeed );
				this.$obj[targetIdx].css("top", this.itemHeight).animate({"top" : 0}, this.rollSpeed, function(){that.aniState = false;});
			}else{
				this.$obj[this.presentIdx].animate({"top" : this.itemHeight}, this.rollSpeed);
				this.$obj[targetIdx].css("top", -this.itemHeight).animate({"top" : 0}, this.rollSpeed, function(){that.aniState = false;});
			}

			this.presentIdx = targetIdx;
		}
	}

	that.init = function(){

		this.$slideBox.css({ "position" : "relative", "overflow" : "hidden" });

		this.$obj = new Array();

		for(var i=0; i<this.itemNum; i++){
			this.$obj[i] = this.$slideItem.eq(i);

			this.$obj[i].css({
				"float" : "none",
				"position" : "absolute",
				"top" : this.itemHeight,
			});
		}

		// 첫번째 컨텐츠 활성화
		this.$obj[0].css("top", 0);

		// 이전 버튼 클릭 이벤트
		this.$btnPrev.on({
			click : function(e){
				e.preventDefault();
				that.moveSlide( that.presentIdx - 1 );
			}
		});

		// 다음 버튼 클릭 이벤트
		this.$btnNext.on({
			click : function(e){
				e.preventDefault();
				that.moveSlide( that.presentIdx + 1 );
			}
		});

		// 자동 슬라이드 제어
		if( opts.autoMove == true && this.itemNum > 1 ){
			// 자동 슬라이드 시작
			that.autoState = setInterval(function(){ that.moveSlide( that.presentIdx + 1 ); }, that.autoRollTerm);
		}

	}

	that.init();

	return that;
}

/* 일반 탭 제어 */
function generalTabCtrl( targetBox ){
	var $targetBox = $(targetBox),
		$tabList = $targetBox.find(".tabList > li"),
		$contList = $targetBox.find(".tabCont");

	function activateTabChg( targetIdx ){
		$tabList.removeClass("on");
		$tabList.eq(targetIdx).addClass("on");

		$contList.removeClass("on");
		$contList.eq(targetIdx).addClass("on");

		$(window).resize();
	}

	$tabList.find("a").on({
		click : function(e){
			e.preventDefault();
			activateTabChg( $(this).parent().index() );
		}
	});
}

/* 20141209 주현 수정 : S */
/* dropDownMenu(2depth) 제어 */
function dropDownMenuCtrl( dep1, dep2 ){
	var dep1Idx = ( dep1 > -1 ) ? dep1 : -1,
		dep2Idx = ( dep2 > -1 ) ? dep2 : -1;

	var $overallBox = $(".dropDownArea"),
		$dep1Area = $overallBox.find("> li").eq(0),
		$dep1Title = $dep1Area.find("> a"),
		$dep1subUL = $dep1Area.find(".subMenu"),
		$dep2Area = $overallBox.find("> li").eq(1),
		$dep2Title = $dep2Area.find("> a"),
		$dep2subUL = $dep2Area.find(".subMenu");

	maxHeightCtrl();

	chgDropDownState();

	$dep1Title.off("click").on({
		click : function(e){
			e.preventDefault();

			$dep2Area.removeClass("on");

			if( $dep1Area.hasClass("on") ){
				$dep1Area.removeClass("on");
				$dep1subUL.removeClass("on");
			}
			else{
				$dep1Area.addClass("on");
				$dep1subUL.addClass("on");
			}
		}
	});

	$dep1subUL.find("a").off("click").on({
		click : function(e){
			e.preventDefault();
			dep1Idx = $(this).parent().index();
			dep2Idx = -1;

			chgDropDownState();
		}
	});

	$dep2Title.off("click").on({
		click : function(e){
			e.preventDefault();

			$dep1Area.removeClass("on");

			if( $dep2Area.hasClass("on") )	$dep2Area.removeClass("on");
			else							$dep2Area.addClass("on");
		}
	});

	$dep2subUL.find("a").off("click").on({
		click : function(e){
			dep2Idx = $(this).parent().index();
			chgDropDownState();
		}
	});


	function chgDropDownState(){
		var dep1Txt = $dep1subUL.eq(0).find("li").eq( dep1Idx ).find("a").text(),
			dep2Txt = $dep2subUL.eq( dep1Idx ).find("li").eq( dep2Idx ).find("a").text();

		if( dep1Idx < 0 )	dep1Txt = "ALL";
		if( dep2Idx < 0 )	dep2Txt = "ALL";

		$dep2subUL.eq( dep1Idx ).addClass("on").siblings().removeClass("on");

		$dep1Title.text( dep1Txt );
		$dep2Title.text( dep2Txt );

		$dep1Area.removeClass("on");
		$dep2Area.removeClass("on");
	}

	function maxHeightCtrl(){
		var winHeight = $(window).height(),
			headHeight = 0,
			$subMenu = $(".dropDownArea .subMenu");

		$(window).resize(function(){
			winHeight = $(window).height();
			$subMenu.css("max-height", winHeight * 0.6 );
		});

		$(window).trigger("resize");
	}
}
/* 20141209 주현 수정 : E */

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

			/* 20141205 주현 수정 : S */
			var selectedElem = ( $slideArea.find("ul li.on").prev().length > 0 ) ? $slideArea.find("ul li.on").prev()[0] : $slideArea.find("ul li:first-child")[0];
			tab_iScroll[$slideArea[0].id].scrollToElement( selectedElem );
			/* 20141205 주현 수정 : E */

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

function BathZineMenuCtrl(){
	$(".bathZineSelect").on({
		click : function(e){
			e.preventDefault();

			if($(this).hasClass("on")){
				$(this).removeClass("on");
			}else{
				$(this).addClass("on");
			}
		}
	});
}

/* 20141209 주현 수정 : S */
/* remodeling 제어 */
function remodelingCtrl(){
	if( $(".productListArea .productListSlide").length > 0 ){
		$(".productListArea").each(function(){
			var $targetSlide = $(this).find(".productListSlide");

			$targetSlide.touchSlider({
				flexible : true,
				btn_prev : $(this).find(".btnPrev"),
				btn_next : $(this).find(".btnNext")
			});

			$(window).trigger("resize");
		});

	}

	$(".remodelingWrap > article > a.productViewCate").on({
		click : function(e){
			e.preventDefault();
			if($(this).parent().hasClass("on")){
				$(this).parent().removeClass("on");
			}else{
				$(this).parent().parent().find("> article").removeClass("on");
				$(this).parent().addClass("on");

				setTimeout(function(){
					$(window).trigger("resize");
				}, 50);
			}
		}
	});
}
/* 20141209 주현 수정 : E */

function slideListBox(){
	$(".slideListBox li").on({
		click : function(e){
			e.preventDefault();

			var target = $(".slideListBox li").index(this);

			$(".slideListBox li").removeClass("on");
			$(this).addClass("on");

			$(".slideContBox .composition > li").removeClass("on");
			$(".slideContBox .composition > li:eq("+ target +")").addClass("on");
		}
	});
}

/* 20141211 수정 : S */
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
/* 20141211 수정 : E */
function minHeightCtrl(){
	$(window).resize(function(){
		var totalHeight = $("#container").outerHeight() + $("#wrap > footer").outerHeight(),
			extraHeight = $(window).height() - totalHeight;

		$("#container").css("min-height", $("#container").height() + extraHeight );
	});

	$(window).trigger("resize");
}

function smartLet(){
	$("#allAbility").hide();

	$(".smartletTab li a").click(function(){
		$(".smartletTab li").removeClass("on");
		$(this).parent("li").addClass("on");
		$(".ability").hide();
		$($(this).attr("href")).show();

		return false;
	});

	$(".smartletImgArea li a").click(function(){
		$this = $(this);
		var changeImg = $this.children("img").attr("src");
		$(".smartletImgArea span img").attr("src", changeImg);

		return false;
	});
}

/* 기능 초기화 */
$(window).load(function(){
	// 전체 메뉴, 상단 검색 메뉴 제어
	allMenuCtrl();

	// 전체 메뉴 Nav 제어
	navCtrl();

	// FAQ 제어
	faqCtrl();

	// 2depth Location 제어
	locMenuCtrl();

	/* 20141212 수정 : S */
	/* Main Vertial IScroll */
	mainIScrollInit();
	/* 20141212 수정 : E */

	// 2depth Location 제어
	BathZineMenuCtrl();

	// DropDownMenu 제어
	//if( $(".dropDownArea").length > 0 )	dropDownMenuCtrl();

	// Tab 가로 슬라이드 제어
	if( $(".tabSlideArea").length > 0 )		tabHozizonSlideCtrl();

	//DropDownArea 영역 제어 (ex :  dropDownMenuCtrl( depth1, depth2) )
	dropDownMenuCtrl(0,0);

	// remodelingCtrl
	remodelingCtrl();

	// 최소높이 제어
	minHeightCtrl();

	//스마트렛 피쳐
	smartLet();

	//홍보관
	$(".daelimPRVisual .visualList").touchSlider({
		roll : false,						//2015-02-04 옵션추가
		flexible : true,
		auto_roll : false,
		auto_roll_term : 5000,
		btn_prev : $(".daelimPRVisual .btnPrev"),
		btn_next : $(".daelimPRVisual .btnNext"),
		paging : $(".daelimPRVisual .dotList")
	});

	//main tab - 욕실아이템
	$(".bathItem>ul").hide();
	$(".bathItem>ul").first().show();
	//main tab - 욕실리모델링
	$(".remodelItem>ul").hide();
	$(".remodelItem>ul").first().show();
});