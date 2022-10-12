<?

function f_getCaseNm($casesize){

  $casenm = array (
            _("아이폰 6") => array ('iphone6'), 
            _("아이폰 6 플러스") => array ('iphone6+',_("iphone6플러스"),_("iphone6 플러스")),
            _("아이폰 5S") => array ('iphone5s' , 'iphone5/5s'),
            _("아이폰 5C") => array ('iphone5c'),
            _("아이폰 4S") => array ('iphone4/4s'),
            _("갤럭시노트 4") => array ('galaxynote4', 'Galaxy note4' , _("갤럭시노트4")),
            _("갤럭시노트 3") => array ('galaxynote3', 'Galaxy note3' , _("갤럭시노트3")),
            _("갤럭시노트 2") => array (_("갤럭시노트2") , 'galaxynote2', 'Galaxy NOTE2'),
            _("갤럭시노트 1") => array ('Galaxy note1', 'galaxynote' ,'Galaxy Note', ),
            _("갤럭시 S5") => array (_("갤럭시s5"), 'Galaxy S5', 'galaxys5'),
            _("갤럭시 S4") => array ('galaxys4' , _("갤럭시s4"),'Galaxy S4'),
            _("갤럭시 S3") => array ('Galaxy S3' , 'galaxys3' ,_("갤럭시s3"),_("갤럭시 s3")),
            'LG G3' => array ('LG G3'),
            'LG G2' => array ('LGG2' ,'LG G2'),
            'LG Gpro2' => array ( _("옵티머스Gpro2") , _("옵티머스 Gpro2") , 'Optimus Gpro2'),
            _("베가 아이언") => array ('VEGA IRON', 'Vega iron'),
            _("베가 시크릿노트") => array ('VEGA Secret Note', 'Vega secret note')
    );
 
    foreach ($casenm as $k => $v) {
        foreach ($v as $val){
          if ($casesize == $val) return $k;
        } 
    }    
  return ""; 
}

?>