<?
/*
* @date : 20190320
* @author : kdk
* @brief : 견적 상품 추가 현수막(Placard),실사출력(RealPrint)
* @desc : 현수막 (PR01),실사출력(PR02)
*/

/*
 * @date : 20180404
 * @author : kdk
 * @brief : 인터프로 자동견적 관련 가격 업데이트.
 * @desc :
 */
?>
<?
include "../../lib/library.php";
include 'lib_util_print_admin.php';

$m_print = new M_print();

switch($_REQUEST[mode]) {

    //가격정보 초기화(파일삭제).
    case "print_price_init" :
        //debug($_POST[opt_mode]);
        if ($_POST[opt_mode] && file_exists("../../data/print/goods_items/".$cid."_".$_POST[opt_mode].".php")) {
            unlink("../../data/print/goods_items/".$cid."_".$_POST[opt_mode].".php");
        }
        
        break;

//
    //현수막,실사출력 1mm당 단가 설정.
    case "paper_pr_price_1mm_update" :

        //$paperPriceData = json_decode(base64_decode($_POST[paperJsonData]),1);
        $paperJsonData = json_decode(base64_decode($_POST[paperJsonData]),1);

        foreach ($_POST as $ItemKey => $ItemValue) {
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;

            //$optionData[$ItemKey] = $ItemValue;
            $postArr = split('_', $ItemKey);

            //name,group
            $paperPriceData[$postArr[0]][name] = $paperJsonData[$postArr[0]][name];
            $paperPriceData[$postArr[0]][group] = $paperJsonData[$postArr[0]][group];

            //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk 
            $postArr[1] = str_replace("#", ".", $postArr[1]);
            $paperPriceData[$postArr[0]][paper][$postArr[1]][$postArr[2]] = $ItemValue;
        }
        
        //debug($optionData);exit;
        //debug($paperPriceData);exit;
        //php파일,DB 저장.
        if($paperPriceData) {
            $paperPriceData = array(
                "r_ipro_". $_POST[opt_mode] => json_encode($paperPriceData), 
                "create_mode" => $_POST[opt_mode],
                "create_prefix" => $_POST[opt_prefix],
                "create_group" => $_POST[opt_group],
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP($_POST[opt_mode], $paperPriceData);
        }

        break;
            
    //현수막,실사출력 추가 인쇄비 설정.
    case "print_pr_addprice_update" :

        //$paperPriceData = json_decode(base64_decode($_POST[paperJsonData]),1);
        
        foreach ($_POST as $ItemKey => $ItemValue) {
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //mm2저장 설정.
            if (strpos($ItemKey, "mm2_") !== false) {
                $postArr = split('_', $ItemKey);
                $optionMMData[$postArr[1]] = $ItemValue;
            }
        }
        
        //php파일,DB 저장.
        if($optionMMData) {
            $price_MM_data = array(
                "r_ipro_". $_POST[opt_mode] => json_encode($optionMMData), 
                "create_mode" => $_POST[opt_mode],
                "create_prefix" => $_POST[opt_prefix],
                "create_group" => $_POST[opt_group],
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP($_POST[opt_mode], $price_MM_data);
            //$saveMMPriceData = json_encode($optionMMData);
            //debug($saveMMPriceData);
    
            //if($saveMMPriceData)
            //    $m_print->setPrintOptionPrice($cid, $_POST[opt_mode2], $saveMMPriceData);
        }
            
        break;
    //현수막,실사출력 지류 할인율 설정.
    case "paper_pr_dc_update" :
        //debug($_POST);
        
        $r_ipro_paper_dc = array();
        //$r_ipro_paper_dc = array("PNM01" => "0.9","PNM02" => "1.5","PNM03" => "1.9");
        
        //$paperPriceData = json_decode(base64_decode($_POST[paperJsonData]),1);
        $paperJsonData = json_decode(base64_decode($_POST[paperJsonData]),1);
        //debug($paperPriceData);
        //debug($paperPriceData[PLC01][dc]);
        //debug($paperPriceData[PLC01][name]);
        //debug($paperPriceData[PLC01][group]);

        foreach ($_POST as $ItemKey => $ItemValue) {
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;

            //할인률 설정.
            //$r_ipro_paper_dc[$ItemKey] = $ItemValue;
            //$paperPriceData[$ItemKey][dc] = $ItemValue;
            
            if (strpos($ItemKey, "_") !== false) {
                //debug($ItemKey);
                $postArr = split('_', $ItemKey);
                
                //name,group
                $paperPriceData[$postArr[1]][name] = $paperJsonData[$postArr[1]][name];
                $paperPriceData[$postArr[1]][group] = $paperJsonData[$postArr[1]][group];
            
                $paperPriceData[$postArr[1]][$postArr[0]] = $ItemValue;
            }
        }

        //debug($r_ipro_paper_dc);
        //debug($paperPriceData);

        //php 파일 저장.
        if($paperPriceData) {
            $price_data = array(
                "r_ipro_paper_pr_dc" => json_encode($paperPriceData),
                "r_ipro_paper_dc_data" => json_encode($paperPriceData), 
                "create_mode" => "paper_dc",
                "create_prefix" => "paper_dc",
                "create_group" => "paper",
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP("paper_pr_dc", $price_data);
        }

        $savePriceData = json_encode($paperPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, "paper_pr_dc", $savePriceData);
        }
        
        //exit;
        break;
        
    //현수막,실사출력 지류 가격 설정.
    case "paper_pr_price_update" :
        //debug($_POST);
        
        //$paperPriceData = json_decode(base64_decode($_POST[paperJsonData]),1);
        //debug($paperPriceData);
        
        $paperJsonData = json_decode(base64_decode($_POST[paperJsonData]),1);
        //debug($paperJsonData);

        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //가격 설정.
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);

                //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk 
                $postArr[1] = str_replace("#", ".", $postArr[1]);
                
                //name,group
                $paperPriceData[$postArr[0]][name] = $paperJsonData[$postArr[0]][name];
                $paperPriceData[$postArr[0]][group] = $paperJsonData[$postArr[0]][group];
                $paperPriceData[$postArr[0]][paper][$postArr[1]][$postArr[2]] = $ItemValue;
            }
        }
        //debug($paperPriceData);

        //php 파일 저장.
        if($paperPriceData) {
            $price_data = array(
                "r_ipro_paper_pr" => json_encode($paperPriceData), 
                "create_mode" => "paper",
                "create_prefix" => "paper",
                "create_group" => "paper",
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP("paper_pr", $price_data);
        }

        $savePriceData = json_encode($paperPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, "paper_pr", $savePriceData);
        }

        //exit;
        break;



    //옵셋 ctp(판비)가격 설정.
    case "opset_ctp_update" :
        //debug($_POST);
        
        $optionPriceData = array();
        
        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //할인률 설정.
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);

                $optionPriceData[$postArr[1]][$postArr[0]] = $ItemValue;
            }
        }
        //debug($optionPriceData);
        //exit;

        //php 파일 저장.
        if($optionPriceData) {
            $price_data = array(
                "r_ipro_". $_POST[opt_mode] => json_encode($optionPriceData), 
                "create_mode" => $_POST[opt_mode],
                "create_prefix" => "",
                "create_group" => $_POST[opt_group],
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP($_POST[opt_mode], $price_data);
        }

        $savePriceData = json_encode($optionPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, $_POST[opt_mode], $savePriceData);
        }

        break;
        
    # 일반 인쇄 설정 명함,스티커.
    # 현수막 실사출력 인쇄 설정.
    case "normal_price_update" :

        $optionCntData = array();
        $optionPriceData = array();

        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //수량 저장 설정.
            if (strpos($ItemKey, "cnt_") !== false) {
                $postArr = split('_', $ItemKey);
                $optionCntData[$postArr[1]] = $ItemValue;
            }
        }
                
        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //가격 저장 설정.
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);
                if(count($postArr) < 4) continue;
                //debug($postArr);
								$postArr[2] = str_replace("#", ".", $postArr[2]);			//소수점으로 다시 변환			20180816		chunter
                if ($postArr[2] == "0")
                    $optionPriceData[$postArr[3]][$optionCntData[$postArr[0]]][$postArr[1]] = $ItemValue;
                else                    
                    $optionPriceData[$postArr[3]][$optionCntData[$postArr[0]]][$postArr[1]] = array($postArr[2] => $ItemValue);
            }
        }
        //debug($optionCntData);
        //debug($optionPriceData);
				//exit;

        //php 파일 저장.
        if($optionPriceData) {
            $price_data = array(
                "r_ipro_". $_POST[opt_mode] => json_encode($optionPriceData), 
                "create_mode" => $_POST[opt_mode],
                "create_prefix" => $_POST[opt_prefix],
                "create_group" => $_POST[opt_group],
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP($_POST[opt_mode], $price_data);
        }

        $savePriceData = json_encode($optionPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, $_POST[opt_mode], $savePriceData);
        }
        
        //exit;
        break;
    
    //지류 할인율 설정.
    case "paper_dc_update" :
        //debug($_POST);
        
        $r_ipro_paper_dc = array();
        //$r_ipro_paper_dc = array("PNM01" => "0.9","PNM02" => "1.5","PNM03" => "1.9");
        
        //$paperPriceData = json_decode(base64_decode($_POST[paperJsonData]),1);
        //debug($paperPriceData);
        //debug($paperPriceData[PLC01][dc]);
        //debug($paperPriceData[PLC01][name]);
        //debug($paperPriceData[PLC01][group]);

        foreach ($_POST as $ItemKey => $ItemValue) {
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;

            //할인률 설정.
            $r_ipro_paper_dc[$ItemKey] = $ItemValue;
            $paperPriceData[$ItemKey][dc] = $ItemValue;
        }
        //debug($r_ipro_paper_dc);
        //debug($paperPriceData);

        //php 파일 저장.
        if($paperPriceData) {
            $price_data = array(
                "r_ipro_paper_dc" => json_encode($r_ipro_paper_dc),
                "r_ipro_paper_dc_data" => json_encode($paperPriceData), 
                "create_mode" => "paper_dc",
                "create_prefix" => "paper_dc",
                "create_group" => "paper",
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP("paper_dc", $price_data);
        }

        $savePriceData = json_encode($paperPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, "paper_dc", $savePriceData);
        }
        
        //exit;
        break;
        
    //지류 가격 설정.
    case "paper_price_update" :
        //debug($_POST);
        
        //$paperPriceData = json_decode(base64_decode($_POST[paperJsonData]),1);
        //debug($paperPriceData);
        
        $paperJsonData = json_decode(base64_decode($_POST[paperJsonData]),1);
        //debug($paperJsonData);

        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //가격 설정.
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);

                //post 전송시 변수명 '.'이 포함될 경우 자동으로 '_'로 변경되어 '#'으로 치환처리함. / 20180821 / kdk 
                $postArr[1] = str_replace("#", ".", $postArr[1]);
                
                //name,group
                $paperPriceData[$postArr[0]][name] = $paperJsonData[$postArr[0]][name];
                $paperPriceData[$postArr[0]][group] = $paperJsonData[$postArr[0]][group];
                
                $paperPriceData[$postArr[0]][paper][$postArr[1]][$postArr[2]] = $ItemValue;
            }
        }
        //debug($paperPriceData);

        //php 파일 저장.
        if($paperPriceData) {
            $price_data = array(
                "r_ipro_paper" => json_encode($paperPriceData), 
                "create_mode" => "paper",
                "create_prefix" => "paper",
                "create_group" => "paper",
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP("paper", $price_data);
        }

        $savePriceData = json_encode($paperPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, "paper", $savePriceData);
        }

        //exit;
        break;
    
    //옵션 가격 설정.
    case "item_price_update" :
        //$code = $_POST[opt_mode] ."_". $_POST[opt_group];
        $code = $_POST[opt_mode];
        $optionCntData = array();
        $optionPriceData = array();
        
        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            
            //수량 저장 설정.
            if (strpos($ItemKey, "cnt_") !== false) {
                $postArr = split('_', $ItemKey);
                $optionCntData[$postArr[1]] = $ItemValue;
            }
        }

        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData" || $ItemKey == "opt_mode2" || strpos($ItemKey, "cnt_") !== false || strpos($ItemKey, "mm2_") !== false) continue;
            
            //가격 저장 설정.
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);
                //debug(count($postArr));
                //debug($postArr);
                //if(count($postArr) < 3) continue;
                
                if (count($postArr) == 3) {
                    $optionPriceData[$postArr[2]][$optionCntData[$postArr[0]]][$postArr[1]] = $ItemValue;
                }
                else if (count($postArr) == 2) {
                    $optionPriceData[$optionCntData[$postArr[0]]][$postArr[1]] = $ItemValue;
                }
            }
        }
        //debug($optionCntData);
        //debug($optionPriceData);

        //php 파일 저장.
        if($optionPriceData) {
            $price_data = array(
                "r_ipro_". $_POST[opt_mode] => json_encode($optionPriceData), 
                "create_mode" => $_POST[opt_mode],
                "create_prefix" => $_POST[opt_prefix],
                "create_group" => $_POST[opt_group],
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP($code, $price_data);
        }

        $savePriceData = json_encode($optionPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, $code, $savePriceData);
        }
        
        //옵셋 후가공 예외.domoo, press, foil, uvc, bind_BD1, bind_BD3
        if ($code == "domoo_opset" || $code == "press_opset" || $code == "foil_opset" || $code == "uvc_opset") 
        { 
            foreach ($_POST as $ItemKey => $ItemValue) {
                //mm2저장 설정.
                if (strpos($ItemKey, "mm2_") !== false) {
                    $postArr = split('_', $ItemKey);
                    $optionMMData[$postArr[2]][$postArr[1]] = $ItemValue;
                }
            }
            
            //debug($optionMMData);
            //php파일,DB 저장.
            if($optionMMData) {
                $price_MM_data = array(
                    "r_ipro_". $_POST[opt_mode2] => json_encode($optionMMData), 
                    "create_mode" => $_POST[opt_mode2],
                    "create_prefix" => $_POST[opt_prefix],
                    "create_group" => $_POST[opt_group],
                    "create_date" => date("Y-m-d H:i:s")
                );
    
                makePriceDataPHP($_POST[opt_mode2], $price_MM_data);
                //$saveMMPriceData = json_encode($optionMMData);
                //debug($saveMMPriceData);
        
                //if($saveMMPriceData)
                //    $m_print->setPrintOptionPrice($cid, $_POST[opt_mode2], $saveMMPriceData);
            }
        }
        else if ($code == "bind_BD1_opset" || $code == "bind_BD3_opset") 
        {
            foreach ($_POST as $ItemKey => $ItemValue) {
                //mm2저장 설정.
                if (strpos($ItemKey, "mm2_") !== false) {
                    $postArr = split('_', $ItemKey);
                    
                    if ($postArr[1] == "page" || $postArr[1] == "cnt" || $postArr[1] == "price")
                    {
                        $optionMMData[$_POST[opt_mode2]]['default'][$postArr[1]] = $ItemValue;
                    }
                    else 
                    {
                        $optionMMData[$_POST[opt_mode2]]['page_gram'][$postArr[1]] = $ItemValue;
                    }
                }
            }            
            //debug($optionMMData);
            //exit;
            //무선제본 기본 페이지수
            //$r_ipro_bind_BD1_default = array("page" => "64");
            //$r_ipro_bind_BD1_page_gram = array("120" => "0.8", "140" => "1", "150" => "1.2"); //120이하, 140이하, 150이상
            
            //중철제본 기본 페이지수
            //$r_ipro_bind_BD3_default = array("page" => "32", "cnt" => "1000", "price" => 60000);
            //$r_ipro_bind_BD3_page_gram = array("120" => "10", "140" => "13", "150" => "15"); //120이하, 140이하, 150이상

            //php파일,DB 저장.
            if ($optionMMData[$_POST[opt_mode2]]) {
                foreach ($optionMMData[$_POST[opt_mode2]] as $key => $value) {
                    $arr_data = array(
                        "r_ipro_". $_POST[opt_mode2] ."_". $key => json_encode($value), 
                        "create_mode" => $_POST[opt_mode2] ."_". $key,
                        "create_prefix" => $_POST[opt_prefix],
                        "create_group" => $_POST[opt_group],
                        "create_date" => date("Y-m-d H:i:s")
                    );
                    //debug($arr_data);
                    
                    makePriceDataPHP($_POST[opt_mode2] ."_". $key, $arr_data);
                }
            }            
        }        
        
        //exit;

        break;

    //옵션 가격 설정.
    case "item_dc_update" :
        //debug($_POST);
        
        $optionPriceData = array();
        
        foreach ($_POST as $ItemKey => $ItemValue) {
            //debug($ItemKey);
            if ($ItemKey == "mode" || $ItemKey == "opt_mode" || $ItemKey == "opt_prefix" || $ItemKey == "opt_group" || $ItemKey == "paperJsonData") continue;
            
            //할인률 설정.
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);

                $optionPriceData[$postArr[1]][$postArr[0]] = $ItemValue;
            }
        }
        //debug($optionPriceData);
        //exit;

        //php 파일 저장.
        if($optionPriceData) {
            $price_data = array(
                "r_ipro_". $_POST[opt_mode] => json_encode($optionPriceData), 
                "create_mode" => $_POST[opt_mode],
                "create_prefix" => "",
                "create_group" => $_POST[opt_group],
                "create_date" => date("Y-m-d H:i:s")
            );

            makePriceDataPHP($_POST[opt_mode], $price_data);
        }

        $savePriceData = json_encode($optionPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, $_POST[opt_mode], $savePriceData);
        }

        break;

    /*    
    # 자동견적 관련 규격 가격 업데이트.
    case "size_price_update" :

        $optionPriceData = array();
        //debug($r_ipro_size_digital);
        //debug($r_ipro_size_opset);
        $optionSizeDigitalPriceData = array();
        $optionSizeOpsetPriceData = array();
        
        foreach ($_POST as $ItemKey => $ItemValue) {
            if (!$ItemValue || $ItemValue == "")
                $ItemValue = "0";

            //A2_digital_B2
            //가격 저장 설정
            if (strpos($ItemKey, "_") !== false) {
                $postArr = split('_', $ItemKey);

                $optionPriceData[$postArr[0]][$postArr[1]][$postArr[2]] = $ItemValue;
            }
        }
        //debug($optionPriceData);
        
        foreach ($optionPriceData as $key => $value) {
            foreach ($value as $k => $val) {
                //디지털
                $optionSizeDigitalPriceData[$key] = $val;
                           
                //옵셋
                $optionSizeOpsetPriceData[$key] = $val;
            }
        }
        //debug($optionSizeDigitalPriceData);
        //debug($optionSizeOpsetPriceData);
        
        //php 파일 저장.
        if($optionSizeDigitalPriceData && $optionSizeOpsetPriceData) {
            $size_data = array(
                "r_ipro_size_digital" => json_encode($optionSizeDigitalPriceData), 
                "r_ipro_size_opset" => json_encode($optionSizeDigitalPriceData)
            );

            makePriceDataPHP("SIZE", $size_data);
        }

        $savePriceData = json_encode($optionPriceData);
        //debug($savePriceData);    

        if($savePriceData) {
            $m_print->setPrintOptionPrice($cid, "size_price", $savePriceData);
        }

        break;
    */

}

if (!$_POST[rurl])
    $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>