<?php
include_once('./_common.php');

switch($_POST['wr_domain']){
	case "dodoskin":
	if(preg_match('/[a-zA-Z]/', $_POST['ordernum']))
		$hap_chk = true;
		$sOrdernum = preg_replace("/[^0-9]/", "", $_POST['ordernum']); //합배송 문자열 제거
	break;
	case "Shopee BR":
	if(strlen($_POST['ordernum']) > 14)
		$hap_chk = true;
		$sOrdernum = substr($_POST['ordernum'],0,13);
	break;
	case "Ebay-dodoskin":
	case "Ebay":
	if(strlen($_POST['ordernum']) > 6)
		$hap_chk = true;
		$sOrdernum = substr($_POST['ordernum'],0,6);
	break;
	case "AC":
	case "AC-CAD":
	if(strlen($_POST['ordernum']) > 17)
		$hap_chk = true;
		$sOrdernum = substr($_POST['ordernum'],0,17);
	break;
	case "AJP":
	case "ACF-CAD" :
	if(strlen($_POST['ordernum']) > 19)
		$hap_chk = true;
		$sOrdernum = substr($_POST['ordernum'],0,19);
	break;

	default :
	if(preg_match('/[a-zA-Z]/', $_POST['ordernum']))
		$hap_chk = true;
		$sOrdernum = preg_replace("/[^0-9]/", "", $_POST['ordernum']); //합배송 문자열 제거
	break;
}

//$sOrdernum = preg_replace("/[^0-9]/", "", $_POST['ordernum']);

$seq = explode("|@|",$rack[0])[0];

$sql = "update g5_sales3_list set 
	wr_delivery = '{$wr_delivery}',
	wr_delivery_fee = '{$wr_delivery_fee}',
	wr_delivery_fee2 = '{$wr_delivery_fee2}',
	wr_delivery_oil = '{$wr_delivery_oil}',
	wr_delivery_misu = {$wr_delivery_fee} + {$wr_delivery_fee2} + {$wr_delivery_oil} - wr_delivery_pay
	WHERE seq = '".$seq."'";
sql_query($sql);

$hapSql = "select * from g5_sales3_list where wr_order_num LIKE '%$sOrdernum%'";
$hapRst = sql_query($hapSql);
while($row=sql_fetch_array($hapRst)) {
	
	$update = "update g5_sales3_list set 
	wr_release_traking = '{$no}',
	wr_release_etc = '{$etc}',
	wr_release_use = 1,
	wr_release_date = '".G5_TIME_YMDHIS."',
	wr_release_mbid = '".$member['mb_id']."'
	
	where seq = '{$row['seq']}'
	";
	
	sql_query($update, true);
	
	
	
	if($row['wr_warehouse'] == '9000')
		$filed = 'wr_37_real';
	else if($row['wr_warehouse'] == '1000')
		$filed = 'wr_32_real';
	else if($row['wr_warehouse'] == '3000')
		$filed = 'wr_36_real';
    else if($row['wr_warehouse'] == '4000')
        $filed = 'wr_42_real';
    else if($row['wr_warehouse'] == '5000')
        $filed = 'wr_43_real';
    else if($row['wr_warehouse'] == '6000')
        $filed = 'wr_44_real';
    else if($row['wr_warehouse'] == '7000')
        $filed = 'wr_40_real';
    else if($row['wr_warehouse'] == '8000')
        $filed = 'wr_41_real';

	####################################################
	# 실재고 조사를 위해 실재고관련 필드 업데이트
	####################################################
	if($row['wr_release_use'] != "1"){
		if($row['wr_direct_use'] == 1 || $row['wr_warehouse'] == "3000") {
			sql_query("update g5_write_product set {$filed} = {$filed} - {$row['wr_ea']} where wr_id = '{$row['wr_product_id']}'");
		}else{
			sql_query("update g5_write_product set wr_37_real = wr_37_real - {$row['wr_ea']} where wr_id = '{$row['wr_product_id']}'");
		}
	}

	/*
	//24.06.26 입고생성시 재고차감 되므로 필요없음.
	if($row['wr_direct_use'] != 1) {
	sql_query("update g5_write_product set {$filed} = {$filed} - {$row['wr_ea']} where wr_id = '{$row['wr_product_id']}' limit 1", true);
	
	$rack = sql_fetch("SELECT b.seq, total_stock
			FROM g5_rack b
			LEFT JOIN (
				SELECT wr_rack, SUM(wr_stock) AS total_stock
				FROM g5_rack_stock
				WHERE wr_warehouse = '{$row['wr_warehouse']}'
				AND wr_product_id = '{$row['wr_product_id']}'
				GROUP BY wr_rack
			) AS a ON a.wr_rack = b.seq
			WHERE a.wr_rack IS NOT NULL 
			AND a.total_stock > 0
			AND b.gc_name != '임시창고'
			ORDER BY b.seq
			LIMIT 1");
			
			
	$sql = "insert into g5_rack_stock set wr_warehouse = '{$row['wr_warehouse']}', wr_rack = '{$rack['seq']}', wr_stock = '-{$row['wr_ea']}', wr_product_id = '{$row['wr_product_id']}', wr_sales3_id = '{$row['seq']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	}
	*/
}

if($_POST['rack']){
	
	for($i=0; $i<count($_POST['rack']); $i++) {
		
		$_val = explode('|@|', $_POST['rack'][$i]);
		$_sales = sql_fetch("select * from g5_sales3_list where seq = '{$_val[0]}'");
		
		//24.06.26 입고생성시 재고차감 되므로 필요없음.
		/*$sql = "insert into g5_rack_stock set wr_warehouse = '{$_sales['wr_warehouse']}', wr_rack = '{$_val[1]}', wr_stock = '-{$_sales['wr_ea']}', wr_product_id = '{$_sales['wr_product_id']}', wr_sales3_id = '{$_sales['seq']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
		sql_query($sql, true);
		
		sql_query("update g5_sales3_list set wr_rack = '{$_val[1]}' where seq = '{$_sales['seq']}'");*/
		
	}
	
}


die('y');

?>