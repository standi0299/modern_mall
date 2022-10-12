<?

$login_offset = true;
include "../_header.php";
include_once "../lib/nusoap/lib/nusoap.php";
include "../lib/func.xml.php";

$m_board = new M_board();

########################################################################################################
# 1. 조회수 업테이트 2013_07_26
# 2. 삭제요청 2013_07_31
# 3. popup_del()에서 삭제 일련번호를 넘겨주고 있음. indb2.php -> type_del(mode)  2013_07_31
#    1) 본인이 입력한 게시글이 아니라면 삭제요청 버튼를 비활성화 해줌.
#    2) 삭제요청한 부분은 관리자페이지(편집왕리스트)에 노출함. 관리자가 직접 삭제한다고 함.  
#######################################################################################################
####  1번행____ 팝업창을 열때마다 hit필드에 증가함. ####
$addColumn = "set hit = hit+1";
$addWhere3 = "where no = '$_GET[no]'";
$m_board->setEdkingInfo($_GET[no], $addColumn, $addWhere3); //조회수 업데이트

####  2번행____  게시물 등록자와 일치하는 사람만 삭제하게끔 처리. ####
$addWhere = "where no = '$_GET[no]'";
$data = $m_board->getEdkingInfo($cid, $addWhere);

$del_num = $data[no];
$umid = $data[mid];
$del_ok = $data[del_ok];

if($umid == $sess[mid]) { //게시물 등록자와 현재 로그인하고 있는 사람과 일치하는지 체크 후 삭제요청 가동함.
	if ($del_ok == "Y") $del_no = "<a href='javascript:void(0);' onClick='javascript:popup_del_cancel();'>";
	else $del_no = "<a href='javascript:void(0);' onClick='javascript:popup_del();'>";
}

$tpl->assign("del_no",$del_no);
$tpl->assign("del_ok",$del_ok);

########################################################################################################
# ____________ 하단 알고리즘은 클라이언트와 연동한 작업 _______
# 미요청1 : 추후 페이별로 추출한다고 함. 현재는 그렇게 되어 있지않음. 추후 작업을 요함.
# 미요청2 : 현재 이미지 크기가 클라이언트쪽에서 선언해준다고 함. 만약 이미지를 크게 설정한다면 요청해야함.
# 팝업 편집이미지 크기 설정 : 855 X 587 
#######################################################################################################
	/*
	if (in_array($podskind,$r_podskind20)){ // 2.0 상품
		$soap_url	= "http://podstation20.ilark.co.kr/CommonRef/StationWebService/StationWebService.asmx?WSDL";
	} else {
		$soap_url	= "http://podstation.ilark.co.kr/StationWebService/StationWebService.asmx?WSDL";
	}

	$client = new soapclient($soap_url,true);
	$ret = $client->call('GetPreViewImg',array('storageid'=>$_GET[storageid]));

	$loop = explode("|",$ret[GetPreViewImgResult]);
	$loop = array_notnull($loop);
	*/
##########################################################################################################################################
    /// <summary>
    /// 미리보기 이미지 호출
    /// 블루팟에서 직접 호출
    /// 포토큐브 편집왕 전용 
    /// 2014.02.19 by kdk
    /// </summary>
    /// <returns>성공: 이미지URL|, 실패: fail|메세지</returns>
	///자체스토리지 서버로 호출
	$addWhere2 = "where cid = '$cid' and storageid = '$_GET[storageid]'";
	$data2 = $m_board->getEdkingInfo($cid, $addWhere2);
	
	$img_url = $data2[img_url];
	$loop = explode("|", $img_url);
	$loop = array_notnull($loop);	
	//print_r(getimagesize($loop[1]));
##########################################################################################################################################
	if (!$loop) {
		msg(_("미리보기 지원되지 않습니다."), "close");
	}
	
	if ($loop[1]) {
		$dir = "../data/edking/";
     	if (!is_dir($dir)) {
     		mkdir($dir, 0707);
     		chmod($dir, 0707);
		}	
		
		$dir = "../data/edking/$cid/";
     	if (!is_dir($dir)) {
     		mkdir($dir, 0707);
     		chmod($dir, 0707);
		}
		
		//두번째 이미지 사이즈 구해오기
		$turl  = $loop[1];
		$curl = curl_init(); 
		
		curl_setopt($curl, CURLOPT_URL, $turl); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true); 
		curl_setopt($curl, CURLOPT_HEADER, false); 
		
		$getcurl = curl_exec($curl); 
		curl_close($curl); 
		$fname = $dir.time();
		$fp = fopen($fname , 'w');
		fwrite($fp, $getcurl);  
		fclose($fp);  
		
		$arr_info = getimagesize($fname);
		unlink($fname);
	}
	
	$img_w = $arr_info[0];
	$img_h = $arr_info[1];
        
	$tpl->assign("img_w",$img_w);
	$tpl->assign("img_h",$img_h);
	$tpl->assign("loop",$loop);
	$tpl->print_('tpl');
	
?>

<script type="text/javascript">
function popup_del() {
	if (!confirm('<?=_("삭제요청하시겠습니까?")?>')) {
		return;
	}
	
	location.href = "indb.php?mode=edking_del&no=<?=$del_num?>&pmid=<?=$umid?>";
}

function popup_del_cancel() {
	if (!confirm('<?=_("삭제취소요청하시겠습니까?")?>')) {
		return;
	}
	
	location.href = "indb.php?mode=edking_del_cancel&no=<?=$del_num?>&pmid=<?=$umid?>";
}
</script>