<?php
Header("Cache-Control: no-store, no-cache, must-revalidate");
Header("Pragma: no-cache");

include "../library.php";
//include "../lib/class.db.php";
//include "../_est_header.php";


$mode = $_GET[mode];

switch ($mode) { 
    case 'get_createkey' : 
        get_createkey(); 
        break;     
    case 'temp_order' : 
        temp_order(); 
        break;
    case 'orderinsert_from_temp' : 
        orderinsert_from_temp(); 
        break;
    case 'orderinsert_post' : 
        orderinsert_post(); 
        break;  
    default : 
        echo 'unknown'; 
} 

  
  //post로 전달받은 정보들 실주문처리(신규주문, 재주문, 수정)
  function orderinsert_post(){
    $params = array(
    'order_ID' => $_POST['orderid'],  
    'order_type' => $_POST['order_type'],
    'proc_type' => $_POST['proc_type'],
    'storage_code' => $_POST['storage_code'],   
    'status' => $_POST['status'],
    'product_ID' => $_POST['estm_productid'],
    'order_data' => $_POST['orderdata'],
    'order_title' => $_POST['order_title'],
    'order_memo' => $_POST['order_memo'],
    'cartno' => $_POST['cartno'],
    );
        
    orderinsert_proc($params);
    exit;
  }
  
  
  /* 로컬보관함 사용을 위한 임시 저장*/
  function temp_order(){
    global $cid, $cfg, $cfg_center, $sess;
    if (!$cfg[podsiteid]) 
      $pods_site_ID = $cfg_center[podsiteid]; 
    else
		  $pods_site_ID=$cfg[podsiteid];
		$center_ID=$cfg_center['center_cid'];
		$mall_ID=$cid;
		$user_ID=$sess['mid'];
		if(!$pods_site_ID || !$center_ID || !$mall_ID || !$user_ID)
		{
			echo("fail|"._("유효한 세션 정보가 없습니다."));
			exit;
		}

		$product_ID = $_POST['estm_productid'];
		$order_data = $_POST['orderdata'];
		
		$order_title = $_POST['order_title'];
		$order_memo = $_POST['order_memo'];
		
		try{
		  $m_order = new M_estimate_order();
			$temp_order_ID = $m_order->set_insert_temp_client($center_ID, $mall_ID, $pods_site_ID, $user_ID, $product_ID, $order_data,$order_title,$order_memo);//201309메모,타이틀추가
			
			echo "success|".$temp_order_ID;
		}
		catch(Exception $ex){
			echo "fail|".$ex->getMessage();
		}
	}
    
    //파일업로드 path key 얻기 위해 우선 저장
  function get_createkey(){    
    try {
      $micro = explode(" ",microtime());
      $storageKey = substr($micro[1].sprintf("%03d",floor($micro[0]*1000)), -6);
      echo "success|".$storageKey."|".date("Ymd")."-".$storageKey;
    }
    catch(Exception $ex){
      echo "fail|"._("업로드 경로 생성이 실패했습니다.")."(".$ex->getMessage().")";
    }
    
    exit; 
  }
  
  //order_temp의 임시 주문건을 실주문처리(신규 주문, 재주문)
	function orderinsert_from_temp() {
		$storage_code = $_POST['storage_code'];
		$temp_order_ID = $_POST['temp_order_id'];
		if(!$temp_order_ID){
			echo "fail|".($storage_code?_("편집 보관함 정보").":".$storage_code:"")."|"._("임시 주문 번호가 없습니다.");
			exit;
		}
    
    $m_order = new M_estimate_order;
		$order_temp =$m_order->get_temp_info_client($temp_order_ID);
		if(!$order_temp){
			echo "fail|".($storage_code?_("편집 보관함 정보").":".$storage_code:"")."|"._("임시 주문 정보가 없습니다.");
			exit;
		}
		else $m_order->set_temp_used_client($temp_order_ID); //사용 여부 플래그 갱신

		$params = array(
				'order_ID' => '',
				'order_type' => $_POST['order_type'],
				'proc_type' => $_POST['proc_type'],
				'storage_code' => $storage_code,
				'status' =>'0000',
				'product_ID' => $order_temp['product_ID'],
				'order_data' => $order_temp['order_data'],
				'order_title' => $order_temp['order_title'],
				'order_memo' => $order_temp['order_memo']
				);		
		orderinsert_proc($params);
	}
  
  
  
  /* 저장 처리*/
  function orderinsert_proc($params)
  {
    global $cid, $cfg, $cfg_center, $sess;
		try {      
      if (!$cfg[podsiteid]) 
        $params['pods_site_ID'] = $cfg_center[podsiteid]; 
      else
		    $params['pods_site_ID']=$cfg[podsiteid];      			
			
			$params['center_ID'] = $cfg_center['center_cid'];
			$params['mall_ID'] = $cid;
			$params['user_ID'] = $sess['mid'];
			$params['user_name'] = $sess['name'];
			if(!$params['pods_site_ID'] || !$params['center_ID'] || !$params['mall_ID'])
				throw new Exception(_("유효한 세션 정보가 없습니다."));
			
			//스토리지 존재 체크
			if($params['storage_code'] && $params['order_type']=='EDITOR'){
				$existStorage = checkPodsStorage($params);
				if(!$existStorage) throw new Exception(_("편집 보관함이 존재하지 않습니다."));
			}

			//상품 정보에서 제작사,명칭 파라미터 얻기
      $m_product = new m_estimate_product();
			$product = $m_product->get_info($params['product_ID']);
			if(!$product)throw new Exception('product info none.');
			$params['maker_code'] = $product["exm_maker_code"];
			$params['product_name'] = $product["product_name"];
			
      $params['order_data'] = str_replace('\"', '"', $params['order_data']);      
      //echo $params['order_data'];
      //exit;
			$xml = new SimpleXMLElement($params['order_data']);
			
			$params['cost_price']= getPriceNodeValue($xml,"t_ct_p");//$this-> input-> post('cost_price');
			$params['supply_price']= getPriceNodeValue($xml,"t_sp_p");//$this-> input-> post('supply_price');
			$params['mtr_price'] = getPriceNodeValue($xml,"t_sl_p");//$this-> input-> post('mtr_price');
			
			//20130904 부가세포함 최종 가격에 반올림/버림/단위 적용
			
			$params['order_price']= getTotalPriceNTax($params['mtr_price'],$product["price_calcu_type"],$product["price_calcu_type_num"],$product["price_calcu_vat_flag"]);//$this-> input-> post('order_price');
			$params['count_per_page'] = getCntPerPg($xml); 
			$params['summary']=getOptionSummary($xml);
			
      $m_order = new M_estimate_order();	
			//주문 번호가 있는 경우 수정
			if($params['order_ID'])
			{
				//원본의 user info 비교
				$org_orderinfo = $m_order->get_info_skip_regist($params['order_ID']);
				if(!$org_orderinfo)throw new Exception('original info none.');
				if($params['center_ID']!=$org_orderinfo['center_ID'] || $params['mall_ID']!=$org_orderinfo['mall_ID'] || $params['user_ID']!=$org_orderinfo['user_ID'])
					throw new Exception('invalid user info.');
				
				//수정
				$db_rtn = $m_order->set_update_client($params['order_ID'], $params['center_ID'], $params['mall_ID'], $params['pods_site_ID'], $params['user_ID'], $params['status'], $params['product_ID'], $params['order_data']
						, $params['mtr_price'], $params['order_price'],$params['cost_price'],$params['supply_price']
						,$params['storage_code'],$params['order_type'],$params['count_per_page']
						,$params['order_title'],$params['order_memo']);
				
				if($db_rtn<1) throw new Exception(_('DB 처리 실패'));
			}
			else {
				$params['order_ID'] = $m_order->set_insert_client($params['center_ID'], $params['mall_ID'], $params['pods_site_ID'], $params['user_ID'], $params['status'], $params['product_ID'], $params['order_data']
						, $params['mtr_price'], $params['order_price'],$params['cost_price'],$params['supply_price']
						,$params['storage_code'],$params['order_type'],$params['count_per_page']
						,$params['order_title'],$params['order_memo']);
				
				if(!$params['order_ID']) throw new Exception(_('DB 등록 실패'));
			}
	
			//장바구니로 주문 정보 전달
			$rtnBasket = sendToBasket($params);
			if(!$rtnBasket) throw new Exception(_("장바구니에 정보 전송을 실패했습니다."));
			
			echo "success|".($params['storage_code']?_("편집 보관함 정보").":".$params['storage_code']:"").($params['order_ID']?" "._("주문번호").":".$params['order_ID']:"")."|".$params['order_ID'];
		}
		catch(Exception $ex){
			echo "fail|".($params['storage_code']?_("편집 보관함 정보").":".$params['storage_code']:"").($params['order_ID']?" "._("주문번호").":".$params['order_ID']:"")."|"._("주문처리가 실패했습니다.")."(".$ex->getMessage().")";
		}
	
	}
  
  
  
  //스토리지 존재 여부 체크
  function checkPodsStorage($params){
    global $est_config;	  
		$storageid = $params['storage_code'];				
		$pods_info_all = $est_config["PODS"];
		
		//ver 1.0
		if(strlen($storageid)<20){
			$pods_info = $pods_info_all["1"];
			$response = sendQueryData($pods_info["host"].$pods_info["existfile_url"].'?storageid='.$storageid);
			
			if($response=='ok') 
			 return true;
			else 
			 return false;
		}
		//ver2.0
		else
		{
			$pods_info = $pods_info_all["2"];
			$response = sendQueryData($pods_info["host"].$pods_info["existfile_url"].'?storageid='.$storageid);

			if($response=='ok') 
			 return true;
			else 
			 return false;
		}
		
		return true;
	}
  
  
  function getPriceNodeValue($xml,$xpath){
		$p='';
		$node=$xml->xpath($xpath);
		if($xpath){
			$p=$node[0];
		}
		return $p;
	}
  
  
  /*
	 * 1장의 출력종이에 최적으로 나열될 수 있는 가로,세로 각각에 대한 수량
	 * 예)가로100,세로110인 경우 100*110
	 */
  function getCntPerPg($xml){
		$cntPerPg='';
		$ds=$xml->xpath("p/k/ds");
		//wh_wcnt="5" wh_hcnt="9"
		if($ds){
			$cntPerPg=$ds[0]["wh_wcnt"]."*".$ds[0]["wh_hcnt"];
		}
		return $cntPerPg;
	}
  
  
  
  /*
	 * 요약정보 리턴
	 * 예) 규격:90x50,코팅:기본|라미네이팅,수량:100|1,...
	 */
	function getOptionSummary($xml){
    global $est_config;
		try{
			//$xml = new SimpleXMLElement($xmlstr);
			$panels=$xml->xpath('p');

			$KIND_NAMES = $est_config["KIND_NAMES"];
			$desc=array();
			$str_desc='';
			$ordercnt=0;
			
			foreach($panels as $panel){
				if($panel['key'] == 'default'){
					$desc[strval($panel['key'])]='';
					$node=$panel->xpath("k/oc");
					if($node) $ordercnt+=intval($node[0]["cnt"]);
				}
				elseif($panel['key'] != 'effect'){//커스텀옵션그룹 패널 내용 추가
					$desc[strval($panel['key'])]='';
					$node=$panel->xpath("k/pg");//페이지수 누적
					if($node) $ordercnt+=intval($node[0]["cnt"]);
				}
			}

			//패널별 요약정보 구성
			foreach($panels as $panel){
				if($panel['key'] != 'effect'){//패널의 옵션 추가
					$pkey = (string)$panel['key'];
					$kinds = $panel->xpath('k');
					foreach($kinds as $kind){
						$desc[$pkey] .= $KIND_NAMES[strtoupper((string)$kind['type'])].':';
						switch($kind["type"]){
							case "DS" :
								$node = $kind->xpath("ds");
								if($node) $desc[$pkey].=$node[0]['width']."*".$node[0]['height'];
								break;
							case "QT" :
								$node = $kind->xpath("oc");
								if($node) $desc[$pkey].=$ordercnt."|".$node[0]['gcnt'];
								break;
							default:
								$node = $kind->xpath("o");
								foreach ($node as $root_opt){
									$desc[$pkey].=getOptionDesc($root_opt);
								}
								break;
						}
						$desc[$pkey].=",";
					}
				}
				elseif($panel['key'] == 'effect'){//후가공 패널 내용 추가
					$kinds = $panel->xpath('k');
					foreach($kinds as $kind){
						$node = $kind->xpath("o");
						$sub_desc=array();
						//대상패널별 스크립트 구성
						foreach ($node as $root_opt){
							$pkey = (string)$root_opt['tgid'];
							if(!$pkey)$pkey='default';
							if(!array_key_exists($pkey, $sub_desc)) $sub_desc[$pkey]='';
							$sub_desc[$pkey].=getOptionDesc($root_opt);
						}
						//대상 패널에 추가
						foreach($sub_desc as $key => $value){
							$desc[$key].= $KIND_NAMES[strtoupper((string)$kind['type'])].':';
							$desc[$key].= $value;
							$desc[$key].=",";
						}
					}
				}
			}
			//전체 요약정보 정리
			$PANEL_NAMES = $est_config["PANEL_NAMES"];
			foreach($desc as $key => $value){
				if(endsWith($value,',')) $value = substr($value,0,strlen($value)-1);
				if($key=='default'){
					$str_desc.= $value;
					$str_desc.=",";
				}
				else{
					$str_desc.= $PANEL_NAMES[$key].':[';
					$str_desc.= $value;
					$str_desc.="],";
				}
			}
			if(endsWith($str_desc,',')) $str_desc = substr($str_desc,0,strlen($str_desc)-1);
			return $str_desc;
		}
		catch(Exception $ex){
			echo $ex->getMessage();
			return "";
		}
	}
  
  
  function getOptionDesc($root_opt){
		$desc ='';
		//규격 아이템 처리
		$node = $root_opt->xpath("i[@cmd='width']");
		if($node){
			$node_p = $root_opt->xpath("i[@cmd='height']");
			if($node_p) $desc .=$node[0]["value"]."*".$node_p[0]["value"]."|";
		}
		else{
			//제본위치 아이템 처리
			$node = $root_opt->xpath("i[@cmd='start_pos']");
			if($node){
				$node_p=$root_opt->xpath("i[@cmd='end_pos']");
				if($node_p)$desc .=$node[0]["value"]."~".$node_p[0]["value"]."|";
			}
		}
		//기본 아이템 처리
		appendChildItemValue($root_opt,$desc);
		
		if(endsWith($desc,'|')) $desc = substr($desc,0,strlen($desc)-1);
		
		return $desc;
	}
  
	function appendChildItemValue($xml,&$appendto)
	{
		foreach($xml->xpath('i') as $item)
		{
			if(!$item["cmd"])$appendto.=$item['value'].'|';
			foreach ($item->xpath('o') as $child)
				appendChildItemValue($child,$appendto);
		}
	}
  
  
  //장바구니로 주문정보 전송
	function sendToBasket($params)
	{		
		//======== 지정된 url로 post 전송
		$postargs = array();
		$postargs['st_code']=$params['order_ID'];//주문번호를 보관함코드라는 개념으로 사용 http://192.168.1.199:8088/redmine/issues/1147#note-9;
		$postargs['center_ID']=$params['center_ID'];
		$postargs['mall_ID']=$params['mall_ID'];
		$postargs['user_ID']=$params['user_ID'];
		$postargs['order_data']=$params['order_data'];
		$postargs['order_option_desc']=$params['summary'];
		$postargs['product_code']=$params['product_ID'];
		$postargs['file_down_full_path']="http://".$_SERVER["HTTP_HOST"]."/goods/estimate_c_service.php?mode=getFileList&center_id=".$params['center_ID']."&mall_id=".$params['mall_ID']."&order_id=".$params['order_ID'];
		$postargs['order_type']=$params['order_type'];//편집기 사용주문 : EDITOR , 일반 파일 업로드 : UPLOAD
		$postargs['maker_code']=$params['maker_code'];
		$postargs['product_name']=$params['product_name'];
		$postargs['cost_price']=$params['cost_price'];
		$postargs['supply_price']=$params['supply_price'];
		$postargs['order_price']=$params['order_price'];
		$postargs['order_title']=$params['order_title'];
		$postargs['order_memo']=$params['order_memo'];
		// 			print_r($postargs);
		// 			exit;

		//$rst_send = $this->c_util->sendPostData('http://bpm.bluepod.kr/goods/estimate.cart.php',$postargs);ss_basket_url
		//$rst_send = $this->c_util->sendPostData("http://podstation.ilark.co.kr/testsuccess.asp",$postargs);
		$rst_send = sendPostData('http://'.$_SERVER["HTTP_HOST"].'/goods/estimate.cart.php',$postargs);
		//exit;
		//======== 성공의 경우 장바구니로 이동, 실패시 상품페이지로 이동
		if($rst_send=='success'||$rst_send=='SUCCESS') return true;
		else return false;
	}
  
  
  
  

	
	//수동 파일 업로드 주문의 파일 저장 처리
	function file_upload(){
    global $cid, $cfg, $cfg_center, $est_config;
    
		$resultMsg = "";
		try{		  
			$params['center_ID'] = $cfg_center['center_cid'];
			$params['mall_ID'] = $cid;							
			
			if(!$params['center_ID'] || !$params['mall_ID']) throw new Exception(_("유효한 세션 정보가 없습니다."));
			
			$params['storage_code'] = $_POST['storage_code'];
			if(!$params['storage_code'])throw new Exception(_("업로드 경로 정보가 없습니다."));
			
			$upinfo = saveUploadFile($params['center_ID'],$params['mall_ID'],$params['storage_code']);
			if($upinfo!=null && !empty($upinfo))
			{
				if(!$upinfo["result"]) throw new Exception(_("일부 파일을 저장 실패하였습니다.")."(".$upinfo["msg"].")");
				
				$resultMsg= "success|".$params['storage_code'];
			}
			else 
				$resultMsg= "fail|"._("업로드를 실패했습니다.");
		}
		catch(Exception $ex){
			$resultMsg= "fail|".$ex->getMessage();
		}
		
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=\"". $est_config['charset']."\">";
		echo "<script type='text/javascript'> location.href='/goods/estimate_c_service.php?mode=tossUploadFrame&result=".$resultMsg."';  </script>";
    //echo "<script type='text/javascript'> parent.continueFileOrder('".$resultMsg."'); </script>";
    
    
		exit;
	}

?>	

