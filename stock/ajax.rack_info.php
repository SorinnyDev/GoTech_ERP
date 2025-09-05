<?php
include_once('./_common.php');

if ($is_guest) die('n');
$chk = 0;
$sql_common = " from g5_rack ";
$sql_search = " where gc_warehouse = '{$warehouse}' and gc_use = 1 order by gc_name asc";
$sql = " select * {$sql_common} {$sql_search}  ";

$result = sql_query($sql);
for($a=0; $rack=sql_fetch_array($result); $a++) {
	
	$stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$rack['gc_warehouse']}' and wr_rack = '{$rack['seq']}' and wr_product_id = '{$wr_id}' ");
	
		if($stock['total'] <= 0) continue;
	
?>
<div style="text-align:center"><strong><?php echo $rack['gc_name']?></strong> : <?php echo $stock['total']?>개</div>
<?php 

$chk++;
}

if($chk==0) echo '재고없음<br>';

echo '<a href="#none" class="close_rackinfo">▲</a>';