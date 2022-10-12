<?php
/**
 * service_config
 * 2013.12.12 by chunter
 */

class M_extra_option {
	var $db;
	function M_extra_option() {
		$this -> db = $GLOBALS[db];
	}

	function getOptionList($cid, $center_id, $goodsno) {
		//$sql = "select a.*, b.item_price, b.item_price_type from tb_extra_option a, tb_extra_option_price b
		//  where a.cid = '$cid' and a.bid = '$center_id' and a.goodsno=$goodsno and a.regist_flag = 'Y'
		//  and a.option_item_ID = b.option_item_ID and b.regist_flag='Y' order by a.option_kind_index, a.order_index asc";

		$sql = "select * from tb_extra_option where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' order by option_kind_index, order_index asc";
		//return $this->db->query($query);
		return $this -> db -> listArray($sql);
	}

	function getOptionListToRes($cid, $center_id, $goodsno, $addWhere = '', $optionGroupType = '', $optionKindCode = '') {
		if ($optionGroupType) {
			if ($optionGroupType == "AFTEROPTION") {
				$optionGroupAddWhere = "and option_group_type = '$optionGroupType'";

				if ($optionKindCode) {
					$optionKindAddWhere = "and option_kind_code = '$optionKindCode'";
				}
			} else {
				$optionGroupAddWhere = "and option_group_type in ('$optionGroupType' ,'DOCUMENT')";
				//and option_group_type = '$optionGroupType' ,'DOCUMENT'
			}
		}

		$sql = "select * from tb_extra_option where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' $addWhere $optionGroupAddWhere $optionKindAddWhere order by option_kind_index, order_index asc";
		$res = $this -> db -> query($sql, false);
		//echo $sql;
		while ($data = @mysql_fetch_assoc($res)) {
			$loop[$data[option_item_ID]] = $data;
		}
		return $loop;
		//return $this->db->listArray($sql);
	}

	function getOptionListMaxDate($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select regist_date from tb_extra_option where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' $addWhere order by regist_date desc limit 1";
		//echo $sql;
		list($max_date) = $this -> db -> fetch($sql, 1);
		return $max_date;
	}

