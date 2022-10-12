<?
//include "../_header.php";
list($max_paydt,$max_shipdt) = $db->fetch("select max(paydt), max(shipdt) from tb_studioupload_bluepod_join_db", 1);

$max_paydate = date("Y-m-d H:i:s", strtotime($max_paydt));
$max_shipdate = date("Y-m-d H:i:s", strtotime($max_shipdt));

$query = "select a.*, b.*, c.*, d.coupon_issue_code, e.coupon_name from exm_ord_upload a
          inner join exm_pay b on a.payno = b.payno
          inner join exm_ord_item c on a.payno = c.payno and a.ordno = c.ordno and a.ordseq = c.ordseq
          left join exm_coupon_set d on d.no = c.dc_couponsetno
          left join exm_coupon e on e.cid = a.cid and e.coupon_code = d.coupon_code
          where a.cid = '$cid' and b.paydt > '2014-02-18 00:00:00' and b.paydt > '$max_paydate'
          order by b.paydt asc
";
$res = $db->query($query);

while($data = $db->fetch($res))
{
    $supply = ($data[supplyprice_goods] + $data[supplyprice_opt] + $data[supplyprice_addopt] + $data[supplyprice_printopt] + $data[supplyprice_addpage]) * $data[ea];
    $data[opt] = explode(" / ", $data[opt]);
    foreach ($data[opt] as $k => $v) {
                $v = explode(":", $v);
                $data[optstr][$k] = $v[1];
    }
    $opt1 = $data[optstr][0];
    $opt2 = $data[optstr][1];

    //ms-sql json데이터 추출
    $order_json = readurl("http://studio.ilark.co.kr/order/data/get_order_json.ashx?pods_site_id=$data[upload_pods_site_id]&order_product_code=$data[upload_order_product_code]&order_code=$data[upload_order_code]");

    $json = json_decode($order_json,true);

    $page_option = explode("-", "$json[page_option]");
    $price_option = explode("|", "$json[price_option]");

    $json_price_option = $price_option[2];
    if(!$json_price_option)
    {
        $json_price_option = $price_option[1];
        if(!$json_price_option)
        {
            $json_price_option = $price_option[0];
        }
    }
    
    $query = "
    insert into tb_studioupload_bluepod_join_db set
        payno                       = '$data[payno]',
        ordno                       = '$data[ordno]',
        ordseq                      = '$data[ordseq]',
        upload_order_code           = '$data[upload_order_code]',
        upload_order_product_code   = '$data[upload_order_product_code]',
        goodsnm                     = '$data[goodsnm]',
        ea                          = '$data[ea]',
        itemstep                    = '$data[itemstep]',
        saleprice                   = '$data[saleprice]',
        payprice                    = '$data[payprice]',
        paystep                     = '$data[paystep]',
        orddt                       = '$data[orddt]',
        dc_emoney                   = '$data[dc_emoney]',
        dc_member                   = '$data[dc_member]',
        dc_coupon                   = '$data[dc_coupon]',
        dc_coupon_name              = '$data[coupon_name]',
        dc_coupon_issue_code        = '$data[coupon_issue_code]',
        paymethod                   = '$data[paymethod]',
        opt1                        = '$opt1',
        opt2                        = '$opt2',
        addopt_str                  = '$data[addopt]',
        printopt_str                = '$data[printopt]',
        supply_goods                = '$data[supplyprice_goods]',
        supply_opt                  = '$data[supplyprice_opt]',
        price_opt                   = '$data[aprice]',
        supply_addopt               = '$data[supplyprice_addopt]',
        price_addopt                = '$data[addopt_aprice]',
        supply_printopt             = '$data[supplyprice_printopt]',
        price_printopt              = '$data[print_aprice]',
        supply_addpage              = '$data[supplyprice_addpage]',
        price_addpage               = '$data[addpage_aprice]',
        supply                      = '$supply',
        shipdt                      = '$data[shipdt]',
        shipcomp                    = '$data[compnm]',
        shipcode                    = '$data[shipcode]',
        cid                         = '$data[cid]',
        mid                         = '$data[mid]',
        paydt                       = '$data[paydt]',
        order_name                  = '$data[orderer_name]',
        order_addr                  = '$data[orderer_addr]',
        order_addr_sub              = '$data[orderer_addr_sub]',
        order_phone                 = '$data[orderer_phone]',
        order_mobile                = '$data[orderer_mobile]',
        order_email                 = '$data[orderer_email]',
        goods_price                 = '$page_option[3]',
        basic_order_file_cnt        = '$page_option[0]',
        addopt_aprice               = '$json_price_option',
        order_file_cnt              = '$json[page_cnt]',
        product_match_name          = '$json[product]',
        delivery_supply_price       = '$data[shipprice]',
        folder_location             = '$data[goodsnm]'
        
    on duplicate key update
        payno                       = '$data[payno]',
        ordno                       = '$data[ordno]',
        ordseq                      = '$data[ordseq]',
        goodsnm                     = '$data[goodsnm]',
        ea                          = '$data[ea]',
        itemstep                    = '$data[itemstep]',
        saleprice                   = '$data[saleprice]',
        payprice                    = '$data[payprice]',
        paystep                     = '$data[paystep]',
        orddt                       = '$data[orddt]',
        dc_emoney                   = '$data[dc_emoney]',
        dc_member                   = '$data[dc_member]',
        dc_coupon                   = '$data[dc_coupon]',
        dc_coupon_name              = '$data[dc_coupon_name]',
        dc_coupon_issue_code        = '$data[dc_coupon_issue_code]',
        paymethod                   = '$data[paymethod]',
        opt1                        = '$opt1',
        opt2                        = '$opt2',
        addopt_str                  = '$data[addopt]',
        printopt_str                = '$data[printopt]',
        supply_goods                = '$data[supplyprice_goods]',
        supply_opt                  = '$data[supplyprice_opt]',
        price_opt                   = '$data[aprice]',
        supply_addopt               = '$data[supplyprice_addopt]',
        price_addopt                = '$data[addopt_aprice]',
        supply_printopt             = '$data[supplyprice_printopt]',
        price_printopt              = '$data[print_aprice]',
        supply_addpage              = '$data[supplyprice_addpage]',
        price_addpage               = '$data[addpage_aprice]',
        supply                      = '$supply',
        shipdt                      = '$data[shipdt]',
        shipcomp                    = '$data[compnm]',
        shipcode                    = '$data[shipcode]',
        cid                         = '$data[cid]',
        mid                         = '$data[mid]',
        paydt                       = '$data[paydt]',
        order_name                  = '$data[orderer_name]',
        order_addr                  = '$data[orderer_addr]',
        order_addr_sub              = '$data[orderer_addr_sub]',
        order_phone                 = '$data[orderer_phone]',
        order_mobile                = '$data[orderer_mobile]',
        order_email                 = '$data[orderer_email]',
        goods_price                 = '$page_option[3]',
        basic_order_file_cnt        = '$page_option[0]',
        addopt_aprice               = '$json_price_option',
        order_file_cnt              = '$json[page_cnt]',
        product_match_name          = '$json[product]',
        delivery_supply_price       = '$data[shipprice]',
        folder_location             = '$data[goodsnm]'
    ";
    $db->query($query);
}

