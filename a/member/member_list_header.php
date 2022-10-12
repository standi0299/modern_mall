<?
//날짜 버튼 클릭 후 조회시 버튼 색상 변경
if (!$_POST[date_buton_type]) $_POST[date_buton_type] = "week1";  //색상 표시 없애줌
$button_color = array("yesterday" => "inverse","today" => "inverse","tdays" => "inverse","week" => "inverse","month" => "inverse","all" => "inverse");
if ($_POST[date_buton_type]) {
   $button_color[$_POST[date_buton_type]] = "warning";
}
?>