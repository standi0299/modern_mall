
<table class="addoptbox4">
<?
    //debug($item_data);
    //domoo, press, foil, uvc, bind_BD1, bind_BD3
?>
    
<?
//옵셋 후가공 예외.
if ($opt_mode == "domoo_opset" || $opt_mode == "press_opset" || $opt_mode == "foil_opset" || $opt_mode == "uvc_opset") 
{ 
?>

    <tr>
    <th style="width: 50%;">mm당</th>
    <th>가격</th>
    </tr>

    <?
    foreach ($item_data as $key => $val) 
    {
        $value = "0";
        
        if ($file_data[$val[opt_prefix].$val[opt_key]][1]) $value = $file_data[$val[opt_prefix].$val[opt_key]][1];
    ?>
        <tr>
        <td align='center'><?=$val[opt_value]?>[<?=$val[opt_prefix]?><?=$val[opt_key]?>] 1mm당</td>
        <td align='center'><input type='text' name='mm2_1_<?=$val[opt_prefix]?><?=$val[opt_key]?>' value='<?=$value?>'></td>
        </tr>
    <?
    }
}
else if ($opt_mode == "bind_BD1_opset" || $opt_mode == "bind_BD3_opset") 
{
?>
    <tr>
    <th>기본 페이지</th>
    <?if ($opt_mode == "bind_BD3_opset") {?>
    <th>기본 부수</th>
    <th>기본 가격</th>
    <?}?>
    <th>120g 이하</th>
    <th>121~149g</th>
    <th>150g 이상</th>
    </tr>
    
    <tr>
    <td align='center'><input type='text' name='mm2_page' value='<?=$default_data['page']?>'></td>
    <?if ($opt_mode == "bind_BD3_opset") {?>
    <td align='center'><input type='text' name='mm2_cnt' value='<?=$default_data['cnt']?>'></td>
    <td align='center'><input type='text' name='mm2_price' value='<?=$default_data['price']?>'></td>
    <?}?>
    <td align='center'><input type='text' name='mm2_120g' value='<?=$page_gram_data['120g']?>'></td>
    <td align='center'><input type='text' name='mm2_140g' value='<?=$page_gram_data['140g']?>'></td>
    <td align='center'><input type='text' name='mm2_150g' value='<?=$page_gram_data['150g']?>'></td>
    </tr>
        
<?
}
?>

</table>