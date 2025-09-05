<?php
include_once('./_common.php');

if ($is_guest) die('n');

# 원 입고 정보 불러오기
// 세트 상품인지 확인 후 세트 상품의 경우 해당 원주문번호,동일 wr_set_sku의 단가,신고가격,수수료1,수수료2 수정
$sql = "SELECT * FROM g5_sales2_list WHERE seq='".$seq."'";
$row = sql_fetch($sql);

$add_sql = "";

$upColumn = "wr_delivery = '{$wr_delivery}',
			wr_delivery_fee = '{$wr_delivery_fee}',
			wr_delivery_fee2 = '{$wr_delivery_fee2}',
			wr_delivery_oil = '{$wr_delivery_oil}',
			wr_danga = '".$wr_danga."',
			wr_singo = '".$wr_singo."',
			wr_fee1 = '".$wr_fee1."',
			wr_fee2 = '".$wr_fee2."',
			wr_taxType = '".$wr_taxType."'";

$sql = "update g5_sales2_list set 
    wr_pay_type = '{$wr_pay_type}',
	wr_warehouse_etc = '{$wr_order_etc}',
	wr_date3 = '{$wr_date3}',
	wr_pay_type = '{$wr_pay_type}',
	wr_warehouse_price = '{$wr_warehouse_price}',
	wr_warehouse_etc = '{$wr_warehouse_etc}',
	{$upColumn},
	wr_misu = '{$wr_misu}'
		{$add_sql}
	where seq = '{$seq}'
	";

	if(sql_query($sql, true)) {
		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales2_list SET \n";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num = '".$row['wr_ori_order_num']."' AND wr_set_sku = '".$row['wr_set_sku']."'";
			sql_query($sql);
		}
		
		$sql = "UPDATE g5_sales0_list SET \n";
		$sql .= $upColumn." \n";
		$sql .= "WHERE wr_order_num = '".$row['wr_order_num']."'";
		sql_query($sql);

		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales0_list SET \n";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num = '".$row['wr_ori_order_num']."' AND wr_set_sku = '".$row['wr_set_sku']."'";
			sql_query($sql);
		}

		$sql = "UPDATE g5_sales1_list SET \n";
		$sql .= $upColumn." \n";
		$sql .= "WHERE wr_order_num = '".$row['wr_order_num']."'";
		sql_query($sql);

		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales1_list SET \n";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num = '".$row['wr_ori_order_num']."' AND wr_set_sku = '".$row['wr_set_sku']."'";
			sql_query($sql);
		}

		$sql = "UPDATE g5_sales3_list SET \n";
		$sql .= $upColumn." \n";
		$sql .= "WHERE wr_order_num = '".$row['wr_order_num']."'";
		sql_query($sql);

		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales3_list SET \n";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num = '".$row['wr_ori_order_num']."' AND wr_set_sku = '".$row['wr_set_sku']."'";
			sql_query($sql);
		}
		
		die('y');
	} else {
		die('n');
	}
?>