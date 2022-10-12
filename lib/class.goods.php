<?
/*
* @date : 20180829		
* @author : chunter
* @brief :  필터 검색기능 추가, ,search_word 필드에서 찾는다.
* @desc : "|" 문자로 구분해서 or 연산이 가능하다. 2차, 3차 필터 기능 개발.
 
* @date : 20180314
* @author : kdk
* @brief :  Hot,추천,연관 상품 정보 조회시 사용여부 조건 추가.
* @request :
* @desc : 쿼리 추가 ( and regist_flag !='N' )
* @todo :
*/

/**
 * Goods class
 * 2017.05.12 by kdk
 * list.php, list_templateset.php, list_template.php view.php 에 필요한 정보 조회 및 데이타를 구성한다.
 */
?>
<?
class Goods {
    var $db;
    var $cid;
    var $sess;
    var $cfg;
    var $cfg_center;
    var $host;

    var $m_goods;

    //파라메타.
    var $catno;
    var $goodsno;

    //상품카테고리.
    var $category = array();

    //리스트 페이지 데이타.
    var $listPage;

    //리스트 데이타.
    var $listData;

    //리스트 템플릿 데이타.
    var $listTemplateData;
    var $listTemplateDataRet;

    //상세보기 데이타.
    var $viewData;

    //상세보기 파일경로.
    var $viewInc;

    //편집기정보.
    var $editor = array();

    //템플릿셋 url.
    var $temsetUrl = "/CommonRef/StationWebService/tempset_product_list2.aspx?";

    //템플릿 url.
    var $temUrl = "/CommonRef/StationWebService/temp_product_list2.aspx?";

    //메인 블럭 컨텐츠 정보.
    var $mainBlockContentData;

    //메인 블럭 정보.
    var $mainBlockData;
		
		var $customLimit;			//limit 갯수를 외부에서 조정한다.

    function Goods() {
        $this -> db = $GLOBALS[db];
        $this -> cid = $GLOBALS[cid];
        $this -> sess = $GLOBALS[sess];
        $this -> cfg = $GLOBALS[cfg];
        $this -> cfg_center = $GLOBALS[cfg_center];
        $this -> host = $this -> cfg_center[host];
		$this -> cfg_theme = $GLOBALS[skin_theme];
		
        $this -> m_goods = new M_goods();

        //debug($this->cid);
        //debug($this->cfg_center);
    }

    //main/index.php
    function getMain() {
        //메인 블럭 컨텐츠 정보 조회.
        $mcdata = $this -> m_goods -> getMainBlockContentList($this -> cid);
        if ($mcdata) {
            $mcdata[img] = explode("||", $mcdata[img]);
            $mcdata[img_t] = explode("||", $mcdata[img_t]);
            $mcdata[img_t_on] = explode("||", $mcdata[img_t_on]);
            $mcdata[img_b] = explode("||", $mcdata[img_b]);
            $mcdata[url] = explode("||", $mcdata[url]);
            $mcdata[target] = explode("||", $mcdata[target]);
            $mcdata[idx] = count($mcdata[img]);
            $mcdata[slide_speed] = ($mcdata[slide_speed]) ? $mcdata[slide_speed] * 1000 : 5000;
			$mcdata[title] = str_replace("\"", "&quot;", stripslashes($mcdata[title]));
			$mcdata[title] = explode("||", $mcdata[title]);

            $this -> mainBlockContentData = $mcdata;
        }

        //메인 블럭 정보 조회.
        $data = $this -> m_goods -> getMainBlockList($this -> cid);
         if ($data) {
            foreach ($data as $key => $val) {
                //debug($val);
   
               if ($val[display_data] == "2") {
                  //<img src="/data/main_block/{_cid}/{_blockDataArr.main_block_01.display_img}">
                  $data[$key][display] = "<img src=\"/data/main_block/$this->cid/$val[display_img]\">";
               } else {
                  $data[$key][display] = "<h2>$val[display_text]</h2>";
               }

                //$data[$key][goods] = $this->m_goods->getMainBlockGoodsList($this->cid, $val[block_code]);
                $bdata = $this -> m_goods -> getMainBlockGoodsListWithGoodsInfo($this -> cid, $val[block_code], $val[order_by], $this->sess[mid], $this->sess[bid]);
                //debug($bdata);
                foreach ($bdata as $bkey => $gdata) {
                    //debug($bval);
                    //상품상세정보 조회.
                    //$bid_where = ($this -> sess[bid]) ? "(bid.bid is null or bid.bid = '$this->sess[bid]')" : "bid.bid is null";
                    //$gdata = $this -> m_goods -> getViewInfo($this -> cid, $bval[goodsno], $bid_where);

                    $gdata[cprice] = $gdata[mall_cprice];

                    if ($gdata[cprice] == $gdata[price])
                        $gdata[cprice] = 0;
                    if ($gdata[usestock] && $gdata[totstock] < 1 && $gdata[state] == 0) {
                        $gdata[state] = 1;
                    }

                    if ($gdata[strprice])
                        $gdata[state] = 1;

                    if ($this -> sess[bid])
                        $gdata[price] = get_business_goods_price($gdata[goodsno], $gdata[price]);
                    if ($this -> sess[bid])
                        $gdata[reserve] = get_business_goods_reserve($gdata[goodsno], $gdata[reserve]);

                    if ($gdata[clistimg]) {
                        //$gdata[clistimg] = "http://$this->host/data/goods/$this->cid/s/$gdata[goodsno]/".$gdata[clistimg];
                        $gdata[clistimg] = $this -> get_listimgsrc($gdata[goodsno], $gdata[clistimg]);
                    } else {
                        /*if ($gdata[cimg]) $gdata[img] = $gdata[cimg];
                         $imgs = array_notnull(explode("||",$gdata[img]));
                         if($gdata[imgs]) {
                         $gdata[clistimg] = "http://$this->host/data/goods/$this->cid/l/$gdata[goodsno]/".$imgs[0];
                         }*/
                        if ($gdata[cimg])
                            $gdata[img] = $gdata[cimg];
                        $gdata[clistimg] = $this -> get_imgsrc($gdata[goodsno], $gdata[img]);
                    }

                    //상품 해쉬태그 링크 처리.
                    //if ($gdata[hash_tag]) $gdata[hash_tag] = $this->get_hash_tag($gdata[hash_tag]);
                    if ($data[csearch_word] == "")
                        $data[csearch_word] = $data[search_word];
                    if ($gdata[csearch_word])
                        $gdata[hash_tag] = $this -> get_hash_tag($gdata[csearch_word]);
                    //csearch_word 임시사용.
                    
                    
                    if ($gdata[icon_filename])
            					$gdata[icon_filename] = array_notnull(explode("||", $gdata[icon_filename]));
                    
                    $data[$key][goods][] = $gdata;
                }
            }
        }

        $this -> mainBlockData = $data;
    }

