<?

/*
* @date : 20180125
* @author : kdk
* @brief : 서브 카테고리 정렬 수정 . get_all_sub_category() 함수 수정.
* @request : 김기웅 이사
* @desc : m_goods.php->getCategoryList() 함수 수정.
* @todo : 
*/
### 정산 담당자 추출
//이슈번호6605 : 정산담당자 추가 / 14.10.08 / kjm
function getManager($mode=''){
    global $cid;

    $loop = array();
    $m_manager = new M_manager();
    $res = $m_manager->getList($cid, 'y');
    foreach ($res as $key => $data) {
      if ($mode) $loop[$data[manager_no]] = $data;
      else $loop[$data[manager_no]] = $data[manager_name];
    }
    
    /*
    $res = $db->query("select * from exm_manager where cid = '$cid' and valid = 'y'");
    while ($data = $db->fetch($res)){
        if ($mode) $loop[$data[manager_no]] = $data;
        else $loop[$data[manager_no]] = $data[manager_name];
    }
    */
    return $loop;
}

### 회원 그룹 추출
function getMemberGrp($mode=''){
    global $cid;

    $loop = array();
    $m_member = new M_member();
    $res = $m_member->getGrpList($cid);
    foreach ($res as $key => $data) {
      if ($mode) $loop[$data[grpno]] = $data;
      else $loop[$data[grpno]] = $data[grpnm];
    }
    /*
    $res = $db->query("select * from exm_member_grp where cid = '$cid' order by grplv desc");
    while ($data = $db->fetch($res)){
        if ($mode) $loop[$data[grpno]] = $data;
        else $loop[$data[grpno]] = $data[grpnm];
    }
    */
    
    return $loop;
}

function getBusiness($mode=''){
    global $cid;

    $loop = array();
    $m_member = new M_member();
    $res = $m_member->getBusinessList($cid);
    foreach ($res as $key => $data) {
      if ($mode) $loop[$data[bid]] = $data;
      else $loop[$data[bid]] = $data[business_name];
    }       
    /*
    $res = $db->query("select * from exm_business where cid = '$cid' order by bsort");
    while ($data = $db->fetch($res)){
        if ($mode) $loop[$data[grpno]] = $data;
        else $loop[$data[bid]] = $data[business_name];
    }
    */
    return $loop;
}

### 회원데이타 추출
function f_getMemeberData(){
    global $sess, $cid;
    /*
    $query = "
    select 
        a.*, 
        b.grpnm
    from 
        exm_member a
        left join exm_member_grp b on a.grpno = b.grpno
    where
        a.cid = '$cid' and a.mid='$sess[mid]'
    ";
    $data = $db->fetch($query);
    */

    $m_member = new M_member();
    $data = $m_member->getInfoWithGrp($cid, $sess[mid]);
    return $data;
}


function goodsImg($goodsno,$width='',$height='',$style='',$cid=''){

    global $db,$cfg_center;

    if ($width) $attr_width = "width='$width'";
    if ($height) $attr_height = "width='$height'";
    
    $m_goods = new M_goods();
    $g_data = $m_goods->getInfo($goodsno);
    $img = $g_data[img];
    
    $g_data = $m_goods->getGoodsCidInfo($GLOBALS[cid], $goodsno);
    $cimg = $g_data[cimg];

    //list ($img) = $db->fetch("select img from exm_goods where goodsno = '$goodsno'",1);
    //list ($cimg) = $db->fetch("select cimg from exm_goods_cid where cid = '$GLOBALS[cid]' and goodsno = '$goodsno'",1);
    list($cimg) = explode("|",$cimg);

    return "<img src='http://$cfg_center[host]/data/goods/$GLOBALS[cid]/l/$goodsno/$cimg' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
}


function goodsListImg($goodsno,$width='',$height='',$style='',$cid=''){

    global $db,$cfg_center;

    if ($width) $attr_width = "width='$width'";
    if ($height) $attr_height = "width='$height'";
    
    $m_goods = new M_goods();
    $g_data = $m_goods->getInfo($goodsno);
    $img = $g_data[listimg];
    
    $g_data = $m_goods->getGoodsCidInfo($GLOBALS[cid], $goodsno);
    $cimg = $g_data[clistimg];    

    //list ($img) = $db->fetch("select listimg from exm_goods where goodsno = '$goodsno'",1);
    //list ($cimg) = $db->fetch("select clistimg from exm_goods_cid where cid = '$GLOBALS[cid]' and goodsno = '$goodsno'",1);

    return "<img src='http://$cfg_center[host]/data/goods/$GLOBALS[cid]/s/$goodsno/$cimg' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
}


