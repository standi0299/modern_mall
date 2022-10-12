<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
function form_chk(fm){
	
	var els = fm.elements;
	var err = false;
	
	for (var i=0;i<els.length;i++){
		var el = els[i];

		var val = $j(el).val();
		
		var label;
		if ($j(el).attr("label")!=undefined && $j(el).attr("label")!==false){
			label = $j(el).attr("label");
			//label = _tsl(label);
		} else if ($j(el).parent().prev().text()){
			label = $j(el).parent().prev().text();
			//label = _tsl(label);
		} else {
			label = $j(el).attr("name");
		}

		if (!_pattern(el) && !$j(el).attr("disabled") && $j(el).val().length > 0){
			if ($j(el).attr("msg")){
				alert($j(el).attr("msg"));
			} else {
				alert(label+'<?echo _("의 입력형식이 올바르지 않습니다.")?>');
			}
			$j(el).focus();
			$j(el).val($j(el).val());
			return false;
		}

		if ($j(el).attr("required")!=undefined && $j(el).attr("required")!==false && !$j(el).attr("disabled")){

			var dummyval;
			dummyval = val.replace("　", "");
			dummyval = val.replace(/\s*/, "");

			if ($j(el).attr("type")=="radio" || $j(el).attr("type")=="checkbox"){
				
				var ret = false;
				var fieldnm = eval("fm.elements['"+$j(el).attr("name")+"']");
				var fld = $j("[name="+$j(el).attr('name')+"]",$j(fm));
				fld.each(function(){
					if (this.checked){
						ret = true;
					}
				});

				if (!ret){
					if ($j(el).attr("msg")){
						alert($j(el).attr("msg").replace("|", "\n"));
					} else {
						alert(label+'<?echo _("을(를) 선택해주세요.")?>');
					}
					$j(el).focus();
					return false;
				}

			} else {

				if (dummyval==""){
					var act = (el.tagName=="SELECT") ? '<?echo _("선택")?>':'<?echo _("입력")?>';
					if ($j(el).attr("msg")){
						alert($j(el).attr("msg"));
					} else {
						alert(label+'<?echo _("를(을)")?>' + " "+act+'<?echo _("해주세요.")?>');
					}
					$j(el).focus();
					return false;
				}
			}
		}
		
		if ($j(el).attr("samewith")){
			if ($j("input[name="+$j(el).attr("samewith")+"]"),$j(els)){
				var val1 = $j("input[name="+$j(el).attr("samewith")+"]").val();
				var val2 = $j(el).val();
				if (val1 && val1!=val2){
					alert(label+'<?echo _("가(이) 일치하지 않습니다")?>');
					$j(el).val("");
					$j(el).focus();
					return false;
				}
			}
		}
		
		//btnSubmitDisable();
	}
	
	/* 스팸방지 */
	if (fm.chkspam) fm.chkspam.value = "1";
	
	//IE 11, 크롬에서 게시물 편집내용이 post 로 넘어가지 않기에 강제로 넣어준다.
	//content가 있고 value가 널로 넘어올때만 처리 / 14.07.18 / kjm
	//fm.contet.value가 null 일때만 데이터가 넘어가도록 해놨었는데
	//수정이 안되어서 fm.content만 체크하도록 수정 / 14.08.22 / kjm

	if(fm.content){
		//if(fm.content.value == ''){
			var _iframe = document.getElementById('objFrame');
			var edit_box = document.getElementById('desc');

			if (_iframe && edit_box)
			{
				var _content = _iframe.contentWindow.document;
				edit_box.value = _content.body.innerHTML;
				//alert(edit_box.value);
			}
		//}		
	}
	return true;
}


function FormCheckNSubmitDisable(fm)
{
	if (form_chk(fm))
	{
		btnSubmitDisable();
		return true;
	}
	else 
	{
		return false;
	}
}