    //list.php
    function getList() {
        include_once "../lib/class.page.php";

        $this -> catno = $_GET[catno];

        $this -> get_category($this -> catno);

        if ($this -> category[cells] && $this -> category[rows]) {
            $this -> cfg[cells] = $this -> category[cells];
            $this -> cfg[rows] = $this -> category[rows];
        }

         if (!$_GET[orderby])
            $_GET[orderby] = "c.sort,a.goodsno";
         if (!$_GET[page_num])
            $_GET[page_num] = $this -> cfg[rows];

         if ($this->customLimit)
				 	$limit = $this->customLimit;
				 else
         	$limit = $this -> cfg[cells] * $_GET[page_num];

         $db_table = "
            exm_goods a
            inner join exm_goods_cid b on b.cid = '$this->cid' and b.goodsno = a.goodsno
            inner join exm_category_link c on c.cid = '$this->cid' and c.goodsno = a.goodsno
            left join exm_goods_bid bid on bid.cid = '$this->cid' and bid.goodsno = a.goodsno				
         ";
        
        //우선순위 처리 필요함.
        $orderby = $_GET[orderby];
        if ($_GET[orderby] == "b.hit desc") {
            $sort = "popular";
        }
        else if ($_GET[orderby] == "b.goodsno desc") {
            $sort = "new";
        }
        $data = $this -> m_goods -> getMdGoodsSortList($this->cid, $this->catno, $sort);
        if($data) {
            $addField = ",if(md.priority is null,99999,md.priority) priority";
            $db_table .= " left join md_goods_sort md on md.cid = '$this->cid' and md.catno = '$this->catno' and md.goodsno = a.goodsno and md.sort = '$sort'";
            $orderby = "priority asc";
        }

        //wish 등록 상품 조인하기.			20170922		chunter
        if ($this -> sess[mid])
            $db_table .= " left join md_wish_list wgood on wgood.cid = '$this->cid' and wgood.mid='{$this->sess[mid]}' and wgood.goodsno = a.goodsno";
            
         $db_table .= " left join md_goods_like d on a.goodsno = d.goodsno and d.cid = '$this->cid' and d.mid = '{$this->sess[mid]}'";

        //debug($sql);
        $where[] = ($this -> sess[bid]) ? "(bid.bid is null or bid.bid = '$this->sess[bid]')" : "bid.bid is null";
        $where[] = "c.catno = '$_GET[catno]'";
        $where[] = "b.nodp = 0";
        $where[] = "state < 2";
				
				
				
				//필터 검색 기능 추가			20180829		chunter
				if($_GET[filter])
				{
					//$filter_data = md5_decode($getData[filter]);	
					//$filterWhere = "search_word like '{$_GET[filter]}'";
					$filterWhere = "";
					$filter_and_arr = explode("|", $_GET[filter]);
					foreach ($filter_and_arr as $key => $value) {
						if ($value)
						{
							if ($filterWhere)
								$filterWhere .= " or a.search_word like '%{$value}%'";
							else 
								$filterWhere = " (a.search_word like '%{$value}%'";
						}
					}
					if ($filterWhere)
						$where[] = $filterWhere .")";				
				}
				//debug($where);
				//exit;
				
				if($_GET[seconfilter])
				{
					//$filter_and_data = md5_decode($getData[filter_and]);
					$filter_and_arr = explode("|", $_GET[seconfilter]);
					$filterWhere = "";
					foreach ($filter_and_arr as $key => $value) {
						if ($value)
						{
							if ($filterWhere)
								$filterWhere .= " or a.search_word like '%{$value}%'";
							else 
								$filterWhere = " (a.search_word like '%{$value}%'";
						}
					}
					if ($filterWhere)
						$where[] = $filterWhere .")";
				}
				
				if($_GET[thirdfilter])
				{
					//$filter_and_data = md5_decode($getData[filter_and]);
					$filter_and_arr = explode("|", $_GET[thirdfilter]);
					$filterWhere = "";
					foreach ($filter_and_arr as $key => $value) {
						if ($value)
						{
							if ($filterWhere)
								$filterWhere .= " or a.search_word like '%{$value}%'";
							else 
								$filterWhere = " (a.search_word like '%{$value}%'";
						}
					}
					if ($filterWhere)
						$where[] = $filterWhere .")";
				}
				
							
				

        $pg = new Page($_GET[page], $limit);
        if ($this -> sess[mid])
            $pg -> field = "a.*,b.*,c.*,if(b.price is null,a.price,b.price) price,if(b.goods_desc is null,a.goods_desc,b.goods_desc) goods_desc, wgood.no wishlist_no, d.goods_like $addField";
        else
            $pg -> field = "a.*,b.*,c.*,if(b.price is null,a.price,b.price) price,if(b.goods_desc is null,a.goods_desc,b.goods_desc) goods_desc $addField";		
        $pg -> setQuery($db_table, $where, $orderby);
        $pg -> exec();
        $res = &$pg -> resource;

        $loop = array();
        while ($data = $this -> db -> fetch($res)) {
            //debug($data);
            if ($this -> sess[bid])
                $data[price] = get_business_goods_price($data[goodsno], $data[price]);
            if ($this -> sess[bid])
                $data[reserve] = get_business_goods_reserve($data[goodsno], $data[reserve]);
            $data[cprice] = $data[mall_cprice];

            //상품 옵션 확인(pod_group관련 iphotobook) 2014.07.28 by kdk
            $r_addopt = $this -> m_goods -> getGoodsAddOptList($data[goodsno]);
            $data[r_addopt] = $r_addopt;

            if ($data[clistimg]) {
                //$data[clistimg] = "http://$this->host/data/goods/$this->cid/s/$data[goodsno]/".$data[clistimg];
                $data[clistimg] = $this -> get_listimgsrc($data[goodsno], $data[clistimg]);
            } else {
                /*if ($data[cimg]) $data[img] = $data[cimg];
                 $imgs = array_notnull(explode("||",$data[img]));
                 if($data[imgs]) {
                 $data[clistimg] = "http://$this->host/data/goods/$this->cid/l/$data[goodsno]/".$imgs[0];
                 }*/
                if ($data[cimg])
                    $data[img] = $data[cimg];
                $data[clistimg] = $this -> get_imgsrc($data[goodsno], $data[img]);
            }

            //상품 해쉬태그 링크 처리.
            //if ($data[hash_tag]) $data[hash_tag] = $this->get_hash_tag($data[hash_tag]);
            if ($data[csearch_word] == "")
                $data[csearch_word] = $data[search_word];
            if ($data[csearch_word])
                $data[hash_tag] = $this -> get_hash_tag($data[csearch_word]);
            //csearch_word 임시사용.

            //상품설명
            if ($data[goods_desc])
                $data[goods_desc] = unserialize($data[goods_desc]);

            //간략설명
            if ($data[csummary] == "")
                $data[csummary] = $data[summary];

            //wish 등록 상품이 있을경우 아이콘 표시			20170922		chunter
            if ($data[wishlist_no])
                $data[wishlist_check] = "is-pick";

            //찜(wishlist)리스트 상품 체크			//개별 상품 select로 DB 부하 초래...조인으로 변경		20170922	chunter
            //if($this->sess) {
            //$data[wishlist_check] = $this->get_check_wish_goods($this->sess[mid], $data[goodsno]);
            //}

            //아이콘 추가.
            if ($data[icon_filename])
                $data[icon_filename] = array_notnull(explode("||", $data[icon_filename]));

            $loop[] = $data;
        }

        $this -> listPage = $pg;
        $this -> listData = $loop;
    }

