// onoff extend
(function($) {
	$.fn.onoff = function(n, on, off) {
		var n = n;
		var on = (!on) ? "On." : on + ".";
		var off = (!off) ? "Off." : off + ".";
		$(this).each(function(i) {
			this.src = (this.src).replace(on, off);
			if (i == n) {
				this.src = (this.src).replace(off, on);
			};
		});
	};
})(jQuery);

// jQuery Extend addIdx : dstyle
(function($){$.fn.addIdx = function(s){var s=(!s)?"idx":s;$(this).each(function(n,m){m[s] = n;});return this;};})(jQuery);
// jQuery Extend unClick : dstyle
(function($){$.fn.unclick = function(){this.each(function(n,m){m.onclick=function(){return false;}});return this;};})(jQuery);
//jQuery Extend rebind : dstyle
(function($){$.fn.rebind = function(e,f){this.unbind(e).bind(e,f);return this;};})(jQuery);

function strowCarousel(objDiv,h){
	if(!objDiv.get(0)) return false;
	jQuery(function(){
		// object setting
		var obj = {
				id:objDiv.get(0).id,
				div:objDiv,
				ul:objDiv.find(">ul"),
				li:objDiv.find(">ul>li"),
				first:objDiv.find(">ul>li")
			};
		obj.li.css({"float":"left"}); // 정상적인 레이아웃을 위해 미리 셋팅(강제)
		// clone object setting
		var cbj = {id:"carouselClone"+obj.id};
		obj.div.append("<ul id='"+cbj.id+"' style='display:none;'></ul>");
		var cc = jQuery("#"+cbj.id);
		obj.li.clone().appendTo(cc);
		jQuery.extend(cbj,{ul:cc,li:cc.find(">li")});
		cc=null;
		
		//default value setting
		var val = {
				length:obj.li.length,
				speed:500,
				viewitem:1,
				moveitem:1,
				itemWidth:(obj.first.outerWidth(true)),
				itemHeight:(obj.first.outerHeight(true)),
				prevBtn:null,
				nextBtn:null,
				stopBtn:null,
				startBtn:null,
				paraItem:null,
				interval:true,
				intervalStop:false,
				intervalTime:3000,
				create:function(){}, //callback function
				prevClick:function(){},
				nextClick:function(){},
				moveStart:function(){},
				moveEnd:function(){},
				itemHover:function(){},
				href:null,
				idx:null,
				caseTop:"0px",
				flick:false,
				btnView:"always" // always or indexed(현재 인덱스에 따라 좌우버튼 보이고 안보이고)
			};
		jQuery.extend(val,h);
		
		var valAdd = {
				length:(val.href) ? val.href.length : val.length,
				idx:((val.idx==null)?val.moveitem:val.idx),
				use:true,
				viewWidth:(val.itemWidth*val.viewitem),
				ulWidth:(val.itemWidth*(val.viewitem*3)),
				ulLeft:((val.itemWidth*val.moveitem)*-1),
				count:0,
				intervalTime:(val.intervalTime/100),
				hover:false
		};
		jQuery.extend(val,valAdd);
		valAdd=h=null; // closer (메모리 누수방지)
		
		
		// function setting
		var fn = {
				start:function(){
					fn.resetCase();
					fn.resetDOM();
					val.create.call(null,obj,val);
				},
				hrefStart:function(){
					fn.resetCase();
					cbj.ul.empty();
					jQuery(val.href).each(function(hrefNum,hrefUrl){
						var li = document.createElement("li");
						jQuery(li).css({"float":"left","margin":"0","padding":"0"});
						jQuery(li).load(hrefUrl,function(){
							cbj.ul.append(li);
							if((val.href.length-1)==hrefNum){
								jQuery.extend(cbj,{li:cbj.ul.find("li")});
								fn.resetDOM();
							};
						});
					});
				},
				prevIdx:function(num){
					return (((num%val.length)-(val.moveitem*2))+val.length)%val.length;
				},
				nextIdx:function(num){
					return num%val.length;
				},
				prevMove:function(){
					if(val.use){
						val.count = 0;
						val.use = false;
						val.moveStart.call(null,obj);
						obj.ul.animate({"left":"0"},val.speed,function(){
							fn.resetDOM("prev");
							val.use = true;
							val.moveEnd.call(null,obj,val);
						});
					};
				},
				nextMove:function(){
					if(val.use){
						val.count = 0;
						val.use = false;
						val.moveStart.call(null,obj);
						obj.ul.animate({"left":(val.ulLeft*2)+"px"},val.speed,function(){
							fn.resetDOM("next");
							val.use = true;
							val.moveEnd.call(null,obj,val);
						});
					};
				},
				resetDOM:function(type,num){
					type = (!type) ? "prev" : type ;
					var setArr = [];
					if(num==0 || num){
						var n = num;
					}else{
						var n = val.idx;
					};
					for(i=0;i<(val.viewitem*3);i++){
						setArr.push(fn[type+"Idx"](n+i));
					};
					val.idx = setArr[val.moveitem];
					obj.ul.empty();
					$j(setArr).each(function(n,nm){
						cbj.li.eq(nm).clone().appendTo(obj.ul);
					});
					obj.ul.css({"left":val.ulLeft+"px"});
					if(val.interval){
						obj.ul.find("li").on("mouseover focusin",function(){
							val.count = 0;
							val.hover = true;
							val.itemHover.call(null,obj);
						});
						obj.ul.find("li").on("mouseout focusout",function(){
							val.count = 0;
							val.hover = false;
						});
						
						if(val.paraItem){
							val.paraItem.rebind("mouseover focusin",function(){
								val.count = 0;
								val.hover = true;
								val.itemHover.call(null,obj);
							});
							val.paraItem.rebind("mouseout focusout",function(){
								val.count = 0;
								val.hover = false;
							});
						};
					};
					if(val.btnView=="indexed"){
						if(val.prevBtn){
							val.prevBtn.show();
						}
						if(val.nextBtn){
							val.nextBtn.show();
						}
						if(val.prevBtn && val.idx==0){
							val.prevBtn.hide();
						}
						if(val.nextBtn && val.idx==val.length-1){
							val.nextBtn.hide();
						}
					};
				},
				resetCase:function(){
					obj.div.css({"position":"relative","width":val.viewWidth+"px","height":val.itemHeight+"px","overflow":"hidden"});
					obj.ul.css({"position":"absolute","top":val.caseTop,"left":val.ulLeft+"px","width":val.ulWidth+"px","height":val.itemHeight+"px","overflow":"hidden"});
				},
				intervalMove:function(){
					setInterval(function(){
						if(!val.intervalStop){
							if(val.count<val.intervalTime){
								val.count = val.count+1;
							}else{
								val.count = 0;
								if(!val.hover){
									fn.nextMove();
								};
							};
						};
					},100);
				}
		}
		
		// carousel Start
		// ajax href use AJAX Call
		if(val.href){
			fn.hrefStart();
		}else{
			fn.start();
		};
		
		if(val.flick){
			var nTouch = true; // 새로운 터치
			var isFlick = false; // 플리킹 중인가
			var sX = 0; // 플리킹시의 DIV X좌표
			var sY = 0; // 플리킹시의 DIV Y좌표
			var sT; // 플리킹 시간
			
			obj.ul.on("touchstart",function(event){
				var e = event.originalEvent.touches[0];
				sX = e.screenX;
				sY = e.screenY;
				sT = new Date();
				sT = sT.getTime();
			});
			
			obj.ul.on("touchmove",function(event){
				var e = event.originalEvent.touches[0];
				if(nTouch){
					if(Math.abs(sX-e.screenX)>Math.abs(sY-e.screenY)){
						isFlick=true;
						nTouch=false;
					}
				};
				if(isFlick){
					event.stopPropagation();
					event.preventDefault();
					var c=((sX-e.screenX)/screen.width)* -720;
					obj.ul.css({left:c-720});
				}
			})
			obj.ul.on("touchend",function(event){
				var eT = new Date();
				eT = eT.getTime();
				var e = event.originalEvent.changedTouches[0];
				if(isFlick){
					var d = sX-e.screenX;
					if(40 < d){
						fn.nextMove();
					}else if(d < -40){
						fn.prevMove();
					}else{
						fn.resetDOM("prev",val.idx+1);
					};
					isFlick = false;
					nTouch = true;
				};
			});
		};
		
		
		// action
		/* 이전버튼을 눌렀을때 액션 */
		if(val.prevBtn){
			var prevDOM = document.getElementById(val.prevBtn.get(0).id);
			prevDOM.onclick = function(){return false;};
			val.prevBtn.click(function(){
				val.prevClick.call(null,obj);
				fn.prevMove();
			});
		};
		/* 다음버튼을 눌렀을때 액션 */
		if(val.nextBtn){
			var nextDOM = document.getElementById(val.nextBtn.get(0).id);
			nextDOM.onclick = function(){return false;};
			val.nextBtn.click(function(){
				val.nextClick.call(null,obj);
				fn.nextMove();
			});
		};
		
		/* 스탑버튼을 눌렀을때 액션 */
		if(val.stopBtn){
			var stopDOM = document.getElementById(val.stopBtn.get(0).id);
			stopDOM.onclick = function(){return false;};
			val.stopBtn.click(function(){
				val.intervalStop = true;
			});
		};
		
		/* 시작버튼을 눌렀을때 액션 */
		if(val.startBtn){
			var startDOM = document.getElementById(val.startBtn.get(0).id);
			startDOM.onclick = function(){return false;};
			val.startBtn.click(function(){
				val.intervalStop = false;
			});
		};
		
		
		if(val.paraItem){
			val.paraItem.unclick().addIdx().click(function(){
				val.count = 0;
				/*fn.resetDOM("prev",this.idx);
				fn.nextMove();*/
				// 그냥 바꾸기만 할경우
				fn.resetDOM("prev",this.idx+1);
				setTimeout(function(){
					val.moveEnd.call(null,obj,val);
				});
			})
		};
		
		/* 시간으로 움직임을 만드는 함수 */
		if(val.interval){
			fn.intervalMove();
		};
		
		
		
	});
};

jQuery.extend(jQuery.strow,{carousel:strowCarousel})
jQuery.fn.carousel = function(h){
	strowCarousel(this,h);
};

jQuery.urlVars = function(key){
	return (function(){
		if(location.href.indexOf(key)!=-1){
			return location.href.split(key+"=")[1].split('&')[0];
		};
		return null;
	})();
};
