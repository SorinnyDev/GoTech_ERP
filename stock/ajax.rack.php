<?php
include_once('./_common.php');

if ($is_guest) die('n');
if ($mode == "a") {
	$sql = "SELECT
						rack.seq,
						rack.gc_name,
						SUM(rs.wr_stock) AS total
					FROM
						g5_rack_stock rs
					LEFT JOIN
						g5_rack rack ON rs.wr_rack = rack.seq
					WHERE
						rs.wr_warehouse = '{$warehouse}' AND rs.wr_product_id = '{$wr_id}'
					GROUP BY
						rack.gc_name
					HAVING
						total > 0
					ORDER BY
						total ASC;";

	$result = sql_query($sql);

	for ($a = 0; $stock = sql_fetch_array($result); $a++) {
?>
		<option value="<?= $stock['seq'] ?>" data="<?= $stock['total'] ?>">
			<?= $stock['gc_name'] ?>(재고:<?= $stock['total'] ?>)</option>
	<?php
		$chk++;
	}
} else {
	$chk = 0;
	$sql_common = " from g5_rack ";
	$sql_search = " where gc_warehouse = '{$warehouse}' and gc_use = 1 order by gc_name asc";
	$sql = " select * {$sql_common} {$sql_search}  ";

	$result = sql_query($sql);
	for ($a = 0; $rack = sql_fetch_array($result); $a++) {
	?>
		<option value="<?= $rack['seq'] ?>">
			<?= $rack['gc_name'] ?></option>
<?php
		$chk++;
	}
	if ($chk == 0) echo '<option value="">재고없음</option>';
}