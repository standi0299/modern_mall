<?

include "../lib/library.php";

$m_modern = new M_modern();
$m_member = new M_member();

switch ($_POST[mode]) {
    //시안확정(시안선택).    
    case "fixDesign":
        //$sess[mid]
        $db->start_transaction();
        try {

            $m_modern = new M_modern();
            $m_modern->setDesignDraftFixUpdate($_POST[payno], $_POST[fixid]);
            
            //상태변경.
            $m_modern->setDesignDraftStateUpdate($_POST[payno], "83");
                
            //payno 만 있을 경우
            list($itemstep,$ordno,$ordseq) = $db->fetch("select itemstep,ordno,ordseq from exm_ord_item where payno = '$_POST[payno]'",1);

            if ($itemstep == "82") {
                //상태변경.
                chg_itemstep($_POST[payno],$ordno,$ordseq,82,83,_("관리자 시안 검수완료 처리"));
            }

            $db->query("commit");

            echo("OK");

        } catch(Exception $e) {
            $db->query("rollback");
            
            echo("FAIL");
        }
        $db->end_transaction();
        
        //msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);

    exit;
    break;
            
    //시안요청 댓글 등록.    
    case "addComment":
        //$sess[mid]
        $db->start_transaction();
        try {

            $m_modern = new M_modern();
            
            ### form 전송 취약점 개선 20160128 by kdk
            $_POST[comment] = addslashes(base64_decode($_POST[comment]));
            
            $data = array(
                "cid" => "$cid",
                "mid" => "$sess[mid]",
                "name" => "$sess[name]",
                "payno" => "$_POST[payno]",
                "comment" => "$_POST[comment]",
                "admin" => "0"
            );
            //debug($data);

            $m_modern->setDesignCommentInsert($data);

            $db->query("commit");
        } catch(Exception $e) {
            $db->query("rollback");
        }
        $db->end_transaction();
        
        msg(_("정상적으로 저장되었습니다."), $_SERVER[HTTP_REFERER]);
        exit;break;
        
    break;
    exit;
}

if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);

?>