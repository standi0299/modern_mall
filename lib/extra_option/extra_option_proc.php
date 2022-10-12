<?
	//옵션 등록
	function setOption($cid, $center_id, $goodsno, $display_name, $item_name, 
	$regist_flag, $extra_data1, $extra_data2, $order_index, $option_kind_code, 
	$option_sub_kind_code, $option_kind_index, $have_child, $display_flag, $goods_kind, 
	$necessary_flag, $option_group_type, $option_item_ID, $parent_option_item_ID, $item_price, 
	$item_price_type, $same_price_option_item_ID)
	{
	    global $db;
		
		$item_name = trim($item_name);
		
		$query = "
		insert into tb_extra_option set
			cid							= '$cid',
			bid							= '$center_id',
			goodsno						= '$goodsno',
			display_name				= '$display_name',
			item_name					= '$item_name',
			regist_flag					= '$regist_flag',
			regist_date					= now(),
			extra_data1					= '$extra_data1',
			extra_data2					= '$extra_data2',
			order_index					= '$order_index',
			option_kind_code			= '$option_kind_code',
			option_sub_kind_code		= '$option_sub_kind_code',			
			option_kind_index			= '$option_kind_index',
			have_child					= '$have_child',			
			display_flag				= '$display_flag',
			goods_kind					= '$goods_kind',
			necessary_flag				= '$necessary_flag',
			option_group_type			= '$option_group_type',
			option_item_ID				= '$option_item_ID',
			parent_option_item_ID		= '$parent_option_item_ID',
			item_price					= '$item_price',
			item_price_type				= '$item_price_type',
			same_price_option_item_ID	= '$same_price_option_item_ID'
		";
		
		//debug($query);
		
		$db->query($query);
		$return_data = $db->id;
		
		if ($return_data)
			return $return_data;
		else 
			return "-1";
	}

	//Max option_item_ID
	function getMaxItemID($cid, $center_id, $goodsno) {
		global $db;
		
		$query = "select max(convert(option_item_ID, signed)) + 1 as option_item_ID from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno';";
		//debug("getMaxItemID=".$query);
  
  		$res = $db->query($query);
    	$data = $db->fetch($res);
    
    	if ($data)
			return $data[option_item_ID];
		else
			return "-1";
	}

	//현재 option_kind_index를 기준으로 상위 option_kind_index 가져오기
	function getParentOptionKindIndex($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code) {
		global $db;
		
		$query = "select distinct(option_kind_index) as option_kind_index from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno'
			and option_kind_index < '$option_kind_index'";
		
		if($option_group_type == "AFTEROPTION") {
			$query .= " and option_group_type='AFTEROPTION'";
		} 
		else {
			$query .= " and option_group_type in ('DOCUMENT','FIXOPTION')";
		}

		$query .= " and option_kind_code='$option_kind_code' order by option_kind_index asc;";

  		//debug("getParentOptionKindIndex=".$query);
  		
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}

	//현재 option_kind_index를 기준으로 하위 option_kind_index 가져오기
	function getChildOptionKindIndex($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code) {
		global $db;
		
		$query = "select distinct(option_kind_index) as option_kind_index from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno'
			and option_kind_index > '$option_kind_index'";
		
		if($option_group_type == "AFTEROPTION") {
			$query .= " and option_group_type='AFTEROPTION'";
		} 
		else {
			$query .= " and option_group_type in ('DOCUMENT','FIXOPTION')";
		}

		$query .= " and option_kind_code='$option_kind_code' order by option_kind_index asc;";

  		//debug("getChildOptionKindIndex=".$query);
  		
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}	

	//Next option_kind_idex를 조회한다.
	function getNextOptionKindIndex($cid, $center_id, $goodsno, $option_kind_index, $option_group_type) {
		global $db;
		
		$option_kind_index++;
		
		$query = "select distinct(option_kind_index) as option_kind_index from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno'
			and option_kind_index = '$option_kind_index'";
		
		if($option_kind_code == "AFTEROPTION") {
			$query .= " and option_kind_code='AFTEROPTION'";
		} 
		else {
			$query .= " and option_group_type in ('DOCUMENT','FIXOPTION')";
		}
					
		$query .= ";";

  		//debug("getNextOptionKindIndex=".$query);
  		
  		$res = $db->query($query);
    	$data = $db->fetch($res);
    
    	if ($data)
			return $data[option_kind_index];
		else
			return "-1";
	}
		
	//Max option_kind_index
	function getMaxOptionKindIndex($cid, $center_id, $goodsno, $option_group_type, $option_kind_code="") {
		global $db;
		
		$query = "select max(convert(option_kind_index, signed)) + 1 as option_kind_index from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno' 
			and option_group_type='$option_group_type'";
			
		if($option_kind_code) $query .= " and option_kind_code='$option_kind_code'";
		
		//debug("getMaxOptionKindIndex=".$query);
		
  		$res = $db->query($query);
    	$data = $db->fetch($res);
    
    	if ($data)
			return $data[option_kind_index];
		else
			return "-1";
	}
	
	//Max order_index
	function getMaxOrderIndex($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code, $option_sub_kind_code, $parent_option_item_id = '') {
		global $db;
		
		$query = "select max(convert(order_index, signed)) + 1 as order_index from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno' 
			and option_kind_index='$option_kind_index'
			and option_group_type='$option_group_type'
			and option_kind_code='$option_kind_code'
			and option_sub_kind_code='$option_sub_kind_code'";

		if($parent_option_item_id)
			$query .= " and parent_option_item_id = '$parent_option_item_id'";

		//debug("getMaxOrderIndex=".$query);

  		$res = $db->query($query);
    	$data = $db->fetch($res);
    
    	if ($data)
			return $data[order_index];
		else
			return "-1";
	}
	
	//Group by option_kind_index
	function getGroupbyOptionKindIndex($cid, $center_id, $goodsno) {
		global $db;
		
		$query = "select option_kind_index from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno' group by option_kind_index;";
		
		//debug("getGroupbyOptionKindIndex=".$query);
  
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}
	
	//base 정보
	function getBaseOptionDataItem($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code, $option_sub_kind_code, $option_item_id) {
		global $db;
		
		$query = "select * from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno'
		and option_kind_index = '$option_kind_index'
		and option_group_type='$option_group_type'
		and option_kind_code='$option_kind_code'";

		if($option_sub_kind_code)
			$query .= " and option_sub_kind_code='$option_sub_kind_code'";
				
		if($option_item_id)
			$query .= " and option_item_id = '$option_item_id'";
		
		$query .= " and regist_flag = 'Y' order by order_index asc limit 1;";
		
		//debug("getBaseOptionDataItem=".$query);

  		$res = $db->query($query);
    	$data = $db->fetch($res);
    
    	if ($data)
			return $data;
		else
			return "-1";
	}
		
	//base_option_id 를 기준으로 하위 (parent_option_item_id)option_kind_index 데이타를 조회하여 복사한다.
	function getParentOptionItemList($cid, $center_id, $goodsno, $option_kind_index, $option_kind_code, $option_sub_kind_code, $parent_option_item_id) {
		global $db;
		
		$query = "select * from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno'
		and option_kind_index = '$option_kind_index'
		and option_kind_code='$option_kind_code'";

		if($option_sub_kind_code)
			$query .= " and option_sub_kind_code='$option_sub_kind_code'";
		
		if($parent_option_item_id)
			$query .= " and parent_option_item_id in ($parent_option_item_id)";
		
		$query .= " and regist_flag = 'Y' order by order_index asc;";
		
		//debug("getParentOptionItemList=".$query);
  
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}
	
	//같은 가격 항목 설정 가져오기
	function getDistinctSameOptionItemList($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code) {
		global $db;
		
		$query = "select distinct(item_name) from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno'
		and option_kind_index='$option_kind_index'
		and option_group_type='$option_group_type'
		and option_kind_code='$option_kind_code'
		and (same_price_option_item_ID is null or same_price_option_item_ID = '')
		and regist_flag = 'Y';";
		
		//debug("getDistinctSameOptionItemList=".$query);
  
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}

	//같은 가격 항목 설정 가져오기
	function getSameOptionItemList($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code, $option_sub_kind_code, $item_name = '', $parent_option_item_id = '') {
		global $db;
		
		$query = "select * from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno'
		and option_kind_index='$option_kind_index'
		and option_group_type='$option_group_type'
		and option_kind_code='$option_kind_code'
		and option_sub_kind_code='$option_sub_kind_code'";
		
		if($parent_option_item_id)
			$query .= " and parent_option_item_id = '$parent_option_item_id'";
			
		if($item_name)
			$query .= " and item_name = '$item_name'";
		
		$query .= " and regist_flag = 'Y' order by option_item_ID asc;";
		
		//debug("getSameOptionItemList=".$query);
  
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}

	//option_kind_index 기준으로 항목 가져오기
	function getKindIndexOptionItemList($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $option_kind_code, $option_sub_kind_code, $order_index='') {
		global $db;
		
		$query = "select * from tb_extra_option where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno'
		and option_kind_index='$option_kind_index'";
		
		if($option_kind_code == "AFTEROPTION") {
			$query .= " and option_kind_code='AFTEROPTION'";
		} 
		else {
			$query .= " and option_group_type in ('DOCUMENT','FIXOPTION')";
		}
		
		if($option_kind_code) $query .= " and option_kind_code='$option_kind_code'";
		
		if($option_sub_kind_code) $query .= " and option_sub_kind_code='$option_sub_kind_code'";
		
		if($order_index) $query .= " and order_index = '$order_index'";
			
		$query .= " and (same_price_option_item_ID is null or same_price_option_item_ID = '')
		and regist_flag = 'Y'
		order by order_index asc;";
		
		//debug("getKindIndexOptionItemList=".$query);
  
  		$data = $db->listArray($query);

    	if ($data)
			return $data;
	}

	//노출 여부 업데이트
	function setUpdateDisplayFlag($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $display_flag) {
		global $db;
	
		$query = "
		update tb_extra_option set
			display_flag = '$display_flag'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			and option_kind_index = '$option_kind_index'
			and option_group_type = '$option_group_type'
		";

		//debug($query);
		$db->query($query);
	}	

	//후가공 가격 타입 업데이트
	function setUpdateItemPriceType($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $item_price_type) {
		global $db;
	
		$query = "
		update tb_extra_option set
			item_price_type = '$item_price_type'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			and option_kind_index = '$option_kind_index'
			and option_group_type = '$option_group_type'
		";

		//debug($query);
		$db->query($query);
	}
	
	//출력 순서 업데이트
	function setUpdateOrderIndex($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $item_name, $order_index) {
		global $db;
	
		$query = "
		update tb_extra_option set
			order_index = '$order_index'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			and option_kind_index = '$option_kind_index'
			and option_group_type = '$option_group_type'
			and item_name = '$item_name'
		";

		//debug($query);
		$db->query($query);
	}	

	//사용 여부 업데이트
	function setUpdateRegistFlag($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $item_name, $regist_flag) {
		global $db;
	
		$query = "
		update tb_extra_option set
			regist_flag = '$regist_flag'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			and option_kind_index = '$option_kind_index'
			and option_group_type = '$option_group_type'
			and item_name = '$item_name'
		";

		//debug($query);
		$db->query($query);
	}
	
?>