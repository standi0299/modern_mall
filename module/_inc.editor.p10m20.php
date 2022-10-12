<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ko">
<head>
<title>p10m20</title>
<meta http-equiv="content-type" content="text/html; charset=euc-kr">
<script src="/js/jquery.js"></script>

<!-- pod js 호출 -->
<script type="text/javascript" language="javascript" src="http://podstation.ilark.co.kr/JS/?siteid=<?=$info[siteid]?>&master=true"></script>
<script type="text/javascript" language="javascript" src="http://podstation.ilark.co.kr/JS/CallEditor.js"></script>
<!-- /pod js 호출 -->

</head>

<body>
<script type="text/javascript" language="javascript" src="http://podstation.ilark.co.kr/JS/ActiveLoader.js"></script>
<h3 align="center">Calling Editor...</h3>

<script>

// 보관함코드(수정시)
var storageid		= "<?=$info[storageid]?>";
// 상품코드
var productid		= "<?=$info[productid]?>";
// 상품옵션코드
var optionid		= "<?=$info[optionid]?>";
// 사이트아이디
var siteid			= "<?=$info[siteid]?>";
// 사용자 아이디
var userid			= "<?=$info[userid]?>";
// podstation과의 정보공유 등의 목적을 갖는 로그
var addopt = [];
<? foreach ($info[addopt] as $k=>$v){ ?>
	addopt["<?=$k?>"] = escape("<?=$v?>");
<? } ?>
var addoptstr = addopt.join("^");

<?
  //비디오북 링크 설정    20131120    chunter  
  $vlinkTag = "";
  if ($info[vidiobook_link] == "N")
    $vlinkTag = ",vl:false";
?>

var sessionparam	= "sub_option:"+addoptstr+",param:<?=$info[param]?><?=$vlinkTag?>,pname:"+escape("<?=$info[pname]?>");
// 편집인화 상품전용
var skinid			= "";
// 상품코드/옵션코드 데이터의 주체 site,pods
var idmode			= "pods";
// xml url
var editxmlurl		= "";

var macroxmlurl  = "<?=$info[macroxmlurl]?>";

<? if ($info[editmode]) { ?>
  sessionparam += ",editmode:<?=$info[editmode]?>";    //save as 기능추가에 따른 editor 추가    20150909    chunter (editmode : saveas)
<? } ?>

// 편집기 실행
$(function(){
	setTimeout(function(){
		var ret = CallEditor(storageid,productid,optionid,siteid,userid,sessionparam,skinid,idmode,editxmlurl,macroxmlurl);
		xs_ret(ret);
	},500);
});
</script>

</body>