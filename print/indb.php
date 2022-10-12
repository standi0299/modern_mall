<?
include "../lib/library.php";
include_once 'lib_print.php';

switch($_POST[mode]) {

	# 작업 사이즈 처리.
    case "getWorkOptSize":
        
        //debug($_POST[opt_size]);
        //$_POST[opt_size] = "A4(210mm x 297mm)";
        //$_POST[opt_size] = "20*20";
        //debug($_POST[opt_size]);

        if (strpos($_POST[opt_size], "x") !== false) {
            //A4(210mm x 297mm)
            $size = explode("x", $_POST[opt_size]);
            //debug($size);
            if ($size[0]) {
                $s = explode("(", $size[0]);
                if ($s[1]) {
                    $ss = trim(str_replace("mm", "", $s[1]));
                    $json_data[size_x] = ($ss + 6);
                }
            }
            
            if ($size[1]) {
                $s = trim(str_replace("mm)", "", $size[1]));
                $json_data[size_y] = ($s + 6);
            }
        }
        else if (strpos($_POST[opt_size], "*") !== false) {
            $size = explode("*", $_POST[opt_size]);
            //debug($size);
            if ($size[0]) {
                $json_data[size_x] = ($size[0] + 6);
            }
            
            if ($size[1]) {
                $json_data[size_y] = ($size[1] + 6);
            }           
        }
                
        //debug($json_data);
        $json_data = json_encode($json_data);
        echo $json_data;
        exit;
        break;

    # 작업 사이즈 처리.
    case "getSenaka":

        $paper_width = array();
        
        //내지.
        foreach ($_POST['inside_page'] as $key => $value) 
        {
            $inside_page = $_POST['inside_page'][$key];
            $inside_paper = $_POST['inside_paper'][$key];
            $inside_paper_gram = $_POST['inside_paper_gram'][$key];

            //용지 두께  계산.
            if (!$_POST['inside_page'][$key]) $inside_page = 1;
            $p_width = $r_ipro_standard_paper_width[$inside_paper][$inside_paper_gram];
            $paper_width[] = ($inside_page * $p_width);
            
            $json_data['inside_senaka_'.$key] = ($inside_page * $p_width);
        }

        //간지,면지.
        foreach ($_POST['inpage_page'] as $key => $value) 
        {       
            $inpage_page = $_POST['inpage_page'][$key];
            $inpage_paper = $_POST['inpage_paper'][$key];
            $inpage_paper_gram = $_POST['inpage_paper_gram'][$key];

            //용지 두께  계산.
            if (!$_POST['inpage_page'][$key]) $inpage_page = 1;
            $p_width = $r_ipro_standard_paper_width[$inpage_paper][$inpage_paper_gram];
            $paper_width[] = ($inpage_page * $p_width);
            
            $json_data['inpage_senaka_'.$key] = ($inpage_page * $p_width);
        }
        
        //debug($paper_width);
        //용지 두께  합 계산.
        if (is_array($paper_width)) {
            foreach ($paper_width as $key => $val) {
                $paperWidth += $val; 
            }
        }
        //debug($paperWidth);
        $json_data['PaperWidth'] = $paperWidth;

        //debug($json_data);
        $json_data = json_encode($json_data);
        echo $json_data;
        exit;
        break;
}

//if (!$_POST[rurl])
//   $_POST[rurl] = $_SERVER[HTTP_REFERER];
//go($_POST[rurl]);
?>