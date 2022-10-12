<?
include "../lib/library.php";

if($_GET[exit_code] == "0")
   $indata[mode] = "cancel";

$_GET[keyresult] = str_replace("https", "http", $_GET[keyresult]);
$keyresult = readurl($_GET[keyresult]);

$storageid = urlencode(addslashes($keyresult));

$keyresult_decode = json_decode($keyresult,1);

$sessionparam = $keyresult_decode[uploaded_list][0][session_param];

$_data = explode("&",$sessionparam);
if($_data){
   foreach ($_data as $k2=>$v2){
      $v2 = explode("=",$v2);
      $retdata[$v2[0]] = $v2[1];
   } 
}

$retdata[sessionparam] = explode(",",urldecode($retdata[sessionparam]));

$retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][1]);
$indata = json_decode(base64_decode($retdata[sessionparam][1]),1);

//로그인 쿠키 처리.
//debug($indata[bluepod_mid]);
if($indata[bluepod_mid])
{
	$login_data = memberLoginProc($indata[bluepod_mid], "", "N", "/member/login.php", '', "N");
//debug($login_data);
	
	_member_login($login_data);
}

//exe 편집기 종료여부 체크 클라우드 서버			20160811		chunter
$cloud_server_url = "http://blackorwhite.ilark.co.kr/_cloud/save_complete.php";

//편집기 종류에 따른 장바구니 데이타 처리.(복수편집기) / 16.09.08 / kdk
include "../lib/class.pods.service.php";
# pods editor class
$pods = new PodsEditor($indata[goodsno]);

//상품에 설정된 pods 정보.
$editor_arr = $pods->GetPodsEditor();
if($editor_arr) {
	foreach ($editor_arr as $key => $val) {
		if($val[podskind] == $retdata[editor]) {
			$editor = $val;	
		}		
	}
}
//debug($editor);
		
#복수 편집기 처리 pods_use, podskind, podsno form에 추가하여 넘기 2016.09.08 by kdk  
if($editor[pods_use] && $editor[podskind] && $editor[podsno]) {
	$pods_use = $editor[pods_use]; 
	$podskind = $editor[podskind]; 
	$podsno	= $editor[podsno];
	
	$hidden_data = "<input type='hidden' name='pods_use' value='$pods_use' />
		<input type='hidden' name='podskind' value='$podskind' />
        <input type='hidden' name='podsno' value='$podsno' />";
}

### 복수 편집기 견적 정보 임시 저장 / 16.09.09 / kdk
if($indata[pod_signed] && $indata[pod_signed] <> "") {
	$hidden_data .= "<input type='hidden' name='pod_signed' value='$indata[pod_signed]' />";
}
		
if (in_array($podskind,array(1001,1002,1003,1005,1006,1007,1008,3180,28))) {
	$storageid = $keyresult_decode[storageid];
}

### 패키지상품 관련 처리 / 2017.12.20 / kdk
if ($indata[package_mode])
    $hidden_data .= "<input type='hidden' name='package_mode' value='$indata[package_mode]' />";
//if ($indata[package_cartno])
    //$hidden_data .= "<input type='hidden' name='package_cartno' value='$indata[package_cartno]' />";

/********* 임시 로그 남기기 20200206 kkwon************/
$log_dir = "../data/tmp/pods_return/";
if (!is_dir($log_dir)) {
  mkdir($log_dir, 0707);
  chmod($log_dir, 0707);
}
$log_file = "tmp_".date("YmdHis") . "_" . uniqid();
$fp = fopen($log_dir . $log_file, "w");
$tmp_data = $indata[mode]." ".$indata[bluepod_mid]." ".$indata[cartno]." ".$indata[goodsno]." ".$indata[cartno]." ".$indata[title]." ".addslashes($keyresult);
fwrite($fp, $tmp_data);
/***********************************/
?>
<script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/jquery.js"></script>
<script>
   var $j = jQuery.noConflict();
</script>

<form name="fmView" id="fmView">
   <input type="hidden" name="mid" value="<?=$indata[bluepod_mid]?>"/>
   <input type="hidden" name="cartno" value="<?=$indata[cartno]?>"/>
   <input type="hidden" name="goodsno" value="<?=$indata[goodsno]?>"/>
   <input type="hidden" name="optno" value="<?=$retdata[optno]?>"/>
   <input type="hidden" name="addopt" value="<?=$retdata[addopt]?>"/>
   <input type="hidden" name="productid" value="<?=$retdata[pid]?>"/>

   <!--<input type="hidden" name="optionid" value="{edit.podoptno}"/>-->

   <input type="hidden" name="ea" value="0"/>
   <input type="hidden" name="subject" value="<?=$indata[title]?>"/>
   <input type="hidden" name="storageid" value="<?=$storageid?>"/>

	<?=$hidden_data?>
   
</form>
<Script>
var mode = "<?=$indata[mode]?>";
var package_mode = "<?=$indata[package_mode]?>";

var editmode = "<?=$indata[editmode]?>"; //savaas 경우 장바구니 처리.
if(editmode == "saveas") mode = "view";

if(mode == "edit") {
   var storageid = "<?=$keyresult_decode[storageId]?>";

   $.ajax({
      type: "post",
      url: "../order/indb.php",
      data: "mode=editupdate&storageid=" + storageid,
      async: false,
      success:function() {
          saveComplete2022();
         //location.href='<?//=$cloud_server_url?>//?mode=reload&cloud_sig_code=<?//=$_GET[cloud_sig_code]?>//&langs=<?//=$languages?>//';
      }
   });
   
} else if(mode == "view") {
	document.fmView.ea.value = "1";
	var queryString = $("form[name=fmView]").serialize();

   $.ajax({
      type: "post",
      url: "../order/cart.php",
      data: "mode=cart&exe_mode=Y&" + queryString,
      async: false,
      success:function() {
          saveComplete2022();
         //if(package_mode == "Y") {
         //    location.href='<?//=$cloud_server_url?>//?mode=reload&cloud_sig_code=<?//=$_GET[cloud_sig_code]?>//&langs=<?//=$languages?>//';
         //}
         //else {
         //    location.href='<?//=$cloud_server_url?>//?mode=cart&cloud_sig_code=<?//=$_GET[cloud_sig_code]?>//&langs=<?//=$languages?>//';
         //}

      }
   });

} else if(mode == "cancel" || mode == '') {
   alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
   window.opener = 'nothing';
   window.open('', '_parent', '');
   window.close();
}

// TODO 개선필요
// AWS이관후 http://blackorwhite.ilark.co.kr/_cloud/save_complete.php 의 지연현상 발생으로
// 로케이션 시키지 않고 종료시킴
function saveComplete2022() {
    alert('<?=_("편집을 완료하였습니다")?>');
    window.opener = 'nothing';
    window.open('', '_parent', '');
    window.close();
}
</script>