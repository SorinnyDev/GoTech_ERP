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
$is_upload_file2 = (isset($_FILES['excelfile2']['tmp_name']) && $_FILES['excelfile2']['tmp_name']) ? 1 : 0;
$is_upload_file3 = (isset($_FILES['excelfile3']['tmp_name']) && $_FILES['excelfile3']['tmp_name']) ? 1 : 0;

if( ! $is_upload_file){
    alert("매출자료 엑셀 파일을 업로드해 주세요.");
}

if( ! $is_upload_file2){
    alert("정산금액 엑셀 파일을 업로드해 주세요.");
}
if( ! $is_upload_file3){
    alert("정산배송비 엑셀 파일을 업로드해 주세요.");
}

if(!$domain)
	alert('업로드하실 플랫폼을 선택하셔야 합니다.');



include_once(G5_LIB_PATH.'/PHPExcel/IOFactory.php');

$arr = array();

//매출엑셀
if($is_upload_file) {
    $file = $_FILES['excelfile']['tmp_name'];
	
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

	$savedir = "/home/gotec/www/upload/".date("Ymd")."/";
	FileUploadName("", $savedir, $file, $_FILES['excelfile']['name'], "");

    for ($i = 2; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);

		if($domain == "Qoo10" || $domain == "Qoo10-1") {
			
			
			$wr_subject              = addslashes($rowData[0][1]); //주문번호
			$arr[$wr_subject]['wr_subject'] = $wr_subject;

			$ori_order_num = addslashes($rowData[0][2]); //장바구니 번호
			$arr[$wr_subject]['ori_order_num'] = $ori_order_num; // 장바구니 번호
			
			$arr[$wr_subject]['wr_15'] = addslashes($rowData[0][27]); //통화
			$arr[$wr_subject]['cart_num'] = addslashes($rowData[0][2]); //장바구니 번호
			
			$shippingmethod            = trim(addslashes($rowData[0][14])); //배송코드
			$arr[$wr_subject]['wr_11'] = addslashes($rowData[0][14]); //수량
			$arr[$wr_subject]['wr_17'] = addslashes($rowData[0][13]); //상품명
			$arr[$wr_subject]['wr_16'] = addslashes(str_replace("'", "", $rowData[0][16])); //상품코드(sku)
			$arr[$wr_subject]['wr_2']  = addslashes($rowData[0][33]); //구매자이름
			$arr[$wr_subject]['wr_3']  = addslashes($rowData[0][22]); //주소1
			$arr[$wr_subject]['wr_8']  = addslashes(str_replace("'", "", $rowData[0][23])); //우편번호
			$arr[$wr_subject]['wr_7']  = addslashes($rowData[0][26]); //나라명
			$arr[$wr_subject]['wr_9']  = addslashes($rowData[0][21]); //전화번호
			$arr[$wr_subject]['wr_18'] = $domain; //도메인명

			# 배송관련 정보 추가
			$arr[$wr_subject]['wr_27'] = $arr[$wr_subject]['wr_2'];
			$arr[$wr_subject]['wr_28'] = $arr[$wr_subject]['wr_3'];
			$arr[$wr_subject]['wr_29'] = $arr[$wr_subject]['wr_4'];
			$arr[$wr_subject]['wr_30'] = $arr[$wr_subject]['wr_5'];
			$arr[$wr_subject]['wr_31'] = $arr[$wr_subject]['wr_6'];
			$arr[$wr_subject]['wr_32'] = $arr[$wr_subject]['wr_7'];
			$arr[$wr_subject]['wr_33'] = $arr[$wr_subject]['wr_8'];
			$arr[$wr_subject]['wr_34'] = $arr[$wr_subject]['wr_9'];
			
			//배송코드 : 기본적으로 0003(QEXPRESS)가 들어가고 D열에 Qxpress가 아닌 텍스트가 들어가면 0001(특송)로 입력될 수 있도록 해주세요
			if($rowData[0][3] == "Qxpress") {
				$arr[$wr_subject]['wr_20'] = "0003";
			} else {
				$arr[$wr_subject]['wr_20'] = "0001";
			}
		} else if($domain == "qoo10-jp" || $domain =="qoo10jp-1") {
			

			$wr_subject              = addslashes($rowData[0][1]); //주문번호
			$arr[$wr_subject]['wr_subject'] = $wr_subject;

			$ori_order_num = addslashes($rowData[0][2]); //원 주문번호
			$arr[$wr_subject]['ori_order_num'] = $ori_order_num; // 원 주문번호
			
			$arr[$wr_subject]['wr_15'] = addslashes($rowData[0][27]); //통화
			$arr[$wr_subject]['cart_num'] = addslashes($rowData[0][2]); //장바구니 번호
			
			$arr[$wr_subject]['wr_11'] = addslashes($rowData[0][14]); //수량
			$arr[$wr_subject]['wr_17'] = addslashes($rowData[0][15]); //상품명
			$arr[$wr_subject]['wr_16'] = addslashes(str_replace("'", "", $rowData[0][16])); //상품코드(sku)
			$arr[$wr_subject]['wr_2']  = addslashes($rowData[0][18]); //구매자이름
			$arr[$wr_subject]['wr_3']  = addslashes($rowData[0][22]); //주소1
			$arr[$wr_subject]['wr_8']  = addslashes(str_replace("'", "", $rowData[0][23])); //우편번호
			$arr[$wr_subject]['wr_7']  = addslashes($rowData[0][26]); //나라명
			$arr[$wr_subject]['wr_9']  = addslashes($rowData[0][21]); //전화번호
			$arr[$wr_subject]['wr_18'] = $domain; //도메인명
			$arr[$wr_subject]['wr_12'] = addslashes($rowData[0][14]); //박스수

			# 배송관련 정보 추가
			$arr[$wr_subject]['wr_27'] = $arr[$wr_subject]['wr_2'];
			$arr[$wr_subject]['wr_28'] = $arr[$wr_subject]['wr_3'];
			$arr[$wr_subject]['wr_29'] = $arr[$wr_subject]['wr_4'];
			$arr[$wr_subject]['wr_30'] = $arr[$wr_subject]['wr_5'];
			$arr[$wr_subject]['wr_31'] = $arr[$wr_subject]['wr_6'];
			$arr[$wr_subject]['wr_32'] = $arr[$wr_subject]['wr_7'];
			$arr[$wr_subject]['wr_33'] = $arr[$wr_subject]['wr_8'];
			$arr[$wr_subject]['wr_34'] = $arr[$wr_subject]['wr_9'];
			
			$arr[$wr_subject]['wr_20'] = "0007";
			
			
		}
		
    }

}

