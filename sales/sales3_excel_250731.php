<?php
include_once('./_common.php');

if ($is_guest)
	alert('로그인 후 이용바랍니다.');

// PHPExcel 라이브러리 경로 설정
require_once G5_LIB_PATH . '/PHPExcel.php';

// PHPExcel 객체 생성
$objPHPExcel = new PHPExcel();

// 문서 속성 설정
$objPHPExcel->getProperties()->setCreator("GotecERP")
	->setLastModifiedBy("GotecERP")
	->setTitle("출고등록(한국)")
	->setSubject("출고등록(한국)")
	->setDescription("출고등록(한국) 엑셀 파일")
	->setKeywords("출고등록 한국")
	->setCategory("출고등록");

// 첫 번째 시트 선택
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

// 제목 설정
$sheet->setCellValue('A1', '출고등록(한국)');
$sheet->mergeCells('A1:AK1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->setCellValue('A2', '출력일시 : ' . G5_TIME_YMDHIS);

// 헤더 설정
$headers = array(
	'순번',
	'출고',
	'도메인명',
	'주문번호',
	'주문자ID',
	'주문자명',
	'전화번호',
	'이메일',
	'주소',
	'도시명',
	'주명',
	'우편번호',
	'국가명',
	'랙 번호',
	'매출일자',
	'발주일자',
	'입고일자',
	'출고일자',
	'상품코드',
	'약칭명',
	'몰타이틀',
	'상품명칭',
	'대표코드',
	'HS코드',
	'수량',
	'박스수',
	'단가',
	'신고가격',
	'통화',
	'개당무게',
	'총무게',
	'특송여부',
	'나라',
	'배송사',
	'배송비',
	'추가배송비',
	'유츄할증료',
	'총 배송요금',
	'비고'
);

// 헤더 스타일 설정
$headerStyle = array(
	'font' => array('bold' => true),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => 'F2F2F2')
	),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

// 헤더 입력
foreach ($headers as $col => $header) {
	$cell = PHPExcel_Cell::stringFromColumnIndex($col) . '3';
	$sheet->setCellValue($cell, $header);
	$sheet->getStyle($cell)->applyFromArray($headerStyle);
}

// 데이터 쿼리
if ($warehouse == 1000)
	$sql_search = " (wr_warehouse = 1000 or wr_warehouse = 9000) ";
else if ($warehouse == 3000)
	$sql_search = " wr_warehouse = 3000 ";

if ($date1 && $date2)
	$sql_search .= " and wr_date4 BETWEEN '{$date1}' AND '{$date2}'";

$sql_search .= " and seq IN (" . implode(',', $_POST['seq']) . ") ";

