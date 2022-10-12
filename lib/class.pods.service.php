<?

/**
 * PODStation Call Editor class
 * 2016.03.14 by kdk
 * PODStation 편집기 호출에 필요한 정보 조회 및 데이타를 구성한다.
 */

class PodsEditor {

	var $db;
	var $cid;
	var $sess;
	var $goodsno;
	var $cfg;

	/*
	 * 블루팟 상품에 설정된 pods 편집기 정보
	 * (pods_use;podsno;podskind;defaultpage;minpage;maxpage;video;userdata;)
	 $editor[podsuse] //버전
	 $editor[podsno] //코드
	 $editor[podskind] //editor no
	 $editor[defaultpage] //페이지 기본
	 $editor[minpage] //페이지 최소
	 $editor[maxpage] //페이지 최대
	 $editor[video] //비디오북 링크
	 $editor[userdata] //사용자 정보 연동
	 */
	var $editor = array();

	/*
	 * pods 편집기 호출에 필요한 데이타.
	 */
	var $info = array();

	var $goods = array();
	/*
	 * 블루팟 상품 정보
	 */

	//global $cfg, $r_podskind20, $soap_port;

	function PodsEditor($goodsno) {
		$this -> db = $GLOBALS[db];
		$this -> cid = $GLOBALS[cid];
		$this -> sess = $GLOBALS[sess];
		$this -> goodsno = $goodsno;
	}

	//exm_goods 테이블에서 pods 편집기 정보를 조회한다.
	function GetPodsEditor() {

		//debug("cid=".$this->cid);
		//debug("sess=".$this->sess);
		//debug("goodsno=".$this->goodsno);

		# 상품정보 조회
		$query = "
		select
		    a.goodsno,
		    a.goodsnm,
		    a.podsno,
		    a.podskind,
		    a.defaultpage,
		    a.minpage,
		    a.maxpage,
		    a.pods_use,
		    a.pods_useid,
		    a.pods_userdataurl_flag,
		    a.pods_info1,
		    a.pods_info2,
		    a.pods_info3,
		    a.pods_info4,
		    (select catno from exm_goods_link where cid='$this->cid' and goodsno='$this->goodsno' order by cat_index limit 1) as catno,
		    if(b.price is null,a.price,b.price) price,  
		    if(b.reserve is null,a.reserve,b.reserve) reserve
		from
		    exm_goods a
		    inner join exm_goods_cid b on a.goodsno = b.goodsno
		where
		    a.goodsno = '$this->goodsno'
		    and b.cid = '$this->cid'
		";
		//debug($query);

		$data = $this -> db -> fetch($query);
		//debug($data);
		$this -> goods = $data;
		//debug($this->goods);

		//(pods_use;podsno;podskind;defaultpage;minpage;maxpage;video;userdata;)
		if ($data) {
			//기본pods정보
			$arr = array(
				'pods_use' => $data[pods_use], 
				'podsno' => $data[podsno], 
				'podskind' => $data[podskind], 
				'defaultpage' => $data[defaultpage], 
				'minpage' => $data[minpage], 
				'maxpage' => $data[maxpage], 
				'video' => '', 
				'userdata' => $data[pods_userdataurl_flag] 
			);
			$this -> editor[] = $arr;

			//추가pods정보
			//(pods_use;podsno;podskind;defaultpage;minpage;maxpage;video;userdata;)
			if ($data[pods_info1]) {
				$info = explode(';', $data[pods_info1]);
				//항목 분리
				if ($info[0] && $info[1] && $info[2]) {
					$arr = array(
						'pods_use' => $info[0], 
						'podsno' => $info[1], 
						'podskind' => $info[2], 
						'defaultpage' => $info[3], 
						'minpage' => $info[4], 
						'maxpage' => $info[5], 
						'video' => $info[6], 
						'userdata' => $info[7] 
					);
					$this -> editor[] = $arr;
				}
			}
			
			if ($data[pods_info2]) {
				$info = explode(';', $data[pods_info2]);
				//항목 분리
				if ($info[0] && $info[1] && $info[2]) {
					$arr = array(
						'pods_use' => $info[0], 
						'podsno' => $info[1], 
						'podskind' => $info[2], 
						'defaultpage' => $info[3], 
						'minpage' => $info[4], 
						'maxpage' => $info[5], 
						'video' => $info[6], 
						'userdata' => $info[7]
					);
					$this -> editor[] = $arr;
				}
			}
			
			if ($data[pods_info3]) {
				$info = explode(';', $data[pods_info3]);
				//항목 분리
				if ($info[0] && $info[1] && $info[2]) {
					$arr = array(
						'pods_use' => $info[0], 
						'podsno' => $info[1], 
						'podskind' => $info[2], 
						'defaultpage' => $info[3], 
						'minpage' => $info[4], 
						'maxpage' => $info[5], 
						'video' => $info[6], 
						'userdata' => $info[7]
					);
					$this -> editor[] = $arr;
				}
			}
			
			if ($data[pods_info4]) {
				$info = explode(';', $data[pods_info4]);
				//항목 분리
				if ($info[0] && $info[1] && $info[2]) {
					$arr = array(
						'pods_use' => $info[0], 
						'podsno' => $info[1], 
						'podskind' => $info[2], 
						'defaultpage' => $info[3], 
						'minpage' => $info[4], 
						'maxpage' => $info[5], 
						'video' => $info[6], 
						'userdata' => $info[7]
					);
					$this -> editor[] = $arr;
				}
			}
		}

		$podsno = $this -> db -> listArray("select podsno from exm_goods_opt where goodsno = '$this->goodsno'");
		if ($podsno) {
			foreach ($podsno as $k => $v) {
				
				if($v[podsno] == "") continue;
				
				$arr = array(
					'pods_use' => $data[pods_use], 
					'podsno' => $v[podsno], 
					'podskind' => $data[podskind], 
					'defaultpage' => $data[defaultpage], 
					'minpage' => $data[minpage], 
					'maxpage' => $data[maxpage], 
					'video' => '', 
					'userdata' => $data[pods_userdataurl_flag]
				);
				$this -> editor[] = $arr;
			}
		}

		//debug($this->editor);
		return $this -> editor;
	}

