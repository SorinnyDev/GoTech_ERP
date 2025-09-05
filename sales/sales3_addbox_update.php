<?php
include_once('./_common.php');

if ($is_guest) die('n');

$upColumn = "wr_deli_nm = '" . addslashes($wr_deli_nm) . "'
			,wr_deli_tel = '" . addslashes(str_replace("\\", "", $wr_deli_tel)) . "'
			,wr_deli_city = '" . addslashes($wr_deli_city) . "'
			,wr_deli_ju = '" . addslashes($wr_deli_ju) . "'
			,wr_deli_country = '" . addslashes($wr_deli_country) . "'
			,wr_deli_zip = '" . addslashes($wr_deli_zip) . "'
			,wr_deli_addr1 = '" . addslashes($wr_deli_addr1) . "'
			,wr_deli_addr2 = '" . addslashes($wr_deli_addr2) . "'
			,wr_delivery = '" . addslashes($wr_delivery) . "'
			,wr_delivery_fee = '" . $wr_delivery_fee . "'
			,wr_delivery_fee2 = '" . $wr_delivery_fee2 . "'
			,wr_delivery_oil = '" . $wr_delivery_oil . "'
			,wr_weight3 = '" . $wr_weight3 . "' ";
/*
			,wr_delivery_fee2 = '".$wr_deliverey_fee2."'
			
			,wr_release_traking = '".$wr_release_traking."'
			,wr_release_etc = '".$wr_release_etc."'
*/
// wr_weight3:부피 / wr_release_traking:수출트래킹NO / wr_release_etc:기타
/*
$sql = "update g5_sales3_list set 
	
    wr_weight3 = '{$wr_weight3}',
	wr_delivery = '{$wr_delivery}',
	wr_release_traking = '{$wr_release_traking}',
	wr_delivery_fee2 = '{$wr_delivery_fee2}',
	wr_release_etc = '{$wr_release_etc}'
	
	where seq = '{$seq}'
	";
*/
$sql = "UPDATE g5_sales3_list SET ";
$sql .= $upColumn;
$sql .= ",wr_delivery_fee2 = '" . $wr_delivery_fee2 . "'
		,wr_release_traking = '" . $wr_release_traking . "'
		,wr_release_etc = '" . $wr_release_etc . "'
		,wr_hab_x = '" . $wr_hab_x . "'
		,wr_hab_y = '" . $wr_hab_y . "'
		,wr_hab_z = '" . $wr_hab_z . "'
		,wr_weight_sum1 = '" . $wr_weight_sum1 . "'
		,wr_weight_sum2 = '" . $wr_weight_sum2 . "'
		,wr_weight_sum3 = '" . $wr_weight_sum3 . "' ";
$sql .= "WHERE seq = '" . $seq . "'";

if (sql_query($sql, true)) {

  $row = sql_fetch("SELECT * FROM g5_sales3_list WHERE seq='" . $seq . "'");

  $sql = "UPDATE g5_sales0_list SET ";
  $sql .= $upColumn;
  $sql .= "WHERE seq = '" . $row['sales0_id'] . "'";
  sql_query($sql);

  $sql = "UPDATE g5_sales1_list SET ";
  $sql .= $upColumn;
  $sql .= "WHERE wr_order_num = '" . $row['sales1_id'] . "'";
  sql_query($sql);

  $sql = "UPDATE g5_sales2_list SET ";
  $sql .= $upColumn;
  $sql .= "WHERE wr_order_num = '" . $row['sales2_id'] . "'";
  sql_query($sql);

  # 기타이관 box
  if ($tbn_code) {
    $query = "select * from g5_stock_box where box_code = '$tbn_code'";
    $box = sql_fetch($query);

    if (!count($box)) {
      $query = "insert into g5_stock_box set box_code = '$tbn_code', reg_datetime = now()";
      sql_query($query);
    }

    $query = "select * from g5_stock_box_order where wr_order_num = '{$row['wr_order_num']}'";
    $box_order = sql_fetch($query);

    if (count($box_order) > 0) {
      $query = "delete from g5_stock_box_order where id = '{$box_order['id']}'";
      sql_query($query);
    }

    $query = "insert into g5_stock_box_order set box_code = '$tbn_code', wr_order_num = '{$row['wr_order_num']}', reg_datetime = now()";
    sql_query($query);
  }

  die('y');
} else {
  die('n');
}
?>