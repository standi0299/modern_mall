<?

class stat{

	function stat(){
		global $db,$cfg;
		$this->db = $db;
		$this->cfg = $cfg;

		$this->get_mall(3); //운영중인분양몰리스트추출
	}

	### 분양몰 리스트 추출
	function get_mall($state=""){ $this->r_mall = array();

		$query = "select * from exm_mall";
		if ($state) $query .= " where state='$state'";
		$res = $this->db->query($query);
		$r_mall = array();
		while ($tmp=$this->db->fetch($res)){
			$r_mall[$tmp[cid]] = $tmp;
		}
		//debug($r_mall);
		$this->r_mall = $r_mall;
	}

	### $cid : 추출대상CID입력(복수개시 배열로)
	function get_order($cid=""){
		$this->order	= array();
		$this->pay		= array();

		### 추출대상CID설정
		if ($cid && !is_array($cid)) $cid = array($cid);
		$cid = ($cid) ? $cid : array_keys($this->r_mall);
		$cid = "'".implode("','",$cid)."'";
		if (!$cid) return;

		### ORDER추출
		$query = "
		select 
			cid,
			selfgoods,
			itemstep,
			count(*) cnt
		from 
			exm_pay a 
			inner join exm_ord_item c on a.payno = c.payno
		where
			cid in ($cid)
		group by cid,selfgoods,itemstep
		";
		$res = $this->db->query($query);
		$this->order = array();
		while ($tmp=$this->db->fetch($res)){
			$this->order[$tmp[selfgoods]][$tmp[itemstep]] += $tmp[cnt];
			$this->order[$tmp[cid]][$tmp[selfgoods]][$tmp[itemstep]] = $tmp[cnt];
		}
		
		### PAY추출
		$query = "
		select 
			cid,
			selfgoods,
			paystep,
			count(*) cnt
		from
			exm_pay a
			inner join exm_ord_item c on a.payno = c.payno
		where
			cid in ($cid)
			and paystep in (1,91)
		group by cid,selfgoods,paystep
		";
		$res = $this->db->query($query);
		$this->pay = array();
		while ($tmp = $this->db->fetch($res)){
			$this->pay[$tmp[selfgoods]][$tmp[paystep]] += $tmp[cnt];
			$this->pay[$tmp[cid]][$tmp[selfgoods]][$tmp[paystep]] = $tmp[cnt];
		}

		if ($this->cfg[pg][paymethod] && in_array("t",$this->cfg[pg][paymethod])){
			$this->pay[$tmp[selfgoods]][1] += $this->pay[$tmp[selfgoods]][91];
			$this->pay[$tmp[cid]][$tmp[selfgoods]][1] += $this->pay[$tmp[cid]][$tmp[selfgoods]][91];
		}
	}

	### 일간단위 출고완료상품조회
	function get_day_ship($cid=""){
		
		### 초기화
		$this->ship = array();
		
		### 추출대상CID설정
		if ($cid && !is_array($cid)) $cid = array($cid);
		$cid = ($cid) ? $cid : array_keys($this->r_mall);
		$cid = "'".implode("','",$cid)."'";
		if (!$cid) return;

		### 일간단위설정
		for ($i=0;$i<=7;$i++){
			$this->r_day[] = date("Y-m-d",time()-86400*$i);
		}

		$query = "
		select 
			cid,
			left(shipdt,10) shipdt,
			selfgoods,
			count(*) cnt,
			sum(b.payprice) payprice 
		from 
			exm_pay a
			inner join exm_ord_item b on a.payno = b.payno
		where
			itemstep = 5
			and shipdt >= '".$this->r_day[count($this->r_day)-1]."'
			and shipdt < adddate('{$this->r_day[0]}',interval +1 day)
			and cid in ($cid)
		group by 
			cid,
			left(shipdt,10),
			selfgoods
		";
		$res = $this->db->query($query);
		$this->ship = array();
		while ($tmp = $this->db->fetch($res)){
			$this->ship[$tmp[shipdt]][total][cnt] += $tmp[cnt];
			$this->ship[$tmp[shipdt]][total][payprice] += $tmp[payprice];

			$this->ship[$tmp[cid]][$tmp[shipdt]][total][cnt] += $tmp[cnt];
			$this->ship[$tmp[cid]][$tmp[shipdt]][total][payprice] += $tmp[payprice];
			
			$this->ship[$tmp[cid]][$tmp[shipdt]][$tmp[selfgoods]][cnt] = $tmp[cnt];
			$this->ship[$tmp[cid]][$tmp[shipdt]][$tmp[selfgoods]][payprice] = $tmp[payprice];
		}

		//debug($this->ship);

	}