function goodsListImgSrc($goodsno,$width='',$height='',$style='',$cid=''){

    global $db,$cfg_center;

    if ($width) $attr_width = "width='$width'";
    if ($height) $attr_height = "width='$height'";
    
    $m_goods = new M_goods();
    $g_data = $m_goods->getInfo($goodsno);
    $img = $g_data[listimg];
    
    $g_data = $m_goods->getGoodsCidInfo($GLOBALS[cid], $goodsno);
    $cimg = $g_data[clistimg];
    //list ($img) = $db->fetch("select listimg from exm_goods where goodsno = '$goodsno'",1);
    //list ($cimg) = $db->fetch("select clistimg from exm_goods_cid where cid = '$GLOBALS[cid]' and goodsno = '$goodsno'",1);

    return "http://$cfg_center[host]/data/goods/$GLOBALS[cid]/s/$goodsno/$cimg";
}

/* 센터상품연결에서 사용 */
function goodsListImgCenter($goodsno,$width='',$height='',$style=''){
    global $db,$cfg_center;

    if ($width) $attr_width = "width='$width'";
    if ($height) $attr_height = "width='$height'";
    
    $m_goods = new M_goods();
    $g_data = $m_goods->getInfo($goodsno);
    $img = $g_data[listimg];

    return "<img src='http://$cfg_center[host]/data/goods/s/$goodsno/$img' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
}


/* 출고처수집 */
function get_release($is_cid=0)
{
    global $cid;
    if ($GLOBALS[ici_admin]){
        $fld = "compnm";
    } else {
        $fld = "nicknm";
    }

    $ret = array();
    $m_goods = new M_goods();
    $res = $m_goods->getReleaseListWithCid($cid, $fld, $is_cid);
    foreach ($res as $key => $data) {
      if ($data[cid]==$cid) $data[compnm] = "["._("자체")."] ".$data[compnm];
      $ret[$data[rid]] = trim($data[$fld]);
    }

    /*
    if ($is_cid) $where = "where cid = '$cid' and hide = 0";
    else $where = "where (cid = '' or cid = '$cid') and hide = 0";

    $query = "select * from exm_release $where order by cid,trim($fld)";
    $res = $db->query($query);
    while ($data = $db->fetch($res)){
        if ($data[cid]==$cid) $data[compnm] = "[자체] ".$data[compnm];
        $ret[$data[rid]] = trim($data[$fld]);
    }
    */

    return $ret;
}

/* 제작사수집, 제작사명을 불러옴 / 14.03.18 / kjm */
function get_release_oasis($is_cid=0){
    global $cid;
    $ret = array();
    
    $m_goods = new M_goods();
    $res = $m_goods->getReleaseList($cid, "rid");
    foreach ($res as $key => $data) {
      $ret[$data[rid]] = trim($data[compnm]);
    }
    
    /*
    $query = "select * from exm_release where cid = '$cid' and hide = 0 order by cid,trim(rid)";
    $res = $db->query($query);
    while ($data = $db->fetch($res)){
        $ret[$data[rid]] = trim($data[compnm]);
    }
    */
    return $ret;
}
function get_releasenm($rid=""){
    if (!$rid) return;
    
    $m_goods = new M_goods();
    $r_data = $m_goods->getReleaseInfo($rid);
    $ret = $r_data[nicknm];

    //$query = "select nicknm from exm_release where rid = '$rid'";
    //list ($ret) = $db->fetch($query,1);
    return $ret;
}


# 배송업체 조회
function get_shipcomp(){
    global $cid;

    $ret = array();
    $m_config = new M_config();

		//업체에서 등록한 택배 목록 나타남
    $res = $m_config->getShipCompList($cid);
    foreach ($res as $key => $data) {
    	$ret[$data[shipno]] = $data;
		}
    
    return $ret;
}

function f_getBank(){
    global $cid;

    $ret = array();
    $m_config = new M_config();
    $res = $m_config->getBankList($cid);
    foreach ($res as $key => $data) {
      $ret[$data[bankno]] = $data[bankinfo];
    }
    
    /*
    $query = "select * from exm_bank where cid = '$cid'";
    $res = $db->query($query);
    $ret = array();
    while ($data = $db->fetch($res)){
        $ret[$data[bankno]] = $data[bankinfo];
    }
    */
    return $ret;
}

### 브랜드추출
function get_brand(){
    global $cid;
    
    $r_brand = array();
    $m_config = new M_config();
    $res = $m_config->getBrandList();
    foreach ($res as $key => $data) {
      $r_brand[$data[brandno]] = trim($data[brandnm]);
    }

    /*
    $r_brand = array();
    $res = $db->query("select * from exm_brand order by trim(brandnm)");
    while ($data = $db->fetch($res)){
        $r_brand[$data[brandno]] = trim($data[brandnm]);
    }
    */
    return $r_brand;
}

