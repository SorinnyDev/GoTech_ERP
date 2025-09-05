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
    $succ_count = 0;

    for ($i = 2; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);

        $wr_33              = addslashes($rowData[0][$j++]); //세트,단품 구분
        $wr_subject              = addslashes($rowData[0][$j++]); //제품명
        $wr_2             		 = addslashes($rowData[0][$j++]); //약칙명
        $wr_3             		 = addslashes($rowData[0][$j++]); //수출신고명칭
        $wr_1             		 = addslashes($rowData[0][$j++]); //SKU
        $wr_4             		 = addslashes($rowData[0][$j++]); //바코드
        $wr_5             		 = addslashes($rowData[0][$j++]); //대표코드1
        $wr_6             		 = addslashes($rowData[0][$j++]); //대표코드2
        $wr_9             		 = addslashes($rowData[0][$j++]); //직출
        $wr_10             		 = addslashes($rowData[0][$j++]); //1개당무게
        $wr_11             		 = addslashes($rowData[0][$j++]); //무게단위
        $wr_12             		 = addslashes($rowData[0][$j++]); //HS코드
        $wr_13             		 = addslashes($rowData[0][$j++]); //제조국가
        $wr_14             		 = addslashes($rowData[0][$j++]); //가로
        $wr_15             		 = addslashes($rowData[0][$j++]); //세로
        $wr_16             		 = addslashes($rowData[0][$j++]); //높이
        $wr_17             		 = addslashes($rowData[0][$j++]); //대표상품
        $wr_18             		 = addslashes($rowData[0][$j++]); //중량무게1
        $wr_19             		 = addslashes($rowData[0][$j++]); //중량무게2
        $wr_20             		 = addslashes($rowData[0][$j++]); //배터리
        $wr_21             		 = addslashes($rowData[0][$j++]); //220V유무
        $wr_22             		 = addslashes($rowData[0][$j++]); //발주단가
        $wr_23             		 = addslashes($rowData[0][$j++]); //브랜드
        $wr_24             		 = addslashes($rowData[0][$j++]); //채널
        $wr_25             		 = addslashes($rowData[0][$j++]); //비고
        $wr_26             		 = addslashes($rowData[0][$j++]); //카테고리
        $mb_name           		 = addslashes($rowData[0][$j++]); //담당자
        $wr_27           		 = addslashes($rowData[0][$j++]); //sku2
        $wr_28           		 = addslashes($rowData[0][$j++]); //sku3
        $wr_29           		 = addslashes($rowData[0][$j++]); //sku4
        $wr_30           		 = addslashes($rowData[0][$j++]); //sku5
        $wr_31           		 = addslashes($rowData[0][$j++]); //sku6
        $wr_34           		 = addslashes($rowData[0][$j++]); //구성상품SKU
        $wr_35           		 = addslashes($rowData[0][$j++]); //구성상품SKU별 수량
   

		if(!$wr_subject) {
			$fail_count++;
		}
		
		$mb = sql_fetch("select mb_id, mb_name, mb_password from g5_member where mb_name = '{$mb_name}'");
		
		//담당자없으면 최고관리자로 입력
		if(!$mb['mb_id']) {
			$mb = get_member('admin', 'mb_id, mb_name, mb_password');
		}
		
		$write_table = "g5_write_product";
		$bo_table = "product";
		$wr_num = get_next_num($write_table);
		
		$mb_id = $mb['mb_id'];
        $wr_name = $mb['mb_name'];
        $wr_password = '';
		$wr_content = '-';
		
		$wr_34 = str_replace(',', '|@|', $wr_34);
		$wr_35 = str_replace(',', '|@|', $wr_35);
		
        $sql = " insert into $write_table
                set wr_num = '$wr_num',
					 ca_name = '확정',
                     wr_reply = '$wr_reply',
                     wr_comment = 0,
                     wr_subject = '$wr_subject',
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
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10',
					 wr_11 = '{$wr_11}',
					 wr_12 = '{$wr_12}',
					wr_13 = '{$wr_13}',
					wr_14 = '{$wr_14}',
					wr_15 = '{$wr_15}',
					wr_16 = '{$wr_16}',
					wr_17 = '{$wr_17}',
					wr_18 = '{$wr_18}',
					wr_19 = '{$wr_19}',
					wr_20 = '{$wr_20}',
					wr_21 = '{$wr_21}',
					wr_22 = '{$wr_22}',
					wr_23 = '{$wr_23}',
					wr_24 = '{$wr_24}',
					wr_25 = '{$wr_25}',
					wr_26 = '{$wr_26}',
					wr_27 = '{$wr_27}',
					wr_28 = '{$wr_28}',
					wr_29 = '{$wr_29}',
					wr_30 = '{$wr_30}',
					wr_31 = '{$wr_31}',
					wr_33 = '{$wr_33}',
					wr_34 = '{$wr_34}',
					wr_35 = '{$wr_35}'
					 ";
    sql_query($sql);

    $wr_id = sql_insert_id();

    // 부모 아이디에 UPDATE
    sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

    // 새글 INSERT
    sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$member['mb_id']}' ) ");

    // 게시글 1 증가
    sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");

		
        $succ_count++;
    }
}

$g5['title'] = '제품 엑셀일괄등록 결과';
include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>

<div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
        <p>제품등록을 완료했습니다.<br>제품관리 탭에서 새로고침해주세요.</p>
    </div>

    <dl id="excelfile_result">
        <dt>총제품수</dt>
        <dd><?php echo number_format($total_count); ?></dd>
        <dt>완료건수</dt>
        <dd><?php echo number_format($succ_count); ?></dd>
        <dt>실패건수</dt>
        <dd><?php echo number_format($fail_count); ?></dd>
        <?php if($fail_count > 0) { ?>
        <dt>실패제품코드</dt>
        <dd><?php echo implode(', ', $fail_it_id); ?></dd>
        <?php } ?>
        <?php if($dup_count > 0) { ?>
        <dt>제품코드중복건수</dt>
        <dd><?php echo number_format($dup_count); ?></dd>
        <dt>중복제품코드</dt>
        <dd><?php echo implode(', ', $dup_it_id); ?></dd>
        <?php } ?>
    </dl>

    <div class="btn_win01 btn_win">
        <button type="button" onclick="window.close();">창닫기</button>
    </div>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');