    //list_templateset.php,list_template.php
    function getListTemplate($bTempSet = False) {
        include_once "../lib/class_page_template.php";

        $this -> goodsno = $_GET[goodsno];
        if ($_GET[catno])
            $this -> catno = $_GET[catno];

        //상품정보 조회.
        $data = $this -> m_goods -> getGoodsInfo($this -> cid, $this -> goodsno);

        # podstation siteid
        if ($data[pods_useid] == "center" && $data[podsno]) {
            # 센터 상품일 경우.

            // $this->cfg_center[podsiteid] 센터ID를 p_siteid 파라메타에 추가로 사용. / 2014.02.06 / kdk
            $p_siteid = $this -> cfg_center[podsiteid];
            //공유상품을 사용하는 경우 공유해주는 사이트아이디 (pods 상품 소유 site id)
            $data[podsiteid] = $this -> cfg[podsiteid];
            //사이트아이디 (주문의 주체가 되는 사이트 아이디)

            if ($data[podsiteid] == "") {//센터 분양몰에 설정된 값이 없을 경우  센터ID를 사용. / 2017.03.17 / kdk
                $data[podsiteid] = $this -> cfg_center[podsiteid];
            }
        } else {
            # 자체 상품일 경우.

            $m_mall = new M_mall();

            # 센터 분양몰 설정에 값이 없을 경우 몰에 설정된 값을 사용. / 2017.03.17 / kdk
            $mall = $m_mall -> getInfo($this -> cid);
            if ($mall[self_podsiteid]) {
                $p_siteid = $mall[self_podsiteid];
                //공유상품을 사용하는 경우 공유해주는 사이트아이디 (pods 상품 소유 site id)
            }

            if ($p_siteid == "") {//몰에 설정된 값이 없을 경우 센터 분양몰 설정을 사용. / 2017.03.17 / kdk
                $p_siteid = $this -> cfg[podsiteid];
            }

            $data[podsiteid] = $this -> cfg[podsiteid];
            //사이트아이디 (주문의 주체가 되는 사이트 아이디)

            if ($data[podsiteid] == "") {//센터 분양몰에 설정된 값이 없을 경우 몰 설정을 사용. / 2017.03.17 / kdk
                $data[podsiteid] = $mall[self_podsiteid];
            }
        }
        //debug($p_siteid);
        //debug($data[podsiteid]);
        //debug($data);

        if ($this -> catno == "")
            $this -> catno = $data[catno];

        //상품카테고리 정보 조회.
        $this -> get_category($this -> catno);
        
        //템프릿셋 여부(인터프로)
        if ($this->category[is_set]) {
            $bTempSet = TRUE;
        }

        if ($this -> category[cells] && $this -> category[rows]) {
            $this -> cfg[cells] = $this -> category[cells];
            $this -> cfg[rows] = $this -> category[rows];
        }

        if (!$_GET[orderby])
            $_GET[orderby] = "c.sort";
        if (!$_GET[page_num])
            $_GET[page_num] = $this -> cfg[rows];

        $limit = $this -> cfg[cells] * $_GET[page_num];
        if ($limit == 0)
            $limit = "";

        //인터프로
        if ($this -> cfg[layout][top] == "interpro")
            $limit = "18";

        # PODs2 파라메타
        $siteid = $data[podsiteid];
        $spid = $data[podsno];
        $categoryidx = ($_GET[categoryidx]) ? $_GET[categoryidx] : "0";
        $pagesize = $limit;
        $pagenum = ($_GET[page]) ? $_GET[page] : "1";

        //편집기정보 조회.
        $this -> get_editor($_GET[goodsno]);
        //debug($this->editor);

        $podsno = "";
        foreach ($this->editor as $key => $val) {
            if ($val[podsno] && $val[podsno] != "") {
                $podsno .= trim($val[podsno]) . ",";
            }
        }
        $spid = $podsno;
        //debug($spid);

        //debug($bTempSet);
        $soapurl = 'http://' . PODS20_DOMAIN;
        if ($bTempSet === TRUE) {
            $soapurl .= $this -> temsetUrl;
        } else {
            $soapurl .= $this -> temUrl;
        }
        //debug($soapurl);

        $param = "siteid=$siteid&p_siteid=$p_siteid&spid=$spid&categoryidx=$categoryidx&pagesize=$pagesize&pagenum=$pagenum";
        
        //검색 추가.
        $searchtype = ($_GET[searchtype]) ? $_GET[searchtype] : "";
        $searchtext = ($_GET[searchtext]) ? $_GET[searchtext] : "";
        if ($searchtype && $searchtext) {
            $param .= "&$searchtype=" . base64_encode($searchtext);
        }
        //debug($soapurl.$param);
        //exit;

        $ret = readUrlWithcurl($soapurl . $param, false);
        //debug($ret);

        $obj = json_decode($ret, TRUE);
        //debug($obj);

        $data[url] = base64_encode($soapurl . $param);
        //debug($data[url]);

        //debug($siteid);
        //debug($p_siteid);
        //debug($spid);

        //debug('$obj[success]='.$obj[success]);
        //debug('$obj[TotalCount]='.$obj[TotalCount]);
        //debug('$obj[PageSize]='.$obj[PageSize]);
        //debug('$obj[PageNum]='.$obj[PageNum]);

        if ($searchtype && $searchtext) {
            if ($obj[TotalCount] < 1) {
                //msg('검색 결과가 없습니다.', -1);
            }
        }
        //debug($obj[TotalCount]);

        //debug($data);
        //상품 옵션 확인.
        $data = $this -> get_opt_priceinfo($data[goodsno], $data);
        //debug($data);

        //상품 추가 옵션 확인.
        //$data = $this -> get_addopt_priceinfo($data[goodsno], $data);
        //debug($data);

        $pg = new PageTemplate($_GET[page], $limit);
        $pg -> setTemplateTotal($obj[TotalCount]);
        $pg -> execTemplate();

        $this -> listPage = $pg;
        $this -> listData = $data;
        $this -> listTemplateData = $obj[data];
        $this -> listTemplateDataRet = $ret;
    }