	//podskind (editno) 별로 편집기 호출에 필요한 정보를 생성한다.
	function GetPodsEditorData($editdata = array()) {
		//debug($_REQUEST);
		//debug($_GET);
		//debug($editdata);
		//global $r_podskind30;
		global $cfg;

		$this -> info[pods_use] = $editdata[pods_use];
		$this -> info[podsno] = $editdata[podsno];
		$this -> info[podskind] = $editdata[podskind];
		$this -> info[defaultpage] = $editdata[defaultpage];
		$this -> info[minpage] = $editdata[minpage];
		$this -> info[maxpage] = $editdata[maxpage];
		$this -> info[video] = $editdata[video];
		$this -> info[userdata] = $editdata[userdata];

		//debug($this->info);

		# podstation siteid
		if (!$GLOBALS[cfg][podsiteid])
			$GLOBALS[cfg][podsiteid] = $GLOBALS[cfg_center][podsiteid];
		//debug($GLOBALS[cfg][podsiteid]);
		//debug($GLOBALS[cfg_center][podsiteid]);

		//debug($this->goods);

		# podstation 상품아이디
		if (!$_GET[productid])
			$_GET[productid] = $this -> info[podsno];
		# podstation 옵션아이디
		if (!$_GET[optionid])
			$_GET[optionid] = 1;

		# intro 전달 정보
		$this -> info[intro_optno] = $_GET[optno];
		$this -> info[intro_addopt] = $_GET[addopt];

		# 추가옵션정보
		$_GET[addopt] = explode(",", $_GET[addopt]);
		$addopt = array();
		foreach ($_GET[addopt] as $k => $v) {
			$query = "select addoptnm from exm_goods_addopt where addoptno = '$v'";
			list($dummy) = $this -> db -> fetch($query, 1);
			if ($dummy)
				$addopt[] = $dummy;
		}

		//아래의 자체편집상품일경우에도 변수룰 dummy를 사용하기 때문에 위에서 사용한 dummy값 초기화 / 14.10.13 / kjm
		$dummy = '';

		# 자체편집상품일경우
		// 조건 오류로 인해 자체상품 편집기 호출되지 않았음. 조건 수정   20131202    chunter
		// cfg 배열을 변경할 경우 추후 센터 상품 편집기 호출시 문제됨. 변수 할당으로 변경.   20131202    chunter
		$this -> info[podsiteid] = $GLOBALS[cfg][podsiteid];
		if ($this -> goods[pods_useid] != "center" && $this -> info[podsno]) {
			list($dummy) = $this -> db -> fetch("select self_podsiteid from exm_mall where cid = '$this->cid'", 1);
			if ($dummy)
				$this -> info[podsiteid] = $dummy;
		}

		# 기업그룹 판매가및 적립금 보정
		if ($this -> sess[bid])
			$this -> goods[price] = get_business_goods_price($this -> goodsno, $this -> goods[price]);
		if ($this -> sess[bid])
			$this -> goods[reserve] = get_business_goods_reserve($this -> goodsno, $this -> goods[reserve]);

		# 편집기별 스크립트분리
		$storageid = $_GET[storageid];

		if ($this -> info[pods_use] == "3") {
			//임시로 관리자에서 편집기 실행시 $idProject를 (guid 또는 productid) 반환한다. 2014.06.03 by kdk
			list($preview_link) = $this -> db -> fetch("select preview_link from tb_editor_ext_data where storage_id = '$storageid'", 1);
			if ($preview_link) {
				$arr_link[preview_link] = explode("|", $preview_link);
				if ($arr_link[preview_link][0]) {
					$link = str_replace("http://", "", $arr_link[preview_link][0]);
					$link = explode("/", $link);
					if ($link[8])
						$idProject = $link[8];
				}
			}

			if ($idProject)
				$this -> info[idProject] = $idProject;
			else
				$this -> info[idProject] = $_GET[productid];
		}

		# 편집기 실행에 사용될 변수 분리
		if (!$_GET[adminmode]) {
			$this -> info[adminmode] = ($GLOBALS[sess_admin]) ? "Y" : "N";
		} else {
			$this -> info[adminmode] = ($_GET[adminmode] == "Y") ? "Y" : "N";
		}

		$this -> info[storageid] = $storageid;
		$this -> info[productid] = $_GET[productid];
		$this -> info[optionid] = $_GET[optionid];
		$this -> info[siteid] = $this -> info[podsiteid];

		$this -> info[userid] = ($this -> sess[mid]) ? $this -> sess[mid] : $_COOKIE[cartkey];
		if ($_GET[mid] && $GLOBALS[sess_admin])
			$this -> info[userid] = $_GET[mid];

		$this -> info[addopt] = $addopt;

		//editmode 추가       20150512    chunter
		if ($_GET[editmode]) {
			$this -> info[editmode] = $_GET[editmode];
		}

		$this -> info[param] = $this -> get_sessionparam();

		//블루포토의 경우 유치원/반/원아명을 넘긴다
		if ($_GET[class_ID] && $_GET[child_code]) {
			$m_pretty = new M_pretty();
			$m_member = new M_member();

			$name = $m_member -> getInfo($this -> cid, $this -> sess[mid]);
			$classData = $m_pretty -> getClassInfo($this -> cid, $this -> sess[mid], $_GET[class_ID]);
			$childData = $m_pretty -> getChildInfo($this -> cid, $this -> sess[mid], $_GET[child_code]);

			$this -> info[pname] = $name[name] . " / " . $classData[class_name] . " / " . $childData[child_name];
		} else {
		   if($this -> info[podskind] == "3055"){
            include_once "../lib/lib_const.php";
            global $r_cover_type, $r_cover_paper, $r_cover_coating;

            $m_goods = new M_goods();
            $coverData = $m_goods->getCoverRangeDataWithCoverID($_REQUEST[cover_id]);
            
            //$ext_data = $this->db->fetch("select * from tb_editor_ext_data where storage_id = '$storageid'");
            $ext_json_data = $m_goods->get_editor_ext_data($storageid);// json_decode($ext_data[editor_return_json],1);

            if(($ext_json_data[page_count] - $ext_json_data[page_base]) > 0){
               list($addpage_price) = $this->db->fetch("select cover_page_addprice from md_cover_range_option where cover_id = '$coverData[cover_id]' and goodsno = '$coverData[goodsno]'",1);
               $coverData[cover_goods_price] = $coverData[cover_goods_price] + (($ext_json_data[page_count] - $ext_json_data[page_base]) * $addpage_price);
            }

            $cover_data = explode("_", $_REQUEST[cover_id]);

            $this -> info[pname] = $cover_data[0]." / ".$r_cover_type[$cover_data[1]]." / ".$r_cover_paper[$cover_data[2]]." / ".$r_cover_coating[$cover_data[3]]." / ".$this -> goods[goodsnm]." / ".$coverData[cover_goods_price];
         } else 
            $this -> info[pname] = $this -> goods[goodsnm];
		}

		# 프로토콜 없을경우 초기화
		if (!$GLOBALS[cfg][protocol_type])
			$GLOBALS[cfg][protocol_type] = "http://";

		$this -> info[introurl] = $GLOBALS[cfg][protocol_type] . $_SERVER[HTTP_HOST] . "/module/editing.php?callmode=editor&userid=".$this -> sess[mid]."&catno=" . $this -> goods[catno] . "&goodsno=" . $this -> goodsno . "&ea=" . $this -> info[displaycount] . "&optno=" . $this -> info[intro_optno] . "&addopt=" . $this -> info[intro_addopt];

      $m_goods = new M_goods();
      $category_data = $m_goods->getCategoryInfo($this->cid, $this->goods[catno]);

		//POD모듈INTRO data가 없으면 url을 넘기지 않는다.(김기웅이사님 요청) 2016.03.08 by kdk
		//인트로 사용 설정에 따라 처리한다 / 18.07.24 / kjm
		if ($GLOBALS[cfg][pod_module_intro_use] != 'Y' && $category_data[is_intro] != 1 ) {
         $this -> info[introurl] = "";
      }

		$this -> info[displayprice] = $this -> goods[price];
		$this -> info[displaycount] = (is_numeric($_GET[ea]) && $_GET[ea] > 0) ? $_GET[ea] + 0 : 1;
		//$this->info[defaultpage]  = $data[defaultpage];   // 기본페이지수
		//$this->info[minpage]      = $data[minpage];       // 최소페이지수
		//$this->info[maxpage]      = $data[maxpage];       // 최대페이지수

		$this -> info[center_podsid] = $GLOBALS[cfg_center][podsiteid];
		// 센터의 pods 아이디

		# 자체 상품일 경우
		if ($dummy)
			$this -> info[center_podsid] = $this -> info[podsiteid];
		//$this->info[podsiteid]

		# 로고 파일 url 설정
		$this -> info[logourl] = (is_file("../data/pod/$cid/pod_logo.png")) ? $GLOBALS[cfg][protocol_type] . $_SERVER[HTTP_HOST] . "/data/pod/$cid/pod_logo.png" : "";

		# 타이틀 파일 url 설정
		$this -> info[titleourl] = (is_file("../data/pod/$cid/pod_title.png")) ? $GLOBALS[cfg][protocol_type] . $_SERVER[HTTP_HOST] . "/data/pod/$cid/pod_title.png" : "";

		#wpod 명함 편집기 연동시 userDataUrl 연동 여부 by 2014.04.16 kdk
		if ($this -> info[userdata]) {

			//외부에서 명함 편집기 실행 시 명함데이터 가져올 url
			if ($cfg[member_system][out_open_edit][val][extra_data_url] && $_REQUEST[vdp] == "false") {
				$this -> info[requestuser] = $cfg[member_system][out_open_edit][val][extra_data_url] . "?extra_data=" . $_REQUEST[extra_data];
			} else
				$this -> info[requestuser] = $GLOBALS[cfg][protocol_type] . $_SERVER[HTTP_HOST] . "/_sync/get_wpod_extra_data.php?method=macro&mid=" . $this -> info[userid];

			//20150407 / minks / 수정시 파라미터에 보관함코드 추가
			if ($this -> info[podskind] == "1005" && $this -> info[storageid]) {
				$this -> info[requestuser] .= "&storageid=" . $this -> info[storageid];
			}

			$this -> info[extradataurl] = $GLOBALS[cfg][protocol_type] . $_SERVER[HTTP_HOST] . "/_sync/get_wpod_extra_data.php?mid=" . $this -> info[userid]."&";  //"&" 편집기 파라메타 결합시 필요함. / 20190227 / kdk
		}
		//편집기 html데이터
		//웹표준으로 인코딩 한번 해서 곽이사님께 알려드리기

		if ($cfg[member_system][out_open_edit][val][adddesign] && $_REQUEST[vdp] == "true") {

			$editor_add_design = $cfg[member_system][out_open_edit][val][adddesign];

			$editor_add_design = base64_decode($editor_add_design);
			$m_goods = new M_goods();
			//추가옵션 이름 가져오기
			$addopt_data = $m_goods -> getGoodsAddOpt($cid, $_REQUEST[addopt]);

			$editor_add_design = str_replace("{addoptnm}", $addopt_data[addoptnm], $editor_add_design);

			$editor_add_design = str_replace("{count}", $_REQUEST[ea], $editor_add_design);
			$editor_add_design = base64_encode($editor_add_design);

			$this -> info[displayhtml] = $editor_add_design;
			$this -> info[volume] = $_REQUEST[ea];
		}
		//debug($this->info[displayhtml]);

		#비디오북 링크 설정
		if ($this -> info[video]) {
			$this -> info[vidiobook_link] = "Y";
		} else
			$this -> info[vidiobook_link] = "N";

		#wpod vdp 설정
		if ($_GET[vdp]) {
			$this -> info[vdp] = $_GET[vdp];
		}

		# 템플릿셋 id 설정
		if ($_GET[templatesetid])
			$this -> info[templatesetid] = $_GET[templatesetid];

		# 템플릿 id 설정
		if ($_GET[templateid])
			$this -> info[templateid] = $_GET[templateid];

		//초간편 포토북 상품 정보 연동 추가 2014.09.02 by kdk
		if ($this -> info[podskind] == "3240") {
			$this -> info[get_layout_spec] = "http://" . $_SERVER[HTTP_HOST] . "/_sync/get_layout_spec.php?goodsno=$this->goodsno&addopt=$this->info[intro_addopt]";
			//상품 규격 / 페이지 연동 url
			$this -> info[get_product_info] = "http://" . $_SERVER[HTTP_HOST] . "/_sync/get_product_info.php?goodsno=$this->goodsno";
			//상품 정보 연동 url
		}

		if ($_GET[class_ID] && $_GET[child_code]) {
			$this -> info[macroxmlurl] = "http://" . $_SERVER[HTTP_HOST] . "/_sync/get_kids_macro_data.php?mid=" . $this -> sess[mid] . "&class_ID=" . $_GET[class_ID] . "&child_ID=" . $_GET[child_code];
		}

		$this -> info[module] = $this -> get_module();

		//편집기 호출 시 상품코드의 앞뒤에 공백이 있으면 제거한다 / 16.12.22 / kjm
		$this -> info[productid] = trim($this -> info[productid]);

		//debug("### this->goods ###");
		//debug($this->goods);
		//debug("### info ###");
		//debug($this->info);

		//기본 5자리로 리턴한다.

		$temp_key = getTempKey($this -> info[userid]);
		$temp_key = addslashes($temp_key);
		$this -> info[temp_key] = $temp_key;

		return $this -> info;
	}

