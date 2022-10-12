<?
include "../library.php";
include "../lib_const.php";
//chkMember();
include "./extra_option_proc.php";
$db->query("set names utf8");
  
$return_data = "";

$preset = $_GET[preset];
$goods_kind = $_GET[goods_kind];
$option_kind_index = $_GET[option_kind_index];
$option_kind_code = $_GET[option_kind_code];
$option_group_type = $_GET[option_group_type];
$goodsno = $_GET[goodsno];

/*
debug($cfg);
debug($cfg_center);
debug($sess_admin);

debug($cid);
debug($preset);
debug($goods_kind);
debug($option_kind_index);
debug($option_kind_code);
debug($option_group_type);
debug($goodsno);

echo('$cid='.$cid."<br/>");
echo('$preset='.$preset."<br/>");
echo('$goods_kind='.$goods_kind."<br/>");
echo('$option_kind_index='.$option_kind_index."<br/>");
echo('$option_kind_code='.$option_kind_code."<br/>");
echo('$option_group_type='.$option_group_type."<br/>");
echo('$goodsno='.$goodsno."<br/>");
*/

//tb_extra_option 등록
function set_tb_extra_option(
	$cid, $center_id, $goodsno, $display_name, $item_name, 
	$extra_data1, $extra_data2, $order_index, $option_kind_code, 
	$option_kind_index, $goods_kind, $option_group_type, 
	$option_item_ID, $parent_option_item_ID, $item_price, $item_price_type, $same_price_option_item_ID) {
	
	$regist_flag = "Y";
	$display_flag = "Y";
	$necessary_flag = ($option_group_type == "AFTEROPTION") ? "N" : "Y";

	
	$ret = setOption($cid, $center_id, $goodsno, $display_name, $item_name, 
	$regist_flag, $extra_data1, $extra_data2, $order_index, $option_kind_code, 
	$option_kind_index, $display_flag, $goods_kind, $necessary_flag, $option_group_type, 
	$option_item_ID, $parent_option_item_ID, $item_price, $item_price_type, $same_price_option_item_ID);
	
	//debug($ret);
	
	if($ret < 0) {
		msg(_("오류입니다.")."[$ret]");
		echo "FAIL";
		exit;
	}
}

//같은 가격 항목 설정인 경우 사용.
function set_option_data($option_item_id, $parent_option_item_ID, $data, $same_price_option_item_ID='', $item_name='', $order_index='')
{
	if($option_item_id) {		
		if(!$parent_option_item_ID) $parent_option_item_ID = $data[parent_option_item_ID];
		if(!$same_price_option_item_ID) $same_price_option_item_ID = $data[same_price_option_item_ID];
		if(!$item_name) $item_name = $data[item_name];
		if(!$order_index) $order_index = $data[order_index];
		
		$ret = setOption($data[cid], $data[bid], $data[goodsno], $data[display_name], $item_name, 
		$data[regist_flag], $data[extra_data1], $data[extra_data2], $order_index, $data[option_kind_code], 
		$data[option_kind_index], $data[display_flag], $data[goods_kind], $data[necessary_flag], $data[option_group_type], 
		$option_item_id, $parent_option_item_ID, $data[item_price], $data[item_price_type], $same_price_option_item_ID);
		
		//debug($ret);
		
		if($ret < 0) {
			msg(_("오류입니다.")."[$ret]");
			echo "FAIL";
			exit;
		}		
	}
}

