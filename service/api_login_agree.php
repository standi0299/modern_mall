<?
$login_offset = true;
include "../_header.php";

$data = json_decode(base64_decode($_GET[data]),1);

$cfg[agreement] = getCfg('agreement');
$cfg[agreement2] = getCfg('agreement2');

$tpl->define(array(
   'header' => 'layout/layout.htm?header',
   'footer' => 'layout/layout.htm?footer',
));

$tpl -> assign('data', $data);
$tpl -> print_('tpl');
?>