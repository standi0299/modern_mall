<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
$j(function(){
	$j("#pwLayer").fadeTo(0,0);
});

function layerPw(obj,no){

	var div = $j("#pwLayer");

	$j(div).css("z-index","9999");
	$j(div).css("position","absolute");
	$j(div).css("padding","10px");
	$j(div).css("top",$j(obj).offset().top+10+"px");
	$j(div).css("left",$j(obj).offset().left+5+"px");
	$j(div).fadeTo(500,1.0);
	$j(div).css("overflow","hidden");
	$j(div).css("background","#FFFFFF");
	$j("[name=no]",$j(div)).val(no);
	$j("#pwLayer_result").html('');
	$j("[name=password]","#pwLayer").val("");
	$j("[name=password]","#pwLayer").focus();
	$j("[name=password]","#pwLayer").bind('keydown', function(e){ if(e.which === 13) { $j("#pwLayer_submit").trigger("click"); return false;} });
	$j("[name=password]","#pwLayer").bind('keydown', function(e){ if(e.which === 27) { $j("#pwLayer_cancel").trigger("click"); return false;} });

}

function chkPassword(mode, langs){
	
	//기존엔 mode값 한개만 받았으나 list.htm, view.htm에서 넘어온 {lang_kind}값(kor or eng)을 추가로 받습니다.	//
	//langs값을 받아 indb.php에 다른 값들과 함께 넘겨줍니다.
	//2013.12.09 kjm
	
	if (!form_chk(document.pwLayer_form)){
		alert('<?echo _("비밀번호를 입력해주세요.")?>');
		return;
	}
		
	langs_param = '';
	if (langs != "undefined")
		langs_param = '&langs=' + langs;
	
	var no = $j("[name=no]","#pwLayer").val();
	var board_id = $j("[name=board_id]","#pwLayer").val();
	var password = $j("[name=password]","#pwLayer").val();
	$j.post("indb.php", {mode: mode, board_id: board_id, no:no, password:password, lang_kind:langs},
	function(data){

	if (mode=="password_view" && data=="ok"){
			location.href='view.php?board_id='+board_id+'&no='+no + langs_param;
			document.pwLayer_form.submit();
		}

		$j("#pwLayer_result").html(data);
	});
}

function printComment(board_id,no,page){
	$j.post("indb.php", {mode: "comment", board_id: board_id, no:no, page:page},
	function(data){
		$j("#div_comment").html(data);
		_pt_set();
		setComment();
	});
}

function submitComment(fm){

	if (!form_chk(fm)){
		return false;
	}
	var set_board_id = fm.board_id.value;
	var set_no = fm.no.value;
	var set_name = fm.name.value;
	var set_password = (fm.password) ? fm.password.value:"";
	var set_comment = fm.comment.value

	$j.post("indb.php", {mode: "comment_write", board_id: set_board_id, no:set_no, name:set_name, password:set_password, comment:set_comment},
	function(data){
		printComment(set_board_id,set_no,1);
	});
}

function setComment(){
	$j(".comment_div").mouseenter(function(){
		var no = $j(this).attr('no');
		var mid = $j(this).attr('mid');
		var delable = $j(this).attr('delable');
		$j(this).css("background","#F3F3F3");
		var div = document.createElement("div");
		$j(div).css("position","absolute");
		$j(div).attr("class","comment_set stxt gray bold");
		if (delable){
			$j(div).html("<span style='cursor: pointer;' onclick='delComment(\""+no+"\")'>" + '<?echo _("삭제")?>' + "</span>");
		} else if (!mid){
			$j(div).html("<span style='cursor: pointer;' onclick='delCommentPassword(this,\""+no+"\")'>" + '<?echo _("삭제")?>' + "</span>");
		}
		$j(div).css("top",$j(this).height()/2 + 5);
		$j(div).css("right",10);
		$j(div).appendTo($j(this));
	});
	$j(".comment_div").mouseleave(function(){
		var no = $j(this).attr('no');
		$j(this).css("background","");
		$j(".comment_set").children().remove();
		$j(".comment_set").remove();
	});
}

function delComment(no){
	if (confirm('<?echo _("정말 삭제하시겠습니까?")?>')){

		$j.post("indb.php", {mode: "comment_del", no:no},
		function(data){
			if (data=="true"){
				$j(".comment_div[no="+no+"]").fadeOut();
			} else {
				alert('<?echo _("삭제실패")?>');
			}
		});
	}
}
function delCommentPassword(obj,no){
	$j("#comment_password").remove();
	var div = document.createElement("div");
	$j(div).attr("class","comment_password stxt red bold");
	$j(div).html("<span id='comment_password_span'>" + '<?echo _("비밀번호를 입력해주세요.")?>' + "</span><br><input type='password' name='password'> <input type='button' class='btn-primary' value='" + '<?echo _("확인")?>' + "' style='cursor: pointer;' onclick='delCommentPw(this,"+no+")'>");
	$j(div).css("position","absolute");
	$j(div).css("width","300px");
	$j(div).css("right","35px");
	$j(div).css("text-align","right");
	$j(div).attr("id","comment_password");
	$j(div).css("top",$j(obj).parent().height()/2 - 25);
	$j(div).appendTo($j(obj).parent());
}
function delCommentPw(obj,no){
	var pw = $j(obj).prev().val();
	$j.post("indb.php", {mode: "comment_chkPassword", password: pw, no:no},
	function(data){
		if (data=="false"){
			$j("#comment_password_span").html('<?echo _("비밀번호가 일치하지 않습니다.")?>');
		} else if (data=="true"){
			delComment(no);
		}
	});

}