### 최근게시물추출
function getBoardData($board_id,$limit=5){
    global $db, $cid;
    $loop = array();

    if (!in_array($board_id,array('qna','review','cs'))) return $loop;
    $m_board = new M_board();
    $res = $m_board->getBoardDataListNLimit($cid, $board_id, $limit);
    foreach ($res as $key => $data) {
      $loop[] = $data;
    }
    
    /*
    if ($board_id=="cs"){
        $db_table   = "exm_mycs";
        $add_where  = " and id = 'cs'";
        $orderby    = "no desc";
    }

    if ($board_id=="qna"){
        $db_table   = "exm_mycs";
        $add_where  = " and id = 'qna'";
        $orderby    = "no desc";
    }

    if ($board_id=="review"){
        $db_table   = "exm_review";
        $orderby    = "no desc";
    }

    $query = "select * from $db_table where cid='$cid' $add_where order by $orderby limit 5";
    $res = $db->query($query);

    while ($data = $db->fetch($res)){
        $loop[] = $data;
    }
    */
    return $loop;
}


//아이락에서 등록한 공지사항, 업그레이트 글 가져온다.
function getBoardDataFromIlark($board_id,$limit=5){
    global $db, $cid, $cfg_center;
    $loop = array();
    
    $m_board = new M_board();
    $res = $m_board->getBoardDataListNLimit($cfg_center[center_cid], $board_id, 5);
    foreach ($res as $key => $data) {
      $loop[] = $data;
    }
    return $loop;
}



function get_business_goods_price($goodsno,$price){
    global $cid,$sess;
    
    $m_goods = new M_goods();
    $data = $m_goods->getGoodsPriceInfo($cid, $sess[bid], $goodsno);    
    $ret = $data[price];
     
    //$query = "select price from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'goods' and goodsno = '$goodsno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $price;
    return $ret;
}

function get_business_goods_reserve($goodsno,$reserve){
    global $cid,$sess;
    
    $m_goods = new M_goods();
    $data = $m_goods->getGoodsPriceInfo($cid, $sess[bid], $goodsno);    
    $ret = $data[reserve];
    
    //$query = "select reserve from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'goods' and goodsno = '$goodsno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $reserve;
    return $ret;
}

function get_business_goods_opt_price($goodsno,$optno,$price){
    global $cid,$sess;
    $m_goods = new M_goods();
    $data = $m_goods->getOptPriceInfo($cid, $sess[bid], $goodsno, $optno);    
    $ret = $data[price];
    
    //$query = "select price from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'opt' and goodsno = '$goodsno' and optno = '$optno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $price;
    return $ret;
}

function get_business_goods_opt_reserve($goodsno,$optno,$reserve){
    global $cid,$sess;
    $m_goods = new M_goods();
    $data = $m_goods->getOptPriceInfo($cid, $sess[bid], $goodsno, $optno);    
    $ret = $data[reserve];
    
    //$query = "select reserve from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'opt' and goodsno = '$goodsno' and optno = '$optno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $reserve;
    return $ret;
}

function get_business_goods_addopt_price($goodsno,$addoptno,$price){
    global $cid,$sess;    
    $m_goods = new M_goods();
    $data = $m_goods->getAddOptPriceInfo($cid, $sess[bid], $goodsno, $addoptno);    
    $ret = $data[price];   
        
    //$query = "select price from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'addopt' and goodsno = '$goodsno' and addoptno = '$addoptno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $price;
    return $ret;
}

function get_business_goods_addopt_reserve($goodsno,$addoptno,$reserve){
    global $cid,$db,$sess;
    $m_goods = new M_goods();
    $data = $m_goods->getAddOptPriceInfo($cid, $sess[bid], $goodsno, $addoptno);    
    $ret = $data[reserve];
    
    //$query = "select reserve from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'addopt' and goodsno = '$goodsno' and addoptno = '$addoptno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $reserve;
    return $ret;
}

function get_business_goods_printopt_price($goodsno,$printoptnm,$price){
    global $cid,$sess;
    $m_goods = new M_goods();
    $data = $m_goods->getPrintOptPriceInfo($cid, $sess[bid], $goodsno, $printoptnm);    
    $ret = $data[price];
    
    //$query = "select price from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'printopt' and goodsno = '$goodsno' and printoptnm = '$printoptnm'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $price;
    return $ret;
}

function get_business_goods_printopt_reserve($goodsno,$printoptnm,$reserve){
    global $cid,$sess;
    $m_goods = new M_goods();
    $data = $m_goods->getPrintOptPriceInfo($cid, $sess[bid], $goodsno, $printoptnm);    
    $ret = $data[reserve];
    
    //$query = "select reserve from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'printopt' and goodsno = '$goodsno' and printoptnm = '$printoptnm'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $reserve;
    return $ret;
}

function get_business_goods_addpage_price($goodsno,$addpage_price){
    global $cid,$sess;    
    $m_goods = new M_goods();
    $data = $m_goods->getAddPagePriceInfo($cid, $sess[bid], $goodsno);    
    $ret = $data[price];
    
    //$query = "select price from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'page' and goodsno = '$goodsno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $addpage_price;
    return $ret;
}
function get_business_goods_addpage_reserve($goodsno,$mall_pagereserve){
    global $cid,$sess;
    $m_goods = new M_goods();
    $data = $m_goods->getAddPagePriceInfo($cid, $sess[bid], $goodsno);    
    $ret = $data[reserve];
    
    //$query = "select reserve from exm_price where cid = '$cid' and bid = '$sess[bid]' and mode = 'page' and goodsno = '$goodsno'";
    //list($ret) = $db->fetch($query,1);
    if (!is_numeric($ret)) $ret = $mall_pagereserve;
    return $ret;
}