//버튼 비활성화 & 주문처리 메세지 / 15.11.11 . kjm
function btnSubmitDisable() {
	var btn_submit_disable = document.getElementById("btn_submit_disable");
	
	//bottom 배너의 높이를 구한다
	var bottom_banner_height = 0;
	var footerArea = document.getElementById("footerArea");	
	if (footerArea != null && footerArea!=undefined && footerArea) bottom_banner_height = footerArea.offsetHeight;

	if (btn_submit_disable != null && btn_submit_disable != undefined && btn_submit_disable)
		btn_submit_disable.style.display = 'none';

	//현재 창, 전체 창의 높이
	var scrollHeight = document.documentElement.scrollHeight;
	var maskHeight = document.documentElement.clientHeight;

	totalHeight = scrollHeight < maskHeight ? maskHeight : scrollHeight;

	var top_px;
	top_px = (totalHeight - 467 - bottom_banner_height) + "px";
	//var maskHeight = window.document.body.clientHeight;

	var loadingImg = '';

   loadingImg += "<div id='loadingImg' style='position:absolute; left:43%; top:"+top_px+"; display:none; z-index:10000;'>";
   loadingImg += "<span style='font-size:30px;font-weight:bold;color:RED'>" + '<?echo _("주문 처리중 입니다.")?>' + "</span>";
   loadingImg += "</div>";

	//화면에 레이어 추가 
	$j('body').append(loadingImg);

  //로딩중 이미지 표시
	$j('#loadingImg').show();
}

