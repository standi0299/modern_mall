<?
/*	 190626		jtkim		배송비관련 함수 추가
 *
 * 	배송비 정책 관련 변수
 * 
 * 	추가 배송 정책 (S)
 * 
 * 	$ship_cfg_*							:			배송 순서 및 형태 설정
 * 	$ship_cfg_type						:			배송 정책 타입 (D:기존 배송정책 , S:선택형 배송 정책)
 *	$ship_cfg_list						:			배송 순서 저장 (G:상품,M:몰,R:제작사) 
 * 	$ship_cfg_next						:			무료배송 설정 (U:상위순위정책 사용 -> 무료배송 ,D:하위순위정책 사용)
 * 	$ship_cfg_dc						:			할인시 적용 여부 (1:주문기준 -> 할인적용금액 ,0:상품기준 -> 할인미적용금액)
 * 
 * 	기존 배송 정책 (D)
 * 
 * 	$data(exm_goods,exm_goods_cid)		: 			상품 정책
 * 
 *  shiptype							:			배송타입(0:일반배송(제작사의정책을따름),1:무료배송,2:개별배송(배송비,배송비원가))
 *  shipprice 							:			배송비
 *  oshipprice							:			배송비원가
 *  rid									:			출고처아이디
 *  self_deliv							:			자체배송타입(1:자체배송)
 * 	self_dtype							:			자체배송형식(0:일반,1무료,2개별),$POST_[self_dtype] 없으면 0, 있으면 2 (현재 상태값 전부 0으로 확인됨)
 * 	self_dprice							:			자체개별배송비
 * 
 *  $data(exm_release) 					: 			제작사 정책
 * 
 *  shiptype							:			배송타입(0:일반배송,1:무료배송,3조건부배송)
 *  shipprice							:			배송비
 * 	oshipprice							:			배송비원가
 *  shipconditional						:			조건부 금액
 *  nicknm								:			출고처 명
 * 
 *  $cfg(exm_config)					:			몰 정책
 * 
 * 	self_deliv							:			자체배송비 사용여부
 * 	self_shiptype						:			배송타입(0:일반배송,1무료배송,3조건부배송)
 * 	self_shipconditional				:			조건부 금액
 * 	self_shipprice						:			배송비
 * 
 */
