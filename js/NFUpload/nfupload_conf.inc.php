<?
/**
* NFUpload - 플래시 기반의 업로드 프로그래스바가 지원되는 멀티업로드 프로그램
*
* 라이센스 : 프리웨어 (개인/회사 구분없이 무료로 사용가능)
* 제작사 : 패스코리아넷 (http://passkorea.net/)
*
* 배포시 주의사항 : 제작사와 라이센스 정보를 삭제하시면 안됩니다.
*/

$__NFUpload = array();

// 업로드할 파일이 저장될 디렉토리.  퍼미션을 777로 조절 필요.  ex) chmod -R 777 data
$__NFUpload['dir'] = 'data';

// 최대 업로드 용량 - 파일 하나당(단위 KB).  ex) 10240 => 10MB, 102400 => 100MB
$__NFUpload['max_size'] = 102400;

// 최대 업로드 용량 - 전체 파일(단위 KB).  ex) 10240 => 10MB, 102400 => 100MB
$__NFUpload['max_size_total'] = 102400;

// 최대 파일갯수 제한.
$__NFUpload['max_count'] = 10;

// 업로드시 중복파일 덮어씌우기 여부.
#$__NFUpload['file_overwrite'] = true;		// 원본파일명 유지, 덮어씌우기 모드.
$__NFUpload['limit_ext'] = 'php;php3;php4;html;htm;inc;js';		// 업로드를 제한할 파일 확장자명.
$__NFUpload['file_overwrite'] = false;		// 고유파일명으로 변환.  자동중복방지, 파일확장자 제한 불필요.  (권장)
#$__NFUpload['limit_ext'] = '';		// 업로드를 제한할 파일 확장자명.

// 한글 인코딩 방법.
#$__NFUpload['charset'] = 'euc-kr';		// PHP iconv 모듈이 반드시 있어야 함!
$__NFUpload['charset'] = 'utf-8';		// 웹페이지가 utf-8로 제작된 경우에 한함.

// 디버깅 모드 사용여부.(업로드 문제시 원인 분석가능)
$__NFUpload['debug'] = true;

// 보관함 코드 생성.
$micro = explode(" ",microtime());
$storageKey = substr($micro[1].sprintf("%03d",floor($micro[0]*1000)), -6);
$storageKey = date("Ymd")."-".$storageKey;

// 업로드 처리 경로.
// 업로드 소스파일 경로 (반드시 전체주소를 입력해야함)
$__NFUpload['UploadUrl'] = "http://files.ilark.co.kr/portal_upload/estm/file/nfupload.aspx?center_id=$cfg_center[center_cid]&mall_id=$cid&storage_code=$storageKey";

//파일 필터링 값.
//$__NFUpload['FileFilter'] = ""; // 파일 필터링 값 ("이미지(*.jpg)|:|*.jpg;*.gif;*.png;*.bmp"); // 기본값 모든파일
//$__NFUpload['FileFilter'] = "이미지 파일|:|*.jpg;*.jpeg;*.gif;*.png;*.bmp"; // 파일 필터링 값 ("이미지(*.jpg)|:|*.jpg;*.gif;*.png;*.bmp"); // 기본값 모든파일
$__NFUpload['FileFilter'] = "이미지 파일|:|*.zip;*.psd;*.ai;*.pdf;*.jpg;*.jpeg;*.png;*.qxf;*.alz;*.eps;*.cdr;*.indd;*.qxd;*.id;*.psd;*.dotx;*.xls;*.xlsx;*.ppt;*.pptx;*.doc;*.docx;*.hwp";

// 업로드 폼에 사용되는 값 (기본값(UploadData))
$__NFUpload['DataFieldName'] = "upfile";

// 업로드 컴포넌트 플래쉬 파일명
$__NFUpload['Flash_Url'] = "/js/NFUpload/nfupload.swf?d=20130913";

// 화면 구성
// 업로드 컴포넌트 넓이 (기본값 480)
$__NFUpload['Width'] = 450;
// 업로드 컴포넌트 폭 (기본값 150)
$__NFUpload['Height'] = 200;
// 컴포넌트에 출력되는 파일명 제목 (기본값: File Name)
$__NFUpload['ColumnHeader1'] = "파일명";
// 컴포넌트에 출력되는 용량 제목 (기본값: File Size)
$__NFUpload['ColumnHeader2'] = "용량";
// 컴포넌트에서 사용되는 폰트 (기본값: Times New Roman)
$__NFUpload['FontFamily'] = "굴림";
// 컴포넌트에서 사용되는 폰트 크기 (기본값: 11)
$__NFUpload['FontSize'] = "11";

// [2008-10-28] Flash 10 support
// 파일찾기 이미지
$__NFUpload['Img_FileBrowse'] = "/js/NFUpload/images/btn_file_browse.gif";
// 파일찾기 이미지 넓이 (기본값 59)
$__NFUpload['Img_FileBrowse_Width'] = "59";
// 파일찾기 이미지 폭 (기본값 22)
$__NFUpload['Img_FileBrowse_Height'] = "22";
// 파일삭제 이미지
$__NFUpload['Img_FileDelete'] = "/js/NFUpload/images/btn_file_delete.gif";
// 파일삭제 이미지 넓이 (기본값 59)
$__NFUpload['Img_FileDelete_Width'] = "59";
// 파일삭제 이미지 폭 (기본값 22)
$__NFUpload['Img_FileDelete_Height'] = "22";
// 파일용량 텍스트
$__NFUpload['TotalSize_Text'] = "첨부용량 ";
// 파일용량 텍스트 폰트
$__NFUpload['TotalSize_FontFamily'] = "굴림";
// 파일용량 텍스트 폰트 크기
$__NFUpload['TotalSize_FontSize'] = "12";

// [2013-09-13] 플래쉬 라이브러리 버그로 인한 파일명 인코딩 여부 추가.
// 파일명 인코딩 여부   
$__NFUpload['Enable_Encoding_Filename'] = true;                   

?>