	### 주간단위 출고완료상품조회
	function get_week_ship($cid=""){

		$this->week_ship = array();

		### 주간단위추출
		$w = date("w",time());
		$this->r_week[0] = date("Y-m-d",strtotime(date("Y-m-d")." -$w days"));
		$this->r_week[1] = date("Y-m-d",strtotime($this->r_week[0]." -7 days"));
		$this->r_week[2] = date("Y-m-d",strtotime($this->r_week[1]." -7 days"));
		$this->r_week[3] = date("Y-m-d",strtotime($this->r_week[2]." -7 days"));

		### 추출대상CID설정
		if ($cid && !is_array($cid)) $cid = array($cid);
		$cid = ($cid) ? $cid : array_keys($this->r_mall);
		$cid = "'".implode("','",$cid)."'";
		if (!$cid) return;

		foreach ($this->r_week as $week){
			$query = "
			select 
				cid,
				'$week' week,
				selfgoods,
				count(*) cnt,
				sum(b.payprice) payprice 
			from 
				exm_pay a
				inner join exm_ord_item b on a.payno = b.payno
			where
				itemstep = 5
				and shipdt >= '$week'
				and shipdt < adddate('$week',interval +7 day)
				and cid in ($cid)
			group by 
				cid,
				selfgoods
			";
			$res = $this->db->query($query);
			while ($tmp = $this->db->fetch($res)){

				$this->week_ship[$tmp[week]][total][cnt] += $tmp[cnt];
				$this->week_ship[$tmp[week]][total][payprice] += $tmp[payprice];

				$this->week_ship[$tmp[cid]][$tmp[week]][total][cnt] += $tmp[cnt];
				$this->week_ship[$tmp[cid]][$tmp[week]][total][payprice] += $tmp[payprice];
				
				$this->week_ship[$tmp[cid]][$tmp[week]][$tmp[selfgoods]][cnt] = $tmp[cnt];
				$this->week_ship[$tmp[cid]][$tmp[week]][$tmp[selfgoods]][payprice] = $tmp[payprice];

			}
		}

		//debug($this->week_ship);

	}


	### 월별통계(업체별상세)
	function get_month_ship($cid=""){

		$this->month_ship = array();
		if (!$this->year) $this->year = date("Y");

		### 월간단위추출
		for ($i=1;$i<=12;$i++){
			$this->r_month[] = $this->year."-".sprintf("%02d",$i);
		}

		### 추출대상CID설정
		if ($cid && !is_array($cid)) $cid = array($cid);
		$cid = ($cid) ? $cid : array_keys($this->r_mall);
		$cid = "'".implode("','",$cid)."'";
		if (!$cid) return;

		foreach ($this->r_month as $month){
			$query = "
			select 
				cid,
				'$month' month,
				selfgoods,
				count(*) cnt,
				sum(b.payprice) payprice 
			from 
				exm_pay a
				inner join exm_ord_item b on a.payno = b.payno
			where
				itemstep >= 2 and itemstep <= 5
				and paymethod!='t'
				and paydt >= '$month-01'
				and paydt < '".date("Y-m-d",strtotime("$month-01 +1 month"))."'
				and cid in ($cid)
			group by 
				cid,
				selfgoods
			";
			$res = $this->db->query($query);
			while ($tmp = $this->db->fetch($res)){

				$this->month_ship[$tmp[month]][total][cnt] += $tmp[cnt];
				$this->month_ship[$tmp[month]][total][payprice] += $tmp[payprice];

				$this->month_ship[$tmp[cid]][$tmp[month]][total][cnt] += $tmp[cnt];
				$this->month_ship[$tmp[cid]][$tmp[month]][total][payprice] += $tmp[payprice];
				
				$this->month_ship[$tmp[cid]][$tmp[month]][$tmp[selfgoods]][cnt] = $tmp[cnt];
				$this->month_ship[$tmp[cid]][$tmp[month]][$tmp[selfgoods]][payprice] = $tmp[payprice];

			}
			//debug($query);

			$query = "
			select 
				cid,
				'$month' month,
				selfgoods,
				count(*) cnt,
				sum(b.payprice) payprice 
			from 
				exm_pay a
				inner join exm_ord_item b on a.payno = b.payno
			where
				((itemstep=92) or (itemstep >= 2 and itemstep <= 5))
				and paymethod='t'
				and orddt >= '$month-01'
				and orddt < '".date("Y-m-d",strtotime("$month-01 +1 month"))."'
				and cid in ($cid)
			group by 
				cid,
				selfgoods
			";
			//debug($query);
			$res = $this->db->query($query);
			while ($tmp = $this->db->fetch($res)){

				$this->month_ship[$tmp[month]][total][cnt] += $tmp[cnt];
				$this->month_ship[$tmp[month]][total][payprice] += $tmp[payprice];

				$this->month_ship[$tmp[cid]][$tmp[month]][total][cnt] += $tmp[cnt];
				$this->month_ship[$tmp[cid]][$tmp[month]][total][payprice] += $tmp[payprice];
				
				$this->month_ship[$tmp[cid]][$tmp[month]][$tmp[selfgoods]][cnt] = $tmp[cnt];
				$this->month_ship[$tmp[cid]][$tmp[month]][$tmp[selfgoods]][payprice] = $tmp[payprice];

			}


		}

	}

}

?>