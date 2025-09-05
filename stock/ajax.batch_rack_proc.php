<?
include_once('./_common.php');

# 결과 리턴 배열
$return = array();
$return['ret_code'] = true;
$return['message'] = "";

if(count((array)$wr_id_arr) == 0){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[wr_id]";
	echo json_encode($return);
	exit;
}

if(!$mb_id && !$wr_23 && !$wr_warehouse && !$wr_rack){
	$return['ret_code'] = false;
	$return['message'] = "수정할 내용을 선택해주세요.";
	echo json_encode($return);
	exit;
}

$query_arr = array();
# 담당자
if($mb_id){
	$query_arr[] = "mb_id='".$mb_id."'";
}

# 브랜드
if($wr_23){
	$query_arr[] = "wr_23='".$wr_23."'";
}
#창고
if($wr_warehouse){
	$query_arr[] = "wr_warehouse='".$wr_warehouse."'";
}
# 지정랙
if($wr_rack){
	$query_arr[] = "wr_rack='".$wr_rack."'";
}
$total = count($wr_id_arr);
$succ = 0;
$fail = 0;
foreach((array)$wr_id_arr as $key => $val){
	$sql = "UPDATE g5_write_product SET \n";
	$sql .= implode(",",$query_arr)." \n";
	$sql .= "WHERE wr_id='".$val."'";
	$rs = sql_query($sql);
	if($rs){
		$succ++;
	}else{
		$fail++;
	}
}

$msg = "담당자 지정랙 일괄 수정이 완료되었습니다.(전체:".number_format($total)." / 성공:".number_format($succ)." / 실패:".number_format($fail).")";
$return['message'] = $msg;

echo json_encode($return);
exit;
?>