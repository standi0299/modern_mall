<?

/**
 * Page class
 * 2009.05.09
 */

class Page{

	var $page	= array();
	/*
	$page[now]		현재 페이지
	$page[num]		한 페이지에 출력되는 레코드 개수
	$page[total]	전체 페이지 수
	$page[url]		페이지 링크 URL
	$page[navi]		페이지 네비게이션
	$page[prev]		이전페이지 아이콘
	$page[next]		다음페이지 아이콘
	*/
	var $recode	= array();
	/*
	$recode[start]	시작 레코드 번호
	$recode[total]	전체 레코드 수 (전체 글수)
	*/

	var $vars		= array();
	var $field		= "*";			// 가져올 필드
	var $cntQuery	= "";			// 전체 레코드 개수 가져올 쿼리문 (조인시 성능 향상을 위해)
	var $nolimit	= false;		// 참일 경우 전체 데이타 추출
	var $idx		= 0;			// 해당페이지 첫번쩨 레코드 번호값
	var $mode = 'default';
	var $class_tag = '';


   function Page($page=1,$page_num=20, $page_mode='default', $page_class_tag = "") {
      global $cfg;

		$this->vars[page]= getQueryString('chk,page,password,x,y');
		$this->page[now] = ($page<1) ? 1 : $page;
		$this->page[num] = ($page_num<1) ? 20 : $page_num;
		$this->page[url] = $_SERVER['PHP_SELF'];
		$this->recode[start] = ($this->page[now]-1) * $this->page[num];
      $this->mode = $page_mode;

      if ($this->mode == 'default')
      {
         $this->page[prev] = "&lsaquo;";
         $this->page[next] = "&rsaquo;";
      }
		$this->class_tag = $page_class_tag;
		$this->db = &$GLOBALS[db];
	}

	function setTotal(){
      if (!isset($this->recode[total])){
         $this->resource = $this->db->query($this->query);
			list ($this->recode[total]) = $this->db->fetch("select found_rows()",1);
		}
		$this->idx = $this->recode[total] - $this->recode[start];
	}

	function setQuery($db_table,$where='',$orderby='',$tmp='')
	{
		$this->db_table = $db_table;
      $this->tmpQry = $tmp;
      if ($where) $this->where = "where ".implode(" and ",$where);
      if (trim($orderby)) $this->orderby = "order by ".$orderby;
      if (!isset($this->recode[total])) $sql_calc_found_rows = "sql_calc_found_rows";
      $this->query = "select $sql_calc_found_rows $this->field from $this->db_table $this->where $this->tmpQry $this->orderby limit {$this->recode[start]},{$this->page[num]}";
		//debug($this->query);
	}
    
   //스튜디오 2.0에서 groupby가 필요해서 추가 / 14.03.24 / kjm  
   function setQuery_studio($db_table,$where='',$orderby='',$groupby='',$tmp=''){
      $this->db_table = $db_table;
      $this->tmpQry = $tmp;
      if ($where) $this->where = "where ".implode(" and ",$where);
      if (trim($orderby)) $this->orderby = "order by ".$orderby;
      if (trim($groupby)) $this->groupby = "group by ".$groupby;
      if (!isset($this->recode[total])) $sql_calc_found_rows = "sql_calc_found_rows";
      $this->query = "select $sql_calc_found_rows $this->field from $this->db_table $this->where $this->tmpQry $this->orderby $this->groupby limit {$this->recode[start]},{$this->page[num]}";
	}
	
	//20181026 / minks / P1테마 주문리스트에서 사용
	function setQuery_P1($db_table,$where='',$groupby='',$orderby='',$tmp=''){
	  $this->db_table = $db_table;
      $this->tmpQry = $tmp;
      if ($where) $this->where = "where ".implode(" and ",$where);
      if (trim($groupby)) $this->groupby = "group by ".$groupby;
      if (trim($orderby)) $this->orderby = "order by ".$orderby;
      if (!isset($this->recode[total])) $sql_calc_found_rows = "sql_calc_found_rows";
      $this->query = "select $sql_calc_found_rows $this->field from $this->db_table $this->where $this->tmpQry $this->groupby $this->orderby limit {$this->recode[start]},{$this->page[num]}";
	}

   function exec(){
		$this->setTotal();

		$this->page[total] = @ceil($this->recode[total]/$this->page[num]);
		if ($this->page[total] && $this->page[now]>$this->page[total]) $this->page[now] = $this->page[total];
		$page[start] = (ceil($this->page[now]/10)-1)*10;

  	   $this->flag = "#catnav";

		if($this->page[now]>10)
		{
   	  	if ($this->mode == 'default')
			{
	  		$navi .= "
	  			<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=1{$this->flag}\">1</a></li>
	  			<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class='arrow'>{$this->page[prev]}</a></li>
	  		";

				$navi_m2 .= "
	  			<a href=\"{$this->page[url]}?{$this->vars[page]}&page=1{$this->flag}\" class='first'></a>
	  			<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class='prev'></a>
	  		";
				
				$navi_p1 .= "	  			
	  			<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class='number_le'><i class='icono-caretLeftSquare'></i></a>
	  		";
         }      
      }

		while($i+$page[start]<$this->page[total]&&$i<10){
         $i++;
			$page[move] = $i+$page[start];
         if ($this->mode == 'default')
         {
				$r_navi[] = ($this->page[now]==$page[move]) ? "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\"  class='selected'>$page[move]</a></li>" : "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\">$page[move]</a></li>";
				$r_navi_m2[] = ($this->page[now]==$page[move]) ? "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\"  class='on'>$page[move]</a>" : "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\">$page[move]</a>";				
				$r_navi_p1[] = ($this->page[now]==$page[move]) ? "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\"  class='number_le1'>$page[move]</a>" : "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\" class='number_le'>$page[move]</a>";
     	   }
		}

      if ($this->mode == 'default')
      {
			$navi .= @implode("",$r_navi);
			$navi_m2 .= @implode("",$r_navi_m2);
			$navi_p1 .= @implode("",$r_navi_p1);
      }

		if($this->page[total]>$page[move]){
			$page[next] = $page[move]+1;
         if ($this->mode == 'default')
         {
            $navi .= "
               <a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\">{$this->page[next]}</a>
  				   <a href=\"{$this->page[url]}?{$this->vars[page]}&page={$this->page[total]}{$this->flag}\">{$this->page[total]}</a>
            ";

            $navi_m2 .= "
               <a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\" class='next'></a>
               <a href=\"{$this->page[url]}?{$this->vars[page]}&page={$this->page[total]}{$this->flag}\" class='last'></a>
            ";

				$navi_p1 .= "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\" class='number_le'><i class='icono-caretRightSquare'></i></a>";
         }			
		}
    
		if ($this->class_tag)
		{
			$navi = "<div class='{$this->class_tag}'><ul>$navi</ul></div>";
			$navi_m2 = "<div class='{$this->class_tag}'>$navi_m2</div>";
		}
		else 
		{
         if ($this->mode == 'default')
			{
			  $navi = "<div class='list-pagination'><ul>$navi</ul></div>";
				$navi_m2 = "<div class='paging-wrap'>$navi_m2</div>";
			}
		}
			
		if ($this->recode[total] && !$this->nolimit) 
		{
			$this->page[navi] = &$navi;
			$this->page[navi_m2] = &$navi_m2;
			$this->page[navi_p1] = &$navi_p1;
      }
	}
}

?>