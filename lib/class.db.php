<?
/**
 * MySQL class
 * 2009.05.09
 */
class DB
{
	var $db_name, $db_conn, $count;
	var $error_pass = 0;
	var $btran = false;
	var $btran_err = false;
	var $insert_id = 0;
   var $id = 0;

	function DB($iniFile=''){
		if (!$iniFile) return;
		include $iniFile;
		$this->connect($db_host,$db_user,$db_pass);
		if ($db_name) $this->select($db_name);
	}

	function connect($db_host,$db_user,$db_pass){
      //$this->db_conn = @mysql_connect($db_host, $db_user, $db_pass);
      $this->db_conn = mysql_connect($db_host, $db_user, $db_pass);
		if (!$this->db_conn){
			$err['msg'] = 'DB connection error..';
			$this->error($err);
		}
	}

	function select($db_name){
		$ret = mysql_select_db($db_name);
		if (!$ret){
			$err['msg'] = 'DB selection error..';
			$this->error($err);
		} else $this->db_name = $db_name;
	}

	function query($query, $blog = true){

		$time[] = $this->microtime_float();

		$debug = @debug_backtrace();
		krsort($debug);
		foreach ($debug as $v) $debuginf[] = $v['file']." (line:$v[line])";
		$debuginf = implode("<br>",$debuginf);

		$res = mysql_query($query, $this->db_conn);
      
		if (!$res) {
         $err['query']	= htmlspecialchars($query);
			$err['file']	= $debuginf;
			$this->error($err);
		} else {
			if (preg_match("/^select/",trim($query))) $this->count = $this->count_($res);
			else $this->count = @mysql_affected_rows($this->db_conn);

      if ($blog) {
  			$time[] = $this->microtime_float();
  			$this->time[] = $time[1] - $time[0];
  			$this->log[] = $query;
         ## 로그
  
  			//if (strpos($query,"exm_edit") || strpos($query,"exm_mycs"))
         if (strpos($query,"exm_pay") || strpos($query,"exm_ord") || strpos($query,"exm_ord_item") || strpos($query,"tb_pretty") || strpos($query,"exm_cart")) {
  
    			$logdir = dirname(__FILE__)."/../dblog/";
    			if (!is_dir($logdir)){
    				mkdir($logdir,0707);
    				chmod($logdir,0707);
    			}
    
    			$filename = date("Ymd");
    			$fp = fopen($logdir.$filename,"a");
    			$logtime = $time[1] - $time[0];
    			$logstr = 
            "
            ------------------------------------------------------------------------
            ".date("Y-m-d H:i:s")."
            query : $query
            time : $logtime
            count : $this->count
            remote_addr : $_SERVER[REMOTE_ADDR]
            ";

    			fwrite($fp,$logstr);
  			}
         ## 로그
      }

		$this->insert_id = mysql_insert_id($this->db_conn);
      $this->id = $this->insert_id;
		return $res;
      }
   }

   function fetch($res,$mode=0){
		if (!is_resource($res)) $res = $this->query($res);
		return (!$mode) ? @mysql_fetch_assoc($res) : @mysql_fetch_array($res);
   }
    
    
   function listFetch($sql){   
      $loop = array();
      $res = $this->query($sql);
       
      if ($this->count == 1)
         //return @mysql_fetch_array($res);
         return @mysql_fetch_assoc($res);
      else {       
         //while ($data = @mysql_fetch_array($res)){        
         while ($data = @mysql_fetch_assoc($res)){
            $loop[] = $data;
         }     
         return $loop;
      }
   }
  
  
   function listArray($sql) {   
      $loop = array();
      $res = $this->query($sql);      
           
      //while ($data = @mysql_fetch_array($res)){        
      while ($data = @mysql_fetch_assoc($res)){
         $loop[] = $data;
      }     
      return $loop;    
   }

	function count_($result){
      $rows = mysql_num_rows($result);
      if ($rows == null) $rows = 0;
	  return $rows;
   }

	function close(){
      $ret = @mysql_close($this->db_conn);
      $this->db_conn = null;
      return $ret;
   }

	function error($err){
      if ($this->error_pass){
			$this->error = 1;
			return;
		}
      
		$errmsg = mysql_error();
      
      //회사에서 접속한경우와 서버가 로컬인경우만 db오류 보여주기..    20150130
      if (($_SERVER[REMOTE_ADDR]=="210.96.184.229" || strpos($_SERVER[SERVER_ADDR], "192.168.0.") !== false)) {
  		  echo "
  		  <div style='padding:2'>
  		  <table width=100% border=1 bordercolor='#cccccc' style='border-collapse:collapse;font:9pt Courier New'>
  		  <col width=100 style='padding-right:10;text-align:right;font-weight:bold'><col style='padding:3 0 3 10'>
  		  <tr><td bgcolor=#f0f0f0>error</td><td>$errmsg</td></tr>
  		  ";
  		  foreach ($err as $k=>$v) echo "<tr><td bgcolor=#f0f0f0>$k</td><td>$v</td></tr>";
  		  echo "</table></div>";
      } else {
         echo "<div style='padding:2'>"._("처리 과정중 오류가 발생되었습니다.사이트 관리자에 문의주세요.")."</div>";
      }
    
		
		if ($this->btran){
			$this->btran_err = true;
			$this->end_transaction();
		}
		$this->close();

      ## 로그
		$logdir = dirname(__FILE__)."/../dblog/";
		if (!is_dir($logdir)){
			mkdir($logdir,0707);
			chmod($logdir,0707);
		}

		$filename = date("Ymd")."_err";
		$fp = fopen($logdir.$filename,"a");
		$logstr = "------------------------------------------------------------------------
      ".date("Y-m-d h:i:s")."
      query : $err[query]
      remote_addr : $_SERVER[REMOTE_ADDR]
      error_message : $errmsg
      ";
		foreach ($err as $k=>$v) $logstr .= "$k : $v\r\n";
	 	fwrite($fp,$logstr);
      ## 로그


		exit();
   }

	function viewLog(){
		echo "
		<table width=800 border=1 bordercolor='#cccccc' style='border-collapse:collapse;font:8pt tahoma'>
		<tr bgcolor='#f7f7f7'>
			<th width=40 nowrap>no</th>
			<th width=100%>query</th>
			<th width=80 nowrap>time</th>
		</tr>
		<col align=center><col style='padding-left:5'><col align=center>
		";
		foreach ($this->log as $k=>$v){
			echo "
			<tr>
				<td>".++$idx."</td>
				<td>$v</td>
				<td>{$this->time[$k]}</td>
			</tr>
			";
		}
		echo "
		<tr bgcolor='#f7f7f7'>
			<td>total</td>
			<td></td>
			<td>".array_sum($this->time)."</td>
		</tr>
		</table>
		";
	}

	### microtime 실수형 변환
	function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function start_transaction(){
		$this->btran = true;
		$this->query("start transaction");
	}

	function end_transaction(){
		if ($this->btran==true){
			if ($this->btran_err){
				$this->query("rollback");
			} else {
				$this->query("commit");
			}
		}
	}
   
   function end_trans($command = "commit"){
      $this->query($command);
      //$this->query("rollback");
      //$this->query("commit");      
   }
}

?>