switch ($preset) {
	case '100101':		
		
		$base_option_id = $_GET[base_option_id]; //기준이 되는 option_id 하위 데이타를 조회시 사용한다.
				
		if($option_group_type == "DOCUMENT") { //option_group_type 이 DOCUMENT 이면
			
			$f_document_item_name = $_GET[f_document_item_name]; //표시항목
			$f_document_x = $_GET[f_document_x]; //실제 항목
			$f_document_y = $_GET[f_document_y]; //실제 항목
			$f_same_item_name = $_GET[f_same_item_name]; //같은 가격 항목 설정
			
			//debug($base_option_id);
			//debug($f_document_item_name);
			//debug($f_document_x);
			//debug($f_document_y);
			//debug($f_same_option_document_id);

			//같은 가격 항목 설정 가져오기	getSameOptionItemList
			if($f_same_item_name) {
				$ret = getSameOptionItemList('', $cid, $goodsno, '1', $option_group_type, $option_kind_code, $f_same_item_name);
				//debug($ret);
				$same_price_option_item_ID = $ret[option_item_ID];
			}

			$new_option_item_id = getMaxItemID('', $cid, $goodsno);
			
			if($new_option_item_id) {
				$new_order_index = getMaxOrderIndex('', $cid, $goodsno, '1', $option_group_type, $option_kind_code);
				
				//$res = setOption("", $cid, $goodsno, "규격", $f_document_item_name, 
				//"Y", $f_document_x, $f_document_y, $new_order_index, $option_kind_code, 
				//"1", "Y", $goods_kind, "Y", $option_group_type, 
				//$new_option_item_id, "0", "", "", $same_price_option_item_ID);
				
				set_tb_extra_option("", $cid, $goodsno, _("규격"), $f_document_item_name, 
					$f_document_x, $f_document_y, $new_order_index, $option_kind_code, 
					"1", $goods_kind, $option_group_type, 
					$new_option_item_id, "0", "", "", $same_price_option_item_ID);
			}

			if(!$f_same_item_name) { //같은 가격 항목 설정이 없으면 base_option_id 를 기준으로 하위 (parent_option_item_id)option_kind_index 데이타를 조회하여 복사한다.
				
				$ret2 = getParentOptionItemList('', $cid, $goodsno, "2", $base_option_id); //기준 option_item_id로 option_kind_index=2 항목 조회
				if($ret2) {
					//debug($ret2);
					foreach ($ret2 as $k => $v) {
						$new_item_id2 = getMaxItemID('', $cid, $goodsno);
						set_option_data($new_item_id2, $new_option_item_id, $v);

						$ret3 = getParentOptionItemList('', $cid, $goodsno, "3", $v[option_item_ID]);
						if($ret3) {							
							//debug($ret3);
							foreach ($ret3 as $k => $v) {
								$new_item_id3 = getMaxItemID('', $cid, $goodsno);
								set_option_data($new_item_id3, $new_item_id2, $v);

								$ret4 = getParentOptionItemList('', $cid, $goodsno, "4", $v[option_item_ID]);

								if($ret4) {									
									//debug($ret4);
									foreach ($ret4 as $k => $v) {
										$new_item_id4 = getMaxItemID('', $cid, $goodsno);
										set_option_data($new_item_id4, $new_item_id3, $v);
									}
								}
							}
						}
					}
				}
				
			}
			
		}
		else if($option_group_type == "FIXOPTION") { //option_group_type 이 FIXOPTION 이면
			
			if($option_kind_index == "2") { //용지

				$f_item_name = $_GET[f_item_name];
				$f_item_name2 = $_GET[f_item_name2];
				$f_same_item_name = $_GET[f_same_item_name];
				
				$extra_data1 = $_GET[extra_data1];
				$extra_data2 = $_GET[extra_data2];
				
				//debug($base_option_id);
				//debug($f_item_name);
				//debug($f_item_name2);
				//debug($f_same_item_name);
	
				$ret1 = getKindIndexOptionItemList('', $cid, $goodsno, '1', 'DOCUMENT', $option_kind_code);// 규격 항목 option_kind_index = 1
				
				if($ret1) {
					//debug($ret1);				
					//1차 용지 입력은 규격 항목 option_kind_index = 1 밑에 모두 넣는다.
					foreach ($ret1 as $k => $v) {
						//debug($v[display_name]."->".$v[item_name]."->".$v[option_item_ID]);
						
						if($f_same_item_name) { //같은 가격 항목 설정이 있다면 (규격 항목 option_kind_index = 1) f_same_item_name 으로 용지를 조회하여 같은 가격 항목으로 설정한다.
							
							$ret_same = getSameOptionItemList('', $cid, $goodsno, '2', $option_group_type, $option_kind_code, $f_same_item_name, $v[option_item_ID]);
							//debug($ret_same);
							$same_price_option_item_ID = $ret_same[option_item_ID];
	
							$new_option_item_id = getMaxItemID('', $cid, $goodsno);						
							if($new_option_item_id) {
								$new_order_index = getMaxOrderIndex('', $cid, $goodsno, '2', $option_group_type, $option_kind_code);
								$item_name = explode(',',$f_item_name);

								foreach($item_name as $key) {
									//setOption('', $cid, $goodsno, '용지', $key, 
									//'Y', '', '', $new_order_index, $option_kind_code, 
									//'2', 'Y', $goods_kind, 'Y', $option_group_type, 
									//$new_option_item_id, $v[option_item_ID], '', '', $same_price_option_item_ID);
									
									set_tb_extra_option("", $cid, $goodsno, _("용지"), $key, 
										$extra_data1, $extra_data2, $new_order_index, $option_kind_code, 
										$option_kind_index, $goods_kind, $option_group_type, 
										$new_option_item_id, $v[option_item_ID], "", "", $same_price_option_item_ID);									
								}
							}
													
						}
						else {
																
							//2차 항목(입력값 1차 항목)												
							if($f_item_name) {
								$item_name = explode(',',$f_item_name); //2차 항목 분리(입력값 1차 항목)
								$item_name2 = explode(',',$f_item_name2); //3차 항목 분리(입력값 3차 항목)
								
								//4차 항목 조회
								$ret_same_arr = getDistinctSameOptionItemList('', $cid, $goodsno, '4', $option_group_type, $option_kind_code);							
								foreach($item_name as $key) {
									$new_option_item_id = getMaxItemID('', $cid, $goodsno);
	
									if($new_option_item_id) {
										$new_order_index = getMaxOrderIndex('', $cid, $goodsno, '2', $option_group_type, $option_kind_code, $v[option_item_ID]);
										//setOption('', $cid, $goodsno, '용지', $key, 
										//'Y', '', '', $new_order_index, $option_kind_code, 
										//'2', 'Y', $goods_kind, 'Y', $option_group_type, 
										//$new_option_item_id, $v[option_item_ID], '', '', '');

										set_tb_extra_option("", $cid, $goodsno, _("용지"), $key, 
											"", "", $new_order_index, $option_kind_code, 
											$option_kind_index, $goods_kind, $option_group_type, 
											$new_option_item_id, $v[option_item_ID], "", "", "");
										
										//3차 항목(입력값 2차 항목)
										if($f_item_name2) {
											$i = 1;
											foreach($item_name2 as $k) {	
												$new_option_item_id3 = getMaxItemID('', $cid, $goodsno);
	
												if($new_option_item_id3) {
													//setOption('', $cid, $goodsno, '', $k, 
													//'Y', '', '', $i, $option_kind_code, 
													//'3', 'Y', $goods_kind, 'Y', $option_group_type, 
													//$new_option_item_id3, $new_option_item_id, '', '', '');

													set_tb_extra_option("", $cid, $goodsno, "", $k, 
														"", "", $i, $option_kind_code, 
														"3", $goods_kind, $option_group_type, 
														$new_option_item_id3, $new_option_item_id, "", "", "");
													
													//하위 옵션 찾아서 넣어라.(인쇄도수 항목 option_kind_index = 4)
													foreach ($ret_same_arr as $k => $j) {													
														$ret_same = getSameOptionItemList('', $cid, $goodsno, '4', $option_group_type, $option_kind_code, $j[item_name], '');
														//debug($ret_same[0]);
														$new_option_item_id4 = getMaxItemID('', $cid, $goodsno);
														set_option_data($new_option_item_id4, $new_option_item_id3, $ret_same[0]);
													}
												}
												$i++;
											}
										}
									}
								}
							}
							
						}					
					}
				}
							
			}
			else if($option_kind_index == "4"){ //인쇄도수
			
				$f_item_name = $_GET[f_item_name];
				$f_same_item_name = $_GET[f_same_item_name];
				
				$extra_data1 = $_GET[extra_data1];
				$extra_data2 = $_GET[extra_data2];
				
				//debug($base_option_id);
				//debug($f_item_name);
				//debug($f_same_item_name);
				
				$item_name = explode(',',$f_item_name); //항목 분리
				
				if($f_same_item_name) { //같은 가격 항목 설정이 있다면 (인쇄도수 항목 option_kind_index = 4) f_same_item_name 으로 용지를 조회하여 개수만큼 (option_item_ID=>same_price_option_item_ID)넣는다.
							
					$ret_same = getSameOptionItemList('', $cid, $goodsno, $option_kind_index, $option_group_type, $option_kind_code, $f_same_item_name, $v[option_item_ID]);
					//debug($ret_same);
					$new_order_index = getMaxOrderIndex('', $cid, $goodsno, '4', $option_group_type, $option_kind_code);
					
					foreach($item_name as $key) {
						foreach ($ret_same as $k => $v) {
							$new_item_id = getMaxItemID('', $cid, $goodsno);
							//debug($new_order_index);
							set_option_data($new_item_id, '', $v, $v[option_item_ID], $key, $new_order_index);
						}
						$new_order_index++;
					}
										
				}
				else {

					$ret1 = getKindIndexOptionItemList('', $cid, $goodsno, $option_kind_index, $option_group_type, $option_kind_code, '1');// 인쇄도수 항목 option_kind_index = 4

					if($ret1) {
						foreach($item_name as $key) {
							$new_order_index = getMaxOrderIndex('', $cid, $goodsno, '4', $option_group_type, $option_kind_code);									
							foreach ($ret1 as $k => $v) {	
								$new_item_id = getMaxItemID('', $cid, $goodsno);
								
								//setOption('', $cid, $goodsno, '인쇄도수', $key, 
								//'Y', '', '', $new_order_index, $option_kind_code, 
								//'4', 'Y', $goods_kind, 'Y', $option_group_type, 
								//$new_item_id, $v[parent_option_item_ID], '', '', '');
								
								set_tb_extra_option("", $cid, $goodsno, _("인쇄도수"), $key, 
									$extra_data1, $extra_data2, $new_order_index, $option_kind_code, 
									"4", $goods_kind, $option_group_type, 
									$new_item_id, $v[parent_option_item_ID], "", "", "");
							}
								
							$new_order_index++;
						}
					}
										
				}
			
			}

		}
		else if($option_group_type == "AFTEROPTION") { //option_group_type 이 NOROPTION 이면
			
			if($option_kind_code == "ETC") { //기타 후가공 옵션이면...

				$addopt_group_view = $_GET[addopt_group_view];
				$addopt_group_name = $_GET[addopt_group_name];
				$addopt = $_GET[addopt];
				
				//debug($base_option_id);
				//debug($option_kind_index);
				//debug($addopt_group_view);
				//debug($addopt_group_name);
				//debug($addopt);
				
				$view = ($addopt_group_view) ? "Y" : "N";
				//debug($view);				
				
				$item = explode(',',$addopt); //항목 분리
				
				$new_option_kind_index = getMaxOptionKindIndex("", $cid, $goodsno, $option_group_type);
				//debug($new_option_kind_index);

				$new_order_index = "1";

				foreach($item as $key) {
					if($key) {
						$new_item_id = getMaxItemID('', $cid, $goodsno);

						set_tb_extra_option("", $cid, $goodsno, $addopt_group_name, $key, 
							"", "", $new_order_index, $option_kind_code.$new_option_kind_index, 
							$new_option_kind_index, $goods_kind, $option_group_type, 
							$new_item_id, "0", "", "", "");

						$new_order_index++;
					}
				}

			}
			else {

				$f_item_name = $_GET[f_item_name];
				$extra_data1 = $_GET[extra_data1];
				$extra_data2 = $_GET[extra_data2];
				
				//debug($base_option_id);
				//debug($f_item_name);
				//debug($f_nor_extra1);
				//debug($f_nor_extra2);
				
				$item_name = explode(',',$f_item_name); //항목 분리
				
				$new_order_index = getMaxOrderIndex('', $cid, $goodsno, $option_kind_index, $option_group_type, $option_kind_code);
				//debug($new_order_index);
	
				foreach($item_name as $key) {
					if($key) {			
						$new_item_id = getMaxItemID('', $cid, $goodsno);
						
						//setOption('', $cid, $goodsno, $r_est_option[$option_kind_code], $key, 
						//'Y', $f_nor_extra1, $f_nor_extra2, $new_order_index, $option_kind_code, 
						//$option_kind_index, 'Y', $goods_kind, 'N', $option_group_type, 
						//$new_item_id, '', '', '', '');
						
						set_tb_extra_option("", $cid, $goodsno, $r_est_option[$option_kind_code], $key, 
							$extra_data1, $extra_data2, $new_order_index, $option_kind_code, 
							$option_kind_index, $goods_kind, $option_group_type, 
							$new_item_id, "0", "", "", "");
						$new_order_index++;
					}
				}
				
			}
			
		}

		echo("OK");
		exit;
		break;
	
	default:
		
		break;
}	

echo("FAIL");
  
?>