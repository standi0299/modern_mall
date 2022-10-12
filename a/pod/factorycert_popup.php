<?
/*
* @date : 20190107
* @author : kdk
* @brief : 출고증 출력  기능.
* @request :
* @desc : 
* @todo : 
*/

require('../../lib/fpdf181/korean.php');

include "../lib.php";

if (!$_GET[payno]) {
    msg(_("주문 코드가 넘어오지 못했습니다!"), "close");
}

$m_pretty = new M_pretty();
$m_pod = new M_pod();
$m_order = new M_order();
$m_member = new M_member();
$m_print = new M_print();

$r_pods_order_trans = array(-1 => _("실패"), 0 => _("미전송"), 1 => _("완료"));
$r_step_selectBox = array(2=>_("제작대기"), 3=>_("제작중"), 4=>_("발송대기"), 5=>_("출고완료"));

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

### 배송업체정보추출
$r_shipcomp = get_shipcomp();

### 출고처 추출
$r_rid["|self|"] = _("자체출고");
$r_rid_x = get_release();
$r_rid = array_merge($r_rid, $r_rid_x);

### 결제정보추출
$pay = $db -> fetch("select * from exm_pay where payno = '$_GET[payno]'");
//$pay[receiver_zipcode] = explode("-", $pay[receiver_zipcode]);
//debug($pay);

//상품 전체 개수 / 15.08.25 / kjm
$totalea = $m_order->getOrdItemSumEa($_GET[payno]);

$query = "
select * from
   exm_ord
where
   payno = '$pay[payno]'
";
$db -> query("set names utf8");
$res = $db -> query($query);

$cancelok = true;
while ($data = $db -> fetch($res)) {
   $loop[$data[ordno]] = $data;
   $query = "select * from exm_ord_item where payno = '$pay[payno]' and ordno = '$data[ordno]' order by ordseq";
   $res2 = $db -> query($query);
   while ($tmp = $db -> fetch($res2)) {
      if ($tmp[est_order_option_desc]) {

         $tmp[est_order_option_desc_str] = str_replace("<br>", "\n", str_replace("::", ":", $tmp[est_order_option_desc]));
         $tmp[est_order_option_desc_str] = str_replace("]", "]\n", $tmp[est_order_option_desc_str]);
         //if ($tmp[est_order_option_desc_str]) $tmp[est_order_option_desc_str] = substr($tmp[est_order_option_desc_str] , 0, -1); 
      }
      if ($tmp[addopt])
         $tmp[addopt] = unserialize($tmp[addopt]);
      if ($tmp[printopt])
         $tmp[printopt] = unserialize($tmp[printopt]);
      $loop[$data[ordno]][item][$tmp[ordseq]] = $tmp;
      if ($tmp[itemstep] != 1)
         $cancelok = false;
   }
}
//debug($loop);

### 결제정보 pod_pay.
$pay_list = $m_pod->getPodPayList($cid, $pay[mid], "and payno='$_GET[payno]'");
if($pay_list) $pod = $pay_list[0];

### 회원정보.
$member = $m_member->getInfo($cid, $pay[mid]);

//debug($member);
//exit;
$data[member] = $member;
$data[pay] = $pay;
$data[pod] = $pod;
$data[loop] = $loop;
//debug($data);

class PDFK extends PDF_Korean
{
    function HeaderTable()
    {
        $this->SetFont('','B','24');
        $this->Cell(0,10,iconv('utf-8','euc-kr',"주 문 출 고 증"),0,0,'C');
        $this->Ln();
    }
    