if (!$sst && !$sod) {
	$sst = "seq";
	$sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "select * from g5_sales3_list where {$sql_search} {$sql_order} ";
$rst = sql_query($sql);

// 환율 정보 가져오기
$sql = "select rate, ex_eng from g5_excharge";
$result = sql_fetch_all($sql);
$ex_list = array_column($result, 'rate', 'ex_eng');
$ex_jpy = $ex_list['JPY'] / 100;

$rowNum = 4; // 데이터 시작 행
while ($row = sql_fetch_array($rst)) {
	$product = sql_fetch("select * from g5_write_product where wr_id = '{$row['wr_product_id']}'");

	$release_state = ($row['wr_release_use'] == 1) ? 'O' : 'X';

	$rack = sql_fetch("select wr_rack from g5_rack_stock where wr_warehouse = '1000' and wr_product_id = '{$row['wr_product_id']}' GROUP BY wr_rack HAVING SUM(wr_stock) > 0;");

	// 배송비 계산 로직 추가
	$delivery_nm = "";
	$delivery_fee = 0;
	$delivery_fee2 = 0;
	$delivery_oil = 0;
	$is_auto_calculated = false;

	if ($is_delivery_calc !== 'Y' || ($row['wr_delivery'] && $row['wr_delivery_fee'])) {
		$deliveryData = sql_fetch("SELECT * FROM g5_delivery_company WHERE wr_code='" . $row['wr_delivery'] . "'");
		$delivery_nm = $deliveryData['wr_name'];
		$delivery_fee = $row['wr_delivery_fee'];
		$delivery_fee2 = $row['wr_delivery_fee2'];
		$delivery_oil = $row['wr_delivery_oil'];
	} else {
		// 배송비 자동 계산
		$is_auto_calculated = true;

		$sql = "select * from g5_sales3_list where wr_ori_order_num = '{$row['wr_ori_order_num']}' AND wr_warehouse='" . $row['wr_warehouse'] . "' order by wr_order_num asc";
		$hap = sql_query($sql);

		$total_weight = 0;
		$total_weight2 = 0;
		$wr_weight3 = 0;
		while ($item2 = sql_fetch_array($hap)) {
			$wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_id = '{$item2['wr_product_id']}'");

			$total_weight += (float)$wr_weight3['wr_10'] * (int)$item2['wr_ea'];
			$total_weight2 += (float)$wr_weight3['wr_weight3'] * (int)$item2['wr_ea'];
		}

		// 중량무게 계산
		$weight1 = $row['wr_weight1'] ?? 0;
		$weight2 = $row['wr_weight2'] ?? 0;
    $weight3 = isNotEmpty($row['wr_weight3']) ? $row['wr_weight3'] : $total_weight2;

		// 실제 무게, 부피, 중량무게 중 가장 큰 값 사용
		$max_weight = max($total_weight, $weight1, $weight2, $weight3);

		// 국가 코드 가져오기
		$country_dcode = sql_fetch("SELECT wr_code as code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}'");
		$country = $country_dcode['code'];

		// 배송비 조회
		if ($row['wr_servicetype'] === '0003') {
			$sql = "
				SELECT {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name
				FROM g5_shipping_price A
				LEFT OUTER JOIN g5_delivery_company C ON C.wr_code = A.cust_code
				WHERE {$country} != 0
				and C.wr_use = '1'
				and weight_code = '1'
				and cust_code in ('1029', '1030', '1030')
				GROUP BY cust_code
				ORDER BY price ASC
			";
		} else if ($row['wr_servicetype'] === '0001') {
      # 특송여부가 0001인일 경우 FEDEX / DHL / UPS 중 가장 낮은 배송사로 계산 (UPS 없음)
      $sql = "
				SELECT {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name
				FROM g5_shipping_price A
				LEFT OUTER JOIN g5_delivery_company C ON C.wr_code = A.cust_code
				WHERE weight_code >= {$max_weight} 
        and {$country} != 0
				and C.wr_use = '1'
				and cust_code in ('1006', '1007')
				GROUP BY cust_code
				ORDER BY price ASC
			";
    } else if ($row['wr_servicetype'] === '0007' and $row['wr_domain'] === 'AJP') {
      # AJP 도메인의 특송 여부 0007 KSE로만 배송비 계산
      $sql = "
				SELECT {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name
				FROM g5_shipping_price A
				LEFT OUTER JOIN g5_delivery_company C ON C.wr_code = A.cust_code
				WHERE weight_code >= {$max_weight} 
        and {$country} != 0
				and C.wr_use = '1'
				and cust_code in ('1021')
				GROUP BY cust_code
				ORDER BY price ASC
			";

    } else {
			$sql = "SELECT {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name 
					FROM g5_shipping_price A
					LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
					WHERE weight_code >= {$max_weight} 
					AND {$country} != 0 
					and C.wr_use='1' 
					and cust_code not in ('1006', '1007', '1030')
					GROUP BY cust_code 
					ORDER BY price ASC";
		}

		$result = sql_fetch_all($sql);

		// 배송비 계산
		foreach ($result as $k => &$item) {
			if ($item['cust_code'] === '1029') {
				$item['price'] = round($item['price'] * $ex_list['USD']);
			}

			if ($item['cust_code'] === '1021') {
				$item['price'] = round($item['price'] * $ex_jpy);
			}

			if ($item['cust_code'] === '1029') {
				$item['price'] = $item['price'] * $total_weight;
			}

			if ($item['cust_code'] === '1030') {
				$item['price'] = $item['price'] * $max_weight;
			}

			$oil_percent = max($item['wr_percent'], 0);
			$item['oil_price'] = round($item['price'] * $oil_percent);

			$item['total_price'] = $item['oil_price'] + $item['price'];
		}

		// 가장 저렴한 배송사 선택
		usort($result, function ($a, $b) {
			return $a['total_price'] - $b['total_price'];
		});

		if (!empty($result)) {
			$cheapest = $result[0];
			$delivery_nm = $cheapest['wr_name'];
			$delivery_fee = $cheapest['price'];
			$delivery_fee2 = 0; // 추가 배송비는 0으로 설정
			$delivery_oil = $cheapest['oil_price'];
		}
	}

	$rack_name = ' ' . get_rack_name($row['wr_rack']);

	// 데이터 입력
	$sheet->setCellValue('A' . $rowNum, $rowNum - 3);
	$sheet->setCellValue('B' . $rowNum, $release_state);
	$sheet->setCellValue('C' . $rowNum, $row['wr_domain']);
  $sheet->setCellValueExplicit('D' . $rowNum, $row['wr_order_num'], PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue('E' . $rowNum, $row['wr_mb_id']);
	$sheet->setCellValue('F' . $rowNum, $row['wr_mb_name']);
	$sheet->setCellValueExplicit('G' . $rowNum, $row['wr_deli_tel'], PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue('H' . $rowNum, $row['wr_email']);
	$sheet->setCellValue('I' . $rowNum, $row['wr_deli_addr1'] . " " . $row['wr_deli_addr2']);
	$sheet->setCellValue('J' . $rowNum, $row['wr_deli_city']);
	$sheet->setCellValue('K' . $rowNum, $row['wr_deli_ju']);
	$sheet->setCellValue('L' . $rowNum, $row['wr_deli_zip']);
	$sheet->setCellValue('M' . $rowNum, $row['wr_deli_country']);
	$sheet->setCellValue('N' . $rowNum, $rack_name);
	$sheet->setCellValue('O' . $rowNum, $row['wr_date']);
	$sheet->setCellValue('P' . $rowNum, $row['wr_date2']);
	$sheet->setCellValue('Q' . $rowNum, $row['wr_date3']);
	$sheet->setCellValue('R' . $rowNum, $row['wr_date4']);
	$sheet->setCellValueExplicit('S' . $rowNum, $product['wr_1'], PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue('T' . $rowNum, $product['wr_2']);
	$sheet->setCellValue('U' . $rowNum, $row['wr_product_nm']);
	$sheet->setCellValue('V' . $rowNum, $product['wr_subject']);
	$sheet->setCellValueExplicit('W' . $rowNum, $product['wr_5'], PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValueExplicit('X' . $rowNum, $row['wr_hscode'], PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue('Y' . $rowNum, $row['wr_ea']);
	$sheet->setCellValue('Z' . $rowNum, 1); #박스수 일괄 1 고정
	$sheet->setCellValue('AA' . $rowNum, $row['wr_danga']);
	$sheet->setCellValue('AB' . $rowNum, $row['wr_singo']);
	$sheet->setCellValue('AC' . $rowNum, $row['wr_currency']);
	$sheet->setCellValue('AD' . $rowNum, $row['wr_weight1']);
	$sheet->setCellValue('AE' . $rowNum, $row['wr_weight2']);
	$sheet->setCellValue('AF' . $rowNum, $row['wr_servicetype']);
	$sheet->setCellValue('AG' . $rowNum, $row['wr_deli_country']);
	$sheet->setCellValue('AH' . $rowNum, $delivery_nm);
	$sheet->setCellValue('AI' . $rowNum, $delivery_fee);
	$sheet->setCellValue('AJ' . $rowNum, $delivery_fee2);
	$sheet->setCellValue('AK' . $rowNum, $delivery_oil);
	$sheet->setCellValue('AL' . $rowNum, $delivery_fee + $delivery_fee2 + $delivery_oil);
	$sheet->setCellValue('AM' . $rowNum, $row['wr_release_etc']);

	// 자동 계산된 배송비에 노란색 배경 적용
	if ($is_auto_calculated) {
		$yellowStyle = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FFFF00')
			)
		);

		$sheet->getStyle('AH' . $rowNum)->applyFromArray($yellowStyle); // 배송사
		$sheet->getStyle('AI' . $rowNum)->applyFromArray($yellowStyle); // 배송비
		$sheet->getStyle('AJ' . $rowNum)->applyFromArray($yellowStyle); // 추가배송비
		$sheet->getStyle('AK' . $rowNum)->applyFromArray($yellowStyle); // 유류할증료
		$sheet->getStyle('AL' . $rowNum)->applyFromArray($yellowStyle); // 총 배송요금
	}

	$rowNum++;
}

// 열 너비 자동 조정
foreach (range('A', 'AM') as $columnID) {
	$sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// 파일 다운로드 설정
$filename = "release_list_" . G5_TIME_YMD . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
