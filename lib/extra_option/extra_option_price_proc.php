<?
  
  
  function getOptionPrice($cid, $center_id, $goodsno, $option_item_ID, $order_cnt, $order_cnt_page, $document_x, $document_y, $print_x, $print_y, $addWhere)
  {
    global $db;   
    //$cid = '';
    //항목이 없을경우 가격을 찾아본다.
    //$query = "select a.option_kind_code, b.item_price, b.item_price_type from 
    //  tb_extra_option a, tb_extra_option_price b where a.cid = '$cid' and a.bid = '$center_id' and a.goodsno=$goodsno and a.option_item_ID=$option_item_ID and a.regist_flag = 'Y'
    //  and a.option_item_ID = b.option_item_ID and b.regist_flag='Y'";
      
    //$query = "select * from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_item_ID=$option_item_ID and regist_flag = 'Y'";      
    //echo $query;
    //exit;
    //$res = $db->query($query);
    //$data = $db->fetch($res);
        
    $classExtraOption = new M_extra_option();
    $data = $classExtraOption->getOptionItemInfo($cid, $center_id, $goodsno, $option_item_ID, $addWhere);
    //debug($data);
	
	//goods Preset 조회 2015.02.13 by kdk
	$preset = $classExtraOption->GetGoodsPreset($goodsno);
	//debug($preset);
	
    if ($data)
    {      
      //마지막 문자열에서 ";"을 지워준다.
      //if (substr($data[item_price], -1) == ";")       
      //  $data[item_price] = substr($data[item_price], 0, -1);
      
      //표지부수, 내지 수량이 같이 넣어올경우 (주문수량*부수별 = 총주문수량)
      if (($preset == "100104" || $preset == "100108") && $order_cnt_page) { //($data[option_kind_code] == "P-PP" 사용안함) 2015.02.11 by kdk
          $order_cnt = $order_cnt * (int)$order_cnt_page;
	  }
      //debug($order_cnt);
      
      //면적 계산의 경우 주문수량 정보 대신 면적 (x, y 곱)을 넘긴다.
      if ($data[item_price_type] == "SIZE")
      {
        if (! $document_x) $document_x = 0;
        if (! $document_y) $document_y = 0;
          
        if (! $print_x) $print_x = 0;
        if (! $print_y) $print_y = 0;
                  
        //현수막 천(지류)의 경우 면적 계산은 documnet 크기로 한다.
        if ($data[option_kind_code] == "PP")      
          $target_cnt =  (int)$document_x * (int)$document_y;
   
        //나머지 일반 면적 계산 옵션처리.  
        else 
          $target_cnt =  (int)$print_x * (int)$print_y;
        
      } else 
        $target_cnt = $order_cnt;
      
      $product_price_arr = getPriceSection($data[item_price], $target_cnt);
      $product_supply_price = $product_price_arr[0];   //원가,판매가 형태로 리턴된다.
      $product_price = $product_price_arr[1];   //원가,판매가 형태로 리턴된다.
          
  
      //가격 계산
      if ($product_price != "-1") 
      {
        //echo $product_price . "<BR>";
                  
        if($data[item_price_type] == "CNT")     //단가
        {
          //수식이 있는 경우 수식으로 계산.
          if (strpos($product_price, "[CNT]") !== false)
          {
            $product_price = str_replace("[CNT]", $order_cnt, $product_price);
            //echo $product_price;            
            $return_data = calc_string($product_price);
          } 
          else 
          {
            $return_data =  (int)$product_supply_price * (int)$order_cnt . "|";   //원가|판매가 형태로 넘겨준다   20140328
            $return_data .=  (int)$product_price * (int)$order_cnt;
          }
        }
        else if($data[item_price_type] == "TIME")     //구간 가격 (1회)
          
          //수식이 있는 경우 수식으로 계산.
          if (strpos($product_price, "[CNT]") !== false)
          {
            $product_price = str_replace("[CNT]", $order_cnt, $product_price);                            
            $return_data = calc_string($product_price);                
          } 
          else 
          {            
            $return_data = (int)$product_supply_price ."|";      //원가|판매가 형태로 넘겨준다   20140328
            $return_data .= (int)$product_price;
          }
        else if($data[item_price_type] == "SIZE")     //면적 가격
        {      
          
          //현수막 천(지류)의 경우 면적 계산은 documnet 크기로 한다.
          if ($data[option_kind_code] == "PP")
          {            
            $return_data = (int)$product_supply_price * (int)$document_x * (int)$document_y . "|";   //원가|판매가 형태로 넘겨준다   20140328;  
            $return_data .= (int)$product_price * (int)$document_x * (int)$document_y;
            
          //나머지 일반 면적 계산 옵션처리.  
          } else {          
            //수식이 있는 경우 수식으로 계산. 면적 계산의 경우 print_x, print_y 문자엶로 체크
            if (strpos($product_price, "[PRINT_X]") !== false || strpos($product_price, "[PRINT_Y]") !== false)
            {
              $product_price = str_replace("[CNT]", "", $product_price);      //수량은 제거
              $product_price = str_replace("[PRINT_X]", $print_x, $product_price);                            
              $product_price = str_replace("[PRINT_Y]", $print_y, $product_price);
              $return_data = calc_string($product_price);                
            } else
            {
              $return_data = (int)$product_supply_price * (int)$print_x * (int)$print_y. "|";   //원가|판매가 형태로 넘겨준다   20140328;  
              $return_data .= (int)$product_price * (int)$print_x * (int)$print_y;
            }
          }
          
            
          //$return_data =  $cnt_arr[2] * (int)$_GET[print_x] * (int)$_GET[print_y] * (int)$_GET[order_cnt];
          
        }
      }
    }
    
    if ($return_data)
      return $return_data;
    else 
      return "-1";    
  }

  
  //구간별 원가,판매가 배열을 넘겨준다.
  function getPriceSection($price_rule, $target_cnt)
  {    
    $price_arr = explode(";", $price_rule);      
              
    $product_price = array(-1, -1);
    $minOrderCnt = 0;
    $minOptionProductPrice = array(-1,-1);
    $maxOrderCnt = 0;
    $maxOptionProductPrice = array(-1,-1);
    
    //debug($price_rule);
    foreach ($price_arr as $key => $value) 
    {
      if ($value)
      {
        $cnt_arr = explode("~", $value);      
        //debug($cnt_arr);
        
        //구간별 가격 설정이 되어 있는 경우     ex)1~500~100~110;501~1000~80~90;1001~2000~60~50   //시작,종료,원가,판매가;
        if (sizeof($cnt_arr) > 3) 
        {
          //범위 안에 가격이 있을 경우          
          if ((int)$cnt_arr[0] <= (int)$target_cnt && (int)$cnt_arr[1] >= (int)$target_cnt)
          {
            if($cnt_arr[2] != '')
              $product_price = array($cnt_arr[2], $cnt_arr[3]);
          }
          
          
          //최대 설정가격보다 더 큰 수량이 들어올경우 maxPrice 에 넣어둔다.
          else if ((int)$cnt_arr[1] < (int)$target_cnt)
          {
            if($maxOrderCnt < (int)$cnt_arr[1])
            {              
              $maxOrderCnt = (int)$cnt_arr[1];
            
              if($cnt_arr[2] != '')
                $maxOptionProductPrice =  array($cnt_arr[2], $cnt_arr[3]);
            }
          }
          
          
          //최소 설정가격보다 적은 수량의 주문이 들어올경우 minPrice 에 넣어둔다.
          else if ((int)$cnt_arr[0] >= (int)$target_cnt)
          {
            if($minOrderCnt == 0 || $minOrderCnt >= (int)$cnt_arr[0])
            {              
              $minOrderCnt = (int)$cnt_arr[0];
            
              if($cnt_arr[2] != '')
                $minOptionProductPrice = array($cnt_arr[2], $cnt_arr[3]);
            }
          }     
        } 
        
        //구간별 가격 설정이 없고 수량별 가격설정만 되어 있는 경우     ex)100~110~120;200~100~110;300~90;400~80~90;     //수량,원가,판매가
        else if (sizeof($cnt_arr) > 2)          
        {
          //설정 수량과 일치하는 경우
          if ((int)$cnt_arr[0] == (int)$target_cnt)
          {            
            if($cnt_arr[1] != '')
            {
              $product_price =  array($cnt_arr[1], $cnt_arr[2]);
              break;
            }           
          }
                      
          //설정 수량보다 작을경우           
          else if ((int)$cnt_arr[0] >= (int)$target_cnt)
          {
            if($minOrderCnt == 0 || $minOrderCnt >= (int)$cnt_arr[0])
            {              
              $minOrderCnt = (int)$cnt_arr[0];            
              if($cnt_arr[1] != '')
                $minOptionProductPrice =  array($cnt_arr[1], $cnt_arr[2]);
            }
          }
          
          //최대 설정가격보다 더 큰 수량이 들어올경우 maxPrice 에 넣어둔다.
          else if ((int)$cnt_arr[0] < (int)$target_cnt)
          {
            if($maxOrderCnt < (int)$cnt_arr[0])
            {              
              $maxOrderCnt = (int)$cnt_arr[0];
            
              if($cnt_arr[1] != '')
                $maxOptionProductPrice = array($cnt_arr[1], $cnt_arr[2]);
            }
          }            
                      
        //가격에 범위가 없을 경우 그냥 판매가격으로 계산      //원가,판매가
        } else {
          if($cnt_arr[0] != '') 
            $product_price =  array($cnt_arr[0], $cnt_arr[1]);
        }       
        
      }
    }
       
    //debug($product_price);        
    $return_price = array(-1,-1);
    if ($product_price[0] > -1)
      $return_price = $product_price;
    else if ($maxOptionProductPrice[0] > -1)
      $return_price = $maxOptionProductPrice;
    else if ($minOptionProductPrice[0] > -1)
      $return_price = $minOptionProductPrice;
    
    if ($return_price[1] == '')
      $return_price[1] = '0';
    return $return_price;
  }


  function calc_string( $mathString )
  {
    $cf_DoCalc = create_function("", "return (" . $mathString . ");" );
    return $cf_DoCalc();
  };
  
  
  function getExcelColName($num) {
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return getExcelColName($num2 - 1) . $letter;
    } else {
        return $letter;
    }
  }

  //수량(건수) 가격(할인율) 조회하기
  function getOptionUnitPrice($cid, $center_id, $goodsno, $unit_cnt)
  {
    global $db;

    $classExtraOption = new M_extra_option();
    $data = $classExtraOption->getOptionUnitPrice($cid, $center_id, $goodsno);
    
    //debug($data);
    if ($data)
    {      
      $product_price_arr = getPriceSection($data[unit_cnt_price], $unit_cnt);
      $product_supply_price = $product_price_arr[0];   //원가,판매가 형태로 리턴된다.
      $product_price = $product_price_arr[1];   //원가,판매가 형태로 리턴된다.
            
      //할인율 리턴
      if ($product_price != "-1") 
      {
        //echo $product_price . "<BR>";                  
        $return_data =  $product_supply_price. "|";   //원가|판매가 형태로 넘겨준다   20140328
        $return_data .=  $product_price;
      }
    }

    if ($return_data)
      return $return_data;
    else 
      return "-1";
  }


  function getOptionPriceS2($cid, $center_id, $goodsno, $option_item, $order_cnt, $order_cnt_page, $document_x, $document_y, $print_x, $print_y, $addWhere, $item_price_type, $option_group_type)
  {
    global $db;

    $classExtraOption = new M_extra_option();
    $data = $classExtraOption->getOptionItemInfoS2($cid, $center_id, $goodsno, $option_item, $addWhere);
    //debug($data);
	
	//goods Preset 조회 2015.02.13 by kdk
	$preset = $classExtraOption->GetGoodsPreset($goodsno);
	//debug($preset);
	
    if ($data)
    {
      //#책자프리셋 후가공옵션 계산 방식 수정 2015.06.10 by kdk	      
      //표지부수, 내지 수량이 같이 넣어올경우 (주문수량*부수별 = 총주문수량)
      /*if (($preset == "100104" || $preset == "100108") && $order_cnt_page) { //($data[option_kind_code] == "P-PP" 사용안함) 2015.02.11 by kdk

		  //#책자프리셋 후가공옵션 계산 방식 수정 2015.06.10 by kdk
		  //2.추가된 모든 내지 수량을 합산한 총 페이지수에 따른 후가공 가격테이블을 계산합니다.
		  if($option_group_type != "AFTEROPTION") {      		
          	$order_cnt = $order_cnt * (int)$order_cnt_page;
		  }
	  }*/
	  //#책자프리셋 후가공옵션 계산 방식 수정 2015.06.10 by kdk
      //debug($order_cnt);
      
      //면적 계산의 경우 주문수량 정보 대신 면적 (x, y 곱)을 넘긴다.
      if ($item_price_type == "SIZE")
      {
        if (! $document_x) $document_x = 0;
        if (! $document_y) $document_y = 0;
          
        if (! $print_x) $print_x = 0;
        if (! $print_y) $print_y = 0;
                  
        //현수막 천(지류)의 경우 면적 계산은 documnet 크기로 한다.
        if ($data[option_kind_code] == "PP")      
          $target_cnt =  (int)$document_x * (int)$document_y;
   
        //나머지 일반 면적 계산 옵션처리.  
        else 
          $target_cnt =  (int)$print_x * (int)$print_y;
        
		
		//기존 수량방식으로 입력된 가격을 사용하기 위해서 처리함 / 16.11.07 / kdk
		$target_cnt = $order_cnt;
		
      } else 
        $target_cnt = $order_cnt;
	  
//debug($data[option_price]);
//debug($target_cnt);

      $product_price_arr = getPriceSection($data[option_price], $target_cnt);
      $product_supply_price = $product_price_arr[0];   //원가,판매가 형태로 리턴된다.
      $product_price = $product_price_arr[1];   //원가,판매가 형태로 리턴된다.
//debug($product_price_arr);
//debug($product_supply_price);
//debug($product_price);

      //가격 계산
      if ($product_price != "-1") 
      {
        //echo $product_price . "<BR>";
                  
        if($item_price_type == "CNT")     //단가
        {
          //수식이 있는 경우 수식으로 계산.
          if (strpos($product_price, "[CNT]") !== false)
          {
            $product_price = str_replace("[CNT]", $order_cnt, $product_price);
            //echo $product_price;            
            $return_data = calc_string($product_price);
          } 
          else 
          {
            $return_data =  (int)$product_supply_price * (int)$order_cnt . "|";   //원가|판매가 형태로 넘겨준다   20140328
            $return_data .=  (int)$product_price * (int)$order_cnt;
          }
        }
        else if($item_price_type == "TIME")     //구간 가격 (1회)
          
          //수식이 있는 경우 수식으로 계산.
          if (strpos($product_price, "[CNT]") !== false)
          {
            $product_price = str_replace("[CNT]", $order_cnt, $product_price);
            $return_data = calc_string($product_price);
          } 
          else 
          {            
            $return_data = (int)$product_supply_price ."|";      //원가|판매가 형태로 넘겨준다   20140328
            $return_data .= (int)$product_price;
          }
        else if($item_price_type == "SIZE")     //면적 가격
        {      
          /*
          //현수막 천(지류)의 경우 면적 계산은 documnet 크기로 한다.
          if ($data[option_kind_code] == "PP")
          {            
            $return_data = (int)$product_supply_price * (int)$document_x * (int)$document_y . "|";   //원가|판매가 형태로 넘겨준다   20140328;  
            $return_data .= (int)$product_price * (int)$document_x * (int)$document_y;
            
          //나머지 일반 면적 계산 옵션처리.  
          } else {          
            //수식이 있는 경우 수식으로 계산. 면적 계산의 경우 print_x, print_y 문자엶로 체크
            if (strpos($product_price, "[PRINT_X]") !== false || strpos($product_price, "[PRINT_Y]") !== false)
            {
              $product_price = str_replace("[CNT]", "", $product_price);      //수량은 제거
              $product_price = str_replace("[PRINT_X]", $print_x, $product_price);
              $product_price = str_replace("[PRINT_Y]", $print_y, $product_price);
              $return_data = calc_string($product_price);                
            } else
            {
              $return_data = (int)$product_supply_price * (int)$print_x * (int)$print_y. "|";   //원가|판매가 형태로 넘겨준다   20140328;  
              $return_data .= (int)$product_price * (int)$print_x * (int)$print_y;
            }
          }         
            
          //$return_data =  $cnt_arr[2] * (int)$_GET[print_x] * (int)$_GET[print_y] * (int)$_GET[order_cnt];
		  */

		  //document 크기로 면적 계산의 처리함 / 16.11.07 / kdk
		  //시즌2부터 print_x,print_y를 사용하지 않음.
		  //면적당 금액이 소수점으로 입력가능하므로 float로 계산하고 소수점이하는 절사함.
		  //수량 처리가 누락되어 추가함. * (int)$target_cnt / 17.05.16 / kdk
          $return_data = floor((float)$product_supply_price * (float)$document_x * (float)$document_y) * (int)$target_cnt."|";   //원가|판매가 형태로 넘겨준다   20140328;  
          $return_data .= floor((float)$product_price * (int)$document_x * (int)$document_y) * (int)$target_cnt;
        }
      }
    }
    
    if ($return_data)
      return $return_data;
    else 
      return "-1";
  }

	function getOptionItemPriceTypeS2($cid, $center_id, $goodsno, $option_group_type, $add_where = '') {
	    global $db;
	
	    $classExtraOption = new M_extra_option();
	    $data = $classExtraOption->getOptionItemPriceType($cid, $center_id, $goodsno, $option_group_type, $add_where);
	    
	    if ($data)
	    {      
	        $return_data =  $data[item_price_type];
	    }
	
	    if ($return_data)
	      return $return_data;
	    else 
	      return "";
	}


	function getOptionMasterUse($cid, $center_id, $goodsno, $option_kind_index) {
	    global $db;
	
	    $classExtraOption = new M_extra_option();
	    $data = $classExtraOption->getExtraOptionMasterUse($cid, $center_id, $goodsno, $option_kind_index);
	    
	    if ($data)
	    {      
	        $return_data =  $data[use_flag];
	    }
	
	    if ($return_data)
	      return $return_data;
	    else 
	      return "";
	}
	
?>