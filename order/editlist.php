<?

/*
* @date : 20180314
* @author : kdk
* @brief : 3055편집기일때 커버옵션정보를 가져온다.
* @request :
* @desc :
* @todo :
*/

/*
* @date : 20180309
* @author : kdk
* @brief : 정렬:최종수정일로 수정, 옵션항목 구분자 추가 및 폰트크기 조정, 인화상품:편집완료로 변경 및 복사기능 제외, 삭제예정일 추가.
* @request : 
* @desc :
* @todo :
*/

$podspage = true;
include "../_header.php";
include_once "../lib/nusoap/lib/nusoap.php";

switch ($_REQUEST[mode]){
	case "del":
		if ($sess[mid]){
			$hidelog = "user_del(member) $sess[mid] $_COOKIE[cartkey] / ".date("y-m-d H:i:s");
			$db->query("update exm_edit set _hide = '1', _hidelog = '$hidelog' where cid = '$cid' and storageid = '$_REQUEST[storageid]' and mid = '$sess[mid]' and _hide != 1");
		} else if ($_COOKIE[cartkey]){
			$hidelog = "user_del(non-member) $_COOKIE[cartkey] / ".date("y-m-d H:i:s");
			$db->query("update exm_edit set _hide = '1', _hidelog = '$hidelog' where cid = '$cid' and storageid = '$_REQUEST[storageid]' and editkey = '$_COOKIE[cartkey]' and _hide != 1");
		}
		go($_SERVER[HTTP_REFERER]);
	exit;
	break;

	//20140925 / minks / 편집타이틀 수정 추가
	case "edittitle":	
		$_GET[title] = addslashes($_GET[title]);
		
		$query = "update exm_edit set title='$_GET[title]' where storageid='$_GET[storageid]'";
		$db->query($query);

		//장바구니 편집타이틀 수정 추가 / 20170914 / kdk
		$query = "update exm_cart set title='$_GET[title]' where storageid='$_GET[storageid]'";
      	$db->query($query);
	  
		go($_SERVER[HTTP_REFERER]);
	exit;
	break;

	//20140930 / minks / 편집정보 이관 추가	
	case "transfer_edit":
		if ($_GET[cartno]){
			foreach ($_GET[cartno] as $k=>$v){
				$query = "update exm_edit set mid='$_GET[mid]', updatedt=now() where storageid='$v'";
				$query2 = "update exm_cart set mid='$_GET[mid]', updatedt=now() where storageid='$v'";

				$db->query($query);
				$db->query($query2);
			}
		}

		?>
		<script> window.opener.location.reload(); window.close(); </script>
      <?

   exit;
   break;
}

$hidelog = "auto_del / $cfg[source_save_days] "._("보관기간만료")." editlist/".date("y-m-d H:i:s");

$db->query("update exm_edit set _hide = '1',_hidelog = '$hidelog' where cid = '$cid' and _hide != '1' and date_format(updatedt,'%Y-%m-%d') <= adddate(curdate(), interval -$cfg[source_save_days] day)");

$db->query("delete from exm_cart where cid = '$cid' and date_format(updatedt,'%Y-%m-%d') <= adddate(curdate(), interval -$cfg[source_save_days] day)");

function _ilark_vars($vars,$flag=";"){
	$r = array();
	$div = explode($flag,$vars);
	foreach ($div as $tmp){
		$pos = strpos($tmp,"=");
		list ($k,$v) = array(substr($tmp,0,$pos),substr($tmp,$pos+1));
		if (!$k) continue;
		$r[$k] = $v;
	}
	return $r;
}

$addwhere = ($sess[mid]) ? "mid = '$sess[mid]'":"editkey = '$_COOKIE[cartkey]'";
if (!$sess[mid] && !$_COOKIE[cartkey]){
	$addwhere = "0=1";
}

#복수 편집기 처리 pods_use, podskind, podsno exm_edit 테이블 사용 2016.03.16 by kdk
$query = "
select
	a.storageid,a.goodsno,
	case 
	when a.podskind = '' then b.podskind
	when a.podskind is null then b.podskind
	else a.podskind
	end as 'podskind',
	case 
	when a.pods_use = '' then b.pods_use
	when a.pods_use is null then b.pods_use
	else a.pods_use
	end as 'pods_use',
	case 
	when a.podsno = '' then b.podsno
	when a.podsno is null then b.podsno
	else a.podsno
	end as 'podsno'
	#,b.podskind,b.pods_use