//정산금액
if($is_upload_file2) {
    $file = $_FILES['excelfile2']['tmp_name'];
	
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

	$savedir = "/home/gotec/www/upload/".date("Ymd")."/";
	FileUploadName("", $savedir, $file, $_FILES['excelfile2']['name'], "");

    for ($i = 2; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);

		if($domain == "Qoo10" || $domain == "Qoo10-1") {
			$wr_subject2              = addslashes($rowData[0][2]); //주문번호
			$arr[$wr_subject2]['wr_13'] =  addslashes(($rowData[0][14] / $arr[$wr_subject2]['wr_11'])); //단가 (2번 O열 / 1번 O열)
			$arr[$wr_subject2]['wr_11'] =  addslashes($rowData[0][8]);
			$arr[$wr_subject2]['wr_12'] =  addslashes($rowData[0][8]);
			$arr[$wr_subject2]['tot_amt'] = addslashes($rowData[0][14]); // 정산금액
		} else if($domain == "qoo10-jp" || $domain == "qoo10jp-1") {
			$wr_subject2              = addslashes($rowData[0][2]); //주문번호
			$arr[$wr_subject2]['wr_13'] =  addslashes(($rowData[0][14] / $arr[$wr_subject2]['wr_11'])); //단가 (2번 O열 / 1번 O열)
			$arr[$wr_subject2]['tot_amt'] = addslashes($rowData[0][14]); // 정산금액
		}

		$arr[$wr_subject2]['wr_35'] = $rowData[0][19]; // 수수료1 2번파일 T열
    }
}

//배송비
if($is_upload_file3) {
    $file = $_FILES['excelfile3']['tmp_name'];
	
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

	$savedir = "/home/gotec/www/upload/".date("Ymd")."/";
	FileUploadName("", $savedir, $file, $_FILES['excelfile3']['name'], "");

    for ($i = 2; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);
											
											
		$related_ordernum  = addslashes($rowData[0][11]); //관련주문번호
		//배송비는 주문번호가 아닌 관련주문번호 기준으로 값을 가지고 와야합니다! 관련주문번호에 2개 이상의 주문번호가 입력이 되어있다면 그 주문번호에 갯수로 배송비를 N분할을 해서 배송비가 분할이 되어야합니다.
		$order_num = @explode('|', $related_ordernum);
		$order_num = array_map('trim', $order_num);
		$order_num_cnt = count($order_num);
		
		if($domain == "Qoo10" || $domain == "Qoo10-1") {
			
			if($order_num_cnt == 1) {
				//단건일경우
				$arr[$order_num[0]]['wr_14'] =  addslashes(($arr[$order_num[0]]['tot_amt'] + (int)$rowData[0][4])); // 매출파일2번 O열+ 매출파일3번 E열
			} else {
				for($a=0; $a<$order_num_cnt; $a++) {
					$wr_14 = $rowData[0][6] / $order_num_cnt;
					
					$wr_14 =sprintf("%.2f", $wr_14);
					
					$arr[$order_num[$a]]['wr_14'] =  $wr_14 + $arr[$order_num[$a]]['tot_amt'];
				}
			}
		} else if($domain == "qoo10-jp" || $domain == "qoo10jp-1") {
			
			if($order_num_cnt == 1) {
				//단건일경우
				$arr[$order_num[0]]['wr_14'] =  addslashes(($arr[$order_num[0]]['tot_amt'] + (int)$rowData[0][4])); // 매출파일2번 O열+ 매출파일3번 E열
			} else {
				for($a=0; $a<$order_num_cnt; $a++) {
					$wr_14 = $rowData[0][6] / $order_num_cnt;
					
					$wr_14 = floor($wr_14);
					$arr[$order_num[$a]]['wr_14'] =  $arr[$order_num[$a]]['tot_amt'] + $wr_14;
				}
			}
		}
		$arr[$order_num[0]]['wr_36'] = $rowData[0][5]; // 수수료2 3번 파일 F열
		$arr[$order_num[0]]['wr_22'] = 0; // tax 없음
		$arr[$order_num[0]]['wr_23'] = $rowData[0][4]; // shipping price 3번 파일 E열
    }
}

