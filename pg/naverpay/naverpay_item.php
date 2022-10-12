<?
include_once(dirname(__FILE__).'/../../lib/library.php');
include_once(dirname(__FILE__).'/../../lib/class.cart.php');
//include_once(dirname(__FILE__).'/naverpay.inc.php');
include_once(dirname(__FILE__).'/naverpay.config.php');
include_once(dirname(__FILE__).'/naverpay.lib.php');

	
	$query = $_SERVER['QUERY_STRING'];

	$vars = array();

	foreach(explode('&', $query) as $pair) {
    list($key, $value) = explode('=', $pair);
    $key = urldecode($key);
    $value = preg_replace("/[^A-Za-z0-9\-_]/", "", urldecode($value));
    $vars[$key][] = $value;
	}

	$itemIds = $vars['ITEM_ID'];

	if (count($itemIds) < 1) {
    exit('ITEM_ID 는 필수입니다.');
	}

	header('Content-Type: application/xml;charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<response>
<?php
	$m_goods = new M_goods();			
	$c_goods = new Goods();
	
	foreach($itemIds as $goodsno) {
    
    $g_data = $m_goods->getInfo($goodsno);
    if(!$g_data['goodsno'])
    	continue;
		
		if ($g_data[goods_desc])
    	$g_data[goods_desc] = unserialize($g_data[goods_desc]);
		
		//이미지 URL
		if ($g_data[clistimg]) {				
			$clistimg = $c_goods->get_listimgsrc($g_data[goodsno], $g_data[clistimg]);
		}
		else {				
			if ($g_data[cimg]) $g_data[img] = $g_data[cimg];
			$clistimg = $c_goods->get_imgsrc($g_data[goodsno], $g_data[img]);
		}
		
		
		//재고 수량
		if ($g_data[usestock] && $g_data[totstock] < 1 && $g_data[state]==0)
			$totstock = 0;
		else 
			$totstock = $g_data[totstock];	

		$c_goods->getView($g_data[goodsno]);	
		$opt_data = $c_goods->viewData;
		
		
		//$returnInfo = getCfg("", "naver_returninfo");

    $id          = $g_data['goodsno'];
    $name        = $g_data['goodsnm'];
    $description = $g_data['goods_desc'];
    $price       = get_business_goods_price($g_data[goodsno], $g_data[price]);;
    $image       = $clistimg;
    $quantity    = $totstock;
    $ca_name     = '';
    $ca_name2    = '';
    $ca_name3    = '';
    
		$list_cat = $m_goods->getGoodsCategoryInfo($cid, $g_data['goodsno']);
		
		//debug($opt_data);
		//exit;
		
?>
	<item id="<?php echo $id; ?>">
<?php if($it['ec_mall_pid']) { ?>
		<mall_pid><![CDATA[<?php echo $it['ec_mall_pid']; ?>]]></mall_pid>
<?php } ?>
		<name><![CDATA[<?php echo $name; ?>]]></name>
		<url><?php echo SERVER_DOMAIN.'/goods/view.php?goodsno='.$goodsno; ?></url>
		<description><![CDATA[<?php echo $description; ?>]]></description>
		<image><?php echo $image; ?></image>
		<thumb><?php echo $image; ?></thumb>
		<price><?php echo $price; ?></price>
		<quantity><?php echo $quantity; ?></quantity>
		<category>
			<first id="<?=$list_cat[0][catno]?>"><![CDATA[<?=$list_cat[0][catnm]?>]]></first>
			<second id="<?=$list_cat[1][catno]?>"><![CDATA[<?=$list_cat[1][catnm]?>]]></second>
			<third id="<?=$list_cat[2][catno]?>"><![CDATA[<?=$list_cat[2][catnm]?>]]></third>
		</category>
		
		<options> <!-- 상품의 옵션이 없으면 이 내용은 없어도 된다. -->
<?	//옵션의 가격정보를 넘길수 없다.  npay20 을 개발해야 한다. 그런데 선택 옵션을 pods로 넘길 방법이 없다. 결국 pods 를 수정해야 하는가???	그래서 여기까지 개발후 홀드한다..		20171017		chunter 
		//옵션들을 제거하면 어떻게 되는가???
				
		if ($opt_data[r_opt]) {
			foreach ($opt_data[r_opt] as $opt_key => $opt_value) {
				echo "<option name=\"{$opt_data[optnm][$opt_key]}\">\r\n";
				foreach ($opt_value[item] as $opt_item_key => $opt_item_value) {
					echo "<select><![CDATA[{$opt_item_key}]]></select>\r\n";	
				}
				echo "</option>\r\n";
			}
		}
		
		if ($opt_data[r_addopt]) {
			foreach ($opt_data[r_addopt] as $opt_key => $opt_value) {
				echo "<option name=\"{$opt_value[addopt_bundle_name]}\">\r\n";
				foreach ($opt_value[addopt] as $opt_item_key => $opt_item_value) {
					echo "<select><![CDATA[{$opt_item_value[addoptnm]}]]></select>\r\n";	
				}
				echo "</option>\r\n";
			}
		}
		
		if ($opt_data[r_printopt]) {
			echo "<option name=\"인화옵션\">\r\n";
			foreach ($opt_data[r_printopt] as $opt_key => $opt_value) {
				echo "<select><![CDATA[{$opt_value[printoptnm]}]]></select>\r\n";	
			}
			echo "</option>\r\n";			
		}
?>
		</options>
		
		<returnInfo> <!-- 상품별 반품 주소 -->
			<zipcode><?=$returnInfo[zipcode]?></zipcode><!-- 우편번호 -->
			<address1><?=$returnInfo[address1]?></address1> <!-- 기본 주소. 동(읍/면/리)까지 입력 -->
			<address2><?=$returnInfo[address2]?></address2> <!-- 상세 주소. 번지 및 아파트 동호수까지입력 -->
			<sellername><?=$returnInfo[seller_name]?></sellername> <!-- 수령인 이름 -->
			<contact1><?=$returnInfo[seller_tel]?></contact1> <!-- 연락처1 -->
			<contact2></contact2> <!-- 연락처2. 이 값은 생략할 수 있다. -->
		</returnInfo>
		
	</item>
<?php
}
echo('</response>');

?> 