	private function get_module() {
		//debug($this->goods);
		//debug($this->info);

		# 브라우저 정보확인.
		$chkBrowser = getBrowser();
		if ($chkBrowser[name] == "Internet Explorer")
			$browser = "IE";
		else
			$browser = "notIE";

      $m_goods = new M_goods();
      $goods_data = $m_goods->getInfo($this->goodsno);
      //debug($goods_data);
      $editor_type = $goods_data[pods_editor_type];
      
      $editor_type = json_decode($editor_type,1);
      //debug($editor_type);

		$module = "";
		switch ($this->info[podskind]) {
			## pods1.0 | 2.7,3.0편집기 | 명함
			case "28" :
				$module = ($storageid) ? "p10m20" : "p10m20card";
				break;
			## pods1.0 | 3.5편집기 | 명함
			case "3180" :
				//$module = ($storageid) ? "p10m30":"p10m30card";
				$module = "p10m30card";
				break;
			## pods2.0 | 3.5편집기 | 앨범/북3.5 3050,3051,3052 | 표지편집기3.5 3230 | 표지편집기3.5 3231 | 간편편집기3.5 3110
			case "3010" :
			//pods2.0 인화편집기4.0 추가 / 16.07.01 / kdk
			case "3020" :
			case "3040" :
			case "3041" :
			case "3042" :
			case "3043" :
			case "3053" :
			case "3054" :
			case "3060" :
			case "3050" :
			case "3051" :
			case "3052" :
         case "3055" :
				//북편집기가 4.0으로 바꿔서 이제 브라우저만 체크하면 된다 / 17.02.23 / kjm
				if ($browser == "notIE" || ($browser == "IE" && $editor_type[editor_active_exe] == "exe")) {
					$module = "p20m40";
					break;
				}
			case "3011" :
				//pods2.0 고급인화편집기4.0 추가 / 16.07.01 / kdk
				//debug($browser);
				if ($browser == "notIE") {
					include_once "../lib/class.pods.php";
					$c_pod = new PODStation('20');
					//debug($data);
					$module_ver = $c_pod -> GetCustomMuduleVersion($this -> info[podsiteid], $this -> info[podskind]);
					if ($module_ver == "40") {
						$module = "p20m40";
						//pods 4.0 exe 실행 연동      20150918    chunter
						break;
					}
				}
			case "3090":
         case "3091":
         case "3092":
            if($browser == "notIE") {
               $module = "p20m40"; //북편집기가 4.0으로 바꿔서 이제 브라우저만 체크하면 된다 / 17.02.23 / kjm
               break;
            }
			case "3110" :
			case "3111" :
			case "3112" :
			case "3113" :
			case "3030" :
				//pods1.0 과 2.0 편집기코드가 중복될 수 있어 분기 처리함. 2014.03.04 by kdk
				$module = ($this -> info[pods_use] == "1") ? "p10m30" : "p20m30";
				break;
			case "3230" :
			case "3231" :
			case "3240" :
				//초간편 포토북 추가 2014.06.30 by kdk
				$module = "p20m30";
				break;
			default :
				$module = (strlen($this -> info[podskind]) == "4" && substr($this -> info[podskind], 0, 1) == "3") ? "p10m30" : "p10m20";
				break;
		}

		if ($this -> info[pods_use] == "3") {
			$module = "p20mweb";
		}

		//debug($module);
		return $module;
	}