from
	exm_edit a
	inner join exm_goods b on a.goodsno = b.goodsno
	inner join exm_goods_cid x on x.cid = a.cid and x.goodsno = a.goodsno
	left join exm_goods_opt c on a.goodsno = c.goodsno and c.optno = a.optno
where a.cid = '$cid' and $addwhere and _hide != 1
";

$res = $db->query($query);

while ($data = $db->fetch($res)) {
	if ($data[storageid] && $data[goodsno]!="-1") {
		//2013.12.27 / minks / podskind값을 $data에서 가져옴
		if (in_array($data[podskind],$r_podskind20) || $data[pods_use]==3) { /* 2.0 상품 */
			$r_storageid20[] = trim($data[storageid]);
		} else {
			$r_storageid[] = trim($data[storageid]);
		}
	}
}

$podsApi = new PODStation();

if ($r_storageid){
	$r_storageid = trim(implode(",",$r_storageid));
	$ret_result = $podsApi->GetMultiOrderInfoResult($r_storageid, true);
	
	if ($ret_result)
	{
		foreach ($ret_result as $v) {
			$v = _ilark_vars(substr($v,8));
			$pod_ret[$v[ID]] = $v;
		}
	}
}

if ($r_storageid20){
	$r_storageid20 = trim(implode(",",$r_storageid20));
	$podsApi->setVersion('20');
	$ret_result = $podsApi->GetMultiOrderInfoResult($r_storageid20);

	list($flag) = explode("|",substr($ret_result,0,8));
	$ret_result = substr($ret_result,strpos($ret_result,"STORAGE_ID"));
	$ret_result = explode("|^|",$ret_result);

	if ($ret_result) foreach ($ret_result as $v){
		$v = _ilark_vars(substr($v,8));
		$pod_ret[$v[ID]] = $v;
	}
}

#복수 편집기 처리 pods_use, podskind, podsno exm_edit 테이블 사용 2016.03.16 by kdk
$query = "
select
	*,a.state,a.goodsno,a.updatedt,
	#if (c.podsno>0,c.podsno,b.podsno) podsno

	case 
	when a.podskind = '' then b.podskind
	when a.podskind is null then b.podskind
	else a.podskind
	end as 'podskind',

	case 
	when a.pods_use = '' then b.pods_use
	when a.pods_use is null then b.pods_use
	else a.pods_use
	end as 'pods_use',

	case
	when a.podsno = '' then b.podsno
	when a.podsno is null then b.podsno
	else a.podsno
	end as 'podsno'

	,a.addoptno
	,date_add(a.updatedt, interval $cfg[source_save_days] day) as source_del_days
from
	exm_edit a
	inner join exm_goods b on a.goodsno = b.goodsno
	inner join exm_goods_cid x on x.cid = a.cid and x.goodsno = a.goodsno
	left join exm_goods_opt c on a.goodsno = c.goodsno and c.optno = a.optno
where a.cid = '$cid' and $addwhere and _hide != 1 order by a.updatedt desc
";
$res = $db->query($query);