function get_manager($valid=""){
    global $cid;
    $ret = array();
    $m_manager = new M_manager();
    $res = $m_manager->getList($cid, $valid);
    foreach ($res as $key => $data) {
      $ret[$data[manager_no]] = $data;
    }
    
    /*
    if ($valid) $valid_where = " and valid = '$valid'";
    $query = "select * from exm_manager where cid = '$cid' $valid_where";
    $res = $db->query($query);
    $ret = array();
    while ($data = $db->fetch($res)){
        $ret[$data[manager_no]] = $data;
    }
    */
    return $ret;
}

function set_stock($goodsno,$optno,$ea)
{
  global $cid;
  $m_goods = new M_goods();
  if ($optno)
  {
    $g_data = $m_goods->getGoodsOptInfo($goodsno, $optno);
    $stock = $g_data[stock];      
    //list($stock) = $db->fetch("select stock from exm_goods_opt where goodsno = '$goodsno' and optno = '$optno'",1);
        
    $stock = $stock + $ea;
    if ($stock < 0) $stock = 0;
        
    $m_goods->setGoodsOptStockUpdate($goodsno, $optno, $stock);
    //$db->query("update exm_goods_opt set stock = '$stock' where goodsno = '$goodsno' and optno = '$optno'");
  } else {
    $g_data = $m_goods->getInfo($goodsno);
    $stock = $g_data[totstock];
    //list($stock) = $db->fetch("select totstock from exm_goods where goodsno = '$goodsno'",1);
        
    $stock = $stock + $ea;
    if ($stock < 0) $stock = 0;
        
    $m_goods->setGoodsStockUpdate($goodsno, $stock);
    //$db->query("update exm_goods set totstock = '$stock' where goodsno = '$goodsno'");
  }
}


# category_link 조정
  function set_category_link($arr=array())
  {
    global $cid;

    if (!$arr || !is_array($arr)){
      return;
    }

    foreach ($arr as $goodsno)
    {
      $category_arr = array();
      $m_goods = new M_goods();
      $res = $m_goods->getCategoryLinkList($cid, $goodsno);
      foreach ($res as $key => $data) 
      {
        $sort = time()*-1;
      
        //$query = "select * from exm_goods_link where cid = '$cid' and goodsno = '$goodsno'";
        //$res = $db->query($query);
        //$sort = time()*-1;
        //while ($data = $db->fetch($res)){

        for ($i=1;$i<=strlen($data[catno])/3;$i++)
        {
          $len = $i*3;
          $in_catno = substr($data[catno],0,$len);
          $m_goods->setCategoryLinkInsert($cid, $in_catno, $data[goodsno], $sort);

          /*
          $query = "
          insert into exm_category_link set
              cid     = '$cid',
              catno   = '$in_catno',
              goodsno = '$data[goodsno]',
              sort    = '$sort'
          on duplicate key update goodsno = goodsno
          ";
          $db->query($query);
          */ 
          $sort--;

          $category_arr[] = $in_catno;
        }
      }

      $category_arr = "'".implode("','",$category_arr)."'";
        $m_goods->setCategoryLinkDelete($cid, $goodsno, $category_arr);
        //$db->query("delete from exm_category_link where cid = '$cid' and goodsno = '$goodsno' and catno not in ($category_arr)");
    }
  }



function getBidGoods($goodsno){
    global $cid;
    $ret = array();
    $m_goods = new M_goods();
    $res = $m_goods->getGoodsBidList($cid, $goodsno);
    foreach ($res as $key => $data) {
      $ret[$data[bid]] = $data[business_name];
    }    
    
    /*
    $query = "
    select a.bid,b.business_name from
        exm_goods_bid a
        inner join exm_business b on b.cid = a.cid and b.bid = a.bid
    where
        a.cid = '$cid'
        and a.goodsno = '$goodsno'
    ";
    $res = $db->query($query);
    $ret = array();
    while ($tmp = $db->fetch($res)){
        $ret[$tmp[bid]] = $tmp[business_name];
    }
    */
    
    $ret = implode(", ",$ret);
    return $ret;
}

