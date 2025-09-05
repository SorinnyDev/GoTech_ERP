<?php
include_once('./_common.php');

set_time_limit(0);
ini_set('memory_limit', '50M');

$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if (!$is_upload_file) alert("엑셀 파일을 업로드해 주세요.");

$file = $_FILES['excelfile']['tmp_name'];

include_once(G5_LIB_PATH . '/PHPExcel/IOFactory.php');

$objPHPExcel = PHPExcel_IOFactory::load($file);
$sheet = $objPHPExcel->getSheet(0);

$num_rows = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

$savedir = "/home/gotec/www/upload/" . date("Ymd") . "/";

$total_count = 0;
$fail_array = [];

for ($i = 2; $i <= $num_rows; $i++) {
  $total_count++;

  $j = 0;

  $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
    NULL,
    TRUE,
    FALSE);

  $sku = trim(addslashes($rowData[0][0])); // SKU
  $ea = trim(addslashes($rowData[0][2])); // 수량
  $memo = trim(addslashes($rowData[0][3])); // 비고
  $warehouse = trim(addslashes($rowData[0][4])); // 창고 (한국폐기창고/미국폐기창고)
  $rack = trim(addslashes($rowData[0][5])); // 렉 이름

  if (!$sku) {
    setFail($sku, 'SKU를 확인해주세요.');
    continue;
  }

  if ($ea < 1) {
    setFail($sku, '수량을 확인해주세요.');
    continue;
  }

  if (!in_array($warehouse, ['한국 폐기창고', '미국 폐기창고'])) {
    setFail($sku, '창고를 확인해주세요.');
    continue;
  }

  if (!$rack) {
    setFail($sku, '창고를 확인해주세요.');
    continue;
  }

  $sql = "select * from g5_rack where gc_name = '{$rack}'";
  $rack_result = sql_fetch($sql);

  if (!$rack_result) {
    setFail($sku, '존재하지 않는 렉입니다.');
    continue;
  }

  $wr_rack = $rack_result['seq'];


  $sql = "select * from g5_write_product as c where c.wr_subject LIKE '%$sku%' OR c.wr_1 LIKE '%$sku%'";
  $product = sql_fetch($sql);

  if (!$product) {
    setFail($sku, '존재하지 않는 상품입니다.');
    continue;
  }

  $sql = "insert into g5_return_list 
        set mb_id = '{$member['mb_id']}',
        sales3_id = '0',
        product_id = '{$product['wr_id']}',
        wr_order_num = '0',
        wr_stock = '{$ea}',
        wr_state = '1',
        wr_datetime = '" . G5_TIME_YMDHIS . "',
        wr_product_state = '2',
        wr_memo = '{$memo}'
        ";

  $result = sql_query($sql);

  if (!$result) {
    setFail($sku, '서버 오류입니다.');
    continue;
  }

  $return_id = sql_insert_id();

  $sql = "insert into g5_rack_stock set wr_warehouse = '{$warehouse}', wr_rack = '{$rack}', wr_stock = '{$ea}', wr_product_id = '{$product['wr_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '폐기재고이동:폐기:{$return_id}' ";
  sql_query($sql);

  switch ($warehouse) {
    case '한국 폐기창고':
      $field = 'wr_45';
      break;

    case '미국 폐기창고':
      $field = 'wr_46';
      break;

    default:
      $field = '';
      break;
  }

  if ($field) {
    $sql = "update g5_write_product set {$field} = {$field} + {$ea} where wr_id = '{$product['wr_id']}'";
    sql_query($sql);
  }


  //반품창고 이동 기록
  $rack_name = get_rack_name($wr_rack);
  $sql = "insert into g5_return_stock set return_id = '{$return_id}', 
      wr_stock = '{$ea}', 
      wr_rack = '{$rack_name}', 
      wr_warehouse = '{$warehouse}', 
      wr_datetime = '" . G5_TIME_YMDHIS . "'";
  sql_query($sql);

  //남은수량 체크 후 상태 업데이트
  $chk = sql_fetch("select SUM(wr_stock) as qty from g5_return_list where seq = '{$return_id}'");

  $chk2 = sql_fetch("select SUM(wr_stock) as qty from g5_return_stock where return_id = '{$return_id}'");

  if ($chk['qty'] == $chk2['qty']) {
    sql_query("update g5_return_list set wr_state = 2 where seq = '{$return_id}'");
  }


}

$g5['title'] = '폐기재고 엑셀등록 결과';

include_once(G5_PATH . '/head.sub.php');

add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);


function setFail($sku, $reason)
{
  global $fail_array;

  $arr = [];
  $arr['sku'] = $sku;
  $arr['reason'] = $reason;

  $fail_array[] = $arr;
}

?>

  <div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
      <p>폐기재고 등록을 완료했습니다.</p>
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
          echo "{$v['sku']} 실패 사유: {$v['reason']} <br> ";
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