while ($data = $db->fetch($res)){
   $ret = $pod_ret[$data[storageid]];

    //pods2.0 storageid(22자리)만 사용하고 사용자파일 업로드 주문건은 제외함.
    if (strlen($data[storageid]) != 22) continue;

	if ($flag!="success"){
		$db->query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
		$data[state] = 0;
	} else {
		# 인화상품의 경우 인화옵션 내역파악 3010,3011,3020 
		if (in_array($data[podskind],array(1,2, 3010, 3011, 3020))){
			$ret[DATA] = str_replace("[","",$ret[DATA]);
			$ret[DATA] = str_replace("]","",$ret[DATA]);
			$ret[DATA] = explode("|",$ret[DATA]);
			foreach ($ret[DATA] as $v){
				unset($printopt);
				$v2 = _ilark_vars($v,",");
				$printopt[printoptnm] = $v2[size];
				$printopt[ea] = $v2[count];
				$data[r_print_opt][] = $printopt;
			}
			
			$printopt_desc = "";
			foreach ($data[r_print_opt] as $key => $val) {
				$printopt_desc .= $val[printoptnm].":".$val[ea].",";
			}
			
			//인화옵션항목 출력값 수정.
			$data[printopt] = $data[r_print_opt];
			$data[printopt_desc] = substr($printopt_desc , 0, -1);
            
            $data[state] = 1;
            $data[PROGRESS] = "100%";

		} else if(in_array($data[podskind],array(28,3180))) {
			$query = "select optno from exm_goods_opt where goodsno = '$data[goodsno]' and opt1 = '$ret[TOTALCOUNT]'";
			list($data[optno]) = $db->fetch($query,1);
			$db->query("update exm_edit set optno = '$data[optno]' where storageid = '$data[storageid]'");
		} else {
			# 포토북의 제작 상태 가져오기
			$ret[PROGRESS] = explode("/",$ret[PROGRESS]);
			if ($ret[PROGRESS][0]!=$ret[PROGRESS][1]){
				$add_errmsg = $ret[PROGRESS][0]." / ".$ret[PROGRESS][1];
				$db->query("update exm_edit set state = 0 where storageid = '$data[storageid]'");
				$data[state] = 0;
			} else {
				$db->query("update exm_edit set state = 1 where storageid = '$data[storageid]' and state != 9");
				list ($data[state]) = $db->fetch("select state from exm_edit where storageid = '$data[storageid]'",1);
			}

         //2013.12.31 / minks / 에러메시지 수정( 0/1 -> 50% )
         //20140123 by kdk
         if (in_array($data[podskind],array(3030,3040,3041,3042,3043,3050,3051,3052,3060,3110,3112,3053,3054))) {
             //2013.12.26 / minks / 편집중일 때만 몇 퍼센트 편집됬는지 진행률을 나타냄
             if($ret[PROGRESS][0]==0){
                 $data[PROGRESS] = "0%";
             }
             else{
                 $data[PROGRESS] = round($ret[PROGRESS][0] / $ret[PROGRESS][1] * 100)."%";
             }
         }
         else {
             $data[PROGRESS] = "";
         }
      }

		if ($data[optno]){
			# 상품옵션정보 exm_goods_opt 데이터 수집 (옵션,옵션가 및 상품정보)
			$query = "
			select
				a.*,
				if(b.aprice is null,a.aprice,b.aprice) aprice,
				areserve
			from
				exm_goods_opt a
				left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
			where
				a.goodsno = '$data[goodsno]'
				and a.optno = '$data[optno]'
			";
			$tmp = $db->fetch($query);

			if (!$data[optnm1]) $data[optnm1] = _("옵션1");
			if (!$data[optnm2]) $data[optnm2] = _("옵션2");
			if ($tmp[opt1]) $data[opt][] = $data[optnm1].":".$tmp[opt1];
			if ($tmp[opt2]) $data[opt][] = $data[optnm2].":".$tmp[opt2];
			if (is_array($data[opt])) $data[opt] = implode(" / ",$data[opt]);
         
         //옵션을 사용하면 옵션의 데이터를 가져온다.
         if ($tmp[podsno]) $data[podsno] = $tmp[podsno];
		}
		
		$data[r_addoptno] = $data[addoptno];

		if ($data[addoptno]){
			$data[addoptno] = explode(",",$data[addoptno]);
			foreach ($data[addoptno] as $k=>$v){

				$query = "
				select
					a.*,
					if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
					addopt_areserve,
					c.*
				from
					exm_goods_addopt a
					left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
					inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
				where
					a.addoptno = '$v'
				";
				$tmp = $db->fetch($query);

				if (!$tmp[goodsno]) $data[error] = 4;
				if ($tmp[addopt_bundle_view]) $data[error] = 5;
				if ($tmp[addopt_view]) $data[error] = 5;

				$data[addopt][] = $tmp;
				$data[addopt_aprice] += $tmp[addopt_aprice];
				$data[addopt_areserve] += $tmp[addopt_areserve];
			}
		}
	}

	$query = "
	select itemstep,est_order_data from
		exm_ord_item
	where
		storageid = '$data[storageid]'
		and itemstep not in (0,-1)
	";
	list($step,$est_order_data) = $db->fetch($query,1);
	if ($step){
		$db->query("update exm_edit set state = 9 where storageid = '$data[storageid]'");
		$data[state] = 9;
	}

	###편집정보 미리보기 이미지###
	$loopImgsP1 = array();
	$loopImgs = array();
	
	list($previewLink) = $db->fetch("select preview_link from tb_editor_ext_data where storage_id = '$data[storageid]'", 1);
	//debug($previewLink);
	if($previewLink == "") {
   		if (in_array($data[podskind], $r_podskind20)) {// 2.0 상품
			$podVersion = '20';
		} else {
			$podVersion = '10';
		}
		$clsPods = new PODStation($podVersion);
		
		if ($cfg[skin_theme] == "P1") {
			//$loopImgsP1 = $clsPods->GetPreViewImg($data[storageid]);
		} else {
			$loopImgs = $clsPods->GetPreViewImg($data[storageid]);
		}
	}
	else {
		$loopImgs = explode("|", $previewLink);
  		$loopImgs = array_notnull($loopImgs);
	}
	//debug($loopImgs);
   
   if (count($loopImgsP1) > 0) {
   	  //픽스토리일 경우 미리보기 출력
   	  //속도가 느려 제거함  
	  //$data[preview_img] = "<img src='$loopImgsP1[0]' style='display:none;' onerror='this.src=\"/data/noimg.png\"'/>";
   } else if($loopImgs[0] && $loopImgs[0] != "") {
      $attr_width = "width='100'";
      //$attr_height = "height='$height'";
		$attr_style = "style='border:1px solid #dedede'";

      $img = "<img src='$loopImgs[0]' $attr_width $attr_height $attr_style onerror='this.src=\"/data/noimg.png\"'/>";
		//debug($img);

		//편집정보 미리보기 이미지
		$data[img] = $img;
	} else {
		//$loopImgs 가 없는 경우는 기본 상품 이미지로 출력.(출력되지 않는 버그였음) 		20161027	chunter
		$data[img] = goodsListImg($data[goodsno],'40','','border:1px solid #dedede');
	}

	###상품명 링크###
	//debug($est_order_data);
	$optionData = json_decode($est_order_data, true);
	//$optionData = orderJsonParse2($est_order_data);
	//debug($optionData);

	$link = getViewLinkWithTemplate($data[goodsno], $optionData[templateSetIdx], $optionData[templateIdx]);

	//상품명 링크
	$data[link] = $link;
    
    //3055편집기일때 커버옵션정보를 가져온다. 
    if (in_array($data[podskind],array(3055))){
        //debug($data[podskind]);
        $ext_data = $db->fetch("select * from tb_editor_ext_data where storage_id = '$data[storageid]'");
        $ext_json_data = json_decode($ext_data[editor_return_json],1);
        //debug($ext_json_data);
        
        $cover_range_data = $db->fetch("select * from md_cover_range_option where cover_id = '$ext_json_data[cover_range_id]'");
        
        //debug($cover_range_data);
        $data[cover_range_data] = $cover_range_data[cover_range]."/".$r_cover_type[$cover_range_data[cover_type]]."/".$cover_range_data[cover_paper_name]."/".$cover_range_data[cover_coating_name];
        //debug($data[cover_range_data]);
        $data[cover_id] = $cover_range_data[cover_id];
    }    
	
	$data[title] = str_replace("\"", "&quot;", stripslashes($data[title]));
	
	if ($cfg[skin_theme] == "P1") {
		$m_goods = new M_goods();
		$categoryArr = $m_goods->getGoodsCategoryInfo($cid, $data[goodsno]);
		foreach ($categoryArr as $cat_k=>$cat_v) {
			switch (strlen($cat_v[catno])) {
				case "3":
					$data[catno1] = $cat_v[catno];
					$data[catnm1] = $cat_v[catnm];
					break;
				case "6":
					$data[catnm2] = $cat_v[catnm];
					break;
				case "9":
					$data[catnm3] = $cat_v[catnm];
					break;
				case "12":
					$data[catnm4] = $cat_v[catnm];
					break;
			}
		}
		
		if ($data[opt]) {
			$data[opt] = explode(" / ", $data[opt]);
			if ($data[opt][0]) {
				$data[opt][0] = explode(":", $data[opt][0]);
				$data[mobile_opt] = $data[opt][0][1];
			}
			if ($data[opt][1]) {
				$data[opt][1] = explode(":", $data[opt][1]);
				$data[mobile_opt] .= " / ".$data[opt][1][1];
			}
		}
	}

	if ($data[pods_editor_type]) {
		$data[pods_editor_type] = json_decode($data[pods_editor_type],1);
	}
  
	$loop[] = $data;
}

