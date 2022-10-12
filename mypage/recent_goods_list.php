<?
include_once "../_header.php";

$goods = new Goods();
$loop = $r_today = array();

$today[goodsno] = explode(",",$_COOKIE[today]);

$today[goodsno] = array_unique($today[goodsno]);
$today[goodsno] = array_notnull($today[goodsno]);

$today[goodsno] = array_slice($today[goodsno],0,20);

if ($goodsno){
   foreach ($today[goodsno] as $k=>$v) if ($v==$goodsno."_".$catno) unset($today[goodsno][$k]);
   array_unshift($today[goodsno],$goodsno."_".$catno);
   unset($today[goodsno][20]);
}

if ($today[goodsno])
{
	foreach ($today[goodsno] as $k=>$v)
	{
		$exp_data = explode("_",$v);
		
		//상품이 없는 경우만 db 조회
		if (!$r_today[$exp_data[0]])
		{
      $query = "select a.goodsno, a.img, goodsnm, if(b.price is null,a.price,b.price) price, mall_cprice as cprice, b.clistimg from exm_goods a 
	    		inner join exm_goods_cid b on a.goodsno = b.goodsno
	        where b.cid = '$cid' and a.goodsno = '$exp_data[0]'";
	
			list ($goodsno, $listimg, $goodsnm, $price, $cprice, $clistimg) = $db->fetch($query,1);
      $r_today[$goodsno][name] = $goodsnm;
      $r_today[$goodsno][price] = $price;
      $r_today[$goodsno][cprice] = $cprice;
      $r_today[$goodsno][goodsno] = $exp_data[0];
      $r_today[$goodsno][catno] = $exp_data[1];
			
			if ($clistimg) {					
				$r_today[$goodsno][clistimg] = $goods->get_listimgsrc($goodsno, $clistimg);
			}
			else {					
				$r_today[$goodsno][clistimg] = $goods->get_imgsrc($goodsno, $listimg);
			}
			//$r_today[$goodsno][clistimg] = $clistimg;
		}
	}
}
//debug($r_today);
$tpl->assign('today',$r_today);
$tpl->print_('tpl');
?>