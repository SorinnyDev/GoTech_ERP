<?
include_once('../common.php');
// 제품이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '1024M');

error_reporting(E_ALL);
ini_set('display_errors', '1');
/*
1. 입고 자료 가져오기
2. 출고 데이터 수량 가져와서 순차적 차감
   2-1. 순차적 차감시 해당 재고의 단가 가져오기
*/

$sql = "SELECT * FROM g5_sales3_list WHERE wr_product_id <> 0 GROUP BY wr_order_num ORDER BY wr_date4 ASC limit 100000,20000";
$sales3Rs = sql_query($sql);
$i = 0;
while($row = sql_fetch_array($sales3Rs)) {
	# g5_sales3_det 데이터 등록(선입 선출 데이터 등록)
	# 입고 데이터 중 출고 가능 데이터 조회
	$sql = "SELECT * FROM g5_sales2_list WHERE wr_product_id='".$row['wr_product_id']."' AND wr_chul_ea > 0 ORDER BY wr_date3 ASC";
	$rs = sql_query($sql);
	$wr_ea = $row['wr_ea'];
	while($ibRow = sql_fetch_array($rs)){
		if($wr_ea > 0){
			$ori_wr_ea = $wr_ea;
			$wr_ea = $wr_ea - $ibRow['wr_chul_ea'];
			if($wr_ea >= 0){
				# 입고 데이터의 출고 가능 잔여 수량 변경
				sql_query("UPDATE g5_sales2_list SET wr_chul_ea = '0' WHERE seq='".$ibRow['seq']."'");

				$sql = "INSERT INTO g5_sales3_det SET \n";
				$sql .= "	sales2_id = '".$ibRow['seq']."' \n";
				$sql .= "	,sales3_id = '".$row['seq']."' \n";
				$sql .= "	,wr_order_num = '".$row['wr_order_num']."' \n";
				$sql .= "	,wr_product_id = '".$row['wr_product_id']."' \n";
				$sql .= "	,wr_warehouse = '".$row['wr_warehouse']."' \n";
				$sql .= "	,wr_rack = '".$row['wr_rack']."' \n";
				$sql .= "	,ibgo_danga = '".$ibRow['wr_order_price']."' \n";
				$sql .= "	,chul_ea = '".$ibRow['wr_chul_ea']."' \n";
				$sql .= "	,chul_date = '".$row['wr_date4']."' \n";
				$sql .= "	,reg_date = NOW() \n";
				$sql .= "	,del_yn = 'N'";
				$rs = sql_query($sql);
			}else{
				# 입고 데이터의 출고 가능 잔여 수량 변경
				sql_query("UPDATE g5_sales2_list SET wr_chul_ea = '".(-1*$wr_ea)."' WHERE seq='".$ibRow['seq']."'");

				$sql = "INSERT INTO g5_sales3_det SET \n";
				$sql .= "	sales2_id = '".$ibRow['seq']."' \n";
				$sql .= "	,sales3_id = '".$row['seq']."' \n";
				$sql .= "	,wr_order_num = '".$row['wr_order_num']."' \n";
				$sql .= "	,wr_product_id = '".$row['wr_product_id']."' \n";
				$sql .= "	,wr_warehouse = '".$row['wr_warehouse']."' \n";
				$sql .= "	,wr_rack = '".$row['wr_rack']."' \n";
				$sql .= "	,ibgo_danga = '".$ibRow['wr_order_price']."' \n";
				$sql .= "	,chul_ea = '".$ori_wr_ea."' \n";
				$sql .= "	,chul_date = '".$row['wr_date4']."' \n";
				$sql .= "	,reg_date = NOW() \n";
				$sql .= "	,del_yn = 'N'";
				$rs = sql_query($sql);
			}
		}
	}
	$i++;
}
?>