# 실질판매가 조회함수
function get_goods_sale_price($goodsno,$optno="",$addoptno="",$cid="",$bid=""){
    global $db;
    if (!$cid) $cid = $GLOBALS[cid];
    if (!$bid) $bid = $GLOBALS[sess][bid];
    
    $m_goods = new M_goods();
    $g_data = $m_goods->getGoodsSalePrice($cid, $goodsno);
    $price = $g_data[price];
    
    /*    
    $query = "
    select
        if(c.price is null,if(b.price is null,a.price,b.price),c.price) as price
    from
        exm_goods as a
        inner join exm_goods_cid as b on b.cid = '$cid' and b.goodsno = a.goodsno
        left join exm_price as c on c.cid = '$cid' and c.bid = '$bid' and c.mode = 'goods' and c.goodsno = a.goodsno
    where
        a.goodsno = '$goodsno'
    ";
    list($price) = $db->fetch($query,1);
    */

    if ($optno)
    {
      $g_data = $m_goods->getGoodsOptSalePrice($cid, $bid, $goodsno, $optno);
      $price_opt = $g_data[price];

      /*
        $query = "
        select
            if(c.price is null,if(b.aprice is null,a.aprice,b.aprice),c.price) as price
        from
            exm_goods_opt as a
            left join exm_goods_opt_price as b on b.cid = '$cid' and b.goodsno = a.goodsno and b.optno = a.optno
            left join exm_price as c on c.cid = '$cid' and c.bid = '$bid' and c.mode = 'opt' and c.goodsno = a.goodsno and c.optno = a.optno
        where
            a.goodsno = '$goodsno'
            and a.optno = '$optno'
        ";
        list($price_opt) = $db->fetch($query,1);
     */
        $price += $price_opt;
    }
    if ($addoptno){
        if (!is_array($addoptno)) $addoptno = explode(",",$addoptno);
        foreach ($addoptno as $v)
        {
          $g_data = $m_goods->getGoodsAddOptSalePrice($cid, $bid, $goodsno, $v);
          $price_addopt = $g_data[price];

          /*       
            $query = "
            select
                if(c.price is null,if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice),c.price) as price
            from
                exm_goods_addopt as a
                left join exm_goods_addopt_price as b on b.cid = '$cid' and b.goodsno = a.goodsno and b.addoptno = a.addoptno
                left join exm_price as c on c.cid = '$cid' and c.bid = '$bid' and c.mode = 'addopt' and c.goodsno = a.goodsno and c.addoptno = a.addoptno
            where
                a.goodsno = '$goodsno'
                and a.addoptno = '$v'
            ";
            list($price_addopt) = $db->fetch($query,1);
         */
            $price += $price_addopt;
        }
    }
    return $price;
}

### 회원체크
  function chkMember()
  {
     global $cfg;
      //if (!$GLOBALS[sess]) go("../member/login.php?rurl=$_SERVER[REQUEST_URI]");

      //sess.mid 가 사라지는 현상으로 인해 login 체크시 sess.mid 로 변경한다. (wemakedesign 발생)  20140729  chunter
      if (!$GLOBALS[sess][mid]){
         go("../member/login.php?rurl=$_SERVER[REQUEST_URI]");
      }
      
      //적립금 지급처리.			20180913		
      //실제 적립금 처리는 센터에서 처리한다.		_ilarksync/set_pay_after_add_emoney.php				//20190917	chunter
			//getDeliveryEndEmoney($GLOBALS[sess][mid]);
   }

### 가격계산
function auto_price($price,$aprice,$unit,$change,$digit,$math){
   ### %계산
   if ($unit == "per"){
      $aprice = $price * $aprice / 100;
   }

   ### 가격계산
   if ($change=="dc") $price = $price - $aprice;
   else $price = $price + $aprice;

   ### 소수점처리
   $digit = $digit * 10;

   if ($digit) $price = $price / $digit;

   if ($math){
      switch ($math){
         case "floor":
            $price = floor($price);
            break;

         case "ceil":
            $price = ceil($price);
            break;

         case "round":
            $price = round($price);
            break;
      }
   }
   if ($digit) $price = $price * $digit;
   return ($price);
}


## SSL데이터 복호화
function dec($str){
   $keyarr = explode(".",$_SERVER[REMOTE_ADDR]);
   $keyarr = array_map("md5",$keyarr);
   foreach ($keyarr as $k=>$v) $keyarr[$k] = substr($v,0,2).substr($v,-2);
   $dec = base64_decode($str);
   $dec = substr($dec,4,8).substr($dec,16,8).substr($dec,28,-4);
   $dec = unserialize(base64_decode($dec));

   return $dec;
}

## 스튜디오 업로드 상세보기 링크
function get_studio_upload_detail($payno,$ordno,$ordseq,$msg="",$open_mode='01'){
   global $db;
   
   if (!$msg) $msg = _("스튜디오업로드 상세보기");

   $m_order = new M_order();
   $data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);
   $itemstep = $data[itemstep];
   //list ($itemstep) = $db->fetch("select itemstep from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'",1);

   if ($open_mode=="01" && ($itemstep==1 || $itemstep==91)) $open_mode = "02";

   $data = $m_order->getOrdUploadItemInfo($payno, $ordno, $ordseq);
   $order_code = $data[upload_order_code];
   $order_product_code = $data[upload_order_product_code];
   //list($order_code,$order_product_code) = $db->fetch("select upload_order_code,upload_order_product_code from exm_ord_upload where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'",1);

   $url = "http://studio.ilark.co.kr/order/order_detail.aspx?order_code=".$order_code."&order_product_code=".$order_product_code."&open_mode=".$open_mode;
   $str = "<span onclick=\"popup('$url',830,500,'yes','yes','xx')\" style='cursor:pointer;color:red;text-decoration:underline;'>$msg</span>";
   return $str;
}

