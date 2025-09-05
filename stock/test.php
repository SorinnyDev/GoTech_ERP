<?php 
include_once('./_common.php');

@sql_query("delete from g5_rack_stock where wr_rack = '' ");

$sql = "select a.*, b.gc_name, SUM(a.wr_stock) AS cnt from g5_rack_stock a 
LEFT JOIN g5_rack b ON(a.wr_rack = b.seq) 
LEFT JOIN g5_write_product c ON(a.wr_product_id = c.wr_id) 

where a.wr_warehouse = '1000'
and c.wr_32 < 0

GROUP BY wr_product_id
";
$rst = sql_query($sql);
for($i=0; $row=sql_fetch_array($rst); $i++) {
	
	if($row['cnt'] < 0) {
		@sql_query("update g5_write_product set wr_32 = '0' where wr_id = '{$row['wr_product_id']}' LIMIT 1");
	} else {
		@sql_query("update g5_write_product set wr_32 = '{$row['cnt']}' where wr_id = '{$row['wr_product_id']}' LIMIT 1");
	}
}
?>