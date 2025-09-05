<?php
include_once('./_common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

if(!$_POST['wr_id']) alert('잘못 된 접근입니다.');

if($mode == "move1_kor") {
	#미분류 재고 랙 이동 - 한국
	
	//이동창고 재고증감 
	$sql = "insert into g5_rack_stock set wr_warehouse = '1000', wr_rack = '{$ms1_rack}', wr_stock = '{$ms1_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	alert('[한국창고(1000)] 미분류 재고가 랙으로 이동되었습니다.');
	
} else if($mode == "move1_usa") {
	#미분류 재고 랙 이동 - 미국 
	
	//이동창고 재고증감 
	$sql = "insert into g5_rack_stock set wr_warehouse = '3000', wr_rack = '{$ms1_rack}', wr_stock = '{$ms1_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	alert('[미국창고(3000)] 미분류 재고가 랙으로 이동되었습니다.');
	
	
} else if($mode == "move2") {
	#일반재고 랙 이동
	
	$ms_stock = trim($_POST['ms_stock']);
	$ms_stock = (int)$ms_stock;
	
	//대상창고 재고차감 
	$sql = "insert into g5_rack_stock set wr_warehouse = '{$ms_warehouse}', wr_rack = '{$ms_rack}', wr_stock = '-{$ms_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql);
	
	//이동창고 재고증감 
	$sql2 = "insert into g5_rack_stock set wr_warehouse = '{$ms_warehouse2}', wr_rack = '{$ms_rack2}', wr_stock = '{$ms_stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
	sql_query($sql2);
	
	//이동후 전체재고 다시 계산 
	$stock_kor = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '1000' and wr_product_id = '{$wr_id}'"); //한국 전체 재고
	
	$stock_kor2 = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '7000' and wr_product_id = '{$wr_id}'"); //한국 전체 재고
	
	$stock_usa = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '3000' and wr_product_id = '{$wr_id}'"); //미국 전체 재고
	
	$stock_usa2 = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '8000' and wr_product_id = '{$wr_id}'"); //미국 전체 재고
	
	$stock_kor = (int)$stock_kor['total'];//한국창고
	$stock_kor2 = (int)$stock_kor2['total'];//한국반품창고
	$stock_usa = (int)$stock_usa['total'];//미국창고
	$stock_usa2 = (int)$stock_usa2['total'];//미국반품창고
	
	//전체재고 상품마스터에 다시 업데이트 
	sql_query("update g5_write_product set wr_32 = '{$stock_kor}', wr_36 = '{$stock_usa}', wr_40 = '{$stock_kor2}', wr_41 = '{$stock_usa2}' where wr_id = '{$wr_id}'");
	
	
	alert('재고가 선택하신 창고와 랙으로 이동되었습니다.');

} else if($mode == "indi") {
	
	if(!is_numeric($_POST['qty'])) alert('재고를 입력하세요.');
	if($_POST['ori_qty'] == $_POST['qty']) alert('재고 수량 변동이 없습니다.');
	
	if($_POST['ori_qty'] > $_POST['qty']) {
		//재고 감소
		$ea = (int)$_POST['qty'] - (int)$_POST['ori_qty']; // 증감 된 재고 계산
	} else {
		//재고 증감 
		$ea = (int)$_POST['qty'] - (int)$_POST['ori_qty']; // 증감 된 재고 계산
	}

	
	//$ea = (int)$_POST['qty'];
	$sql = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$seq}', wr_stock = '{$ea}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."', wr_move_log = '직접 변경' ";
    sql_query($sql);
	
	if($warehouse == 1000) {
		$stock_kor = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '1000' and wr_product_id = '{$wr_id}'"); //한국 전체 재고
		
		$stock_kor = (int)$stock_kor['total'];
		$sql_search = " wr_32 = '{$stock_kor}' ";
	} else if($warehouse == 3000) {
		$stock_usa = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '3000' and wr_product_id = '{$wr_id}'"); //미국 전체 재고
		
		$stock_usa = (int)$stock_usa['total'];
		$sql_search = " wr_36 = '{$stock_usa}' ";
        
	} else if($warehouse == 7000) {
		$stock_kor2 = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '7000' and wr_product_id = '{$wr_id}'"); //한국반품창고 전체 재고
		
		$stock_kor2 = (int)$stock_kor2['total'];
		$sql_search = " wr_40 = '{$stock_kor2}' ";
	} else if($warehouse == 8000) {
		$stock_usa = sql_fetch("select SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '8000' and wr_product_id = '{$wr_id}'"); //미국반품창고 전체 재고
		
		$stock_usa2 = (int)$stock_usa2['total'];
		$sql_search = " wr_41 = '{$stock_usa2}' ";
	}

	
	//상품 총재고 업데이트 
	sql_query("update g5_write_product set {$sql_search} where wr_id = '{$wr_id}'");
	
	alert('랙 재고가 변경 되었습니다.');
}

?>