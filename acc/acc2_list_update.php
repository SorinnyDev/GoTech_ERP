<?php
include_once('./_common.php');

# 결과 리턴
$return = array();
$return['ret_code'] = true;
$return['message'] = "";

if(!$member){
    $return['ret_code'] = false;
	$return['message'] = "로그인 후 이용해주세요.";
	echo json_encode($return);
    exit;
}

if(count((array)$seq_arr)==0){
	$return['ret_code'] = false;
	$return['message'] = "선택된 목록이 없습니다.";
	echo json_encode($return);
    exit;
}else if(!isDefined($mode)){
	$return['ret_code'] = false;
	$return['message'] = "필수값이 누락되었습니다.[mode]";
	echo json_encode($return);
    exit;
}

$flag = true;
$msg = "업데이트가 완료되었습니다.";

# 트랜잭션 시작
sql_trans_start();

foreach($seq_arr as $k => $v){
  $total_price[$v] = str_replace(',', '', $total_price[$v]);
  $pay_price[$v] = str_replace(',', '', $pay_price[$v]);
  $misu[$v] = str_replace(',', '', $misu[$v]);

  if ($flag == true) {
		if($mode == "wr_orderer"){
			$wr_warehouse_price = $pay_price[$v];
			$wr_misu = $misu[$v];

      if ($is_all == 'Y') {
        $wr_warehouse_price = $total_price[$v];
        $wr_misu = 0;
      }
			
			$sql = "UPDATE g5_sales2_list SET wr_warehouse_price='".$wr_warehouse_price."',wr_misu = '".$wr_misu."' WHERE seq='".$v."'";
			$rs = sql_query($sql);
			if($rs){
				$sql = "UPDATE g5_sales3_list SET wr_misu='".$wr_misu."' WHERE sales2_id='".$v."'";
				$rs = sql_query($sql);
				if(!$rs){
					$flag = false;
					$msg = "업데이트에 실패하였습니다.[sales2_id:".$v." / sales3]";
				}
			}else{
				$flag = false;
				$msg = "업데이트에 실패하였습니다.[seq:".$v." / sales2]";
			}
		} else if($mode == "wr_delivery") {
			$wr_delivery_pay = $pay_price[$v];
			$wr_delivery_misu = $misu[$v];
			$sql = "UPDATE g5_sales3_list SET wr_delivery_pay='".$wr_delivery_pay."',wr_delivery_misu='".$wr_delivery_misu."' WHERE seq='".$v."'";
			$rs = sql_query($sql);
			if(!$rs){
				$flag = false;
				$msg = "업데이트에 실패하였습니다.[seq:".$v." / sales3]";
			}
		}
	}
}

if($flag == true){
	sql_trans_commit();
}else{
	sql_trans_rollback();
}

$return['ret_code'] = $flag;
$return['message'] = $msg;


echo json_encode($return);
exit;


?>