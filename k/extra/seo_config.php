<?
include "../_header.php";
include "../_left_menu.php";


	$file_name = dirname(__FILE__) ."/../../data/user_config/" .$cid. "/main_searching_keyword.php";	
	if (file_exists($file_name))
	{
		include_once $file_name;
		
		$result[main_title] = $usr_searching_title;		
		$result[main_author] = $usr_searching_author;
		$result[main_description] = $usr_searching_description;
		$result[main_keyword] = $usr_searching_keyword;
	}
		
	$file_name = dirname(__FILE__) ."/../../data/user_config/" .$cid. "/cate_searching_keyword.php";	
	if (file_exists($file_name))
	{
		include_once $file_name;
		
		$result[cate_title] = $usr_searching_title;		
		$result[cate_author] = $usr_searching_author;
		$result[cate_description] = $usr_searching_description;
		$result[cate_keyword] = $usr_searching_keyword;
	}
	
	$file_name = dirname(__FILE__) ."/../../data/user_config/" .$cid. "/item_searching_keyword.php";
	if (file_exists($file_name))
	{
		include_once $file_name;
		
		$result[item_title] = $usr_searching_title;		
		$result[item_author] = $usr_searching_author;
		$result[item_description] = $usr_searching_description;
		$result[item_keyword] = $usr_searching_keyword;
	}
			
		
		
		
		

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("SEO 설정")?></h4>
      </div>
      
      
      <ul class="nav nav-tabs">
				<li class="active"><a href="#default-tab-1" data-toggle="tab">공통정보 설정</a></li>
				<li class=""><a href="#default-tab-2" data-toggle="tab">상품 분류 설정</a></li>
				<li class=""><a href="#default-tab-3" data-toggle="tab">상품 상세 설정</a></li>
			</ul>
			
			
			
			<div class="panel-body panel-form">
      <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
      <input type="hidden" name="mode" value="seo">	
      	
			<div class="tab-content">
				<div class="tab-pane fade active in" id="default-tab-1">
					<div class="form-group">
             <label class="col-md-2 control-label"><?=_("타이틀")?> Title</label>
             <div class="col-md-10">
                <input type="text" class="form-control input-sm" name="main_title" value="<?=$result[main_title]?>">
             </div>
          </div>

          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Author</label>
             <div class="col-md-10">
                <input type="text" class="form-control input-sm" name="main_author" value="<?=$result[main_author]?>">
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Description</label>
             <div class="col-md-10">
                <textarea class="form-control" name="main_description" rows="5"><?=$result[main_description]?></textarea>
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Keyword</label>
             <div class="col-md-10">
                <textarea class="form-control" name="main_keyword" rows="5"><?=$result[main_author]?></textarea>
             </div>
          </div>
				</div>
				
				
				<div class="tab-pane fade" id="default-tab-2">
					<div class="form-group">
             <label class="col-md-2 control-label"><?=_("타이틀")?> Title</label>
             <div class="col-md-10">
                <input type="text" class="form-control input-sm" name="cate_title" value="<?=$result[cate_title]?>">
             </div>
          </div>

          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Author</label>
             <div class="col-md-10">
                <input type="text" class="form-control input-sm" name="cate_author" value="<?=$result[cate_author]?>">
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Description</label>
             <div class="col-md-10">
                <textarea class="form-control" name="cate_description" rows="5"><?=$result[cate_description]?></textarea>
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Keyword</label>
             <div class="col-md-10">
                <textarea class="form-control" name="cate_keyword" rows="5"><?=$result[cate_keyword]?></textarea>
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label">예약어 안내</label>
             <div class="col-md-10">
                {{상품분류명}} : 현재 카테고리명을 출력합니다.<br>                
             </div>
          </div>     
				</div>
				
				<div class="tab-pane fade" id="default-tab-3">
					<div class="form-group">
             <label class="col-md-2 control-label"><?=_("타이틀")?> Title</label>
             <div class="col-md-10">
                <input type="text" class="form-control input-sm" name="item_title" value="<?=$result[item_title]?>">
             </div>
          </div>

          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Author</label>
             <div class="col-md-10">
                <input type="text" class="form-control input-sm" name="item_author" value="<?=$result[item_author]?>">
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Description</label>
             <div class="col-md-10">
                <textarea class="form-control" name="item_description" rows="5"><?=$result[item_description]?></textarea>
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("메타태그")?> Keyword</label>
             <div class="col-md-10">
                <textarea class="form-control" name="item_keyword" rows="5"><?=$result[item_keyword]?></textarea>
             </div>
          </div>
          
          <div class="form-group">
             <label class="col-md-2 control-label">예약어 안내</label>
             <div class="col-md-10">
                {{상품명}} : 현재 상품명을 출력합니다.<br>                
                {{상품검색어}} : 현재 상품에 등록된 검색어를 출력합니다.<br>
             </div>
          </div>
				</div>
				
				
			</div>
			
			
			
			<div class="form-group">         
         <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-sm btn-success">
               <?=_("저 장")?>
            </button>
         </div>
      </div>
      </form>
      </div>
      
					

      
   </div>
</div>



<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>