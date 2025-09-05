<?php
include_once('./_common.php');

$product_id = $connect_db->real_escape_string($_GET['product_id']);
$warehouse = $connect_db->real_escape_string($_GET['warehouse'] ?? '');

switch ($warehouse) {
	case 7000:
		$warename = "한국반품창고";
		break;
	case 8000:
		$warename = "미국반품창고";
		break;
	case 9000:
		$warename = "FBA반품창고";
		break;
	case 9100:
		$warename = "W-FBA반품창고";
		break;
	case 9200:
		$warename = "U-FBA반품창고";
		break;
	default:
		$warename = "";
}

$sql = "SELECT a.seq,
					a.mb_id,
					a.sales3_id,
					a.product_id,
					a.wr_order_num,
					a.wr_stock,
					-- a.wr_datetime,
					a.wr_state,
					-- a.wr_state_date,
					a.wr_product_state,
					b.wr_rack,
					b.wr_warehouse,
					c.wr_1,
					c.wr_subject,
					d.wr_domain,
					c.wr_22 AS 'price',
					i.seq AS 'img_seq',
					i.mb_id AS 'return_mb_id',
					i.wr_img1,
					i.wr_img2,
					i.wr_img3,
					i.wr_img4,
					i.wr_img5,
					i.wr_memo AS 'state_memo',
					i.wr_datetime AS 'img_datetime'
				FROM g5_return_list a
				LEFT JOIN g5_return_stock b ON a.seq = b.return_id
				LEFT JOIN g5_write_product c ON a.product_id = c.wr_id
				LEFT JOIN g5_sales3_list d ON a.sales3_id = d.seq
				LEFT JOIN g5_return_img i ON i.return_id = a.seq
				WHERE (1)
					AND REPLACE(b.wr_warehouse, ' ', '') LIKE '$warename'
					AND (a.wr_state = 2	OR a.wr_state = 1)
					AND a.product_id = $product_id
				ORDER BY a.seq DESC";

$rst = sql_query($sql);

$data_rows = [];
if ($rst) {
	while ($row = sql_fetch_array($rst)) {
		$data_rows[] = $row;
	}
} else {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: application/json');
	echo json_encode(['error' => 'Database query failed.', 'details' => $connect_db->error]);
	exit;
}

header('Content-Type: application/json');
echo json_encode($data_rows);

exit;