$query_ship = "select a.*, d.compnm, b.shipdt, b.shipcode from exm_ord_upload a
               inner join exm_ord_item b on a.payno = b.payno and a.ordno = b.ordno and a.ordseq = b.ordseq
               inner join exm_pay c on a.payno = c.payno
               left join exm_shipcomp d on b.shipcomp = d.shipno
               where a.cid = '$cid' and c.paydt > '2014-02-18 00:00:00' and b.shipdt > '$max_shipdate'
               order by b.shipdt asc
";
$res = $db->query($query_ship);

while($data = $db->fetch($res))
{
    $query = "
    update tb_studioupload_bluepod_join_db set 
        shipdt      = '$data[shipdt]',
        shipcomp    = '$data[compnm]',
        shipcode    = '$data[shipcode]'
     where
        upload_order_code = '$data[upload_order_code]' and upload_order_product_code = '$data[upload_order_product_code]'
    ";
    $db->query($query);
    //debug($db);
}

//MS SQL 접속
/*테스트서버
$msServer = "192.168.1.199";
$msUser = "sa";
$msPass = "dkdlfkr12!@";
$msDB = "studio_upload";
*/
/*
$msServer = "211.115.221.165";
$msUser = "oasis_studio";
$msPass = "dkdlfkr12!@";
$msDB = "studio_upload";

$ms_db = mssql_connect($msServer, $msUser, $msPass, $msDB) or die("server connection fail");
mssql_select_db($msDB, $ms_db) or die("DB connection fail");

$ms_query = "select order_code, order_product_code, delivery_normal_price, order_user_name, cast(order_option_json as text) as order_option_json,
             cast(folder_location as text) as folder_location, CONVERT(VARCHAR, order_date, 20) as order_date
             from dbo.order_studio where mall_ID = '$cid'";

$ms_res = mssql_query($ms_query, $ms_db);

while($ms_data = mssql_fetch_array($ms_res))
{
    $json = json_decode(iconv('euc-kr', 'utf-8', $ms_data[4]),1);
    
        debug($json);

    $folder_location = iconv('euc-kr', 'utf-8', $ms_data[folder_location]);
    $order_user_name = iconv('euc-kr', 'utf-8', $ms_data[order_user_name]);

    $page_option = explode("-", "$json[page_option]");
    $price_option = explode("|", "$json[price_option]");

    $json_price_option = $price_option[2];
    if(!$json_price_option)
    {
        $json_price_option = $price_option[1];
        if(!$json_price_option)
        {
            $json_price_option = $price_option[0];
        }
    }
  
    $ms_update_query = "
    update tb_studioupload_bluepod_join_db set
    goods_price = '$page_option[3]',
    basic_order_file_cnt = '$page_option[0]',
    addopt_aprice = '$json_price_option',
    order_file_cnt = '$json[page_cnt]',
    delivery_supply_price = '$ms_data[delivery_normal_price]',
    order_user_name = '$order_user_name',
    folder_location = '$folder_location',
    product_match_name = '$json[product]',
    order_date = '$ms_data[order_date]'
    where upload_order_product_code = '$ms_data[order_product_code]' and '$ms_data[order_date]' > '$max_order_date'
    ";
    $db->query($ms_update_query);
};
*/
?>