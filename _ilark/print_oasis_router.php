<?
/*
* @date : 20180725
* @author : kdk
* @brief : 오아시스 연동. 라우터 정보 insert,update.
* @desc : 라우터 프로그램에서 넘어오는 항목
 * router_user_id : 라우터 셋팅 사용자 아이디
 * router_user_name : 라이터 셋팅 사용자명
 * machine_id : 장비 아이디
 * data : 장비 json 데이타
 * type : 
   - delete면 해당 장비아이디로 저장된거 삭제
   - insert면 인서트
   - update면 해당 장비정보 업데이트
*/
?>
<?
include "../lib/library.php";
include "../print/lib_print.php";

try {
    $m_print = new M_print();
    $response[code] = "FAIL";
    
    if (!$_REQUEST[router_user_id]) {
        $response[msg] = "라우터 셋팅 사용자 아이디가 없습니다.";
        echo json_encode($response);
        exit;
    }

    if (!$_REQUEST[router_user_name]) {
        $response[msg] = "라이터 셋팅 사용자명이 없습니다.";
        echo json_encode($response);
        exit;
    }
    
    if (!$_REQUEST[machine_id]) {
        $response[msg] = "장비 아이디가 없습니다.";
        echo json_encode($response);
        exit;
    }

    if ($_REQUEST[type] == "insert" || $_REQUEST[type] == "update") {
        if (!$_REQUEST[data]) {
            $response[msg] = "장비 정보가 없습니다.";
            echo json_encode($response);
            exit;
        }
    }

    if (!$_REQUEST[type]) {
        $response[msg] = "작업 타입 정보가 없습니다.";
        echo json_encode($response);
        exit;
    }

    $router_user_id = $_REQUEST[router_user_id];
    $router_user_name = $_REQUEST[router_user_name];
    $machine_id = $_REQUEST[machine_id];
    $router_info = $_REQUEST[data];
    $send_type = $_REQUEST[type];

    if ($send_type == "insert") {
        $m_print->setOasisRouterInsert($cid, $router_user_id, $router_user_name, $machine_id, $router_info, $send_type);
    }
    else if ($send_type == "update") {
        $m_print->setOasisRouterUpdate($cid, $router_user_id, $router_user_name, $machine_id, $router_info, $send_type);
    }
    else if ($send_type == "delete") {
        $m_print->setOasisRouterDelete($cid, $router_user_id, $machine_id);
    }
    
    $response[code] = "OK";
}
catch (Exception $e) {
    $response[msg] = $e->getMessage();
    $response[error] = $e->getMessage();
}

echo json_encode($response);
?>