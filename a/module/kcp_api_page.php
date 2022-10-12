<?php
    header("Content-type: text/html; charset=utf-8");
    /* ============================================================================== */
    /* =   API URL                                                                  = */
    /* = -------------------------------------------------------------------------- = */
    $target_URL = "https://stg-spl.kcp.co.kr/gw/enc/v1/payment"; // 개발서버
    //$target_URL = "https://spl.kcp.co.kr/gw/enc/v1/payment"; // 운영서버
    /* ============================================================================== */
    /* =  요청정보                                                                                                                                                         = */
    /* = -------------------------------------------------------------------------- = */    
    $tran_cd            = $_POST[ "tran_cd"  ]; // 요청코드
    $site_cd            = $_POST[ "site_cd"  ]; // 사이트코드
    $site_key           = $_POST[ "site_key" ]; // 사이트키
    // 인증서 정보(직렬화)
    $kcp_cert_info      = "-----BEGIN CERTIFICATE-----MIIDgTCCAmmgAwIBAgIHBy4lYNG7ojANBgkqhkiG9w0BAQsFADBzMQswCQYDVQQGEwJLUjEOMAwGA1UECAwFU2VvdWwxEDAOBgNVBAcMB0d1cm8tZ3UxFTATBgNVBAoMDE5ITktDUCBDb3JwLjETMBEGA1UECwwKSVQgQ2VudGVyLjEWMBQGA1UEAwwNc3BsLmtjcC5jby5rcjAeFw0yMTA2MjkwMDM0MzdaFw0yNjA2MjgwMDM0MzdaMHAxCzAJBgNVBAYTAktSMQ4wDAYDVQQIDAVTZW91bDEQMA4GA1UEBwwHR3Vyby1ndTERMA8GA1UECgwITG9jYWxXZWIxETAPBgNVBAsMCERFVlBHV0VCMRkwFwYDVQQDDBAyMDIxMDYyOTEwMDAwMDI0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAppkVQkU4SwNTYbIUaNDVhu2w1uvG4qip0U7h9n90cLfKymIRKDiebLhLIVFctuhTmgY7tkE7yQTNkD+jXHYufQ/qj06ukwf1BtqUVru9mqa7ysU298B6l9v0Fv8h3ztTYvfHEBmpB6AoZDBChMEua7Or/L3C2vYtU/6lWLjBT1xwXVLvNN/7XpQokuWq0rnjSRThcXrDpWMbqYYUt/CL7YHosfBazAXLoN5JvTd1O9C3FPxLxwcIAI9H8SbWIQKhap7JeA/IUP1Vk4K/o3Yiytl6Aqh3U1egHfEdWNqwpaiHPuM/jsDkVzuS9FV4RCdcBEsRPnAWHz10w8CX7e7zdwIDAQABox0wGzAOBgNVHQ8BAf8EBAMCB4AwCQYDVR0TBAIwADANBgkqhkiG9w0BAQsFAAOCAQEAg9lYy+dM/8Dnz4COc+XIjEwr4FeC9ExnWaaxH6GlWjJbB94O2L26arrjT2hGl9jUzwd+BdvTGdNCpEjOz3KEq8yJhcu5mFxMskLnHNo1lg5qtydIID6eSgew3vm6d7b3O6pYd+NHdHQsuMw5S5z1m+0TbBQkb6A9RKE1md5/Yw+NymDy+c4NaKsbxepw+HtSOnma/R7TErQ/8qVioIthEpwbqyjgIoGzgOdEFsF9mfkt/5k6rR0WX8xzcro5XSB3T+oecMS54j0+nHyoS96/llRLqFDBUfWn5Cay7pJNWXCnw4jIiBsTBa3q95RVRyMEcDgPwugMXPXGBwNoMOOpuQ==-----END CERTIFICATE-----";
    $enc_data           = $_POST[ "enc_data" ]; // 암호화 인증데이터
    $enc_info           = $_POST[ "enc_info" ]; // 암호화 인증데이터  
    $ordr_mony          = $_POST[ "good_mny" ]; // 결제금액
    
    $data = [
              'tran_cd'        => $tran_cd, 
              'site_cd'        => $site_cd,
              'site_key'       => $site_key,
              'kcp_cert_info'  => $kcp_cert_info,
              'enc_data'       => $enc_data,
              'enc_info'       => $enc_info,
              'ordr_mony'      => $ordr_mony
            ];
    
    $req_data = json_encode($data);
    
    $header_data = array( "Content-Type: application/json", "charset=utf-8" );
    
    // API REQ
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    // API RES
    $res_data  = curl_exec($ch); 
    
    /* ============================================================================== */
    /* =  응답정보                                                                                                          = */
    /* = -------------------------------------------------------------------------- = */
    // 공통
    $res_cd = "";
    $res_msg = "";
    $res_en_msg = "";
    $tno = "";
    $amount = "";
    // 카드
    $card_cd = "";
    $card_name = "";
    $app_no = "";
    
    // RES JSON DATA Parsing
    $json_res = json_decode($res_data, true);
    
    $res_cd = $json_res["res_cd"];
    $res_msg = $json_res["res_msg"];
    
    if ( $res_cd == "0000" )
    {
        $tno = $json_res["tno"];
        $amount = $json_res["amount"];
        $card_cd = $json_res["card_cd"];
        $card_name = $json_res["card_name"];
        $app_no = $json_res["app_no"];
    }
    
    curl_close($ch); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>*** NHN KCP API SAMPLE ***</title>
    <meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=yes, target-densitydpi=medium-dpi">
    <link href="static/css/style.css" rel="stylesheet" type="text/css"/>
