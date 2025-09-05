<?php
include_once('./_common.php');

set_time_limit(0);
ini_set('memory_limit', '50M');

$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if (!$is_upload_file) alert("엑셀 파일을 업로드해 주세요.");

if ($is_upload_file) {
  $file = $_FILES['excelfile']['tmp_name'];

  include_once(G5_LIB_PATH . '/PHPExcel/IOFactory.php');

  $objPHPExcel = PHPExcel_IOFactory::load($file);
  $sheet = $objPHPExcel->getSheet(0);

  $num_rows = $sheet->getHighestRow();
  $highestColumn = $sheet->getHighestColumn();

  $savedir = "/home/gotec/www/upload/" . date("Ymd") . "/";
//  FileUploadName("", $savedir, $file, $_FILES['excelfile']['name'], "");

  $total_count = 0;
  $fail_array = [];

  for ($i = 2; $i <= $num_rows; $i++) {
    $total_count++;

    $j = 0;

    $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
      NULL,
      TRUE,
      FALSE);

    $ordernum = trim(addslashes($rowData[0][0])); //주문번호
    $ea = trim(addslashes($rowData[0][1])); //반품수량
    $sku = trim(addslashes($rowData[0][2])); //반품수량
    $wr_warehouse = trim(addslashes($rowData[0][3])); // 창고 (한국창고, 미국창고)
    $wr_rack_name = trim(addslashes($rowData[0][4])); // 렉 이름
    $stock = $ea;
    $wr_type = trim(addslashes($rowData[0][5]));; // 상품 상태
    $reg_datetime = trim(addslashes($rowData[0][6]));; // 등록 일시
    $wr_product_state = 0;
    if ($wr_type === "양품") {
      $wr_product_state = '1';
    } else if ($wr_type === '리퍼') {
      $wr_product_state = '2';
    }

    $is_ignore = false;

    if (!$ordernum) {
      $is_ignore = true;
    }

    if (is_null($ea)) {
      $arr = [];
      $arr['ordernum'] = $is_ignore ? $sku : $ordernum;
      $arr['reason'] = "입력 값이 올바르지 않습니다.";
      $fail_array[] = $arr;
      continue;
    }

    # 등록일시 날짜 타입 확인
    if (PHPExcel_Shared_Date::isDateTime($sheet->getCell('G' . $i))) {
      $timestamp = PHPExcel_Shared_Date::ExcelToPHP($reg_datetime);
      $reg_datetime = date("Y-m-d", $timestamp);
    } else if ($reg_datetime) {
      $reg_datetime = trim($reg_datetime); // 이미 문자열일 경우
    } else {
      $reg_datetime = G5_TIME_YMDHIS;
    }

    if (!$is_ignore) {
      $sql = "select seq, wr_order_num, wr_date, wr_code, wr_ea, wr_product_id from g5_sales3_list where wr_order_num = '{$ordernum}'";
      $sales3 = sql_fetch($sql);

      if (!$sales3) {
        $is_ignore = true;
      }

      if (!$is_ignore && $sales3['wr_ea'] < $ea) {
        $arr = [];
        $arr['ordernum'] = $ordernum;
        $arr['reason'] = "주문 수량보다 반품 수량이 많습니다.";
        $fail_array[] = $arr;
        continue;
      }
    }

    if (!$is_ignore) {
      $sql = "select * from g5_return_list where wr_order_num = '{$ordernum}'";
      $return = sql_fetch($sql);

      if ($return) {
        $sql = "delete from g5_return_list where wr_order_num = '{$ordernum}'";
        sql_query($sql);
      }
    }

    if (!$is_ignore) {
      $sql = "insert into g5_return_list 
        set mb_id = '{$member['mb_id']}',
        sales3_id = '{$sales3['seq']}',
        product_id = '{$sales3['wr_product_id']}',
        wr_order_num = '{$sales3['wr_order_num']}',
        wr_stock = '{$ea}',
        wr_state = '1',
        wr_datetime = '" . $reg_datetime . "',
        wr_product_state = '{$wr_product_state}'
        ";

    } else {
      $sql = "select * from g5_write_product as c where c.wr_subject LIKE '%$sku%' OR c.wr_1 LIKE '%$sku%'";
      $product_obj = sql_fetch($sql);

      $sql = "insert into g5_return_list 
        set mb_id = '{$member['mb_id']}',
        sales3_id = '0',
        product_id = '{$product_obj['wr_id']}',
        wr_order_num = '0',
        wr_stock = '{$ea}',
        wr_state = '1',
        wr_datetime = '" . $reg_datetime . "',
        wr_product_state = '{$wr_product_state}'
        ";

    }


    if (!sql_query($sql, true)) {
      $arr = [];
      $arr['ordernum'] = $is_ignore ? $sku : $ordernum;
      $arr['reason'] = "알 수 없는 이유로 서버 등록에 실패했습니다.";
      $fail_array[] = $arr;
      continue;
    }

    $return_id = sql_insert_id();


    # 재고 까지 넣기
    $wr_id = $is_ignore ? $product_obj['wr_id'] : $sales3['wr_product_id']; // product_id


    # 변수 설정시
    if ($wr_id && $wr_warehouse && $wr_rack_name && $stock && $wr_type) {

      if (!in_array($wr_warehouse, ['한국창고', '미국창고', '한국반품창고', '미국반품창고'])) {
        $arr = [];
        $arr['ordernum'] = $is_ignore ? $sku : $ordernum;
        $arr['reason'] = "창고 이름을 확인해주세요.";
        $fail_array[] = $arr;
        continue;
      }

      if ($wr_warehouse === "한국창고") {
        $wr_warehouse = 1000;
      } else if ($wr_warehouse === "미국창고") {
        $wr_warehouse = 3000;
      } else if ($wr_warehouse === '한국반품창고') {
        $wr_warehouse = 7000;
      } else if ($wr_warehouse === '미국반품창고') {
        $wr_warehouse = 8000;
      }

      if (!in_array($wr_type, ['양품', '리퍼'])) {
        $arr = [];
        $arr['ordernum'] = $is_ignore ? $sku : $ordernum;
        $arr['reason'] = "상품 상태를 확인해주세요.";
        $fail_array[] = $arr;
        continue;
      }

      $sql = "select * from g5_rack where gc_name = '{$wr_rack_name}'";
      $rack_result = sql_fetch($sql);

      if (!$rack_result) {
        $arr = [];
        $arr['ordernum'] = $is_ignore ? $sku : $ordernum;
        $arr['reason'] = "존재하지 않는 렉 입니다.";
        $fail_array[] = $arr;
        continue;
      }

      $wr_rack = $rack_result['seq'];

      //이동창고 재고증감
      $sql = "insert into g5_rack_stock set wr_warehouse = '{$wr_warehouse}', wr_rack = '{$wr_rack}', wr_stock = '{$stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '반품재고이동:{$wr_type}:{$return_id}' ";
      sql_query($sql);

      switch ($wr_warehouse) {
        case "1000" :
          $filed = 'wr_32';
          $real_filed = 'wr_32_real';
          $ware_name = "한국창고";
          break;
        case "3000" :
          $filed = 'wr_36';
          $real_filed = 'wr_36_real';
          $ware_name = "미국창고";
          break;
        case "7000" :
          $filed = 'wr_40';
          $real_filed = 'wr_40_real';
          $ware_name = "한국 반품창고";
          break;
        case "8000" :
          $filed = 'wr_41';
          $real_filed = 'wr_41_real';
          $ware_name = "미국 반품창고";
          break;
      }


      //상품마스터 재고 업데이트
      sql_query("update g5_write_product set {$filed} = {$filed} + {$stock}, {$real_filed} = {$real_filed} + {$stock} where wr_id = '{$wr_id}'");

      //반품창고 이동 기록
      $rack_name = get_rack_name($wr_rack);
      $sql = "insert into g5_return_stock set return_id = '{$return_id}', 
      wr_stock = '{$stock}', 
      wr_rack = '{$rack_name}', 
      wr_warehouse = '{$ware_name}', 
      wr_datetime = '" . G5_TIME_YMDHIS . "'";
      sql_query($sql);


      //남은수량 체크 후 상태 업데이트
      $chk = sql_fetch("select SUM(wr_stock) as qty from g5_return_list where seq = '{$return_id}'");

      $chk2 = sql_fetch("select SUM(wr_stock) as qty from g5_return_stock where return_id = '{$return_id}'");

      if ($chk['qty'] == $chk2['qty']) {
        sql_query("update g5_return_list set wr_state = 2 where seq = '{$return_id}'");
      }

    }

  }
}


$g5['title'] = '반품재고 엑셀등록 결과';

include_once(G5_PATH . '/head.sub.php');

add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);
?>

  <div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
      <p>반품재고 등록을 완료했습니다.</p>
    </div>

    <dl id="excelfile_result">
      <dt>총 등록 건수</dt>
      <dd><?php echo number_format($total_count); ?></dd>
      <dt>완료 건수</dt>
      <dd><?php echo number_format($total_count - count($fail_array)); ?></dd>
      <dt>실패 건수</dt>
      <dd><?php echo number_format(count($fail_array)); ?>

        <?php if (count($fail_array) > 0) { ?>
      <dt>실패 사유</dt>
      <dd>
        <?php
        foreach ($fail_array as $v) {
          echo "{$v['ordernum']} 실패 사유: {$v['reason']} <br> ";
        }
        ?>
      </dd>
      <?php } ?>


    </dl>

    <div class="btn_win01 btn_win">
      <button type="button" onclick="window.close();">창닫기</button>
    </div>

  </div>

<?php
include_once(G5_PATH . '/tail.sub.php');