## 카테고리 url 가져오기
function get_category_anchor($catno,$set_target="") {
   global $cid,$db;

   $m_goods = new M_goods();
   $category = $m_goods->getCategoryInfo($cid, $catno);
   //$category = $db->fetch("select catno,is_url,url_target,url from exm_category where cid = '$cid' and catno = '$catno'");

   $target = "_self";
   $src = "/goods/list.php?catno=".$category[catno];
   if ($category[is_url]){
      if ($category[url_target]=="studio") {
         if (preg_match("/product_detail\.aspx/si",$category[url])) {
            $_viewpage = 1;
            preg_match("/product_id=([0-9]+)/si",$category[url],$match);
            $_value = $match[1];
         } else {
            if (preg_match("/product_list\.aspx/si",$category[url])) {
               preg_match("/product_category_id=([0-9]+)/si",$category[url],$match);
               $_value = $match[1];
            } else {
               $_value = $category[url];
            }
            $_viewpage = 0;
         }
         $src = "/goods/studio.php?view_=".$_viewpage."&val_=".$_value."&catno=".$category[catno];
      } else if ($category[url_target]=="est") {
            $src = "/goods/estimate.php?no=".$category[url];
      } else {
         if ($category[url]) {
            if (substr($category[url],0,1)!="." && substr($category[url],0,1)!="/") $src = "http://".$category[url];
            else $src = $category[url];
            $target = $category[url_target];
         }
      }
   }
   if ($set_target) $target = $set_target;

   //iphotobook 탑메뉴에서 자바스크립트를 사용하기 위한 임시코드 by kdk
   if (strpos($src, "http://javascript:") !== false) {
      $src = str_replace("http://javascript:", "", $src);
      $anchor = "<a href='#' onclick=\"$src\" target='$target'>";
   } else {
      $anchor = "<a href='$src' target='$target'>";
   }

   //$anchor = "<a href='$src' target='$target'>";

   return $anchor;
}

//카테고리 링크 만들기
function get_category_anchor_from_arr($category,$set_target="", $categoryLinkAddClass = ""){
   global $cid;

   $target = "_self";

   if (strlen($category[catno]) == 9) {
    $src_name = "#catnav";
   }

   $src = "/goods/list.php?catno=".$category[catno].$src_name;

   if ($category[is_url]){
      if ($category[url_target]=="studio"){
         if (preg_match("/product_detail\.aspx/si",$category[url])){
            $_viewpage = 1;
            preg_match("/product_id=([0-9]+)/si",$category[url],$match);
            $_value = $match[1];
         } else {
            if (preg_match("/product_list\.aspx/si",$category[url])){
               preg_match("/product_category_id=([0-9]+)/si",$category[url],$match);
               $_value = $match[1];
            } else {
               $_value = $category[url];
            }
            $_viewpage = 0;
         }
         $src = "/goods/studio.php?view_=".$_viewpage."&val_=".$_value."&catno=".$category[catno];
      } else if ($category[url_target]=="est"){
         $src = "/goods/estimate.php?no=".$category[url];
      } else {
         if ($category[url]){
            if (substr($category[url],0,1)!="." && substr($category[url],0,1)!="/") $src = "http://".$category[url];
            else $src = $category[url];
            $target = $category[url_target];
         }
      }
   }
   if ($set_target) $target = $set_target;

   $anchor = "<a href='$src' target='$target' $categoryLinkAddClass>";

   return $anchor;
}

## 전체 카테고리 url 가져오기 (db 부담을 줄이기 위해 한번에 가져온다)    20140402    chunter
function get_all_category_anchor($set_target="") {
   global $cid,$db;

   $anchor = array();
   $m_goods = new M_goods();
   $res = $m_goods->getCategoryList($cid);
   foreach ($res as $key => $category) {
  
      //$query = "select catno,is_url,url_target,url from exm_category where cid = '$cid'";
      //$res = $db->query($query);
      //while ($category = $db->fetch($res))
      //{  
      $target = "_self";
      $src = "/goods/list.php?catno=".$category[catno];
      if ($category[is_url]){
         if ($category[url_target]=="studio"){
            if (preg_match("/product_detail\.aspx/si",$category[url])){
               $_viewpage = 1;
               preg_match("/product_id=([0-9]+)/si",$category[url],$match);
               $_value = $match[1];
            } else {
               if (preg_match("/product_list\.aspx/si",$category[url])){
                  preg_match("/product_category_id=([0-9]+)/si",$category[url],$match);
                  $_value = $match[1];
               } else {
                  $_value = $category[url];
               }
               $_viewpage = 0;
            }
            $src = "/goods/studio.php?view_=".$_viewpage."&val_=".$_value."&catno=".$category[catno];
         } else if ($category[url_target]=="est"){
            $src = "/goods/estimate.php?no=".$category[url];
         } else {
            if ($category[url]){
               if (substr($category[url],0,1)!="." && substr($category[url],0,1)!="/") $src = "http://".$category[url];
               else $src = $category[url];
               $target = $category[url_target];
            }
         }
      }
      if ($set_target) $target = $set_target;

      $anchor[$category[catno]] = "<a href='$src' target='$target'>";
   }

   return $anchor;
}