$write_table = "g5_write_sales";
$bo_table = "sales";

foreach($arr as $k=>$v) {
	
	//정산금액,배송비에 1번 매출엑셀에 없는 주문번호가 딸려옴. 도메인값이 없으면 건너뜀
	if(!$v['wr_18'] || $v['wr_18'] == "") continue;
	
	$wr_num = get_next_num($write_table);

	$chk = sql_fetch("select * from  {$write_table} where wr_subject LIKE '%{$k}%' and wr_17 = '{$v['wr_17']}' ");

	if($chk){
		$fail_count++;
		continue;
	}
	$mb_id = $member['mb_id'];
	$wr_name = $member['mb_name'];
	$wr_password = '';
	$wr_content = '-';
	
	$item = sql_fetch("select wr_id,taxType from g5_write_product where (wr_1 = '".addslashes($v['wr_16'])."' or wr_27 = '".addslashes($v['wr_16'])."' or wr_28 = '".addslashes($v['wr_16'])."' or wr_29 = '".addslashes($v['wr_16'])."' or wr_30 = '".addslashes($v['wr_16'])."' or wr_31 = '".addslashes($v['wr_16'])."') AND wr_delYn = 'N' ");	
	if(isDefined($item['taxType']) == true){ // 해당 상품에서 과세/면세를 적용 후 매출등록 시 SET 상품에서 각 상품별 과세/면세 다시 적용
		$wr_37 = $item['taxType'];
	}else{ // 조회되는 상품이 없을 경우 기본값 과세로 적용
		$wr_37 = "1";
	}

	// 3번 엑셀에 주문번호가 없어서 관련 컬럼의 값이 빈값으로 들어감
	// 따라서 값이 없을 경우 신고가격은 역계산으로 나머지는 0으로 처리
	if(isDefined($v['wr_14']) == false){
		$v['wr_14'] = $v['wr_13'] * $v['wr_11'];
	}
	if(isDefined($v['wr_22']) == false){
		$v['wr_22'] = 0;
	}
	if(isDefined($v['wr_23']) == false){
		$v['wr_23'] = 0;
	}
	if(isDefined($v['wr_36']) == false){
		$v['wr_36'] = 0;
	}
	
	$sql = " insert into $write_table
			set wr_num = '$wr_num',
				 wr_reply = '$wr_reply',
				 wr_comment = 0,
				 ori_order_num = '{$v['ori_order_num']}',
				 wr_subject = '{$k}',
				 wr_content = '$wr_content',
				 wr_link1_hit = 0,
				 wr_link2_hit = 0,
				 wr_hit = 0,
				 wr_good = 0,
				 wr_nogood = 0,
				 mb_id = '{$mb_id}',
				 wr_password = '$wr_password',
				 wr_name = '$wr_name',
				 wr_datetime = '".G5_TIME_YMDHIS."',
				 wr_last = '".G5_TIME_YMDHIS."',
				 wr_ip = '{$_SERVER['REMOTE_ADDR']}',
				 wr_1 = '{$v['wr_1']}',
				 wr_2 = '{$v['wr_2']}',
				 wr_3 = '{$v['wr_3']}',
				 wr_4 = '{$v['wr_4']}',
				 wr_5 = '{$v['wr_5']}',
				 wr_6 = '{$v['wr_6']}',
				 wr_7 = '{$v['wr_7']}',
				 wr_8 = '{$v['wr_8']}',
				 wr_9 = '{$v['wr_9']}',
				 wr_10 = '{$v['wr_10']}',
				 wr_11 = '{$v['wr_11']}',
				 wr_12 = '{$v['wr_12']}',
				wr_13 = '{$v['wr_13']}',
				wr_14 = '{$v['wr_14']}',
				wr_15 = '{$v['wr_15']}',
				wr_16 = '{$v['wr_16']}',
				wr_17 = '{$v['wr_17']}',
				wr_18 = '{$v['wr_18']}',
				wr_19 = '{$v['wr_19']}',
				wr_20 = '{$v['wr_20']}',
				wr_21 = '{$v['wr_21']}',
				wr_22 = '{$v['wr_22']}',
				wr_23 = '{$v['wr_23']}',
				wr_24 = '{$v['wr_24']}',
				wr_25 = '{$v['wr_25']}',
				wr_26 = '{$v['cart_num']}',
				wr_27 = '{$v['wr_27']}',
				wr_28 = '{$v['wr_28']}',
				wr_29 = '{$v['wr_29']}',
				wr_30 = '{$v['wr_30']}',
				wr_31 = '{$v['wr_31']}',
				wr_32 = '{$v['wr_32']}',
				wr_33 = '{$v['wr_33']}',
				wr_34 = '{$v['wr_34']}',
				wr_35 = '{$v['wr_35']}',
				wr_36 = '{$v['wr_36']}',
				wr_37 = '{$wr_37}',
				wr_product_id = '{$item['wr_id']}'
				 ";

	sql_query($sql, true);

	$wr_id = sql_insert_id();

	// 부모 아이디에 UPDATE
	sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

	// 새글 INSERT
	sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

	// 게시글 1 증가
	sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");


	$succ_count++;

}

