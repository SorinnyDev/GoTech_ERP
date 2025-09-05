<?php
include_once './_common.php';

if (!isset($wr_id) || !isset($warehouse_key)) alert('잘못된 접근입니다.');

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");

// $warehouse_fields = [
//   "1000" => ['wr_32', 'wr_32_real'], // 한국
//   "3000" => ['wr_36', 'wr_36_real'], // 미국
//   "4000" => ['wr_42', 'wr_42_real'], // FBA
//   "5000" => ['wr_43', 'wr_43_real'], // W-FBA
//   "6000" => ['wr_44', 'wr_44_real'], // U-FBA

//   "7000" => ['wr_40', 'wr_40_real'], // 한국 반품
//   "8000" => ['wr_41', 'wr_41_real'], // 미국 반품

//   "11000" => ['wr_45', 'wr_45_real'], // 한국 폐기
//   "12000" => ['wr_46', 'wr_46_real'], // 미국 폐기
// ];

// $warehouse_fields 배열을 초기화합니다.
$warehouse_fields = [];

// $warehouseConfig 배열을 순회하며 $warehouse_fields를 채웁니다.
foreach ($warehouseConfig as $warehousecode => $warehouseinfo) {
    // 각 창고 코드($code)를 키로, filed와 filed_real 값을 배열로 저장합니다.
    $warehouse_fields[$warehousecode] = [$warehouseinfo['filed'], $warehouseinfo['filed_real']];
}

foreach (PLATFORM_TYPE as $key => $value) {
  if ($key != $warehouse_key) continue;
  if (!isset($warehouse_fields[$key])) continue;

  list($stock_field, $real_field) = $warehouse_fields[$key];
  $store_stock = $it[$stock_field];
  $store_stock_real = $it[$real_field];
  ?>
  <div class="tbl_head01 tbl_wrap" style="width:20%; float:left;height:550px;overflow-y:scroll;">
    <table>
      <thead style="position:sticky;top:0;">
      <tr>
        <th style="width:100%" colspan="2">
          <strong style="display:block;padding-bottom:10px"><?= $value ?>창고</strong>
          <?= $store_stock ?> /
          <input type="text" name="" value="<?= number_format($store_stock_real) ?>" class="total_qty_input">개
          <button type="button" class="total_qty_btn" onclick="open_log('<?= $key ?>', '<?= $wr_id ?>')">이동로그</button>
        </th>
      </tr>
      <tr>
        <th style="width:100%" colspan="2">
          <input type="text" id="<?= $key ?>_ser" class="frm_input rack_search" placeholder="랙 이름 검색" style="width:100%">
        </th>
      </tr>
      <tr>
        <th style="width:30%">랙 번호</th>
        <th>재고수량</th>
      </tr>
      </thead>
      <tbody id="<?= $key ?>_list">
      <?php
      // 1. 랙 목록
      $sql = "SELECT * FROM g5_rack WHERE gc_warehouse = '{$key}' AND gc_use = 1 ORDER BY SUBSTRING(gc_name, 1, 1) ASC";
      $rack_list = sql_fetch_all($sql);

      // 2. 랙별 재고 총합
      $sql = "
          SELECT wr_rack, SUM(wr_stock) AS total
          FROM g5_rack_stock
          WHERE wr_warehouse = '{$key}' AND wr_product_id = '{$wr_id}'
          GROUP BY wr_rack
      ";
      $stock_rows = sql_fetch_all($sql);
      $stock_map = [];
      foreach ($stock_rows as $row) {
        $stock_map[$row['wr_rack']] = (int)$row['total'];
      }

      // 3. 랙별 출고예정 수량
      $sql = "
          SELECT A.wr_rack, SUM(A.wr_ea) AS sales_stock
          FROM g5_sales2_list A
          LEFT JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num
          WHERE A.wr_direct_use = '1'
            AND A.wr_etc_use = '0'
            AND IFNULL(B.wr_release_use, 0) = '0'
            AND A.wr_warehouse = '{$key}'
            AND A.wr_product_id = '{$wr_id}'
          GROUP BY A.wr_rack
      ";
      $sales_rows = sql_fetch_all($sql);
      $sales_map = [];
      foreach ($sales_rows as $row) {
        $sales_map[$row['wr_rack']] = (int)$row['sales_stock'];
      }

      foreach ($rack_list as $i => $row) {
        $rack_seq = $row['seq'];
        $stock_total = $stock_map[$rack_seq] ?? 0;
        $sales_stock = $sales_map[$rack_seq] ?? 0;
        $bg = 'bg' . ($i % 2);
        ?>
        <tr class="<?= $bg ?>">
          <td><?= $row['gc_name'] ?></td>
          <td>
            <form name="frmIndi<?= $rack_seq ?>" action="./rack_move_update.php" method="post">
              <input type="hidden" name="mode" value="indi">
              <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
              <input type="hidden" name="warehouse" value="<?= $key ?>">
              <input type="hidden" name="seq" value="<?= $rack_seq ?>">
              <input type="hidden" name="ori_qty" value="<?= $stock_total ?>">
              <input type="hidden" name="sales_stock" value="<?= $sales_stock ?>">
              <?= $stock_total ?> /
              <input type="number" name="qty" class="frm_input qty" value="<?= $stock_total + $sales_stock ?>" style="text-align:right;width:40%"
                     oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
              <button class="btn01">수정</button>
            </form>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>
