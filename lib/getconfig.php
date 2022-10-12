<? 

$query = "select * from tb_kids_class where mid = '$mid'";

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<config>
    <request url_encoding="utf-8">
        <url id="file_upload"><![CDATA[http://localr/lib/kids_upload.php]]></url>
        <url id="member_list"><![CDATA[http://ilark.co.kr/members.php]]></url>
    </request>
    <user id="thegirl92">
        <name><![CDATA[<?=_("김말똥")?>]]></name>
        <num><![CDATA[1566]]></num>
        <sessionparam><![CDATA[book=12345]]></sessionparam>
    </user>
    <site id="11111">
        <name><![CDATA[22222]]></name>
    </site>
    <options>
        <option id="multi_upload">2</option>
    </options>
</config>
