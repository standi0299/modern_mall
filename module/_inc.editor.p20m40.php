<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ko">
<head>
<title>p20m40</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">

<!-- pod js 호출 -->
<!--[if lte IE 9]>
<script type="text/javascript" src="http://wpod.ilark.co.kr/farms/pods/Scripts/ie/json2.js"></script>
<![endif]-->
<script type="text/javascript" src="http://wpod.ilark.co.kr/farms/pods/Scripts/jquery-1.9.0.js"></script>
<script type="text/javascript" src="http://wpod.ilark.co.kr/farms/pods/Scripts/ie/jquery.xdomain.js"></script>

<script src="http://<?=PODS20_DOMAIN?>/CommonRef/JS/CallEditorForCustomURI.js" type="text/javascript"></script>
<!-- /pod js 호출 -->

<script type="text/javascript" src="/js/chk_edge.js"></script>

<script>
	var headerTitle = '<?=$cfg[titleDoc]?>';
</script>
<script type="text/javascript" src="/js/BlackOrWhite.js"></script>

<form name="sendurl" action="" method="POST">
</form>

</head>

<body><!--<script type="text/javascript" language="javascript" src="http://<?=PODS20_DOMAIN?>/CommonRef/JS/ActiveLoader30.js"></script>-->
<!--<h3 align="center">Calling Editor...</h3>-->

