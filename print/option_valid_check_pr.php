<?
/*
* @date : 20190325
* @author : kdk
* @brief : 옵션 제약사항 체크.
* @desc : 현수막, 실사출력 처리를 해야하고 개발후 검증이 필요함.
*/
?>
<? 
	include_once 'lib_print.php';

    $print_goods_type = $_POST[print_goods_type]; //상품구분
    //debug($print_goods_item[$param_goods_no]);

    try {
        
        $response[print_goods_type] = $print_goods_type;

        $max_size = 0;
        $min_size = 0;
        
        // * 규격 확인.
        if ($_POST[opt_size]) 
        {
            if ($_POST[opt_size] == "USER") { //사이즈 직접입력. 
                $size_x = $_POST[cut_width];
                $size_y = $_POST[cut_height];
            }
            
            if ($size_x > $size_y) {
                $max_size = $size_x;
                $min_size = $size_y;
            }
            else {
                $max_size = $size_y;
                $min_size = $size_x;
            }
        }
        else 
        {
            $response[alert_msg] = "사이즈 정보를 선택(입력)해주세요.";
        }				
			
                
        if ($print_goods_type == "PR01") { //현수막.
            if ($_POST[opt_size] == "USER") { //사이즈 직접입력. 
                if ($size_x > 5000) {
                    $response[alert_msg] = "가로의 최대 사이즈는 5000mm입니다.\n5000mm초과 사이즈 주문시 고객센터에 문의해주세요.";
                    echo json_encode($response);
                    exit;
                }

                if ($size_y > 1800) {
                    $response[alert_msg] = "세로의 최대 사이즈는 1800mm입니다.\n1800mm초과 사이즈 주문시 고객센터에 문의해주세요.";
                    echo json_encode($response);
                    exit;
                }                
            }
        }
        else if ($print_goods_type == "PR02") { //실사출력.
            if ($_POST[opt_size] == "USER") { //사이즈 직접입력. 
                if ($size_x > 5000) {
                    $response[alert_msg] = "가로의 최대 사이즈는 5000mm입니다.\n5000mm초과 사이즈 주문시 고객센터에 문의해주세요.";
                    echo json_encode($response);
                    exit;
                }

                if ($size_y > 3600) {
                    $response[alert_msg] = "세로의 최대 사이즈는 3600mm입니다.\n3600mm초과 사이즈 주문시 고객센터에 문의해주세요.";
                    echo json_encode($response);
                    exit;
                }                
            }
        }
    }
    catch (Exception $e) {
        $response[error] = $e->getMessage();
    }

    echo json_encode($response);
?>