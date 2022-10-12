<?

function f_decode_json_data($ext_data, $key=''){
   
   $ext_data = json_decode($ext_data,1);
   
   if($key)
	  return $ext_data[$key];
   else
      return $ext_data;

}

?>