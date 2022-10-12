<?
include_once "../_pheader.php";
include_once "../../lib/class.page.php";

//exm_member 테이블에 buying필드에 총 구매 금액을 넣는 처리가 없어서
//임시방편으로 구매내역의 구매금액의 총 금액을 표시해줌 / 14.07.23 / kjm
$query = "select * from 
exm_pay a
inner join exm_ord b on b.payno = a.payno
inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno
where cid = '$cid' and mid = '$_GET[mid]' and itemstep>4 and itemstep<10";

$item = $db->query($query);
while($total = $db->fetch($item)){
    $buy_price += $total[payprice];
}

$res = $db->query("select * from exm_member where cid = '$cid' and mid = '$_GET[mid]'");
$data = $db->fetch($res);

if (!$data) echo "<script>alert('존재하지 않는 회원입니다');window.close()</script>";

## 회원그룹 추출
$r_grp = getMemberGrp();

$r_sex = array("m"=>"남자","f"=>"여자");
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <div id="content" class="content">
      <div class="panel panel-inverse">
         <div class="panel panel-info">
            <div class="panel-heading">
               <h4 class="panel-title"><?=$_GET[mid]?><?=_("님의 회원정보")?></h4>
            </div>
         </div>
         
         <div class="panel-body">
            <div class="table-responsive">
               <table id="data-table" class="table table-striped table-bordered">
                  <tr>
                     <th>아이디</th>
                     <td><?=$data[mid]?>(<?=$r_grp[$data[grpno]]?>)</td>
                  </tr>
                  <tr>
                     <th>이름</th>
                     <td><?=$data[name]?></td>
                  </tr>
                  <tr>
                     <th>이메일</th>
                     <td><?=$data[email]?></td>
                  </tr>
                  <tr>
                     <th>주소</th>
                     <td>[<?=$data[zipcode]?>]</br><?=$data[address]?> <?=$data[address_sub]?></td>
                  </tr>
                  <tr>
                     <th>전화번호</th>
                     <td><?=$data[phone]?></td>
                  </tr>
                  <tr>
                     <th>핸드폰</th>
                     <td><?=$data[mobile]?></td>
                  </tr>
                  <tr>
                     <th>성별</th>
                     <td><?=$r_sex[$data[sex]]?></td>
                  </tr>
                  <tr>
                     <th>생년월일</th>
                     <td><?=$data[birth_year]?>년 <?=substr($data[birth],0,2)?>월 <?=substr($data[birth],2,2)?>일</td>
                  </tr>
                  <tr>
                     <th>적립금</th>
                     <td><?=number_format($data[emoney])?>원</td>
                  </tr>
                  <tr>
                     <th>구매금액</th>
                     <td><?=number_format($buy_price)?>원</td>
                  </tr>
                  <tr>
                     <th>가입일</th>
                     <td><?=$data[regdt]?></td>
                  </tr>
                  <tr>
                     <th>로그인정보</th>
                     <td><?=$data[cntlogin]?>회 (최종로그인시간 <?=$data[lastlogin]?>)</td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<? include_once "../_pfooter.php"; ?>