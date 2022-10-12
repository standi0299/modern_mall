<?php

/*
* @date : 20181018
* @author : chunter
* @brief : 상품 상세설명 복사를 위한 기능 추가. 몰 자세 설명글을 변경한다. exm_goods_cid 테이블의 desc 필드.
* @request : Pixstory
* @desc : setGoodsDetailUpdateWithGoodsCategory(), setGoodsDetailUpdateWithCategory(),setGoodsDetailUpdateWithGoodsNo()
 * 
* @date : 20180125
* @author : kdk
* @brief : 서브 카테고리 정렬 수정 . getCategoryList() 함수 수정.
* @request : 김기웅 이사
* @desc : orderby 파라메타 추가함.
* @todo : 
*/

/**
 * cart model
 * 2014.04.09 by chunter
 */

class M_goods {
	var $db;
	var $this;
	function M_goods() {
		$this -> db = $GLOBALS[db];
	}

	function getInfo($goodsno) {
		$sql = "select * from exm_goods where goodsno='$goodsno'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getGoodsCidInfo($cid, $goodsno) {
		//$sql = "select mall_pageprice,mall_pagereserve,strprice,goodsno,price,reserve,self_deliv,self_dtype,self_dprice,b2b_goodsno, cimg,clistimg from exm_goods_cid where cid = '$cid' and goodsno = '$goodsno'";
		//$sql = "select * from exm_goods_cid where cid = '$cid' and goodsno = '$goodsno'";
		//echo $sql;
		//return $this->db->fetch($sql);

		if ($cid == "") {//전체검색 / 17.03.13 / kdk
			$sql = "select cid from exm_goods_cid where goodsno='$goodsno'";
			
			return $this -> db -> query($sql);
		} else {
			$sql = "select * from exm_goods_cid where cid = '$cid' and goodsno = '$goodsno'";
			//echo $sql;
			return $this -> db -> fetch($sql);
		}
	}

	//상품정보  조회 list_templateset.php,list_template.php 에서 사용.
	function getGoodsInfo($cid, $goodsno) {
		$sql = "select
		    a.goodsno,
		    a.goodsnm,
		    a.podsno,
		    a.podskind,
		    a.defaultpage,
		    a.minpage,
		    a.maxpage,
		    a.pods_use,
		    a.pods_useid,
		    a.state,
		    a.pods_userdataurl_flag,
		    a.goods_group_code,
		    a.pods_editor_type,
		    (select catno from exm_goods_link where cid='$cid' and goodsno='$goodsno' order by sort limit 1) as catno,
		    if(b.price is null,a.price,b.price) price,  
		    if(b.reserve is null,a.reserve,b.reserve) reserve
		from
		    exm_goods a
		    inner join exm_goods_cid b on a.goodsno = b.goodsno
		where
		    a.goodsno = '$goodsno'
		    and b.cid = '$cid'
		";

		return $this -> db -> fetch($sql);
	}

	//옵션 정보 조회 list_templateset.php,list_template.php 에서 사용.
	function getGoodsOptPriceInfo($cid, $goodsno) {
		$sql = "select
			a.*,
			if(b.aprice is null,a.aprice,b.aprice) aprice,
			areserve
		from
			exm_goods_opt a
			left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
		where
			a.goodsno = '$goodsno'
			and a.opt_view=0
		order by osort		
		";

		//debug($sql);
		//return $this -> db -> listArray($sql);
		return $this -> db -> query($sql);
	}

	//추가 옵션 정보 조회 list_templateset.php,list_template.php 에서 사용.
	function getGoodsAddOptPriceInfo($goodsno) {
		$sql = "select * from exm_goods_addopt_bundle 
        where
        goodsno = '$goodsno'
        and addopt_bundle_view = 0
        order by addopt_bundle_sort";

		//debug($sql);
		//return $this -> db -> listArray($sql);
		return $this -> db -> query($sql);
	}

	//추가 옵션 정보 조회 list_templateset.php,list_template.php 에서 사용.
	function getGoodsAddOptPriceInfoList($cid, $addopt_bundle_no) {
		$sql = "select
          a.*,
          if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
          addopt_areserve
        from
          exm_goods_addopt a
          left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
        where
          a.addopt_bundle_no = '$addopt_bundle_no'
          and addopt_view = 0
        order by addopt_sort";

		//debug($sql);
		//return $this -> db -> listArray($sql);
		return $this -> db -> query($sql);
	}

	//인화 옵션 가격 정보. view_defaul.php에서 사용.
	function getGoodsPrintOptInfoList($cid, $goodsno) {
		$sql = "select
                a.*,
                if(b.print_price is null,a.print_price,b.print_price) print_price,
                print_reserve,
                a.print_price print_cprice
            from
                exm_goods_printopt a
                left join exm_goods_printopt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.printoptnm = b.printoptnm
            where
                a.goodsno = '$goodsno'
            order by osort";

		//debug($sql);
		//return $this -> db -> listArray($sql);
		return $this -> db -> query($sql);
	}

	//회원 그룹별  할인 ,적립 가격  학인. view_defaul.php에서 사용.
	function getGroupDisSaveInfo($grpno) {
		return $this -> db -> fetch("select grpnm,grpdc,grpsc from exm_member_grp where grpno = '$grpno'", 1);
	}

	//상점별 옵션 노출여부  list_templateset.php,list_template.php 에서 사용.
	function getGoodsOptMallView($cid, $goodsno, $optno) {
		$sql = "select view from tb_goods_opt_mall_view where cid = '$cid' and goodsno = '$goodsno' and optno = '$optno'";
		return $this -> db -> fetch($sql, 1);
	}

