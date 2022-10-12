<?

function f_dp_goods($dpcode){

	global $db,$cfg_center,$cfg,$cid,$sess;
	$loop = array();

	$dp_ = $db->fetch("select * from exm_goods_dp where cid = '$cid' and dpno = '$dpcode'");

	$limit = $dp_[rows]*$dp_[cells];

	$bid_where = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')":"bid.bid is null";

	$query = "
	select 
		a.*,b.*,c.*,d.*,
		if(b.price is null,a.price,b.price) price,
		b.mall_cprice,
		a.goodsno as f_goodsno, d.catno as f_catno
	from
		exm_goods a
		inner join exm_goods_cid b on b.goodsno = a.goodsno
		inner join exm_dp_link c on c.goodsno = a.goodsno and b.cid = c.cid
		left join exm_goods_link d on d.goodsno = a.goodsno and d.cid = c.cid
		left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
	where
		c.dpno	= '$dpcode'
		and b.cid = '$cid'
		and c.cid = '$cid'
		and b.nodp = 0
		and $bid_where
	order by seq
	limit $limit
	";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		if ($sess[bid]) $data[price] = get_business_goods_price($data[goodsno],$data[price]);
		if ($sess[bid]) $data[reserve] = get_business_goods_reserve($data[goodsno],$data[reserve]);
		
		//2013.12.09 / minks / 조건문 추가(상품명이 20byte이상이고 goodsnm_cut_flag가 Y일 경우 상품명을 20byte까지만 보여주고 나머지 경우는 상품명을 그대로 보여줌)
		//2013.12.18 / minks / mb_substr은 문자단위로 변수명을 자름 -> 상품명이 20byte이상이고 goodsnm_cut_flag가 Y일 경우 상품명을 13문자까지만 보여주고 나머지 경우는 상품명을 그대로 보여줌(수정)
		if(strlen($data[goodsnm]) > 20 && $dp_[goodsnm_cut_flag] == "Y"){			
			$data[goodsnm] = mb_substr($data[goodsnm], 0, 13, "UTF-8");
		}
		else{
			$data[goodsnm] = $data[goodsnm];
		}
		
		$loop[] = $data;
	}

	$dp_[cells] = ($dp_[cells]) ? $dp_[cells]:$cfg[cells];
	$dp_[listimg_w] = ($dp_[listimg_w]) ? $dp_[listimg_w]:$cfg[listimg_w];

	$GLOBALS[tpl]->assign('dpcode',$dpcode);
	$GLOBALS[tpl]->assign('loopBox',$loop);
	$GLOBALS[tpl]->assign('dp_',$dp_);
	$tpl = "/goods/_list.box.htm";
	$GLOBALS[tpl]->define('box',$tpl);
	$GLOBALS[tpl]->print_('box');
}

?>