// M2스킨의 경우 최종수정날짜,삭제예정날짜 시간 삭제(Y-m-d) 표시
if($cfg[skin_theme] == "M2"){
	if($loop){
		foreach($loop as $k => $v){
			if($v['updatedt']) $loop[$k]['updatedt'] = date("Y-m-d", strtotime($v['updatedt']));
			if($v['source_del_days']) $loop[$k]['source_del_days'] = date("Y-m-d", strtotime($v['source_del_days']));
		}
	}
}
$r_state = array("0"=>_("편집중"),"1"=>_("편집완료"),"9"=>_("주문접수"));
 //debug($loop);

// kidsnote의 경우 상품페이지 에서 센터 리스트 선택하는 selectbox 필요 211113 jtkim
if ($cid == "kidsnote") {
    $kn_centerList = array();
    if ($_COOKIE['kidsnote_access_token']) {
        $cfg[sns_login] = unserialize($cfg[sns_login]);

        $kidsnote_client_url = $cfg[sns_login][kidsnote_client_url] . "o/token/";
        $kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
        $kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
        $kidsnote_redirect_uri = "http://" . $_SERVER['SERVER_NAME'] . "/_oauth/callback_kidsnote.php";
        $kidsnote_code = $_GET['code'];

        $kidsnote_me_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/";
        $kidsnote_me_center_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/center";
        $kidsnote_me_employments_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/employments";
        $header_data = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $header_data[] = 'Content-Type: application/json';
        $header_data[] = 'Authorization: ' . $_COOKIE['kidsnote_token_type'] . " " . $_COOKIE['kidsnote_access_token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $kidsnote_me_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $me_type = curl_exec($ch);
        curl_close($ch);
        $me_type_arr = json_decode($me_type, true);
        //print_r($me_type_arr);
        if ($me_type_arr['error']) {
            //echo "회원정보 가져오기 실패";
        }

        //admin 원장 / teacher 교사 / parent 부모
        if ($me_type_arr['type'] == "teacher") {
            $ch_ = curl_init();
            curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_employments_url);
            curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data);
            curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
            $me_teacher = curl_exec($ch_);
            // $info = curl_getinfo($ch_);
            //print_r($info);
            curl_close($ch_);
            $me_teacher_arr = json_decode($me_teacher, true);
            // print_r($me_teacher_arr);
            // 센터리스트 파싱 이후 $kn_centerList array push
            // api 응답이 오지 않아 확인 필요함
            if(isset($me_teacher_arr)){
                if(count($me_teacher_arr) > 0){
                    // center id 중복 제거
                    forEach($me_teacher_arr as $k){
                        $center_id_duplicate = false;
                        forEach($kn_centerList as $k_){
                            if($k['center_id'] == $k_['center_id']){
                                $center_id_duplicate = true;
                            }
                        }
                        if($center_id_duplicate === false){
                            array_push($kn_centerList, $k);
                        }
                    }
                }
            }

        } else if ($me_type_arr['type'] == "admin") {
            $ch_ = curl_init();
            curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_center_url);
            curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data);
            curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_, CURLOPT_VERBOSE, true);
            $me_admin = curl_exec($ch_);
            //$info = curl_getinfo($ch_);
            //print_r($info);
            curl_close($ch_);
            $me_admin_arr = json_decode($me_admin, true);
            // print_r($me_admin_arr);
            if(isset($me_admin_arr)){
                array_push($kn_centerList, array('center_id' => $me_admin_arr['id'] , 'center_name' => $me_admin_arr['name']));
            }
        }

    }
    if($_COOKIE['kidsnote_access_token'] && $_COOKIE['kidsnote_token_type']) $tpl->assign("kn_token",$_COOKIE['kidsnote_token_type']." ".$_COOKIE['kidsnote_access_token']);
    $tpl->assign("kn_centerList",$kn_centerList);

    if($loop && count($kn_centerList) > 0){
        foreach ($loop as $idx => $v) {
            $loop[$idx]['kn_centerList'] = $kn_centerList;
        }
    }
}
 //print_r($kn_centerList);
$tpl->assign("loop",$loop);
$tpl->print_('tpl');
?>