<script>
$(function(){
	waitForMsgStart();			//exe 편집기 체크 시작			20160811		chunter

   var url;
   var o = new PODsParams();

      o.storageid = "<?=$info[storageid]?>";    // 스토리지 아이디 (수정시)
      o.productid = "<?=$info[productid]?>";    // 상품코드

      o.product_siteid = "<?=$info[center_podsid]?>"; // 타사의 공유상품을 사용하는 경우 공유해주는 사이트의 코드

      o.siteid = "<?=$info[siteid]?>";       // 사이트아이디
      o.userid = "<?=$info[userid]?>";       // 사용자아이디
      o.defaultpage = "<?=$info[defaultpage]?>";   // 기본페이지수
      o.minpage = "<?=$info[minpage]?>";     // 최소페이지수
      o.maxpage = "<?=$info[maxpage]?>";     // 최소페이지수
      o.options = "<?=$info[optionid]?>";    // 사이트옵션코드

      var addopt = [];
      <? foreach ($info[addopt] as $k=>$v){ ?>
         addopt["<?=$k?>"] = escape("<?=$v?>");
      <? } ?>
      var addoptstr = addopt.join("^");
      o.sessionparam = "sub_option:"+addoptstr+",param:<?=$info[param]?>,pname:"+escape("<?=$info[pname]?>");

      o.introurl = "<?=$info[introurl]?>";       // 인트로 페이지에 사용 할 url 주소
      o.logourl = "<?=$info[logourl]?>";     // 로고이미지의 url 주소.
      o.titleurl = "<?=$info[titleurl]?>";       // 인트로 타이플 이미지의 url 주소
      o.displayprice = "<?=$info[displayprice]?>"; // 디스플레이용 가격 정보
      o.displaycount = "<?=$info[displaycount]?>"; // 디스플레이용 주문 수량
      o.displayoption = "";                     // 디스플레이용 옵션 문자열
      o.editxmlurl = "";                     // 뽐내기 기능 사용시, 뽐내기xml 파일 url 주소

      <? if($info[requestuser]) { ?>
      o.requestuser = "<?=$info[requestuser]?>";
      <? } ?>

      o.book_seneca = "10";                     // 표지편집기에 사용하는 책등사이즈
      o.book_wing = "";                   // 표지편집기에 사용하는 날개사이즈
      //o.set_window_pos = $("#set_window_pos").val();
      o.templatesetid = "<?=$info[templatesetid]?>";     // 표지편집기 템플릿셋 번호
      o.templateid = "<?=$info[templateid]?>";     // 표지편집기 템플릿 번호
      o.get_layout_spec = "<?=$info[get_layout_spec]?>"; //초간단포토북일 경우 상품 규격 / 페이지 연동 url
      //o.get_page_form = $("#get_page_form").val();
      o.get_product_info = "<?=$info[get_product_info]?>"; //초간단포토북일 경우 상품 정보 연동 url
      o.adminmode = "<?=$info[adminmode]?>"; //adminmode 값 저장하기.

      //if ($('#saveas').is(":checked")) o.editmode = "saveas";
      <? if ($info[editmode])  { ?>
      o.editmode  = "<?=$info[editmode]?>";    //save as 기능추가에 따른 editor 추가    20150514    chunter (editmode : saveas)
      <? } ?>
      o.macroxmlurl  = "<?=$info[macroxmlurl]?>";
      o.returnurl = "http://<?=$_SERVER[HTTP_HOST]?>/module/popup_exe_return_with_cloud.php?cloud_sig_code=" + cloud_sig_code + "&child_ID=<?=$_GET[child_code]?>&class_ID=<?=$_GET[class_ID]?>&goodsno=<?=$data[goodsno]?>";
	  //o.returnurl2 = "http://<?=$_SERVER[HTTP_HOST]?>/module/popup_exe_return_with_cloud_tmp.php?cloud_sig_code=" + cloud_sig_code + "&child_ID=<?=$_GET[child_code]?>&class_ID=<?=$_GET[class_ID]?>&goodsno=<?=$data[goodsno]?>";
      o.coverrangeid     = "<?=$cover_id?>";
      o.coverrangeurl    = "http://<?=$cfg_center[host]?>/_ilarksync/get_cover_range_data.php?goodsno=<?=$goodsno?>";
      o.coverrangedata   = "<?=$coverrangedata?>";
      o.startdate		 = "<?=$startdate?>";

      o.set_window_pos  = "<?=$cfg[pods_size]?>";
      <? if ($info[token])  { ?>
        o.token  = "<?=$info[token]?>";
      <? } ?>
      <? if ($info[center_id]) { ?>
        o.center_id  = "<?=$info[center_id]?>";
      <? } ?>
      //var r = GetCustomURIKey(o);
      //alert(r);

/*호출추가*/
   var params = "storageid=" + o.storageid + "&pid=" + o.productid + "&siteid=" + o.siteid + "&userid=" + o.userid +
      (o.product_siteid ? "&p_siteid=" + o.product_siteid : "&p_siteid=" + o.siteid) +
      (o.defaultpage ? "&dp=" + o.defaultpage : "") +
      (o.minpage ? "&minp=" + o.minpage : "") +
      (o.maxpage ? "&maxp=" + o.maxpage : "") +
     // (o.adminmode ? "&adminmode=" + encodeURIComponent(o.adminmode) : "&adminmode=Y"  ) +
      "&opt=" + o.options +
      "&sessionparam=" + o.sessionparam +
      (o.introurl ? "&introurl=" + encodeURIComponent(o.introurl) : "") +
      (o.logourl ? "&logourl=" + encodeURIComponent(o.logourl) : "") +
      (o.titleurl ? "&titleurl=" + encodeURIComponent(o.titleurl) : "") +
      (o.book_seneca ? "&seneca=" + o.book_seneca : "") +
      (o.book_wing ? "&wing=" + o.book_wing : "") +
      (o.displayprice ? "&dpprice=" + o.displayprice : "") +
      (o.displaycount ? "&dpcnt=" + o.displaycount : "") +
      (o.displayoption ? "&dpopt=" + encodeURIComponent(o.displayoption) : "") +
      (o.editxmlurl ? "&editxmlurl=" + encodeURIComponent(o.editxmlurl) : "") +
      (o.requestuser ? "&requestuser=" + encodeURIComponent(o.requestuser) : "") +
      (o.editno ? "&editno=" + o.editno : "") +
      (o.adminmode ? "&adminmode=" + encodeURIComponent(o.adminmode) : "") +
      (o.querystring ? "&querystring=" + o.querystring : "") +
      (o.set_window_pos ? "&set_window_pos=" + o.set_window_pos : "") +
      (o.templatesetid ? "&templatesetid=" + o.templatesetid : "") +
      (o.templateid ? "&templateid=" + o.templateid : "") +
      (o.get_layout_spec ? "&get_layout_spec=" + encodeURIComponent(o.get_layout_spec) : "") +
      (o.get_product_info ? "&get_product_info=" + encodeURIComponent(o.get_product_info) : "") +
      (o.editmode ? "&editmode=" + o.editmode : "") +
      "&macroxmlurl=" + encodeURIComponent(o.macroxmlurl) +
      (o.coverrangeid ? "&coverrangeid=" + encodeURIComponent(o.coverrangeid) : "") +
      (o.coverrangeurl ? "&coverrangeurl=" + encodeURIComponent(o.coverrangeurl) : "") +
      (o.coverrangedata ? "&coverrangedata=" + encodeURIComponent(o.coverrangedata) : "") +
      (o.startdate ? "&startdate=" + o.startdate : "") +
     (o.token ? "&token=" + encodeURIComponent(o.token) : "") +
     (o.center_id ? "&center_id=" + o.center_id : "") +
     (o.returnurl ? "&return_url=" + encodeURIComponent(o.returnurl) : "");
	  //(o.returnurl2 ? "&return2_url=" + encodeURIComponent(o.returnurl2) : "");

	//alert( JSON.stringify(params) );

    $.ajax({
        url: "http://<?=PODS20_DOMAIN?>/CommonRef/CustomURI/create_editor_startup.aspx",
        dataType: "jsonp",
        data: params,
        jsonp: "callback",
        jsonpCallback: "Callback",
        cache: false,
        async: false,
        success: function (data) {
            if (data.result != null && data.result != 'undefined' && data.result != 'Fail') {
              //alert(JSON.stringify(data));
              result_data = data;

               //alert(data.key);
               //alert(data.svr);
               //alert(data.url);
              url = data.url;

							checkExeAppInstall(url,
              	// 설치가 안된경우나 설치검사를 지원하지 않는 브라우저인 경우(Edge)
								function (e, b) {
									parent.slide();
								}
							);
               //window.location = url;
            }
            else alert('<?=_("Key 생성에 실패했습니다.(관리자에게 문의하십시오.)")?>');
        },
        error: function (xhr, status, e) {
            alert('<?=_("실패")?>' + ":" + e.responseText);
        }
    });

    function Callback(data) {
    }

});

function parent_slide()
{
	parent.slide();
}
</script>
</body>