	//카테고리번호 조회 view.php에서 사용.
	function getCatno($cid, $goodsno) {
		$sql = "select catno from exm_goods_link where cid = '$cid' and goodsno = '$goodsno' limit 1";
		//debug($sql);
		return $this -> db -> fetch($sql, 1);
	}

	//카테고리명 조회 view.php에서 사용.
	function getCatnm($cid, $catno) {
		$sql = "select catnm from exm_category where cid='$cid' and catno='$catno' limit 1";
		//debug($sql);
		return $this -> db -> fetch($sql, 1);
	}

	//상품상세정보 조회 view.php에서 사용.
	function getViewInfo($cid, $goodsno, $bid_where) {
		$sql = "select
			a.*, b.*,if(b.price is null,a.price,b.price) price,
			if(b.`desc` ='',a.`desc`,b.`desc`) `desc`,
			if(b.mall_pageprice is null,a.pageprice,b.mall_pageprice) pageprice,
         a.goods_desc as center_goods_desc, b.goods_desc as mall_goods_desc
		from
			exm_goods a
			inner join exm_goods_cid b on cid = '$cid' and a.goodsno = b.goodsno
			left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
		where
			a.goodsno = '$goodsno'
			and $bid_where
		";
		return $this -> db -> fetch($sql);
	}

	//그룹별 제작옵션(임포지션옵션) 정보 조회 view.php에서 사용.
	function getUseImpoGroupInfoList($goodsno) {
		$sql = "select * from tb_use_imposition_option_group where goodsno = '$$goodsno' order by opt_group_sort";

		//debug($sql);
		//return $this -> db -> listArray($sql);
		return $this -> db -> query($sql);
	}

	//제작옵션(임포지션옵션) 정보 조회 view.php에서 사용.
	function getUseImpoInfoList($opt_group_no) {
		$sql = "select * from tb_use_imposition_option where opt_group_no = '$opt_group_no' order by opt_sort";

		//debug($sql);
		//return $this -> db -> listArray($sql);
		return $this -> db -> query($sql);
	}

	//그룹별 옵션 노출여부  view.php에서 사용.
	function getUseImpoGroupMallView($cid, $goodsno, $opt_group_no) {
		$sql = "select view from tb_use_imposition_option_group_mall_view where cid = '$cid' and goodsno = '$goodsno' and opt_group_no = '$opt_group_no'";
		return $this -> db -> fetch($sql, 1);
	}

	//상점별 옵션 노출여부  view.php에서 사용.
	function getUseImpoMallView($cid, $goodsno, $optno) {
		$sql = "select view from tb_use_imposition_option_mall_view where cid = '$cid' and goodsno = '$goodsno' and optno = '$optno'";
		return $this -> db -> fetch($sql, 1);
	}

	//메인 블럭 정보 index.php에서 사용.
	function getMainBlockList($cid) {
		$sql = "select * from md_main_block where cid='$cid' order by order_by asc";
		#debug($sql);
		return $this -> db -> listArray($sql);
		
	}

	//메인 블럭 컨텐츠 정보 index.php에서 사용.
	function getMainBlockContentList($cid) {
		$sql = "select * from md_main_content where cid='$cid'";
		#debug($sql);
		return $this -> db -> fetch($sql);
	}

	//메인 블럭별 상품 정보 index.php에서 사용.
	function getMainBlockGoodsList($cid, $dpno, $orderby) {
		switch ($orderby) {
			case '1':
				$limit = " limit 3";
				break;

			case '3':
				$limit = " limit 12";
				break;
				
			case '4':
				$limit = " limit 5";
				break;

			case '5':
				$limit = " limit 4";
				break;
				
			case '6':
				$limit = " limit 1";
				break;

			case '7':
				$limit = " limit 16";
				break;
				
			default:
				$limit = "";
				break;
		}	
		$sql = "select * from exm_dp_link where cid='$cid' and dpno='$dpno' order by seq $limit";
		return $this -> db -> listArray($sql);
	}
	
	//db 부하를 줄이기 위해 목록과 정보를 같이 가져온다.			20171122		chunter
	function getMainBlockGoodsListWithGoodsInfo($cid, $dpno, $orderby, $mid = '', $bid = '') {
		//iscrean 예외 처리	
		if($cid == 'iscream' && $orderby =='1'){
			$orderby = '0';
		}
		
		switch ($orderby) {
			case '0':
				$limit = "";
				break;
				
			case '1':
				$limit = " limit 3";
				break;
				
			case '3':
				$limit = " limit 12";
				break;

			case '4':
				$limit = " limit 5";
				break;

			case '5':
				$limit = " limit 4";
				break;

			case '6':
				$limit = " limit 1";
				break;

			case '7':
				$limit = " limit 16";
				break;

			case '11':
				$limit = " limit 12";
				break;

			default:
				$limit = "";
				break;
		}	
		$sql = "select * from exm_dp_link where cid='$cid' and dpno='$dpno' order by seq $limit";
		#debug($sql);
		if ($mid)
    	$field = "a.*,b.*,if(b.price is null,a.price,b.price) price,if(b.goods_desc is null,a.goods_desc,b.goods_desc) goods_desc, wgood.no wishlist_no, c.goods_like";
   	else
    	$field = "a.*,b.*,if(b.price is null,a.price,b.price) price,if(b.goods_desc is null,a.goods_desc,b.goods_desc) goods_desc";
		
		$db_table = "exm_dp_link dp_link
		inner join exm_goods a on a.goodsno = dp_link.goodsno
		inner join exm_goods_cid b on b.cid = '$cid' and b.goodsno = a.goodsno
		left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
		left join md_goods_like c on a.goodsno = c.goodsno and c.cid = '$cid' and c.mid = '{$mid}'
		";

   	//wish 등록 상품 조인하기.			20170922		chunter
    if ($mid)
    	$db_table .= " left join md_wish_list wgood on wgood.cid = '$cid' and wgood.mid='{$mid}' and wgood.goodsno = a.goodsno";
		
		$where = ($bid) ? " and (bid.bid is null or bid.bid = '$bid')" : " and bid.bid is null";		
		$sql = "select $field from $db_table where dp_link.cid='$cid' and dp_link.dpno='$dpno' $where order by dp_link.seq $limit";
		//debug($sql);
		return $this -> db -> listArray($sql);
	}
	
