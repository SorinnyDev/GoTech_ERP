<?php  

	header("Content-Type:text/html;charset=utf-8"); 



	$EV_DOC_ROOT = $_SERVER['DOCUMENT_ROOT']."/";
	$EV_ROOT_PATH = "/";
	
	$EV_IMG_PATH = $EV_ROOT_PATH."admin/img/";
	$EV_CSS_PATH = $EV_ROOT_PATH."admin/css/";
	$EV_JS_PATH = $EV_ROOT_PATH."admin/js/";
	$EV_LIB_PATH = $EV_DOC_ROOT."lib/";
	$EV_INC_PATH = $EV_DOC_ROOT."admin/inc/";
	
	$EV_UPLOAD_PATH = $EV_ROOT_PATH."data/";
	$EV_UPLOAD_DIR = $EV_DOC_ROOT."data/";
	
	$EV_SITE_CHARSET = "utf-8";
	
	$EV_UPLOAD_EXT_IMG = "bmp,gif,jpg,png";
	$EV_UPLOAD_EXT_MOV = "swf,mov,mpg,mpeg,mp3,mp4";
	$EV_UPLOAD_EXT_DOC = "doc,hwp,txt,ppt,pptx,docx,pdf,zip,alz,html,htm,xlsx,xls";
	$EV_UPLOAD_EXT_ALL = $EV_UPLOAD_EXT_IMG.",".$EV_UPLOAD_EXT_MOV.",".$EV_UPLOAD_EXT_DOC;

	$EV_DB_SERVER = "localhost";
	$EV_DB_USER = "wave38_gotech";
	$EV_DB_PASS = "gotech1215@@";

	$EV_DB_NAME = "wave38_gotech";

	$EV_DB_CHARSET = "utf8"; //utf8 or euckr, no - character
	
	//현재 날짜 변수
	$thisYear = date("Y");
	$thisMonth = date("m");
	$thisDay = date("d");

?>
