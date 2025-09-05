<?
include_once('./_common.php');
$wr_id = $_REQUEST['wr_id']; // 상품 seq
$wr_out_warehouse = $_REQUEST['wr_out_warehouse']; // 출발지 창고 코드


$sql = "SELECT GR.*,GRS.total_stock FROM g5_rack GR \n";
$sql .= "LEFT OUTER JOIN( \n";
$sql .= "	SELECT wr_rack,SUM(wr_stock) AS total_stock FROM g5_rack_stock WHERE wr_product_id='".$wr_id."' GROUP BY wr_rack \n";
$sql .= ")GRS ON GRS.wr_rack = GR.seq \n";
$sql .= "WHERE GR.gc_warehouse = '".$wr_out_warehouse."' AND GR.gc_use = 1 AND GR.gc_name != '임시창고' AND GRS.total_stock > 0 ORDER BY gc_name ASC;";
$rs = sql_query($sql);
?>
<option value="">출고 랙 선택</option>
<?while($row = sql_fetch_array($rs)){?>
	<option value="<?php echo $row['seq']?>" data="<?php echo $row['total_stock']?>"><?php echo $row['gc_name']?> (재고:<?php echo $row['total_stock']?>)</option>
<?}?>