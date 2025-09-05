<?php
include_once('./_common.php');

// 제품이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '50M');

function only_number($n)
{
    return preg_replace('/[^0-9]/', '', (string)$n);
}

$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if( ! $is_upload_file){
    alert("엑셀 파일을 업로드해 주세요.");
}

if($is_upload_file) {
    $file = $_FILES['excelfile']['tmp_name'];

    include_once(G5_LIB_PATH.'/PHPExcel/IOFactory.php');

    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    $dup_it_id = array();
    $fail_it_id = array();
    $dup_count = 0;
    $total_count = 0;
    $fail_count = 0;
	$error_count = 0;
    $succ_count = 0;

    for ($i = 2; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);
		
		$SKU1 = $rowData[0][0];
		$domain = $rowData[0][1];
		$warehouse = $rowData[0][2];
		$product_fee = $rowData[0][3];
		$paypal_fee = $rowData[0][4];
		$grant_price = $rowData[0][5];
		$FBA_fee = $rowData[0][6];

		$sql = "SELECT * FROM g5_write_product WHERE wr_1='".$SKU1."'";
		$row = sql_fetch($sql);
		$wr_id = $row['wr_id'];

		if(isDefined($wr_id) == false){
			$fail_count++;
		}else{

			$sql = "INSERT INTO g5_write_product_fee(wr_id, domain,warehouse,fee_type,product_fee,paypal_fee,grant_price,FBA_fee,regdate) \n";
			$sql .= "VALUES('".$wr_id."', '".$domain."', '".$warehouse."', '1', '".(float)$product_fee."','".(float)$paypal_fee."', '".(float)$grant_price."','".(float)$FBA_fee."',NOW()) \n";
			$sql .= "ON DUPLICATE KEY UPDATE product_fee = '".(float)$product_fee."' \n";
			$sql .= "	,paypal_fee = '".(float)$paypal_fee."' \n";
			$sql .= "	,grant_price = '".(float)$grant_price."' \n";
			$sql .= "	,FBA_fee = '".(float)$FBA_fee."'";

			$result = sql_query($sql);

			if($result){
				$succ_count++;
			}else{
				$error_count++;
			}
		}
	}
}

$g5['title'] = '수수료 엑셀일괄등록 결과';
include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>

<div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
        <p>수수료 등록을 완료했습니다.</p>
    </div>

    <dl id="excelfile_result">
        <dt>총제품수</dt>
        <dd><?php echo number_format($total_count); ?></dd>

        <dt>완료건수</dt>
        <dd><?php echo number_format($succ_count); ?></dd>

        <dt>실패건수</dt>
        <dd><?php echo number_format($fail_count); ?></dd>

		<dt>오류건수</dt>
		<dd><?=number_format($error_count)?></dd>

    </dl>

    <div class="btn_win01 btn_win">
        <button type="button" onclick="window.close();">창닫기</button>
    </div>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');