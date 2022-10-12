<?

/*
* @date : 20180810
* @author : kdk
* @brief : 명세서/견적서 양식.
* @desc :
*/

include "../lib/library.php";
include "../lib/class.cart.php";

$m_member = new M_member();

try {
    # cookie check
    //if (!$_COOKIE[cartkey] && !$sess[mid] && !$sess_admin[mid]) 
    //  throw new Exception("Error cookie", 1);
    
    # cartno check
    if (!$_REQUEST[option_json]) 
        throw new Exception("Error Parameter [option_json]", 1);

    # 견적서 관리 정보
    if ($cfg[bill_vat_yn] == "") $cfg[bill_vat_yn] = "1";
    if ($cfg[bill_nameComp] == "") $cfg[bill_nameComp] = $cfg[nameComp];
    if ($cfg[bill_typeBiz] == "") $cfg[bill_typeBiz] = $cfg[typeBiz];
    if ($cfg[bill_itemBiz] == "") $cfg[bill_itemBiz] = $cfg[itemBiz];
    if ($cfg[bill_regnumBiz] == "") $cfg[bill_regnumBiz] = $cfg[regnumBiz];
    if ($cfg[bill_nameCeo] == "") $cfg[bill_nameCeo] = $cfg[nameCeo];
    if ($cfg[bill_managerInfo] == "") $cfg[bill_managerInfo] = $cfg[managerInfo];
    if ($cfg[bill_address] == "") $cfg[bill_address] = $cfg[address];
    if ($cfg[bill_phoneComp] == "") $cfg[bill_phoneComp] = $cfg[phoneComp];
    if ($cfg[bill_faxComp] == "") $cfg[bill_faxComp] = $cfg[faxComp];
    
    # 가격 확인
    $t_ea = 0;
    $t_supply_price = 0;
    $t_tax_price = 0;
    $t_price = 0;
    $t_supply_shipprice = 0;
    $t_tax_shipprice = 0;
    $t_shipprice = 0;


    # 견적 확인
    if ($_POST[option_json]) {
        $data = json_decode($_POST[option_json], true);
        
        if ($data[est_order_option_desc])
        {
            $data[est_order_option_desc] = str_replace("[]", "", $data[est_order_option_desc]);
            $option_desc = explode(";]", $data[est_order_option_desc]);
            $option_desc = str_replace("[", "", $option_desc);
            $option_desc = str_replace(";", ",", $option_desc);
        }
    }

    # 회원 정보 조회
    $member = $m_member->getInfo($cid, $sess[mid]);
  
    //견적금액(공급가액 단가+세액) = 합계공급가액 + 합계세액
    $t_price = $_POST[est_sale_price]; 
    $t_supply_price = $_POST[est_opt_price];
    $t_tax_price = $_POST[est_vat_price];

    //nowdt, billno 생성
    $nowdt = date("Y-m-d H:i:s");
    $billno = date("Y-m-d-His", strtotime($nowdt))."-".rand(1000,9999);
    
    # 명세서/견적서 폼 생성
    $html = "
    <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
    <html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ko\">
    <head>
    <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
    <script type=\"text/javascript\" src=\"../js/jquery-1.9.1.min.js\"></script>
    </head>
    <body style=\"margin:0; padding:0; font-size:12px; line-height:150%; color:#4d4d4d; font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
    <div id=\"bill\">
    <div id=\"pop\" style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;position:relative;\">
    <div class=\"estimate\" style=\"margin:0;padding:10px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
        <h1 style=\"margin:0;padding:0 0 30px;font-size:40px;line-height:60px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;position:relative;height:33px;font-weight:bold;\">"._("견적서")."</h1>
        <table style=\"border-collapse:collapse; width:100%;\">
            <tr>
                <td style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
                    <h2 style=\"margin:0;padding:0 0 20px;font-size:30px;line-height:50px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;\">".$member[cust_name]."</h2>
                    <table class=\"date non\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;border:none;\">
                        <tbody>
                        <tr>
                            <th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("견적번호")."</th>
                            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$billno."</td>
                        </tr>
                        <tr>
                            <th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("견적일시")."</th>
                            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$nowdt."</td>
                        </tr>
                        <tr>
                            <th width=\"70\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("담당자")."</th>
                            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$member[name]."</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style=\"margin:0;padding:0;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">
                    <table class=\"date supplier\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
                        <thead>
                        <tr>
                            <th colspan=\"4\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("공급자")."</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("상호")."</th>
                            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_nameComp]."</td>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("대표자")."</th>
                            <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".$cfg[bill_nameCeo];
                                
                            if (is_file("../data/bill/".$cid."/bill_seal.png")) {
                                $html .= "<img src=\"http://".$_SERVER[HTTP_HOST]."/data/bill/".$cid."/bill_seal.png\" alt=\"\" style=\"vertical-align:middle;border:none;\" />";
                            }
                            
                            $html .= "
                            </td>
                        </tr>
                        <tr>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("등록번호")."</th>
                            <td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_regnumBiz]."</td>
                        </tr>
                        <tr>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("주소")."</th>
                            <td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_address]."</td>
                        </tr>
                        <tr>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("담당자")."</th>
                            <td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_managerInfo]."</td>
                        </tr>
                        <tr>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("업태/종목")."</th>
                            <td colspan=\"3\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_typeBiz]."/".$cfg[bill_itemBiz]."</td>
                        </tr>
                        <tr>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("전화")."</th>
                            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_phoneComp]."</td>
                            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:left;\">"._("팩스")."</th>
                            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$cfg[bill_faxComp]."</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>";

        $html .= "
        <div class=\"title\" style=\"margin:30px 0 15px;padding:15px 5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;overflow:hidden;text-align:right;background:#f2f2f2;\">
            <div class=\"tit\" style=\"margin:0;padding:0;font-size:20px;line-height:30px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;float:left;font-weight:bold;\">"._("견적금액")."<span style=\"font-size:15px;\">("._("공급가액 + 세액").")</span></div>
            <div class=\"won\" style=\"margin:0;padding:0;font-size:20px;line-height:30px;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;\">&#8361;".number_format($t_price)."</div>
        </div>";
     
        $html .= "
        <table class=\"date\" style=\"border-collapse:collapse;width:100%;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
            <thead>
                <tr>
                    <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("품명")."</th>
                    <th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("수량")."</th>
                    <th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("공급가액")."</th>
                    <th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("세액")."</th>
                    <th class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">"._("단가")."</th>
                </tr>
                </thead>
                <tbody>";

                foreach ($option_desc as $key => $value) {
                    if($value) {
                        $value = str_replace("::", ":", $value);
                        $goodsnm .= "<div style=\"margin-top:5px;\">".$value."</div>";
                    }
                }

                $html .= " 
                <tr>
                    <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">".$goodsnm."</td>
                    <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\">".$v_ea."</td>
                    <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
                    <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
                    <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;\"></td>
                </tr>";

    
        $html .= "
        </tbody>
        <tfoot>
        <tr>
            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;background:#d9d9d9;\">"._("합계")."</td>
            <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".$t_ea."</td>
            <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_supply_price)."</td>
            <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_tax_price)."</td>
            <td class=\"ar\" style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;text-align:right;font-weight:bold;background:#d9d9d9;\">".number_format($t_price)."</td>
        </tr>
        </tfoot>
    </table>
    <table class=\"date etc\" style=\"border-collapse:collapse;width:100%;margin-top:10px;border-top:1px solid #000000;border-bottom:1px solid #000000;\">
        <thead>
        <tr>
            <th style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;font-weight:bold;text-align:left;background:#d9d9d9;\">"._("기타")."</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style=\"margin:0;padding:5px;font-size:12px;line-height:150%;color:#4d4d4d;font-family:dotum, AppleGothic, Segoe UI, sans-serif;\">";
        
    $strEtc = "";
    $i = 1;
    
    //기타
    if ($cfg[bill_Etc]) $strEtc .= $cfg[bill_Etc] . "\n";
    $strEtc = explode("\n", $strEtc); //"\n" 분리

    foreach ($strEtc as $k => $v) {
        if ($i == 4) $html .= $v;
        else $html .= $v."<br />";
        
        $i++;
    }

    for ($j=$i; $j < 5; $j++) {
        if($j == 4) $html .= "&nbsp;"; 
        else $html .= "<br />";
    }
     
                $html .= "
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    </div>
    </div>
    </body>
    </html>";
    
    echo($html);
} catch (Exception $e) {
    echo 'Fail: ',  $e->getMessage(), "\n";
}

?>