    //view.php
    function getView($goodsno = '', $catno = '') {
        	
		if ($goodsno)
			$this -> goodsno = $goodsno;
		else
        	$this -> goodsno = $_GET[goodsno];
        
        if ($catno)
            $this -> catno = $catno;
        else
        	$this -> catno = $_GET[catno];

        //카테고리번호 조회.
        if (!$this -> catno) {
            list($_GET[catno]) = $this -> m_goods -> getCatno($this -> cid, $this -> goodsno);
            $this -> catno = $_GET[catno]; //템플릿리스트에서 상세보기로 넘어오면 카테고리번호가 없어 추가.
        }

        //상품카테고리 정보 조회.
        $this -> get_category($this -> catno);
        $catnm = $this -> category[catnm];
        //debug($catnm);

        //상품상세정보 조회.
        $bid_where = ($this -> sess[bid]) ? "(bid.bid is null or bid.bid = '$this->sess[bid]')" : "bid.bid is null";
        $data = $this -> m_goods -> getViewInfo($this -> cid, $this -> goodsno, $bid_where);

        $data[cprice] = $data[mall_cprice];

        if ($data[cprice] == $data[price])
            $data[cprice] = 0;
        if ($data[usestock] && $data[totstock] < 1 && $data[state] == 0) {
            $data[state] = 1;
        }

        if ($data[strprice])
            $data[state] = 1;

        if ($this -> sess[bid])
            $data[price] = get_business_goods_price($data[goodsno], $data[price]);
        if ($this -> sess[bid])
            $data[reserve] = get_business_goods_reserve($data[goodsno], $data[reserve]);

        ### 추가정보 배열처리
        if ($data[exp]) {
            $tmp[exp] = explode(chr(13), $data[exp]);
            unset($data[exp]);
            foreach ($tmp[exp] as $k => $v) {
                $div = explode(":", $v);
                if (!$div[1])
                    continue;
                $data[exp][] = array('name' => $div[0], 'value' => $div[1], );
            }
        }

        //debug($data);
        //상품 옵션 확인.
        $data = $this -> get_opt_priceinfo($data[goodsno], $data); 
        //debug($data);

        //상품 추가 옵션 확인.
        $data = $this -> get_addopt_priceinfo($data[goodsno], $data);
        //debug($data);

        //그룹별 제작옵션(임포지션옵션)
        $data = $this -> get_useimpositioninfo($data[goodsno], $data);
        //debug($data);

        $data[optnm][0] = $data[optnm1];
        $data[optnm][1] = $data[optnm2];

        if ($data[cimg])
            $data[img] = $data[cimg];
        $data[img] = array_notnull(explode("||", $data[img]));
        foreach ($data[img] as $key => $val) {
            $data[img][$key] = $this -> get_imgsrc($data[goodsno], $val);
        }
        //debug($data[img]);
        
        $data[cover_range] = $this -> m_goods->getCoverRangeOption($data[goodsno], "cover_id");
        
        //$data[cover_range] = $this->db->listArray("select * from md_cover_range_option where goodsno = '$data[goodsno]' order by cover_id");
        //debug($data[cover_range]);
        if($data[cover_range]) {
           if($_GET[cover_id]) {
              foreach($data[cover_range] as $key => $val) {
                 if($val[cover_id] == $_GET[cover_id])
                    $data[price] = $val[cover_goods_price];
              }
           } else 
              $data[price] = $data[cover_range][0][cover_goods_price];
        }

        //if ($data[clistimg_w]) $data[listimg_w] = $data[clistimg_w];
        //if ($data[listimg_w]) $data[listimg_w] = $this->get_listimgsrc($data[goodsno], $data[listimg_w]);
        //debug($data[listimg_w]);

        if ($data[icon_filename])
            $data[icon_filename] = array_notnull(explode("||", $data[icon_filename]));

        //20150811 / minks / 모바일일 경우 편집기 실행을 위해 siteid, p_siteid, cartkey를 조회
        if ($_GET[mobile_type] == "Y") {
            if (!$this -> cfg[podsiteid])
                $this -> cfg[podsiteid] = $this -> cfg_center[podsiteid];
            $podsiteid = $this -> cfg[podsiteid];

            if ($data[pods_useid] != "center" && $data[podsno]) {
                $m_mall = new M_mall();
                list($dummy) = $m_mall -> getSelfPodSiteId($this -> cid);
                if ($dummy)
                    $podsiteid = $dummy;
            }

            $data[siteid] = $podsiteid;
            $data[p_siteid] = $this -> cfg_center[podsiteid];
            if ($dummy)
                $data[p_siteid] = $podsiteid;

            $data[cartkey] = $_COOKIE[cartkey];

            //20160531 / minks / sess값을 암호화해서 편집기 호출시 넘김
            $data[sessdata] = makeEncrypData($this -> sess);
        }

        //편집기정보 조회.
        $this -> get_editor($this -> goodsno);
        //debug($this->editor);

        //템플릿 이미지 정보.
        if ($_GET[url])
            $data = $this -> get_templateinfo($data);

        if ($data[goods_group_code] == "30" && $this->category[goods_view] != "view_interpro") {//자동 견적(인쇄) 3.0 && 인터프로가 아니면...
            $this -> viewInc = "./_view_option.php";
        } else if ($data[goods_group_code] == "20" && $this->category[goods_view] != "view_interpro") {//자동 견적(스튜디오) 3.0 && 인터프로가 아니면...
            //debug($data[extra_option]);
            $extra_option = explode('|', $data[extra_option]);
            //항목 분리
            if (count($extra_option) > 0) {
                $extra_stu_order = $extra_option[3];
            }

            if ($extra_stu_order == "UPL") {
                //$this->viewInc = "./_view_studio.php";
            } else {
                $this -> viewInc = "./_view_option.php";
            }
        } else {
            $this -> viewInc = "./_view.php";

            //인화 옵션 정보
            $res = $this -> m_goods -> getGoodsPrintOptInfoList($this -> cid, $data[goodsno]);
            while ($tmp = $this -> db -> fetch($res)) {
                if ($this -> sess[bid])
                    $tmp[print_price] = get_business_goods_printopt_price($tmp[goodsno], $tmp[printoptnm], $tmp[print_price]);
                if ($this -> sess[bid])
                    $tmp[print_reserve] = get_business_goods_printopt_reserve($tmp[goodsno], $tmp[printoptnm], $tmp[print_reserve]);

                $data[r_printopt][] = $tmp;
            }
        }

        //회원 그룹별  할인 ,적립 가격  확인.
        if ($this -> sess[mid]) {
            $grpno = $this -> sess[grpno];

            if ($grpno) {
                list($grpnm, $grpdc, $grpsc) = $this -> m_goods -> getGroupDisSaveInfo($grpno);
                $data[dcprice] = round($data[price] * (100 - $grpdc) / 100, -1);
            } else {
                $data[dcprice] = 0;
            }
        }

        //패키지 상품 정보 조회.
        $data[addtionitem][package] = $this -> get_addtioniteminfo($data[goodsno], "P", "package");
        //추천 상품 정보 조회.
        $data[addtionitem][recomand] = $this -> get_addtioniteminfo($data[goodsno], "I", "recomand");
        //연관 상품 정보 조회.
        $data[addtionitem][relation] = $this -> get_addtioniteminfo($data[goodsno], "I", "relation");

        //상품설명
        if ($data[goods_desc])
            $data[goods_desc] = unserialize($data[goods_desc]);

        //상품 해쉬태그 링크 처리.
        //if ($data[hash_tag]) $data[hash_tag] = $this->get_hash_tag($data[hash_tag]);
        if ($data[csearch_word] == "")
            $data[csearch_word] = $data[search_word];
        if ($data[csearch_word])
            $data[hash_tag] = $this -> get_hash_tag($data[csearch_word]);
        //csearch_word 임시사용.
        
        //상품 옵션 이미지 / 2017.10.10 / kdk
        //$opt_img = $this -> m_goods -> getOptionImgList($this -> cid, $this -> goodsno);
        //if($opt_img) $data[opt_img] = json_encode($opt_img);

        //자동견 옵션 이미지 / 2017.10.18 / kdk
        $m_extra_option = new M_extra_option();
        $opt_img = $m_extra_option->getOptImgList($this -> cid, $this -> cfg_center[center_cid], $this -> goodsno);
        if($opt_img) $data[opt_img] = json_encode($opt_img);
        //debug($data[opt_img]);

        $goods_like = $this->get_goods_like($this->cid, $this -> sess[mid], $this->goodsno);
        $data[goods_like] = $goods_like;
        
        //debug($data);
        $this -> viewData = $data;
    }