$query = "SELECT *, COUNT(*) AS cnt FROM g5_write_sales where wr_18 = '{$domain}' GROUP BY wr_26 HAVING cnt > 1";
$result = sql_query($query);

while ($row = sql_fetch_array($result)) {
	
	
	$ordernum = $row['wr_subject'];
	$cnt = $row['cnt'];
	$wr_2 = $row['wr_2'];

	for ($i = 0; $i <= $cnt; $i++) {
		// 중복 주문번호 뒤에 문자열 추가 및 업데이트
	
		$newOrderNumber = $row['wr_subject']."-". chr(64 + $i); // A, B, C, ...
		$sql = "SELECT * FROM g5_write_sales WHERE wr_subject = '$newOrderNumber'";
		$chk = sql_fetch($sql);
		if($chk['wr_id']){
			$cnt++;
			continue;
		}
		$updateQuery = "UPDATE g5_write_sales SET wr_subject = '$newOrderNumber',
		wr_1 = '".addslashes($row['wr_1'])."',
		wr_2 = '".addslashes($row['wr_2'])."',
		wr_3 = '".addslashes($row['wr_3'])."',
		wr_4 = '".addslashes($row['wr_4'])."',
		wr_5 = '".addslashes($row['wr_5'])."',
		wr_6 = '".addslashes($row['wr_6'])."',
		wr_7 = '".addslashes($row['wr_7'])."',
		wr_8 = '".addslashes($row['wr_8'])."',
		wr_9 = '".addslashes($row['wr_9'])."',
		wr_10 = '".addslashes($row['wr_10'])."',
		wr_15 = '".addslashes($row['wr_15'])."',
		wr_18 = '".addslashes($row['wr_18'])."',
		wr_19 = '".addslashes($row['wr_19'])."',
		wr_20 = '".addslashes($row['wr_20'])."',
		wr_21 = '".addslashes($row['wr_21'])."',
		wr_22 = '".addslashes($row['wr_22'])."',
		wr_23 = '".addslashes($row['wr_23'])."',
		wr_24 = '".addslashes($row['wr_24'])."',
		wr_25 = '1'
		WHERE wr_26 = '{$row['wr_26']}' LIMIT 1";
		
		
		
		sql_query($updateQuery);
	}
}


alert_close("총 {$succ_count}건의 큐텐 매출등록이 완료되었습니다.");

$g5['title'] = '매출자료 엑셀일괄등록 결과';
include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>

<div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
        <p>매출자료 등록을 완료했습니다.</p>
    </div>

    <dl id="excelfile_result">
        <dt>총 매출건수</dt>
        <dd><?php echo number_format($total_count); ?></dd>
        <dt>완료건수</dt>
        <dd><?php echo number_format($succ_count); ?></dd>
        <dt>실패건수(중복/오류)</dt>
        <dd><?php echo number_format($total_count-$succ_count); ?>
		
		<?php echo implode('/', $fail_wr_1)?>
		</dd>
      
    
    </dl>

    <div class="btn_win01 btn_win">
        <button type="button" onclick="window.close();">창닫기</button>
    </div>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');