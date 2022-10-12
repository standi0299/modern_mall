<?
	include_once dirname(__FILE__) . "/../lib/library.php";
		
	$query = $_SERVER['QUERY_STRING'];
	$vars = array();
	
	foreach(explode('&', $query) as $pair) {
	    list($key, $value) = explode('=', $pair);
	    $key = urldecode($key);
	    $value = preg_replace("/[^A-Za-z0-9\-_]/", "", urldecode($value));
	    $vars[$key][] = $value;
	}
	$itemIds = $vars['ITEM_ID'];
	
	header('Content-Type: application/xml;charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo "<response>";
	
if (is_array($itemIds)) 
{
	 
	//$goods = new Goods();
	$m_goods = new M_goods();
	foreach($itemIds as $goods_no) 
	{		
		//$goods->getView($goods_no);
		//$data = $goods->viewData;
		$data = $m_goods->getInfo($goods_no);
		if ($data[goodsnm]) {	
			$categoryList = $m_goods->getLinkCategoryDataList($cid, $goods_no);
			
			//제고관리 사용할경우만 재고수량 표시. 사용안할경우 항상 제고 100;
			if ($data[usestock] == "1") 
				$totstock = $data[totstock];
			else 
				$totstock = "100";								
			$img_arr = explode("|", $data[img]);						
			
			$goods_image = "http://". $cfg_center[host]."/data/goods/".$cid."/l/".$goods_no."/" .$img_arr[0];
			if ($data[listimg])
				$goods_thumb_image = "http://". $cfg_center[host]."/data/goods/".$cid."/s/".$goods_no."/" .$data[listimg];			
			if (!$img_arr[0]) $goods_image = $goods_thumb_image; 
			
?>			  
			<item id="<?=$goods_no?>">
				<name><![CDATA[<?=$data[goodsnm]?>]]></name>
				<url><?php echo "http://".USER_HOST.'/goods/view.php?goodsno='.$goods_no."&catno=".$categoryList[0][catno]; ?></url>
				<description><![CDATA[<?php echo $data[summary]; ?>]]></description>
				<image><?=$goods_image?></image>
				<thumb><?=$goods_thumb_image?></thumb>
				<price><?=$data[cprice]?></price>
				<quantity><?=$totstock?></quantity>
				
				<category>
					<first id="<?=$categoryList[0][catno]?>"><![CDATA[<?php echo $categoryList[0][catnm]; ?>]]></first>
					<second id="<?=$categoryList[1][catno]?>"><![CDATA[<?php echo $categoryList[1][catnm]; ?>]]></second>
					<third id="<?=$categoryList[2][catno]?>"><![CDATA[<?php echo $categoryList[2][catnm]; ?>]]></third>
				</category>
			</item>

<?
		}
	}
}	
	echo "</response>";
?> 
