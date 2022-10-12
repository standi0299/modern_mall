<?

function f_getToday($goodsno)
{
	
	global $db,$cid;   
	$goods = new Goods(); 
	$loop = $r_today = array();
	
	if ($goodsno)
	{
		list ($chk) = $db->fetch("select goodsno from exm_goods where goodsno = '$goodsno'",1);
	}
	if ($chk) $_COOKIE[today] .= ",".$goodsno."_".$catno;
	if (!$_COOKIE[today]) $_COOKIE[today] = "";

	$today[goodsno] = explode(",",$_COOKIE[today]);
   
	$today[goodsno] = array_unique($today[goodsno]);
	$today[goodsno] = array_notnull($today[goodsno]);
   
	$today[goodsno] = array_slice($today[goodsno],0,20);

	if ($goodsno){
		foreach ($today[goodsno] as $k=>$v)	if ($v==$goodsno."_".$catno) unset($today[goodsno][$k]);
		array_unshift($today[goodsno],$goodsno."_".$catno);
		unset($today[goodsno][20]);
	}
		
	if ($today[goodsno])
	{
		foreach ($today[goodsno] as $k=>$v)
		{
			//if ($index > 2) continue;
			$exp_data = explode("_",$v);
      
			//상품이 없는 경우만 db 조회
			if (!$r_today[$v])
			{			   
				$query = "select a.goodsno, a.img, goodsnm, if(b.price is null,a.price,b.price) price, mall_cprice as cprice, b.clistimg from exm_goods a 
	                     inner join exm_goods_cid b on a.goodsno = b.goodsno
	                     where b.cid = '$cid' and a.goodsno = '$exp_data[0]'";
	
				list ($goodsno, $listimg, $goodsnm, $price, $cprice, $clistimg) = $db->fetch($query,1);
				$r_today[$v][name] = $goodsnm;
				$r_today[$v][price] = $price;
	      $r_today[$v][cprice] = $cprice;
	      $r_today[$v][goodsno] = $exp_data[0];
	      $r_today[$v][catno] = $exp_data[1];
	      				
				if ($clistimg) {					
					$r_today[$v][clistimg] = $goods->get_listimgsrc($goodsno, $clistimg);
				}
				else {					
					$r_today[$v][clistimg] = $goods->get_imgsrc($goodsno, $listimg);
				}
	      //$r_today[$v][clistimg] = $clistimg;
			}
		}
	}

	$cookie = implode(",",array_keys($r_today));
	if ($goodsno) setCookie('today',$cookie,time()+3600*24,'/');
	
	return $r_today;
}

?>