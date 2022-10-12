<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
(function($){
	$(document).ready(function(){
		
		$('#topBanner').click(function () {
				$(".topPopup").slideUp(800);
		});
		
		$('.go-top-btn').click(function(){
	    window.scrollTo( 0, 0 );
	  });
	  $('.go-bottom-btn').click(function(){
	    window.scrollTo( 0, document.body.scrollHeight );
	  });
	});
})(jQuery1_11_0);

//wish list 찜하기.
function setWishlist(mid, catno, goodsno) {
	if (mid == "") {
		alert('<?echo _("회원일 경우에만 찜하기가 가능합니다.")?>');
	} else {
		$j.post("indb.php", {mode: "ajax_setWishlist", catno: catno, goodsno: goodsno}, function(data) {
			if (data == "OK") {
				alert('<?echo _("해당 상품을 찜하셨습니다.")?>');
			} else if (data != "") {
				alert(data);
			} else {
				alert('<?echo _("찜하기 도중 오류가 발생했습니다.")?>');
			}
		});
	}
}	

function showSnsLayer()
{
	jQuery1_11_0("#ly_sns").toggle();
}

function url_copy(copy_url) 
	{			
		var targetId = "_hiddenCopyText_";
    var target = document.createElement("textarea");
		target.style.position = "absolute";
    target.style.left = "-9999px";
    target.style.top = "0";
    target.id = targetId;
    document.body.appendChild(target);
    target.textContent = copy_url;
    
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);
    
    // copy the selection
    var succeed;
    succeed = document.execCommand("copy");
    
    //target.remove();
	}
	

function moveWinCenter() {
	//팝업창 가로 세로 길이
	var popup_w = window.document.body.offsetWidth;
	var popup_h = window.document.body.offsetHeight;

	//모니터 가로 세로 길이
	var screen_w = window.screen.width;
	var screen_h = window.screen.height;

	//팝업창 위치
	var x = (screen_w - popup_w)/2;
	var y = (screen_h - popup_h)/2;

	this.moveTo(x, y);
}
