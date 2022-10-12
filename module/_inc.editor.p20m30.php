<?
//$info[siteid] = "demo";
//debug($info);
//if ($info[optionid]===1) unset($info[optionid]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ko">
<head>
<title>p20m30</title>
<meta http-equiv="content-type" content="text/html; charset=euc-kr">
<script src="/js/jquery.js"></script>

<!-- pod js 호출 -->
<script type="text/javascript" language="javascript" src="http://<?=PODS20_DOMAIN?>/CommonRef/JS/?siteid=<?=$info[siteid]?>&siteproductcode=<?=$info[productid]?>"></script>
<script type="text/javascript" language="javascript" src="http://<?=PODS20_DOMAIN?>/CommonRef/JS/CallEditor.js"></script>

<!---테스트용 편집기 로드-->
<!--<script type="text/javascript" language="javascript" src="http://<?=PODS20_DOMAIN?>/CommonRef/JS/CallEditorTest.js"></script>-->

<!-- /pod js 호출 -->

</head>

<body><script type="text/javascript" language="javascript" src="http://<?=PODS20_DOMAIN?>/CommonRef/JS/ActiveLoader30.js"></script>
<h3 align="center">Calling Editor...</h3>

<script>
$(function(){

	var o = new PODsParams();
	var skin = "<?=$cfg[skin]?>";

	o.storageid		  = "<?=$info[storageid]?>"; 		// 스토리지 아이디 (수정시)
	o.productid		  = "<?=$info[productid]?>";		// 상품코드
	o.siteid			  = "<?=$info[siteid]?>";			// 사이트아이디
	o.product_siteid = "<?=$info[center_podsid]?>";	// 타사의 공유상품을 사용하는 경우 공유해주는 사이트의 코드
	o.userid			  = "<?=$info[userid]?>";			// 사용자아이디
	o.defaultpage	  = "<?=$info[defaultpage]?>";	// 기본페이지수
	o.minpage		  = "<?=$info[minpage]?>";		// 최소페이지수
	o.maxpage		  = "<?=$info[maxpage]?>";		// 최대페이지수
	o.options		  = "<?=$info[optionid]?>";		// 사이트옵션코드

   o.introurl = "<?=$info[introurl]?>";       // 인트로 페이지에 사용 할 url 주소
	o.logourl        = "<?=$info[logourl]?>";		// 로고이미지의 url 주소.
	o.titleurl       = "<?=$info[titleurl]?>";       // 인트로 타이플 이미지의 url 주소
	o.displayprice	  = "<?=$info[displayprice]?>";	// 디스플레이용 가격 정보
	o.displaycount	  = "<?=$info[displaycount]?>";	// 디스플레이용 주문 수량
	o.displayoption  = "";							// 디스플레이용 옵션 문자열
	o.book_seneca	  = "10";							// 표지편집기에 사용하는 책등사이즈
	o.book_wing      = "";							// 표지편집기에 사용하는 날개사이즈
	o.editxmlurl	  = "";							// 뽐내기 기능 사용시, 뽐내기xml 파일 url 주소
	o.requestuser	  = "";							// 사용자정보 연동 url 주소
	o.adminmode		  = "<?=$info[adminmode]?>";		// 관리자 모드여부
   o.templatesetid  = "<?=$info[templatesetid]?>";     // 표지편집기 템플릿셋 번호
   o.templateid     = "<?=$info[templateid]?>";     // 표지편집기 템플릿 번호

	o.get_layout_spec	 = "<?=$info[get_layout_spec]?>"; //초간단포토북일 경우 상품 규격 / 페이지 연동 url
	o.get_product_info = "<?=$info[get_product_info]?>"; //초간단포토북일 경우 상품 정보 연동 url
	o.coverrangeid     = "<?=$cover_id?>";
	//o.coverrangeurl    = "http://192.168.1.195:9095/_ilarksync/get_cover_range_data.php?goodsno=<?=$goodsno?>";
	o.coverrangeurl    = "http://<?=$cfg_center[host]?>/_ilarksync/get_cover_range_data.php?goodsno=<?=$goodsno?>";
	o.coverrangedata   = "<?=$coverrangedata?>";
	o.startdate		   = "<?=$startdate?>";

    <? if ($info[token])  { ?>
    o.token  = "<?=$info[token]?>"; //토근 값 넘김 키즈노트
    <? } ?>

    <? if ($info[center_id]) { ?>
    o.center_id  = "<?=$info[center_id]?>"; // 센터 값 넘김 키즈노트
    <? } ?>
	
	<? if ($info[editmode]) { ?>
	o.editmode = "<?=$info[editmode]?>"; //save as 기능추가에 따른 editor 추가    20150514    chunter (editmode : saveas)
	<? } ?>
	
   o.macroxmlurl  = "<?=$info[macroxmlurl]?>";

	// podstation과의 정보공유 등의 목적을 갖는 로그
	var addopt = [];

	<? foreach ($info[addopt] as $k=>$v) { ?>
		addopt["<?=$k?>"] = escape("<?=$v?>");
	<? } ?>

	var addoptstr = addopt.join("^");

   o.sessionparam = "sub_option:"+addoptstr+",param:<?=$info[param]?>,pname:"+escape("<?=$info[pname]?>");

   <? if($info[requestuser]) { ?>
      o.requestuser = "<?=$info[requestuser]?>";
	<? } ?>

	//return;
	//alert(JSON.stringify(o));

	setTimeout(function(){
		var ret = CallEditor(o);
		xs_ret(ret);
	},500);
});
</script>

</body>