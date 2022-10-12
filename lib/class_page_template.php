<?

/**
 * Page Template class
 * 2014.01.23
 * PODs2에 사이트코드,사이트상품코드,카테고리 정보를 연동하여 템플릿 리스트로 페이지를 구성한다.
 * kdk
 */

class PageTemplate{

    var $page   = array();
    /*
    $page[now]      현재 페이지
    $page[num]      한 페이지에 출력되는 레코드 개수
    $page[total]    전체 페이지 수
    $page[url]      페이지 링크 URL
    $page[navi]     페이지 네비게이션
    $page[prev]     이전페이지 아이콘
    $page[next]     다음페이지 아이콘
    */
    var $recode = array();
    /*
    $recode[start]  시작 레코드 번호
    $recode[total]  전체 레코드 수 (전체 글수)
    */

    var $vars       = array();
    var $nolimit    = false;        // 참일 경우 전체 데이타 추출
    var $idx        = 0;            // 해당페이지 첫번쩨 레코드 번호값

    function PageTemplate($page=1,$page_num=20){
    	global $cfg;
		
        $this->vars[page]= str_replace("&catno=0&orderby=c.sort", "", getQueryString('chk,page,password,x,y')); //&catno=0&orderby=c.sort
        $this->page[now] = ($page<1) ? 1 : $page;
        $this->page[num] = ($page_num<1) ? 20 : $page_num;
		
        $this->page[url] = $_SERVER['PHP_SELF'];
        $this->recode[start] = ($this->page[now]-1) * $this->page[num];
        $this->page[prev] = "&lsaquo;";
		$this->page[next] = "&rsaquo;";
		
		$this->layout_top = $cfg[layout][top];
		
    	if ($this->layout_top == 'interpro')
    	{
			$this->page[prev] = "<img src=\"/skin/modern/assets/images/interpro/ic_chevron_left.png\" alt=\"left\">Prev&nbsp;&nbsp;|";
		  	$this->page[next] = "|&nbsp;&nbsp;Next<img src=\"/skin/modern/assets/images/interpro/ic_chevron_right.png\" alt=\"right\">";
    	}
    }

    function setTemplateTotal($total){
        if (!isset($this->recode[total])){
            $this->recode[total] = $total;
        }
        $this->idx = $this->recode[total] - $this->recode[start];
    }

    function execTemplate(){

		$this->page[total]  = @ceil($this->recode[total]/$this->page[num]);
		if ($this->page[total] && $this->page[now]>$this->page[total]) $this->page[now] = $this->page[total];
		
        //$this->flag = "#catnav";
        
		if ($this->layout_top == 'interpro') {
	        
	        $page[start] = (ceil($this->page[now]/5)-1)*5;
	
	        if($this->page[now]>5){
	            $navi .= "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class='arrow'>{$this->page[prev]}</a></li>";
	        }
	
	        while($i+$page[start]<$this->page[total]&&$i<5){
	            $i++;
	            $page[move] = $i+$page[start];
	            $r_navi[] = ($this->page[now]==$page[move]) ? "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\"  class='selected'>$page[move]</a></li>" : "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\">$page[move]</a></li>";
	        }		
			
	        $navi .= @implode("",$r_navi);
	
	        if($this->page[total]>$page[move]){
	            $page[next] = $page[move]+1;
	            $navi .= "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\">{$this->page[next]}</a></li>";
	        }
			
		}
        else if ($this->layout_top == 'alaskaprint') {
            /*
                <a href="#" class="first"></a>
                <a href="#" class="prev"></a>
                <a href="#">1</a>
                <a href="#">2</a>
                <a href="#" class="on">3</a>
                <a href="#">4</a>
                <a href="#">5</a>
                <a href="#">6</a>
                <a href="#">7</a>
                <a href="#">8</a>
                <a href="#">9</a>
                <a href="#">10</a>
                <a href="#" class="next"></a>
                <a href="#" class="last"></a> 
            */
            $page[start] = (ceil($this->page[now]/5)-1)*5;
    
            if($this->page[now]>5){
                $navi .= "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class='prev'></a>";
            }
    
            while($i+$page[start]<$this->page[total]&&$i<5){
                $i++;
                $page[move] = $i+$page[start];
                $r_navi[] = ($this->page[now]==$page[move]) ? "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\" class='on'>$page[move]</a>" : "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\">$page[move]</a>";
            }       
            
            $navi .= @implode("",$r_navi);
    
            if($this->page[total]>$page[move]){
                $page[next] = $page[move]+1;
                $navi .= "<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\" class='next'></a>";
            }
                    
        }
		else {

	        $page[start] = (ceil($this->page[now]/10)-1)*10;
	
	        if($this->page[now]>10){
	            $navi .= "
	  			<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=1{$this->flag}\">1</a></li>
	  			<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[start]{$this->flag}\" class='arrow'>{$this->page[prev]}</a></li>
	  			";
	        }
	
	        while($i+$page[start]<$this->page[total]&&$i<10){
	            $i++;
	            $page[move] = $i+$page[start];
	            $r_navi[] = ($this->page[now]==$page[move]) ? "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\"  class='selected'>$page[move]</a></li>" : "<li><a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[move]{$this->flag}\">$page[move]</a></li>";
	        }		
			
	        $navi .= @implode(" <font color='#C3C3C3'>/</font> ",$r_navi);
	
	        if($this->page[total]>$page[move]){
	            $page[next] = $page[move]+1;
	            $navi .= "
				<a href=\"{$this->page[url]}?{$this->vars[page]}&page=$page[next]{$this->flag}\">{$this->page[next]}</a>
				<a href=\"{$this->page[url]}?{$this->vars[page]}&page={$this->page[total]}{$this->flag}\">{$this->page[total]}</a>
				";
	        }

	 		$navi = "<div class='list-pagination'><ul>$navi</ul></div>";

		}

        if ($this->recode[total] && !$this->nolimit) $this->page[navi] = &$navi;
    }
}

?>