    //list.php (_intro.php)
    function getIntro() {
        $this -> catno = $_GET[catno];
        $this -> get_category($this -> catno);
    }

    //검색 (해시태그 상품에만 해당)
    function getSearch($search_field, $search_word) {
        include_once "../lib/class.page.php";

        //if(!$_GET[orderby]) $_GET[orderby] = "c.sort";
        if (!$_GET[page_num])
            $_GET[page_num] = "4";

        $limit = $this -> cfg[cells] * $_GET[page_num];

        $db_table = "
		exm_goods a
		inner join exm_goods_cid b on b.cid = '$this->cid' and b.goodsno = a.goodsno
		#inner join exm_category_link c on c.cid = '$this->cid' and c.goodsno = a.goodsno
		left join exm_goods_bid bid on bid.cid = '$this->cid' and bid.goodsno = a.goodsno
		";

        $where[] = ($this -> sess[bid]) ? "(bid.bid is null or bid.bid = '$this->sess[bid]')" : "bid.bid is null";
        //$where[] = "c.catno = '$_GET[catno]'";
        if ($search_field && $search_word)
            $where[] = " concat($search_field) like '%$search_word%' ";

        $where[] = "b.nodp = 0";
        $where[] = "state < 2";

        $pg = new Page($_GET[page], $limit);
        //$pg->field = "a.*,b.*,c.*,if(b.price is null,a.price,b.price) price";
        $pg -> field = "a.*,b.*,if(b.price is null,a.price,b.price) price,(select catno from exm_category_link where cid='$this->cid' and goodsno=a.goodsno order by sort limit 1) catno";
        $pg -> setQuery($db_table, $where, $_GET[orderby]);
        $pg -> exec();
        $res = &$pg -> resource;

        $loop = array();
        while ($data = $this -> db -> fetch($res)) {
            if ($this -> sess[bid])
                $data[price] = get_business_goods_price($data[goodsno], $data[price]);
            if ($this -> sess[bid])
                $data[reserve] = get_business_goods_reserve($data[goodsno], $data[reserve]);
            $data[cprice] = $data[mall_cprice];

            //상품 옵션 확인(pod_group관련 iphotobook) 2014.07.28 by kdk
            $r_addopt = $this -> m_goods -> getGoodsAddOptList($data[goodsno]);
            $data[r_addopt] = $r_addopt;

            if ($data[clistimg]) {
                //$data[clistimg] = "http://$this->host/data/goods/$this->cid/s/$data[goodsno]/".$data[clistimg];
                $data[clistimg] = $this -> get_listimgsrc($data[goodsno], $data[clistimg]);
            } else {
                if ($data[cimg])
                    $data[img] = $data[cimg];
                $data[clistimg] = $this -> get_imgsrc($data[goodsno], $data[img]);
            }

            //상품 해쉬태그 링크 처리.
            //if ($data[hash_tag]) $data[hash_tag] = $this->get_hash_tag($data[hash_tag]);
            if ($data[csearch_word] == "")
                $data[csearch_word] = $data[search_word];
            if ($data[csearch_word])
                $data[hash_tag] = $this -> get_hash_tag($data[csearch_word]);
            //csearch_word 임시사용.

            $loop[] = $data;
        }

        $this -> listPage = $pg;
        $this -> listData = $loop;
    }

