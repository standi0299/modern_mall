<?
/*
* @date : 20190103
* @author : kdk
* @brief : 청구서 출력  기능.
* @request :
* @desc : 
* @todo : 
*/

require('../../lib/fpdf181/korean.php');

include "../lib.php";

if (!$_GET[mid]) {
    msg(_("회원 코드가 넘어오지 못했습니다!"), "close");
}

$m_member = new M_member();
$m_pod = new M_pod();

$year_month = date("Y-m");
$year_month = "2018-12";
//debug($year_month);

$addwhere = "";

if ($_GET[mid]) {
    $addwhere = "and mid = '$_GET[mid]'";
    
    //회원정보 조회.
    $list = $m_member -> getList($cid, $addwhere);
    
    if($list) $member_data = $list[0]; 
}

$data[cust_name] = $member_data[cust_name];
$data[bill_bank_name] = $cfg[bill_bank_name];
$data[bill_bank_no] = $cfg[bill_bank_no];
$data[bill_bank_owner] = $cfg[bill_bank_owner];
$data[bill_nameCeo] = $cfg[bill_nameCeo];
$data[bill_faxComp] = $cfg[bill_faxComp];
$data[bill_nameComp] = $cfg[bill_nameComp];
$data[bill_phoneComp] = $cfg[bill_phoneComp];
$data[bill_itemBiz] = $cfg[bill_itemBiz];
$data[bill_typeBiz] = $cfg[bill_typeBiz];
$data[bill_regnumBiz] = $cfg[bill_regnumBiz];
$data[bill_address] = $cfg[bill_address];
//debug($data);

//현재 월 입금액 조회.
$data[deposit_price] = $m_pod->getDepositPrice($cid, $_GET[mid], " and left(deposit_date,7) = '$year_month'");
//debug($price_data);

//전월 잔액(미수금) 조회.
$prev_month = date("Y-m",strtotime("$year_month -1 month")); //한달전
$data[remain_price] = $m_pod->getRemainPrice($cid, $_GET[mid], " and left(orddt,7) = '$prev_month'");
//debug($price_data);

//현재 월기준으로 조회.
$addwhere = " and left(orddt,7) = '$year_month'";

//잔액(미수금)이 있는 주문만 조회.
$addwhere .= " and remain_price > 0";

$sales_list = $m_pod->getPodPayList($cid, $_GET[mid], $addwhere);
//debug($sales_list);

if ($sales_list) {
    $totalCnt = count($sales_list);
    //debug($totalCnt);
    $totpayprice = 0;
    $totvatprice = 0;
    $totorderprice = 0;
    $totdepositprice = 0;
    
    foreach ($sales_list as $key => $val) {
        $totpayprice += $val[payprice];
        $totvatprice += $val[vat_price];
        $totdepositprice += $val[deposit_price];
    }
    
    $totorderprice = $totpayprice+$totvatprice;
}
//debug($totalCnt);
//debug($totpayprice);
//debug($totvatprice);
//debug($totdepositprice);
//debug($totorderprice);
$data[total_cnt] = $totalCnt;
$data[tot_pay_price] = $totpayprice;
$data[tot_vat_price] = $totvatprice;
$data[tot_deposit_price] = $totdepositprice;
$data[tot_order_price] = $totorderprice;

class PDFK extends PDF_Korean
{
    function HeaderTable($data)
    {
        $this->SetFont('','B','24');
        $this->Cell(0,10,iconv('utf-8','euc-kr',"청 구 서"),0,0,'C');
        $this->Ln();

        $this->SetFont('','','9');
        $this->Cell(0,10,iconv('utf-8','euc-kr',$data[cust_name]." 귀중"),0,0);
    
        $this->SetXY( 10, 30 );
    
        $this->SetFont('','B','10');
        //$this->SetFillColor(128,128,128);
        $this->SetFillColor(196,215,155);
        //$this->SetTextColor(255);
        $this->SetDrawColor(92,92,92);
        $this->SetLineWidth(.2);
    
        $this->Cell(25,7,iconv('utf-8','euc-kr',"일자"),1,0,'C',true);
        $this->Cell(50,7,iconv('utf-8','euc-kr',"품 명"),1,0,'C',true);
        $this->Cell(20,7,iconv('utf-8','euc-kr',"규격"),1,0,'C',true);
        $this->Cell(15,7,iconv('utf-8','euc-kr',"수량"),1,0,'C',true);
        $this->Cell(20,7,iconv('utf-8','euc-kr',"금액"),1,0,'C',true);
        $this->Cell(20,7,iconv('utf-8','euc-kr',"부가세"),1,0,'C',true);
        $this->Cell(20,7,iconv('utf-8','euc-kr',"소계"),1,0,'C',true);
        $this->Cell(20,7,iconv('utf-8','euc-kr',"잔액"),1,0,'C',true);
        $this->Ln();
    }
    