function get_all_sub_category($catno) {
   global $cid,$db,$cfg;
	$bFirstActive = false;

   $category = array();
	$m_goods = new M_goods();
   $res = $m_goods->getCategoryList($cid, '0', 'sort');
   foreach ($res as $key => $data) 
   {
  	   //debug($data);
		if (substr($data[catno], 0, 3) == substr($catno, 0, 3))
		{
         if ($data[is_mobile] != "1")
         {
				$target = "_self";
            $src = "/goods/list.php?catno=".$data[catno];
				if ($data[url]){
        	      if (substr($data[url],0,1)!="." && substr($data[url],0,1)!="/") $src = "http://".$data[url];
               else $src = $data[url];
               $target = $data[url_target];
            }

            switch (strlen($data[catno]))
				{
               case "6":
						if (strlen($catno) > 5)
						{
							$data[active] = "";
							if ($data[catno] == substr($catno, 0, 6))
								$data[active] = "active";
						} else {
							if (!$bFirstActive)	$data[active] = "active";			//첫번째걸 active 처리
							$bFirstActive = true;
						}

                  $category[$data[catno]]= $data;
               break;
					case "9":
                  $activeClass = "";
						if ($catno == $data[catno]){
						   if($cfg[skin_theme] == "M2" || $cfg[skin_theme] == "M3")
						      $activeClass = " class='on'";
                     else
                        $activeClass = " class='selected'";
                  }
						$data[category_link_tag] = get_category_anchor_from_arr($data, "", $activeClass);
						
						if ($category[substr($data[catno],0,6)]){
							$category[substr($data[catno],0,6)][sub][$data[catno]] = $data;
						}
               break;
            }
			}
      }
   }
   return $category;
}


function get_all_sub_category_P1($catno) {
	global $cid,$db,$cfg, $P1_S_category;
	$p1_sub_cate = substr($catno, 0, 3);
	if ($P1_S_category[$p1_sub_cate])
	{
		foreach ($P1_S_category[$p1_sub_cate] as $key => $value) {
			if ($key == $catno)
				$p1_sub_catetory[active]	= "checked";
			else
				$p1_sub_catetory[active]	= "";
			$p1_sub_catetory[catnm]	= "$value";
			$p1_sub_catetory[catno]	= "$key";
			$category[] = $p1_sub_catetory;
		}
	} else {
		$p1_sub_cate = substr($catno, 0, 6);
		if ($P1_S_category[$p1_sub_cate])
		{
			foreach ($P1_S_category[$p1_sub_cate] as $key => $value) {
				if ($key == $catno)
					$p1_sub_catetory[active]	= "checked";
				else
					$p1_sub_catetory[active]	= "";
				$p1_sub_catetory[catnm]	= "$value";
				$p1_sub_catetory[catno]	= "$key";
				$category[] = $p1_sub_catetory;
			}	
		}
	}	
   return $category;
}

//부가가치세 생성
function makeSurtax($price){
   $price = floor($price * 0.1);
   return($price);
}

//pods 전체 보관한에 대한 용량 가져오기     20150604    chunter
function getPodsStorageTotalSize($pods_site_id, $member_id = '', $bByteFlag = true) {
   $edit_size1 = 0;
   $podsSizeArr = getPodsStorageSize($pods_site_id, $member_id, $bByteFlag);  
   if (is_array($podsSizeArr)) {
      foreach ($podsSizeArr as $key => $value) {
         $edit_size1 += $value;
      }
   }
   return $edit_size1;
}