function _pattern(obj){
	if(!$j(obj).attr("pt")) return true;
	var val = $j(obj).val();
	
	/* 정수형		*/	var _pt_numdot	= /^[0-9.]+$/;
	/* 정수형		*/	var _pt_num		= /^[-]*[0-9]+$/;
	/* 자연수		*/	var _pt_numplus	= /^[0-9]+$/;
	/* 소문자		*/	var _pt_low		= /^[a-z]+$/;
	/* 대문자		*/	var _pt_up		= /^[A-Z]+$/;
	/* 한글		*/	var _pt_kr		= /^[가-힣]+$/;
	/* 아이디		*/	var _pt_id		= /^[a-zA-Z]{1}[0-9a-zA-Z_-]{5,19}$/;
	/* 아이디		*/	var _pt_board_id	= /^[a-z]{1}[0-9a-zA-Z_-]{2,19}$/;
	/* 패스워드	*/	var _pt_pw		= /^.{6,20}$/;
	/* 이메일		*/	var _pt_email	= /^[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[@]{1}[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[.]{1}[A-Za-z]{2,5}$/; //아이디 특수문자 사용 불가능(-,_ 사용 가능) / 14.11.18 / kjm
 
	// 아래가 기존 정규식 위에가 새로 바꾼 정규식 a.aaaa@aaa.co.kr 형식(아이디에 .이 있어도 가능하도록)이 가능하도록 변경 / 14.11.17 / kjm
	// /^[_a-zA-Z0-9-]+@[._a-zA-Z0-9-]+\.[a-zA-Z]+$/;
	// /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/; -> 아이디에 특수문자 사용 가능
										  
	/* 아이피		*/	var _pt_ip		= /^[.0-9]+$/;
	/* 			*/	var _pt_txt		= /.*/;
	/*			*/	var _pt_txteng	= /.*/;
	/* 쿠폰코드	*/	var _pt_coupon	= /^[0-9a-zA-Z_-]{6,10}$/;
	/* pods_id	*/	var _pt_podsid	= /^[0-9a-z]{5}[0-9a-z]*$/;

	/* hp		*/	var _pt_hp	= /^\d{3}-\d{3,4}-\d{4}$/;
	/* tel		*/	var _pt_tel	= /^\d{2,3}-\d{3,4}-\d{4}$/;

	/* hp		*/	var _pt_hp2	= /^[0-9]{10,11}$/;
	/* tel		*/	var _pt_tel2 = /^[0-9]{9,11}$/;

	/* 정수형소수점 */	var _pt_numdot2	= /^(\d{1,2}([.]\d{0,2})?)?$/; //100 이하의 숫자만 입력가능하며 소수점 둘째자리까지만 허용됩니다.

	var pattern = eval($j(obj).attr("pt"));

	if (pattern!=undefined && pattern){
		return (pattern.test(val));
	} else {
		return true;
	}
};

/*
$j(function(){
//	_pt_set();
});
*/

function _pt_set(){

	$j("[pt=_pt_num]").css("ime-mode","disabled");
	$j("[pt=_pt_num]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 48 || keycode > 57) && keycode!=13 && keycode!=45 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_numdot]").css("ime-mode","disabled");
	$j("[pt=_pt_numdot]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8 && keycode!=46){
			return false;
		}
	});
	$j("[pt=_pt_numplus]").css("ime-mode","disabled");
	$j("[pt=_pt_numplus]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	//숫자 + 영어만 가능하도록 / 16.01.05 / kjm
	$j("[pt=_pt_numeng]").css("ime-mode","disabled");
	$j("[pt=_pt_numeng]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8 && (keycode < 97 || keycode > 122)){
			return false;
		}
	});
	$j("[pt=_pt_low]").css("ime-mode","disabled");
	$j("[pt=_pt_low]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 97 || keycode > 122) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_up]").css("ime-mode","disabled");
	$j("[pt=_pt_up]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 65 || keycode > 90) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_kr]").css("ime-mode","active");
	$j("[pt=_pt_kr]").keypress(function(e){
		var keycode = e.which;
		if (keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_id]").css("ime-mode","disabled");
	$j("[pt=_pt_id]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 97 || keycode > 122) && keycode != 95 && (keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_pw]").css("ime-mode","disabled");
	$j("[pt=_pt_pw]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 65 || keycode > 90) && (keycode < 97 || keycode > 122) && keycode != 45 && keycode != 95 && (keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_txt]").keypress(function(e){
		var keycode = e.which;
		if ((keycode == 34 || keycode == 92 || keycode == 39) && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_txteng]").css("ime-mode","disabled");
	$j("[pt=_pt_txteng]").keypress(function(e){
		var keycode = e.which;
		if ((keycode == 34 || keycode==92) && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_email]").css("ime-mode","disabled");
	$j("[pt=_pt_email]").keypress(function(e){
		var keycode = e.which;
		if ((keycode < 65 || keycode > 90) && (keycode < 97 || keycode > 122) && keycode != 64 && keycode != 46 && keycode != 45 && keycode != 32 && keycode != 95 && (keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_ip]").css("ime-mode","disabled");
	$j("[pt=_pt_ip]").keypress(function(e){
		var keycode = e.which;
		if (keycode != 46 && (keycode < 48 || keycode > 57) && keycode!=13 && keycode!=8){
			return false;
		}
	});
	$j("[pt=_pt_coupon]").css("ime-mode","disabled");
	$j("[pt=_pt_coupon]").keypress(function(e){
		var keycode = e.which;
		if ((keycode == 34 || keycode==92) && keycode!=8){
			return false;
		}
	});
}

function chkResno(resno)
{
	fmt = /^\d{6}[1234]\d{6}$/;
	if (!fmt.test(resno)) {
		if (!arguments[1]) alert('<?echo _("잘못된 주민등록번호입니다.")?>'); 
		return false;
	}

	birthYear = (resno.charAt(6) <= '2') ? '19' : '20';
	birthYear += resno.substr(0, 2);
	birthMonth = resno.substr(2, 2) - 1;
	birthDate = resno.substr(4, 2);
	birth = new Date(birthYear, birthMonth, birthDate);

	if ( birth.getYear()%100 != resno.substr(0, 2) || birth.getMonth() != birthMonth || birth.getDate() != birthDate) {
		if (!arguments[1]) alert('<?echo _("잘못된 주민등록번호입니다.")?>');
		return false;
	}

	buf = new Array(13);
	for (i = 0; i < 13; i++) buf[i] = parseInt(resno.charAt(i));

	multipliers = [2,3,4,5,6,7,8,9,2,3,4,5];
	for (i = 0, sum = 0; i < 12; i++) sum += (buf[i] *= multipliers[i]);

	if ((11 - (sum % 11)) % 10 != buf[12]) {
		if (!arguments[1]) alert('<?echo _("잘못된 주민등록번호입니다.")?>');
		return false;
	}
	return true;
}