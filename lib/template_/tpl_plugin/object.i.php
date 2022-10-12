<?

class tpl_object_i
{ 
	function tpl_object_i() {} 

	function inc($file='',$size=0,$cells=0){	
		if (!$file) $file = 'goods/_list.1.htm';

		if (!$cells) $cells = 4;
		if (!$GLOBALS[tpl]->var_[''][cells]) $GLOBALS[tpl]->assign('cells',$cells);
		if ($size) $GLOBALS[tpl]->assign('size',$size);

		$GLOBALS[tpl]->define('tmp',$file);
		$GLOBALS[tpl]->print_('tmp');
	}

	function prt($catno,$ea='',$size=0,$tpl=''){
		$ea = explode(",",$ea);
		if (!($ea[0]+0)) $ea[0] = 5;
		if (!($ea[1]+0)) $ea[1] = 2;

		$GLOBALS[tpl]->include_('f_getGoodsData');
		$GLOBALS[tpl]->include_('f_getGoodsBox');
		$loop = (!$this->goodsbox) ? f_getGoodsData($catno,$ea[0]*$ea[1]) : f_getGoodsBox($catno,$ea[0]*$ea[1]+$GLOBALS[cfg][goodsbox][ea_add]);

		if (!$GLOBALS[ici_admin] && $this->goodsbox && !$loop) return;
		if ($this->goodsbox && !$loop) echo "<div class=stxt style='padding:10px;border:1px dotted gray;background:#f7f7f7;'><b>- ��ǰ�ڽ� <span class=eng>[$catno]</span></b><div class='gray desc'>(��ǰ�� �����Ͻ÷��� ���콺�� �̰��� �÷��ּ���)</div></div>";
		
		$GLOBALS[tpl]->assign('loop',$loop);
		$GLOBALS[tpl]->assign('cells',$ea[0]);
		$GLOBALS[tpl]->assign('size',$size);
		$this->inc($tpl);
	}

	function mainlist($catno='',$data=''){
		//
	}

	function goodsbox($code,$tmp=''){
		global $cfg;
		echo "<div name='_goods_box_' code='$code' $tmp>";
		$file = dirname(__FILE__)."/../../../skin/$cfg[skin]/_conf/goodsbox/$code.php";
		if (is_file($file)) include $file;
		$this->goodsbox = 1;
		$this->prt($code,$cfg[goodsbox][ea],$cfg[goodsbox][size],$cfg[goodsbox][tpl]);
		echo "</div>";
		$this->goodsbox = 0;
	}

	function getMemberData(){
		global $db;

		$query = "
		select * from 
			exm_member a
			left join exm_member_grp b on a.grpno=b.grpno
		where
			a.cid = '{$GLOBALS[sess][cid]}' and a.mid = '{$GLOBALS[sess][mid]}'
		";
		$data = $db->fetch($query);

		$query = "
		select
			count(*)
		from
			exm_coupon a
			inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '{$GLOBALS[sess][mid]}'
		where
			a.cid = '{$GLOBALS[sess][cid]}'
			and coupon_use = 0
			and
			(
				(
				coupon_period_system = 'date'
				and coupon_period_sdate <= curdate()
				and coupon_period_edate >= curdate()
				) or
				(
				coupon_period_system = 'deadline'
				and adddate(coupon_setdt,interval coupon_period_deadline day) >= adddate(curdate(),interval 1 day)
				) or
				(
				coupon_period_system = 'deadline_date'
				and coupon_period_deadline_date >= curdate()
				) 
			)
		order by coupon_setdt desc
		";
		list($data[coupon]) = $db->fetch($query,1);

		$GLOBALS[tpl]->assign('member',$data);
	}

}

?>