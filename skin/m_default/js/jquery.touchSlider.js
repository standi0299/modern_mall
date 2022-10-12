/**
 * @param Object	settings	환경변수 오브젝트
 *      item_box        -   슬라이드 아이템 영역(null)
 *		roll			-	순환 (default true)
 *		flexible		-	유동 레이아웃 (default false)
 *		view			-	다중 컬럼 (default 1)
 *		speed			-	애니메이션 속도 (default 75)
 *		auto_roll		-	자동 슬라이드 (default false)
 *		auto_roll_term	-	자동 슬라이드 시간간격( default 5000 )
 *		range			-	넘김 판정 범위 (default 0.15)
 *		page			-	초기 페이지 (default 1)
 *		transition		-	CSS3 transition 사용 (default false)
 *		btn_prev		-	prev 버튼 (jQuery Object, default null)
 *		btn_next		-	next 버튼 (jQuery Object, default null)
 *		btn_play		-	play 버튼 (jQuery Object, default null)
 *		paging			-	page 버튼 (jQuery Object, default null)
 *		initComplete	-	초기화 콜백
 *		counter			-	슬라이드 콜백, 카운터
 *
 * @example

	$("#target").touchSlider({
		flexible : true
	});

*/

(function ($) {

	$.fn.touchSlider = function (settings) {

		settings.supportsCssTransitions = (function (style) {
			var prefixes = ['Webkit','Moz','Ms'];
			for(var i=0, l=prefixes.length; i < l; i++ ) {
				if( typeof style[prefixes[i] + 'Transition'] !== 'undefined') {
					return true;
				}
			}
			return false;
		})(document.createElement('div').style);

		settings = jQuery.extend({
			roll : true,
			flexible : false,
			btn_prev : null,
			btn_next : null,
			paging : null,
			speed : 75,
			view : 1,
			range : 0.15,
			page : 1,
			auto_roll : false,
			auto_roll_term : 5000,
			transition : false,
			initComplete : null,
			counter : null
		}, settings);

		var opts = [];
		opts = $.extend({}, $.fn.touchSlider.defaults, settings);

		return this.each(function () {

			var _this = this;

			$.fn.extend(this, touchSlider);
			this.opts = opts;
			this.init();

			$(window).bind("orientationchange resize", function () {
				setTimeout(function(){ _this.resize(_this); }, 200 );

			});
		});

	};

	var touchSlider = {

		init : function () {
			var _this = this;

			this._view = this.opts.view;
			this._speed = this.opts.speed;
			this._tg = $(this);
			this._list = this._tg.children();
			this._width = parseInt(this._tg.css("width"));
			this._height = parseInt(this._list.height());
			this._item_w = parseInt(this._list.css("width"));
			this._len = this._list.length;
			this._range = this.opts.range * this._width;
			this._pos = [];
			this._start = [];
			this._startX = 0;
			this._startY = 0;
			this._left = 0;
			this._top = 0;
			this._drag = false;
			this._scroll = false;
			this._btn_prev;
			this._btn_next;
			this.auto_roll_id = null;

			$(this)
					.unbind("touchstart", this.touchstart)
					.unbind("touchmove", this.touchmove)
					.unbind("touchend", this.touchend)
					.bind("touchstart", this.touchstart)
					.bind("touchmove", this.touchmove)
					.bind("touchend", this.touchend)

			this._tg.css({
				"position":"relative",
				"overflow":"hidden",
				//"width" : this._width,
				"height" : this._height
			});

			if(this.opts.flexible) this._item_w = this._width / this._view;
			if(this.opts.roll) this._len = Math.ceil(this._len / this._view) * this._view;

			var page_gap = (this.opts.page > 1 && this.opts.page <= this._len) ? (this.opts.page - 1) * this._item_w : 0;

			for(var i=0; i<this._len; ++i) {
				this._pos[i] = this._item_w * i - page_gap;
				this._start[i] = this._pos[i];
				this._list.eq(i).css({
					"float" : "none",
					"display" : "block",
					"position" : "absolute",
					"top" : "0",
					"left" : this._pos[i] + "px",
					"width" : this._item_w + "px"
				});
				if(this.opts.supportsCssTransitions && this.opts.transition) {
					this._list.eq(i).css({
						"-moz-transition" : "0ms",
						"-moz-transform" : "",
						"-ms-transition" : "0ms",
						"-ms-transform" : "",
						"-webkit-transition" : "0ms",
						"-webkit-transform" : "",
						"transition" : "0ms",
						"transform" : ""
					});
				}
			}

			if(this.opts.btn_prev && this.opts.btn_next) {
				this.opts.btn_prev.unbind("click").bind("click", function(e) {
					e.preventDefault();
					_this.autoRollChg( false );
					_this.animate(1, true);
				});
				this.opts.btn_next.unbind("click").bind("click", function(e) {
					e.preventDefault();
					_this.autoRollChg( false );
					_this.animate(-1, true);
				});
			}

			if(this.opts.paging) {
				var paging_A = this.opts.paging.find("a");

				paging_A.each(function() {
					$(this).bind("click", function(e) {
						e.preventDefault();
						_this.autoRollChg( false );
						_this.go_page( $(this).parent().index(), e);
					});
				});
			}

			if( this.opts.btn_play ){
				this.opts.btn_play.unbind("click").bind("click", function(e) {
					e.preventDefault();

					if( $(this).hasClass("on") )	_this.autoRollChg( false );
					else							_this.autoRollChg( true );
				})
			}

			if( this.opts.auto_roll ){
				this.autoRollChg( true );
			}

			/* 20141209 수정 : S */
			/* 2015-01-28 수정 by Boram : S */
			if( this._len == 1 ){
				if(this.opts.btn_prev && this.opts.btn_next){
					this.opts.btn_prev.unbind("click").bind("click", function(e) {
						e.preventDefault();
						alert("처음입니다");
					});
					this.opts.btn_next.unbind("click").bind("click", function(e) {
						e.preventDefault();
						alert("마지막입니다");
					});
				}
			}
			/* 2015-01-28 수정 by Boram  : E */
			else if( this._len < 2 ){
				if(this.opts.btn_prev && this.opts.btn_next){
					this.opts.btn_prev.unbind("click").remove();
					this.opts.btn_next.unbind("click").remove();
				}
				if( this.opts.paging )	this.opts.paging.remove();
				if( this.opts.paging ){
					_this.autoRollChg( false );
					this.opts.btn_play.remove();
				}

				$(this)
					.unbind("touchstart", this.touchstart)
					.unbind("touchmove", this.touchmove)
					.unbind("touchend", this.touchend);
			}
			/* 20141209 수정 : E */

			this.initComplete();
			this.counter();
		},

		autoRollChg : function ( ts ) {
			var _this = this;

			clearInterval( this.auto_roll_id );
			if( this.opts.btn_play )	this.opts.btn_play.removeClass("on").find("img").each(off);

			if( ts ){
				this.auto_roll_id = setInterval(function(){ _this.animate(-1, true); }, this.opts.auto_roll_term );
				if( this.opts.btn_play )	this.opts.btn_play.addClass("on").find("img").each(on);
			}
		},

		initComplete : function () {
			if(typeof(this.opts.initComplete) == "function") {
				this.opts.initComplete(this);
			}
		},
		/* 20141209 주현 수정 : S */
		resize : function (e) {
			if(e.opts.flexible) {
				var tmp_w = e._item_w,
					cont_height = 0,
					child_Elems = e._list.find("> *");

				e._width = parseInt(e._tg.css("width"));

				e._item_w = e._width / e._view;
				e._range = e.opts.range * e._width;


				for(var i=0; i<e._len; ++i) {
					e._pos[i] = e._pos[i] / tmp_w * e._item_w;
					e._start[i] = e._start[i] / tmp_w * e._item_w;
					e._list.eq(i).css({
						"left" : e._pos[i] + "px",
						"width" : e._item_w + "px"
					});
					if(this.opts.supportsCssTransitions && this.opts.transition) {
						e._list.eq(i).css({
							"-moz-transition" : "0ms",
							"-moz-transform" : "",
							"-ms-transition" : "0ms",
							"-ms-transform" : "",
							"-webkit-transition" : "0ms",
							"-webkit-transform" : "",
							"transition" : "0ms",
							"transform" : ""
						});
					}
				}

				for( var i=0;i<child_Elems.length;i++){
					if( cont_height < child_Elems.eq(i).height() )	cont_height = child_Elems.eq(i).height();
				}

				cont_height = child_Elems.height();
				e._height = parseInt(cont_height);
				this._tg.css({ "height" : e._height});

				var _this = this;

				// "productListSlide" 클래스명을 가지고 있을 경우
				if( $(_this).hasClass("productListSlide") && _this.opts.btn_prev && _this.opts.btn_next && _this._len > 1 ){
					var tempHeight = $(_this).find("figure").height();
					_this.opts.btn_prev.css("top", tempHeight / 2 );
					_this.opts.btn_next.css("top", tempHeight / 2 );
				}
			}

			this.counter();
		},
		/* 20141209 주현 수정 : E */

		touchstart : function (e) {
			if( e.type == "touchstart" && e.originalEvent.touches.length <= 1 ) {
				this.autoRollChg( false );

				this._startX = e.originalEvent.touches[0].pageX;
				this._startY = e.originalEvent.touches[0].pageY;
				this._scroll = false;

				this._start = [];
				for(var i=0; i<this._len; ++i) {
					this._start[i] = this._pos[i];
				}
			}
		},

		touchmove : function (e) {
			if( e.type == "touchmove" && e.originalEvent.touches.length <= 1) {
				this._left = e.originalEvent.touches[0].pageX - this._startX;
				this._top = e.originalEvent.touches[0].pageY - this._startY;
				var w = this._left < 0 ? this._left * -1 : this._left;
				var h = this._top < 0 ? this._top * -1 : this._top;

				if (w < h || this._scroll) {
					this._left = 0;
					this._drag = false;
					this._scroll = true;
				} else {
					if ( navigator.userAgent.indexOf("android 4.1") > -1 ) {
						e.stopPropagation();
					} else {
						e.preventDefault();
					}
					this._drag = true;
					this._scroll = false;
					this.position(e);
				}

				for(var i=0; i<this._len; ++i) {
					var tmp = this._start[i] + this._left;

					if(this.opts.supportsCssTransitions && this.opts.transition) {
						var trans = "translate3d(" + tmp + "px,0,0)";
						this._list.eq(i).css({
							"left" : "",
							"-moz-transition" : "0ms",
							"-moz-transform" : trans,
							"-ms-transition" : "0ms",
							"-ms-transform" : trans,
							"-webkit-transition" : "0ms",
							"-webkit-transform" : trans,
							"transition" : "0ms",
							"transform" : trans
						});
					} else {
						this._list.eq(i).css("left", tmp + "px");
					}

					this._pos[i] = tmp;
				}
			}
		},

		touchend : function (e) {
			if(e.type == "touchend" && e.originalEvent.touches.length <= 1) {
				if(this._scroll) {
					this._drag = false;
					this._scroll = false;
					return false;
				}

				this.animate(this.direction());
				this._drag = false;
				this._scroll = true;
			}
		},

		position : function (d) {
			var gap = this._view * this._item_w;

			if(d == -1 || d == 1) {
				this._startX = 0;
				this._start = [];
				for(var i=0; i<this._len; ++i) {
					this._start[i] = this._pos[i];
				}
				this._left = d * gap;
			} else {
				if(this._left > gap) this._left = gap;
				if(this._left < - gap) this._left = - gap;
			}

			if(this.opts.roll) {
				var tmp_pos = [];
				for(var i=0; i<this._len; ++i) {
					tmp_pos[i] = this._pos[i];
				}
				tmp_pos.sort(function(a,b){return a-b;});


				var max_chk = tmp_pos[this._len-this._view];
				var p_min = $.inArray(tmp_pos[0], this._pos);
				var p_max = $.inArray(max_chk, this._pos);


				if(this._view <= 1) max_chk = this._len - 1;

				if((d == 1 && tmp_pos[0] >= 0) || (this._drag && tmp_pos[0] > 0)) {
					for(var i=0; i<this._view; ++i, ++p_min, ++p_max) {
						this._start[p_max] = this._start[p_min] - gap;
						this._list.eq(p_max).css("left", this._start[p_max] + "px");
					}
				} else if((d == -1 && tmp_pos[max_chk] <= 0) || (this._drag && tmp_pos[max_chk] <= 0)) {
					for(var i=0; i<this._view; ++i, ++p_min, ++p_max) {
						this._start[p_min] = this._start[p_max] + gap;
						this._list.eq(p_min).css("left", this._start[p_min] + "px");
					}
				}
			} else {
				if(this.limit_chk()) this._left = this._left / 2;
			}
		},

		animate : function (d, btn_click) {
			if(this._drag || !this._scroll || btn_click) {
				var _this = this;
				var speed = this._speed;

				if(btn_click) this.position(d);

				var gap = d * (this._item_w * this._view);
				if(this._left == 0 || (!this.opts.roll && this.limit_chk()) ) gap = 0;

				this._list.each(function (i, el) {
					_this._pos[i] = _this._start[i] + gap;

					if(_this.opts.supportsCssTransitions && _this.opts.transition) {
						var transition = speed + "ms";
						var transform = "translate3d(" + _this._pos[i] + "px,0,0)";

						if(btn_click) transition = "0ms";

						$(this).css({
							"left" : "",
							"-moz-transition" : transition,
							"-moz-transform" : transform,
							"-ms-transition" : transition,
							"-ms-transform" : transform,
							"-webkit-transition" : transition,
							"-webkit-transform" : transform,
							"transition" : transition,
							"transform" : transform
						});
					} else {
						$(this).animate({"left": _this._pos[i] + "px"}, speed);
					}
				});

				this.counter();
			}
		},

		direction : function () {
			var r = 0;

			if(this._left < -(this._range)) r = -1;
			else if(this._left > this._range) r = 1;

			if(!this._drag || this._scroll) r = 0;

			return r;
		},

		limit_chk : function () {
			var last_p = parseInt((this._len - 1) / this._view) * this._view;
			return ( (this._start[0] == 0 && this._left > 0) || (this._start[last_p] == 0 && this._left < 0) );
		},

		go_page : function (i, e) {
			var crt = ($.inArray(0, this._pos) / this._view) + 1;
			var cal = crt - (i + 1);

			while(cal != 0) {
				if(cal < 0) {
					this.animate(-1, true);
					cal++;
				} else if(cal > 0) {
					this.animate(1, true);
					cal--;
				}
			}
		},

		get_page : function () {
			return {
				total : Math.ceil(this._len / this._view),
				current : ($.inArray(0, this._pos) / this._view) + 1
			}
		},

		chg_paging_state : function (){
			var presentIdx = this.opts.page - 1;
			this.opts.paging.children().removeClass("on").find("img").each(off);
			this.opts.paging.children().eq(presentIdx).addClass("on").find("img").each(on);
		},

		counter : function () {
			if($.inArray(0, this._pos) < 0) {
				this.init();
			}

			this.opts.page = this.get_page().current;

			this._list.removeClass("on");
			this._list.eq( this.opts.page - 1).addClass("on");

			if( this.opts.paging )	this.chg_paging_state();

			if(typeof(this.opts.counter) == "function") {
				this.opts.counter(this.get_page());
			}
		}

	};

})(jQuery);