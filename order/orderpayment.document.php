<?

/*
* @date : 20210805
* @author : jtkim
* @brief : 기존 indb 처리시 헤더 content type이 application/x-www-form-urlencoded 형태로 전송되기 때문 파일 업로드 별도 작업 필요
* @brief : 결제 요청시 파일업로드작업이 필요한 경우 해당 파일을 수행함
* @request : 픽스토리
* @desc :
* @todo :
*/

include_once "../lib/library.php";
//include_once dirname(__FILE__) . "/../models/m_studio.php";
include_once "../lib/class.cart.php";

	$m_member = new M_member();

	if($_POST['document_type'] == "CRD"){
		$document_mobile = $_POST['document_mobile'];
		$document_card_num = $_POST['document_card_num'];
		$document_email = $_POST['document_email'];

		$m_member->setDocumentInfo($cid, $sess['mid'], $_POST['document_type'], $_POST['payno'], $document_mobile, $document_email, $document_card_num, "", "", "");
	}else if($_POST['document_type'] == "CRE" || $_POST['document_type'] == "TI"){
		$document_licensee_num = $_POST['document_licensee_num'];
		$document_email = $_POST['document_email'];

		$fname = "";

		$dir = "../data/document/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		$dir = "../data/document/$cid/";
		if (!is_dir($dir)) {
			mkdir($dir, 0707);
			chmod($dir, 0707);
		}

		//첨부파일 업로드
		if ($_FILES['document_file']['tmp_name']) {
			$ext = substr(strrchr($_FILES['document_file']['name'], "."), 1);
			$fname = time().rand(0, 9999).".".$ext;

			move_uploaded_file($_FILES['document_file']['tmp_name'], $dir.$fname);
		}

		$m_member->setDocumentInfo($cid, $sess['mid'], $_POST['document_type'], $_POST['payno'], "", $document_email, "", $document_licensee_num, $fname, "");
	}

	echo "true";
	exit;
?>