</head>

<body oncontextmenu="return false;">
    <div class="wrap">
        <!-- header -->
        <div class="header">
            <a href="index.html" class="btn-back"><span>뒤로가기</span></a>
            <h1 class="title">TEST SAMPLE</h1>
        </div>
        <!-- //header -->
        <!-- contents -->
        <div id="skipCont" class="contents">
            <h2 class="title-type-3">요청  DATA</h2>
            <ul class="list-type-1">
                <li>
                    <div class="left">
                        <p class="title"></p>
                    </div>
                    <div class="right">
                        <div class="ipt-type-1 pc-wd-3">
                            <textarea style="height:200px; width:450px" readonly><?=$req_data?></textarea>
                        </div>
                    </div>
                </li>
            </ul>
            <h2 class="title-type-3">응답  DATA </h2>
            <ul class="list-type-1">
                <li>
                    <div class="left">
                        <p class="title"></p>
                    </div>
                    <div class="right">
                        <div class="ipt-type-1 pc-wd-3">
                            <textarea style="height:200px; width:450px" readonly><?=$res_data?></textarea>
                        </div>
                    </div>
                </li>
            </ul>
            <h2 class="title-type-3">응답  DATA </h2>
            <ul class="list-type-1">
                <li>
                    <div class="left"><p class="title">결과코드</p></div>
                    <div class="right">
                        <div class="ipt-type-1 pc-wd-2">
                            <?=$res_cd ?>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="left"><p class="title">결과메세지</p></div>
                    <div class="right">
                        <div class="ipt-type-1 pc-wd-2">
                            <?=$res_msg ?>
                        </div>
                    </div>
                </li>
                <?
                if ( $res_cd == "0000" )
                {
                ?>
                    <li>
                        <div class="left"><p class="title">KCP 거래번호</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <?=$tno ?>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="left"><p class="title">결제금액</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <?=$amount ?>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="left"><p class="title">카드</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <?=$card_name ?>(<?=$card_cd ?>)
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="left"><p class="title">승인번호</p></div>
                        <div class="right">
                            <div class="ipt-type-1 pc-wd-2">
                                <?=$app_no ?>
                            </div>
                        </div>
                    </li>
                <?
                }
                ?>
            </ul>
            
            <ul class="list-btn-2">
                <li class="pc-only-show"><a href="index.html" class="btn-type-3 pc-wd-2">처음으로</a></li>
            </ul>
        </div>
        <div class="grid-footer">
            <div class="inner">
                <!-- footer -->
                <div class="footer">
                    ⓒ NHN KCP Corp.
                </div>
                <!-- //footer -->
            </div>
        </div>
    </div>
    <!--//wrap-->
</body>
</html>