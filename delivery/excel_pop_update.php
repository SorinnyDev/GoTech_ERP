<?php
include_once('./_common.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);
// 제품이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '250M');

function only_number($n)
{
    return preg_replace('/[^0-9]/', '', (string)$n);
}

$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if( ! $is_upload_file){
    alert("엑셀 파일을 업로드해 주세요.");
}


if(!$wr_code)
	alert('업로드하실 배송사를 선택하셔야 합니다.');

//기존 업로드 된 배송사 초기화
@sql_query("delete from g5_shipping_price where cust_code = '{$wr_code}'");

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
    $succ_count = 0;

    for ($i = 1; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);

			$wr_code                 = addslashes($rowData[0][0]); //배송사코드
			$weight_code             = addslashes($rowData[0][1]); //무게
		
			$ii = 2;
			
			$sql_add = "";
			
			for($a=1001; $a<=1247; $a++) {
				
				$rowData[0][$ii] = str_replace(',', '', $rowData[0][$ii]);
				
				${'N'.$a} = addslashes((int)$rowData[0][$ii]);
				$ii++;
				
				$sql_add .= ", N".$a." = '".${'N'.$a}."'";
			}
			
		
	
		

		if(!$wr_code || !$weight_code) {
			$fail_count++;
			 continue;
		}
		
		
		$sql = "insert into g5_shipping_price set 
		cust_code = '{$wr_code}',
		weight_code = '{$weight_code}'
		{$sql_add}
		";
		
		sql_query($sql, true);
		
	}
	
	alert('배송비가 입력되었습니다.');
}