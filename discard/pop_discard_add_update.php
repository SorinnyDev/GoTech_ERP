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
  $target_rack = trim(addslashes($rowData[0][6])); // 차감 렉 이름

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

  $is_failed = false;

  switch ($warehouse) {
    case '한국 폐기창고':
      $field = 'wr_45';
      $before_wr_warehouse = '1000';
      $wr_warehouse = '11000';
      $target_field = 'wr_32';
      break;

    case '미국 폐기창고':
      $field = 'wr_46';
      $before_wr_warehouse = '3000';
      $wr_warehouse = '12000';
      $target_field = 'wr_36';
      break;

    default:
      setFail($sku, '창고를 확인해주세요.');
      $is_failed = true;
      break;
  }

  if ($is_failed) {
    continue;
  }


  $sql = "select * from g5_rack where gc_name = '{$rack}' and gc_warehouse = '{$wr_warehouse}'";
  $rack_result = sql_fetch($sql);

  if (!$rack_result) {
    setFail($sku, '존재하지 않는 렉입니다.');
    continue;
  }

  $wr_rack = $rack_result['seq'];

  $sql = "select * from g5_rack where gc_name = '{$target_rack}' and gc_warehouse = '{$before_wr_warehouse}'";
  $target_rack_result = sql_fetch($sql);

  if (!$target_rack_result) {
    setFail($sku, '존재하지 않는 차감 렉입니다.');
    continue;
  }



  $sql = "select * from g5_write_product as c where c.wr_subject LIKE '%$sku%' OR c.wr_1 LIKE '%$sku%'";
  $product = sql_fetch($sql);

  if (!$product) {
    setFail($sku, '존재하지 않는 상품입니다.');
    continue;
  }

  $sql = "insert into g5_discard_list 
        set mb_id = '{$member['mb_id']}',
        product_id = '{$product['wr_id']}',
        wr_stock = '{$ea}',
        wr_datetime = '" . G5_TIME_YMDHIS . "',
        wr_memo = '{$memo}'
        ";

  $result = sql_query($sql);

  if (!$result) {
    setFail($sku, '서버 오류입니다.');
    continue;
  }

  $discard_id = sql_insert_id();

  switch ($warehouse) {
    case '한국 폐기창고':
      $sql = "insert into g5_rack_stock set wr_warehouse = '1000', wr_rack = '{$target_rack_result['seq']}', wr_stock = '-{$ea}', wr_product_id = '{$product['wr_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '한국창고>한국폐기창고 재고이관 ID:{$discard_id}' ";
      sql_query($sql);
      break;

    case '미국 폐기창고':
      $sql = "insert into g5_rack_stock set wr_warehouse = '3000', wr_rack = '{$target_rack_result['seq']}', wr_stock = '-{$ea}', wr_product_id = '{$product['wr_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '미국창고>미국폐기창고 재고이관 ID:{$discard_id}' ";
      sql_query($sql);
      break;

    default:
      $field = '';
      $wr_warehouse = '';
      $target_field = '';
      break;
  }

  $query = "select seq from g5_rack where gc_warehouse = '$wr_warehouse' and gc_name = '$rack'";
  $rack = sql_fetch($query);
  $rack = $rack['seq'];

  $sql = "insert into g5_rack_stock set wr_warehouse = '{$wr_warehouse}', wr_rack = '{$rack}', wr_stock = '{$ea}', wr_product_id = '{$product['wr_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '폐기재고이동:폐기:{$discard_id}' ";
  sql_query($sql);

  if ($field) {
    $sql = "update g5_write_product set {$field} = {$field} + {$ea}, {$field}_real = {$field}_real + {$ea} where wr_id = '{$product['wr_id']}'";
    sql_query($sql);

    $sql = "update g5_write_product set {$target_field} = {$target_field} - {$ea}, {$target_field}_real = {$target_field}_real - {$ea} where wr_id = '{$product['wr_id']}'";
    sql_query($sql);
  }

  $rack_name = get_rack_name($wr_rack);
  $sql = "insert into g5_discard_stock 
          set discard_id = '{$discard_id}', 
      wr_stock = '{$ea}', 
      wr_rack = '{$rack_name}', 
      wr_warehouse = '{$warehouse}', 
      wr_datetime = '" . G5_TIME_YMDHIS . "'";
  sql_query($sql);

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
