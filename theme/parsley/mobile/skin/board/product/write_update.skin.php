<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$mb = get_member($wmb_id, 'mb_name');
/*wr_32, 36, 37, 39, 40, 41 필드는 재고수량 필드,, 여기서 업데이트안함 */


$wr_34_arr = [];
$wr_35_arr = [];

for($i=0; $i<count($_POST['wr_34']); $i++) {
	
	if(!$_POST['wr_34'][$i]) continue;
	
	$wr_34_arr[] = $_POST['wr_34'][$i];
}

$wr_34 = implode('|@|', $wr_34_arr);

for($i=0; $i<count($_POST['wr_35']); $i++) {
	
	if(!$_POST['wr_34'][$i] || !$_POST['wr_35'][$i]) continue;
	
	$wr_35_arr[] = $_POST['wr_35'][$i];
}

$wr_34 = implode('|@|', $wr_34_arr);
$wr_35 = implode('|@|', $wr_35_arr);


$sql = "update {$write_table} set 
mb_id = '{$wmb_id}',
wr_name = '{$mb['mb_name']}',
wr_11 = '{$wr_11}',
wr_12 = '{$wr_12}',
wr_13 = '{$wr_13}',
wr_14 = '{$wr_14}',
wr_15 = '{$wr_15}',
wr_16 = '{$wr_16}',
wr_17 = '{$wr_17}',
wr_18 = '{$wr_18}',
wr_19 = '{$wr_19}',
wr_20 = '{$wr_20}',
wr_21 = '{$wr_21}',
wr_22 = '{$wr_22}',
wr_23 = '{$wr_23}',
wr_24 = '{$wr_24}',
wr_25 = '{$wr_25}',
wr_26 = '{$wr_26}',
wr_27 = '{$wr_27}',
wr_28 = '{$wr_28}',
wr_29 = '{$wr_29}',
wr_30 = '{$wr_30}',
wr_31 = '{$wr_31}',
wr_33 = '{$wr_33}',
wr_34 = '{$wr_34}',
wr_35 = '{$wr_35}',
wr_rack = '{$wr_rack}',
wr_warehouse = '{$wr_warehouse}'

where wr_id = '{$wr_id}'";

if(sql_query($sql,true)){
	// $wr_subject = sql_fetch("SELECT wr_subject FROM g5_write_sales WHERE wr_16 = '{$wr_1}' ")['wr_subject'];

	// // 담당자 전체 변경
	// sql_query("update g5_write_product set mb_id = '{$wmb_id}', wr_name = '{$wr_name}' where wr_1 = '{$wr_1}' "); //sku로 매치 업데이트
	// sql_query("update g5_write_sales set mb_id = '{$wmb_id}' where wr_subject = '{$wr_subject}' ");
	// sql_query("update g5_sales1_list set mb_id = '{$wmb_id}' where wr_order_num = '{$wr_subject}' ");
	// sql_query("update g5_sales2_list set mb_id = '{$wmb_id}' where wr_order_num = '{$wr_subject}' ");
	// sql_query("update g5_sales3_list set mb_id = '{$wmb_id}' where wr_order_num = '{$wr_subject}' ");
	
	//기존 판매관리에 등록 된 sku값 일괄변경 
	sql_query("update g5_sales0_list set wr_code = '{$wr_1}' where wr_product_id = '{$wr_id}'"); 
	sql_query("update g5_sales1_list set wr_code = '{$wr_1}' where wr_product_id = '{$wr_id}'"); 
	sql_query("update g5_sales2_list set wr_code = '{$wr_1}' where wr_product_id = '{$wr_id}'"); 
	sql_query("update g5_sales3_list set wr_code = '{$wr_1}' where wr_product_id = '{$wr_id}'"); 
}