	function getGoodsOptInfo($goodsno, $optno) {
		$sql = "select * from exm_goods_opt where goodsno = '$goodsno' and optno = '$optno'";
		return $this -> db -> fetch($sql);
	}

	function setGoodsStockUpdate($goodsno, $stock) {
		$sql = "update exm_goods set totstock = '$stock' where goodsno = '$goodsno'";
		$this -> db -> query($sql);
	}

	function setGoodsOptStockUpdate($goodsno, $optno, $stock) {
		$sql = "update exm_goods_opt set stock = '$stock' where goodsno = '$goodsno' and optno = '$optno'";
		$this -> db -> query($sql);
	}

	function getGoodsBidInfo($cid, $bid, $goodsno) {
		if ($bid)
			$addWhere = "and bid = '{$bid}'";
		$sql = "select goodsno from exm_goods_bid where cid = '$cid'  $addWhere and goodsno = '$goodsno' limit 1";
		//echo $sql;
		list($bid_chk) = $this -> db -> fetch($sql, 1);

		return $bid_chk;
	}

	function getGoodsPriceWithOpt($cid, $goodsno, $optno) {
		$sql = "select a.*,
                  if(b.aprice is null,a.aprice,b.aprice) aprice,
                  areserve,
                  asprice,
                  b.b2b_optno b2b_goodsno
              from
                  exm_goods_opt a
                  left join exm_goods_opt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.optno = b.optno
              where
                  a.goodsno = '$goodsno'
                  and a.optno = '$optno'
              ";

		return $this -> db -> fetch($sql);
	}

	function getGoodsPrintOpt($cid, $goodsno, $printoptnm) {
		$sql = "select
                if(b.print_price is null,a.print_price,b.print_price) print_price,
                print_reserve,
                print_sprice,
                print_oprice
            from
                exm_goods_printopt a
                left join exm_goods_printopt_price b on b.cid = '$cid' and a.goodsno = b.goodsno and a.printoptnm = b.printoptnm
            where
                a.goodsno = '$goodsno'
                and a.printoptnm = '$printoptnm'";

		return $this -> db -> fetch($sql);
	}

	//추가 옵션 정보
	function getGoodsAddOpt($cid, $addoptionno) {
		$sql = "select
          a.*,
          if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
          addopt_areserve,
          addopt_asprice,
          addopt_aoprice,
          c.*
      from
          exm_goods_addopt a
          left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
          inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
      where
          a.addoptno = '$addoptionno'
      ";

		return $this -> db -> fetch($sql);
	}

	//상품 가격 정보 관련 쿼리들     20150211
	function getGoodsSalePrice($cid, $goodsno) {
		$sql = "select if(c.price is null,if(b.price is null,a.price,b.price),c.price) as price
               from exm_goods as a
               inner join exm_goods_cid as b on b.cid = '$cid' and b.goodsno = a.goodsno
               left join exm_price as c on c.cid = '$cid' and c.bid = '$bid' and c.mode = 'goods' and c.goodsno = a.goodsno
              where
               a.goodsno = '$goodsno'";
		return $this -> db -> fetch($sql);
	}

	function getGoodsOptSalePrice($cid, $bid, $goodsno, $optno) {
		$sql = "select if(c.price is null,if(b.aprice is null,a.aprice,b.aprice),c.price) as price
        from
            exm_goods_opt as a
            left join exm_goods_opt_price as b on b.cid = '$cid' and b.goodsno = a.goodsno and b.optno = a.optno
            left join exm_price as c on c.cid = '$cid' and c.bid = '$bid' and c.mode = 'opt' and c.goodsno = a.goodsno and c.optno = a.optno
        where
            a.goodsno = '$goodsno'
            and a.optno = '$optno'";

		return $this -> db -> fetch($sql);
	}

	function getGoodsAddOptSalePrice($cid, $bid, $goodsno, $addoptno) {
		$sql = "select
                if(c.price is null,if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice),c.price) as price
            from
                exm_goods_addopt as a
                left join exm_goods_addopt_price as b on b.cid = '$cid' and b.goodsno = a.goodsno and b.addoptno = a.addoptno
                left join exm_price as c on c.cid = '$cid' and c.bid = '$bid' and c.mode = 'addopt' and c.goodsno = a.goodsno and c.addoptno = a.addoptno
            where
                a.goodsno = '$goodsno'
                and a.addoptno = '$addoptno'";