    function ContentTable($data, $list_data)
    {
        $this->SetFont('','',9);
        //$this->SetFillColor(128,128,128);
        //$this->SetTextColor(000,000,000);
        //$this->SetDrawColor(92,92,92);
        $this->SetLineWidth(.2);

        $this->Cell(25,7,"",1,0,'C');
        $this->Cell(50,7,iconv('utf-8','euc-kr',"★ 전 월 잔 액"),1,0,'C');
        $this->Cell(20,7,"",1,0,'C');
        $this->Cell(15,7,"-",1,0,'C');
        $this->Cell(20,7,"-",1,0,'C');
        $this->Cell(20,7,"-",1,0,'C');
        $this->Cell(20,7,"-",1,0,'C');
        $this->Cell(20,7,number_format($data[remain_price]),1,0,'C');
        $this->Ln();

        foreach ($list_data as $key => $value) {
            $this->Cell(25,7,substr($value[orddt],0,10),1,0,'C');
            $this->Cell(50,7,iconv('utf-8','euc-kr',$value[order_title]),1,0,'C');
            $this->Cell(20,7,iconv('utf-8','euc-kr',""),1,0,'C');
            $this->Cell(15,7,number_format($value[ea]),1,0,'C');
            $this->Cell(20,7,number_format($value[payprice]),1,0,'C');
            $this->Cell(20,7,number_format($value[vat_price]),1,0,'C');
            $this->Cell(20,7,number_format($value[payprice]+$value[vat_price]),1,0,'C');
            $this->Cell(20,7,number_format($value[remain_price]),1,0,'C');
            $this->Ln();
        }
    }

    function FooterTable($data)
    {
        $this->SetFont('','B','9');
        $this->SetLineWidth(.2);

        $this->Cell(25,7,"",'L');
        $this->Cell(50,7,"");
        $this->Cell(20,7,"");
        $this->Cell(15,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,iconv('utf-8','euc-kr',"전월잔액"),0,0,'C');
        $this->Cell(20,7,number_format($data[remain_price]),'R',0,'C');
        $this->Ln();
        
        $this->Cell(95,7,iconv('utf-8','euc-kr',"입금계좌 : $data[bill_bank_name] $data[bill_bank_no]"),'L',0,'C');
        $this->Cell(15,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,iconv('utf-8','euc-kr',"당월판매액"),0,0,'C');
        $this->Cell(20,7,number_format($data[tot_pay_price]),'R',0,'C');
        $this->Ln();

        $this->Cell(95,7,iconv('utf-8','euc-kr',"예금주명 : $data[bill_bank_owner]"),'L',0,'C');
        $this->Cell(15,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,iconv('utf-8','euc-kr',"부가세"),0,0,'C');
        $this->Cell(20,7,number_format($data[tot_vat_price]),'R',0,'C');
        $this->Ln();
        
        $this->Cell(25,7,"",'L');
        $this->Cell(50,7,"");
        $this->Cell(20,7,"");
        $this->Cell(15,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,iconv('utf-8','euc-kr',"총합계액"),0,0,'C');
        $this->Cell(20,7,number_format($data[tot_order_price]),'R',0,'C');
        $this->Ln();

        $this->Cell(25,7,"",'L');
        $this->Cell(50,7,"");
        $this->Cell(20,7,"");
        $this->Cell(15,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,"");
        $this->Cell(20,7,iconv('utf-8','euc-kr',"당월수금액"),0,0,'C');
        $this->Cell(20,7,number_format($data[deposit_price]),'R',0,'C');
        $this->Ln();
        
        $this->Cell(25,7,"",'LB');
        $this->Cell(50,7,"",'B');
        $this->Cell(20,7,"",'B');
        $this->Cell(15,7,"",'B');
        $this->Cell(20,7,"",'B');
        $this->Cell(20,7,"",'B');
        $this->Cell(20,7,iconv('utf-8','euc-kr',"현재잔액"),'B',0,'C');
        $this->Cell(20,7,number_format(($data[remain_price] + $data[tot_order_price]) - $data[deposit_price]),'RB',0,'C');
        $this->Ln();

        $this->SetFont('','','9');
        $this->Cell(0,10,iconv('utf-8','euc-kr',$data[bill_nameComp]." TEL: ". $data[bill_phoneComp]. " FAX: ".$data[bill_faxComp]),0,0);
        $this->Ln();
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
}

$pdf = new PDFK();
$pdf->AliasNbPages();
$pdf->AddUHCFont();
$pdf->AddPage();
$pdf->SetFont('UHC','',16);
$pdf->HeaderTable($data);
$pdf->ContentTable($data, $sales_list);
$pdf->FooterTable($data);
$pdf->PageTable();
$pdf->Output();
?>