    //Hot 상품 조회.
    function getHotItem($catno) {
        $data = $this -> get_addtioniteminfo($catno, "C", "hot", "4");
        if ($data) {
            foreach ($data as $key => $val) {

                if ($val[clistimg])
                    $data[$key][clistimg] = $this -> get_listimgsrc($val[goodsno], $val[clistimg]);
                else
                    $data[$key][clistimg] = $this -> get_imgsrc($val[goodsno], $val[listimg]);

                //wish 등록 상품이 있을경우 아이콘 표시			20170922		chunter
                if ($val[wishlist_no])
                    $data[$key][wishlist_check] = "is-pick";

                //DB부하로 인해 select 에서 처리하도록 변경			20170922		chunter
                //$c_goods = $this->m_goods->getGoodsCidInfo($this->cid, $val[goodsno]);
                //$data[$key][clistimg] = $this->get_listimgsrc($val[goodsno], $c_goods[clistimg]);

                //찜(wishlist)리스트 상품 체크		//DB 부하로 인해 join 으로 변경			20170922		chunter
                //if($this->sess) {
                //$data[$key][wishlist_check] = $this->get_check_wish_goods($this->sess[mid], $val[goodsno]);
                //}
            }
        }
        return $data;
    }

    //상품카테고리 조회.
    private function get_category($catno) {
        $category = $this -> m_goods -> getCategoryInfo($this -> cid, $catno);

        if ($category[header]) {
            $category[header] = str_replace('&lt;', '<', $category[header]);
            $category[header] = str_replace('&gt;', '>', $category[header]);
            $category[header] = str_replace('&amp;', '&', $category[header]);
            $category[header] = str_replace('&nbsp;', ' ', $category[header]);
        }

        if ($category[goods_list] == "")
            $category[goods_list] = "list";
        if ($category[goods_view] == "")
            $category[goods_view] = "view";

        $this -> category = $category;
    }

    //편집기정보 조회.
    private function get_editor($goodsno) {
        //복수 편집기 실행 관련.
        include_once dirname(__FILE__)."/class.pods.service.php";
        $pods = new PodsEditor($goodsno);

        //상품에 설정된 pods편집기정보(복수편집기) 조회.
        $this -> editor = $pods -> GetPodsEditor();
        //debug($this->editor);
    }

