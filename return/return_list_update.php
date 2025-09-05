<?php 
include_once('./_common.php');

if(!$seq || !$stock) die('n');

$seq = (int)$_POST['seq'];
$wr_id = (int)$_POST['wr_id'];
$stock = (int)$_POST['stock'];

$sales3 = sql_fetch("select wr_order_num,wr_date,wr_code from g5_sales3_list where seq = '{$seq}'");

/*
$wr_product_id = sql_fetch("select wr_product_id from g5_sales3_list where wr_order_num = '{$sales0['wr_order_num']}' ")['wr_product_id'];
$item = sql_fetch("select wr_subject,wr_5 from g5_write_product where wr_id = '{$wr_product_id}' ");
$wr_num = get_next_num("g5_write_returns");
*/

$sql = "insert into g5_return_list set 
mb_id = '{$member['mb_id']}',
sales3_id = '{$seq}',
product_id = '{$wr_id}',
wr_order_num = '{$sales3['wr_order_num']}',
wr_stock = '{$stock}',
wr_datetime = '".G5_TIME_YMDHIS."'
";

// $arr = array("sales0"=>$sales0,"wr_product_id"=>$wr_product_id,"item"=>$item,"wr_num"=>$wr_num);

if(sql_query($sql, true)){
    /*
    $sql = "INSERT INTO g5_write_returns SET    wr_num = '{$wr_num}',
                                                mb_id = '{$member['mb_id']}',
                                                wr_name = '{$member['mb_name']}',
                                                wr_subject = '{$sales0['wr_order_num']}',
                                                wr_date1 = '{$sales0['wr_date']}',
                                                wr_date2 = '".G5_TIME_YMD."',
                                                wr_1 = '{$sales0['wr_code']}',
                                                wr_2 = '{$item['wr_subject']}',
                                                wr_3 = '{$item['wr_5']}',
                                                wr_4 = '{$stock}' ";
    //반품 상품 리스트업
    if(sql_query($sql)){
        $wr_parent = sql_insert_id();
        sql_query("UPDATE g5_write_returns SET wr_parent = '{$wr_parent}' WHERE wr_id = '{$wr_parent}' ");
        sql_query("UPDATE g5_board SET bo_count_write = bo_count_write + 1 WHERE bo_table = 'returns' ");
    }
    */

	die('y');
	// die(json_encode($arr));
} else {
	die('n');
}