	//pods 편집기 호출 시 넘길 sessionparam 정보를 생성한다.
	private function get_sessionparam() {

		if ($_REQUEST[master_cartno]) {
			$sessionparam_data = $this -> db -> fetch("select * from tb_pretty_cart_mapping where master_cartno = '$_REQUEST[master_cartno]'");
		}

		# sessionparam 작성
		$sessionparam[cid] = $this -> cid;
		$sessionparam[goodsno] = $_GET[goodsno];

		if ($_GET[mid])
			$this -> sess[mid] = $_GET[mid];

		//편집기 호출시 기업그룹별 가격을 가져오기 위해 회원 아이디 전달 / 15.03.18 / kjm
		if ($this -> sess[mid])
			$sessionparam[bluepod_mid] = $this -> sess[mid];
        
		//유치원 웹앨범 연동을 위한 추가 데이타. 원아 코드   20140625    chunter
		if ($_GET[child_code])
			$sessionparam[child_code] = $_GET[child_code];

		if ($_GET[class_ID])
			$sessionparam[class_ID] = $_GET[class_ID];

		//블루포토 시즌 추가, 사용하는 곳만 넘긴다 / 17.03.16 / kjm
		if ($_COOKIE[season_code])
			$sessionparam[season_code] = $_COOKIE[season_code];

		//null 로 넘어가면 pods 에서 오류 발생됨.     20140626    chunter
		if ($_GET[optno])
			$sessionparam[optno] = $_GET[optno];
		if ($_GET[addopt])
			$sessionparam[addopt] = $_GET[addopt];

		if ($sessionparam_data[cartno]) {
			$sessionparam[cartno] = $sessionparam_data[cartno];
		}
      else if ($_GET[cartno]) {
         $sessionparam[cartno] = $_GET[cartno];
      }

		if ($sessionparam_data[title])
			$sessionparam[title] = $sessionparam_data[title];

		$sessionparam[mode] = $_REQUEST[mode];

		//editmode 추가       20150512    chunter
		if ($_GET[editmode]) {
			$sessionparam[editmode] = $_GET[editmode];
		}

		if ($_GET[impoptno])
			$sessionparam[impoptno] = $_GET[impoptno];

      //자동견적 옵션 저장을 위해 처리 / 20171023 / kdk
      if ($_GET[pod_signed])
         $sessionparam[pod_signed] = $_GET[pod_signed];

      //패키지상품 관련 처리 / 2017.12.20 / kdk
      if ($_GET[package_mode])
         $sessionparam[package_mode] = $_GET[package_mode];
      //if ($_GET[package_cartno])
         //$sessionparam[package_cartno] = $_GET[package_cartno];
        
		$sessionparam = base64_encode(json_encode($sessionparam));

		//debug($sessionparam);
		return $sessionparam;
	}

}
?>