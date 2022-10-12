<?

include "../lib/library.php";

$m_board = new M_board();
$m_etc = new M_etc();
$m_member = new M_member();
//$m_goods = new M_goods();

/***/switch ($_REQUEST[mode]){
   
   case "get_pods_goods_data":
      
      //$_GET[goodsno] = $data[goodsno];

      $goods = new Goods();
      $goods->getView($_POST[goodsno]);
      $data_goods = $goods->viewData;
	  
	  if (!$GLOBALS[cfg][podsiteid]) $GLOBALS[cfg][podsiteid] = $GLOBALS[cfg_center][podsiteid];
      $siteCode = $GLOBALS[cfg][podsiteid];
	  
	  if ($data_goods[pods_useid] != "center" && $data_goods[podsno]) {
	  	list($dummy) = $db->fetch("select self_podsiteid from exm_mall where cid = '$cid'", 1);
	  	if ($dummy) $siteCode = $dummy;
	  }
	  
	  if ($_POST[podsno]) $siteProductCode = $_POST[podsno];
	  else $siteProductCode = $data_goods[podsno];
	  
	  if ($_POST[podoptno]) $optionCode = $_POST[podoptno];
   
      if ($data_goods[r_opt][0][item]) {
         foreach ($data_goods[r_opt][0][item] as $key => $val) {
            if ($optionCode == "" && $val[podoptno] != "") $optionCode = $val[podoptno];
            if ($siteProductCode == "" && $val[podsno] != "") $siteProductCode = $val[podsno];
            break;
         }
      }
	  
      if (!$optionCode) $optionCode = 1;
      
      $soapurl = 'http://'.PODS20_DOMAIN;
      $soapurl .= "/CommonRef/StationWebService/GetSiteProductTemplateToJson.aspx?";
      $param = "siteCode=$siteCode&siteProductCode=$siteProductCode&optionCode=$optionCode";

      $ret = readUrlWithcurl($soapurl.$param, false);
      $result[success] = "true";
   
      if ($ret) {
         //fail.
         if (strpos($ret, "fail|") === false) {
            //$result[previewList] = $ret;
            $obj = json_decode($ret, TRUE);
            //debug($obj);
            $result[previewList] = $obj;
            //debug($obj);
            $frame_cnt = 0;
			
			if ($obj) {
	            foreach ($obj as $key => $val) {
	               $frame_cnt += $val[frame_cnt];
	            }
			}
      
            $result[frameCnt] = $frame_cnt;
         }
         else {
            $result[success] = "false";
            $result[resultMsg] = $ret;
         }
      }
   
      $page = $result[previewList];
      
      if ($page) {
	      foreach ($page as $key => $val) {
	         if ($val[type] == "epilog") {
	            $loop_epilog[] = $val;
	         } else {
	            $loop[] = $val;
	         }
	      }
	  }
	  
      if ($loop && $loop_epilog) {
         $previewList = array_merge($loop, $loop_epilog);
	  } else if ($loop) {
	  	$previewList = $loop;
	  } else if ($loop_epilog) {
	  	$previewList = $loop_epilog;
	  }
	  
	  if ($_POST[goodstype] == "book") {
	  	 $data[option_preview] = "";
	  	
	  	 if ($previewList) {
	  		foreach ($previewList as $k => $v) {
	  			/*if ($v[type] == "cover") {
	  				$data[option_preview] .= "<div class=\"hard\" style=\"background-image:url($v[url])\"></div>";
				} else {
					$data[option_preview] .= "<div class=\"double\" style=\"background-image:url($v[url]);background-size:100% 100%;\"></div>";
				}*/
				
				$data[totcnt] = count($previewList);
				$data[width] = "800px";
				$data[height] = (800 * ($v[height] / $v[width]))."px";
				$data[option_preview] .= "<img class=\"main_goods_img\" src=\"$v[url]\" style=\"position:absolute;\">";
			}
		 }
	  } else if ($_POST[goodstype] == "calendar") {
	  	 $data[option_preview] = "";
	  	
	  	 if ($previewList) {
	  		foreach ($previewList as $k => $v) {
	  			$data[totcnt] = count($previewList);
				$data[width] = "800px";
				$data[height] = (800 * ($v[height] / $v[width]))."px";
				$data[option_preview] .= "<img class=\"main_goods_img\" src=\"$v[url]\" style=\"position:absolute;\">";
			}
		 }
	  } else if ($_POST[goodstype] == "fancy") {
	  	 $data[option_preview] = "";
		 $data[sub_option_preview] = "";
		
		 if ($previewList) {
	  		foreach ($previewList as $k => $v) {
				$data[width] = "800px";
				$data[height] = (800 * ($v[height] / $v[width]))."px";
	  			$data[option_preview] .= "<img class=\"myclass main_goods_img pods_goods_img\" src=\"$v[url]\" style=\"margin:-45px -90px;\" />";
				//$data[sub_option_preview] .= "<div class=\"w3-col s4\"><img class=\"demo w3-opacity w3-hover-opacity-off sub_goods_img\" src=\"$v[url]\" index=\"0\" style=\"cursor:pointer; width:70px; height:70px;\" /></div>";
				
				$data[sub_option_preview] .= "<div class=\"w3-col s4\"><img class=\"demo w3-opacity w3-hover-opacity-off sub_goods_img\" src=\"$v[url]\" index=\"0\" style=\"cursor:pointer;\" /></div>";
			}
		 }
	  }

      echo json_encode($data);
	  
   exit; break;
   

case "sendSmsMobileLink_miodio":
    
    include_once dirname(__FILE__)."/../lib/class.sms.php";
    $sms = new Sms();

    $from = "1544-3225";
    $sms->from = $from;
    $sms->to = $_POST[to];
    
    //For iOS
    //[미오디오] 아래 링크를 통해 미오디오 앱을 시작하세요~ https://goo.gl/2dhKfn

    //For Android
    //[미오디오] 아래 링크를 통해 미오디오 앱을 시작하세요~ https://goo.gl/EnCj6Z
    
    $link = "https://goo.gl/EnCj6Z"; //Android
    if ($_POST[app] == "iOS") $link = "https://goo.gl/2dhKfn"; //iOS

    $sms->send(_("[미오디오] 아래 링크를 통해 미오디오 앱을 시작하세요~")." ".$link);

    exit;break;
    
case "sendSmsMobileLink":
    
    include_once dirname(__FILE__)."/../lib/class.sms.php";
    $sms = new Sms();

    $from = "1544-4790";
    $sms->from = $from;
    $sms->to = $_POST[to];
    
    //For iOS
    //[포토큐브] 아래 링크를 통해 포토큐브 앱을 시작하세요~ https://goo.gl/4QBiJL

    //For Android
    //[포토큐브] 아래 링크를 통해 포토큐브 앱을 시작하세요~ http://goo.gl/hKaaon

    $link = "http://goo.gl/hKaaon"; //Android
    if ($_POST[app] == "iOS") $link = "https://goo.gl/4QBiJL"; //iOS
        
    //$sms->send(_("[포토큐브] 아래 링크를 통해 포토큐브 앱을 시작하세요~")." http://goo.gl/hKaaon");
    $sms->send(_("[포토큐브] 아래 링크를 통해 포토큐브 앱을 시작하세요~")." ".$link);

    exit;break; 

case "getCoverRangeOption" :
   //debug($_POST);

   $data = $db->listArray("select cover_range from md_cover_range_option where goodsno = '$_POST[goodsno]' group by cover_range");

   foreach($data as $val){
      if($val[cover_range] == $_POST[cover_range]) $selected = "selected";
      else $selected = "";
      $json_data[cover_range] .= "<option value=\"$val[cover_range]\" $selected>$val[cover_range]</option>";
   }

   $data = $db->listArray("select cover_type from md_cover_range_option where goodsno = '$_POST[goodsno]' and cover_range = '$_POST[cover_range]' group by cover_type");
   foreach($data as $val){
      if($val[cover_type] == $_POST[cover_type]) $selected = "selected";
      else $selected = "";
      $cover_type = $r_cover_type[$val[cover_type]];
      $json_data[cover_type] .= "<option value=\"$val[cover_type]\" $selected>$cover_type</option>";
   }

   $data = $db->listArray("select cover_paper_code from md_cover_range_option where goodsno = '$_POST[goodsno]' and cover_range = '$_POST[cover_range]' and cover_type = '$_POST[cover_type]' group by cover_paper_code");

   foreach($data as $val){
      if($val[cover_paper_code] == $_POST[cover_paper_code]) $selected = "selected";
      else $selected = "";
      $cover_paper_code = $r_cover_paper[$val[cover_paper_code]];
      $json_data[cover_paper_code] .= "<option value=\"$val[cover_paper_code]\" $selected>$cover_paper_code</option>";
   }
   
   
   $data = $db->listArray("select cover_coating_code from md_cover_range_option where goodsno = '$_POST[goodsno]' and cover_range = '$_POST[cover_range]' and cover_type = '$_POST[cover_type]' and cover_paper_code = '$_POST[cover_paper_code]' group by cover_coating_code");
   if(!$data){
      $data = $db->listArray("select cover_coating_code from md_cover_range_option where goodsno = '$_POST[goodsno]' and cover_range = '$_POST[cover_range]' and cover_type = '$_POST[cover_type]' group by cover_coating_code");
   }
   foreach($data as $val){
      if($val[cover_coating_code] == $_POST[cover_coating_code]) $selected = "selected";
      else $selected = "";
      $cover_coating_code = $r_cover_coating[$val[cover_coating_code]];
      $json_data[cover_coating_code] .= "<option value=\"$val[cover_coating_code]\" $selected>$cover_coating_code</option>";
   }
   
   //debug($json_data);
   $json_data = json_encode($json_data);
   echo $json_data;
   exit;
   break;
   
### 상품 미리보기 / 20170808 / kdk
case "ajax_preview_json":

	$result = array();
	$bEditor = true;
	$option = "";
	
	$_GET[goodsno] = $_REQUEST[goodsno];
	$_GET[catno] = $_REQUEST[catno];
	
	$templateset_id = $_REQUEST[templateset_id];
	$template_id = $_REQUEST[template_id];
	

    try {

		$goods = new Goods();
		$goods->getView();
		$editor = $goods->editor;
		$data = $goods->viewData;
		$category = $goods->category;
		
		//debug($data);
		//debug($editor);
		//debug($category);
		
		foreach ($editor as $key => $val) {
			if($val[pods_use] == "0") {
				$bEditor = false;
				break;
			}
		}
		
		if($bEditor) {
			//debug("편집기 있어!");
			// 상품 옵션.			
			if((int)$data[pageprice] > 0) {
				$option .= "<tr>
					<th><span>"._("페이지추가 가격")."</span></th>
					<td>". number_format($data[pageprice]) ." ("._("본 가격은 추가단위당의 가격입니다.").")</td>
				</tr>";
			}

			if(!in_array($data[podskind],array(28,3180))) {
				if($data[r_opt]) {
					$i = 0;
					foreach ($data[r_opt] as $key => $val) {
						//debug($key);
						//debug($val);

						$name = ($data[optnm][$key]) ? $data[optnm][$key] : _("옵션").($key+1); 
						$select = "<select class=\"selectType\" name=\"optno[]\" onchange=\"updateOption(this)\" required><option value=\"\">"._("선택")."</option>";

						foreach ($val as $key1 => $val1) {
							//debug($key1);
							//debug($val1);
							$item = "";
							foreach ($val1 as $key2 => $val2) {
								$item .= "<option value=\"$val2[optno]\" title=\"$val2[optnm]\" stock=\"$val2[stock]\" podoptno=\"$val2[podoptno]\" productid=\"$val2[podsno]\">$val2[optnm]";

								if($val2[aprice] && count($data[r_opt])==1) {
									$item .= "(+". number_format($val2[aprice]) ._("원").")";
								}

								$item .= "</option>";
							}
						}

						$select .= $item . "</select>";

						$option .= "<tr>
							<th><span>$name</span></th>
							<td>$select</td>
						</tr>";
					}
				}
			}

			// 상품명.
			$result[goodsnm] = $data[goodsnm];

			// 상품 옵션.			
			$result[optionHtml] = "<table cellpadding=\"0\" cellspacing=\"0\" class=\"estTable\">
				<colgroup>
					<col class=\"inp-w-80\" /><col width=\"*\" />
				</colgroup>
				<tbody>
				$option
				</tbody>
			</table>";
			
			// 상품 가격.			
			if ($data[cprice]) {
				$cprice = "<div class=\"amount2\">"._("소비자가격")." ".  number_format($data[cprice]) . _("원")."</div>";
				$cprice2 = "<div class=\"amount2\" id=\"goods_cprice_x\">(▼ ". number_format($data[cprice]-$data[price]) ._("원 할인").")</div>";
			}
			
			if ($data[strprice]) {
				$price = "<span>$data[strprice]</span>";
			}
			else {
				$price = "<span id=\"goods_price\" class=\"amount\">". number_format($data[price]) ._("원")."</span>" . $cprice2;
			}
			
			$result[priceHtml] = "
			$cprice
			<div><span class=\"amount\">"._("판매가격")."</span> 
				$price
			</div>";
			
			// 편집기 호출 버튼.
			if ($data[podskind] > 0) { //pods 연동 상품이면...
				if($editor) {
					foreach ($editor as $key => $val) {
						//debug($key);
						$pods_use = $val[pods_use];
						$podskind = $val[podskind];
						$podsno = $val[podsno];
						$templateSetIdx = $templateset_id;
						$templateIdx = $template_id;
						
						if ($val[pods_use] == "3") {
							//pods_use = 3 간편편집 주문하기
							$btnSpan = "<span class=\"btn_01\"></span>"._("간편편집주문");
						}
						else if ($val[pods_use] == "2") {
							//pods_use = 2 고급편집 주문하기
							$btnSpan = "<span class=\"btn_02\"></span>"._("고급편집주문");
						}
						else {
							$btnSpan = "<span class=\"btn_04\"></span>"._("편집하기");
						}

						$editorBtn .= "<a href=\"javascript:call_exec('$pods_use', '$podskind', '$podsno', '$templateSetIdx', '$templateIdx');\" class=\"estimate_btn\">$btnSpan</a>";

						if($key == 0) break; //편집기 한개만 사용. 복수편집기 예정.
					}
				}
				//수동업로드 내파일 주문하기
				//$editorBtn .= "<a href=\"javascript:;\" onclick=\"fileInfoOpenLayer(this);\" class=\"estimate_btn\"><span class=\"btn_03\"></span>$btnLbl</a>";
			}
			else {
				if ($data[order_type] == "UPLOAD") {
					if ($data[extra_auto_pay_flag] == "0") {
						//견적의뢰
						$editorBtn .= "<a href=\"javascript:;\" onclick=\"orderInfoOpenLayer(this);\" class=\"estimate_btn\"><span class=\"btn_05\"></span>"._("견적의뢰")."</a>";
					}
					else {
						//수동업로드 내파일 주문하기
						$editorBtn .= "<a href=\"javascript:;\" onclick=\"fileInfoOpenLayer(this);\" class=\"estimate_btn\"><span class=\"btn_03\"></span>$btnLbl</a>";
					}
				}
				else {
					//바로구매
					$editorBtn .= "<a href=\"javascript:;\" onclick=\"exec('buy');\" class=\"estimate_btn\"><span class=\"btn_05\"></span>"._("바로구매")."</a>";
			
					//장바구니
					$editorBtn .= "<a href=\"javascript:;\" onclick=\"exec('cart');\" class=\"estimate_btn\"><span class=\"btn_03\"></span>"._("장바구니")."</a>";
				}
			}

			$result[editorHtml] = $editorBtn;

			// pods2.0 템플릿 미리보기 이미지 연동.
			$result[previewList] = "";

			# PODs2 파라메타

			# podstation siteid
			if (!$GLOBALS[cfg][podsiteid])
				$GLOBALS[cfg][podsiteid] = $GLOBALS[cfg_center][podsiteid];

			# 자체편집상품일경우
			// 조건 오류로 인해 자체상품 편집기 호출되지 않았음. 조건 수정   20131202    chunter
			// cfg 배열을 변경할 경우 추후 센터 상품 편집기 호출시 문제됨. 변수 할당으로 변경.   20131202    chunter
			$siteCode = $GLOBALS[cfg][podsiteid];
			if ($data[pods_useid] != "center" && $data[podsno]) {
				list($dummy) = $db->fetch("select self_podsiteid from exm_mall where cid = '$cid'", 1);
				if ($dummy)
					$siteCode = $dummy;
			}
			//debug($siteCode);
			
			$siteProductCode = $data[podsno];
            //debug($siteProductCode);
	
			//편집정보 옵션코드  조회.
			if($data[r_opt][0][item]) {
				foreach ($data[r_opt][0][item] as $key => $val) {
					if ($val[podoptno]) $optionCode = $val[podoptno];
					if ($siteProductCode == "" && $val[podsno] != "") $siteProductCode = $val[podsno];
					break;
				}
			}
			else {
				$optionCode = "1";
			}
	
			$soapurl = 'http://' .PODS20_DOMAIN;
			$soapurl .= "/CommonRef/StationWebService/GetSiteProductTemplateToJson.aspx?";
			
			//debug($soapurl);
			//siteCode=moderndemo&siteProductCode=modernPageBook&optionCode=1	
			$param = "siteCode=$siteCode&siteProductCode=$siteProductCode&optionCode=$optionCode";
			//debug($soapurl.$param);
			
			//templateSetIdx, templateSetIdx 파라미터 추가				20180117		chunter
			$param .= "&templateSetIdx=$templateset_id&templateIdx=$template_id";
 
			$ret = readUrlWithcurl($soapurl.$param, false);
			//debug($ret);
         $result[success] = "true";

			if ($ret) {
            //fail.
            if (strpos($ret, "fail|") === false) {
               //$result[previewList] = $ret;
               $obj = json_decode($ret, TRUE);
               //debug($obj);
               $result[previewList] = $obj;
               //debug($obj);
               $frame_cnt = 0;
               foreach ($obj as $key => $val) {
                  $frame_cnt += $val[frame_cnt];
               }

               $result[frameCnt] = $frame_cnt;
            }
            else {
               $result[success] = "false";
               $result[resultMsg] = $ret;
            }
         }
         else {
            $result[success] = "false";
            $result[resultMsg] = _("미리보기 정보가 없습니다.");
         }
		}
		else {
         //debug("편집기 없어!");
			$result[success] = "false";
			$result[optionHtml] = "";
			$result[resultMsg] = _("편집기 정보가 없습니다.");
			$result[returnUrl] = "../goods/view.php?catno=$_GET[catno]&goodsno=$_GET[goodsno]";
		}
		
		//$result[success] = "true";

    } catch(Exception $e) {
		$result[success] = "false";
		$result[resultMsg] = $e;
    }

	//debug($result);
	echo json_encode($result);
	
	exit; break;
	
### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
case "ajax_option_json":

    $_POST[option_json] = urldecode(base64_decode($_POST[option_json]));
    $_POST[option_json] = str_replace('"{\"', '{\"', $_POST[option_json]);
    $_POST[option_json] = str_replace('\"}"', '\"}', $_POST[option_json]);

    $query = "
    insert into tb_extra_option_json_temp set
        pod_signed = '$_POST[pod_signed]',
        option_json = '$_POST[option_json]',
        order_title = '$_POST[order_title]',
        order_memo = '$_POST[order_memo]'
    on duplicate key update
        pod_signed = '$_POST[pod_signed]'
    ";
    //debug($query);
    //exit;
    $db->query($query);
	$return_data = $db->id;
	if ($return_data > -1)
		echo("OK");
	else 
		echo("FAIL");
	
	exit; break;

case "brand_search":

	$keyword = trim($_POST[keyword]);
	$query = "select * from exm_brand where brandnm like '%$_POST[keyword]%' or brandnm2 like '%$_POST[keyword]%' order by brandnm";
	$res = $db->query($query);
	$loop[kor] = array();
	while ($data = $db->fetch($res)){
		$loop[kor][] = $data[brandno]."^|^".$data[brandnm];
	}

	$query = "select * from exm_brand where brandnm like '%$_POST[keyword]%' or brandnm2 like '%$_POST[keyword]%' order by brandnm2";
	$res = $db->query($query);
	$loop[eng] = array();
	while ($data = $db->fetch($res)){
		$loop[eng][] = $data[brandno]."^|^".$data[brandnm2];
	}

	$query = "select * from exm_brand where brandnm like '%$_POST[keyword]%' or brandnm2 like '%$_POST[keyword]%' order by sort";
	$res = $db->query($query);
	$loop['sort'] = array();
	while ($data = $db->fetch($res)){
		$loop['sort'][] = $data[brandno]."^|^".$data[brandnm];
	}

//	sleep(1);

	echo json_encode($loop);

	exit; break;

case "ajax_updateOption":

	$_POST[opt1] = htmlspecialchars_decode($_POST[opt1]);

	$loop = array();
	$query = "
	select
		a.*,
		if(b.aprice is null,a.aprice,b.aprice) aprice,
		areserve,
		mall_opt_cprice
	from
		exm_goods_opt a
		left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
	where
		a.goodsno='$_POST[goodsno]'
		and opt1='$_POST[opt1]'
		and opt_view=0
   ";
   $res = $db->query($query);
	while ($data=$db->fetch($res)){
			
		//상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
		list($opt_view) = $db->fetch("select view from tb_goods_opt_mall_view where cid = '$cid' and goodsno = '$data[goodsno]' and optno = '$data[optno]' ",1);
		if($opt_view == "1") continue;

		if ($sess[bid])	$data[aprice] = get_business_goods_opt_price($data[goodsno],$data[optno],$data[aprice]);
		if ($sess[bid])	$data[areserve] = get_business_goods_opt_reserve($data[goodsno],$data[optno],$data[areserve]);

		$loop[] = $data;
	}
	echo json_encode($loop);

	exit; break;

case "addQna":
	if (!$sess[mid]) {
		echo "<script>alert('"._("로그인 후 이용이 가능하십니다.")."');</script>";
		echo "<script>opener.location='../member/login.php?rurl=/goods/view.php?catno=$_POST[catno]&goodsno=$_POST[goodsno]';</script>";
		exit;
	}
	
	$tableName = "exm_mycs";
	$addColumn = "set
		id			= 'qna',
		cid			= '$cid',
		mid			= '$sess[mid]',
		name		= '$sess[name]',
		goodsno		= '$_POST[goodsno]',
		subject		= '$_POST[subject]',
		content		= '$_POST[content]',
		secret		= '$_POST[secret]',
		regdt		= now()";
	$m_board->setCustomerService("insert", $tableName, $addColumn);

	echo "<script>location.href='view.php?catno=$_POST[catno]&goodsno=$_POST[goodsno]';</script>";
	
exit;break;

/***/}  switch ($_GET[mode]){

case "delete_event_comment":
	### 유효성체크
	if (!$_GET[eventno]) break;
	
	$chk = $m_etc->getEventCommentCheck($cid, $sess[mid], $_GET[eventno]);
	if (!$chk[eventno]) msg(_("삭제권한이 없습니다."), -1);
	if ($chk[emoney]) msg(_("적립금 적립 후에는 삭제가 불가능합니다."), -1);
	
	### 삭제
	$m_etc->delEventCommentInfo($cid, $sess[mid], $_GET[eventno]);
	
break;

/***/} switch ($_POST[mode]){

case "write_event_comment":
	### 유효성체크
	if (!$_POST[eventno]) break;
	if (!$sess)	msg(_("로그인후 작성하실 수 있습니다."), -1);
	
	$chk = $m_etc->getEventCommentCheck($cid, $sess[mid], $_POST[eventno]);
	if ($chk[eventno]) msg(_("아이디당 한번만 작성할 수 있습니다."), -1);
	
	### 코멘트저장
	$addColumn = "set
		cid			= '$cid',
		eventno		= '$_POST[eventno]',
		mid			= '$sess[mid]',
		comment		= '".addslashes($_POST[comment])."',
		regdt		= NOW()";
	$m_etc->setEventComment($cid, $addColumn);
	
break;

//20150630 / minks / 모바일 상품 리스트 출력
case "mobile_goods_list":
	include "../lib/class.page.php";
	
	$limit = 10;
	
	$db_table = "exm_goods a
		inner join exm_goods_cid b on b.cid = '$cid' and b.goodsno = a.goodsno
		inner join exm_category_link c on c.cid = '$cid' and c.goodsno = a.goodsno
		left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno";
		
	$where[] = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')" : "bid.bid is null";
	$where[] = "c.catno = '$_POST[catno]'";
	$where[] = "b.nodp = 0";
	$where[] = "a.state < 2";
	
	$orderby = ($_POST[orderby]) ? $_POST[orderby] : "c.sort";
	
	$pg = new Page($_POST[page], $limit);
	$pg->field = "a.*,b.*,c.*,if(b.price is null,a.price,b.price) price";
	$pg->setQuery($db_table, $where, $orderby);
	$pg->exec();
	$res = &$pg->resource;
	
	$loop = array();
	
	while ($data = $db->fetch($res)) {
		if ($data[state] == 1) $state = "["._("품절")."]";
		else $state = "";
		
		echo "<li><a href=\"../goods/view.php?catno=$data[catno]&goodsno=$data[goodsno]&mobile_type=Y\" target=\"_self\">";
		echo "<img src=\"http://$cfg_center[host]/data/goods/$cid/s/$data[goodsno]/$data[clistimg]\" onerror='this.src=\"/data/noimg.png\"' alt=\"\">";
		echo "<span>$data[goodsnm] $state</span>";
		echo "</a></li>";
	}
	
exit; break;
	
case "ajax_setWishlist":
	if ($_POST[goodsno]) {
		$data = $m_member->getWishChk($cid, $sess[mid], $_POST[catno], $_POST[goodsno]);
		
		if ($data) {
			echo(_("이미 찜한 상품입니다."));
		} else {
			$m_member->setWishInfo($cid, $sess[mid], $_POST[catno], $_POST[goodsno]);
			echo("OK");
		}
	} else {
		echo(_("찜할 상품번호가 없습니다."));
	}
	
exit; break;

//review > 미오디오 , review_pix > 픽스토리
case "review":
   
	if (!$sess[name]) $sess[name] = $sess[mid];

   $fname = $_POST[delimg];
   $size_chk = false;
   
   if ($_FILES['img'][size]) {
      foreach($_FILES['img'][size] as $key => $val){
         if($val/1024 > 1024 * 5) $size_chk = true;
      } 
   }

	
   $size_chk = true;
   if($size_chk == false) msg(_("파일 크기가 5MB이상인 파일이 존재합니다."), -1);
   else {
      
      if($_POST[mode_pix] == "review_pix"){
         $payno_pix = explode("|", $_POST[payno]);
         
         $_POST[payno] = $payno_pix[0];
         $_POST[ordno] = $payno_pix[1];
         $_POST[ordseq] = $payno_pix[2];
         $_POST[catno] = $payno_pix[3];
         $_POST[goodsno] = $payno_pix[4];
      }

      $dir = "../data/review/";
      if (!is_dir($dir)) {
         mkdir($dir, 0707);
         chmod($dir, 0707);
      }
      
      $dir = "../data/review/$cid/";
      if (!is_dir($dir)) {
         mkdir($dir, 0707);
         chmod($dir, 0707);
      }
      
      $dir = "../data/review/$cid/$_POST[payno]/";
      if (!is_dir($dir)) {
         mkdir($dir, 0707);
         chmod($dir, 0707);
      }
      
      $dir = "../data/review/$cid/$_POST[payno]/$_POST[ordno]/";
      if (!is_dir($dir)) {
         mkdir($dir, 0707);
         chmod($dir, 0707);
      }
         
      $dir = "../data/review/$cid/$_POST[payno]/$_POST[ordno]/$_POST[ordseq]/";
      if (!is_dir($dir)) {
         mkdir($dir, 0707);
         chmod($dir, 0707);
      }
	
		if($_FILES['img'][tmp_name][0]){
			// 확장자가 jpg,png,pdf가 아닐시 업로드 불가
			$ext = substr(strrchr($_FILES[img][name][0] , "."), 1);
			// 허용 확장자
			$ext_allow = array("jpeg","jpg","png","pdf");
			// 확장자 소문자 처리
			$ext_lower = strtolower($ext);
			//debug($_FILES['img'][name]);
			if (!in_array($ext_lower,$ext_allow)){
				switch($cfg[skin_theme]){
					case "P1" : msg(_("이미지는 jpg,png,pdf 형태만 업로드 가능합니다."), "review_write_pix.php");
					break;
					case "M2" : msg(_("이미지는 jpg,png,pdf 형태만 업로드 가능합니다."), "review_M2.php");
					break;
					default : msg(_("이미지는 jpg,png,pdf 형태만 업로드 가능합니다."), "review.php");
					break; 
				}
			}
		}

      if ($fname && ($_FILES['img'][tmp_name] || $_POST[kind] == "normal")) {
         unlink($dir.$fname);
         $fname = "";
      }

      //debug($_FILES['img']); exit;
      if ($_FILES['img'][tmp_name]) {
			$_FILES['img'][tmp_name] = array_notnull($_FILES['img'][tmp_name]);
         
         foreach($_FILES['img'][tmp_name] as $key => $val) {
            if (is_uploaded_file($val)) {
               $fname[] = $_FILES['img'][name][$key];
               move_uploaded_file($val, $dir.$_FILES['img'][name][$key]);
            }
         }
      }

      if($fname) $img = implode("|", $fname);
      
      $m_member->setReviewInfo($cid, $_POST[payno], $_POST[ordno], $_POST[ordseq], $_POST[catno], $_POST[goodsno], $sess[mid], $sess[name], $_POST[subject], $_POST[content], $_POST[degree], $_POST[review_deny_user], $_POST[kind], $img);
      
      $m_goods = new M_goods();
      $goodsdata = $m_goods->getInfo($_POST[goodsno]);
      
      //적립금 지급처리.
      setAddReviewWrite($cid, $sess[mid], $_POST[payno], $goodsdata[goodsnm]);
      
      if($cfg[skin_theme] == "M2")
         msg(_("후기가 저장되었습니다."), "review_M2.php");
      else if($cfg[skin_theme] == "P1")
         msg(_("후기가 저장되었습니다."), "review_write_pix.php");
      else
         msg(_("후기가 저장되었습니다."), "review.php");
   }
break;

case "goods_like" :

   if(!$sess[mid]){
      echo "not_mid";
   } else {   
      list($like) = $db->fetch("select goods_like from md_goods_like where cid = '$cid' and mid = '$sess[mid]' and goodsno = '$_POST[goodsno]'",1);
      
      if(!$like || $like == '')
         $sql = "insert into md_goods_like set cid = '$cid', mid = '$sess[mid]', goodsno = '$_POST[goodsno]', goods_like = 'Y'";
      else if ($like == 'N')
         $sql = "update md_goods_like set goods_like = 'Y' where cid = '$cid' and mid = '$sess[mid]' and goodsno = '$_POST[goodsno]'";
      else if ($like == 'Y')
         $sql = "update md_goods_like set goods_like = 'N' where cid = '$cid' and mid = '$sess[mid]' and goodsno = '$_POST[goodsno]'";
   
      $db->query($sql);
   
      if($_POST[element]){
         if($_POST[element] == "view"){
            $m_goods = new M_goods();
            $cnt = $m_goods->get_goods_like_cnt($cid, $_POST[goodsno]);
            echo $cnt;
         } else
            echo $_POST[element];
      } else
         echo "ok";
   }
   exit;
   break;

case "delete_comment" :
   $sql = "delete from exm_event_comment where cid = '$cid' and no = '$_POST[no]'";
   $db->query($sql);
   
   echo "ok";
   exit;
   break;

/***/ }

if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);

?>