//pods 사용자별 보관한 크기  가져오기     20150409    chunter
function getPodsStorageSize($pods_site_id, $member_id = '', $bByteFlag = true) {	
	/*
  include_once dirname(__FILE__)."/../lib/nusoap/lib/nusoap.php";
  //$soap_url   = "http://".PODS20_DOMAIN."/StationWebService/StationWebService.asmx?WSDL";     //pods1
  $soap_url   = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/StationWebService.asmx?WSDL";
  $pod_data[siteCode] = $pods_site_id;  
  //$pod_data[siteid] = $pods_site_id;        //pods1 연동안
  if ($member_id)
    $pod_data[orderUserId] = $member_id;
    
  //$client = new soapclient($soap_url,true);
  $client = new nusoap_client($soap_url,true);
  $client->xml_encoding = "UTF-8";
  $client->soap_defencoding = "UTF-8";
  $client->decode_utf8 = false;
    
  $ret = $client->call("GetSiteUserStorageSize", $pod_data);
  $ret = explode("|",$ret["GetSiteUserStorageSizeResult"]);
  */
	
	//$ret = readUrlWithcurl('http://'.PODS20_DOMAIN.'/CommonRef/StationWebService/GetSiteUserStorageSize.aspx?siteCode=' . $pods_site_id . "&orderUserId=" .$member_id);
	//$ret = explode("|",$ret);
	$podsApi = new PODStation('20');
	$ret = $podsApi->GetSiteUserStorageSize($pods_site_id, $member_id);

   $r_ret = array("success"=>1,"fail"=>-1);
   if ($ret[0]=="success") {
      if ($ret[1] != '') {
         $podsSizeArr = json_decode($ret[1], true);

         $byteUnit = 1;
         if ($bByteFlag)
            $byteUnit = 1048;
         //$byteUnit = 1048576;
         if (is_array($podsSizeArr)) {
            foreach ($podsSizeArr as $key => $value) {
               $podsSizeArrTemp[$value[orderuserid]]  = $value[size] * $byteUnit;             //byte 로 변환.
            }
         }
         return $podsSizeArrTemp;
      } else {
         return 0;
      }
   } else {
      return 0;
   }
}

function sendMobilePush($registrationID=array(), $title='', $message='') {
   $apiKey = "AIzaSyDOPbHKcjh3c8wVkWQGvm1Y-QmR36sJohc";
   //$url = "https://android.googleapis.com/gcm/send";
   $url = "https://fcm.googleapis.com/fcm/send";

   $headers = array("Content-Type:application/json", "Authorization:key=" . $apiKey);

   //notification data는 iOS 전송 시 필요함. / 20190213 / kdk
   $data = array(
      'notification' => array('title' => $title, 'body' => $message),
      'data' => array('title' => $title, 'message' => $message),
      'registration_ids' => $registrationID
   );
   //debug($data);

   $ch = curl_init();

   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

   $response = curl_exec($ch);
   curl_close($ch);
   
   //$response = json_decode($response);
   //$obj = $response->{"success"};

   return $response;
}

//아이폰 메세지 push			//개발 코드만 작성한 상태임. app별 pem 파일을 등록해야 하며 테스트도 진행해야 한다.			20180905	chunter
function sendMobileIphonePush($deviceToken, $message)
{
	//$deviceToke = "";			// 디바이스토큰ID
	//개발용
	$apnsHost = "gateway.sandbox.push.apple.com";
	$apnsCert = "apns-dev.pem";
	if (file_exists($apnsCert))
	{
		echo "find";		
	} else 
		echo "not found";
		
	//실제용
	//$apnsHost = "gateway.push.apple.com";
	//$apnsCert = "apns-production.pem";
	$apnsPort = 2195;
	
	$payload = array('aps'=>array("alert" => $message, "badge"=>0, "sound"=>"default"));
	$payload = json_decode($payload);
	
	$streamContext = stream_context_create();
	stream_context_set_option($streamContext, "ssl", "local_cert", $apnsCert);
	$apns = stream_socket_client("ssl://".$apnsHost.":".$apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
	
	if ($apns)
	{
		$apnsMessage = chr(0).chr(0).chr(32).pack("H*", str_replace(" ", "", $deviceToken));
		fwrite($apns, $apnsMessage);
		flose($apns);
	}	
}

//temp_key 생성
//편집데이터가 정상적으로 저장되지 않았을 때 복구하기 위한 임시 키
function getTempKey($mid, $rand_num = 5){
   $micro = explode(" ",microtime());
   $date = date("md");

   $mid_rand = $date.sprintf("%03d",floor($micro[0]*1000));

   $start_rand = genRandom($rand_num);
   $end_rand = genRandom($rand_num);

   $mid = $mid;

   $temp_key = $start_rand.$mid_rand.$mid.$end_rand;

   return $temp_key;
}

function genRandom($length = 5) {
   $char = 'abcdefghijklmnopqrstuvwxyz';
   $char .= '0123456789';
   $char .= '!@%^*-_';

   $result = '';
   for($i = 1; $i <= $length; $i++) {
      $result .= $char[mt_rand(0, strlen($char)-1)];
   }
   return $result;
}

//20180906 대문자와 숫자만 kdk
function genRandom2($length = 5) {
   $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $char .= '0123456789';

   $result = '';
   for($i = 1; $i <= $length; $i++) {
      $result .= $char[mt_rand(0, strlen($char)-1)];
   }
   return $result;
}

//20170309 / minks / 아이콘 이미지 가져오기
function getIcon($cid, $icon_filename) {
	$icon = "";
	$filename = array();
	$filename = explode("||", $icon_filename);
	
	foreach ($filename as $k => $v) {
		$icon .= "<img src='../../data/icon/$cid/$v' /> ";
	}
	
	return $icon;
}
?>