<?

include "../lib/library.php";

/***********************************/ switch ($_GET[mode]){ /*************************************/

### 주소록삭제
case "address_delete":

	### 유효성체크
	if (!$cid || !$sess[mid]) break;
	if (!$_GET[addressno]) break;

	### 삭제
	$query = "
	delete from exm_address
	where
		cid				= '$cid'
		and mid			= '$sess[mid]'
		and addressno	= '$_GET[addressno]'
	";
	//debug($query);
	$db->query($query);

	break;


/***********************************/ } switch ($_POST[mode]) { /**********************************/

	case "address_insert":
	case "address_modify":

		### 유효성체크
		if (!$cid || !$sess[mid]) break;
		if ($_POST[mode] == "address_modify" && !$_POST[addressno]) break;

		if ($_POST[receiver_phone])
			$_POST[receiver_phone] = implode("-", $_POST[receiver_phone]);
		$_POST[receiver_mobile] = implode("-", $_POST[receiver_mobile]);
		//$_POST[receiver_zipcode]= implode("-",$_POST[receiver_zipcode]);


		$fields = "
		addressnm			= '$_POST[addressnm]',
		receiver_name		= '$_POST[receiver_name]',
		receiver_phone		= '$_POST[receiver_phone]',
		receiver_mobile		= '$_POST[receiver_mobile]',
		receiver_zipcode	= '$_POST[receiver_zipcode]',
		receiver_addr		= '$_POST[receiver_addr]',
		receiver_addr_sub	= '$_POST[receiver_addr_sub]'
		";

		if ($_POST[mode] == "address_modify") {
			$query = "
		update exm_address set
			$fields
		where
			cid				= '$cid'
			and mid			= '$sess[mid]'
			and addressno	= '$_POST[addressno]'
		";
		} else {
			$query = "
		insert into exm_address set 
			cid				= '$cid',
			mid				= '$sess[mid]',
			$fields
		";
		}
		//debug($query);
		$db->query($query);

		break;

	case "ajaxGetAddress":

		### 유효성체크
		if (!$cid || !$sess[mid]) exit;
		if (!$_POST[addressno]) exit;

		### 주소록추출
	if ($_POST[skin_type] == "bizcard" || $_POST['center_cid'] == "municube") {
		$query = "
		select * from exm_address 
		where
		cid				= '$cid'
		and addressno	= '$_POST[addressno]'
		";
	} else {
		$query = "
		select * from exm_address 
		where
		cid				= '$cid'
		and mid			= '$sess[mid]'
		and addressno	= '$_POST[addressno]'
	  ";
	}

	$data = $db->fetch($query);
	$encoded = json_encode($data);
	echo $encoded;

	exit;
	break;

   case "change_basic_address" :
      if(!$_POST[addressno] || $_POST[addressno] == ""){
         msg("변경할 주소 목록이 없습니다.\\n주소를 입력해 주세요.", -1);
      } else {
         $sql = "update exm_address set use_check = 'N' where cid = '$cid' and mid = '$sess[mid]'";
         $db->query($sql);

         $sql = "update exm_address set use_check = 'Y' where cid = '$cid' and mid = '$sess[mid]' and addressno = '$_POST[addressno]'";
         $db->query($sql);

         msg("변경 완료했습니다.",-1);
      }
   break;

/***********************************/ } /*********************************************************/

if (!$_POST[rurl]) $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);

?>