function f_get_delivery($goodsno){
	global $db,$cid,$cfg;

	$sql = "select * from exm_config where cid = '$cid' and config_group = 'ship_cfg'";
	$sql_arr = $db->listArray($sql);
	foreach($sql_arr as $k => $v){
		switch($v[config]){
			// 할인 시 배송 설정
			case "ship_cfg_dc" : $ship_cfg_dc = $v[value];
			break;
			// 배송 정책 우선순위
			case "ship_cfg_list" : $ship_cfg_list = $v[value];
			break;
			// 무료 배송 설정
			case "ship_cfg_next" : $ship_cfg_next = $v[value];
			break;
			// 배송 설정 타입
			case "ship_cfg_type" : $ship_cfg_type = $v[value];
			break;
			default : break;
		}
	}
	
	$data = $db->fetch("select shiptype,shipprice,rid,self_deliv,self_dtype,self_dprice from exm_goods a inner join exm_goods_cid b on a.goodsno = b.goodsno where a.goodsno = '$goodsno' and b.cid = '$cid'");

	if($ship_cfg_list)	$ship_cfg_list_arr = explode(",",$ship_cfg_list);

	function shipCfgMall($next,$list){
		global $cfg;
		list($s_data[self_deliv],$s_data[shiptype],$s_data[shipconditional],$s_data[shipprice]) = array($cfg[self_deliv],$cfg[self_shiptype],$cfg[self_shipconditional],$cfg[self_shipprice]);
		$s_data['ship_cfg_name'] = 'mall';
		//다음 순서찾기
		$now_value = array_values($list);
		$next_value = $now_value[array_search("M",$now_value)+1];

		//몰정책 사용
		if($s_data[self_deliv]==1){			
			switch($s_data[shiptype]){
				//일반배송
				case "0"	:
				//조건부배송
				case "3"	:
					return $s_data;
					break;
				//무료배송
				case "1"	:
					//상위정책 (무료배송적용)		
					if($next == "U"){
						$s_data[shipprice]=0;
						return $s_data;
					//하위정책 (상품정책)
					}else if($next == "D" && $next_value == "G"){
						return shipCfgGood($next,$list);
					//하위정책 (제작사정책)
					}else if($next == "D" && $next_value == "R"){
						return shipCfgRelease($next,$list);
					//마지막 순서 (몰정책)
					}else{
						$s_data[shipprice]=0;
						return $s_data;
					}
					break;
			}
		//몰정책 미사용 하위순위 있으면 하위순위로
		}else{
			if($next_value =="G"){
				return shipCfgGood($next,$list);
			}else if($next_value == "R"){
				return shipCfgRelease($next,$list);
			}else{
				return $s_data;
			}
		}


	}
	function shipCfgRelease($next,$list){
		global $data,$db;
		list($s_data[shipconditional],$s_data[shiptype],$s_data[release],$s_data[shipprice],$s_data[oshipprice]) = $db->fetch("select shipconditional,shiptype,nicknm,shipprice,oshipprice from exm_release where rid = '$data[rid]'",1);
		$s_data['ship_cfg_name'] = 'release';
		//다음 순서찾기
		$now_value = array_values($list);
		$next_value = $now_value[array_search("R",$now_value)+1];

		switch($s_data[shiptype]){
			//일반배송
			case "0"	:
			//조건부배송
			case "3"	:
				return $s_data;
			//무료배송
			case "1"	:
				//상위정책 (무료배송적용)		
				if($next == "U"){
					$s_data[shipprice]=0;
					return $s_data;
				//하위정책 (몰정책)
				}else if($next == "D" && $next_value == "M"){
					return shipCfgMall($next,$list);
				//하위정책 (제작사정책)
				}else if($next == "D" && $next_value == "G"){
					return shipCfgGood($next,$list);
				//마지막 순서 (상품정책)
				}else{
					$s_data[shipprice]=0;
					return $s_data;
				}
				break;

		}


	}

	function shipCfgGood($next,$list){
		global $data;
		
		list($s_data[shiptype],$s_data[shipprice],$s_data[rid],$s_data[self_deliv],$s_data[self_dtype],$s_data[self_dprice]) = array($data[shiptype],$data[shipprice],$data[rid],$data[self_deliv],$data[self_dtype],$data[self_dprice]);
		$s_data['ship_cfg_name'] = 'good';
		//다음 순서찾기
		$now_value = array_values($list);
		$next_value = $now_value[array_search("G",$now_value)+1];
		switch($s_data[shiptype]){
			// 배송타입(제작사 정책)
			case "0"	:
				return shipCfgRelease($next,$list);
				break;
						
			//배송타입(무료배송)
			case "1"	:		
				//상위정책 (무료배송적용)		
				if($next == "U"){
					$s_data[shipprice]=0;
					return $s_data;
				//하위정책 (몰정책)
				}else if($next == "D" && $next_value == "M"){
					return shipCfgMall($next,$list);
				//하위정책 (제작사정책)
				}else if($next == "D" && $next_value == "R"){
					return shipCfgRelease($next,$list);
				//마지막 순서 (상품정책)
				}else{
					$s_data[shipprice]=0;
					return $s_data;
				}
				break;

			// 배송타입(개별배송)
			case "2"	:
				return $s_data;
				break;
		}
	}

	//선택형 배송정책 사용
	if( $ship_cfg_type == "S" && (count($ship_cfg_list_arr)==3) ){
		if($ship_cfg_list_arr[0] == "G"){
			$res_data = shipCfgGood($ship_cfg_next,$ship_cfg_list_arr);
		}else if($ship_cfg_list_arr[0] == "M"){
			$res_data = shipCfgMall($ship_cfg_next,$ship_cfg_list_arr);
		}else if($ship_cfg_list_arr[0] == "R"){
			$res_data = shipCfgRelease($ship_cfg_next,$ship_cfg_list_arr);
		}
		
		$res_data[ship_cfg_dc] = $ship_cfg_dc;
		$res_data[ship_cfg_type] = $ship_cfg_type;
		$ret[0] = $res_data;

	}else{
		if ($data[shiptype]==0){
			list($data[shipconditional],$data[r_shiptype],$data[release],$data[shipprice],$data[oshipprice]) = $db->fetch("select shipconditional,shiptype,nicknm,shipprice,oshipprice from exm_release where rid = '$data[rid]'",1);
		}

		if ($cfg[self_deliv]==1 && $data[shiptype]!=2){
			$data[shiptype] = $cfg[self_shiptype];
			$data[r_shiptype] = $cfg[self_shiptype];
			$data[shipconditional] = $cfg[self_shipconditional];
			$data[shipprice] = $cfg[self_shipprice];
		}

		$ret[0] = $data;
		
	}
	
	return $ret;

}

?>