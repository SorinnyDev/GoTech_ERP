<?
include_once("./_common.php");

# 반환값 배열
$return = array();
$return['ret_code'] = true;
$return['message'] = "";

if(!$mode){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[mode]";
	echo json_encode($return);
	exit;
}

if(!$wr_id){

	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[wr_id]";
	echo json_encode($return);
	exit;
}

if($mode == "DEL"){
	$sql = "UPDATE g5_write_product SET wr_delYn='Y' WHERE wr_id='".$wr_id."'";
	$rs = sql_query($sql);
	if($rs){
		$return['ret_code'] = true;
		$return['message'] = "삭제처리가 되었습니다.";
	}else{
		$return['ret_code'] = false;
		$return['message'] = "삭제처리에 실패하였습니다.";
	}
	echo json_encode($return);
	exit;
}
?>