		return $this -> db -> fetch($sql);
	}

    function getGoodsAddOptNoList($addopt_bundle_no) {
        $sql = "select * from exm_goods_addopt where addopt_bundle_no = '$addopt_bundle_no' order by addopt_sort";

        return $this -> db -> listArray($sql);
    }	
	
	function getGoodsAddOptList($goodsno) {
		$sql = "select * from exm_goods_addopt_bundle 
        where
        goodsno = '$goodsno'
        and addopt_bundle_view = 0
        order by addopt_bundle_sort";

		return $this -> db -> listArray($sql);
	}

	function getGoodsAddOptPriceList($cid, $addopt_bundle_no) {
		$sql = "  select
          a.*,
          if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
          addopt_areserve
        from
          exm_goods_addopt a
          left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
        where
          a.addopt_bundle_no = '$addopt_bundle_no'
          and addopt_view = 0
        order by addopt_sort";

		return $this -> db -> listArray($sql);
	}

	//상점별 추가 옵션 정보
	function getGoodsAddOptViewMall($sql) {
		//debug($sql);
		return $this -> db -> fetch($sql, 1);
	}

	//20150211  chunter
	function getGoodsPriceInfo($cid, $bid, $goodsno) {
		$sql = "select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'goods' and goodsno = '$goodsno'";
		return $this -> db -> fetch($sql);
	}

	function getAddPagePriceInfo($cid, $bid, $goodsno) {
		$sql = "select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'page' and goodsno = '$goodsno'";
		return $this -> db -> fetch($sql);
	}

	function getOptPriceInfo($cid, $bid, $goodsno, $optno) {
		$sql = "select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'opt' and goodsno = '$goodsno' and optno = '$optno'";
		return $this -> db -> fetch($sql);
	}

	function getAddOptPriceInfo($cid, $bid, $goodsno, $addoptno) {
		$sql = "select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'addopt' and goodsno = '$goodsno' and addoptno = '$addoptno'";
		return $this -> db -> fetch($sql);
	}

	function getPrintOptPriceInfo($cid, $bid, $goodsno, $printoptnm) {
		$sql = "select * from exm_price where cid = '$cid' and bid = '$bid' and mode = 'printopt' and goodsno = '$goodsno' and printoptnm = '$printoptnm'";
		return $this -> db -> fetch($sql);
	}

	//20150211  chunter
	function getReleaseList($cid, $orderby) {
		$sql = "select * from exm_release where cid = '$cid' and hide = 0 order by cid,trim($orderby)";
		return $this -> db -> listArray($sql);
	}

	function getReleaseInfo($rid) {
		$sql = "select * from exm_release where rid = '$rid'";
		return $this -> db -> fetch($sql);
	}

	function getReleaseListWithCid($cid, $orderby, $is_cid) {
		if ($is_cid)
			$where = " and cid = '$cid'";
		else
			$where = " and (cid = '' or cid = '$cid')";

		$sql = "select * from exm_release where hide = 0 $where order by cid,trim($orderby)";
		return $this -> db -> listArray($sql);
	}

	function getGoodsBidList($cid, $goodsno) {
		$sql = "select a.bid,b.business_name from exm_goods_bid a inner join exm_business b on b.cid = a.cid and b.bid = a.bid where a.cid = '$cid' and a.goodsno = '$goodsno'";
		return $this -> db -> listArray($sql);
	}

	//20150211
	function getCategoryInfo($cid, $catno) {
		$sql = "select * from exm_category where cid = '$cid' and catno = '$catno'";
        //debug($sql);
		return $this -> db -> fetch($sql);
	}

	function getCategoryList($cid, $hidden_flag = '', $orderby = '') {
		if ($hidden_flag != '')
			$addWhere = " and hidden = '$hidden_flag' ";
        
        $order_by = "order by catno, sort asc";
        if ($orderby == 'sort')
            $order_by = "order by length(catno), sort asc";
        
		$sql = "select * from exm_category where cid = '$cid' $addWhere $order_by";
        //debug($sql);
		return $this -> db -> listArray($sql);
	}

	//템플릿 분류 / 2017.09.21 / kdk
    //최상위 카테고리
    function getTopTemplateCategoryList($cid) {
        $sql = "select catno,catnm from md_template_category where cid='$cid' and char_length(catno)=3 order by catno";
        return $this -> db -> listArray($sql);
    }
    	
	function getTemplateCategoryList($cid, $hidden_flag = '') {
		if ($hidden_flag != '')
			$addWhere = " and hidden = '$hidden_flag' ";
		$sql = "select * from md_template_category where cid = '$cid' order by catno, sort asc";
		//debug($sql);
		return $this -> db -> listArray($sql);
	}

	function getTemplateCategoryLinkNo($cid, $goodsno) {
		$sql = "select *, (select catnm from md_template_category where cid='$cid' and catno=md_template_category_link.catno) as catnm from md_template_category_link where cid = '$cid' and goodsno = '$goodsno'";
		return $this -> db -> fetch($sql);
	}

	function setTemplateCategoryLinkInsert($cid, $catno, $goodsno) {
		$sql = "insert into md_template_category_link set
              cid     = '$cid',
              catno   = '$catno',
              goodsno = '$goodsno'
              on duplicate key update goodsno = goodsno";
		//debug($sql);	  
		$this -> db -> query($sql);
	}

	function setTemplateCategoryLinkDelete($cid, $goodsno) {
		$sql = "delete from md_template_category_link where cid = '$cid' and goodsno = '$goodsno'";
		$this -> db -> query($sql);
	}
	//템플릿 분류

	function getCategoryLinkList($cid, $goodsno, $orderBy = '') {
		if ($orderBy)
			$orderBy = " order by $orderBy";
		$sql = "select * from exm_goods_link where cid = '$cid' and goodsno = '$goodsno' $orderBy";
		return $this -> db -> listArray($sql);
	}
	
	function getLinkCategoryDataList($cid, $goodsno) {
		$sql = "select a.* from exm_category a, exm_goods_link b where a.catno = b.catno and a.cid='$cid' and b.cid = '$cid' and b.goodsno = '$goodsno' order by cat_index asc";
		return $this->db->listArray($sql);
	}

	function setCategoryLinkInsert($cid, $in_catno, $goodsno, $sort) {
		$sql = "insert into exm_category_link set
              cid     = '$cid',
              catno   = '$in_catno',
              goodsno = '$goodsno',
              sort    = '$sort'
              on duplicate key update goodsno = goodsno";
		$this -> db -> query($sql);
	}

	function setCategoryLinkDelete($cid, $goodsno, $category_nos) {
		$sql = "delete from exm_category_link where cid = '$cid' and goodsno = '$goodsno' and catno not in ($category_nos)";
		$this -> db -> query($sql);
	}

    function setGoodsCategoryLinkInsert($cid, $goodsNo, $catNo, $catIndex)
    {
        $sql = "insert into exm_goods_link set
                cid = '$cid',
                goodsno = '$goodsNo',
                catno = '$catNo',
                sort = -unix_timestamp(),
                cat_index = $catIndex
                on duplicate key update
                catno = '$catNo'";
        $this->db->query($sql);
    }
    
    function setGoodsCategoryLinkDelete($cid, $goodsNo, $catIndex = '')
    {
        if ($catIndex)
            $sql = "delete from exm_goods_link where cid = '$cid' and goodsno = '$goodsNo' and cat_index = $catIndex";
        else
            $sql = "delete from exm_goods_link where cid = '$cid' and goodsno = '$goodsNo'";
        $this->db->query($sql);
    }

	//몰별 포인트 설정값 불러오기.      20150625    chunter
	function getPrettyPointInfoWithCid($goodsno, $cid) {
		$sql = "select * from tb_goods_cid_pretty_point where goodsno='$goodsno' and cid='$cid'";
		//echo $sql;
		return $this -> db -> fetch($sql);
	}

	function getPrettyMemberGoodsMappingInfo($cid, $mid, $goodsno) {
		$sql = "select goods_each_price from tb_member_goods_mapping where cid = '$cid' and mid = '$mid' and goodsno = '$goodsno'";
		//echo $sql;
		return $this -> db -> fetch($sql, 1);
	}

	//진열 상품 검색 / 16.08.03 / kdk
	function getDpLinkList($cid, $dpno) {
		$sql = "select * from exm_dp_link where cid = '$cid' and dpno = '$dpno'";
		//echo $sql;
		//return $this -> db -> listArray($sql);
		return $this -> db -> fetch($sql);
	}

	//진열 템플릿 검색 / 16.08.03 / kdk
	function getTemplateDpLinkList($cid, $dpno) {
		$sql = "select * from tb_template_dp_link where cid = '$cid' and dpno = '$dpno'";
		//echo $sql;
		//return $this -> db -> listArray($sql);
		return $this -> db -> fetch($sql);
	}

	//P관리자 견적상품 후가공 정보 조회 / 16.09.02 / kdk
	function getExtraOptionInfo($cid, $bid, $goodsno) {
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$bid' and goodsno='$goodsno' and option_group_type != 'AFTEROPTION' and regist_flag = 'Y' order by option_kind_index, option_item_index asc";

		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	function getExtraAfterOptionInfo($cid, $bid, $goodsno) {
		$sql = "select * from tb_extra_option_master where cid = '$cid' and bid = '$bid' and goodsno='$goodsno' and option_group_type = 'AFTEROPTION' and regist_flag = 'Y' order by option_kind_index, option_item_index asc";

		//echo $sql;
		return $this -> db -> listArray($sql);
	}

	//P관리자 옵션 사용여부 리스트 조회 / 16.08.05 / kdk
	function getExtraOptionUseList($cid, $bid, $goodsno) {
		$result = array();
		$sql = "select * from tb_extra_option_master_use where cid = '$cid' and bid = '$bid' and goodsno='$goodsno' order by option_kind_index asc";
		//echo $sql;
		$res = $this -> db -> listArray($sql);

		foreach ($res as $key => $data) {
			$result[$data[option_kind_index]] = $data[use_flag];
		}

		return $result;
	}

	//P관리자 견적상품 후가공 옵션 도움말이미지 정보 조회 / 16.08.11 / kdk
	function getExtraAfterOptionHelpInfo($cid, $goodsno, $code) {
		$sql = "select * from tb_extra_option_after_help where cid = '$cid' and goodsno = '$goodsno' and option_kind_code = '$code'";
		//echo $sql;
		//return $this -> db -> listArray($sql);
		return $this -> db -> fetch($sql);
	}

	function getAdminGoodsList($cid, $addWhere = '', $orderby = '', $limit = '', $sort = '', $mid = '') {
		if ($addWhere)
			$addWhere = "where " . $addWhere;
		if (!$orderby)
			$orderby = "order by a.goodsno desc";
		
		//회원 여부에 따라 wish 상품 조인을 처리한다.			20170922		chunter
		if ($mid)
			$query = "select a.*,b.price,b.nodp,if(b.price is null,a.price,b.price) price, b.reserve, b.clistimg, c.catno, b.goodsnm_deco, b.mall_cprice, b.icon_filename, d.priority, wgood.no wishlist_no";
		else
			$query = "select a.*,b.price,b.nodp,if(b.price is null,a.price,b.price) price, b.reserve, b.clistimg, b.csummary, c.catno, b.goodsnm_deco, b.mall_cprice, b.icon_filename, d.priority";
		 
		$query .= " from exm_goods a 
      inner join exm_goods_cid b on b.cid = '$cid' and a.goodsno=b.goodsno 
      left join exm_goods_link c on c.cid = '$cid' and a.goodsno=c.goodsno
      left join md_goods_sort d on d.cid = '$cid' and d.goodsno = a.goodsno and d.sort = '$sort'";
			
		if ($mid)
			$query .= " left join md_wish_list wgood on wgood.cid = '$cid' and wgood.mid='$mid' and wgood.goodsno = a.goodsno";
		
		$query .= " $addWhere group by a.goodsno $orderby $limit";
		//echo $query;
		return $this -> db -> listArray($query);
	}

	//관리자 메인화면 상품 설정리스트.
	function getAdminMainBlockGoodsList($cid, $addWhere = '', $orderby = '', $limit = '') {
		if (!$orderby)
			$orderby = "order by a.seq asc";
		$query = "select 
			a.dpno,a.seq,
			b.price,b.nodp,if(b.price is null,c.price,b.price) price, b.reserve, b.goodsnm_deco, b.mall_cprice,
			c.*,
			d.catno
		from exm_dp_link a 
			inner join exm_goods_cid b on b.cid='$cid' and a.goodsno=b.goodsno
			inner join exm_goods c on c.goodsno=a.goodsno
			left join exm_goods_link d on d.cid='$cid' and d.goodsno=a.goodsno
			$addWhere
			group by a.goodsno $orderby $limit";
		//debug($query);
		return $this -> db -> listArray($query);
	}

	function getAdminSelfGoodsList($cid, $addWhere = '', $orderby = '', $limit = '') {
		if (!$orderby)
			$orderby = "order by a.goodsno desc";
		$query = "select a.*, b.cid, b.price as mall_price, b.mall_cprice, b.cimg, b.clistimg from 
                  exm_goods a
                  left join exm_goods_cid b on b.goodsno = a.goodsno and b.cid = '$cid'
                where reg_cid = '$cid' and privatecid = '$cid' $addWhere
                group by a.goodsno $orderby $limit";

		return $this -> db -> listArray($query);
	}

	function getAdminCenterGoodsConnectList($cid, $addWhere = '', $orderby = '', $limit = '') {
		if ($addWhere)
			$addWhere = $addWhere;
		if (!$orderby)
			$orderby = "order by a.goodsno desc";
		$query = "select a.*, b.cid from 
                  exm_goods a 
                  left join exm_goods_cid b on b.goodsno = a.goodsno and b.cid = '$cid'
                  where b.cid is null and (privatecid like '%|$cid|%' or privatecid = '' or privatecid like '$cid|%' or privatecid like '%|$cid' or privatecid='$cid')
                  $addWhere
                  $orderby $limit";
        //debug($query);
		return $this -> db -> listArray($query);
	}

    function getAdminCenterGoodsList($cid, $addWhere = '', $orderby = '', $limit = '') {
        if ($addWhere)
            $addWhere = $addWhere;
        if (!$orderby)
            $orderby = "order by a.goodsno desc";
        $query = "select a.*, b.cid from 
                  exm_goods a 
                  left join exm_goods_cid b on b.goodsno = a.goodsno and b.cid = '$cid'
                  where b.cid is null 
                  #and (privatecid like '%|$cid|%' or privatecid = '' or privatecid like '$cid|%' or privatecid like '%|$cid' or privatecid='$cid')
                  $addWhere
                  $orderby $limit";
        //debug($query);
        return $this -> db -> listArray($query);
    }	
	
	//자동견적옵션 복사를 위한 source용 상품정보 조회 2016.04.05 by kdk.
	function getCopyGoodsExtraOption($extra_option, $goodsno) {
		$sql = "select goodsno, goodsnm from exm_goods where extra_option = '$extra_option' and goodsno !='$goodsno'";
		//echo $sql;
		return $this -> db -> query($sql);
	}

	//최상위 카테고리
	function getTopCategoryList($cid) {
		$sql = "select catno,catnm from exm_category where cid='$cid' and char_length(catno)=3 order by catno";
		return $this -> db -> listArray($sql);
	}
	
	//추천상품,연관상품,...
	function getAddtionGoodsItem($cid, $addWhere='') {
		$sql = "select * from tb_goods_addtion_item $addWhere";
		//echo $sql;
		return $this->db->fetch($sql);
	}
	
	//추천상품,연관상품,... 수정
	function setAddtionGoodsItem($cid, $addtion_key_kind, $addtion_key, $addtion_goods_kind, $addtion_goodsno, $regist_flag) {
		$addWhere = "where cid='$cid' and addtion_key_kind='$addtion_key_kind' and addtion_key='$addtion_key' and addtion_goods_kind='$addtion_goods_kind'";
		$data = $this->getAddtionGoodsItem($cid, $addWhere);
		
		if ($data) {
			$sql = "update tb_goods_addtion_item set
					addtion_goodsno='$addtion_goodsno',
					regist_flag='$regist_flag' 
					$addWhere";
		} else {
			$sql = "insert into tb_goods_addtion_item set 
					cid='$cid',
					addtion_key_kind='$addtion_key_kind',
					addtion_key='$addtion_key',
					addtion_goods_kind='$addtion_goods_kind',
					addtion_goodsno='$addtion_goodsno',
					regist_flag='$regist_flag',
					regist_date=now()";
		}
		$this->db->query($sql);
	}
	
	//추천상품,연관상품,... 삭제
	function delAddtionGoodsItem($cid, $addtion_key_kind, $addtion_key) {
		if ($addtion_key_kind == "I") {
			$sql = "delete from tb_goods_addtion_item where cid='$cid' and addtion_key_kind='$addtion_key_kind' and addtion_key='$addtion_key'";
		} else if ($addtion_key_kind == "C") {
			$sql = "delete from tb_goods_addtion_item where cid='$cid' and addtion_key_kind='$addtion_key_kind' and addtion_key like '$addtion_key%'";
		}
		$this->db->query($sql);
	}
	
	//상품에 매핑된 아이콘 수정
	function setGoodsIcon($cid, $icon_filename) {
		$sql = "update exm_goods 
				set icon_filename=replace(replace(replace(icon_filename,'$icon_filename||',''),'||$icon_filename',''),'$icon_filename','') 
				where reg_cid='$cid' and icon_filename like '%$icon_filename%'";
		$this->db->query($sql);
		
		$sql2 = "update exm_goods_cid 
				 set icon_filename=replace(replace(replace(icon_filename,'$icon_filename||',''),'||$icon_filename',''),'$icon_filename','') 
				 where cid='$cid' and icon_filename like '%$icon_filename%'";
		$this->db->query($sql2);
	}
	
	//상품에 매핑된 카테고리 조회
	function getGoodsCategoryInfo($cid, $goodsno) {
		$sql = "select catno,catnm from exm_category where cid='$cid' and catno in (select catno from exm_category_link where cid='$cid' and goodsno='$goodsno')";
		return $this->db->listArray($sql);
	}
	
	//쿠폰적용대상 상품 조회
	function getCouponGoodsInfo($cid, $goodsno) {
		$sql = "select *,if(b.price is null,a.price,b.price) price 
			from exm_goods a 
			inner join exm_goods_cid b on a.goodsno = b.goodsno 
			where b.nodp = 0 and b.cid = '$cid' and a.goodsno = '$goodsno'";
		return $this->db->fetch($sql);
	}
	
	//이벤트 연결상품 조회
	function getEventGoodsInfo($cid, $eventno) {
		$sql = "select * from exm_goods a 
			inner join exm_goods_cid b on a.goodsno = b.goodsno 
			left join exm_event_link c on b.goodsno = c.goodsno 
			where b.cid	= '$cid' and c.eventno = '$eventno'
			order by c.grpno, c.sort";
		return $this->db->listArray($sql);
	}
	
	//이벤트 연결상품 검색
	function getEventGoodsSearch($cid, $tableName, $addColumn='', $addWhere='', $limit, $pagenum) {
		$sql = "select $addColumn from $tableName where $addWhere order by a.goodsno desc limit $limit,$pagenum";
		return $this->db->listArray($sql);
	}

	//찜(wishlist)리스트 상품 체크
	function getCheckWishListGoods($cid, $mid, $goodsno) {
		$sql = "select * from md_wish_list where cid = '$cid' and mid = '$mid' and goodsno = '$goodsno'";
		//debug($sql);
		return $this->db->fetch($sql);
	}
	
	function chkDuplicateCategoryLink($cid, $goodsno, $catIndex){
      $sql = "select goodsno from exm_goods_link where cid = '$cid' and goodsno = '$goodsno' and cat_index = '$catIndex'";
      list($chk) = $this->db->fetch($sql, 1);
      return $chk;
   }
   
   function setGoodsCategoryLinkUpdate($cid, $goodsno, $catno, $catIndex){
      $sql = "update exm_goods_link set catno = '$catno' where cid = '$cid' and goodsno = '$goodsno' and cat_index = '$catIndex'";
      $this->db->query($sql);
   }

	//중복제거
	function chkDuplicateCategoryLink2($cid, $goodsno, $catIndex){
      $sql = "select goodsno from exm_goods_link where cid = '$cid' and goodsno = '$goodsno' and cat_index = '$catIndex'";
  	  return $this->db->listArray($sql);
	}

   //상품 옵션 이미지 / 2017.10.10 / kdk
   function getOptionImgList($cid, $goodsno) {
      $sql = "select * from mb_option_image_info where cid = '$cid' and goodsno = '$goodsno'";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   function getOptionImg($cid, $goodsno, $option_item) {
      $sql = "select * from mb_option_image_info where cid = '$cid' and goodsno = '$goodsno' and option_item = '$option_item'";
      return $this -> db -> fetch($sql);
   }

   function setOptionImgInsert($cid, $goodsno, $option_item, $option_img) {
      $sql = "insert into mb_option_image_info set
               cid     = '$cid',
               goodsno = '$goodsno',
               option_item = '$option_item',
               option_img = '$option_img',
               regist_date = now()";
      //debug($sql);
      $this -> db -> query($sql);
   }

    function setOptionImgUpdate($cid, $goodsno, $option_item, $option_img) {
        $sql = "update mb_option_image_info set option_img='$option_img', update_date=now() where  cid = '$cid' and goodsno = '$goodsno' and option_item = '$option_item'";
        $this -> db -> query($sql);
    }

    function setOptionImgDelete($cid, $goodsno, $option_item) {
        $sql = "delete from mb_option_image_info where cid = '$cid' and goodsno = '$goodsno' and option_item = '$option_item'";
        $this -> db -> query($sql);
    }
    //상품 옵션 이미지 / 2017.10.10 / kdk

    //패키지 상품 관련 리스트 조건이 기존 쿼리들과 조금 틀려 새로 추가함.(센터용) / 2017.12.11 / kdk
    function getCenterAdminGoodsList($cid, $addWhere = '', $orderby = '', $limit = '', $sort = '') {
        if ($addWhere)
            $addWhere = "where " . $addWhere;
        if (!$orderby)
            $orderby = "order by a.goodsno desc";
        
        $query = "select a.*,c.catno,c.sort,c.cat_index,d.priority from exm_goods a 
            left join exm_goods_link c on c.cid = '$cid' and a.goodsno=c.goodsno
            left join md_goods_sort d on d.cid = '$cid' and d.goodsno = a.goodsno and d.sort = '$sort'";

        $query .= " $addWhere group by a.goodsno $orderby $limit";
        //echo $query;
        return $this -> db -> listArray($query);
    }

    function getCoverRangeOption($goodsno, $orderby = ''){
       if($orderby) $order_by = "order by $orderby";

       $cover_range_option = $this->db->listArray("select * from md_cover_range_option where goodsno = '$goodsno' $order_by");
       return $cover_range_option;
    }

    function getCoverRangeStandard($goodsno){
       $cover_range_standard = $this->db->listArray("select * from md_cover_range_standard where goodsno = '$goodsno'");
       return $cover_range_standard;
    }

    function getCoverRangeStandardData($cover_id){
       $data = $this->db->fetch("select * from md_cover_range_standard where cover_id = '$cover_id'");
       return $data;
    }
    
    function getCoverRangeDataWithCoverID($cover_id){
       $cover_range_option = $this->db->fetch("select * from md_cover_range_option where cover_id = '$cover_id'");
       return $cover_range_option;
    }
    
    //상품 우선 순위 조회
    function getMdGoodsSortList($cid, $catno, $sort) {
        $sql = "select * from md_goods_sort where cid = '$cid' and catno = '$catno' and sort = '$sort'";
        //debug($sql);
        return $this -> db -> listArray($sql);
    }

   function get_goods_like_cnt($cid, $goodsno){
       list($cnt) = $this->db->fetch("select count(goods_like) from md_goods_like where cid = '$cid' and goodsno = '$goodsno' and goods_like = 'Y'",1);
       return $cnt;
   }
   
   function get_editor_ext_data($storageid){
      $ext_data = $this->db->fetch("select * from tb_editor_ext_data where storage_id = '$storageid'");
      $ext_json_data = json_decode($ext_data[editor_return_json],1);
      
      return $ext_json_data;
   }
   
   function get_goods_poke_list($cid, $mid){
      $poke_goods_cnt = $this->db->listArray("select * from exm_goods a
            inner join md_goods_like b on a.goodsno = b.goodsno
          where b.cid = '$cid' and b.mid = '$mid' and b.goods_like = 'Y'");
          
      return $poke_goods_cnt;
   }
   
   function getOptDataWithOsort($goodsno, $osort){
      $query = "select * from exm_goods_opt where goodsno = '$goodsno' and osort = '$osort'";
      return $this->db->fetch($query);
   }
	 
	 
	//현재 카테고리에 속한 모든 파일 상세 살명 변경하기				//20181015		chunter
	function setGoodsDetailUpdateWithGoodsCategory($cid, $goodsno) 
	{
		//$gInfo = $this->getInfo($goodsno);
		$sql = "select * from exm_goods_cid where cid='$cid' and goodsno = '$goodsno'";
		$gInfo = $this->db->fetch($sql);
		
		$sql = "select a.* from exm_category a, exm_goods_link b where a.catno = b.catno and a.cid='$cid' and b.cid = '$cid' and b.goodsno = '$goodsno'";
		$cateList = $this->db->listArray($sql);
		
		//다중 카테고리일경우 라서 foreach 처리한다.
		foreach ($cateList as $key => $value) {
			$this->setGoodsDetailUpdateInCategory($cid, $value[catno], $gInfo[desc]);
		}
	}
	
	//특정 카테고리 상품 상세 설명변경. 자식 카테고리 처리여부 			//20181015		chunter
	function setGoodsDetailUpdateWithCategory($cid, $goodsno, $catno, $bChildInclude = false) 
	{
		$sql = "select * from exm_goods_cid where cid='$cid' and goodsno = '$goodsno'";
		$gInfo = $this->db->fetch($sql);
		
		if ($bChildInclude)
		{
			$sql = "select * from exm_category where catno like '{$cateno}%' and a.cid='$cid'";
			$cateList = $this->db->listArray($sql);
			
			//하위 모든 카테고리를 처리한다.
			foreach ($cateList as $key => $value) {
				$this->setGoodsDetailUpdateInCategory($cid, $value[catno], $gInfo[desc]);				
			}
		} else {
			$this->setGoodsDetailUpdateInCategory($cid, $catno, $gInfo[desc]);
		}
	}
	
	//상품코드들로 상세설명 변경. 		여러 상품코드를 넘길수 있다. 자신의 판매상품만 변경이 가능하다.				20181016		chunter
	function setGoodsDetailUpdateWithGoodsNo($cid, $goodsno, $copyGoodsnos) 
	{
		//스페이스 제거
		$copyGoodsnos = str_replace(" ", "", $copyGoodsnos);
		$goodsnosArr = explode(",", $copyGoodsnos);
		$whereGoodsno = implode("','", $goodsnosArr);
		$whereGoodsno = "'" .$whereGoodsno . "'";
		
		$sql = "select * from exm_goods_cid where cid='$cid' and goodsno = '$goodsno'";
		$gInfo = $this->db->fetch($sql);
		//$gInfo[desc] = addslashes($gInfo[desc]);		
		$sql = "update exm_goods_cid set `desc` = '{$gInfo[desc]}'
			where	cid='$cid' and goodsno in (
			select a.goodsno from exm_goods a inner join exm_goods_cid b on a.goodsno = b.goodsno where a.goodsno in ({$whereGoodsno}) and b.cid = '{$cid}')";
		//debug($sql);
		$this->db->query($sql);
	}
	
	//카테고리 상품 설명을 변경한다.							//20181015		chunter
	function setGoodsDetailUpdateInCategory($cid, $cateno, $goods_detail) 
	{
		$sql = "update exm_goods_cid set `desc` = '{$goods_detail}'
			where	cid='$cid' and goodsno in (
				select b.goodsno from exm_category a, exm_goods_link b where a.catno = b.catno and a.cid='$cid' and b.cid = '$cid' and a.catno = '{$cateno}')";
		//debug($sql);
		$this->db->query($sql);		
	}
	
	//20181116 / minks / 상위 카테고리까지 조회
	function getCatnm2($cid, $catno) {
		list($catnm) = $this->db->fetch("select group_concat(catnm separator '>') as catnm from exm_category where cid = '$cid' and (catno = left('$catno',3) or catno = left('$catno',6) or catno = left('$catno',9) or catno = left('$catno',12))", 1);
		
		return $catnm;
	}
	 
	 
}
?>