    function ContentTable($data)
    {
        $this->SetFont('','','9');
        $this->Ln();
        
        $this->SetLineWidth(.1);

        $this->Cell(45,7,iconv('utf-8','euc-kr',"주문번호"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[pay][payno]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"주문일시"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[pay][orddt]),1,0);
        $this->Ln();

        if ($data[pay][mid]) {
            $info_member = $data[pay][mid];
        } else {
            $info_member = _("비회원");
        }
        
        $this->Cell(45,7,iconv('utf-8','euc-kr',"아이디"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',"$info_member"),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"회원명"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[member][name]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"사업자명"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[member][cust_name]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"상품금액"),1,0);
        $this->Cell(50,7,number_format($data[pod][payprice]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"일반전화"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[member][phone]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"배송금액"),1,0);
        $this->Cell(50,7,number_format($data[pod][ship_price]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"휴대전화"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[member][mobile]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"부가세액"),1,0);
        $this->Cell(50,7,number_format($data[pod][vat_price]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"입금방법"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$r_paymethod[$data[pay][paymethod]]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"합계"),1,0);
        $this->Cell(50,7,number_format($data[pod][payprice]+$data[pod][ship_price]+$data[pod][vat_price]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"입금자명"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[pay][payer_name]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"입금액"),1,0);
        $this->Cell(50,7,number_format($data[pod][deposit_price]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',""),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',""),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"미수금액"),1,0);
        $this->Cell(50,7,number_format($data[pod][remain_price]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',""),0,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',""),0,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',""),0,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',""),0,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"배송방법"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',""),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"운송장번호"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',""),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"받는사람"),1,0);
        $this->Cell(145,7,iconv('utf-8','euc-kr',$data[pay][receiver_name]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"일반전화"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[pay][receiver_phone]),1,0);
        $this->Cell(45,7,iconv('utf-8','euc-kr',"휴대전화"),1,0);
        $this->Cell(50,7,iconv('utf-8','euc-kr',$data[pay][receiver_mobile]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"주소"),1,0);
        $this->Cell(145,7,iconv('utf-8','euc-kr',$data[pay][receiver_zipcode]." ".$data[pay][receiver_addr]." ".$data[pay][receiver_addr_sub]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"배송메모"),1,0);
        $this->Cell(145,7,iconv('utf-8','euc-kr',$data[pay][request]),1,0);
        $this->Ln();
        $this->Cell(45,7,iconv('utf-8','euc-kr',"추가메모(작업)"),1,0);
        $this->Cell(145,7,iconv('utf-8','euc-kr',$data[pay][request2]),1,0);
        $this->Ln();
        $this->Ln();

        $this->Cell(20,7,iconv('utf-8','euc-kr',"상품번호"),1,0,'C');
        $this->Cell(40,7,iconv('utf-8','euc-kr',"상품명"),1,0,'C');
        $this->Cell(80,7,iconv('utf-8','euc-kr',"주문사양"),1,0,'C');
        $this->Cell(25,7,iconv('utf-8','euc-kr',"판매단가"),1,0,'C');
        $this->Cell(25,7,iconv('utf-8','euc-kr',"부가세액"),1,0,'C');
        $this->Ln();

        foreach ($data[loop] as $k=>$ord) {
            foreach ($ord[item] as $k2=>$item) {
                    
                $goodsno = "";
                $goodsnm = "";
                $item_option = "";
                $payprice = "";
                $vatprice = "0";
                
                if ($item[opt]){
                    $item_option .= $item[opt];
                }
                
                if ($item[addopt]) {
                    foreach ($item[addopt] as $v) {
                        $item_option .= $v[addopt_bundle_name].":".$v[addoptnm];
                    }
                }
                
                if ($item[printopt]) {
                    foreach ($item[printopt] as $v) {
                        $item_option .= $v[printoptnm].":".$v[ea]."(".number_format($v[print_price]).")";
                    }
                }
                
                if ($item[est_order_option_desc_str]) {
                    //debug($item[est_order_option_desc_str]);
                    $item_option = $item[est_order_option_desc_str];
                }

                /*$this->MultiCell(30,7,iconv('utf-8','euc-kr',$item[goodsno]),1,'C');
                $this->MultiCell(50,7,iconv('utf-8','euc-kr',$item[goodsnm]),1,'C');
                $this->MultiCell(60,7,iconv('utf-8','euc-kr',$item_option),1,'L');
                $this->MultiCell(25,7,number_format($item[payprice]),1,'C');
                $this->MultiCell(25,7,number_format(0),1,'C');
                $this->Ln();*/
                
                //Table columns
                $this->SetWidths(array(20,40,80,25,25));
                $this->SetAligns(array('C','C','L','C','C'));
                
                $goodsno = iconv('utf-8','euc-kr',$item[goodsno]);
                $goodsnm = $item[goodsnm];
                //debug($goodsnm);
                //$item_option = iconv('utf-8','euc-kr',$item_option);
                $payprice = number_format($item[payprice]);
                
                $this->Row(array($goodsno,$goodsnm,$item_option,$payprice,0));
            }
        }
        
    }

    function FooterTable()
    {

    }

    function PageTable()
    {
        //아래쪽에서 1.5 cm 위로 이동
        //$this->SetY(-15);
        //글꼴을 Arial italic 8 로 선택
        $this->SetFont('Arial','I',8);
        //현재 페이지 번호와 총 페이지 갯수를 출력
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }
    
    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }
    
    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

        if($nb>1) $nb++;
        $h=5*$nb;

        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,iconv('utf-8','euc-kr',$data[$i]),0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }
    
    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    
    function NbLines($w,$txt)
    {
        //debug($w);
        //debug($txt);
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        //debug("wmax:".$wmax);
        
        //debug($s);
        //debug($nb);        
        
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        //debug($nl);
        return $nl;
    }
}

$pdf = new PDFK();
$pdf->AliasNbPages();
$pdf->AddUHCFont();
$pdf->AddPage();
$pdf->SetFont('UHC','',16);
$pdf->HeaderTable();
$pdf->ContentTable($data);
$pdf->FooterTable();
//$pdf->PageTable();
$pdf->Output();
?>
