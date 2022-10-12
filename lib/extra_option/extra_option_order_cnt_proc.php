<?
	//수정
	function setOptionOrderCntRule($cid, $center_id, $goodsno, $rule, $optionKindCode = '', $drule = '')
	{
	    global $db;
	
		if ($optionKindCode)
	  	$addWhere = " and option_kind_code = '$optionKindCode'";
	
		$query = "
		update tb_extra_option_order_cnt set
			cnt_rule = '$rule',
			display_cnt_rule = '$drule'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno	= '$goodsno'
			$addWhere
		";

		//debug($query);
		//echo $query;
		$db->query($query);
	}

	function setOptionOrderUnitCntRule($cid, $center_id, $goodsno, $rule, $optionKindCode = '')
	{
	    global $db;

		if ($optionKindCode)
	  	$addWhere = " and option_kind_code = '$optionKindCode'";
			
		$query = "
		update tb_extra_option_order_cnt set
			unit_cnt_rule = '$rule'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			$addWhere
		";

		//debug($query);
		$db->query($query);
	}	

	//수량(건수) 가격 테이블에 unit_cnt_rule 업데이트 2014.12.23 by kdk
	function setOptionPriceUnitCntRule($cid, $center_id, $goodsno, $rule)
	{
	    global $db;

		$query = "
		select * from tb_extra_option_unit_price 
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno	= '$goodsno'
		";

		//debug($query);
		$data = $db->fetch($query);

		if (!$data) {
			$query = "
			insert into tb_extra_option_unit_price (cid,bid,goodsno,unit_cnt_rule,regist_date)
			values ('$cid','$center_id','$goodsno','$rule',now())
			";
		}
		else {
			$query = "
			update tb_extra_option_unit_price set
				unit_cnt_rule = '$rule',
				update_date = now()
			where
				cid	= '$cid'
				and bid	= '$center_id'
				and goodsno = '$goodsno'
			";
		}

		//debug($query);
		//echo $query;
		$db->query($query);
	}

	//사용자 수량 단위 / 사용자 수량 입력 여부 업데이트 2015.06.03 by kdk
	function setOptionOrderCntRuleUser($cid, $center_id, $goodsno, $user_cnt_rule_name, $user_unit_cnt_rule_name, $user_cnt_input_flag, $optionKindCode = '')
	{
	    global $db;
		
		if ($optionKindCode)
	  	$addWhere = " and option_kind_code = '$optionKindCode'";
	
		$query = "
		update tb_extra_option_order_cnt set
			user_cnt_rule_name = '$user_cnt_rule_name',
			user_unit_cnt_rule_name = '$user_unit_cnt_rule_name',
			user_cnt_input_flag = '$user_cnt_input_flag'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno	= '$goodsno'
			$addWhere
		";

		//debug($query);
		//echo $query;
		$db->query($query);
	}	
		
	
	function setOptionOrderDisplayName($cid, $center_id, $goodsno, $displayName, $optionKindCode = '')
	{
	    global $db;

		if ($optionKindCode)
	  	$addWhere = " and option_kind_code = '$optionKindCode'";
			
		$query = "
		update tb_extra_option_order_cnt set
			display_name = '$displayName'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			$addWhere
		";

		//debug($query);
		$db->query($query);
	}
	
	function setOptionPriceType($cid, $center_id, $goodsno, $optionGroupType, $itemPriceType, $optionKindCode = '') {
	    global $db;

		if ($optionGroupType)
	  	$addWhere = " and option_group_type = '$optionGroupType'";

		if ($optionKindCode)
	  	$addWhere .= " and option_kind_code = '$optionKindCode'";

		$query = "
		update tb_extra_option_master set
			item_price_type = '$itemPriceType'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			$addWhere
		";
		//debug($query);
		//echo $query;
		$db->query($query);
		
		$query = "
		update tb_extra_option set
			item_price_type = '$itemPriceType'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
			$addWhere
		";
		//debug($query);
		//echo $query;		
		$db->query($query);
	}	

	//조회
	function getOptionOrderCntRule($cid, $center_id, $goodsno, $optionKindCode)
	{
	    global $db;
	
		$query = "
		select * from tb_extra_option_order_cnt 
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno	= '$goodsno'
			and option_kind_code = '$optionKindCode'
		";

		//debug($query);
		return $db->fetch($query);
	}
	
	//후가공 tb_extra_option_order_cnt 등록
	function setAfterOptionOrderCntRule($cid, $center_id, $goodsno, $optionKindCode)
	{
	    global $db;
	
        $query = "insert into tb_extra_option_order_cnt (
          cid, bid, goodsno, display_name, cnt_rule, regist_flag, regist_date, option_kind_code, unit_cnt_rule)
        VALUES (
          '$cid', '$center_id', '$goodsno', '"._("페이지")."', '1~10~1;', 'Y', now(), '$optionKindCode', '')";
		
		//debug($query);
		$db->query($query);
	}	
?>