    //상품 옵션 확인.
    private function get_opt_priceinfo($goodsno, $data) {
        //상품 옵션 확인.
        $res = $this -> m_goods -> getGoodsOptPriceInfo($this -> cid, $goodsno);
        while ($tmp = $this -> db -> fetch($res)) {
            //상점별 옵션 노출여부.
            list($opt_view) = $this -> m_goods -> getGoodsOptMallView($this -> cid, $goodsno, $tmp[optno]);
            if ($opt_view == "1")
                continue;

            if ($this -> sess[bid])
                $tmp[aprice] = get_business_goods_opt_price($tmp[goodsno], $tmp[optno], $tmp[aprice]);
            if ($this -> sess[bid])
                $tmp[areserve] = get_business_goods_opt_reserve($tmp[goodsno], $tmp[optno], $tmp[areserve]);

            if ($tmp[opt1]) {
                $tmp[optnm] = htmlspecialchars($tmp[opt1]);
                $data[r_opt][0][item][$tmp[opt1]] = $tmp;
                if ($data[optlength] < 2)
                    $data[optlength] = 1;
            }

            if ($tmp[opt2]) {
                $tmp[optnm] = $tmp[opt2];
                
                if($this -> cfg[skin_theme] == "M2" || $this -> cfg[skin_theme] == "M3" || $this -> cfg[skin_theme] == "P1")
                  $data[r_opt][1][item][$tmp[opt1]][$tmp[opt2]] = $tmp;
                else
                   $data[r_opt][1][item][$tmp[opt2]] = $tmp;
                
                $data[optlength] = 2;
            }
        }

        return $data;
    }

    //상품 추가 옵션 확인.
    private function get_addopt_priceinfo($goodsno, $data) {
        //상품 추가 옵션 확인.
        $res = $this -> m_goods -> getGoodsAddOptPriceInfo($goodsno);
        while ($tmp = $this -> db -> fetch($res)) {
            $res2 = $this -> m_goods -> getGoodsAddOptPriceInfoList($this -> cid, $tmp[addopt_bundle_no]);

            $data[r_addopt][$tmp[addopt_bundle_no]] = $tmp;
            while ($tmp2 = $this -> db -> fetch($res2)) {

                if ($this -> sess[bid])
                    $tmp2[addopt_aprice] = get_business_goods_addopt_price($tmp2[goodsno], $tmp2[addoptno], $tmp2[addopt_aprice]);
                if ($this -> sess[bid])
                    $tmp2[addopt_areserve] = get_business_goods_addopt_reserve($tmp2[goodsno], $tmp2[addoptno], $tmp2[addopt_areserve]);

                $data[r_addopt][$tmp[addopt_bundle_no]][addopt][] = $tmp2;
            }
        }

        return $data;
    }

    //제작옵션(임포지션옵션) 확인.
    private function get_useimpositioninfo($goodsno, $data) {
        //그룹별 제작옵션(임포지션옵션)
        $res = $this -> m_goods -> getUseImpoGroupInfoList($goodsno);
        while ($tmp = $this -> db -> fetch($res)) {
            //그룹별 옵션 노출여부
            list($opt_group_view) = $this -> m_goods -> getUseImpoGroupMallView($this -> cid, $goodsno, $tmp[opt_group_no]);
            if ($opt_group_view == "1")
                continue;

            $data[r_impopt][$tmp[opt_group_no]] = $tmp;

            //제작옵션(임포지션옵션)
            $res2 = $this -> m_goods -> getUseImpoInfoList($tmp[opt_group_no]);
            while ($tmp2 = $this -> db -> fetch($res2)) {
                //상점별 옵션 노출여부
                list($opt_view) = $this -> m_goods -> getUseImpoMallView($this -> cid, $goodsno, $tmp2[optno]);
                if ($opt_view == "1")
                    continue;

                $data[r_impopt][$tmp[opt_group_no]][opt][] = $tmp2;
            }
        }

        return $data;
    }

    //상품 해쉬태그 링크 처리.
    private function get_hash_tag($htag) {
        $htag = explode(",", $htag);
        foreach ($htag as $key => $val) {
            $data .= "<a href='/goods/search.php?ht=" . $val . "'>#$val</a> ";
        }

        return $data;
    }

    //템플릿 이미지 정보.
    private function get_templateinfo($data) {

        if ($_GET[url]) {
            $_GET[url] = base64_decode($_GET[url]);

            # 센터상품일경우
            // $cfg_center[podsiteid] 센터ID를 p_siteid 파라메타에 추가로 사용 20140206 kdk
            if ($data[pods_useid] == "center" && $data[podsno]) {
                $p_siteid = $this -> cfg_center[podsiteid];
            }

            //주문처리 또는 장바구니 처리시 URL에 포함된 spid가 복수일 경우 누락된 정보가 있음. 어디서 누락된지 모름.
            //복수 편집기 실행 관련 2016.03.16 kdk
            $podsno = "";
            foreach ($this->editor as $key => $val) {
                if ($val[podsno] && $val[podsno] != "") {
                    $podsno .= $val[podsno] . ",";
                }
            }
            $spid = $podsno;

            $mUrl = "";
            $exp = explode('&', $_GET[url]);
            //항목 분리
            if (count($exp) > 0) {
                foreach ($exp as $key => $val) {
                    if (!(strpos($val, "p_siteid=") === false)) {
                        if ($p_siteid != "")
                            $mUrl .= "p_siteid=$p_siteid&";
                        else
                            $mUrl .= $val . "&";
                    } else if (!(strpos($val, "spid=") === false)) {
                        if ($spid != "")
                            $mUrl .= "spid=$spid&";
                        else
                            $mUrl .= $val . "&";
                    } else {
                        $mUrl .= $val . "&";
                    }
                }
                $mUrl = substr($mUrl, 0, -1);
            }

            if ($_GET[url] != $mUrl)
                $_GET[url] = $mUrl;

            $data[templateSetIdx] = $_GET[templateSetIdx];
            $data[templateIdx] = $_GET[templateIdx];
            //$soapurl = "http://" .PODS20_DOMAIN. "/WebService/tempset_info.aspx?templateSetIdx=".$_GET[templateSetIdx];
            $soapurl = $_GET[url];
            //debug($soapurl);
            $ret = readUrlWithcurl($soapurl, false);
            $obj = json_decode($ret, TRUE);
            //debug($obj);
            if ($obj[data]) {
                foreach ($obj[data] as $item) {
                    //debug($item);
                    foreach ($item as $key => $val) {
                        //debug($val);
                        if ($val[templateData]) {
                            if ($val[templateSetIdx] == $_GET[templateSetIdx] && $val[templateData][0][templateIdx] == $_GET[templateIdx]) {
                                if ($val[templateData][0][templateURL])
                                    $tempURL .= $val[templateData][0][templateURL] . "||";
                                $templateName = $val[templateData][0][templateName];
                            }
                        } else {
                            if ($val[templateSetIdx] == $_GET[templateSetIdx] && $val[templateIdx] == $_GET[templateIdx]) {
                                if ($val[templateURL])
                                    $tempURL .= $val[templateURL] . "||";
                                $templateName = $val[templateName];
                            }
                        }
                    }
                }

                $template = array();
                foreach ($obj[data] as $item) {
                    //debug($item);
                    foreach ($item as $key => $val) {
                        //debug($val);
                        if ($val[templateData]) {
                            if ($val[templateData][0][templateName] == $templateName) {
                                $templateName = $val[templateData][0][templateName];
                                $template[$val[siteProductCode]] = array('templateSetIdx' => $val[templateSetIdx], 'templateIdx' => $val[templateData][0][templateIdx]);
                            }
                        } else {
                            if ($val[templateName] == $templateName) {
                                $template[$val[siteProductCode]] = array('templateSetIdx' => $val[templateSetIdx], 'templateIdx' => $val[templateIdx]);
                            }
                        }
                    }
                }

            }
            //debug($tempURL);
            //debug($templateName);
            //debug($template);
            //debug($data[img]);
            //등록된 이미지가 없을 경우 템플릿 이미지 사용 / 16.08.03 / kdk
            if (count($data[img]) > 0) {
                foreach ($data[img] as $item) {
                    //if($item) $img[] = "http://$this->host/data/goods/$this->cid/l/$data[goodsno]/".$item;
                    if ($item)
                        $img[] = "http://$this->host/data/goods/l/$data[goodsno]/" . $item;
                }
                $data[img] = $img;
                //debug($data[img]);
            } else {
                //debug($tempURL);
                $data[img] = array_notnull(explode("||", $tempURL));
            }
            //debug($data);
            //debug($data[img]);
            //$data[img] = array_notnull(explode("||",$tempURL));
        }

        return $data;
    }

