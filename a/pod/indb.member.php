<?
include "../lib.php";
include "../../lib/class.excel.reader.php";

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk

if ($_FILES['file']['name']){
   $uploaddir = '../../data/excel_temp/';

   if (!is_dir($uploaddir)) {    
      mkdir($uploaddir, 0707);
   } else
      @chmod($uploaddir, 0707);

   $ext = explode(".", $_FILES['file']['name']);

   $name = time() . "." . $ext[1];

   move_uploaded_file($_FILES[file][tmp_name], $uploaddir . $name);
   
   $excelImportFileName = $uploaddir . $name;

   $ext = substr(strrchr($excelImportFileName, "."), 1);
   $ext = strtolower($ext);

   if ($ext == "xlsx") {
      // Reader Excel 2007 file
      include "../../lib/PHPExcel.php";
      $objReader = PHPExcel_IOFactory::createReader("Excel2007");
      $objReader -> setReadDataOnly(true);
      $objReader -> canRead($excelImportFileName);

      $objPHPExcel = $objReader -> load($excelImportFileName);
      $objPHPExcel -> setActiveSheetIndex(0);
      $objWorksheet = $objPHPExcel -> getActiveSheet();
      $xlsData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);

      foreach ($xlsData as $key => $value) {
         if ($key >= 5 && ($value[C] && $value[B])) {
            //cid   mid password    credit_member   cust_name   name    phone   zipcode address regdt   cust_ceo    cust_no cust_type   cust_class  cust_address    etc1    ect2    etc3
            
            $mid = trim($value[B]);
            $password = trim($value[C]);
            $name = trim($value[F]);
            $phone = trim($value[G]);
            $zipcode = trim($value[H]);
            $address = trim($value[I]);
            $regdt = substr($value[J],0,10);
            $cust_name = trim($value[K]);
            $cust_ceo = trim($value[L]);
            $cust_no = trim($value[M]);
            $cust_type = trim($value[N]);
            $cust_class = trim($value[O]);
            $cust_address = trim($value[P]);
            $etc1 = trim($value[Q]);
            $etc2 = trim($value[R]);
            $etc3 = trim($value[S]);
            $etc4 = $password;
            $etc5 = trim($value[E]);
            
            $etc = "$etc1,$etc2,$etc3";
            $credit_member = "0";
            $grpno = "1";
            
            if($cust_no)
                $grpno = "2";
            
            if($password)
               $password = md5($password);
            
            //$value[H] = str_replace("..", "", $value[H]);

            $QUERY = "
               insert into exm_member set
                cid              = 'indigo',
                mid              = '$mid',
                password         = '$password',
                name             = '$name',
                phone            = '$phone',
                zipcode          = '$zipcode',
                address          = '$address',
                regdt            = '$regdt',
                cust_name        = '$cust_name',
                cust_ceo         = '$cust_ceo',
                cust_no          = '$cust_no',
                cust_type        = '$cust_type',
                cust_class       = '$cust_class',
                cust_address     = '$cust_address',
                etc1             = '$etc',
                etc4             = '$etc4',
                etc5             = '$etc5',
                credit_member    = '$credit_member',
                sort             = -UNIX_TIMESTAMP(),                              
                state            = '0',
                grpno            = '$grpno'
               on duplicate key update
                password         = '$password',
                name             = '$name',
                phone            = '$phone',
                zipcode          = '$zipcode',
                address          = '$address',
                regdt            = '$regdt',
                cust_name        = '$cust_name',
                cust_ceo         = '$cust_ceo',
                cust_no          = '$cust_no',
                cust_type        = '$cust_type',
                cust_class       = '$cust_class',
                cust_address     = '$cust_address',
                etc1             = '$etc',
                etc4             = '$etc4',
                etc5             = '$etc5',
                credit_member    = '$credit_member',
                sort             = -UNIX_TIMESTAMP(),                              
                state            = '0',
                grpno            = '$grpno'
            ;";
            
            //DEBUG($QUERY);
            //exit;
            $db->query($QUERY);
         }
      }
   }
}

msg("완료되었습니다", -1);
?>