	function setDeleteAllOptionList($cid, $center_id, $goodsno, $addWhere = '') {
		//$sql = "update tb_extra_option set regist_flag = 'N' where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' $addWhere";
		$sql = "delete from tb_extra_option where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' $addWhere";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function setDeleteAllOptionListS2($cid, $center_id, $goodsno, $option_group_type) {
		$sql = "delete from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type'";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function getOrderCntList($cid, $center_id, $goodsno, $kind_code) {
		$sql = "select * from tb_extra_option_order_cnt where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' 
        and goodsno='$goodsno' and option_kind_code='$kind_code'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getOptionPriceList($cid, $center_id, $goodsno, $addWhere = '') {
		/*$sql = "select option_item_ID, item_price from tb_extra_option where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno'";
		//echo $sql;
		return $this -> db -> listArray($sql);*/
		
		$sql = "select * from tb_extra_option_price_info 
			where cid = '$cid' 
			and bid = '$center_id' 
			and goodsno = '$goodsno' 
			$addWhere
			";
		
		//echo $sql;
		return $this -> db -> listArray($sql);
		
	}

	function checkOptionPriceList($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select option_item_ID, item_price from tb_extra_option where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' $addWhere limit 1";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function setOptionItemPrice($cid, $center_id, $goodsno, $optionItemID, $priceData, $addWhere = '') {
		$sql = "update tb_extra_option set item_price='$priceData' where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$goodsno' and option_item_ID='$optionItemID' $addWhere";
		//echo $sql . "<BR>";
		$this -> db -> query($sql);
	}

	function getOptionItemInfo($cid, $center_id, $goodsno, $optionItemID, $addWhere = '') {
		$sql = "select * from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_item_ID='$optionItemID' $addWhere and regist_flag = 'Y'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getOptionChildItemList($cid, $center_id, $goodsno, $search_option_ID) {
		$sql = "select * from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and parent_option_item_ID='$search_option_ID' and regist_flag = 'Y' order by order_index asc";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	//regist_flag, display_flag 모두 'Y' 인경우만 조회
	function getOptionChildItemListOnlyVisible($cid, $center_id, $goodsno, $search_option_ID, $addWhere) {
		$sql = "select * from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and parent_option_item_ID='$search_option_ID'  $addWhere and regist_flag = 'Y' and display_flag = 'Y' order by order_index asc";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	//regist_flag, display_flag 모두 'Y' 인경우만 조회 (id => value)
	function getOptionChildItemListOnlyVisibleS2($cid, $center_id, $goodsno, $search_option_ID, $addWhere) {
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and parent_item_name='$search_option_ID' $addWhere and regist_flag = 'Y' and display_flag = 'Y' order by option_item_index asc";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function getOptionSamePriceItemID($cid, $center_id, $goodsno, $optionItemID, $addWhere = '') {
		$sql = "select same_price_option_item_ID from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno $addWhere and option_item_ID='$optionItemID' and regist_flag = 'Y'";
		//echo $sql;
		list($same_item_ID) = $this -> db -> fetch($sql, 1);
		return $same_item_ID;
	}

	//옵션 아이템 순서 변경.
	function setOptionItemIndexUpdate($cid, $center_id, $goodsno, $option_kind_index, $item_name, $order_index) {
		$query = "
      update tb_extra_option set
        order_index = '$order_index'
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_sub_kind_code = '$option_kind_index'
        and item_name = '$item_name'
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//옵션 item 출력 여부 업데이트
	function setOptionItemDisplayFlagUpdate($cid, $center_id, $goodsno, $option_kind_index, $item_name, $display_flag) {
		$query = "
      update tb_extra_option set
        display_flag = '$display_flag'
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_sub_kind_code = '$option_kind_index'        
        and item_name = '$item_name'
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//옵션 item 삭제
	function setOptionItemRegistFlagUpdate($cid, $center_id, $goodsno, $option_kind_index, $item_name, $regist_flag) {
		$query = "
      update tb_extra_option set
        regist_flag = '$regist_flag'
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_sub_kind_code = '$option_kind_index'        
        and item_name = '$item_name'
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//옵션  그룹 출력 명칭 변경
	function setOptionItemDisplayNameUpdate($cid, $center_id, $goodsno, $option_kind_index, $old_display_name, $display_name) {
		$query = "
      update tb_extra_option set
        display_name = '$display_name'
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'        
        and option_sub_kind_code = '$option_kind_index'
        and display_name = '$old_display_name'        
        and regist_flag = 'Y'
      ";
		//and option_kind_index = '$option_kind_index'

		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	//옵션 명칭 변경
	function setOptionItemNameUpdate($cid, $center_id, $goodsno, $option_kind_index, $old_item_name, $update_item_name) {
		$query = "
      update tb_extra_option set
        item_name = '$update_item_name'
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_sub_kind_code = '$option_kind_index'
        and item_name = '$old_item_name'        
        and regist_flag = 'Y'
      ";
		//and option_kind_index = '$option_kind_index'

		//debug($query);
		$this -> db -> query($query);
	}

	function CopyOrderCntExtraOption($cid, $center_id, $goodsno, $sourceGoodsNo) {

		$query = "insert into tb_extra_option_order_cnt (
      cid, bid, goodsno, display_name, cnt_rule, regist_flag, regist_date, option_kind_code, unit_cnt_rule, display_cnt_rule, user_cnt_rule_name, user_unit_cnt_rule_name, user_cnt_input_flag)
      select 
        '$cid', '$center_id', '$goodsno', display_name, cnt_rule, regist_flag, now(), option_kind_code, unit_cnt_rule, display_cnt_rule, user_cnt_rule_name, user_unit_cnt_rule_name, user_cnt_input_flag
      from tb_extra_option_order_cnt
      where cid = '$center_id' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	function CopyPriceExtraOption($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option (
			cid, bid, display_flag, display_name, extra_data1, extra_data2, 
			goods_kind, goodsno, have_child, item_name, item_price, 
			item_price_type, master_option_kind_code, necessary_flag, option_group_type, option_item_ID, 
			option_kind_code, option_kind_index, option_sub_kind_code, order_index, parent_option_item_ID, 
			regist_date, regist_flag, same_price_option_item_ID
		)
    	select
			'$cid', '$center_id', display_flag, display_name, extra_data1, extra_data2, 
			goods_kind, '$goodsno', have_child, item_name, item_price, 
			item_price_type, master_option_kind_code, necessary_flag, option_group_type, option_item_ID, 
			option_kind_code, option_kind_index, option_sub_kind_code, order_index, parent_option_item_ID, 
			now(), regist_flag, same_price_option_item_ID
    	from tb_extra_option
    	where cid = '$center_id' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	// ################################
	//프리셋 옵션 관리 테이블
	// ################################

	function getAdminOptionItemInfo($cid, $center_id, $goodsno, $option_kind_index, $item_name = '') {
		if ($item_name)
			$addWhere = " and item_name='$item_name'";

		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_kind_index='$option_kind_index' $addWhere and regist_flag = 'Y'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	//master 옵션 사용여부 리스트 조회
	function getAdminOptionUseList($cid, $center_id, $goodsno) {
		$sql = "select * from tb_extra_option_master_use where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno order by option_kind_index asc";
		//return $this->db->query($query);
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	//옵션 사용여부 업데이트
	function setUpdateUseFlag($cid, $center_id, $goodsno, $option_kind_index, $use_flag) {
		$query = "update tb_extra_option_master_use set
        use_flag = '$use_flag',
        update_date = now()
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'        
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//옵션 사용여부 업데이트
	function setInsertUseFlag($cid, $center_id, $goodsno, $option_kind_index, $use_flag, $option_group_type) {
		$query = "insert into tb_extra_option_master_use set              
          cid = '$cid',
          bid = '$center_id',
          goodsno = '$goodsno',
          option_kind_index = '$option_kind_index',
          use_flag = '$use_flag',
          option_group_type = '$option_group_type'        
        on duplicate key update
          use_flag = '$use_flag'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//후가공 가격 타입 업데이트
	function setUpdateAdminItemPriceType($cid, $center_id, $goodsno, $option_kind_index, $item_price_type) {
		$query = "
      update tb_extra_option_master set
        item_price_type = '$item_price_type',
        update_date = now()
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        and regist_flag = 'Y'        
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//옵션 item 사용순서 변경
	function setUpdateAdminItemOrderIndex($cid, $center_id, $goodsno, $option_kind_index, $item_name, $order_index) {
		$query = "
      update tb_extra_option_master set
        option_item_index = '$order_index',
        update_date = now()
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        and item_name = '$item_name'
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 item 출력 여부 업데이트
	function setUpdateAdminItemDisplayFlag($cid, $center_id, $goodsno, $option_kind_index, $option_group_type, $item_name, $display_flag, $option_kind_code = '') {
		if ($option_kind_code)
			$addWhere = " and option_kind_code='$option_kind_code'";

		$query = "
      update tb_extra_option_master set
        display_flag = '$display_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        $addWhere        
        and item_name = '$item_name'
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 하위 item 출력 여부 업데이트 2015.05.12 by kdk
	function setUpdateAdminChildItemDisplayFlag($cid, $center_id, $goodsno, $parent_item_name, $display_flag, $option_kind_code = '') {
		if ($option_kind_code)
			$addWhere = " and option_kind_code='$option_kind_code'";

		$query = "
      update tb_extra_option_master set
        display_flag = '$display_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and parent_item_name = '$parent_item_name'
        $addWhere
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 삭제
	function setUpdateAdminOptionRegistFlag($cid, $center_id, $goodsno, $option_kind_index, $regist_flag) {
		$query = "
      update tb_extra_option_master set
        regist_flag = '$regist_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'        
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 item 삭제
	function setUpdateAdminItemRegistFlag($cid, $center_id, $goodsno, $option_kind_index, $item_name, $regist_flag, $option_kind_code = '') {
		if ($option_kind_code)
			$addWhere = " and option_kind_code='$option_kind_code'";

		$query = "
      update tb_extra_option_master set
        regist_flag = '$regist_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        $addWhere        
        and item_name = '$item_name'
        and regist_flag = 'Y'
      ";

		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	//마스터 옵션 하위 item 삭제 2014.06.26 by kdk
	function setUpdateAdminChildItemRegistFlag($cid, $center_id, $goodsno, $parent_item_name, $regist_flag, $option_kind_code = '') {
		if ($option_kind_code)
			$addWhere = " and option_kind_code='$option_kind_code'";

		$query = "
      update tb_extra_option_master set
        regist_flag = '$regist_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and parent_item_name = '$parent_item_name'
        $addWhere
        and regist_flag = 'Y'
      ";

		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	//마스터 옵션 item 삭제
	function setUpdateAdminDisplayName($cid, $center_id, $goodsno, $option_kind_index, $display_name) {
		$query = "
      update tb_extra_option_master set
        display_name = '$display_name',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        and regist_flag = 'Y'
      ";

		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	function setUpdateAdminOptionItemName($cid, $center_id, $goodsno, $option_kind_index, $item_index, $old_item_name, $update_item_name) {
		$query = "
      update tb_extra_option_master set
        item_name = '$update_item_name',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        and option_item_index = '$item_index'        
        and item_name = '$old_item_name'        
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}
	
	//책자 프리셋3 (100112)
	function setUpdateAdminOptionItemNameExtraData($cid, $center_id, $goodsno, $option_kind_index, $item_index, $old_item_name, $update_item_name, $extra_data1, $extra_data2) {
		$query = "
      update tb_extra_option_master set
        item_name = '$update_item_name',
        extra_data1 = '$extra_data1',
        extra_data2 = '$extra_data2',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        and option_item_index = '$item_index'        
        and item_name = '$old_item_name'        
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}	
	
	//부모 옵션 item 명칭 변경하기
	function setUpdateAdminOptionParentItemName($cid, $center_id, $goodsno, $option_kind_index, $old_item_name, $update_item_name) {
		$query = "
      update tb_extra_option_master set
        parent_item_name = '$update_item_name',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'                
        and parent_item_name = '$old_item_name'        
        and regist_flag = 'Y'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//master 옵션 리스트 조회
	function getAdminOptionList($cid, $center_id, $goodsno, $display_flag = '', $optionGroupType = '', $optionKindCode = '') {
		if ($display_flag)
			$displayAddWhere = "and display_flag = 'Y'";

		if ($optionGroupType) {
			if ($optionGroupType == "AFTEROPTION") {
				$optionGroupAddWhere = "and option_group_type = '$optionGroupType'";
			} else {
				$optionGroupAddWhere = "and option_group_type in ('$optionGroupType','DOCUMENT')";
				//and option_group_type = '$optionGroupType'
			}
		}
		if ($optionKindCode)
			$optionKindAddWhere = "and option_kind_code = '$optionKindCode'";

		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' $displayAddWhere and goodsno=$goodsno $optionGroupAddWhere $optionKindAddWhere and regist_flag = 'Y' order by option_kind_index, option_item_index asc";
		//return $this->db->query($query);
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function getAdminOptionItemList($cid, $center_id, $goodsno, $option_kind_index) {
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_kind_index='$option_kind_index' and regist_flag = 'Y' order by option_item_index asc";
		//return $this->db->query($query);
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	//마스터 옵션 항목 하위 항목조회. (value 로만 검색)
	function getAdminOptionChildItem($cid, $center_id, $goodsno, $itemValue, $optionKindCode = '') {
		if ($optionKindCode)
			$addWhere = "and option_kind_code = '$optionKindCode'";

		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and parent_item_name='$itemValue' $addWhere and goodsno=$goodsno and display_flag = 'Y' and regist_flag = 'Y' order by option_item_index asc";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	//같은가격 마스터 옵션 찾기
	function getAdminSamePriceOptionItem($cid, $center_id, $goodsno, $itemValue, $optionKindCode = '') {
		if ($optionKindCode)
			$addWhere = "and option_kind_code = '$optionKindCode'";

		$sql = "select same_price_item_name from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and item_name='$itemValue' $addWhere and goodsno=$goodsno and display_flag = 'Y' and regist_flag = 'Y' order by option_item_index asc";
		//echo $sql;
		list($same_price_item) = $this -> db -> fetch($sql, 1);
		return $same_price_item;
	}

	//옵션 kind_index 순서 맞는 항목(item) 리스트 조회
	function getMaxExtraOptionMasterItemIndex($cid, $center_id, $goodsno, $option_kind_index) {
		$sql = "select option_item_index from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_kind_index=$option_kind_index and regist_flag = 'Y' order by option_item_index desc limit 1";
		//echo $sql;
		list($option_item_index) = $this -> db -> fetch($sql, 1);
		if (!$option_item_index)
			$option_item_index = 0;
		return $option_item_index;
	}

	function getMaxExtraOptionMasterKindIndex($cid, $center_id, $goodsno) {
		$sql = "select option_kind_index from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and regist_flag = 'Y' order by option_kind_index desc limit 1";
		//echo $sql;
		list($option_kind_index) = $this -> db -> fetch($sql, 1);
		if (!$option_kind_index)
			$option_kind_index = 0;
		return $option_kind_index;
	}

	function getMaxExtraOptionMasterTblKindIndex($cid, $center_id, $goodsno) {
		$sql = "select extra_tbl_kind_index from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and regist_flag = 'Y' order by extra_tbl_kind_index desc limit 1";
		//echo $sql;
		list($extra_tbl_kind_index) = $this -> db -> fetch($sql, 1);
		if (!$extra_tbl_kind_index)
			$extra_tbl_kind_index = 0;
		return $extra_tbl_kind_index;
	}

	//같은 가격 항목 설정 가져오기
	function getAdminSameOptionItemList($cid, $center_id, $goodsno, $option_kind_index) {
		$query = "select item_name from tb_extra_option_master where cid = '$cid' and bid='$center_id' and goodsno = '$goodsno'
      and option_kind_index='$option_kind_index' and regist_flag = 'Y'
      and (same_price_item_name is null or same_price_item_name = '')";
		//echo $query;

		$data = $this -> db -> listArray($query);
		return $data;
	}

	function getAdminOptionListMaxDate($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select regist_date from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' $addWhere and regist_flag = 'Y' order by regist_date desc limit 1";
		//echo $sql;
		list($max_date) = $this -> db -> fetch($sql, 1);
		return $max_date;
	}

	function getAdminOptionListMaxUpdateDate($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select update_date from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' $addWhere order by update_date desc limit 1";
		//echo $sql;
		list($max_date) = $this -> db -> fetch($sql, 1);
		if ($max_date == null)
			$max_date = '';
		return $max_date;
	}

	function getAdminOptionUseFlagMaxDate($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select update_date from tb_extra_option_master_use where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno'  and option_kind_index not in (98,99) $addWhere order by update_date desc limit 1";
		//echo $sql;
		list($max_date) = $this -> db -> fetch($sql, 1);
		if ($max_date == null)
			$max_date = '';
		return $max_date;
	}

	//master 옵션 항목 추가
	function InsertExtraOptionMaster($cid, $center_id, $goodsno, $option_kind_index, $option_item_name, $option_item_index, $parent_item_name, $same_price_item_name, $extra_data1, $extra_data2, $option_group_type = '', $display_name = '', $item_price_type = '', $use_flag = 'Y') {
		//부모 item 이 있을 경우 조건식에 추가한다.
		if ($parent_item_name)
			$addWhere = " and parent_item_name='$parent_item_name'";

		//같은 값이 가진 item_name이 있을 경우는 추가하지 않는다.
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
        and goodsno=$goodsno and item_name='$option_item_name' and regist_flag = 'Y'" . $addWhere;
		//echo $sql;
		$item_data = $this -> db -> fetch($sql);

		if (!$item_data) {
			$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
          and goodsno=$goodsno  and regist_flag = 'Y' and (same_price_item_name='' or same_price_item_name is null) limit 1";
			//echo $sql;
			$item_data = $this -> db -> fetch($sql);

			if (!$item_data) {
				$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
	          and goodsno=$goodsno  and (same_price_item_name='' or same_price_item_name is null) limit 1";
				//echo $sql;
				$item_data = $this -> db -> fetch($sql);
			}

			$display_flag = 'Y';
			//기존 입력된 옵션이 있을경우 (item 만 추가한 경우)
			if ($item_data) {
				$option_kind_index = $item_data[option_kind_index];
				$option_kind_code = $item_data[option_kind_code];

				$display_name = $item_data[display_name];
				$necessary_flag = $item_data[necessary_flag];
				$have_child = $item_data[have_child];

				$item_price_type = $item_data[item_price_type];
				$option_group_type = $item_data[option_group_type];

				$extra_tbl_kind_code = $item_data[extra_tbl_kind_code];
				$extra_tbl_kind_index = $item_data[extra_tbl_kind_index];

				if ($parent_item_name == "")
					$parent_item_name = $item_data[parent_item_name];
			} else {
				if ($option_group_type == 'AFTEROPTION')
					$necessary_flag = "N";
				else
					$necessary_flag = "Y";

				$have_child = '0';
				$extra_tbl_kind_index = $this -> getMaxExtraOptionMasterTblKindIndex($cid, $center_id, $goodsno) + 1;
				$option_kind_code = 'ETC' . $extra_tbl_kind_index;
				$extra_tbl_kind_code = 'ETC' . $extra_tbl_kind_index;

				$this -> setInsertUseFlag($cid, $center_id, $goodsno, $option_kind_index, $use_flag, $option_group_type);
			}

			$query = "INSERT INTO tb_extra_option_master(
          cid, bid, goodsno, option_kind_code, item_name, 
          parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
          display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
          item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index, regist_flag)
        VALUES(
          '$cid', '$center_id', '$goodsno', '$option_kind_code', '$option_item_name',
          '$parent_item_name','$extra_data1','$extra_data2','$option_kind_index','$option_item_index',
          '$display_flag','$display_name','$necessary_flag','$have_child','$same_price_item_name',
          '$item_price_type','$option_group_type','$extra_tbl_kind_code','$extra_tbl_kind_index', 'Y')";
			//debug($query);
			$this -> db -> query($query);
		}
	}

	//실제 옵션 처리 table 에 옵션 항목 추가하기.
	function InsertExtraOption($cid, $center_id, $goodsno, $goods_kind, $option_item_ID, $parent_option_item_ID, $same_price_option_item_ID, $masterOptionData, $InsertExtraTblKindIndex = 0) {
		if ($InsertExtraTblKindIndex == 0)
			$InsertExtraTblKindIndex = $masterOptionData[extra_tbl_kind_index];

		$query = "insert into tb_extra_option (
        cid, bid, goodsno, display_name, item_name, regist_flag, extra_data1, extra_data2, order_index, option_kind_code, 
        option_sub_kind_code, option_kind_index, have_child, display_flag, goods_kind, necessary_flag, option_group_type, 
        option_item_ID, parent_option_item_ID, item_price, item_price_type, same_price_option_item_ID, master_option_kind_code, regist_date)
      values( 
        '$cid', '$center_id', '$goodsno', '$masterOptionData[display_name]', '$masterOptionData[item_name]', 'Y', '$masterOptionData[extra_data1]', '$masterOptionData[extra_data2]', '$masterOptionData[option_item_index]', '$masterOptionData[extra_tbl_kind_code]', 
        '$masterOptionData[extra_tbl_kind_index]', '$InsertExtraTblKindIndex', '$masterOptionData[have_child]', '$masterOptionData[display_flag]', '$goods_kind', '$masterOptionData[necessary_flag]', '$masterOptionData[option_group_type]',
        '$option_item_ID', '$parent_option_item_ID', '$masterOptionData[item_price]', '$masterOptionData[item_price_type]', '$same_price_option_item_ID', '$masterOptionData[option_kind_code]',now())";
		//debug($query);
		//echo($query.";<br>");
		$this -> db -> query($query, false);
		//$this->db->id;
	}

	function CopyMasterExtraOption($cid, $center_id, $goodsno, $item_price_type, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_master (
        cid, bid, goodsno, option_kind_code, item_name, 
        parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
        display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
        item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index
      )
      select       
        '$cid', '$center_id', '$goodsno', option_kind_code, item_name, 
          parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
          display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
          item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index 
        from tb_extra_option_master 
        where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo' and option_group_type<>'AFTEROPTION' and regist_flag = 'Y' order by option_kind_index, option_item_index asc 
      ";
		//debug($query);
		//and (option_group_type='FIXOPTION' or option_group_type='DOCUMENT') 를 and option_group_type<>'AFTEROPTION' 로 변경 2014.07.10 by kdk
		$this -> db -> query($query, false);
	}

	function CopyMasterExtraAfterOption($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_master (
        cid, bid, goodsno, option_kind_code, item_name, 
        parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
        display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
        item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index
      )
      select       
        '$cid', '$center_id', '$goodsno', option_kind_code, item_name, 
          parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
          display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
          item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index 
        from tb_extra_option_master 
        where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo' and option_group_type='AFTEROPTION' and regist_flag = 'Y' order by option_kind_index, option_item_index asc 
      ";
		//debug($query);
		$this -> db -> query($query, false);
	}

	function CopyMasterExtraOptionUse($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "INSERT INTO tb_extra_option_master_use
            (goodsno, cid, bid, option_kind_index, use_flag, option_group_type)
            
            select '$goodsno', '$cid', '$center_id', option_kind_index, use_flag, option_group_type from tb_extra_option_master_use
            where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo' order by option_kind_index asc";
		//debug($query);
		$this -> db -> query($query);
	}

	//goodsKind 조회 2014.07.18 by kdk
	function getGoodsKind($goodsno) {
		$sql = "select extra_option from exm_goods where goodsno=$goodsno";
		list($extra_option) = $this -> db -> fetch($sql, 1);
		if ($extra_option) {
			$extraOption = explode('|', $extra_option);
			//항목 분리
			if (count($extraOption) > 0) {
				$goodsKind = $extraOption[0];
			}
		}
		return $goodsKind;
	}

	//option_kind_index 찾기
	function getExtraOptionMasterKindIndex($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select option_kind_index from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno $addWhere and regist_flag = 'Y' order by option_kind_index desc limit 1";
		//echo $sql;
		list($option_kind_index) = $this -> db -> fetch($sql, 1);
		if (!$option_kind_index)
			$option_kind_index = 0;
		return $option_kind_index;
	}

	//실제 옵션 처리 table 에 옵션 항목 삭제하기.
	function DeleteExtraOption($cid, $center_id, $goodsno) {
		$query = "delete from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno'";
		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	//실제 옵션 처리 table 에 옵션 항목 삭제하기.
	function DeleteExtraOptionS2($cid, $center_id, $goodsno) {
		$query = "delete from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno'";
		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	//실제 옵션 처리 table 에 옵션 항목 option_group_type 별 삭제하기.
	function DeleteExtraOptionS2group($cid, $center_id, $goodsno, $optionGroupType) {
		$query = "delete from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$optionGroupType'";
		//debug($query);
		//echo $query;
		$this -> db -> query($query);
	}

	//option_item_id
	function getMaxOptionItemId($cid, $center_id, $goodsno) {
		$sql = "select max(convert(option_item_ID, signed)) + 1 as option_item_id  from tb_extra_option where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno";
		//echo $sql;
		list($option_item_id) = $this -> db -> fetch($sql, 1);
		if (!$option_item_id || $option_item_id == 0)
			$option_item_id = 1;
		return $option_item_id;
	}

	// ################################
	//수량(건수) 테이블 2014.12.23 by kdk
	// ################################
	function getOptionUnitPrice($cid, $center_id, $goodsno) {
		$sql = "select * from tb_extra_option_unit_price where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function setOptionUnitPrice($cid, $center_id, $goodsno, $unit_cnt_price) {
		$sql = "
		update tb_extra_option_unit_price set
			unit_cnt_price = '$unit_cnt_price'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
		";
		//echo $sql;
		return $this -> db -> query($sql);
	}

	//수량(건수) 가격 테이블에 unit_cnt_rule 업데이트 2014.12.23 by kdk
	function setOptionUnitPriceRule($cid, $center_id, $goodsno, $rule) {
		$sql = "
		update tb_extra_option_unit_price set
			unit_cnt_rule = '$rule'
		where
			cid	= '$cid'
			and bid	= '$center_id'
			and goodsno = '$goodsno'
		";

		//debug($sql);
		//echo $sql;
		return $this -> db -> query($sql);
	}

	//수량(건수) 가격 테이블에 unit_cnt_rule insert 2015.01.06 by kdk
	function setInsertOptionUnitPriceRule($cid, $center_id, $goodsno, $rule) {
		$query = "insert into tb_extra_option_unit_price (cid,bid,goodsno,unit_cnt_rule,regist_date)
		values ('$cid','$center_id','$goodsno','$rule',now())";

		//debug($query);
		$this -> db -> query($query);
	}

	function CopyUnitPriceExtraOption($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_unit_price (cid, bid, goodsno, display_name, unit_cnt_rule, unit_cnt_price, regist_date)
    	select '$cid', '$center_id', '$goodsno', display_name, unit_cnt_rule, unit_cnt_price, regist_date
    	from tb_extra_option_unit_price
    	where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	//goods Preset 조회 2015.02.13 by kdk
	function GetGoodsPreset($goodsno) {
		$sql = "select extra_option from exm_goods where goodsno=$goodsno";
		list($extra_option) = $this -> db -> fetch($sql, 1);
		if ($extra_option) {
			$extraOption = explode('|', $extra_option);
			//항목 분리
			if (count($extraOption) > 0) {
				$goodsPreset = $extraOption[1];
			}
		}
		return $goodsPreset;
	}

	//tb_extra_option_master_use 옵션 사용여부 조회
	function getExtraOptionMasterUse($cid, $center_id, $goodsno, $optionKindIndex) {
		$sql = "select use_flag from tb_extra_option_master_use where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_kind_index='$optionKindIndex' limit 1";

		list($use_flag) = $this -> db -> fetch($sql, 1);
		if (!$use_flag)
			$use_flag = "Y";
		return $use_flag;
	}

	//자동견적 가격 테이블 시즌2 관련 쿼리문들
	function getOptionPriceListS2($cid, $center_id, $goodsno, $option_group_type = '') {
		$sql = "select * from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type' order by order_index asc";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function checkOptionPriceListS2($cid, $center_id, $goodsno, $option_group_type = '') {
		$sql = "select ID from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type' limit 1";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getOptionListMaxDateS2($cid, $center_id, $goodsno, $option_group_type = '') {
		$sql = "select update_date from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type' order by update_date desc limit 1";
		//echo $sql;
		list($max_date) = $this -> db -> fetch($sql, 1);
		return $max_date;
	}

	function CopyPriceExtraOptionS2($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_price_info (cid, bid, goodsno, option_group_type,option_item,option_price,order_index, regist_date, update_date)
    	select '$cid', '$center_id', '$goodsno', option_group_type,option_item,option_price,order_index, now(), now()
    	from tb_extra_option_price_info
    	where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	function getOptionItemInfoS2($cid, $center_id, $goodsno, $optionItem, $addWhere = '') {
		$sql = "select * from tb_extra_option_price_info where cid='$cid' and bid='$center_id' and goodsno=$goodsno and option_item='$optionItem' $addWhere";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getOptionItemPriceType($cid, $center_id, $goodsno, $option_group_type, $addWhere = '') {
		$sql = "select item_price_type from tb_extra_option_master where cid='$cid' and bid='$center_id' and goodsno=$goodsno and option_group_type='$option_group_type' $addWhere limit 1";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	//마스터 옵션 item사용 여부 처리
	function setUpdateRegistFlag($cid, $center_id, $goodsno, $option_kind_index, $option_item_index, $regist_flag) {
		$query = "
      update tb_extra_option_master set
        regist_flag = '$regist_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'        
        and option_item_index = '$option_item_index'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 하위 item사용 여부 처리
	function setUpdateChildRegistFlag($cid, $center_id, $goodsno, $option_kind_index, $option_item_index, $regist_flag) {
		$query = "
      update tb_extra_option_master set
        regist_flag = '$regist_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'        
        and option_item_index = '$option_item_index'
      ";

		//debug($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 item 출력 여부 업데이트
	function setUpdateDisplayFlag($cid, $center_id, $goodsno, $option_kind_index, $option_item_index, $display_flag, $option_kind_code = '') {
		if ($option_kind_code)
			$addWhere = " and option_kind_code='$option_kind_code'";

		$query = "
      update tb_extra_option_master set
        display_flag = '$display_flag',
        update_date = now()        
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'        
        and option_item_index = '$option_item_index'
        $addWhere
      ";

		//debug($query);
		//echo($query);
		$this -> db -> query($query);
	}

	//마스터 옵션 하위 item 출력 여부 업데이트
	function setUpdateChildDisplayFlag($cid, $center_id, $goodsno, $option_kind_index, $option_item_index, $display_flag, $option_kind_code = '') {
		if ($option_kind_code)
			$addWhere = " and option_kind_code='$option_kind_code'";

		$sql = "select item_name from tb_extra_option_master 
	  	where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' 
	  		and option_kind_index='$option_kind_index' 
	  		and option_item_index='$option_item_index' limit 1";

		$data = $this -> db -> fetch($sql);
		if ($data) {
			$item_name = $data[item_name];

			$query = "
	      update tb_extra_option_master set
	        display_flag = '$display_flag',
	        update_date = now()        
	      where
	        cid = '$cid'
	        and bid = '$center_id'
	        and goodsno = '$goodsno'
	        and parent_item_name = '$item_name'
	        $addWhere
	      ";

			//debug($query);
			//echo($query);
			$this -> db -> query($query);
		}
	}

	//같은가격 마스터 옵션 찾기 2015.08.11 by kdk
	function getSamePriceOptionItem($cid, $center_id, $goodsno, $itemValue, $addWhere = '') {
		$sql = "select same_price_item_name from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and item_name='$itemValue' $addWhere and goodsno=$goodsno and display_flag = 'Y' and regist_flag = 'Y' order by option_item_index asc";
		//echo $sql;
		list($same_price_item) = $this -> db -> fetch($sql, 1);
		return $same_price_item;
	}

	//같은 값이 가진 item_name이 있는 확인한다.
	function getSameItemName($cid, $center_id, $goodsno, $option_kind_index, $option_item_name) {
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
        and goodsno=$goodsno and item_name='$option_item_name' and regist_flag = 'Y'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	### 자동견적 옵션 초기화(삭제) 2016.04.06 by kdk
	function extraOptionInit($cid, $center_id, $goodsno) {
		$this -> db -> query("delete from tb_extra_option where cid='$cid' and bid='$center_id' and goodsno='$goodsno'");
		$this -> db -> query("delete from tb_extra_option_master where cid='$cid' and bid='$center_id' and goodsno='$goodsno'");
		$this -> db -> query("delete from tb_extra_option_order_cnt where cid='$cid' and bid='$center_id' and goodsno='$goodsno'");
		$this -> db -> query("delete from tb_extra_option_master_use where cid='$cid' and bid='$center_id' and goodsno='$goodsno'");
		$this -> db -> query("delete from tb_extra_option_unit_price where cid='$cid' and bid='$center_id' and goodsno='$goodsno'");
		$this -> db -> query("delete from tb_extra_option_price_info where cid='$cid' and bid='$center_id' and goodsno='$goodsno'");
	}

	### 자동견적 옵션 복사 2016.04.06 by kdk
	function extraOptionCopy($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$this -> CopyMasterExtraOption($cid, $center_id, $goodsno, "", $sourceGoodsNo);
		$this -> CopyMasterExtraAfterOption($cid, $center_id, $goodsno, $sourceGoodsNo);
		$this -> CopyMasterExtraOptionUse($cid, $center_id, $goodsno, $sourceGoodsNo);
		$this -> CopyOrderCntExtraOption($cid, $center_id, $goodsno, $sourceGoodsNo);

		//자동견적 가격 테이블 시즌2
		$this -> CopyPriceExtraOptionS2($cid, $center_id, $goodsno, $sourceGoodsNo);

		//수량(건수) 가격 정보를 복사한다. 2014.12.23 by kdk
		$this -> CopyUnitPriceExtraOption($cid, $center_id, $goodsno, $sourceGoodsNo);
	}

	### 몰 자체상품일 자동견적 옵션 복사 2016.04.06 by kdk
	function extraOptionCopyMall($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$this -> CopyMasterExtraOptionMall($cid, $center_id, $goodsno, "", $sourceGoodsNo);
		$this -> CopyMasterExtraAfterOptionMall($cid, $center_id, $goodsno, $sourceGoodsNo);
		$this -> CopyMasterExtraOptionUseMall($cid, $center_id, $goodsno, $sourceGoodsNo);
		$this -> CopyOrderCntExtraOptionMall($cid, $center_id, $goodsno, $sourceGoodsNo);

		//자동견적 가격 테이블 시즌2
		$this -> CopyPriceExtraOptionS2Mall($cid, $center_id, $goodsno, $sourceGoodsNo);

		//수량(건수) 가격 정보를 복사한다. 2014.12.23 by kdk
		$this -> CopyUnitPriceExtraOptionMall($cid, $center_id, $goodsno, $sourceGoodsNo);
	}

	//몰 자체상품일 경우 cid = cid
	function CopyMasterExtraOptionMall($cid, $center_id, $goodsno, $item_price_type, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_master (
        cid, bid, goodsno, option_kind_code, item_name, 
        parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
        display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
        item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index
      )
      select       
        '$cid', '$center_id', '$goodsno', option_kind_code, item_name, 
          parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
          display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
          item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index 
        from tb_extra_option_master 
        where cid = '$cid' and bid = '$center_id' and goodsno='$sourceGoodsNo' and option_group_type<>'AFTEROPTION' and regist_flag = 'Y' order by option_kind_index, option_item_index asc 
      ";
		//debug($query);
		//and (option_group_type='FIXOPTION' or option_group_type='DOCUMENT') 를 and option_group_type<>'AFTEROPTION' 로 변경 2014.07.10 by kdk
		$this -> db -> query($query, false);
	}

	//몰 자체상품일 경우 cid = cid
	function CopyMasterExtraAfterOptionMall($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_master (
        cid, bid, goodsno, option_kind_code, item_name, 
        parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
        display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
        item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index
      )
      select       
        '$cid', '$center_id', '$goodsno', option_kind_code, item_name, 
          parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
          display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
          item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index 
        from tb_extra_option_master 
        where cid = '$cid' and bid = '$center_id' and goodsno='$sourceGoodsNo' and option_group_type='AFTEROPTION' and regist_flag = 'Y' order by option_kind_index, option_item_index asc 
      ";
		//debug($query);
		$this -> db -> query($query, false);
	}

	//몰 자체상품일 경우 cid = cid
	function CopyMasterExtraOptionUseMall($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "INSERT INTO tb_extra_option_master_use
            (goodsno, cid, bid, option_kind_index, use_flag, option_group_type)
            
            select '$goodsno', '$cid', '$center_id', option_kind_index, use_flag, option_group_type from tb_extra_option_master_use
            where cid = '$cid' and bid = '$center_id' and goodsno='$sourceGoodsNo' order by option_kind_index asc";
		//debug($query);
		$this -> db -> query($query);
	}

	//몰 자체상품일 경우 cid = cid
	function CopyOrderCntExtraOptionMall($cid, $center_id, $goodsno, $sourceGoodsNo) {

		$query = "insert into tb_extra_option_order_cnt (
      cid, bid, goodsno, display_name, cnt_rule, regist_flag, regist_date, option_kind_code, unit_cnt_rule, display_cnt_rule, user_cnt_rule_name, user_unit_cnt_rule_name, user_cnt_input_flag)
      select 
        '$cid', '$center_id', '$goodsno', display_name, cnt_rule, regist_flag, now(), option_kind_code, unit_cnt_rule, display_cnt_rule, user_cnt_rule_name, user_unit_cnt_rule_name, user_cnt_input_flag
      from tb_extra_option_order_cnt
      where cid = '$cid' and bid = '$center_id' and regist_flag = 'Y' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	//몰 자체상품일 경우 cid = cid
	function CopyPriceExtraOptionS2Mall($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_price_info (cid, bid, goodsno, option_group_type,option_item,option_price,order_index, regist_date, update_date)
    	select '$cid', '$center_id', '$goodsno', option_group_type,option_item,option_price,order_index, now(), now()
    	from tb_extra_option_price_info
    	where cid = '$cid' and bid = '$center_id' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	//몰 자체상품일 경우 cid = cid
	function CopyUnitPriceExtraOptionMall($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_unit_price (cid, bid, goodsno, display_name, unit_cnt_rule, unit_cnt_price, regist_date)
    	select '$cid', '$center_id', '$goodsno', display_name, unit_cnt_rule, unit_cnt_price, regist_date
    	from tb_extra_option_unit_price
    	where cid = '$cid' and bid = '$center_id' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	###############################################################################
	#####자동견적 책자 프리셋 추가 100112 시즌3 가격 테이블 시즌2 관련 쿼리문들
	function getOptionPriceListS3($cid, $center_id, $goodsno, $option_group_type = '') {
		$sql = "select * from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type' order by order_index asc";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function checkOptionPriceListS3($cid, $center_id, $goodsno, $option_group_type = '') {
		$sql = "select ID from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type' limit 1";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getOptionListMaxDateS3($cid, $center_id, $goodsno, $option_group_type = '') {
		$sql = "select update_date from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type' order by update_date desc limit 1";
		//echo $sql;
		list($max_date) = $this -> db -> fetch($sql, 1);
		return $max_date;
	}

	function CopyPriceExtraOptionS3($cid, $center_id, $goodsno, $sourceGoodsNo) {
		$query = "insert into tb_extra_option_price_info (cid, bid, goodsno, option_group_type,option_item,option_price,order_index, regist_date, update_date)
    	select '$cid', '$center_id', '$goodsno', option_group_type,option_item,option_price,order_index, now(), now()
    	from tb_extra_option_price_info
    	where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo'";
		//debug($query);
		$this -> db -> query($query);
	}

	function getOptionItemInfoS3($cid, $center_id, $goodsno, $optionItem, $addWhere = '') {
		$sql = "select * from tb_extra_option_price_info where cid='$cid' and bid='$center_id' and goodsno=$goodsno and option_item='$optionItem' $addWhere";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function setDeleteAllOptionListS3($cid, $center_id, $goodsno, $option_group_type) {
		$sql = "delete from tb_extra_option_price_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno' and option_group_type='$option_group_type'";
		//echo $sql;
		return $this -> db -> listArray($sql);
	}
	
	function InsertExtraOptionMasterS3($cid, $center_id, $goodsno, $option_kind_index, $option_item_name, $option_item_index, $parent_item_name, $same_price_item_name, $extra_data1, $extra_data2, $option_group_type = '', $display_name = '', $item_price_type = '', $use_flag = 'Y', $preset = '', $kindCode = '') {
	
		//부모 item 이 있을 경우 조건식에 추가한다.
		if ($parent_item_name)
			$addWhere = " and parent_item_name='$parent_item_name'";

		//option_kind_code 이 있을 경우 조건식에 추가한다.
		if ($kindCode)
			$addWhere .= " and option_kind_code='$kindCode'";		

		//같은 값이 가진 item_name이 있을 경우는 추가하지 않는다.
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
        and goodsno=$goodsno and item_name='$option_item_name' and regist_flag = 'Y'" . $addWhere;
		//echo $sql;
		$item_data = $this -> db -> fetch($sql);

		if (!$item_data) {
			$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
          and goodsno=$goodsno  and regist_flag = 'Y' and (same_price_item_name='' or same_price_item_name is null) limit 1";
			//echo $sql;
			$item_data = $this -> db -> fetch($sql);

			if (!$item_data) {
				$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
	          and goodsno=$goodsno  and (same_price_item_name='' or same_price_item_name is null) limit 1";
				//echo $sql;
				$item_data = $this -> db -> fetch($sql);
			}

			$display_flag = 'Y';
			//기존 입력된 옵션이 있을경우 (item 만 추가한 경우)
			if ($item_data) {
				$option_kind_index = $item_data[option_kind_index];
				$option_kind_code = $item_data[option_kind_code];

				$display_name = $item_data[display_name];
				$necessary_flag = $item_data[necessary_flag];
				$have_child = $item_data[have_child];

				$item_price_type = $item_data[item_price_type];
				$option_group_type = $item_data[option_group_type];

				$extra_tbl_kind_code = $item_data[extra_tbl_kind_code];
				$extra_tbl_kind_index = $item_data[extra_tbl_kind_index];

				if ($parent_item_name == "")
					$parent_item_name = $item_data[parent_item_name];
				
				//100112 책자 프리셋용 option_kind_index가  4,9,13,17 일 경우 option_kind_code를 pp1,pp2.. 처럼 index를 부여한다. 책자프리셋처럼...
				if ($preset == "100112") {
					if ($option_kind_index == "4" || $option_kind_index == "9" || $option_kind_index == "13" || $option_kind_index == "17") {
						//option_kind_index count.
						$sql = "select count(*) as cnt from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' and goodsno=$goodsno ";
						//echo $sql;
						$item_count = $this -> db -> fetch($sql);
						if($item_count[cnt] > 0) $option_kind_code = $item_data[option_kind_code]. ($item_count[cnt] + 1);
					}
					else {
						$option_kind_code = $kindCode;
					}
				}

			} else {
				if ($option_group_type == 'AFTEROPTION')
					$necessary_flag = "N";
				else
					$necessary_flag = "Y";

				$have_child = '0';
				$extra_tbl_kind_index = $this -> getMaxExtraOptionMasterTblKindIndex($cid, $center_id, $goodsno) + 1;
				$option_kind_code = 'ETC' . $extra_tbl_kind_index;
				$extra_tbl_kind_code = 'ETC' . $extra_tbl_kind_index;

				$this -> setInsertUseFlag($cid, $center_id, $goodsno, $option_kind_index, $use_flag, $option_group_type);
			}

			$query = "INSERT INTO tb_extra_option_master(
          cid, bid, goodsno, option_kind_code, item_name, 
          parent_item_name, extra_data1, extra_data2, option_kind_index, option_item_index, 
          display_flag, display_name, necessary_flag, have_child, same_price_item_name, 
          item_price_type, option_group_type, extra_tbl_kind_code, extra_tbl_kind_index, regist_flag)
        VALUES(
          '$cid', '$center_id', '$goodsno', '$option_kind_code', '$option_item_name',
          '$parent_item_name','$extra_data1','$extra_data2','$option_kind_index','$option_item_index',
          '$display_flag','$display_name','$necessary_flag','$have_child','$same_price_item_name',
          '$item_price_type','$option_group_type','$extra_tbl_kind_code','$extra_tbl_kind_index', 'Y')";
			//debug($query);
			$this -> db -> query($query);
		}
	}	

	//같은 값이 가진 item_name이 있는 확인한다.
	function getSameItemNameS3($cid, $center_id, $goodsno, $option_kind_index, $option_item_name, $parent_item_name = '', $option_kind_code = '') {
		//부모 item 이 있을 경우 조건식에 추가한다.
		if ($parent_item_name)
			$addWhere = " and parent_item_name='$parent_item_name'";			
		
		//option_kind_code 이 있을 경우 조건식에 추가한다.
		if ($option_kind_code)
			$addWhere .= " and option_kind_code='$option_kind_code'";

		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and option_kind_index='$option_kind_index' 
        and goodsno=$goodsno and item_name='$option_item_name' and regist_flag = 'Y' $addWhere ";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	//옵션 kind_index 순서 맞는 항목(item) 리스트 조회
	function getMaxExtraOptionMasterItemIndexS3($cid, $center_id, $goodsno, $option_kind_index, $parent_item_name = '', $option_kind_code = '') {
		//부모 item 이 있을 경우 조건식에 추가한다.
		if ($parent_item_name)
			$addWhere = " and parent_item_name='$parent_item_name'";
				
		//option_kind_code 이 있을 경우 조건식에 추가한다.
		if ($option_kind_code)
			$addWhere .= " and option_kind_code='$option_kind_code'";

		$sql = "select option_item_index from tb_extra_option_master where cid = '$cid' and bid = '$center_id' and goodsno=$goodsno and option_kind_index=$option_kind_index and regist_flag = 'Y' $addWhere order by option_item_index desc limit 1";
		//echo $sql;
		list($option_item_index) = $this -> db -> fetch($sql, 1);
		if (!$option_item_index)
			$option_item_index = 0;
		return $option_item_index;
	}

	//옵션 item 사용순서 변경
	function setUpdateAdminItemOrderIndexS3($cid, $center_id, $goodsno, $option_kind_index, $option_kind_code, $parent_item_name = '', $item_name, $order_index) {

		//부모 item 이 있을 경우 조건식에 추가한다.
		if ($parent_item_name)
			$addWhere = " and parent_item_name = '$parent_item_name'";

		$query = "
      update tb_extra_option_master set
        option_item_index = '$order_index',
        update_date = now()
      where
        cid = '$cid'
        and bid = '$center_id'
        and goodsno = '$goodsno'
        and option_kind_index = '$option_kind_index'
        and option_kind_code = '$option_kind_code'
        and item_name = '$item_name'
        and regist_flag = 'Y' $addWhere
      ";

		//debug($query);
		$this -> db -> query($query);
	}	
	
	#####자동견적 책자 프리셋 추가 100112 시즌3 가격 테이블 시즌2 관련 쿼리문들
	###############################################################################
	
	function checkOptionItemUpdate($cid, $center_id, $goodsno, $addWhere = '') {
		$sql = "select * from tb_extra_option_master 
		where cid = '$cid' 
		and bid = '$center_id' 
		and goodsno='$goodsno' 
		$addWhere		
		order by update_date desc limit 1";
		
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

    //모던->상품->견적옵션->이미지링크 정보 리스트.
    function getOptImgList($cid, $center_id, $goodsno) {
        $sql = "select * from tb_extra_option_image_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno'";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }
    
    //모던->상품->견적옵션->이미지링크 정보.
    function getOptImg($cid, $center_id, $goodsno, $option_item) {
        $sql = "select * from tb_extra_option_image_info where cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno' and option_item = '$option_item'";
        return $this -> db -> fetch($sql);
    }
    //모던->상품->견적옵션->이미지링크 정보 입력.
    function setInsertOptImg($cid, $center_id, $goodsno, $option_item, $option_img) {
        $sql = "insert into tb_extra_option_image_info set
              cid     = '$cid',
              bid     = '$center_id',
              goodsno = '$goodsno',
              option_item = '$option_item',
              option_img = '$option_img',
              regist_date = now()";
        //debug($sql);    
        $this -> db -> query($sql);
    }
    //모던->상품->견적옵션->이미지링크 정보 수정.
    function setUpdateOptImg($cid, $center_id, $goodsno, $option_item, $option_img) {
        $sql = "update tb_extra_option_image_info set option_img='$option_img', update_date=now() where  cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno' and option_item = '$option_item'";
        $this -> db -> query($sql);
    }
    //모던->상품->견적옵션->이미지링크 정보 삭제.
    function setDeleteOptImg($cid, $center_id, $goodsno, $option_item) {
        $sql = "delete from tb_extra_option_image_info where cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno' and option_item = '$option_item'";
        $this -> db -> query($sql);
    }    
    //모던->상품->견적옵션->이미지링크 정보 전체 삭제.
    function setAllDeleteOptImg($cid, $center_id, $goodsno) {
        $sql = "delete from tb_extra_option_image_info where cid = '$cid' and bid = '$center_id' and goodsno='$goodsno'";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }
    //모던->상품->견적옵션->이미지링크 정보 전체 복사.
    function CopyMasterExtraOptionImg($cid, $center_id, $goodsno, $sourceGoodsNo) {
        $query = "INSERT INTO tb_extra_option_image_info
            (goodsno, cid, bid, option_item, option_img)
            
            select '$goodsno', '$cid', '$center_id', option_item, option_img from tb_extra_option_image_info
            where cid = '$center_id' and bid = '$center_id' and goodsno='$sourceGoodsNo' order by ID asc";
        //debug($query);
        $this -> db -> query($query);
    }
    //모던->상품->견적옵션->제한설정 초기화.
    function setUpdateOptInitLimitFlag($cid, $center_id, $goodsno) {
        $sql = "update tb_extra_option_image_info set limit_flag='0' where  cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno'";
        $this -> db -> query($sql);
    }
    //모던->상품->견적옵션->제한설정 수정.
    function setUpdateOptLimitFlag($cid, $center_id, $goodsno, $option_item, $limit_flag) {
        $sql = "update tb_extra_option_image_info set limit_flag='$limit_flag', update_date=now() where  cid = '$cid' and bid = '$center_id' and goodsno = '$goodsno' and option_item = '$option_item'";
        $this -> db -> query($sql);
    }
        
		
}
?>