    //Hot,추천,연관 상품 정보 조회.
    private function get_addtioniteminfo($goodsno, $key_kind, $goods_kind, $limit = '') {
        $data = "";

        $addWhere = " where cid='$this->cid' and regist_flag !='N' and addtion_key = '$goodsno'";
        if ($key_kind)
            $addWhere .= " and addtion_key_kind = '$key_kind'";
        if ($goods_kind)
            $addWhere .= " and addtion_goods_kind = '$goods_kind'";
        if ($limit)
            $addWhere .= " limit $limit";

        $item = $this -> m_goods -> getAddtionGoodsItem($this -> cid, $addWhere);

        if ($item[addtion_goodsno]) {
            $addWhere2 = "a.goodsno in ($item[addtion_goodsno])";
            $orderby2 = "order by field(a.goodsno,$item[addtion_goodsno])";
            $data = $this -> m_goods -> getAdminGoodsList($this -> cid, $addWhere2, $orderby2, "", "", $this -> sess[mid]);
        }

        if ($data && $key_kind == "P") //패키지 상품. 
        {
            foreach ($data as $k => $v) {
                //상품 옵션 확인.
                $v = $this -> get_opt_priceinfo($v[goodsno], $v);
                if($v[optnm1]) $v[optnm][0] = $v[optnm1];
                if($v[optnm2]) $v[optnm][1] = $v[optnm2];
    
                //상품 추가 옵션 확인.
                $v = $this -> get_addopt_priceinfo($v[goodsno], $v);
        
                //그룹별 제작옵션(임포지션옵션)
                $v = $this -> get_useimpositioninfo($v[goodsno], $v);
                
                //debug($v);
                $data[$k] = $v;
            }
        }

        return $data;
    }

    public function get_imgsrc($goodsno, $cimg) {
        list($cimg) = explode("||", $cimg);
        return "http://$this->host/data/goods/$this->cid/l/$goodsno/$cimg";
        //return "http://$this->host/data/goods/l/$goodsno/$cimg";
    }

    public function get_listimgsrc($goodsno, $clistimg) {
        return "http://$this->host/data/goods/$this->cid/s/$goodsno/$clistimg";
        //return "http://$this->host/data/goods/s/$goodsno/$clistimg";
    }

    private function get_img($goodsno, $cimg, $width = '', $height = '', $style = '') {
        if ($width)
            $attr_width = "width='$width'";
        if ($height)
            $attr_height = "width='$height'";

        list($cimg) = explode("||", $cimg);

        return "<img src='http://$this->host/data/goods/$this->cid/l/$goodsno/$cimg' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
        //return "<img src='http://$this->host/data/goods/l/$goodsno/$cimg' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
    }

    private function get_listimg($goodsno, $clistimg, $width = '', $height = '', $style = '', $cid = '') {
        if ($width)
            $attr_width = "width='$width'";
        if ($height)
            $attr_height = "width='$height'";

        return "<img src='http://$this->host/data/goods/$this->cid/s/$goodsno/$cimg' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
        //return "<img src='http://$this->host/data/goods/s/$goodsno/$cimg' $attr_width $attr_height style='$style' onerror='this.src=\"/data/noimg.png\"'/>";
    }

    //찜(wishlist)리스트 상품 체크
    private function get_check_wish_goods($mid, $goodsno) {
        $result = "";

        $data = $this -> m_goods -> getCheckWishListGoods($this -> cid, $mid, $goodsno);
        if ($data)
            $result = "is-pick";
        //css class명 리턴.

        return $result;
    }
    
     function get_goods_like($cid, $mid, $goodsno){
       list($goods_like) = $this->db->fetch("select goods_like from md_goods_like where cid = '$cid' and mid = '$mid' and goodsno = '$goodsno'",1);
       return $goods_like;
    }

}
?>
